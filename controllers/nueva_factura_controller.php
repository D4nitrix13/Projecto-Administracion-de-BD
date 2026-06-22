<?php

require_once __DIR__ . "/../repositories/ClienteRepository.php";
require_once __DIR__ . "/../repositories/ProductoRepository.php";
require_once __DIR__ . "/../repositories/SeccionRepository.php";
require_once __DIR__ . "/../repositories/PlazoRepository.php";
require_once __DIR__ . "/../services/FacturaService.php";
require_once __DIR__ . "/../services/PlazoService.php";

function obtenerDatosNuevaFactura(): array
{
    $user = $_SESSION["user"];
    $idRol = (int)($user["id_rol"] ?? 0);

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $clienteRepository = new ClienteRepository($connection);
    $productoRepository = new ProductoRepository($connection);
    $seccionRepository = new SeccionRepository($connection);
    $facturaService = new FacturaService(
        $connection,
        new FacturaValidationService($connection),
        new FacturaCalculationService($connection, $productoRepository),
    );

    $error = null;

    $clientes = $clienteRepository->obtenerClientesHabituales();
    $productos = $productoRepository->obtenerProductosParaFactura();

    $seccionUsuario = null;
    $secciones = [];

    if (!empty($user["id_seccion"])) {
        $seccionUsuario = $seccionRepository->obtenerSeccionPorId((int)$user["id_seccion"]);
    } else {
        $secciones = $seccionRepository->obtenerTodasLasSecciones();
    }

    $idCliente = "";
    $idSeccion = $user["id_seccion"] ?? "";
    $descuentoGlobal = "0.00";
    $tipoClienteVenta = TIPO_CLIENTE_HABITUAL;
    $nombreClienteFugaz = "";
    $montoPagado = "0.00";
    $fechaEntregaEstimada = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = $facturaService->registrarFactura($_POST, $user);

        if ($resultado["success"]) {
            $idFacturaCreada = (int)$resultado["id_factura"];

            $plazosData = $_POST["plazos_data"] ?? "";
            if (!empty($plazosData)) {
                $plazosParsed = json_decode($plazosData, true);
                if (is_array($plazosParsed) && count($plazosParsed) > 0) {
                    $totalFactura = (float)($_POST["total_calculado"] ?? 0);
                    $montoPagadoVal = (float)($_POST["monto_pagado"] ?? 0);
                    $saldoPendiente = max(0, $totalFactura - $montoPagadoVal);

                    if ($saldoPendiente > 0.01) {
                        $fechaLimite = end($plazosParsed)["fecha_pago"] ?? "";
                        if (!empty($fechaLimite)) {
                            $plazoService = new PlazoService(new PlazoRepository($connection));
                            $plazoService->crearPlan(
                                $idFacturaCreada,
                                $totalFactura,
                                $fechaLimite,
                                $plazosParsed
                            );
                        }
                    }
                }
            }

            $_SESSION["flash_success"] = "Factura registrada correctamente.";
            header("Location: detalle_factura.php?id=" . $idFacturaCreada);
            exit();
        }

        $error = $resultado["message"];

        $idCliente = $_POST["id_cliente"] ?? "";
        $idSeccion = $_POST["id_seccion"] ?? ($user["id_seccion"] ?? "");
        $descuentoGlobal = $_POST["descuento_global"] ?? "0.00";
        $tipoClienteVenta = $_POST["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL;
        $nombreClienteFugaz = $_POST["nombre_cliente_fugaz"] ?? "";
        $montoPagado = $_POST["monto_pagado"] ?? "0.00";
        $fechaEntregaEstimada = $_POST["fecha_entrega_estimada"] ?? "";
    }

    $textoSubtitulo = obtenerTextoSubtituloNuevaFactura($idRol);

    return [
        "user" => $user,
        "error" => $error,
        "clientes" => $clientes,
        "productos" => $productos,
        "secciones" => $secciones,
        "seccionUsuario" => $seccionUsuario,
        "idCliente" => $idCliente,
        "idSeccion" => $idSeccion,
        "descuentoGlobal" => $descuentoGlobal,
        "tipoClienteVenta" => $tipoClienteVenta,
        "nombreClienteFugaz" => $nombreClienteFugaz,
        "montoPagado" => $montoPagado,
        "fechaEntregaEstimada" => $fechaEntregaEstimada,
        "textoSubtitulo" => $textoSubtitulo,
    ];
}

function obtenerTextoSubtituloNuevaFactura(int $idRol): string
{
    if ($idRol === 1) {
        return "Registre una nueva venta para Panda Estampados y Kitsune.";
    }

    return "Registre una nueva venta para Kitsune.";
}
