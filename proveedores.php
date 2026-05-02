<?php

session_start();

$pageTitle = "Proveedores - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/proveedores_controller.php";

requireLogin();

$viewData = obtenerDatosProveedores();

$user          = $viewData["user"];
$idRol         = $viewData["idRol"];
$puedeGestionar = $viewData["puedeGestionar"];
$error         = $viewData["error"];
$success       = $viewData["success"];
$flashSuccess  = $viewData["flashSuccess"];
$flashError    = $viewData["flashError"];
$busqueda      = $viewData["busqueda"];
$proveedores   = $viewData["proveedores"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/proveedores/header.php"; ?>

        <section class="dashboard-card">

            <?php require __DIR__ . "/partials/proveedores/alerts.php"; ?>

            <?php if ($puedeGestionar): ?>
                <?php require __DIR__ . "/partials/proveedores/create-form.php"; ?>

                <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">
            <?php endif; ?>

            <?php require __DIR__ . "/partials/proveedores/filters.php"; ?>

            <?php require __DIR__ . "/partials/proveedores/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>