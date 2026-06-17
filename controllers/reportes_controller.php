<?php
// * Stored function or procedure has been executed

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
        "ventasDetalladas" => $repo->obtenerVentasDetalladas($fechaDesdeSql, $fechaHastaSql),
        "productosReporte" => $repo->obtenerProductosReporte($fechaDesdeSql, $fechaHastaSql),
        "clientesReporte" => $repo->obtenerClientesReporte($fechaDesdeSql, $fechaHastaSql),
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
