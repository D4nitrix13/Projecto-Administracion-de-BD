<?php

declare(strict_types=1);

namespace App\Service;

class FacturaValidationService
{
    public function __construct(private \PDO $connection) {}

    public function normalizarTipoClienteVenta(string $tipo): string
    {
        return $tipo === TIPO_CLIENTE_FUGAZ ? TIPO_CLIENTE_FUGAZ : TIPO_CLIENTE_HABITUAL;
    }

    public function resolverIdCliente(array $post, string $tipoClienteVenta): int
    {
        if ($tipoClienteVenta === TIPO_CLIENTE_HABITUAL) {
            return isset($post["id_cliente"]) ? (int) $post["id_cliente"] : 0;
        }

        return $this->obtenerIdClienteFugaz();
    }

    public function resolverIdSeccion(array $post, array $user): int
    {
        if (!empty($user["id_seccion"])) {
            return (int) $user["id_seccion"];
        }

        return isset($post["id_seccion"]) && $post["id_seccion"] !== ""
            ? (int) $post["id_seccion"]
            : 0;
    }

    public function obtenerItemsDesdePost(array $post): array
    {
        $idsProductos = $post["id_producto"] ?? [];
        $cantidades = $post["cantidad"] ?? [];
        $descuentosLinea = $post["descuento_linea"] ?? [];
        $items = [];

        for ($i = 0; $i < count($idsProductos); $i++) {
            $idProducto = (int) ($idsProductos[$i] ?? 0);
            $cantidad = (int) ($cantidades[$i] ?? 0);

            if ($idProducto <= 0 && $cantidad <= 0) {
                continue;
            }

            $descuentoRaw = trim((string) ($descuentosLinea[$i] ?? "0"));
            $descuento = is_numeric($descuentoRaw) ? (float) $descuentoRaw : 0.0;

            $items[] = [
                "id_producto"     => $idProducto,
                "cantidad"        => $cantidad,
                "descuento_linea" => max(0.0, $descuento),
            ];
        }

        return $items;
    }

    public function validarDatosFactura(
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
            return "Debe agregar al menos un producto.";
        }

        if ($descuentoGlobal !== "" && !is_numeric($descuentoGlobal)) {
            return "El descuento global debe ser numérico.";
        }

        foreach ($items as $item) {
            if ((int) $item["id_producto"] <= 0) {
                return "Producto inválido en alguna fila.";
            }

            if ((int) $item["cantidad"] < 1) {
                return "La cantidad mínima es 1.";
            }
        }

        return null;
    }

    public function validarProductosYStock(array $items, array $productosMap): void
    {
        $cantidadPorProducto = [];

        foreach ($items as $item) {
            $id = (int) $item["id_producto"];

            if (!isset($productosMap[$id])) {
                throw new \Exception("Producto no encontrado.");
            }

            if (!isset($cantidadPorProducto[$id])) {
                $cantidadPorProducto[$id] = 0;
            }

            $cantidadPorProducto[$id] += (int) $item["cantidad"];
        }

        foreach ($cantidadPorProducto as $id => $cantidadTotal) {
            $stock = (int) $productosMap[$id]["stock"];

            if ($cantidadTotal > $stock) {
                throw new \Exception("Stock insuficiente para '{$productosMap[$id]["nombre"]}'. Disponible: $stock.");
            }
        }
    }

    public function validarPagoProduccion(
        float $montoPagado,
        float $total,
        string $fechaEntregaEstimada
    ): ?string {
        if ($total <= 0) {
            return "El total debe ser mayor que cero.";
        }

        if ($fechaEntregaEstimada === "") {
            return "Debe seleccionar una fecha de entrega.";
        }

        $timestamp = strtotime($fechaEntregaEstimada);

        if ($timestamp === false) {
            return "La fecha de entrega no es válida.";
        }

        if ($timestamp < strtotime(date("Y-m-d"))) {
            return "La fecha de entrega no puede ser anterior a hoy.";
        }

        $minimo = round($total * 0.50, 2);

        if ($montoPagado < $minimo) {
            return "Debe pagar al menos el 50%. Mínimo: C$ " . number_format($minimo, 2);
        }

        if ($montoPagado > $total) {
            return "El monto no puede exceder el total.";
        }

        return null;
    }

    public function validarLimiteClienteFugaz(string $tipo, float $total): ?string
    {
        $limite = $this->obtenerLimiteClienteFugaz();

        if ($tipo === TIPO_CLIENTE_FUGAZ && $total > $limite) {
            return "Cliente fugaz no puede exceder C$ " . number_format($limite, 2) . ". Registre como habitual.";
        }

        return null;
    }

    public function obtenerLimiteClienteFugaz(): float
    {
        $rutas = [
            __DIR__ . "/../../storage/system/configuracion_sistema.json",
            dirname(__DIR__) . "/storage/system/configuracion_sistema.json",
            $_SERVER["DOCUMENT_ROOT"] . "/storage/system/configuracion_sistema.json",
            __DIR__ . "/../../configuracion_sistema.json",
        ];

        $default = 1000.00;

        foreach ($rutas as $ruta) {
            if (!is_file($ruta)) continue;

            $contenido = file_get_contents($ruta);

            if ($contenido === false || trim($contenido) === "") continue;

            $config = json_decode($contenido, true);

            if (!is_array($config)) continue;

            $limite = $config["limite_de_venta_cliente_fugaz"] ?? $default;

            if (is_numeric($limite) && (float) $limite > 0) {
                return (float) $limite;
            }
        }

        return $default;
    }

    private function obtenerIdClienteFugaz(): int
    {
        $statement = $this->connection->query("SELECT obtener_id_cliente_fugaz()");
        $id = $statement->fetchColumn();

        return $id ? (int) $id : 0;
    }
}
