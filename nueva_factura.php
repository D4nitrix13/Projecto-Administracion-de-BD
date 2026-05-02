<?php

session_start();

$pageTitle = "Nueva factura - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/nueva_factura_controller.php";

requireLogin();

$viewData = obtenerDatosNuevaFactura();

$user                 = $viewData["user"];
$error                = $viewData["error"];
$clientes             = $viewData["clientes"];
$productos            = $viewData["productos"];
$secciones            = $viewData["secciones"];
$seccionUsuario       = $viewData["seccionUsuario"];
$idCliente            = $viewData["idCliente"];
$idSeccion            = $viewData["idSeccion"];
$descuentoGlobal      = $viewData["descuentoGlobal"];
$tipoClienteVenta     = $viewData["tipoClienteVenta"];
$nombreClienteFugaz   = $viewData["nombreClienteFugaz"];
$textoSubtitulo       = $viewData["textoSubtitulo"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/facturas/nueva/header.php"; ?>

        <?php require __DIR__ . "/partials/facturas/nueva/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/facturas/nueva/styles.php"; ?>
    <?php require __DIR__ . "/partials/facturas/nueva/scripts.php"; ?>

</body>

</html>