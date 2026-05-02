<?php

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
        $fechaDesde = $filtros["fechaDesde"] ?? "";
        $fechaHasta = $filtros["fechaHasta"] ?? "";

        $sql = "
            SELECT 
                f.id_factura,
                f.fecha,
                f.total,
                c.nombres || ' ' || c.apellidos AS cliente,
                u.nombre AS usuario,
                s.nombre AS seccion
            FROM Factura f
            JOIN Cliente c ON f.id_cliente = c.id_cliente
            JOIN Usuario u ON f.id_usuario = u.id_usuario
            JOIN Seccion s ON f.id_seccion = s.id_seccion
            WHERE 1 = 1
        ";

        $params = [];

        if (in_array($idRol, [2, 3], true)) {
            $sql .= "
                AND c.tipo_cliente = 'Detallista'
                AND s.nombre = 'Kitsune'
            ";
        }

        if ($busqueda !== "") {
            $sql .= "
                AND (
                    CAST(f.id_factura AS TEXT) ILIKE :q
                    OR c.nombres ILIKE :q
                    OR c.apellidos ILIKE :q
                    OR c.nombres || ' ' || c.apellidos ILIKE :q
                    OR u.nombre ILIKE :q
                    OR s.nombre ILIKE :q
                )
            ";

            $params[":q"] = "%" . $busqueda . "%";
        }

        if ($seccionFiltroInt !== null) {
            $sql .= " AND f.id_seccion = :seccion";
            $params[":seccion"] = $seccionFiltroInt;
        }

        if ($usuarioFiltroInt !== null) {
            $sql .= " AND f.id_usuario = :usuario";
            $params[":usuario"] = $usuarioFiltroInt;
        }

        if ($fechaDesde !== "") {
            $sql .= " AND f.fecha >= :fecha_desde";
            $params[":fecha_desde"] = $fechaDesde . " 00:00:00";
        }

        if ($fechaHasta !== "") {
            $sql .= " AND f.fecha <= :fecha_hasta";
            $params[":fecha_hasta"] = $fechaHasta . " 23:59:59";
        }

        $sql .= " ORDER BY f.fecha DESC, f.id_factura DESC";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFacturaPorId(int $idFactura): ?array
    {
        $statement = $this->connection->prepare("
        SELECT 
            f.id_factura,
            f.fecha,
            f.subtotal,
            f.descuento,
            f.impuesto,
            f.total,
            c.nombres || ' ' || c.apellidos AS cliente,
            c.telefono,
            c.direccion,
            u.nombre AS usuario,
            s.nombre AS seccion
        FROM Factura f
        JOIN Cliente c ON f.id_cliente = c.id_cliente
        JOIN Usuario u ON f.id_usuario = u.id_usuario
        JOIN Seccion s ON f.id_seccion = s.id_seccion
        WHERE f.id_factura = :id_factura
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
            df.id_detalle,
            p.codigo,
            p.nombre,
            df.cantidad,
            df.precio_unitario,
            df.descuento_linea,
            df.total_linea
        FROM DetalleFactura df
        JOIN Producto p ON df.id_producto = p.id_producto
        WHERE df.id_factura = :id_factura
        ORDER BY df.id_detalle ASC
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
            f.*,
            c.nombres AS cli_nombres,
            c.apellidos AS cli_apellidos,
            c.telefono AS cli_telefono,
            c.direccion AS cli_direccion,
            c.identificacion AS cli_identificacion,
            u.nombre AS usuario_nombre,
            s.nombre AS seccion_nombre
        FROM Factura f
        JOIN Cliente c ON f.id_cliente = c.id_cliente
        JOIN Usuario u ON f.id_usuario = u.id_usuario
        JOIN Seccion s ON f.id_seccion = s.id_seccion
        WHERE f.id_factura = :id_factura
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
            df.id_detalle,
            df.cantidad,
            df.precio_unitario,
            df.descuento_linea,
            df.total_linea,
            p.nombre AS producto_nombre,
            p.codigo AS producto_codigo
        FROM DetalleFactura df
        JOIN Producto p ON df.id_producto = p.id_producto
        WHERE df.id_factura = :id_factura
        ORDER BY df.id_detalle ASC
    ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
