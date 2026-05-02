<?php

session_start();

$pageTitle = "Editar proveedor - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/editar_proveedor_controller.php";

requireLogin();

$viewData = obtenerDatosEditarProveedor();

$user          = $viewData["user"];
$error         = $viewData["error"];
$proveedor     = $viewData["proveedor"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/proveedores/editar/header.php"; ?>

        <?php require __DIR__ . "/partials/proveedores/editar/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/proveedores/editar/styles.php"; ?>

</body>

</html>