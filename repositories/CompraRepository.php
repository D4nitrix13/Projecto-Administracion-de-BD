<?php
// * Stored function or procedure has been executed

final class CompraRepository
{
    public function __construct(private PDO $connection) {}

    public function obtenerProveedores(): array
    {
        $statement = $this->connection->query("
            SELECT
                id_proveedor,
                nombre
            FROM listar_proveedores_para_compras()
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarios(): array
    {
        $statement = $this->connection->query("
            SELECT
                id_usuario,
                nombre
            FROM listar_usuarios_para_compras()
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerComprasFiltradas(
        string $busqueda,
        ?int $proveedorId,
        ?int $usuarioId,
        string $fechaDesde,
        string $fechaHasta
    ): array {
        $fechaDesdeSql = $fechaDesde !== ""
            ? $fechaDesde . " 00:00:00"
            : null;

        $fechaHastaSql = $fechaHasta !== ""
            ? $fechaHasta . " 23:59:59"
            : null;

        $statement = $this->connection->prepare("
            SELECT
                id_compra,
                fecha,
                total,
                proveedor,
                usuario
            FROM buscar_compras_filtradas(
                :busqueda,
                :id_proveedor,
                :id_usuario,
                :fecha_desde,
                :fecha_hasta
            )
        ");

        $statement->execute([
            ":busqueda" => trim($busqueda),
            ":id_proveedor" => $proveedorId,
            ":id_usuario" => $usuarioId,
            ":fecha_desde" => $fechaDesdeSql,
            ":fecha_hasta" => $fechaHastaSql,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
