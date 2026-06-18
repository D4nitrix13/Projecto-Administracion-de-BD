<?php

declare(strict_types=1);

namespace App\Repository;

class CatalogoRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerCategorias(): array
    {
        $statement = $this->connection->query("
            SELECT id_categoria, nombre
            FROM listar_categorias_catalogo()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function buscarProductos(
        string $busqueda,
        ?int $idCategoria,
        string $disponibilidad
    ): array {
        $statement = $this->connection->prepare("
            SELECT
                id_producto, codigo, nombre, descripcion,
                imagen, categoria, precio_venta, stock
            FROM buscar_productos_catalogo(
                :busqueda,
                :id_categoria,
                :disponibilidad
            )
        ");

        $statement->execute([
            ":busqueda"      => $busqueda,
            ":id_categoria"  => $idCategoria,
            ":disponibilidad" => $disponibilidad,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
