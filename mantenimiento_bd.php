<?php

session_start();

$pageTitle = "Mantenimiento BD - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/helpers/notificaciones.php";

requireAdmin();

date_default_timezone_set("America/Managua");

$user = $_SESSION["user"];

$error = null;
$errorDetail = null;
$success = null;
$successDetail = null;

$projectRoot = __DIR__;

$scriptFull = $projectRoot . "/scripts/backup_full.sh";
$scriptDiff = $projectRoot . "/scripts/backup_diff.sh";
$scriptLogs = $projectRoot . "/scripts/backup_logs.sh";
$scriptMantenimiento = $projectRoot . "/scripts/mantenimiento_bd.sh";

$backupFullDir = $projectRoot . "/backups/full";
$backupDiffDir = $projectRoot . "/backups/diff";
$backupLogsDir = $projectRoot . "/backups/logs";
$databaseLogsDir = $projectRoot . "/database/logs";
$walDir = $projectRoot . "/database/wal_archive";

function mantenimientoAsegurarDirectorio(string $directorio): void
{
    if (!is_dir($directorio)) {
        mkdir($directorio, 0775, true);
    }
}

function mantenimientoFormatearTamano(int $bytes): string
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

function mantenimientoFormatearFecha(?int $timestamp): string
{
    if (!$timestamp) {
        return "No disponible";
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

function mantenimientoObtenerArchivos(string $directorio, string $patron = "*"): array
{
    mantenimientoAsegurarDirectorio($directorio);

    $archivos = glob($directorio . "/" . $patron) ?: [];

    return array_values(
        array_filter(
            $archivos,
            fn(string $archivo): bool => is_file($archivo)
        )
    );
}

function mantenimientoObtenerResumenDirectorio(string $directorio, string $patron = "*"): array
{
    $archivos = mantenimientoObtenerArchivos($directorio, $patron);

    $totalTamano = array_sum(
        array_map(
            fn(string $archivo): int => (int)filesize($archivo),
            $archivos
        )
    );

    usort(
        $archivos,
        fn(string $a, string $b): int => (filemtime($b) ?: 0) <=> (filemtime($a) ?: 0)
    );

    $ultimoArchivo = $archivos[0] ?? null;

    return [
        "total" => count($archivos),
        "tamano" => $totalTamano,
        "ultimo_archivo" => $ultimoArchivo ? basename($ultimoArchivo) : "No disponible",
        "ultima_fecha" => $ultimoArchivo ? mantenimientoFormatearFecha(filemtime($ultimoArchivo) ?: null) : "No disponible",
    ];
}

function mantenimientoEjecutarScript(string $script, string $projectRoot): array
{
    if (!is_file($script)) {
        return [
            "success" => false,
            "message" => "No se encontró el script: " . basename($script),
        ];
    }

    if (!is_executable($script)) {
        @chmod($script, 0775);
    }

    $command = sprintf(
        "cd %s && bash %s 2>&1",
        escapeshellarg($projectRoot),
        escapeshellarg($script)
    );

    $output = [];
    $returnCode = 0;

    exec($command, $output, $returnCode);

    $message = trim(implode("\n", $output));

    return [
        "success" => $returnCode === 0,
        "message" => $message,
    ];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    $scriptSeleccionado = match ($action) {
        "backup_full" => $scriptFull,
        "backup_diff" => $scriptDiff,
        "backup_logs" => $scriptLogs,
        "mantenimiento_completo" => $scriptMantenimiento,
        default => null,
    };

    if ($scriptSeleccionado === null) {
        $error = "Acción no válida.";
        $errorDetail = "La acción solicitada no corresponde a una tarea de mantenimiento disponible.";
    } else {
        $resultado = mantenimientoEjecutarScript($scriptSeleccionado, $projectRoot);
        $mensaje = trim((string)($resultado["message"] ?? ""));

        if ($resultado["success"]) {
            $success = "ok";

            notificar("mantenimiento_bd", "Mantenimiento BD", "Se ejecutó: {$action}", [
                "id_usuario_origen" => (int)$user["id_usuario"],
                "rol_origen" => $user["rol"] ?? "",
                "metadata" => ["accion" => $action],
            ]);

            if ($mensaje === "" || str_contains($mensaje, "El script terminó sin devolver salida")) {
                $successDetail = "La tarea finalizó correctamente. Puede revisar la carpeta backups/logs para ver el detalle del proceso.";
            } else {
                $successDetail = $mensaje;
            }
        } else {
            $error = "error";
            $errorDetail = $mensaje !== ""
                ? $mensaje
                : "No se pudo completar la tarea. Revise backups/logs para obtener más información.";
        }
    }
}
$resumenFull = mantenimientoObtenerResumenDirectorio($backupFullDir, "*.sql");
$resumenDiff = mantenimientoObtenerResumenDirectorio($backupDiffDir, "*.sql");
$resumenBackupLogs = mantenimientoObtenerResumenDirectorio($backupLogsDir, "*");
$resumenDatabaseLogs = mantenimientoObtenerResumenDirectorio($databaseLogsDir, "*");
$resumenWal = mantenimientoObtenerResumenDirectorio($walDir, "*");

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/mantenimiento-bd/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/mantenimiento-bd/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/mantenimiento-bd/alerts.php"; ?>

        <?php require __DIR__ . "/partials/sistema/mantenimiento-bd/status.php"; ?>

        <?php require __DIR__ . "/partials/sistema/mantenimiento-bd/panel.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>