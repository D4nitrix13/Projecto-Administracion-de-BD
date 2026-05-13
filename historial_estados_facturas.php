<?php
session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/historial_estados_facturas_controller.php";

requireLogin();

$datos = obtenerDatosHistorialEstadosFacturas();

$user = $datos["user"];
$historialEstados = $datos["historialEstados"];
$resumen = $datos["resumen"];
$tiposEvento = $datos["tiposEvento"];

$busqueda = $datos["busqueda"];
$tipoEventoFiltro = $datos["tipoEventoFiltro"];
$estadoPagoFiltro = $datos["estadoPagoFiltro"];
$estadoProduccionFiltro = $datos["estadoProduccionFiltro"];
$fechaDesde = $datos["fechaDesde"];
$fechaHasta = $datos["fechaHasta"];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de estados</title>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/facturacion/facturas/styles.php"; ?>
    <?php require __DIR__ . "/partials/facturacion/facturas/historial/styles.php"; ?>
</head>

<body class="dashboard-body">
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">
        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/facturacion/facturas/historial/header.php"; ?>
        <?php require __DIR__ . "/partials/facturacion/facturas/historial/stats.php"; ?>
        <?php require __DIR__ . "/partials/facturacion/facturas/historial/filters.php"; ?>
        <?php require __DIR__ . "/partials/facturacion/facturas/historial/timeline.php"; ?>
    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
</body>

</html>