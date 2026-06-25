<?php

declare(strict_types=1);

namespace App\Service;

require_once __DIR__ . "/../../helpers/notificaciones.php";

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

            $datosPago = $this->calculation->calcularDatosPagoProduccion(0.0, $totales["total"]);

            $this->connection->beginTransaction();

            $idFactura = $this->insertarFactura(
                $idCliente, $idSeccion, $user, $tipoCliente,
                trim($post["nombre_cliente_fugaz"] ?? ""),
                $totales, $items, $datosPago
            );

            $this->connection->commit();

            \notificar("factura_creada", "Factura creada", "Nueva factura #{$idFactura} — $" . number_format($totales["total"], 2), [
                "id_usuario_origen" => (int) $user["id_usuario"],
                "id_seccion" => $idSeccion,
                "rol_origen" => $user["rol"] ?? "",
                "metadata" => ["factura_id" => $idFactura, "total" => $totales["total"]],
            ]);

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
            $stmt = $this->connection->prepare(
                "SELECT eliminar_factura_sistema(:id_factura) AS eliminado"
            );
            $stmt->execute([":id_factura" => $idFactura]);
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->connection->commit();

            if (!empty($resultado["eliminado"])) {
                return ["success" => true, "message" => "Factura eliminada y stock restaurado."];
            }

            return ["success" => false, "message" => "No se pudo eliminar la factura."];
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

            $this->actualizarTotalesFactura($idFactura, $totales);
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

    private function actualizarTotalesFactura(int $idFactura, array $totales): void
    {
        $this->connection->prepare("
            UPDATE factura
            SET subtotal = :subtotal,
                descuento = :descuento,
                impuesto = :impuesto,
                total = :total,
                saldo_pendiente = GREATEST(:total - monto_pagado, 0),
                porcentaje_pagado = CASE
                    WHEN :total > 0 THEN ROUND((monto_pagado / :total) * 100, 2)
                    ELSE 0
                END
            WHERE id_factura = :id_factura
        ")->execute([
            ":id_factura" => $idFactura,
            ":subtotal"   => $totales["subtotal"],
            ":descuento"  => $totales["descuento"],
            ":impuesto"   => $totales["impuesto"],
            ":total"      => $totales["total"],
        ]);
    }

    public function registrarAbono(int $idFactura, float $montoAbono, string $comentario, array $user): array
    {
        try {
            if ($idFactura <= 0) {
                return ["success" => false, "message" => "Factura inválida."];
            }

            if ($montoAbono <= 0) {
                return ["success" => false, "message" => "El monto del abono debe ser mayor a cero."];
            }

            $stmt = $this->connection->prepare("
                SELECT id_factura, total, monto_pagado, saldo_pendiente, porcentaje_pagado,
                       estado_pago, estado_produccion
                FROM Factura
                WHERE id_factura = :id
                FOR UPDATE
            ");
            $stmt->execute([":id" => $idFactura]);
            $factura = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$factura) {
                return ["success" => false, "message" => "Factura no encontrada."];
            }

            $total = (float) $factura["total"];
            $montoPagadoActual = (float) $factura["monto_pagado"];
            $saldoPendiente = (float) $factura["saldo_pendiente"];

            if ($montoAbono > $saldoPendiente + 0.01) {
                return ["success" => false, "message" => "El abono excede el saldo pendiente de C$ " . number_format($saldoPendiente, 2) . "."];
            }

            $nuevoMontoPagado = round($montoPagadoActual + $montoAbono, 2);
            $nuevoSaldo = round(max(0.0, $total - $nuevoMontoPagado), 2);
            $nuevoPorcentaje = $total > 0 ? round(($nuevoMontoPagado / $total) * 100, 2) : 0.0;

            $nuevoEstadoPago = $this->calculation->calcularEstadoPago($nuevoMontoPagado, $nuevoSaldo);

            $estadoProduccionActual = $factura["estado_produccion"];
            $nuevoEstadoProduccion = $estadoProduccionActual;

            if ($estadoProduccionActual === "Pendiente" && $nuevoPorcentaje >= 50.0) {
                $nuevoEstadoProduccion = "En producción";
            }

            $this->connection->beginTransaction();

            $this->connection->prepare("
                UPDATE factura
                SET monto_pagado = :monto_pagado,
                    saldo_pendiente = :saldo_pendiente,
                    porcentaje_pagado = :porcentaje_pagado,
                    estado_pago = :estado_pago,
                    estado_produccion = :estado_produccion
                WHERE id_factura = :id_factura
            ")->execute([
                ":id_factura"        => $idFactura,
                ":monto_pagado"      => $nuevoMontoPagado,
                ":saldo_pendiente"   => $nuevoSaldo,
                ":porcentaje_pagado" => $nuevoPorcentaje,
                ":estado_pago"       => $nuevoEstadoPago,
                ":estado_produccion" => $nuevoEstadoProduccion,
            ]);

            $this->marcarCuotaPagadaSiAplica($idFactura, $montoAbono);

            $this->connection->commit();

            $mensajeAbono = "Abono de C$ " . number_format($montoAbono, 2) . " en factura #{$idFactura}";

            if ($nuevoEstadoPago === "Pagado") {
                \notificar("factura_pagada", "Factura pagada", "La factura #{$idFactura} ha sido pagada completamente.", [
                    "id_usuario_origen" => (int) $user["id_usuario"],
                    "metadata" => ["factura_id" => $idFactura, "monto" => $nuevoMontoPagado],
                ]);
            } else {
                \notificar("pago_recibido", "Pago recibido", $mensajeAbono, [
                    "id_usuario_origen" => (int) $user["id_usuario"],
                    "metadata" => ["factura_id" => $idFactura, "monto" => $montoAbono],
                ]);
            }

            $mensajeExito = "Abono registrado. Nuevo saldo: C$ " . number_format($nuevoSaldo, 2);

            if ($nuevoEstadoProduccion !== $estadoProduccionActual) {
                $mensajeExito .= " — Producción iniciada.";
            }

            return [
                "success" => true,
                "message" => $mensajeExito,
                "nuevo_estado_pago" => $nuevoEstadoPago,
                "nuevo_estado_produccion" => $nuevoEstadoProduccion,
            ];
        } catch (\Throwable $e) {
            $this->rollback();
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }

    private function marcarCuotaPagadaSiAplica(int $idFactura, float $montoAbono): void
    {
        $stmt = $this->connection->prepare("
            SELECT id_plazo FROM plazo WHERE id_factura = :id AND estado = 'Activo' LIMIT 1
        ");
        $stmt->execute([":id" => $idFactura]);
        $plazo = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$plazo) {
            return;
        }

        $idPlazo = (int) $plazo["id_plazo"];
        $montoRestante = $montoAbono;

        $stmtCuotas = $this->connection->prepare("
            SELECT id_cuota, monto, monto_pagado
            FROM plazo_cuota
            WHERE id_plazo = :id_plazo AND estado = 'Pendiente'
            ORDER BY numero ASC
        ");
        $stmtCuotas->execute([":id_plazo" => $idPlazo]);

        while ($montoRestante > 0.01) {
            $cuota = $stmtCuotas->fetch(\PDO::FETCH_ASSOC);

            if (!$cuota) {
                break;
            }

            $montoCuota = (float) $cuota["monto"];
            $montoPagadoCuota = (float) $cuota["monto_pagado"];
            $restanteCuota = $montoCuota - $montoPagadoCuota;
            $montoParaCuota = min($montoRestante, $restanteCuota);
            $nuevoPagadoCuota = round($montoPagadoCuota + $montoParaCuota, 2);
            $nuevoEstadoCuota = $nuevoPagadoCuota >= $montoCuota - 0.01 ? "Pagado" : "Pendiente";

            $this->connection->prepare("
                UPDATE plazo_cuota
                SET monto_pagado = :monto_pagado,
                    estado = :estado,
                    fecha_pago_real = CASE
                        WHEN CAST(:estado AS varchar) = 'Pagado'::varchar THEN CURRENT_TIMESTAMP
                        ELSE fecha_pago_real
                    END
                WHERE id_cuota = :id_cuota
            ")->execute([
                ":monto_pagado" => $nuevoPagadoCuota,
                ":estado"       => $nuevoEstadoCuota,
                ":id_cuota"     => (int) $cuota["id_cuota"],
            ]);

            $montoRestante = round($montoRestante - $montoParaCuota, 2);
        }

        $stmtAll = $this->connection->prepare("
            SELECT COUNT(*) FILTER (WHERE estado = 'Pendiente') AS pendientes
            FROM plazo_cuota
            WHERE id_plazo = :id_plazo
        ");
        $stmtAll->execute([":id_plazo" => $idPlazo]);
        $counts = $stmtAll->fetch(\PDO::FETCH_ASSOC);

        if ((int) $counts["pendientes"] === 0) {
            $this->connection->prepare("
                UPDATE plazo SET estado = 'Completado' WHERE id_plazo = :id_plazo
            ")->execute([":id_plazo" => $idPlazo]);
        }
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

        if ($datos["monto_pagado"] > 0) {
            $monto = $datos["monto_pagado"];
            $mensaje = "Abono de $" . number_format($monto, 2) . " en factura #{$idFactura}";

            if ($datos["estado_pago"] === "Pagado") {
                \notificar("factura_pagada", "Factura pagada", "La factura #{$idFactura} ha sido pagada completamente", [
                    "id_seccion" => null,
                    "metadata" => ["factura_id" => $idFactura, "monto" => $monto],
                ]);
            } else {
                \notificar("pago_recibido", "Pago recibido", $mensaje, [
                    "id_seccion" => null,
                    "metadata" => ["factura_id" => $idFactura, "monto" => $monto],
                ]);
            }
        }
    }

    private function rollback(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }
    }
}
