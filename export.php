<?php

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/reportes_controller.php";
require_once __DIR__ . "/helpers/format.php";
require_once __DIR__ . "/bootstrap.php";

use App\Repository\ReportesRepository;
use App\Service\ExportService;

requireLogin();

$user = $_SESSION["user"];

if (($user["rol"] ?? "") !== "Administrador") {
    $_SESSION["flash_error"] = "No tiene permisos para exportar reportes.";
    header("Location: dashboard.php");
    exit;
}

$tipoReporte = $_GET["tipo"] ?? "general";
$fechaDesde = $_GET["desde"] ?? "";
$fechaHasta = $_GET["hasta"] ?? "";

$tipoReporte = in_array($tipoReporte, ["ventas", "productos", "clientes", "completo"], true)
    ? $tipoReporte
    : "general";

$fechas = normalizarRangoFechasReportes($fechaDesde, $fechaHasta);
$fechaDesdeSql = $fechas["desde_sql"];
$fechaHastaSql = $fechas["hasta_sql"];

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";
$repo = new ReportesRepository($connection);

$exportService = new ExportService();

$fechaArchivo = date("Y-m-d");

switch ($tipoReporte) {
    case "ventas":
        $ventas = $repo->obtenerVentasDetalladas($fechaDesdeSql, $fechaHastaSql);
        $exportService->exportarReporteVentas($ventas, $fechaDesde, $fechaHasta);
        $exportService->descargar("reporte_ventas_{$fechaArchivo}");
        break;

    case "productos":
        $productos = $repo->obtenerProductosReporte($fechaDesdeSql, $fechaHastaSql);
        $exportService->exportarReporteProductos($productos, $fechaDesde, $fechaHasta);
        $exportService->descargar("reporte_productos_{$fechaArchivo}");
        break;

    case "clientes":
        $clientes = $repo->obtenerClientesReporte($fechaDesdeSql, $fechaHastaSql);
        $exportService->exportarReporteClientes($clientes, $fechaDesde, $fechaHasta);
        $exportService->descargar("reporte_clientes_{$fechaArchivo}");
        break;

    case "completo":
        $ventas = $repo->obtenerVentasDetalladas($fechaDesdeSql, $fechaHastaSql);
        $productos = $repo->obtenerProductosReporte($fechaDesdeSql, $fechaHastaSql);
        $clientes = $repo->obtenerClientesReporte($fechaDesdeSql, $fechaHastaSql);
        $exportService->exportarReporteCompleto($ventas, $productos, $clientes, $fechaDesde, $fechaHasta);
        $exportService->descargar("reporte_completo_{$fechaArchivo}");
        break;

    default:
        $ventas = $repo->obtenerVentasDetalladas($fechaDesdeSql, $fechaHastaSql);
        $productos = $repo->obtenerProductosReporte($fechaDesdeSql, $fechaHastaSql);
        $clientes = $repo->obtenerClientesReporte($fechaDesdeSql, $fechaHastaSql);
        $exportService->exportarReporteCompleto($ventas, $productos, $clientes, $fechaDesde, $fechaHasta);
        $exportService->descargar("reporte_general_{$fechaArchivo}");
        break;
}
