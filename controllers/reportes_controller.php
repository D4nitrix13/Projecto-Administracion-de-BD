<?php
// * Stored function or procedure has been executed

require_once __DIR__ . "/../helpers/pagination.php";

use App\Repository\ReportesRepository;

function obtenerDatosReportes(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";
    $repo = new ReportesRepository($connection);

    $tipoReporte = $_GET["tipo"] ?? "general";
    $fechaDesde = $_GET["desde"] ?? "";
    $fechaHasta = $_GET["hasta"] ?? "";

    $tipoReporte = in_array($tipoReporte, ["general", "ventas", "productos", "clientes"], true)
        ? $tipoReporte
        : "general";

    $fechas = normalizarRangoFechasReportes($fechaDesde, $fechaHasta);
    $fechaDesdeSql = $fechas["desde_sql"];
    $fechaHastaSql = $fechas["hasta_sql"];

    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));

    // Límites configurables por el usuario (rango 1-100, default 10)
    $limitMasVendidos = max(1, min(100, (int) ($_GET["limit_mas"] ?? 10)));
    $limitMenosVendidos = max(1, min(100, (int) ($_GET["limit_menos"] ?? 5)));
    $limitCategorias = max(1, min(100, (int) ($_GET["limit_categorias"] ?? 10)));
    $limitClientesTop = max(1, min(100, (int) ($_GET["limit_clientes_top"] ?? 10)));
    $limitClientesBajo = max(1, min(100, (int) ($_GET["limit_clientes_bajo"] ?? 10)));

    $totalVentasRegistros = $repo->contarVentasDetalladas($fechaDesdeSql, $fechaHastaSql);
    $totalProductosRegistros = $repo->contarProductosReporte($fechaDesdeSql, $fechaHastaSql);
    $totalClientesRegistros = $repo->contarClientesReporte($fechaDesdeSql, $fechaHastaSql);

    $paginacionVentas = calcularPaginacion($totalVentasRegistros, $paginaActual);
    $paginacionProductos = calcularPaginacion($totalProductosRegistros, $paginaActual);
    $paginacionClientes = calcularPaginacion($totalClientesRegistros, $paginaActual);

    $ventasDetalladas = match ($tipoReporte) {
        "ventas" => $repo->obtenerVentasDetalladas($fechaDesdeSql, $fechaHastaSql, $paginacionVentas["porPagina"], $paginacionVentas["offset"]),
        default => $repo->obtenerVentasDetalladas($fechaDesdeSql, $fechaHastaSql),
    };

    $productosReporte = match ($tipoReporte) {
        "productos" => $repo->obtenerProductosReporte($fechaDesdeSql, $fechaHastaSql, $paginacionProductos["porPagina"], $paginacionProductos["offset"]),
        default => $repo->obtenerProductosReporte($fechaDesdeSql, $fechaHastaSql),
    };

    $clientesReporte = match ($tipoReporte) {
        "clientes" => $repo->obtenerClientesReporte($fechaDesdeSql, $fechaHastaSql, $paginacionClientes["porPagina"], $paginacionClientes["offset"]),
        default => $repo->obtenerClientesReporte($fechaDesdeSql, $fechaHastaSql),
    };

    return [
        "user" => $user,
        "tipoReporte" => $tipoReporte,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,

        "totalClientes" => $repo->obtenerTotalClientes(),
        "totalFacturas" => $repo->obtenerTotalFacturas($fechaDesdeSql, $fechaHastaSql),
        "totalVentas" => $repo->obtenerTotalVentas($fechaDesdeSql, $fechaHastaSql),
        "stockBajo" => $repo->obtenerStockBajo(),

        "ventasPorDia" => $repo->obtenerVentasPorDia($fechaDesdeSql, $fechaHastaSql),
        "productosMasVendidos" => $repo->obtenerProductosMasVendidos($fechaDesdeSql, $fechaHastaSql),
        "ventasDetalladas" => $ventasDetalladas,
        "productosReporte" => $productosReporte,
        "clientesReporte" => $clientesReporte,

        "paginacionVentas" => $paginacionVentas,
        "paginacionProductos" => $paginacionProductos,
        "paginacionClientes" => $paginacionClientes,

        "rankingMasVendidosMes" => $repo->obtenerProductosMasVendidosMes($limitMasVendidos),
        "rankingMasVendidosSemana" => $repo->obtenerProductosMasVendidosSemana($limitMasVendidos),
        "rankingMasVendidosAnio" => $repo->obtenerProductosMasVendidosAnio($limitMasVendidos),
        "rankingMenosVendidosMes" => $repo->obtenerProductosMenosVendidosMes($limitMenosVendidos),
        "rankingMenosVendidosSemana" => $repo->obtenerProductosMenosVendidosSemana($limitMenosVendidos),
        "rankingMenosVendidosAnio" => $repo->obtenerProductosMenosVendidosAnio($limitMenosVendidos),
        "rankingCategoriasDebiles" => $repo->obtenerCategoriasMenosProductos($limitCategorias),

        "rankingClientesTopSemanal" => $repo->obtenerClientesTopComprasSemanal($limitClientesTop),
        "rankingClientesTopMensual" => $repo->obtenerClientesTopComprasMensual($limitClientesTop),
        "rankingClientesTopAnual" => $repo->obtenerClientesTopComprasAnual($limitClientesTop),
        "rankingClientesBajoSemanal" => $repo->obtenerClientesMenosComprasSemanal($limitClientesBajo),
        "rankingClientesBajoMensual" => $repo->obtenerClientesMenosComprasMensual($limitClientesBajo),
        "rankingClientesBajoAnual" => $repo->obtenerClientesMenosComprasAnual($limitClientesBajo),

        // Límites para prellenar los inputs del formulario
        "limitMasVendidos" => $limitMasVendidos,
        "limitMenosVendidos" => $limitMenosVendidos,
        "limitCategorias" => $limitCategorias,
        "limitClientesTop" => $limitClientesTop,
        "limitClientesBajo" => $limitClientesBajo,
    ];
}

function normalizarRangoFechasReportes(string $fechaDesde, string $fechaHasta): array
{
    $desdeSql = null;
    $hastaSql = null;

    if ($fechaDesde !== "") {
        $desdeSql = $fechaDesde . " 00:00:00";
    }

    if ($fechaHasta !== "") {
        $hastaSql = $fechaHasta . " 23:59:59";
    }

    return [
        "desde_sql" => $desdeSql,
        "hasta_sql" => $hastaSql,
    ];
}
