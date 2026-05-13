<?php

session_start();

$pageTitle = "Detalle de factura - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/detalle_factura_controller.php";

requireLogin();

$viewData = obtenerDatosDetalleFactura();

$user     = $viewData["user"];
$factura  = $viewData["factura"];
$detalles = $viewData["detalles"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/facturacion/facturas/detalle/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="dashboard-page-heading invoice-page-heading">
            <?php require __DIR__ . "/partials/facturacion/facturas/detalle/header.php"; ?>
        </section>

        <section class="dashboard-card invoice-detail-card">

            <?php require __DIR__ . "/partials/facturacion/facturas/detalle/summary.php"; ?>

            <?php require __DIR__ . "/partials/facturacion/facturas/detalle/products.php"; ?>

            <?php require __DIR__ . "/partials/facturacion/facturas/detalle/totals.php"; ?>

            <?php require __DIR__ . "/partials/facturacion/facturas/detalle/timeline.php"; ?>

            <?php require __DIR__ . "/partials/facturacion/facturas/detalle/actions.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>