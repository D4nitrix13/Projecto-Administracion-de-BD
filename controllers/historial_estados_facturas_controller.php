<?php

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

    return [
        "user" => $user,
        "historialEstados" => $repository->obtenerHistorialGeneralFiltrado($filtros),
        "resumen" => $repository->obtenerResumenHistorial($filtros),
        "tiposEvento" => $repository->obtenerTiposEvento(),
        "busqueda" => $busqueda,
        "tipoEventoFiltro" => $tipoEventoFiltro,
        "estadoPagoFiltro" => $estadoPagoFiltro,
        "estadoProduccionFiltro" => $estadoProduccionFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
    ];
}
