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

    $resumenHistorial = construirResumenHistorialFactura($factura, $historialEstados);

    return [
        "user" => $user,
        "factura" => $factura,
        "detalles" => $detalles,
        "historialEstados" => $historialEstados,
        "resumenHistorial" => $resumenHistorial,
    ];
}

function construirResumenHistorialFactura(array $factura, array $historialEstados): array
{
    $totalEventos = count($historialEstados);
    $totalAbonadoHistorial = 0.0;
    $eventosPago = 0;
    $eventosProduccion = 0;
    $eventosCancelacion = 0;
    $ultimoEvento = null;

    foreach ($historialEstados as $evento) {
        $tipoEvento = strtolower((string)($evento["tipo_evento"] ?? ""));
        $montoAbonado = (float)($evento["monto_abonado"] ?? 0);

        if ($montoAbonado > 0) {
            $totalAbonadoHistorial += $montoAbonado;
            $eventosPago++;
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

    $fechaFactura = $factura["fecha"] ?? null;
    $fechaEntregaEstimada = $factura["fecha_entrega_estimada"] ?? null;

    $diasDesdeCreacion = null;

    if (!empty($fechaFactura)) {
        $inicio = new DateTime((string)$fechaFactura);
        $hoy = new DateTime();
        $diasDesdeCreacion = (int)$inicio->diff($hoy)->format("%a");
    }

    $estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";
    $estadoPago = $factura["estado_pago"] ?? "Pendiente";

    $estaCompletada = $estadoProduccion === "Entregada";
    $estaCancelada = $estadoProduccion === "Cancelada";
    $estaPagada = $estadoPago === "Pagado";

    $estadoOperativo = "En seguimiento";

    if ($estaCancelada) {
        $estadoOperativo = "Cancelada";
    } elseif ($estaCompletada && $estaPagada) {
        $estadoOperativo = "Completada";
    } elseif ($estaCompletada) {
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
        "totalAbonadoHistorial" => $totalAbonadoHistorial,
        "eventosPago" => $eventosPago,
        "eventosProduccion" => $eventosProduccion,
        "eventosCancelacion" => $eventosCancelacion,
        "ultimoEvento" => $ultimoEvento,
        "montoPagado" => $montoPagado,
        "saldoPendiente" => $saldoPendiente,
        "porcentajePagado" => $porcentajePagado,
        "diasDesdeCreacion" => $diasDesdeCreacion,
        "estadoOperativo" => $estadoOperativo,
        "fechaEntregaEstimada" => $fechaEntregaEstimada,
    ];
}
