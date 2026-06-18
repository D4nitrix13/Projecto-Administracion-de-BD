<?php

session_start();

$pageTitle = "Mantenimiento BD - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/mantenimiento_bd_controller.php";

requireAdmin();

date_default_timezone_set("America/Managua");

$viewData = obtenerDatosMantenimientoBd();

$user = $viewData["user"];
$error = $viewData["error"];
$errorDetail = $viewData["errorDetail"];
$success = $viewData["success"];
$successDetail = $viewData["successDetail"];
$resumenFull = $viewData["resumenFull"];
$resumenDiff = $viewData["resumenDiff"];
$resumenBackupLogs = $viewData["resumenBackupLogs"];
$resumenDatabaseLogs = $viewData["resumenDatabaseLogs"];
$resumenWal = $viewData["resumenWal"];

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
