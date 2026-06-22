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

$paginacionVentas        = $viewData["paginacionVentas"];
$paginacionProductos     = $viewData["paginacionProductos"];
$paginacionClientes      = $viewData["paginacionClientes"];

$rankingMasVendidosMes     = $viewData["rankingMasVendidosMes"];
$rankingMasVendidosSemana  = $viewData["rankingMasVendidosSemana"];
$rankingMasVendidosAnio    = $viewData["rankingMasVendidosAnio"];
$rankingMenosVendidosMes   = $viewData["rankingMenosVendidosMes"];
$rankingMenosVendidosSemana= $viewData["rankingMenosVendidosSemana"];
$rankingMenosVendidosAnio  = $viewData["rankingMenosVendidosAnio"];
$rankingCategoriasDebiles  = $viewData["rankingCategoriasDebiles"];

$rankingClientesTopSemanal  = $viewData["rankingClientesTopSemanal"];
$rankingClientesTopMensual  = $viewData["rankingClientesTopMensual"];
$rankingClientesTopAnual    = $viewData["rankingClientesTopAnual"];
$rankingClientesBajoSemanal = $viewData["rankingClientesBajoSemanal"];
$rankingClientesBajoMensual = $viewData["rankingClientesBajoMensual"];
$rankingClientesBajoAnual   = $viewData["rankingClientesBajoAnual"];

$limitMasVendidos    = $viewData["limitMasVendidos"];
$limitMenosVendidos  = $viewData["limitMenosVendidos"];
$limitCategorias     = $viewData["limitCategorias"];
$limitClientesTop    = $viewData["limitClientesTop"];
$limitClientesBajo   = $viewData["limitClientesBajo"];

$filtrosGETVentas    = ["tipo" => $tipoReporte, "desde" => $fechaDesde, "hasta" => $fechaHasta];
$filtrosGETProductos = ["tipo" => $tipoReporte, "desde" => $fechaDesde, "hasta" => $fechaHasta];
$filtrosGETClientes  = ["tipo" => $tipoReporte, "desde" => $fechaDesde, "hasta" => $fechaHasta];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/analisis/reportes/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="reports-page-heading">
            <div>
                <p class="dashboard-eyebrow">Análisis</p>

                <h1 class="dashboard-title">
                    Reportes generales
                </h1>

                <p class="dashboard-muted">
                    Consulte ventas, productos, clientes, facturación e inventario desde una vista consolidada.
                </p>
            </div>

            <a href="dashboard.php" class="btn-secondary-inline reports-back-btn">
                Volver al dashboard
            </a>
        </section>

        <?php require __DIR__ . "/partials/analisis/reportes/summary.php"; ?>

        <section class="reports-main-card">

            <?php require __DIR__ . "/partials/analisis/reportes/filters.php"; ?>

            <?php require __DIR__ . "/partials/analisis/reportes/charts.php"; ?>

            <?php require __DIR__ . "/partials/analisis/reportes/productos-ranking.php"; ?>

            <?php require __DIR__ . "/partials/analisis/reportes/clientes-ranking.php"; ?>

            <?php require __DIR__ . "/partials/analisis/reportes/ventas-table.php"; ?>

            <?php require __DIR__ . "/partials/analisis/reportes/productos-table.php"; ?>

            <?php require __DIR__ . "/partials/analisis/reportes/clientes-table.php"; ?>

        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <?php require __DIR__ . "/partials/analisis/reportes/scripts.php"; ?>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>