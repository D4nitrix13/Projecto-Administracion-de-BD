<?php
// * Stored function or procedure has been executed

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
            FROM obtener_producto_edicion_por_id(:id_producto)
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
            SELECT actualizar_producto_edicion(
                :id_producto,
                :codigo,
                :nombre,
                :descripcion,
                :imagen,
                :id_categoria,
                :id_proveedor,
                :precio_compra,
                :precio_venta,
                :stock
            ) AS actualizado
        ");

        $statement->execute([
            ":id_producto" => $idProducto,
            ":codigo" => $datos["codigo"],
            ":nombre" => $datos["nombre"],
            ":descripcion" => $datos["descripcion"],
            ":imagen" => $datos["imagen"],
            ":id_categoria" => $datos["id_categoria"],
            ":id_proveedor" => $datos["id_proveedor"],
            ":precio_compra" => (float)$datos["precio_compra"],
            ":precio_venta" => (float)$datos["precio_venta"],
            ":stock" => (int)$datos["stock"],
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
            FROM listar_productos_para_factura()
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
