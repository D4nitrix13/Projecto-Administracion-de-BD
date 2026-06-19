<?php

declare(strict_types=1);

namespace App\Repository;

class ReportesRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerTotalClientes(): int
    {
        $statement = $this->connection->query("
            SELECT obtener_total_clientes_reportes()
        ");

        return (int)$statement->fetchColumn();
    }

    public function obtenerTotalFacturas(?string $fechaDesde, ?string $fechaHasta): int
    {
        $statement = $this->connection->prepare("
            SELECT obtener_total_facturas_reportes(:fecha_desde, :fecha_hasta)
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
        ]);

        return (int)$statement->fetchColumn();
    }

    public function obtenerTotalVentas(?string $fechaDesde, ?string $fechaHasta): float
    {
        $statement = $this->connection->prepare("
            SELECT obtener_total_ventas_reportes(:fecha_desde, :fecha_hasta)
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
        ]);

        return (float)$statement->fetchColumn();
    }

    public function obtenerStockBajo(): int
    {
        $statement = $this->connection->query("
            SELECT obtener_stock_bajo_reportes()
        ");

        return (int)$statement->fetchColumn();
    }

    public function obtenerVentasPorDia(?string $fechaDesde, ?string $fechaHasta): array
    {
        $statement = $this->connection->prepare("
            SELECT dia, total_dia, cantidad_facturas
            FROM obtener_ventas_por_dia_reportes(:fecha_desde, :fecha_hasta)
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMasVendidos(?string $fechaDesde, ?string $fechaHasta): array
    {
        $statement = $this->connection->prepare("
            SELECT id_producto, producto, codigo, cantidad_vendida, total_vendido
            FROM obtener_productos_mas_vendidos_reportes(:fecha_desde, :fecha_hasta)
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerVentasDetalladas(?string $fechaDesde, ?string $fechaHasta, int $limit = 15, int $offset = 0): array
    {
        $statement = $this->connection->prepare("
            SELECT id_factura, fecha, subtotal, descuento, impuesto, total, cliente, usuario, seccion
            FROM obtener_ventas_detalladas_reportes(:fecha_desde, :fecha_hasta)
            LIMIT :limit OFFSET :offset
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
            ":limit" => $limit,
            ":offset" => $offset,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function contarVentasDetalladas(?string $fechaDesde, ?string $fechaHasta): int
    {
        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM obtener_ventas_detalladas_reportes(:fecha_desde, :fecha_hasta)
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function obtenerProductosReporte(?string $fechaDesde, ?string $fechaHasta, int $limit = 15, int $offset = 0): array
    {
        $statement = $this->connection->prepare("
            SELECT id_producto, codigo, nombre, stock, cantidad_vendida, total_vendido
            FROM obtener_productos_reporte(:fecha_desde, :fecha_hasta)
            LIMIT :limit OFFSET :offset
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
            ":limit" => $limit,
            ":offset" => $offset,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function contarProductosReporte(?string $fechaDesde, ?string $fechaHasta): int
    {
        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM obtener_productos_reporte(:fecha_desde, :fecha_hasta)
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function obtenerClientesReporte(?string $fechaDesde, ?string $fechaHasta, int $limit = 15, int $offset = 0): array
    {
        $statement = $this->connection->prepare("
            SELECT id_cliente, cliente, telefono, tipo_cliente, cantidad_facturas, total_comprado
            FROM obtener_clientes_reporte(:fecha_desde, :fecha_hasta)
            LIMIT :limit OFFSET :offset
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
            ":limit" => $limit,
            ":offset" => $offset,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function contarClientesReporte(?string $fechaDesde, ?string $fechaHasta): int
    {
        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM obtener_clientes_reporte(:fecha_desde, :fecha_hasta)
        ");

        $statement->execute([
            ":fecha_desde" => $fechaDesde,
            ":fecha_hasta" => $fechaHasta,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function obtenerProductosMasVendidosMes(): array
    {
        $statement = $this->connection->query("
            SELECT id_producto, producto, codigo, cantidad_vendida, total_vendido
            FROM obtener_productos_mas_vendidos_mes()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMenosVendidosMes(): array
    {
        $statement = $this->connection->query("
            SELECT id_producto, producto, codigo, cantidad_vendida, stock_actual
            FROM obtener_productos_menos_vendidos_mes()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMasVendidosSemana(): array
    {
        $statement = $this->connection->query("
            SELECT id_producto, producto, codigo, cantidad_vendida, total_vendido
            FROM obtener_productos_mas_vendidos_semana()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMenosVendidosSemana(): array
    {
        $statement = $this->connection->query("
            SELECT id_producto, producto, codigo, cantidad_vendida, stock_actual
            FROM obtener_productos_menos_vendidos_semana()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMasVendidosAnio(): array
    {
        $statement = $this->connection->query("
            SELECT id_producto, producto, codigo, cantidad_vendida, total_vendido
            FROM obtener_productos_mas_vendidos_anio()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMenosVendidosAnio(): array
    {
        $statement = $this->connection->query("
            SELECT id_producto, producto, codigo, cantidad_vendida, stock_actual
            FROM obtener_productos_menos_vendidos_anio()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerCategoriasMenosProductos(): array
    {
        $statement = $this->connection->query("
            SELECT id_categoria, categoria, cantidad_productos, stock_total
            FROM obtener_categorias_menos_productos()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
