<?php

session_start();

$pageTitle = "Editar cliente - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/editar_cliente_controller.php";

requireLogin();

$viewData = obtenerDatosEditarCliente();

$user             = $viewData["user"];
$id               = $viewData["id"];
$error            = $viewData["error"];
$nombres          = $viewData["nombres"];
$apellidos        = $viewData["apellidos"];
$telefono         = $viewData["telefono"];
$direccion        = $viewData["direccion"];
$identificacion   = $viewData["identificacion"];
$tipoCliente      = $viewData["tipoCliente"];
$isRestrictedRole = $viewData["isRestrictedRole"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/clientes/editar/header.php"; ?>

        <?php require __DIR__ . "/partials/clientes/editar/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>