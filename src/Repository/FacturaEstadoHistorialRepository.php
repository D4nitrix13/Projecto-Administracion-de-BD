<?php

declare(strict_types=1);

namespace App\Repository;

class FacturaEstadoHistorialRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerHistorialPorFactura(int $idFactura): array
    {
        $statement = $this->connection->prepare("
            SELECT * FROM factura_estado_historial
            WHERE id_factura = :id_factura
            ORDER BY fecha_evento DESC, id_historial DESC
        ");

        $statement->execute([":id_factura" => $idFactura]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialGeneral(): array
    {
        return $this->obtenerHistorialGeneralFiltrado([]);
    }

    public function obtenerHistorialGeneralFiltrado(array $filtros): array
    {
        [$where, $params] = $this->construirFiltros($filtros);

        $statement = $this->connection->prepare("
            SELECT h.*, f.total, f.fecha AS fecha_factura
            FROM factura_estado_historial h
            INNER JOIN factura f ON f.id_factura = h.id_factura
            $where
            ORDER BY h.fecha_evento DESC, h.id_historial DESC
            LIMIT 300
        ");

        $statement->execute($params);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerTiposEvento(): array
    {
        $statement = $this->connection->query("
            SELECT DISTINCT tipo_evento
            FROM factura_estado_historial
            ORDER BY tipo_evento ASC
        ");

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function obtenerResumenHistorial(array $filtros): array
    {
        [$where, $params] = $this->construirFiltros($filtros);

        $statement = $this->connection->prepare("
            SELECT
                COUNT(*) AS total_eventos,
                COUNT(*) FILTER (
                    WHERE tipo_evento ILIKE '%pago%'
                       OR tipo_evento ILIKE '%abono%'
                       OR monto_abonado > 0
                ) AS pagos_registrados,
                COUNT(*) FILTER (WHERE estado_produccion_nuevo = 'Entregada') AS entregadas,
                COUNT(*) FILTER (WHERE estado_produccion_nuevo = 'Cancelada') AS canceladas
            FROM factura_estado_historial h
            INNER JOIN factura f ON f.id_factura = h.id_factura
            $where
        ");

        $statement->execute($params);
        $resumen = $statement->fetch(\PDO::FETCH_ASSOC) ?: [];

        return [
            "total_eventos"    => (int) ($resumen["total_eventos"] ?? 0),
            "pagos_registrados" => (int) ($resumen["pagos_registrados"] ?? 0),
            "entregadas"       => (int) ($resumen["entregadas"] ?? 0),
            "canceladas"       => (int) ($resumen["canceladas"] ?? 0),
        ];
    }

    private function construirFiltros(array $filtros): array
    {
        $where = [];
        $params = [];

        $busqueda = trim((string) ($filtros["busqueda"] ?? ""));
        $tipoEvento = trim((string) ($filtros["tipoEventoFiltro"] ?? ""));
        $estadoPago = trim((string) ($filtros["estadoPagoFiltro"] ?? ""));
        $estadoProduccion = trim((string) ($filtros["estadoProduccionFiltro"] ?? ""));
        $fechaDesde = trim((string) ($filtros["fechaDesde"] ?? ""));
        $fechaHasta = trim((string) ($filtros["fechaHasta"] ?? ""));

        if ($busqueda !== "") {
            $where[] = "(CAST(h.id_factura AS TEXT) ILIKE :busqueda OR h.tipo_evento ILIKE :busqueda OR h.comentario ILIKE :busqueda)";
            $params[":busqueda"] = "%" . $busqueda . "%";
        }

        if ($tipoEvento !== "") {
            $where[] = "h.tipo_evento = :tipo_evento";
            $params[":tipo_evento"] = $tipoEvento;
        }

        if ($estadoPago !== "") {
            $where[] = "h.estado_pago_nuevo = :estado_pago";
            $params[":estado_pago"] = $estadoPago;
        }

        if ($estadoProduccion !== "") {
            $where[] = "h.estado_produccion_nuevo = :estado_produccion";
            $params[":estado_produccion"] = $estadoProduccion;
        }

        if ($fechaDesde !== "") {
            $where[] = "DATE(h.fecha_evento) >= :fecha_desde";
            $params[":fecha_desde"] = $fechaDesde;
        }

        if ($fechaHasta !== "") {
            $where[] = "DATE(h.fecha_evento) <= :fecha_hasta";
            $params[":fecha_hasta"] = $fechaHasta;
        }

        return [empty($where) ? "" : "WHERE " . implode(" AND ", $where), $params];
    }
}
