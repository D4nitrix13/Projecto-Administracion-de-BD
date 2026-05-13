<?php
// * Stored function or procedure has been executed

class FacturaRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerFacturasFiltradas(array $filtros): array
    {
        $idRol = (int)($filtros["idRol"] ?? 0);
        $busqueda = trim($filtros["busqueda"] ?? "");
        $seccionFiltroInt = $filtros["seccionFiltroInt"] ?? null;
        $usuarioFiltroInt = $filtros["usuarioFiltroInt"] ?? null;
        $estadoPagoFiltro = $filtros["estadoPagoFiltro"] ?? "";
        $estadoProduccionFiltro = $filtros["estadoProduccionFiltro"] ?? "";
        $fechaDesde = $filtros["fechaDesde"] ?? "";
        $fechaHasta = $filtros["fechaHasta"] ?? "";

        $fechaDesdeSql = $fechaDesde !== ""
            ? $fechaDesde . " 00:00:00"
            : null;

        $fechaHastaSql = $fechaHasta !== ""
            ? $fechaHasta . " 23:59:59"
            : null;

        $whereExtra = [];
        $params = [
            ":id_rol" => $idRol,
            ":busqueda" => $busqueda,
            ":id_seccion" => $seccionFiltroInt,
            ":id_usuario" => $usuarioFiltroInt,
            ":fecha_desde" => $fechaDesdeSql,
            ":fecha_hasta" => $fechaHastaSql,
        ];

        if ($estadoPagoFiltro !== "") {
            $whereExtra[] = "f.estado_pago = :estado_pago";
            $params[":estado_pago"] = $estadoPagoFiltro;
        }

        if ($estadoProduccionFiltro !== "") {
            $whereExtra[] = "f.estado_produccion = :estado_produccion";
            $params[":estado_produccion"] = $estadoProduccionFiltro;
        }

        $whereExtraSql = "";

        if (!empty($whereExtra)) {
            $whereExtraSql = "WHERE " . implode(" AND ", $whereExtra);
        }

        $statement = $this->connection->prepare("
            SELECT
                bf.id_factura,
                bf.fecha,
                bf.total,
                bf.cliente,
                bf.usuario,
                bf.seccion,
                f.estado_pago,
                f.estado_produccion
            FROM buscar_facturas_filtradas(
                :id_rol,
                :busqueda,
                :id_seccion,
                :id_usuario,
                :fecha_desde,
                :fecha_hasta
            ) AS bf
            INNER JOIN factura f ON f.id_factura = bf.id_factura
            $whereExtraSql
            ORDER BY bf.fecha DESC, bf.id_factura DESC
        ");

        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFacturaPorId(int $idFactura): ?array
    {
        $statement = $this->connection->prepare("
        SELECT
            fd.id_factura,
            fd.fecha,
            fd.subtotal,
            fd.descuento,
            fd.impuesto,
            fd.total,
            fd.cliente,
            fd.telefono,
            fd.direccion,
            fd.usuario,
            fd.seccion,

            f.monto_pagado,
            f.saldo_pendiente,
            f.porcentaje_pagado,
            f.estado_pago,
            f.estado_produccion,
            f.fecha_orden_produccion,
            f.fecha_entrega_estimada,
            f.fecha_entrega_real,
            f.tipo_cliente_venta,
            f.nombre_cliente_fugaz
        FROM obtener_factura_detalle_por_id(:id_factura) AS fd
        INNER JOIN factura f ON f.id_factura = fd.id_factura
        LIMIT 1
    ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        $factura = $statement->fetch(PDO::FETCH_ASSOC);

        return $factura ?: null;
    }

    public function obtenerDetalleFactura(int $idFactura): array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_detalle,
                codigo,
                nombre,
                cantidad,
                precio_unitario,
                descuento_linea,
                total_linea
            FROM obtener_lineas_detalle_factura(:id_factura)
        ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFacturaParaImpresion(int $idFactura): ?array
    {
        $statement = $this->connection->prepare("
        SELECT
            fp.id_factura,
            fp.fecha,
            fp.id_cliente,
            fp.id_usuario,
            fp.id_seccion,
            fp.subtotal,
            fp.descuento,
            fp.impuesto,
            fp.total,
            fp.tipo_cliente_venta,
            fp.nombre_cliente_fugaz,
            fp.cli_nombres,
            fp.cli_apellidos,
            fp.cli_telefono,
            fp.cli_direccion,
            fp.cli_identificacion,
            fp.usuario_nombre,
            fp.seccion_nombre,

            f.monto_pagado,
            f.saldo_pendiente,
            f.porcentaje_pagado,
            f.estado_pago,
            f.estado_produccion,
            f.fecha_orden_produccion,
            f.fecha_entrega_estimada,
            f.fecha_entrega_real
        FROM obtener_factura_para_impresion(:id_factura) AS fp
        INNER JOIN factura f ON f.id_factura = fp.id_factura
        LIMIT 1
    ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        $factura = $statement->fetch(PDO::FETCH_ASSOC);

        return $factura ?: null;
    }

    public function obtenerDetalleFacturaParaImpresion(int $idFactura): array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_detalle,
                cantidad,
                precio_unitario,
                descuento_linea,
                total_linea,
                producto_nombre,
                producto_codigo
            FROM obtener_lineas_factura_para_impresion(:id_factura)
        ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
