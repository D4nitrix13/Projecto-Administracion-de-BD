<?php

class ProductoRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerProductoPorId(int $idProducto): ?array
    {
        $statement = $this->connection->prepare("
            SELECT 
                id_producto,
                codigo,
                nombre,
                descripcion,
                imagen,
                id_categoria,
                id_proveedor,
                precio_compra,
                precio_venta,
                stock
            FROM Producto
            WHERE id_producto = :id_producto
        ");

        $statement->execute([
            ":id_producto" => $idProducto,
        ]);

        $producto = $statement->fetch(PDO::FETCH_ASSOC);

        return $producto ?: null;
    }

    public function actualizarProducto(int $idProducto, array $datos): void
    {
        $statement = $this->connection->prepare("
            UPDATE Producto
            SET 
                codigo = :codigo,
                nombre = :nombre,
                descripcion = :descripcion,
                imagen = :imagen,
                id_categoria = :id_categoria,
                id_proveedor = :id_proveedor,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                stock = :stock
            WHERE id_producto = :id_producto
        ");

        $statement->execute([
            ":codigo" => $datos["codigo"],
            ":nombre" => $datos["nombre"],
            ":descripcion" => $datos["descripcion"],
            ":imagen" => $datos["imagen"],
            ":id_categoria" => $datos["id_categoria"],
            ":id_proveedor" => $datos["id_proveedor"],
            ":precio_compra" => (float)$datos["precio_compra"],
            ":precio_venta" => (float)$datos["precio_venta"],
            ":stock" => (int)$datos["stock"],
            ":id_producto" => $idProducto,
        ]);
    }

    public function obtenerProductosParaFactura(): array
    {
        $statement = $this->connection->query("
        SELECT 
            id_producto,
            codigo,
            nombre,
            precio_venta,
            stock
        FROM Producto
        ORDER BY nombre
    ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
