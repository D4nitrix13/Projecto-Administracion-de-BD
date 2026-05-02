<?php

final class CompraRepository
{
    public function __construct(private PDO $connection) {}

    public function obtenerProveedores(): array
    {
        $sql = "
            SELECT id_proveedor, nombre
            FROM Proveedor
            ORDER BY nombre
        ";

        $statement = $this->connection->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarios(): array
    {
        $sql = "
            SELECT id_usuario, nombre
            FROM Usuario
            ORDER BY nombre
        ";

        $statement = $this->connection->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerComprasFiltradas(
        string $busqueda,
        ?int $proveedorId,
        ?int $usuarioId,
        string $fechaDesde,
        string $fechaHasta
    ): array {
        $sql = "
            SELECT
                c.id_compra,
                c.fecha,
                c.total,
                p.nombre AS proveedor,
                u.nombre AS usuario
            FROM Compra c
            INNER JOIN Proveedor p ON c.id_proveedor = p.id_proveedor
            INNER JOIN Usuario u ON c.id_usuario = u.id_usuario
            WHERE 1 = 1
        ";

        $params = [];

        if ($busqueda !== "") {
            $sql .= "
                AND (
                    p.nombre ILIKE :busqueda
                    OR u.nombre ILIKE :busqueda
                    OR CAST(c.id_compra AS TEXT) ILIKE :busqueda
                )
            ";

            $params[":busqueda"] = "%" . $busqueda . "%";
        }

        if ($proveedorId !== null) {
            $sql .= " AND c.id_proveedor = :proveedor_id";
            $params[":proveedor_id"] = $proveedorId;
        }

        if ($usuarioId !== null) {
            $sql .= " AND c.id_usuario = :usuario_id";
            $params[":usuario_id"] = $usuarioId;
        }

        if ($fechaDesde !== "") {
            $sql .= " AND c.fecha >= :fecha_desde";
            $params[":fecha_desde"] = $fechaDesde . " 00:00:00";
        }

        if ($fechaHasta !== "") {
            $sql .= " AND c.fecha <= :fecha_hasta";
            $params[":fecha_hasta"] = $fechaHasta . " 23:59:59";
        }

        $sql .= " ORDER BY c.fecha DESC";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
