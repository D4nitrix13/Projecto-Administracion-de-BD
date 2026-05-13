<?php
// * Stored function or procedure has been executed

class FacturaService
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function registrarFactura(array $post, array $user): array
    {
        try {
            $tipoClienteVenta = $this->normalizarTipoClienteVenta(
                $post["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL
            );

            $idCliente = $this->resolverIdCliente($post, $tipoClienteVenta);
            $idSeccion = $this->resolverIdSeccion($post, $user);
            $descuentoGlobal = trim($post["descuento_global"] ?? "0");

            $items = $this->obtenerItemsDesdePost($post);

            $error = $this->validarDatosFactura(
                $idCliente,
                $idSeccion,
                $items,
                $descuentoGlobal
            );

            if ($error !== null) {
                return [
                    "success" => false,
                    "message" => $error,
                    "id_factura" => null,
                ];
            }

            $productosMap = $this->obtenerProductosMap($items);

            $this->validarProductosYStock($items, $productosMap);

            $totales = $this->calcularTotales(
                $items,
                $productosMap,
                (float)$descuentoGlobal
            );

            $errorClienteFugaz = $this->validarLimiteClienteFugaz(
                $tipoClienteVenta,
                $totales["total"]
            );

            if ($errorClienteFugaz !== null) {
                return [
                    "success" => false,
                    "message" => $errorClienteFugaz,
                    "id_factura" => null,
                ];
            }

            $montoPagado = $this->normalizarMontoPagado($post["monto_pagado"] ?? "0");
            $fechaEntregaEstimada = trim((string)($post["fecha_entrega_estimada"] ?? ""));

            $errorPagoProduccion = $this->validarPagoProduccion(
                $montoPagado,
                $totales["total"],
                $fechaEntregaEstimada
            );

            if ($errorPagoProduccion !== null) {
                return [
                    "success" => false,
                    "message" => $errorPagoProduccion,
                    "id_factura" => null,
                ];
            }

            $datosPagoProduccion = $this->calcularDatosPagoProduccion(
                $montoPagado,
                $totales["total"],
                $fechaEntregaEstimada
            );

            $this->connection->beginTransaction();

            $idFactura = $this->insertarFactura(
                $idCliente,
                $idSeccion,
                $user,
                $tipoClienteVenta,
                trim($post["nombre_cliente_fugaz"] ?? ""),
                $totales,
                $items,
                $datosPagoProduccion
            );

            $this->connection->commit();

            return [
                "success" => true,
                "message" => "Factura registrada correctamente.",
                "id_factura" => $idFactura,
            ];
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            return [
                "success" => false,
                "message" => "Error al registrar la factura: " . $exception->getMessage(),
                "id_factura" => null,
            ];
        }
    }

    private function normalizarTipoClienteVenta(string $tipo): string
    {
        return $tipo === TIPO_CLIENTE_FUGAZ
            ? TIPO_CLIENTE_FUGAZ
            : TIPO_CLIENTE_HABITUAL;
    }

    private function resolverIdCliente(array $post, string $tipoClienteVenta): int
    {
        if ($tipoClienteVenta === TIPO_CLIENTE_HABITUAL) {
            return isset($post["id_cliente"]) ? (int)$post["id_cliente"] : 0;
        }

        return $this->obtenerIdClienteFugaz();
    }

    private function obtenerIdClienteFugaz(): int
    {
        $statement = $this->connection->query("
            SELECT obtener_id_cliente_fugaz()
        ");

        $id = $statement->fetchColumn();

        return $id ? (int)$id : 0;
    }

    private function resolverIdSeccion(array $post, array $user): int
    {
        if (!empty($user["id_seccion"])) {
            return (int)$user["id_seccion"];
        }

        return isset($post["id_seccion"]) && $post["id_seccion"] !== ""
            ? (int)$post["id_seccion"]
            : 0;
    }

    private function obtenerItemsDesdePost(array $post): array
    {
        $idsProductos = $post["id_producto"] ?? [];
        $cantidades = $post["cantidad"] ?? [];
        $descuentosLinea = $post["descuento_linea"] ?? [];

        $items = [];

        for ($i = 0; $i < count($idsProductos); $i++) {
            $idProducto = (int)($idsProductos[$i] ?? 0);
            $cantidad = (int)($cantidades[$i] ?? 0);
            $descuentoLineaRaw = trim((string)($descuentosLinea[$i] ?? "0"));

            if ($idProducto <= 0 && $cantidad <= 0) {
                continue;
            }

            $descuentoLinea = is_numeric($descuentoLineaRaw)
                ? (float)$descuentoLineaRaw
                : 0.0;

            $items[] = [
                "id_producto" => $idProducto,
                "cantidad" => $cantidad,
                "descuento_linea" => max(0.0, $descuentoLinea),
            ];
        }

        return $items;
    }

    private function validarDatosFactura(
        int $idCliente,
        int $idSeccion,
        array $items,
        string $descuentoGlobal
    ): ?string {
        if ($idCliente <= 0) {
            return "Debe seleccionar un cliente válido.";
        }

        if ($idSeccion <= 0) {
            return "Debe seleccionar una sección válida.";
        }

        if (empty($items)) {
            return "Debe agregar al menos un producto a la factura.";
        }

        if ($descuentoGlobal !== "" && !is_numeric($descuentoGlobal)) {
            return "El descuento global debe ser numérico.";
        }

        foreach ($items as $item) {
            if ((int)$item["id_producto"] <= 0) {
                return "Debe seleccionar un producto válido en todas las filas.";
            }

            if ((int)$item["cantidad"] < 1) {
                return "La cantidad mínima por producto es 1.";
            }
        }

        return null;
    }

    private function obtenerProductosMap(array $items): array
    {
        $idsProductos = array_unique(array_column($items, "id_producto"));
        $idsProductosPgArray = $this->convertirArrayPostgres($idsProductos);

        $statement = $this->connection->prepare("
            SELECT
                id_producto,
                precio_venta,
                stock,
                nombre
            FROM obtener_productos_factura_por_ids(
                CAST(:ids_productos AS INT[])
            )
        ");

        $statement->execute([
            ":ids_productos" => $idsProductosPgArray,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $productosMap = [];

        foreach ($rows as $row) {
            $productosMap[(int)$row["id_producto"]] = [
                "nombre" => (string)$row["nombre"],
                "precio_venta" => (float)$row["precio_venta"],
                "stock" => (int)$row["stock"],
            ];
        }

        return $productosMap;
    }

    private function validarProductosYStock(array $items, array $productosMap): void
    {
        $cantidadPorProducto = [];

        foreach ($items as $item) {
            $idProducto = (int)$item["id_producto"];

            if (!isset($productosMap[$idProducto])) {
                throw new Exception("Uno de los productos seleccionados no existe.");
            }

            if (!isset($cantidadPorProducto[$idProducto])) {
                $cantidadPorProducto[$idProducto] = 0;
            }

            $cantidadPorProducto[$idProducto] += (int)$item["cantidad"];
        }

        foreach ($cantidadPorProducto as $idProducto => $cantidadTotal) {
            $stockDisponible = (int)$productosMap[$idProducto]["stock"];
            $nombreProducto = $productosMap[$idProducto]["nombre"];

            if ($cantidadTotal > $stockDisponible) {
                throw new Exception("Stock insuficiente para '$nombreProducto'. Disponible: $stockDisponible.");
            }
        }
    }

    private function calcularTotales(
        array &$items,
        array $productosMap,
        float $descuentoGlobal
    ): array {
        $subtotal = 0.0;
        $descuentoTotalLineas = 0.0;

        foreach ($items as &$item) {
            $idProducto = (int)$item["id_producto"];
            $precio = (float)$productosMap[$idProducto]["precio_venta"];
            $cantidad = (int)$item["cantidad"];

            $lineaSubtotal = $precio * $cantidad;
            $lineaDescuento = max(
                0.0,
                min((float)$item["descuento_linea"], $lineaSubtotal)
            );
            $lineaTotal = $lineaSubtotal - $lineaDescuento;

            $item["precio_unitario"] = $precio;
            $item["descuento_linea"] = $lineaDescuento;
            $item["total_linea"] = $lineaTotal;

            $subtotal += $lineaSubtotal;
            $descuentoTotalLineas += $lineaDescuento;
        }

        unset($item);

        $descuentoGlobal = max(0.0, $descuentoGlobal);
        $descuentoFactura = $descuentoTotalLineas + $descuentoGlobal;
        $baseImponible = max(0.0, $subtotal - $descuentoFactura);
        $impuesto = round($baseImponible * IVA_RATE, 2);
        $total = round($baseImponible + $impuesto, 2);

        return [
            "subtotal" => round($subtotal, 2),
            "descuento" => round($descuentoFactura, 2),
            "impuesto" => $impuesto,
            "total" => $total,
        ];
    }

    private function insertarFactura(
        int $idCliente,
        int $idSeccion,
        array $user,
        string $tipoClienteVenta,
        string $nombreClienteFugaz,
        array $totales,
        array $items,
        array $datosPagoProduccion
    ): int {
        $statement = $this->connection->prepare("
            SELECT registrar_factura_sistema(
                :id_cliente,
                :id_usuario,
                :id_seccion,
                :subtotal,
                :descuento,
                :impuesto,
                :total,
                :tipo_cliente_venta,
                :nombre_cliente_fugaz,
                CAST(:items AS JSONB)
            ) AS id_factura
        ");

        $statement->execute([
            ":id_cliente" => $idCliente,
            ":id_usuario" => (int)$user["id_usuario"],
            ":id_seccion" => $idSeccion,
            ":subtotal" => $totales["subtotal"],
            ":descuento" => $totales["descuento"],
            ":impuesto" => $totales["impuesto"],
            ":total" => $totales["total"],
            ":tipo_cliente_venta" => $tipoClienteVenta,
            ":nombre_cliente_fugaz" => $nombreClienteFugaz,
            ":items" => json_encode($items, JSON_THROW_ON_ERROR),
        ]);

        $idFactura = (int)$statement->fetchColumn();

        if ($idFactura <= 0) {
            throw new Exception("No se pudo obtener el identificador de la factura registrada.");
        }

        $this->actualizarPagoProduccionFactura($idFactura, $datosPagoProduccion);

        return $idFactura;
    }

    private function normalizarMontoPagado(mixed $valor): float
    {
        $valor = trim((string)$valor);

        if ($valor === "" || !is_numeric($valor)) {
            return 0.0;
        }

        return round(max(0.0, (float)$valor), 2);
    }

    private function validarPagoProduccion(
        float $montoPagado,
        float $total,
        string $fechaEntregaEstimada
    ): ?string {
        if ($total <= 0) {
            return "El total de la factura debe ser mayor que cero.";
        }

        if ($fechaEntregaEstimada === "") {
            return "Debe seleccionar una fecha estimada de entrega.";
        }

        $timestampEntrega = strtotime($fechaEntregaEstimada);

        if ($timestampEntrega === false) {
            return "La fecha estimada de entrega no es válida.";
        }

        $fechaHoy = strtotime(date("Y-m-d"));

        if ($timestampEntrega < $fechaHoy) {
            return "La fecha estimada de entrega no puede ser anterior a hoy.";
        }

        $minimoRequerido = round($total * 0.50, 2);

        if ($montoPagado < $minimoRequerido) {
            return "El cliente debe pagar al menos el 50% del total para iniciar la producción. Mínimo requerido: C$ "
                . number_format($minimoRequerido, 2);
        }

        if ($montoPagado > $total) {
            return "El monto pagado no puede ser mayor al total de la factura.";
        }

        return null;
    }

    private function calcularDatosPagoProduccion(
        float $montoPagado,
        float $total,
        string $fechaEntregaEstimada
    ): array {
        $saldoPendiente = round(max(0.0, $total - $montoPagado), 2);
        $porcentajePagado = $total > 0
            ? round(($montoPagado / $total) * 100, 2)
            : 0.0;

        $estadoPago = $saldoPendiente <= 0.01 ? "Pagado" : "Parcial";

        return [
            "monto_pagado" => round($montoPagado, 2),
            "saldo_pendiente" => $saldoPendiente,
            "porcentaje_pagado" => $porcentajePagado,
            "estado_pago" => $estadoPago,
            "estado_produccion" => "En producción",
            "fecha_entrega_estimada" => date("Y-m-d", strtotime($fechaEntregaEstimada)),
        ];
    }

    private function actualizarPagoProduccionFactura(
        int $idFactura,
        array $datosPagoProduccion
    ): void {
        $statement = $this->connection->prepare("
            UPDATE factura
            SET
                monto_pagado = :monto_pagado,
                saldo_pendiente = :saldo_pendiente,
                porcentaje_pagado = :porcentaje_pagado,
                estado_pago = :estado_pago,
                estado_produccion = :estado_produccion,
                fecha_orden_produccion = CURRENT_TIMESTAMP,
                fecha_entrega_estimada = :fecha_entrega_estimada
            WHERE id_factura = :id_factura
        ");

        $statement->execute([
            ":id_factura" => $idFactura,
            ":monto_pagado" => $datosPagoProduccion["monto_pagado"],
            ":saldo_pendiente" => $datosPagoProduccion["saldo_pendiente"],
            ":porcentaje_pagado" => $datosPagoProduccion["porcentaje_pagado"],
            ":estado_pago" => $datosPagoProduccion["estado_pago"],
            ":estado_produccion" => $datosPagoProduccion["estado_produccion"],
            ":fecha_entrega_estimada" => $datosPagoProduccion["fecha_entrega_estimada"],
        ]);
    }

    private function obtenerLimiteClienteFugaz(): float
    {
        $rutasConfiguracion = [
            __DIR__ . "/../storage/system/configuracion_sistema.json",
            dirname(__DIR__) . "/storage/system/configuracion_sistema.json",
            $_SERVER["DOCUMENT_ROOT"] . "/storage/system/configuracion_sistema.json",
            __DIR__ . "/../configuracion_sistema.json",
        ];

        $limitePorDefecto = 1000.00;

        foreach ($rutasConfiguracion as $archivoConfiguracion) {
            if (!is_file($archivoConfiguracion)) {
                continue;
            }

            $contenido = file_get_contents($archivoConfiguracion);

            if ($contenido === false || trim($contenido) === "") {
                continue;
            }

            $configuracion = json_decode($contenido, true);

            if (!is_array($configuracion)) {
                continue;
            }

            $limite = $configuracion["limite_de_venta_cliente_fugaz"] ?? $limitePorDefecto;

            if (is_numeric($limite) && (float)$limite > 0) {
                return (float)$limite;
            }
        }

        return $limitePorDefecto;
    }

    private function validarLimiteClienteFugaz(
        string $tipoClienteVenta,
        float $total
    ): ?string {
        $limiteClienteFugaz = $this->obtenerLimiteClienteFugaz();

        if (
            $tipoClienteVenta === TIPO_CLIENTE_FUGAZ &&
            $total > $limiteClienteFugaz
        ) {
            return "Un cliente fugaz no puede realizar una compra mayor a C$ "
                . number_format($limiteClienteFugaz, 2)
                . ". Para continuar con esta venta, registre al cliente como cliente habitual.";
        }

        return null;
    }

    public function eliminarFactura(int $idFactura): array
    {
        try {
            if ($idFactura <= 0) {
                return [
                    "success" => false,
                    "message" => "Factura inválida.",
                ];
            }

            $this->connection->beginTransaction();

            $statement = $this->connection->prepare("
                CALL eliminar_factura_sistema(:id_factura)
            ");

            $statement->execute([
                ":id_factura" => $idFactura,
            ]);

            $this->connection->commit();

            return [
                "success" => true,
                "message" => "Factura eliminada correctamente y stock restaurado.",
            ];
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            return [
                "success" => false,
                "message" => "Error al eliminar la factura: " . $exception->getMessage(),
            ];
        }
    }

    private function convertirArrayPostgres(array $valores): string
    {
        $valores = array_map("intval", $valores);
        $valores = array_filter(
            $valores,
            fn(int $valor): bool => $valor > 0
        );

        return "{" . implode(",", $valores) . "}";
    }

    public function obtenerFacturaParaEditar(int $idFactura): ?array
    {
        $statement = $this->connection->prepare("
            SELECT *
            FROM obtener_factura_para_impresion(:id_factura)
            LIMIT 1
        ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        $factura = $statement->fetch(PDO::FETCH_ASSOC);

        return $factura ?: null;
    }

    public function obtenerDetallesFacturaParaEditar(int $idFactura): array
    {
        $statement = $this->connection->prepare("
            SELECT *
            FROM obtener_detalles_factura_edicion(:id_factura)
        ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerClienteFacturaParaEditar(int $idCliente): ?array
    {
        $statement = $this->connection->prepare("
            SELECT *
            FROM obtener_cliente_factura_edicion(:id_cliente)
            LIMIT 1
        ");

        $statement->execute([
            ":id_cliente" => $idCliente,
        ]);

        $cliente = $statement->fetch(PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function obtenerLimiteClienteFugazParaVista(): float
    {
        return $this->obtenerLimiteClienteFugaz();
    }

    public function editarFactura(int $idFactura, array $post, array $user): array
    {
        try {
            if ($idFactura <= 0) {
                return [
                    "success" => false,
                    "message" => "Factura inválida.",
                ];
            }

            $tipoClienteVenta = $this->normalizarTipoClienteVenta(
                $post["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL
            );

            $idCliente = $this->resolverIdCliente($post, $tipoClienteVenta);
            $idUsuario = isset($post["id_usuario"]) ? (int)$post["id_usuario"] : (int)$user["id_usuario"];
            $idSeccion = $this->resolverIdSeccion($post, $user);
            $fecha = trim((string)($post["fecha"] ?? ""));
            $descuentoGlobal = trim((string)($post["descuento_global"] ?? "0"));
            $nombreClienteFugaz = trim((string)($post["nombre_cliente_fugaz"] ?? ""));

            $items = $this->obtenerItemsDesdePost($post);

            if ($fecha === "") {
                return [
                    "success" => false,
                    "message" => "La fecha de la factura es obligatoria.",
                ];
            }

            if ($tipoClienteVenta === TIPO_CLIENTE_FUGAZ && $nombreClienteFugaz === "") {
                return [
                    "success" => false,
                    "message" => "Debe escribir el nombre del cliente fugaz.",
                ];
            }

            $error = $this->validarDatosFactura(
                $idCliente,
                $idSeccion,
                $items,
                $descuentoGlobal
            );

            if ($error !== null) {
                return [
                    "success" => false,
                    "message" => $error,
                ];
            }

            $productosMap = $this->obtenerProductosMap($items);

            $totales = $this->calcularTotales(
                $items,
                $productosMap,
                (float)$descuentoGlobal
            );

            $errorClienteFugaz = $this->validarLimiteClienteFugaz(
                $tipoClienteVenta,
                $totales["total"]
            );

            if ($errorClienteFugaz !== null) {
                return [
                    "success" => false,
                    "message" => $errorClienteFugaz,
                ];
            }

            $montoPagado = $this->normalizarMontoPagado($post["monto_pagado"] ?? "0");
            $fechaEntregaEstimada = trim((string)($post["fecha_entrega_estimada"] ?? ""));

            $errorPagoProduccion = $this->validarPagoProduccion(
                $montoPagado,
                $totales["total"],
                $fechaEntregaEstimada
            );

            if ($errorPagoProduccion !== null) {
                return [
                    "success" => false,
                    "message" => $errorPagoProduccion,
                ];
            }

            $datosPagoProduccion = $this->calcularDatosPagoProduccion(
                $montoPagado,
                $totales["total"],
                $fechaEntregaEstimada
            );

            $this->connection->beginTransaction();

            $statement = $this->connection->prepare("
    CALL editar_factura_sistema(
        :id_factura,
        :fecha,
        :id_cliente,
        :id_usuario,
        :id_seccion,
        :tipo_cliente_venta,
        :nombre_cliente_fugaz,
        :descuento_global,
        :iva,
        CAST(:items AS JSONB)
    )
");

            $statement->execute([
                ":id_factura" => $idFactura,
                ":fecha" => date("Y-m-d H:i:s", strtotime($fecha)),
                ":id_cliente" => $idCliente,
                ":id_usuario" => $idUsuario,
                ":id_seccion" => $idSeccion,
                ":tipo_cliente_venta" => $tipoClienteVenta,
                ":nombre_cliente_fugaz" => $tipoClienteVenta === TIPO_CLIENTE_FUGAZ ? $nombreClienteFugaz : null,
                ":descuento_global" => (float)$descuentoGlobal,
                ":iva" => IVA_RATE,
                ":items" => json_encode($items, JSON_THROW_ON_ERROR),
            ]);

            $this->actualizarPagoProduccionFactura($idFactura, $datosPagoProduccion);
            $this->connection->commit();

            return [
                "success" => true,
                "message" => "Factura actualizada correctamente.",
            ];
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            return [
                "success" => false,
                "message" => "Error al editar la factura: " . $exception->getMessage(),
            ];
        }
    }
}
