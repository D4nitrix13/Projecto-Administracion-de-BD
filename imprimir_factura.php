<?php

// Usaremos formato media carta vertical, que es práctico para facturas:
// 14cm x 21.5cm

session_start();

$pageTitle = "Imprimir factura - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/imprimir_factura_controller.php";

requireLogin();

$viewData = obtenerDatosImprimirFactura();

$user                 = $viewData["user"];
$factura              = $viewData["factura"];
$detalles             = $viewData["detalles"];
$empresaNombre        = $viewData["empresaNombre"];
$empresaDireccion     = $viewData["empresaDireccion"];
$empresaTelefono      = $viewData["empresaTelefono"];
$nombreClienteMostrar = $viewData["nombreClienteMostrar"];
$esFugaz              = $viewData["esFugaz"];
$fechaFactura         = $viewData["fechaFactura"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/facturacion/facturas/imprimir/styles.php"; ?>

<body class="dashboard-body invoice-print-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main invoice-print-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/facturacion/facturas/imprimir/toolbar.php"; ?>

        <?php require __DIR__ . "/partials/facturacion/facturas/imprimir/paper.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>