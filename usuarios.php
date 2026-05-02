<?php

session_start();

$pageTitle = "Trabajadores - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/usuarios_controller.php";

requireAdmin();

$viewData = obtenerDatosUsuarios();

$user             = $viewData["user"];
$error            = $viewData["error"];
$flashSuccess     = $viewData["flashSuccess"];
$flashError       = $viewData["flashError"];
$roles            = $viewData["roles"];
$secciones        = $viewData["secciones"];
$usuarios         = $viewData["usuarios"];
$busqueda         = $viewData["busqueda"];
$rolFiltroInt     = $viewData["rolFiltroInt"];
$seccionFiltro    = $viewData["seccionFiltro"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/usuarios/header.php"; ?>

        <section class="dashboard-card">

            <?php require __DIR__ . "/partials/usuarios/alerts.php"; ?>

            <?php require __DIR__ . "/partials/usuarios/create-form.php"; ?>

            <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

            <?php require __DIR__ . "/partials/usuarios/filters.php"; ?>

            <?php require __DIR__ . "/partials/usuarios/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/usuarios/scripts.php"; ?>

</body>

</html>