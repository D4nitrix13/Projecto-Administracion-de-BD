<?php

declare(strict_types=1);

namespace App\Service;

class FacturaService
{
    public function __construct(
        private \PDO $connection,
        private FacturaValidationService $validation,
        private FacturaCalculationService $calculation,
    ) {}

    public function registrarFactura(array $post, array $user): array
    {
        try {
            $tipoCliente = $this->validation->normalizarTipoClienteVenta(
                $post["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL
            );

            $idCliente = $this->validation->resolverIdCliente($post, $tipoCliente);
            $idSeccion = $this->validation->resolverIdSeccion($post, $user);
            $descuentoGlobal = trim($post["descuento_global"] ?? "0");
            $items = $this->validation->obtenerItemsDesdePost($post);

            $error = $this->validation->validarDatosFactura($idCliente, $idSeccion, $items, $descuentoGlobal);

            if ($error !== null) {
                return ["success" => false, "message" => $error, "id_factura" => null];
            }

            $productosMap = $this->calculation->obtenerProductosMap($items);
            $this->validation->validarProductosYStock($items, $productosMap);

            $totales = $this->calculation->calcularTotales($items, $productosMap, (float) $descuentoGlobal);

            $errorFugaz = $this->validation->validarLimiteClienteFugaz($tipoCliente, $totales["total"]);

            if ($errorFugaz !== null) {
                return ["success" => false, "message" => $errorFugaz, "id_factura" => null];
            }

            $montoPagado = $this->calculation->normalizarMontoPagado($post["monto_pagado"] ?? "0");
            $fechaEntrega = trim((string) ($post["fecha_entrega_estimada"] ?? ""));

            $errorPago = $this->validation->validarPagoProduccion($montoPagado, $totales["total"], $fechaEntrega);

            if ($errorPago !== null) {
                return ["success" => false, "message" => $errorPago, "id_factura" => null];
            }

            $datosPago = $this->calculation->calcularDatosPagoProduccion($montoPagado, $totales["total"], $fechaEntrega);

            $this->connection->beginTransaction();

            $idFactura = $this->insertarFactura(
                $idCliente, $idSeccion, $user, $tipoCliente,
                trim($post["nombre_cliente_fugaz"] ?? ""),
                $totales, $items, $datosPago
            );

            $this->connection->commit();

            return ["success" => true, "message" => "Factura registrada.", "id_factura" => $idFactura];
        } catch (\Throwable $e) {
            $this->rollback();
            return ["success" => false, "message" => "Error: " . $e->getMessage(), "id_factura" => null];
        }
    }

    public function eliminarFactura(int $idFactura): array
    {
        try {
            if ($idFactura <= 0) {
                return ["success" => false, "message" => "Factura inválida."];
            }

            $this->connection->beginTransaction();
            $this->connection->prepare("CALL eliminar_factura_sistema(:id_factura)")
                ->execute([":id_factura" => $idFactura]);
            $this->connection->commit();

            return ["success" => true, "message" => "Factura eliminada y stock restaurado."];
        } catch (\Throwable $e) {
            $this->rollback();
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }

    public function editarFactura(int $idFactura, array $post, array $user): array
    {
        try {
            if ($idFactura <= 0) {
                return ["success" => false, "message" => "Factura inválida."];
            }

            $tipoCliente = $this->validation->normalizarTipoClienteVenta(
                $post["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL
            );

            $idCliente = $this->validation->resolverIdCliente($post, $tipoCliente);
            $idUsuario = isset($post["id_usuario"]) ? (int) $post["id_usuario"] : (int) $user["id_usuario"];
            $idSeccion = $this->validation->resolverIdSeccion($post, $user);
            $fecha = trim((string) ($post["fecha"] ?? ""));
            $descuentoGlobal = trim((string) ($post["descuento_global"] ?? "0"));
            $nombreFugaz = trim((string) ($post["nombre_cliente_fugaz"] ?? ""));
            $items = $this->validation->obtenerItemsDesdePost($post);

            if ($fecha === "") {
                return ["success" => false, "message" => "La fecha es obligatoria."];
            }

            if ($tipoCliente === TIPO_CLIENTE_FUGAZ && $nombreFugaz === "") {
                return ["success" => false, "message" => "Nombre del cliente fugaz es obligatorio."];
            }

            $error = $this->validation->validarDatosFactura($idCliente, $idSeccion, $items, $descuentoGlobal);

            if ($error !== null) {
                return ["success" => false, "message" => $error];
            }

            $productosMap = $this->calculation->obtenerProductosMap($items);
            $totales = $this->calculation->calcularTotales($items, $productosMap, (float) $descuentoGlobal);

            $errorFugaz = $this->validation->validarLimiteClienteFugaz($tipoCliente, $totales["total"]);

            if ($errorFugaz !== null) {
                return ["success" => false, "message" => $errorFugaz];
            }

            $montoPagado = $this->calculation->normalizarMontoPagado($post["monto_pagado"] ?? "0");
            $fechaEntrega = trim((string) ($post["fecha_entrega_estimada"] ?? ""));

            $errorPago = $this->validation->validarPagoProduccion($montoPagado, $totales["total"], $fechaEntrega);

            if ($errorPago !== null) {
                return ["success" => false, "message" => $errorPago];
            }

            $datosPago = $this->calculation->calcularDatosPagoProduccion($montoPagado, $totales["total"], $fechaEntrega);

            $this->connection->beginTransaction();

            $this->connection->prepare("
                CALL editar_factura_sistema(
                    :id_factura, :fecha, :id_cliente, :id_usuario, :id_seccion,
                    :tipo_cliente_venta, :nombre_cliente_fugaz, :descuento_global,
                    :iva, CAST(:items AS JSONB)
                )
            ")->execute([
                ":id_factura"           => $idFactura,
                ":fecha"                => date("Y-m-d H:i:s", strtotime($fecha)),
                ":id_cliente"           => $idCliente,
                ":id_usuario"           => $idUsuario,
                ":id_seccion"           => $idSeccion,
                ":tipo_cliente_venta"   => $tipoCliente,
                ":nombre_cliente_fugaz" => $tipoCliente === TIPO_CLIENTE_FUGAZ ? $nombreFugaz : null,
                ":descuento_global"     => (float) $descuentoGlobal,
                ":iva"                  => IVA_RATE,
                ":items"                => json_encode($items, JSON_THROW_ON_ERROR),
            ]);

            $this->actualizarPagoProduccion($idFactura, $datosPago);
            $this->connection->commit();

            return ["success" => true, "message" => "Factura actualizada."];
        } catch (\Throwable $e) {
            $this->rollback();
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }

    public function obtenerFacturaParaEditar(int $idFactura): ?array
    {
        $statement = $this->connection->prepare("
            SELECT * FROM obtener_factura_para_impresion(:id_factura) LIMIT 1
        ");

        $statement->execute([":id_factura" => $idFactura]);
        $factura = $statement->fetch(\PDO::FETCH_ASSOC);

        return $factura ?: null;
    }

    public function obtenerDetallesFacturaParaEditar(int $idFactura): array
    {
        $statement = $this->connection->prepare("
            SELECT * FROM obtener_detalles_factura_edicion(:id_factura)
        ");

        $statement->execute([":id_factura" => $idFactura]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerClienteFacturaParaEditar(int $idCliente): ?array
    {
        $statement = $this->connection->prepare("
            SELECT * FROM obtener_cliente_factura_edicion(:id_cliente) LIMIT 1
        ");

        $statement->execute([":id_cliente" => $idCliente]);
        $cliente = $statement->fetch(\PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function obtenerLimiteClienteFugazParaVista(): float
    {
        return $this->validation->obtenerLimiteClienteFugaz();
    }

    private function insertarFactura(
        int $idCliente,
        int $idSeccion,
        array $user,
        string $tipoCliente,
        string $nombreFugaz,
        array $totales,
        array $items,
        array $datosPago
    ): int {
        $statement = $this->connection->prepare("
            SELECT registrar_factura_sistema(
                :id_cliente, :id_usuario, :id_seccion,
                :subtotal, :descuento, :impuesto, :total,
                :tipo_cliente_venta, :nombre_cliente_fugaz,
                CAST(:items AS JSONB)
            ) AS id_factura
        ");

        $statement->execute([
            ":id_cliente"           => $idCliente,
            ":id_usuario"           => (int) $user["id_usuario"],
            ":id_seccion"           => $idSeccion,
            ":subtotal"             => $totales["subtotal"],
            ":descuento"            => $totales["descuento"],
            ":impuesto"             => $totales["impuesto"],
            ":total"                => $totales["total"],
            ":tipo_cliente_venta"   => $tipoCliente,
            ":nombre_cliente_fugaz" => $nombreFugaz,
            ":items"                => json_encode($items, JSON_THROW_ON_ERROR),
        ]);

        $idFactura = (int) $statement->fetchColumn();

        if ($idFactura <= 0) {
            throw new \Exception("No se pudo obtener el ID de la factura.");
        }

        $this->actualizarPagoProduccion($idFactura, $datosPago);

        return $idFactura;
    }

    private function actualizarPagoProduccion(int $idFactura, array $datos): void
    {
        $this->connection->prepare("
            UPDATE factura
            SET monto_pagado = :monto_pagado,
                saldo_pendiente = :saldo_pendiente,
                porcentaje_pagado = :porcentaje_pagado,
                estado_pago = :estado_pago,
                estado_produccion = :estado_produccion,
                fecha_orden_produccion = CURRENT_TIMESTAMP,
                fecha_entrega_estimada = :fecha_entrega_estimada
            WHERE id_factura = :id_factura
        ")->execute([
            ":id_factura"            => $idFactura,
            ":monto_pagado"          => $datos["monto_pagado"],
            ":saldo_pendiente"       => $datos["saldo_pendiente"],
            ":porcentaje_pagado"     => $datos["porcentaje_pagado"],
            ":estado_pago"           => $datos["estado_pago"],
            ":estado_produccion"     => $datos["estado_produccion"],
            ":fecha_entrega_estimada" => $datos["fecha_entrega_estimada"],
        ]);
    }

    private function rollback(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }
    }
}
