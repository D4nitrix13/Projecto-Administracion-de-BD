<?php

declare(strict_types=1);

namespace App\Repository;

class FacturaRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerFacturasFiltradas(array $filtros): array
    {
        $pagina = (int) ($filtros["pagina"] ?? 1);
        $porPagina = (int) ($filtros["porPagina"] ?? 15);
        $offset = ($pagina - 1) * $porPagina;

        $params = [
            ":id_rol"            => (int) ($filtros["idRol"] ?? 0),
            ":busqueda"          => trim($filtros["busqueda"] ?? ""),
            ":id_seccion"        => $filtros["seccionFiltroInt"] ?? null,
            ":id_usuario"        => $filtros["usuarioFiltroInt"] ?? null,
            ":fecha_desde"       => ($filtros["fechaDesde"] ?? "") !== ""
                ? $filtros["fechaDesde"] . " 00:00:00" : null,
            ":fecha_hasta"       => ($filtros["fechaHasta"] ?? "") !== ""
                ? $filtros["fechaHasta"] . " 23:59:59" : null,
            ":estado_pago"       => ($filtros["estadoPagoFiltro"] ?? "") !== ""
                ? $filtros["estadoPagoFiltro"] : null,
            ":estado_produccion" => ($filtros["estadoProduccionFiltro"] ?? "") !== ""
                ? $filtros["estadoProduccionFiltro"] : null,
            ":limit"             => $porPagina,
            ":offset"            => $offset,
        ];

        $statement = $this->connection->prepare("
            SELECT * FROM buscar_facturas_filtradas(
                CAST(:id_rol AS integer),
                CAST(:busqueda AS text),
                CAST(:id_seccion AS integer),
                CAST(:id_usuario AS integer),
                CAST(:fecha_desde AS timestamp),
                CAST(:fecha_hasta AS timestamp),
                CAST(:estado_pago AS text),
                CAST(:estado_produccion AS text),
                CAST(:limit AS integer),
                CAST(:offset AS integer)
            )
        ");

        $statement->execute($params);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function contarFacturasFiltradas(array $filtros): int
    {
        $params = [
            ":id_rol"            => (int) ($filtros["idRol"] ?? 0),
            ":busqueda"          => trim($filtros["busqueda"] ?? ""),
            ":id_seccion"        => $filtros["seccionFiltroInt"] ?? null,
            ":id_usuario"        => $filtros["usuarioFiltroInt"] ?? null,
            ":fecha_desde"       => ($filtros["fechaDesde"] ?? "") !== ""
                ? $filtros["fechaDesde"] . " 00:00:00" : null,
            ":fecha_hasta"       => ($filtros["fechaHasta"] ?? "") !== ""
                ? $filtros["fechaHasta"] . " 23:59:59" : null,
            ":estado_pago"       => ($filtros["estadoPagoFiltro"] ?? "") !== ""
                ? $filtros["estadoPagoFiltro"] : null,
            ":estado_produccion" => ($filtros["estadoProduccionFiltro"] ?? "") !== ""
                ? $filtros["estadoProduccionFiltro"] : null,
        ];

        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM buscar_facturas_filtradas(
                CAST(:id_rol AS integer),
                CAST(:busqueda AS text),
                CAST(:id_seccion AS integer),
                CAST(:id_usuario AS integer),
                CAST(:fecha_desde AS timestamp),
                CAST(:fecha_hasta AS timestamp),
                CAST(:estado_pago AS text),
                CAST(:estado_produccion AS text),
                999999,
                0
            )
        ");

        $statement->execute($params);
        return (int) $statement->fetchColumn();
    }

    public function obtenerFacturaPorId(int $idFactura): ?array
    {
        $statement = $this->connection->prepare("
            SELECT
                fd.id_factura, fd.fecha, fd.subtotal, fd.descuento,
                fd.impuesto, fd.total, fd.cliente, fd.telefono,
                fd.direccion, fd.usuario, fd.seccion,
                f.monto_pagado, f.saldo_pendiente, f.porcentaje_pagado,
                f.estado_pago, f.estado_produccion,
                f.fecha_orden_produccion, f.fecha_entrega_estimada,
                f.fecha_entrega_real, f.tipo_cliente_venta, f.nombre_cliente_fugaz
            FROM obtener_factura_detalle_por_id(:id_factura) AS fd
            INNER JOIN factura f ON f.id_factura = fd.id_factura
            LIMIT 1
        ");

        $statement->execute([":id_factura" => $idFactura]);
        $factura = $statement->fetch(\PDO::FETCH_ASSOC);

        return $factura ?: null;
    }

    public function obtenerDetalleFactura(int $idFactura): array
    {
        $statement = $this->connection->prepare("
            SELECT id_detalle, codigo, nombre, cantidad,
                   precio_unitario, descuento_linea, total_linea
            FROM obtener_lineas_detalle_factura(:id_factura)
        ");

        $statement->execute([":id_factura" => $idFactura]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerFacturaParaImpresion(int $idFactura): ?array
    {
        $statement = $this->connection->prepare("
            SELECT
                fp.id_factura, fp.fecha, fp.id_cliente, fp.id_usuario,
                fp.id_seccion, fp.subtotal, fp.descuento, fp.impuesto,
                fp.total, fp.tipo_cliente_venta, fp.nombre_cliente_fugaz,
                fp.cli_nombres, fp.cli_apellidos, fp.cli_telefono,
                fp.cli_direccion, fp.cli_identificacion,
                fp.usuario_nombre, fp.seccion_nombre,
                f.monto_pagado, f.saldo_pendiente, f.porcentaje_pagado,
                f.estado_pago, f.estado_produccion,
                f.fecha_orden_produccion, f.fecha_entrega_estimada,
                f.fecha_entrega_real
            FROM obtener_factura_para_impresion(:id_factura) AS fp
            INNER JOIN factura f ON f.id_factura = fp.id_factura
            LIMIT 1
        ");

        $statement->execute([":id_factura" => $idFactura]);
        $factura = $statement->fetch(\PDO::FETCH_ASSOC);

        return $factura ?: null;
    }

    public function obtenerDetalleFacturaParaImpresion(int $idFactura): array
    {
        $statement = $this->connection->prepare("
            SELECT id_detalle, cantidad, precio_unitario, descuento_linea,
                   total_linea, producto_nombre, producto_codigo
            FROM obtener_lineas_factura_para_impresion(:id_factura)
        ");

        $statement->execute([":id_factura" => $idFactura]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
