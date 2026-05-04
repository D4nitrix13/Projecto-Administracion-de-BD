<?php

session_start();

$pageTitle = "Restaurar base de datos - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/services/BackupService.php";

requireAdmin();

date_default_timezone_set("America/Managua");

$user = $_SESSION["user"];
$connection = require __DIR__ . "/sql/db.php";

$backupService = new BackupService($connection, __DIR__ . "/backups");

$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "restore") {
        $archivo = trim($_POST["archivo"] ?? "");
        $confirmacion = trim($_POST["confirmacion"] ?? "");

        if ($archivo === "") {
            $error = "Debe seleccionar un archivo de respaldo.";
        } elseif ($confirmacion !== "RESTAURAR") {
            $error = "Para confirmar la restauración debe escribir exactamente RESTAURAR.";
        } else {
            $resultado = $backupService->restaurarDesdeArchivo($archivo);

            if ($resultado["success"]) {
                $success = $resultado["message"];
            } else {
                $error = $resultado["message"];
            }
        }
    }
}

$archivos = $backupService->listarRespaldos();

function restaurarFormatearTamano(int $bytes): string
{
    if ($bytes >= 1024 * 1024 * 1024) {
        return number_format($bytes / (1024 * 1024 * 1024), 2) . " GB";
    }

    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 2) . " MB";
    }

    return number_format($bytes / 1024, 2) . " KB";
}

function restaurarFormatearFecha(?string $fecha): string
{
    if (!$fecha) {
        return "Fecha no disponible";
    }

    $timestamp = strtotime($fecha);

    if ($timestamp === false) {
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

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/restaurar-bd/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/restaurar-bd/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/restaurar-bd/alerts.php"; ?>

        <?php require __DIR__ . "/partials/sistema/restaurar-bd/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>