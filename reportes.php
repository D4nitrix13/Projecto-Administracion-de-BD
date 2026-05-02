<?php

session_start();

$pageTitle = "Reportes generales - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/reportes_controller.php";
require_once __DIR__ . "/helpers/format.php";

requireLogin();

$viewData = obtenerDatosReportes();

$user                    = $viewData["user"];
$tipoReporte             = $viewData["tipoReporte"];
$fechaDesde              = $viewData["fechaDesde"];
$fechaHasta              = $viewData["fechaHasta"];

$totalClientes           = $viewData["totalClientes"];
$totalFacturas           = $viewData["totalFacturas"];
$totalVentas             = $viewData["totalVentas"];
$stockBajo               = $viewData["stockBajo"];

$ventasPorDia            = $viewData["ventasPorDia"];
$productosMasVendidos    = $viewData["productosMasVendidos"];
$ventasDetalladas        = $viewData["ventasDetalladas"];
$productosReporte        = $viewData["productosReporte"];
$clientesReporte         = $viewData["clientesReporte"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/reportes/header.php"; ?>

        <?php require __DIR__ . "/partials/reportes/summary.php"; ?>

        <section class="dashboard-card reports-main-card">

            <?php require __DIR__ . "/partials/reportes/filters.php"; ?>

            <?php require __DIR__ . "/partials/reportes/charts.php"; ?>

            <?php require __DIR__ . "/partials/reportes/ventas-table.php"; ?>

            <?php require __DIR__ . "/partials/reportes/productos-table.php"; ?>

            <?php require __DIR__ . "/partials/reportes/clientes-table.php"; ?>

        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/reportes/styles.php"; ?>
    <?php require __DIR__ . "/partials/reportes/scripts.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>