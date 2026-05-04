<?php

session_start();

$pageTitle = "Archivos WAL - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

date_default_timezone_set("America/Managua");

$user = $_SESSION["user"];

$error = null;
$success = null;

$walDir = __DIR__ . "/database/wal_archive";

function walAsegurarDirectorio(string $directory): void
{
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
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

    $dias = [
        "domingo",
        "lunes",
        "martes",
        "miércoles",
        "jueves",
        "viernes",
        "sábado",
    ];

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

function walListarArchivos(string $walDir): array
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

        $archivos[] = [
            "filename" => $filename,
            "type" => walDetectarTipo($filename),
            "size" => $size,
            "size_label" => walFormatearTamano((int)$size),
            "modified_at" => $mtime,
            "modified_label" => walFormatearFecha($mtime),
        ];
    }

    usort(
        $archivos,
        fn(array $a, array $b): int => $b["modified_at"] <=> $a["modified_at"]
    );

    return $archivos;
}

$archivosWal = walListarArchivos($walDir);

$searchFilter = trim($_GET["search"] ?? "");
$typeFilter = trim($_GET["type"] ?? "");
$dateFilter = trim($_GET["date"] ?? "");
$sortFilter = trim($_GET["sort"] ?? "date_desc");

$tiposDisponibles = [];

foreach ($archivosWal as $archivo) {
    if (!in_array($archivo["type"], $tiposDisponibles, true)) {
        $tiposDisponibles[] = $archivo["type"];
    }
}

sort($tiposDisponibles);

$filteredWal = array_filter(
    $archivosWal,
    function (array $archivo) use ($searchFilter, $typeFilter, $dateFilter): bool {
        if ($typeFilter !== "" && $archivo["type"] !== $typeFilter) {
            return false;
        }

        if ($dateFilter !== "" && date("Y-m-d", (int)$archivo["modified_at"]) !== $dateFilter) {
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

$filteredWal = array_values($filteredWal);

usort(
    $filteredWal,
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

$totalArchivos = count($archivosWal);
$totalFiltrados = count($filteredWal);
$totalTamano = array_sum(array_map(fn(array $archivo): int => (int)$archivo["size"], $archivosWal));
$ultimoArchivo = $archivosWal[0] ?? null;

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/archivos-wal/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/alerts.php"; ?>

        <section class="wal-summary-grid">
            <article class="wal-summary-card">
                <span>Total de archivos</span>
                <strong><?= htmlspecialchars((string)$totalArchivos) ?></strong>
                <small>archivos WAL archivados</small>
            </article>

            <article class="wal-summary-card">
                <span>Resultado actual</span>
                <strong><?= htmlspecialchars((string)$totalFiltrados) ?></strong>
                <small>según filtros aplicados</small>
            </article>

            <article class="wal-summary-card">
                <span>Espacio ocupado</span>
                <strong><?= htmlspecialchars(walFormatearTamano((int)$totalTamano)) ?></strong>
                <small>almacenado en wal_archive</small>
            </article>

            <article class="wal-summary-card wal-summary-card-blue">
                <span>Último archivo</span>
                <strong><?= $ultimoArchivo ? htmlspecialchars($ultimoArchivo["type"]) : "N/A" ?></strong>
                <small><?= $ultimoArchivo ? htmlspecialchars($ultimoArchivo["modified_label"]) : "Sin registros" ?></small>
            </article>
        </section>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/filters.php"; ?>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/table.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>