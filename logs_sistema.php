<?php

session_start();

$pageTitle = "Logs del sistema - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

date_default_timezone_set("America/Managua");

$user = $_SESSION["user"];

$error = null;
$success = null;

$databaseLogDir = __DIR__ . "/database/logs";
$backupLogDir = __DIR__ . "/backups/logs";

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

function logsAsegurarDirectorio(string $directory): void
{
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
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

function logsObtenerTipoArchivo(string $filename): string
{
    $lower = strtolower($filename);

    if (str_contains($lower, "postgresql")) {
        return "PostgreSQL";
    }

    if (str_contains($lower, "backup_full")) {
        return "Backup completo";
    }

    if (str_contains($lower, "backup_diff")) {
        return "Backup diferencial";
    }

    if (str_contains($lower, "backup_manual")) {
        return "Backup manual";
    }

    if (str_contains($lower, "cron_backup")) {
        return "Programador automático";
    }

    if (str_contains($lower, "mantenimiento")) {
        return "Mantenimiento";
    }

    if (str_contains($lower, "postgres_logs")) {
        return "Copia de logs";
    }

    if (str_ends_with($lower, ".tar.gz")) {
        return "Archivo comprimido";
    }

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

    $lines = array_slice($lines, -$maxLines);

    return implode(PHP_EOL, $lines);
}

function logsNormalizarTexto(string $text): string
{
    $text = mb_strtolower($text, "UTF-8");
    $text = strtr($text, [
        "á" => "a",
        "é" => "e",
        "í" => "i",
        "ó" => "o",
        "ú" => "u",
        "ñ" => "n",
    ]);

    return $text;
}

function logsListarArchivos(array $allowedSources): array
{
    $logs = [];

    foreach ($allowedSources as $sourceKey => $sourceData) {
        $directory = $sourceData["directory"];

        logsAsegurarDirectorio($directory);

        if (!is_dir($directory)) {
            continue;
        }

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
                ];
            }
        }
    }

    usort(
        $logs,
        fn(array $a, array $b): int => $b["modified_at"] <=> $a["modified_at"]
    );

    return $logs;
}

$logs = logsListarArchivos($allowedSources);

$sourceFilter = trim($_GET["source"] ?? "");
$typeFilter = trim($_GET["type"] ?? "");
$searchFilter = trim($_GET["search"] ?? "");
$dateFilter = trim($_GET["date"] ?? "");
$selectedSource = trim($_GET["selected_source"] ?? "");
$selectedFile = basename(trim($_GET["selected_file"] ?? ""));

$filteredLogs = array_filter(
    $logs,
    function (array $log) use ($sourceFilter, $typeFilter, $searchFilter, $dateFilter): bool {
        if ($sourceFilter !== "" && $log["source"] !== $sourceFilter) {
            return false;
        }

        if ($typeFilter !== "" && $log["type"] !== $typeFilter) {
            return false;
        }

        if ($dateFilter !== "" && date("Y-m-d", (int)$log["modified_at"]) !== $dateFilter) {
            return false;
        }

        if ($searchFilter !== "") {
            $search = logsNormalizarTexto($searchFilter);
            $haystack = logsNormalizarTexto(
                $log["filename"] . " " .
                    $log["type"] . " " .
                    $log["source_label"] . " " .
                    $log["modified_label"]
            );

            if (!str_contains($haystack, $search)) {
                return false;
            }
        }

        return true;
    }
);

$filteredLogs = array_values($filteredLogs);

$totalLogs = count($logs);
$totalFiltered = count($filteredLogs);
$totalSize = array_sum(array_map(fn(array $log): int => (int)$log["size"], $logs));
$lastLog = $logs[0] ?? null;

$availableTypes = [];

foreach ($logs as $log) {
    if (!in_array($log["type"], $availableTypes, true)) {
        $availableTypes[] = $log["type"];
    }
}

sort($availableTypes);

$selectedLog = null;
$selectedContent = null;

if ($selectedSource !== "" && $selectedFile !== "") {
    if (!isset($allowedSources[$selectedSource])) {
        $error = "Origen de log no válido.";
    } else {
        $selectedPath = $allowedSources[$selectedSource]["directory"] . "/" . $selectedFile;
        $realDirectory = realpath($allowedSources[$selectedSource]["directory"]);
        $realFile = realpath($selectedPath);

        if (
            $realDirectory === false ||
            $realFile === false ||
            !str_starts_with($realFile, $realDirectory) ||
            !is_file($realFile)
        ) {
            $error = "No se encontró el archivo de log seleccionado.";
        } else {
            foreach ($logs as $log) {
                if ($log["source"] === $selectedSource && $log["filename"] === $selectedFile) {
                    $selectedLog = $log;
                    break;
                }
            }

            if ($selectedLog && $selectedLog["is_text"]) {
                $selectedContent = logsLeerUltimasLineas($realFile);
            } elseif ($selectedLog) {
                $selectedContent = "Este archivo no se puede previsualizar como texto. Puede descargarlo para revisarlo.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/logs-sistema/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/logs-sistema/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/logs-sistema/alerts.php"; ?>

        <section class="logs-summary-grid">
            <article class="logs-summary-card">
                <span>Total de archivos</span>
                <strong><?= htmlspecialchars((string)$totalLogs) ?></strong>
                <small>logs encontrados</small>
            </article>

            <article class="logs-summary-card">
                <span>Resultado actual</span>
                <strong><?= htmlspecialchars((string)$totalFiltered) ?></strong>
                <small>según filtros aplicados</small>
            </article>

            <article class="logs-summary-card">
                <span>Espacio ocupado</span>
                <strong><?= htmlspecialchars(logsFormatearTamano((int)$totalSize)) ?></strong>
                <small>almacenado en logs</small>
            </article>

            <article class="logs-summary-card logs-summary-card-blue">
                <span>Último log</span>
                <strong><?= $lastLog ? htmlspecialchars($lastLog["type"]) : "N/A" ?></strong>
                <small><?= $lastLog ? htmlspecialchars($lastLog["modified_label"]) : "Sin registros" ?></small>
            </article>
        </section>

        <?php require __DIR__ . "/partials/sistema/logs-sistema/filters.php"; ?>

        <section class="logs-layout">
            <?php require __DIR__ . "/partials/sistema/logs-sistema/table.php"; ?>

            <?php require __DIR__ . "/partials/sistema/logs-sistema/viewer.php"; ?>
        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>