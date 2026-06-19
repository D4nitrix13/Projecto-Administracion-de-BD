<?php

require_once __DIR__ . "/../helpers/pagination.php";

function obtenerDatosArchivosWal(): array
{
    $user = $_SESSION["user"];
    $error = null;
    $success = null;

    $walDir = __DIR__ . "/../database/wal_archive";
    $deleteQueueFile = __DIR__ . "/../storage/system/wal_delete_queue.json";

    walAsegurarArchivoCola($deleteQueueFile);
    $deleteQueue = walProcesarBorradosPendientes($walDir, $deleteQueueFile);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = walProcesarAccionPost($deleteQueue, $deleteQueueFile, $walDir, $user);
        $error = $resultado["error"];
        $success = $resultado["success"];
        $deleteQueue = $resultado["deleteQueue"];
    }

    $archivosWal = walListarArchivos($walDir, $deleteQueue);

    $searchFilter = trim($_GET["search"] ?? "");
    $typeFilter = trim($_GET["type"] ?? "");
    $dateFilter = trim($_GET["date"] ?? "");
    $deleteFilter = trim($_GET["delete_status"] ?? "");
    $sortFilter = trim($_GET["sort"] ?? "date_desc");

    $tiposDisponibles = walObtenerTiposDisponibles($archivosWal);

    $filteredWal = walFiltrarArchivos(
        $archivosWal,
        $searchFilter,
        $typeFilter,
        $dateFilter,
        $deleteFilter
    );

    $filteredWal = walOrdenarArchivos($filteredWal, $sortFilter);

    $totalFiltrados = count($filteredWal);
    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));
    $paginacion = calcularPaginacion($totalFiltrados, $paginaActual);
    $paginatedWal = array_slice($filteredWal, $paginacion["offset"], $paginacion["porPagina"]);

    return [
        "user" => $user,
        "error" => $error,
        "success" => $success,
        "archivosWal" => $archivosWal,
        "filteredWal" => $paginatedWal,
        "tiposDisponibles" => $tiposDisponibles,
        "totalArchivos" => count($archivosWal),
        "totalFiltrados" => $totalFiltrados,
        "totalTamano" => walCalcularTamanoTotal($archivosWal),
        "totalPendingDelete" => walContarPendientes($archivosWal),
        "ultimoArchivo" => $archivosWal[0] ?? null,
        "searchFilter" => $searchFilter,
        "typeFilter" => $typeFilter,
        "dateFilter" => $dateFilter,
        "deleteFilter" => $deleteFilter,
        "sortFilter" => $sortFilter,
        "paginacion" => $paginacion,
    ];
}

function walProcesarAccionPost(
    array $deleteQueue,
    string $deleteQueueFile,
    string $walDir,
    array $user
): array {
    $action = $_POST["action"] ?? "";
    $filename = basename(trim($_POST["filename"] ?? ""));

    $error = null;
    $success = null;

    if ($action === "schedule_delete") {
        $resultado = walProgramarEliminacion($deleteQueue, $deleteQueueFile, $walDir, $filename, $user);
        $error = $resultado["error"];
        $success = $resultado["success"];
        $deleteQueue = $resultado["deleteQueue"];
    } elseif ($action === "cancel_delete") {
        $resultado = walCancelarEliminacion($deleteQueue, $deleteQueueFile, $filename);
        $error = $resultado["error"];
        $success = $resultado["success"];
        $deleteQueue = $resultado["deleteQueue"];
    }

    return [
        "error" => $error,
        "success" => $success,
        "deleteQueue" => $deleteQueue,
    ];
}

function walProgramarEliminacion(
    array $deleteQueue,
    string $deleteQueueFile,
    string $walDir,
    string $filename,
    array $user
): array {
    $path = walResolverRutaSegura($walDir, $filename);

    if ($path === null) {
        return [
            "error" => "No se encontró el archivo WAL seleccionado.",
            "success" => null,
            "deleteQueue" => $deleteQueue,
        ];
    }

    if (walBuscarEntradaCola($deleteQueue, $filename)) {
        return [
            "error" => "Este archivo ya tiene borrado programado.",
            "success" => null,
            "deleteQueue" => $deleteQueue,
        ];
    }

    $deleteQueue[] = [
        "filename" => $filename,
        "scheduled_at" => time(),
        "delete_at" => time() + 86400,
        "scheduled_by" => $user["nombre"] ?? "Administrador",
    ];

    walGuardarColaBorrado($deleteQueueFile, $deleteQueue);

    return [
        "error" => null,
        "success" => "El archivo WAL fue programado para borrarse dentro de 24 horas.",
        "deleteQueue" => $deleteQueue,
    ];
}

function walCancelarEliminacion(
    array $deleteQueue,
    string $deleteQueueFile,
    string $filename
): array {
    $newQueue = array_filter(
        $deleteQueue,
        fn(array $entry): bool => ($entry["filename"] ?? "") !== $filename
    );

    walGuardarColaBorrado($deleteQueueFile, $newQueue);

    return [
        "error" => null,
        "success" => "El borrado programado fue cancelado correctamente.",
        "deleteQueue" => $newQueue,
    ];
}

function walAsegurarDirectorio(string $directory): void
{
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

function walAsegurarArchivoCola(string $file): void
{
    $dir = dirname($file);

    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    if (!is_file($file)) {
        file_put_contents($file, "[]", LOCK_EX);
    }
}

function walLeerColaBorrado(string $file): array
{
    walAsegurarArchivoCola($file);

    $content = file_get_contents($file);

    if ($content === false || trim($content) === "") {
        return [];
    }

    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

function walGuardarColaBorrado(string $file, array $queue): void
{
    file_put_contents(
        $file,
        json_encode(array_values($queue), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function walBuscarEntradaCola(array $queue, string $filename): ?array
{
    foreach ($queue as $entry) {
        if (($entry["filename"] ?? "") === $filename) {
            return $entry;
        }
    }

    return null;
}

function walResolverRutaSegura(string $walDir, string $filename): ?string
{
    $safeFile = basename($filename);
    $path = $walDir . "/" . $safeFile;

    $realDirectory = realpath($walDir);
    $realFile = realpath($path);

    if (
        $realDirectory === false ||
        $realFile === false ||
        !str_starts_with($realFile, $realDirectory) ||
        !is_file($realFile)
    ) {
        return null;
    }

    return $realFile;
}

function walProcesarBorradosPendientes(string $walDir, string $queueFile): array
{
    $queue = walLeerColaBorrado($queueFile);
    $now = time();
    $remaining = [];

    foreach ($queue as $entry) {
        $filename = basename((string)($entry["filename"] ?? ""));
        $deleteAt = (int)($entry["delete_at"] ?? 0);

        if ($filename === "" || $deleteAt <= 0) {
            continue;
        }

        if ($now >= $deleteAt) {
            $path = walResolverRutaSegura($walDir, $filename);

            if ($path !== null && is_writable($path)) {
                @unlink($path);
            }

            continue;
        }

        $remaining[] = $entry;
    }

    walGuardarColaBorrado($queueFile, $remaining);

    return $remaining;
}

function walFormatearTamano(int $bytes): string
{
    if ($bytes >= 1024 * 1024 * 1024) {
        return number_format($bytes / (1024 * 1024 * 1024), 2) . " GB";
    }

    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 2) . " MB";
    }

    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . " KB";
    }

    return $bytes . " B";
}

function walFormatearFecha(?int $timestamp): string
{
    if (!$timestamp) {
        return "Fecha no disponible";
    }

    $dias = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];

    $meses = [
        "enero",
        "febrero",
        "marzo",
        "abril",
        "mayo",
        "junio",
        "julio",
        "agosto",
        "septiembre",
        "octubre",
        "noviembre",
        "diciembre",
    ];

    $diaSemana = $dias[(int)date("w", $timestamp)];
    $dia = (int)date("j", $timestamp);
    $mes = $meses[(int)date("n", $timestamp) - 1];
    $anio = date("Y", $timestamp);
    $hora = date("h:i:s", $timestamp);
    $periodo = strtolower(date("A", $timestamp)) === "am" ? "a.m." : "p.m.";

    return "{$diaSemana} {$dia} de {$mes} {$anio} - {$hora} {$periodo}";
}

function walNormalizarTexto(string $text): string
{
    $text = mb_strtolower($text, "UTF-8");

    return strtr($text, [
        "á" => "a",
        "é" => "e",
        "í" => "i",
        "ó" => "o",
        "ú" => "u",
        "ñ" => "n",
    ]);
}

function walDetectarTipo(string $filename): string
{
    $lower = strtolower($filename);

    if (str_ends_with($lower, ".backup")) {
        return "Backup history";
    }

    if (str_ends_with($lower, ".partial")) {
        return "WAL parcial";
    }

    if (preg_match('/^[0-9A-F]{24}$/i', $filename) === 1) {
        return "Segmento WAL";
    }

    return "Archivo WAL";
}

function walListarArchivos(string $walDir, array $deleteQueue): array
{
    walAsegurarDirectorio($walDir);

    $archivos = [];

    foreach (glob($walDir . "/*") ?: [] as $filepath) {
        if (!is_file($filepath)) {
            continue;
        }

        $filename = basename($filepath);
        $mtime = filemtime($filepath) ?: 0;
        $size = filesize($filepath) ?: 0;
        $queueEntry = walBuscarEntradaCola($deleteQueue, $filename);

        $archivos[] = [
            "filename" => $filename,
            "type" => walDetectarTipo($filename),
            "size" => $size,
            "size_label" => walFormatearTamano((int)$size),
            "modified_at" => $mtime,
            "modified_label" => walFormatearFecha($mtime),
            "delete_pending" => $queueEntry !== null,
            "delete_at_label" => $queueEntry
                ? walFormatearFecha((int)$queueEntry["delete_at"])
                : null,
        ];
    }

    usort(
        $archivos,
        fn(array $a, array $b): int => $b["modified_at"] <=> $a["modified_at"]
    );

    return $archivos;
}

function walObtenerTiposDisponibles(array $archivos): array
{
    $tipos = [];

    foreach ($archivos as $archivo) {
        if (!in_array($archivo["type"], $tipos, true)) {
            $tipos[] = $archivo["type"];
        }
    }

    sort($tipos);

    return $tipos;
}

function walFiltrarArchivos(
    array $archivos,
    string $searchFilter,
    string $typeFilter,
    string $dateFilter,
    string $deleteFilter
): array {
    $filtered = array_filter(
        $archivos,
        function (array $archivo) use (
            $searchFilter,
            $typeFilter,
            $dateFilter,
            $deleteFilter
        ): bool {

            if ($typeFilter !== "" && $archivo["type"] !== $typeFilter) {
                return false;
            }

            if ($dateFilter !== "" && date("Y-m-d", (int)$archivo["modified_at"]) !== $dateFilter) {
                return false;
            }

            if ($deleteFilter === "pending" && !$archivo["delete_pending"]) {
                return false;
            }

            if ($deleteFilter === "active" && $archivo["delete_pending"]) {
                return false;
            }

            if ($searchFilter !== "") {
                $search = walNormalizarTexto($searchFilter);

                $haystack = walNormalizarTexto(
                    $archivo["filename"] . " " .
                        $archivo["type"] . " " .
                        $archivo["size_label"] . " " .
                        $archivo["modified_label"]
                );

                if (!str_contains($haystack, $search)) {
                    return false;
                }
            }

            return true;
        }
    );

    return array_values($filtered);
}

function walOrdenarArchivos(array $archivos, string $sortFilter): array
{
    usort(
        $archivos,
        function (array $a, array $b) use ($sortFilter): int {
            return match ($sortFilter) {
                "date_asc" => $a["modified_at"] <=> $b["modified_at"],
                "size_desc" => $b["size"] <=> $a["size"],
                "size_asc" => $a["size"] <=> $b["size"],
                "name_asc" => strcmp($a["filename"], $b["filename"]),
                "name_desc" => strcmp($b["filename"], $a["filename"]),
                default => $b["modified_at"] <=> $a["modified_at"],
            };
        }
    );

    return $archivos;
}

function walCalcularTamanoTotal(array $archivos): int
{
    return array_sum(
        array_map(
            fn(array $archivo): int => (int)$archivo["size"],
            $archivos
        )
    );
}

function walContarPendientes(array $archivos): int
{
    return count(
        array_filter(
            $archivos,
            fn(array $archivo): bool => (bool)$archivo["delete_pending"]
        )
    );
}
