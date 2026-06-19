<?php

require_once __DIR__ . "/../helpers/pagination.php";
require_once __DIR__ . "/../repositories/FacturaEstadoHistorialRepository.php";

function obtenerDatosHistorialEstadosFacturas(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $repository = new FacturaEstadoHistorialRepository($connection);

    $busqueda = trim($_GET["q"] ?? "");
    $tipoEventoFiltro = trim($_GET["tipo_evento"] ?? "");
    $estadoPagoFiltro = trim($_GET["estado_pago"] ?? "");
    $estadoProduccionFiltro = trim($_GET["estado_produccion"] ?? "");
    $fechaDesde = trim($_GET["desde"] ?? "");
    $fechaHasta = trim($_GET["hasta"] ?? "");

    $estadosPagoPermitidos = ["Pendiente", "Parcial", "Pagado"];
    $estadosProduccionPermitidos = [
        "Pendiente",
        "En producción",
        "Lista para entregar",
        "Entregada",
        "Cancelada",
    ];

    if (!in_array($estadoPagoFiltro, $estadosPagoPermitidos, true)) {
        $estadoPagoFiltro = "";
    }

    if (!in_array($estadoProduccionFiltro, $estadosProduccionPermitidos, true)) {
        $estadoProduccionFiltro = "";
    }

    $filtros = [
        "busqueda" => $busqueda,
        "tipoEventoFiltro" => $tipoEventoFiltro,
        "estadoPagoFiltro" => $estadoPagoFiltro,
        "estadoProduccionFiltro" => $estadoProduccionFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
    ];

    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));
    $totalRegistros = $repository->contarHistorialGeneralFiltrado($filtros);
    $paginacion = calcularPaginacion($totalRegistros, $paginaActual);

    $historialEstados = $repository->obtenerHistorialGeneralFiltrado(
        $filtros,
        $paginacion["porPagina"],
        $paginacion["offset"]
    );

    return [
        "user" => $user,
        "historialEstados" => $historialEstados,
        "resumen" => $repository->obtenerResumenHistorial($filtros),
        "tiposEvento" => $repository->obtenerTiposEvento(),
        "busqueda" => $busqueda,
        "tipoEventoFiltro" => $tipoEventoFiltro,
        "estadoPagoFiltro" => $estadoPagoFiltro,
        "estadoProduccionFiltro" => $estadoProduccionFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
        "paginacion" => $paginacion,
    ];
}
