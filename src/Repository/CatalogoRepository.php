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
        string $disponibilidad,
        int $limite = 20,
        int $offset = 0
    ): array {
        $statement = $this->connection->prepare("
            SELECT
                id_producto, codigo, nombre, descripcion,
                imagen, categoria, precio_venta, stock,
                total_registros
            FROM buscar_productos_catalogo(
                :busqueda,
                :id_categoria,
                :disponibilidad,
                :limite,
                :offset
            )
        ");

        $statement->execute([
            ":busqueda"      => $busqueda,
            ":id_categoria"  => $idCategoria,
            ":disponibilidad" => $disponibilidad,
            ":limite"        => $limite,
            ":offset"        => $offset,
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerCategoriasConImagen(int $limite = 8): array
    {
        $statement = $this->connection->prepare("
            WITH primera_imagen AS (
                SELECT
                    p.id_categoria,
                    p.imagen,
                    p.nombre,
                    ROW_NUMBER() OVER (PARTITION BY p.id_categoria ORDER BY p.nombre) AS rn
                FROM Producto p
                JOIN Categoria c ON p.id_categoria = c.id_categoria
                WHERE c.nombre <> 'Categoria Temporal'
                  AND p.imagen IS NOT NULL
                  AND p.imagen <> ''
            )
            SELECT
                c.id_categoria,
                c.nombre,
                pi.imagen AS primera_imagen,
                (SELECT COUNT(*) FROM Producto WHERE id_categoria = c.id_categoria) AS total_productos
            FROM Categoria c
            LEFT JOIN primera_imagen pi ON pi.id_categoria = c.id_categoria AND pi.rn = 1
            WHERE c.nombre <> 'Categoria Temporal'
            ORDER BY c.nombre
            LIMIT :limite
        ");

        $statement->execute([":limite" => $limite]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerMasVendidos(int $limite = 12): array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_producto, codigo, nombre, imagen,
                precio_venta, stock, total_vendido
            FROM productos_mas_vendidos_catalogo(:limite)
        ");

        $statement->execute([":limite" => $limite]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleProducto(int $idProducto): ?array
    {
        $statement = $this->connection->prepare("
            SELECT
                p.id_producto,
                p.codigo,
                p.nombre,
                p.descripcion,
                p.imagen,
                p.precio_venta,
                p.stock,
                c.nombre AS categoria
            FROM Producto p
            LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
            WHERE p.id_producto = :id
              AND c.nombre <> 'Categoria Temporal'
        ");

        $statement->execute([":id" => $idProducto]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
