<?php
session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/detalle_factura_controller.php";

requireLogin();

$datos = obtenerDatosDetalleFactura();

$user = $datos["user"];
$factura = $datos["factura"];
$detalles = $datos["detalles"];
$historialEstados = $datos["historialEstados"] ?? [];
$resumenHistorial = $datos["resumenHistorial"] ?? [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle de factura</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/facturacion/facturas/detalle/styles.php"; ?>
</head>

<body class="dashboard-body">
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">
        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/facturacion/facturas/detalle/header.php"; ?>

        <section class="invoice-detail-card">
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