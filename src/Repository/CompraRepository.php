<?php

declare(strict_types=1);

namespace App\Repository;

final class CompraRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerProveedores(): array
    {
        $statement = $this->connection->query("
            SELECT id_proveedor, nombre
            FROM listar_proveedores_para_compras()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarios(): array
    {
        $statement = $this->connection->query("
            SELECT id_usuario, nombre
            FROM listar_usuarios_para_compras()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerComprasFiltradas(
        string $busqueda,
        ?int $proveedorId,
        ?int $usuarioId,
        string $fechaDesde,
        string $fechaHasta,
        int $pagina = 1,
        int $porPagina = 15
    ): array {
        $offset = ($pagina - 1) * $porPagina;

        $statement = $this->connection->prepare("
            SELECT id_compra, fecha, total, proveedor, usuario
            FROM buscar_compras_filtradas(
                :busqueda, :id_proveedor, :id_usuario, :fecha_desde, :fecha_hasta,
                :limit, :offset
            )
        ");

        $statement->execute([
            ":busqueda"     => trim($busqueda),
            ":id_proveedor" => $proveedorId,
            ":id_usuario"   => $usuarioId,
            ":fecha_desde"  => $fechaDesde !== "" ? $fechaDesde . " 00:00:00" : null,
            ":fecha_hasta"  => $fechaHasta !== "" ? $fechaHasta . " 23:59:59" : null,
            ":limit"        => $porPagina,
            ":offset"       => $offset,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function contarComprasFiltradas(
        string $busqueda,
        ?int $proveedorId,
        ?int $usuarioId,
        string $fechaDesde,
        string $fechaHasta
    ): int {
        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM buscar_compras_filtradas(
                :busqueda, :id_proveedor, :id_usuario, :fecha_desde, :fecha_hasta
            )
        ");

        $statement->execute([
            ":busqueda"     => trim($busqueda),
            ":id_proveedor" => $proveedorId,
            ":id_usuario"   => $usuarioId,
            ":fecha_desde"  => $fechaDesde !== "" ? $fechaDesde . " 00:00:00" : null,
            ":fecha_hasta"  => $fechaHasta !== "" ? $fechaHasta . " 23:59:59" : null,
        ]);

        return (int) $statement->fetchColumn();
    }
}
