<?php

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

            $this->connection->beginTransaction();

            $idFactura = $this->insertarFactura(
                $idCliente,
                $idSeccion,
                $user,
                $tipoClienteVenta,
                trim($post["nombre_cliente_fugaz"] ?? ""),
                $totales
            );

            $this->insertarDetallesYActualizarStock($idFactura, $items);

            $this->connection->commit();

            return [
                "success" => true,
                "message" => "Factura registrada correctamente.",
                "id_factura" => $idFactura,
            ];
        } catch (Exception $exception) {
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
        $statement = $this->connection->prepare("
            SELECT id_cliente
            FROM Cliente
            WHERE identificacion = 'FUGAZ'
            LIMIT 1
        ");

        $statement->execute();

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

        $placeholders = implode(",", array_fill(0, count($idsProductos), "?"));

        $statement = $this->connection->prepare("
            SELECT id_producto, precio_venta, stock, nombre
            FROM Producto
            WHERE id_producto IN ($placeholders)
        ");

        $statement->execute($idsProductos);

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

    private function calcularTotales(array &$items, array $productosMap, float $descuentoGlobal): array
    {
        $subtotal = 0.0;
        $descuentoTotalLineas = 0.0;

        foreach ($items as &$item) {
            $idProducto = (int)$item["id_producto"];
            $precio = (float)$productosMap[$idProducto]["precio_venta"];
            $cantidad = (int)$item["cantidad"];

            $lineaSubtotal = $precio * $cantidad;
            $lineaDescuento = max(0.0, min((float)$item["descuento_linea"], $lineaSubtotal));
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
        $total = $baseImponible + $impuesto;

        return [
            "subtotal" => $subtotal,
            "descuento" => $descuentoFactura,
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
        array $totales
    ): int {
        $statement = $this->connection->prepare("
            INSERT INTO Factura
                (
                    id_cliente,
                    id_usuario,
                    id_seccion,
                    subtotal,
                    descuento,
                    impuesto,
                    total,
                    tipo_cliente_venta,
                    nombre_cliente_fugaz
                )
            VALUES
                (
                    :id_cliente,
                    :id_usuario,
                    :id_seccion,
                    :subtotal,
                    :descuento,
                    :impuesto,
                    :total,
                    :tipo_cliente_venta,
                    :nombre_cliente_fugaz
                )
            RETURNING id_factura
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
            ":nombre_cliente_fugaz" => (
                $tipoClienteVenta === TIPO_CLIENTE_FUGAZ &&
                $nombreClienteFugaz !== ""
            ) ? $nombreClienteFugaz : null,
        ]);

        return (int)$statement->fetchColumn();
    }

    private function insertarDetallesYActualizarStock(int $idFactura, array $items): void
    {
        $statementDetalle = $this->connection->prepare("
            INSERT INTO DetalleFactura
                (
                    id_factura,
                    id_producto,
                    cantidad,
                    precio_unitario,
                    descuento_linea,
                    total_linea
                )
            VALUES
                (
                    :id_factura,
                    :id_producto,
                    :cantidad,
                    :precio_unitario,
                    :descuento_linea,
                    :total_linea
                )
        ");

        $statementStock = $this->connection->prepare("
            UPDATE Producto
            SET stock = stock - :cantidad
            WHERE id_producto = :id_producto
        ");

        foreach ($items as $item) {
            $statementDetalle->execute([
                ":id_factura" => $idFactura,
                ":id_producto" => $item["id_producto"],
                ":cantidad" => $item["cantidad"],
                ":precio_unitario" => $item["precio_unitario"],
                ":descuento_linea" => $item["descuento_linea"],
                ":total_linea" => $item["total_linea"],
            ]);

            $statementStock->execute([
                ":cantidad" => $item["cantidad"],
                ":id_producto" => $item["id_producto"],
            ]);
        }
    }

    private function validarLimiteClienteFugaz(string $tipoClienteVenta, float $total): ?string
    {
        $limiteClienteFugaz = 1000.00;

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
}
