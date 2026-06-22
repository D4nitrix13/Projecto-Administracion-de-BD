<?php

require_once __DIR__ . "/../helpers/pagination.php";

function obtenerDatosLogsSistema(): array
{
    $user = $_SESSION["user"];

    date_default_timezone_set("America/Managua");

    $error = null;
    $success = null;

    $databaseLogDir = __DIR__ . "/../database/logs";
    $backupLogDir = __DIR__ . "/../backups/logs";
    $deleteQueueFile = __DIR__ . "/../backups/logs/log_delete_queue.json";

    $allowedSources = [
        "database" => [
            "label" => "PostgreSQL",
            "directory" => $databaseLogDir,
            "description" => "Registros generados por la base de datos PostgreSQL.",
        ],
        "backup" => [
            "label" => "Backups",
            "directory" => $backupLogDir,
            "description" => "Registros generados por respaldos, mantenimiento y programación automática.",
        ],
    ];

    logsAsegurarArchivoCola($deleteQueueFile);
    $deleteQueue = logsProcesarBorradosPendientes($allowedSources, $deleteQueueFile);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = logsProcesarAccion($_POST, $allowedSources, $deleteQueueFile, $deleteQueue, $user);
        $error = $resultado["error"];
        $success = $resultado["success"];
        $deleteQueue = logsLeerColaBorrado($deleteQueueFile);
    }

    $logs = logsListarArchivos($allowedSources, $deleteQueue);

    $sourceFilter = trim($_GET["source"] ?? "");
    $typeFilter = trim($_GET["type"] ?? "");
    $searchFilter = trim($_GET["search"] ?? "");
    $dateFilter = trim($_GET["date"] ?? "");
    $deleteFilter = trim($_GET["delete_status"] ?? "");
    $selectedSource = trim($_GET["selected_source"] ?? "");
    $selectedFile = basename(trim($_GET["selected_file"] ?? ""));

    $filteredLogs = logsFiltrarArchivos($logs, $sourceFilter, $typeFilter, $searchFilter, $dateFilter, $deleteFilter);
    $totalLogs = count($logs);
    $totalFiltered = count($filteredLogs);
    $totalPendingDelete = count(array_filter($logs, fn(array $log): bool => (bool)$log["delete_pending"]));
    $totalSize = array_sum(array_map(fn(array $log): int => (int)$log["size"], $logs));
    $lastLog = $logs[0] ?? null;

    $availableTypes = logsObtenerTiposDisponibles($logs);

    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));
    $paginacion = calcularPaginacion($totalFiltered, $paginaActual);
    $paginatedLogs = array_slice($filteredLogs, $paginacion["offset"], $paginacion["porPagina"]);

    $selectedLog = null;
    $selectedContent = null;

    if ($selectedSource !== "" && $selectedFile !== "") {
        $resultado = logsObtenerSeleccion($allowedSources, $selectedSource, $selectedFile, $logs);
        $selectedLog = $resultado["log"];
        $selectedContent = $resultado["content"];

        if ($resultado["error"] !== null) {
            $error = $resultado["error"];
        }
    }

    return [
        "user" => $user,
        "error" => $error,
        "success" => $success,
        "logs" => $logs,
        "filteredLogs" => $paginatedLogs,
        "totalLogs" => $totalLogs,
        "totalFiltered" => $totalFiltered,
        "totalPendingDelete" => $totalPendingDelete,
        "totalSize" => $totalSize,
        "lastLog" => $lastLog,
        "availableTypes" => $availableTypes,
        "selectedLog" => $selectedLog,
        "selectedContent" => $selectedContent,
        "sourceFilter" => $sourceFilter,
        "typeFilter" => $typeFilter,
        "searchFilter" => $searchFilter,
        "dateFilter" => $dateFilter,
        "deleteFilter" => $deleteFilter,
        "selectedSource" => $selectedSource,
        "selectedFile" => $selectedFile,
        "allowedSources" => $allowedSources,
        "paginacion" => $paginacion,
    ];
}

function logsProcesarAccion(
    array $post,
    array $allowedSources,
    string $deleteQueueFile,
    array $deleteQueue,
    array $user
): array {
    $action = $post["action"] ?? "";
    $source = trim($post["source"] ?? "");
    $filename = basename(trim($post["filename"] ?? ""));

    if ($action === "schedule_delete") {
        return logsProgramarBorrado($allowedSources, $deleteQueueFile, $deleteQueue, $source, $filename, $user);
    }

    if ($action === "cancel_delete") {
        return logsCancelarBorrado($deleteQueueFile, $deleteQueue, $source, $filename);
    }

    return ["error" => null, "success" => null];
}

function logsProgramarBorrado(
    array $allowedSources,
    string $deleteQueueFile,
    array $deleteQueue,
    string $source,
    string $filename,
    array $user
): array {
    $path = logsResolverRutaSegura($allowedSources, $source, $filename);

    if ($path === null) {
        return ["error" => "No se encontró el log seleccionado.", "success" => null];
    }

    if (logsBuscarEntradaCola($deleteQueue, $source, $filename)) {
        return ["error" => "Este log ya tiene borrado programado.", "success" => null];
    }

    $deleteQueue[] = [
        "key" => logsCrearClave($source, $filename),
        "source" => $source,
        "filename" => $filename,
        "scheduled_at" => time(),
        "delete_at" => time() + 86400,
        "scheduled_by" => $user["nombre"] ?? "Administrador",
    ];

    logsGuardarColaBorrado($deleteQueueFile, $deleteQueue);

    return ["error" => null, "success" => "El log fue programado para borrarse dentro de 24 horas."];
}

function logsCancelarBorrado(
    string $deleteQueueFile,
    array $deleteQueue,
    string $source,
    string $filename
): array {
    $key = logsCrearClave($source, $filename);
    $newQueue = array_filter(
        $deleteQueue,
        fn(array $entry): bool => ($entry["key"] ?? "") !== $key
    );

    logsGuardarColaBorrado($deleteQueueFile, $newQueue);

    return ["error" => null, "success" => "El borrado programado fue cancelado correctamente."];
}

function logsFiltrarArchivos(array $logs, string $sourceFilter, string $typeFilter, string $searchFilter, string $dateFilter, string $deleteFilter): array
{
    $filtered = array_filter($logs, function (array $log) use ($sourceFilter, $typeFilter, $searchFilter, $dateFilter, $deleteFilter): bool {
        if ($sourceFilter !== "" && $log["source"] !== $sourceFilter) return false;
        if ($typeFilter !== "" && $log["type"] !== $typeFilter) return false;
        if ($dateFilter !== "" && date("Y-m-d", (int)$log["modified_at"]) !== $dateFilter) return false;

        if ($deleteFilter === "pending" && !$log["delete_pending"]) return false;
        if ($deleteFilter === "active" && $log["delete_pending"]) return false;

        if ($searchFilter !== "") {
            $search = logsNormalizarTexto($searchFilter);
            $haystack = logsNormalizarTexto(
                $log["filename"] . " " .
                    $log["type"] . " " .
                    $log["source_label"] . " " .
                    $log["modified_label"]
            );

            if (!str_contains($haystack, $search)) return false;
        }

        return true;
    });

    return array_values($filtered);
}

function logsObtenerTiposDisponibles(array $logs): array
{
    $types = [];

    foreach ($logs as $log) {
        if (!in_array($log["type"], $types, true)) {
            $types[] = $log["type"];
        }
    }

    sort($types);

    return $types;
}

function logsObtenerSeleccion(array $allowedSources, string $selectedSource, string $selectedFile, array $logs): array
{
    $selectedPath = logsResolverRutaSegura($allowedSources, $selectedSource, $selectedFile);

    if ($selectedPath === null) {
        return ["error" => "No se encontró el archivo de log seleccionado.", "log" => null, "content" => null];
    }

    $selectedLog = null;

    foreach ($logs as $log) {
        if ($log["source"] === $selectedSource && $log["filename"] === $selectedFile) {
            $selectedLog = $log;
            break;
        }
    }

    if ($selectedLog === null) {
        return ["error" => null, "log" => null, "content" => null];
    }

    if ($selectedLog["is_text"]) {
        $selectedContent = logsLeerUltimasLineas($selectedPath);
    } else {
        $selectedContent = "Este archivo no se puede previsualizar como texto. Puede descargarlo para revisarlo.";
    }

    return ["error" => null, "log" => $selectedLog, "content" => $selectedContent];
}

function logsAsegurarDirectorio(string $directory): void
{
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

function logsAsegurarArchivoCola(string $file): void
{
    $dir = dirname($file);

    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    if (!is_file($file)) {
        file_put_contents($file, "[]", LOCK_EX);
    }
}

function logsLeerColaBorrado(string $file): array
{
    logsAsegurarArchivoCola($file);

    $content = file_get_contents($file);

    if ($content === false || trim($content) === "") {
        return [];
    }

    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

function logsGuardarColaBorrado(string $file, array $queue): void
{
    file_put_contents(
        $file,
        json_encode(array_values($queue), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function logsCrearClave(string $source, string $filename): string
{
    return $source . "::" . $filename;
}

function logsBuscarEntradaCola(array $queue, string $source, string $filename): ?array
{
    $key = logsCrearClave($source, $filename);

    foreach ($queue as $entry) {
        if (($entry["key"] ?? "") === $key) {
            return $entry;
        }
    }

    return null;
}

function logsResolverRutaSegura(array $allowedSources, string $source, string $filename): ?string
{
    if (!isset($allowedSources[$source])) {
        return null;
    }

    $safeFile = basename($filename);
    $directory = $allowedSources[$source]["directory"];
    $path = $directory . "/" . $safeFile;

    $realDirectory = realpath($directory);
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

function logsProcesarBorradosPendientes(array $allowedSources, string $queueFile): array
{
    $queue = logsLeerColaBorrado($queueFile);
    $now = time();
    $remaining = [];

    foreach ($queue as $entry) {
        $source = (string)($entry["source"] ?? "");
        $filename = basename((string)($entry["filename"] ?? ""));
        $deleteAt = (int)($entry["delete_at"] ?? 0);

        if ($source === "" || $filename === "" || $deleteAt <= 0) {
            continue;
        }

        if ($now >= $deleteAt) {
            $path = logsResolverRutaSegura($allowedSources, $source, $filename);

            if ($path !== null && is_writable($path)) {
                @unlink($path);
            }

            continue;
        }

        $remaining[] = $entry;
    }

    logsGuardarColaBorrado($queueFile, $remaining);

    return $remaining;
}

function logsFormatearTamano(int $bytes): string
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

function logsFormatearFecha(?int $timestamp): string
{
    if (!$timestamp) {
        return "Fecha no disponible";
    }

    $dias = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
    $meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];

    $diaSemana = $dias[(int)date("w", $timestamp)];
    $dia = (int)date("j", $timestamp);
    $mes = $meses[(int)date("n", $timestamp) - 1];
    $anio = date("Y", $timestamp);
    $hora = date("h:i:s", $timestamp);
    $periodo = strtolower(date("A", $timestamp)) === "am" ? "a.m." : "p.m.";

    return "{$diaSemana} {$dia} de {$mes} {$anio} - {$hora} {$periodo}";
}

function logsObtenerTipoArchivo(string $filename): string
{
    $lower = strtolower($filename);

    if (str_contains($lower, "postgresql")) return "PostgreSQL";
    if (str_contains($lower, "backup_full")) return "Backup completo";
    if (str_contains($lower, "backup_diff")) return "Backup diferencial";
    if (str_contains($lower, "backup_manual")) return "Backup manual";
    if (str_contains($lower, "cron_backup")) return "Programador automático";
    if (str_contains($lower, "mantenimiento")) return "Mantenimiento";
    if (str_contains($lower, "postgres_logs")) return "Copia de logs";
    if (str_ends_with($lower, ".tar.gz")) return "Archivo comprimido";

    return "Log del sistema";
}

function logsEsVisibleComoTexto(string $filename): bool
{
    $lower = strtolower($filename);

    return str_ends_with($lower, ".log")
        || str_ends_with($lower, ".txt")
        || str_ends_with($lower, ".json");
}

function logsLeerUltimasLineas(string $filepath, int $maxLines = 400): string
{
    if (!is_file($filepath) || !is_readable($filepath)) {
        return "No se pudo leer el archivo seleccionado.";
    }

    $lines = file($filepath, FILE_IGNORE_NEW_LINES);

    if ($lines === false) {
        return "No se pudo leer el contenido del archivo.";
    }

    return implode(PHP_EOL, array_slice($lines, -$maxLines));
}

function logsNormalizarTexto(string $text): string
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

function logsListarArchivos(array $allowedSources, array $deleteQueue): array
{
    $logs = [];

    foreach ($allowedSources as $sourceKey => $sourceData) {
        $directory = $sourceData["directory"];

        logsAsegurarDirectorio($directory);

        $patterns = [
            $directory . "/*.log",
            $directory . "/*.txt",
            $directory . "/*.json",
            $directory . "/*.tar.gz",
        ];

        foreach ($patterns as $pattern) {
            foreach (glob($pattern) ?: [] as $filepath) {
                if (!is_file($filepath)) {
                    continue;
                }

                $filename = basename($filepath);
                $mtime = filemtime($filepath) ?: 0;
                $size = filesize($filepath) ?: 0;
                $queueEntry = logsBuscarEntradaCola($deleteQueue, $sourceKey, $filename);

                $logs[] = [
                    "source" => $sourceKey,
                    "source_label" => $sourceData["label"],
                    "source_description" => $sourceData["description"],
                    "filename" => $filename,
                    "type" => logsObtenerTipoArchivo($filename),
                    "size" => $size,
                    "size_label" => logsFormatearTamano((int)$size),
                    "modified_at" => $mtime,
                    "modified_label" => logsFormatearFecha($mtime),
                    "is_text" => logsEsVisibleComoTexto($filename),
                    "delete_pending" => $queueEntry !== null,
                    "delete_at" => $queueEntry["delete_at"] ?? null,
                    "delete_at_label" => $queueEntry ? logsFormatearFecha((int)$queueEntry["delete_at"]) : null,
                ];
            }
        }
    }

    usort($logs, fn(array $a, array $b): int => $b["modified_at"] <=> $a["modified_at"]);

    return $logs;
}
