<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ProductoRepository;

class FacturaCalculationService
{
    public function __construct(
        private \PDO $connection,
        private ProductoRepository $productoRepo,
    ) {}

    public function calcularTotales(array &$items, array $productosMap, float $descuentoGlobal): array
    {
        $subtotal = 0.0;
        $descuentoTotalLineas = 0.0;

        foreach ($items as &$item) {
            $precio = (float) $productosMap[(int) $item["id_producto"]]["precio_venta"];
            $cantidad = (int) $item["cantidad"];
            $lineaSubtotal = $precio * $cantidad;
            $lineaDescuento = max(0.0, min((float) $item["descuento_linea"], $lineaSubtotal));

            $item["precio_unitario"] = $precio;
            $item["descuento_linea"] = $lineaDescuento;
            $item["total_linea"] = $lineaSubtotal - $lineaDescuento;

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
            "subtotal"   => round($subtotal, 2),
            "descuento"  => round($descuentoFactura, 2),
            "impuesto"   => $impuesto,
            "total"      => $total,
        ];
    }

    public function calcularDatosPagoProduccion(
        float $montoPagado,
        float $total,
        string $fechaEntregaEstimada
    ): array {
        $saldoPendiente = round(max(0.0, $total - $montoPagado), 2);
        $porcentajePagado = $total > 0 ? round(($montoPagado / $total) * 100, 2) : 0.0;

        return [
            "monto_pagado"          => round($montoPagado, 2),
            "saldo_pendiente"       => $saldoPendiente,
            "porcentaje_pagado"     => $porcentajePagado,
            "estado_pago"           => $this->calcularEstadoPago($montoPagado, $saldoPendiente),
            "estado_produccion"     => "En producción",
            "fecha_entrega_estimada" => date("Y-m-d", strtotime($fechaEntregaEstimada)),
        ];
    }

    public function normalizarMontoPagado(mixed $valor): float
    {
        $valor = trim((string) $valor);

        if ($valor === "" || !is_numeric($valor)) {
            return 0.0;
        }

        return round(max(0.0, (float) $valor), 2);
    }

    public function obtenerProductosMap(array $items): array
    {
        $ids = array_unique(array_column($items, "id_producto"));
        $idsPgArray = $this->convertirArrayPostgres($ids);

        $statement = $this->connection->prepare("
            SELECT id_producto, precio_venta, stock, nombre
            FROM obtener_productos_factura_por_ids(CAST(:ids_productos AS INT[]))
        ");

        $statement->execute([":ids_productos" => $idsPgArray]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $map = [];

        foreach ($rows as $row) {
            $map[(int) $row["id_producto"]] = [
                "nombre"        => (string) $row["nombre"],
                "precio_venta"  => (float) $row["precio_venta"],
                "stock"         => (int) $row["stock"],
            ];
        }

        return $map;
    }

    public function convertirArrayPostgres(array $valores): string
    {
        $valores = array_filter(array_map("intval", $valores), fn(int $v): bool => $v > 0);

        return "{" . implode(",", $valores) . "}";
    }

    private function calcularEstadoPago(float $montoPagado, float $saldoPendiente): string
    {
        if ($saldoPendiente <= 0.01) {
            return "Pagado";
        }

        if ($montoPagado > 0) {
            return "Parcial";
        }

        return "Pendiente";
    }
}
