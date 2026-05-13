<?php

require_once __DIR__ . "/../repositories/FacturaRepository.php";
require_once __DIR__ . "/../repositories/FacturaEstadoHistorialRepository.php";

function obtenerDatosDetalleFactura(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $facturaRepository = new FacturaRepository($connection);
    $historialRepository = new FacturaEstadoHistorialRepository($connection);

    $idFactura = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if ($idFactura <= 0) {
        $_SESSION["flash_error"] = "Factura no válida.";
        header("Location: facturas.php");
        exit();
    }

    $factura = $facturaRepository->obtenerFacturaPorId($idFactura);

    if (!$factura) {
        $_SESSION["flash_error"] = "Factura no encontrada.";
        header("Location: facturas.php");
        exit();
    }

    $detalles = $facturaRepository->obtenerDetalleFactura($idFactura);
    $historialEstados = $historialRepository->obtenerHistorialPorFactura($idFactura);

    if (empty($historialEstados)) {
        $historialEstados = construirHistorialBaseDesdeFactura($factura);
    }

    $resumenHistorial = construirResumenHistorialFactura($factura, $historialEstados);

    return [
        "user" => $user,
        "factura" => $factura,
        "detalles" => $detalles,
        "historialEstados" => $historialEstados,
        "resumenHistorial" => $resumenHistorial,
    ];
}

function construirHistorialBaseDesdeFactura(array $factura): array
{
    $idFactura = (int)($factura["id_factura"] ?? 0);
    $fecha = $factura["fecha"] ?? date("Y-m-d H:i:s");
    $estadoPago = $factura["estado_pago"] ?? "Pendiente";
    $estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";
    $montoPagado = (float)($factura["monto_pagado"] ?? 0);
    $saldoPendiente = (float)($factura["saldo_pendiente"] ?? 0);

    $historial = [];

    $historial[] = [
        "id_historial" => null,
        "id_factura" => $idFactura,
        "tipo_evento" => "Factura creada",
        "estado_pago_anterior" => null,
        "estado_pago_nuevo" => $estadoPago,
        "estado_produccion_anterior" => null,
        "estado_produccion_nuevo" => "Pendiente",
        "monto_abonado" => $montoPagado,
        "saldo_nuevo" => $saldoPendiente,
        "comentario" => "Registro base generado desde la información actual de la factura.",
        "fecha_evento" => $fecha,
        "generado_desde_factura" => true,
    ];

    if ($estadoProduccion !== "Pendiente") {
        $historial[] = [
            "id_historial" => null,
            "id_factura" => $idFactura,
            "tipo_evento" => "Estado de producción actual",
            "estado_pago_anterior" => null,
            "estado_pago_nuevo" => $estadoPago,
            "estado_produccion_anterior" => "Pendiente",
            "estado_produccion_nuevo" => $estadoProduccion,
            "monto_abonado" => 0,
            "saldo_nuevo" => $saldoPendiente,
            "comentario" => "La factura actualmente se encuentra en estado de producción: {$estadoProduccion}.",
            "fecha_evento" => $factura["fecha_orden_produccion"] ?? $fecha,
            "generado_desde_factura" => true,
        ];
    }

    if (!empty($factura["fecha_entrega_real"])) {
        $historial[] = [
            "id_historial" => null,
            "id_factura" => $idFactura,
            "tipo_evento" => "Entrega registrada",
            "estado_pago_anterior" => null,
            "estado_pago_nuevo" => $estadoPago,
            "estado_produccion_anterior" => null,
            "estado_produccion_nuevo" => "Entregada",
            "monto_abonado" => 0,
            "saldo_nuevo" => $saldoPendiente,
            "comentario" => "La factura tiene fecha real de entrega registrada.",
            "fecha_evento" => $factura["fecha_entrega_real"] . " 18:00:00",
            "generado_desde_factura" => true,
        ];
    }

    return $historial;
}

function construirResumenHistorialFactura(array $factura, array $historialEstados): array
{
    $totalEventos = count($historialEstados);
    $eventosPago = 0;
    $eventosProduccion = 0;
    $eventosCancelacion = 0;
    $totalAbonadoHistorial = 0.0;
    $ultimoEvento = null;

    foreach ($historialEstados as $evento) {
        $tipoEvento = mb_strtolower((string)($evento["tipo_evento"] ?? ""));
        $montoAbonado = (float)($evento["monto_abonado"] ?? 0);

        if ($montoAbonado > 0) {
            $eventosPago++;
            $totalAbonadoHistorial += $montoAbonado;
        }

        if (
            str_contains($tipoEvento, "producción") ||
            str_contains($tipoEvento, "produccion") ||
            str_contains($tipoEvento, "entrega") ||
            str_contains($tipoEvento, "lista")
        ) {
            $eventosProduccion++;
        }

        if (str_contains($tipoEvento, "cancel")) {
            $eventosCancelacion++;
        }

        if ($ultimoEvento === null) {
            $ultimoEvento = $evento;
            continue;
        }

        $fechaActual = strtotime((string)($evento["fecha_evento"] ?? ""));
        $fechaUltimo = strtotime((string)($ultimoEvento["fecha_evento"] ?? ""));

        if ($fechaActual > $fechaUltimo) {
            $ultimoEvento = $evento;
        }
    }

    $totalFactura = (float)($factura["total"] ?? 0);
    $montoPagado = (float)($factura["monto_pagado"] ?? 0);
    $saldoPendiente = (float)($factura["saldo_pendiente"] ?? max(0, $totalFactura - $montoPagado));
    $porcentajePagado = $totalFactura > 0 ? ($montoPagado / $totalFactura) * 100 : 0;

    $estadoPago = $factura["estado_pago"] ?? "Pendiente";
    $estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";

    $flujoPago = [
        "Pendiente" => in_array($estadoPago, ["Pendiente", "Parcial", "Pagado"], true),
        "Parcial" => in_array($estadoPago, ["Parcial", "Pagado"], true),
        "Pagado" => $estadoPago === "Pagado",
    ];

    $flujoProduccion = [
        "Pendiente" => true,
        "En producción" => in_array($estadoProduccion, ["En producción", "Lista para entregar", "Entregada"], true),
        "Lista para entregar" => in_array($estadoProduccion, ["Lista para entregar", "Entregada"], true),
        "Entregada" => $estadoProduccion === "Entregada",
        "Cancelada" => $estadoProduccion === "Cancelada",
    ];

    $diasDesdeCreacion = null;

    if (!empty($factura["fecha"])) {
        $inicio = new DateTime((string)$factura["fecha"]);
        $hoy = new DateTime();
        $diasDesdeCreacion = (int)$inicio->diff($hoy)->format("%a");
    }

    $estadoOperativo = "En seguimiento";

    if ($estadoProduccion === "Cancelada") {
        $estadoOperativo = "Cancelada";
    } elseif ($estadoProduccion === "Entregada" && $estadoPago === "Pagado") {
        $estadoOperativo = "Completada";
    } elseif ($estadoProduccion === "Entregada") {
        $estadoOperativo = "Entregada con saldo pendiente";
    } elseif ($estadoProduccion === "Lista para entregar") {
        $estadoOperativo = "Lista para entregar";
    } elseif ($estadoProduccion === "En producción") {
        $estadoOperativo = "En producción";
    } elseif ($estadoPago === "Parcial") {
        $estadoOperativo = "Abonada pendiente de producción";
    }

    return [
        "totalEventos" => $totalEventos,
        "eventosPago" => $eventosPago,
        "eventosProduccion" => $eventosProduccion,
        "eventosCancelacion" => $eventosCancelacion,
        "totalAbonadoHistorial" => $totalAbonadoHistorial,
        "ultimoEvento" => $ultimoEvento,
        "montoPagado" => $montoPagado,
        "saldoPendiente" => $saldoPendiente,
        "porcentajePagado" => $porcentajePagado,
        "flujoPago" => $flujoPago,
        "flujoProduccion" => $flujoProduccion,
        "historialGenerado" => !empty($historialEstados[0]["generado_desde_factura"]),
        "diasDesdeCreacion" => $diasDesdeCreacion,
        "estadoOperativo" => $estadoOperativo,
        "fechaEntregaEstimada" => $factura["fecha_entrega_estimada"] ?? null,
    ];
}
