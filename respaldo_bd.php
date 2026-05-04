<?php

session_start();

$pageTitle = "Respaldos BD - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/respaldo_bd_controller.php";

requireAdmin();

$viewData = obtenerDatosRespaldosBd();

$user      = $viewData["user"];
$error     = $viewData["error"];
$success   = $viewData["success"];
$archivos  = $viewData["archivos"];

?>

<!DOCTYPE html>
<html lang="es">



<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/backups-manuales/header.php"; ?>

        <section class="dashboard-card backup-page-card">

            <?php require __DIR__ . "/partials/sistema/backups-manuales/alerts.php"; ?>

            <div class="backup-grid">
                <?php require __DIR__ . "/partials/sistema/backups-manuales/backup-form.php"; ?>

                <?php require __DIR__ . "/partials/sistema/backups-manuales/restore-form.php"; ?>
            </div>

            <?php require __DIR__ . "/partials/sistema/backups-manuales/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/sistema/backups-manuales/styles.php"; ?>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>