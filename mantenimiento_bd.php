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

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/respaldo/header.php"; ?>

        <section class="dashboard-card backup-page-card">

            <?php require __DIR__ . "/partials/respaldo/alerts.php"; ?>

            <div class="backup-grid">
                <?php require __DIR__ . "/partials/respaldo/backup-form.php"; ?>

                <?php require __DIR__ . "/partials/respaldo/restore-form.php"; ?>
            </div>

            <?php require __DIR__ . "/partials/respaldo/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/respaldo/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>