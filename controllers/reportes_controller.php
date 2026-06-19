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

        "rankingMasVendidosMes" => $repo->obtenerProductosMasVendidosMes(),
        "rankingMasVendidosSemana" => $repo->obtenerProductosMasVendidosSemana(),
        "rankingMasVendidosAnio" => $repo->obtenerProductosMasVendidosAnio(),
        "rankingMenosVendidosMes" => $repo->obtenerProductosMenosVendidosMes(),
        "rankingMenosVendidosSemana" => $repo->obtenerProductosMenosVendidosSemana(),
        "rankingMenosVendidosAnio" => $repo->obtenerProductosMenosVendidosAnio(),
        "rankingCategoriasDebiles" => $repo->obtenerCategoriasMenosProductos(),
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
