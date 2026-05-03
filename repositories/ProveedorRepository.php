<?php
// * Stored function or procedure has been executed

class ProveedorRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerProveedoresOrdenados(): array
    {
        $statement = $this->connection->query("
            SELECT
                id_proveedor,
                nombre
            FROM listar_proveedores_ordenados()
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearProveedor(array $datos): void
    {
        $statement = $this->connection->prepare("
            SELECT crear_proveedor_sistema(
                :nombre,
                :telefono,
                :email,
                :direccion
            ) AS creado
        ");

        $statement->execute([
            ":nombre" => $datos["nombre"],
            ":telefono" => $datos["telefono"],
            ":email" => $datos["email"],
            ":direccion" => $datos["direccion"],
        ]);
    }

    public function obtenerProveedoresFiltrados(string $busqueda): array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_proveedor,
                nombre,
                telefono,
                email,
                direccion
            FROM buscar_proveedores_filtrados(:busqueda)
        ");

        $statement->execute([
            ":busqueda" => trim($busqueda),
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProveedorPorId(int $idProveedor): ?array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_proveedor,
                nombre,
                telefono,
                email,
                direccion
            FROM obtener_proveedor_por_id(:id_proveedor)
        ");

        $statement->execute([
            ":id_proveedor" => $idProveedor,
        ]);

        $proveedor = $statement->fetch(PDO::FETCH_ASSOC);

        return $proveedor ?: null;
    }

    public function actualizarProveedor(array $datos): void
    {
        $statement = $this->connection->prepare("
            SELECT actualizar_proveedor_sistema(
                :id_proveedor,
                :nombre,
                :telefono,
                :email,
                :direccion
            ) AS actualizado
        ");

        $statement->execute([
            ":id_proveedor" => $datos["id_proveedor"],
            ":nombre" => $datos["nombre"],
            ":telefono" => $datos["telefono"],
            ":email" => $datos["email"],
            ":direccion" => $datos["direccion"],
        ]);
    }

    public function eliminarProveedor(int $idProveedor): void
    {
        $statement = $this->connection->prepare("
            SELECT eliminar_proveedor_sistema(:id_proveedor) AS eliminado
        ");

        $statement->execute([
            ":id_proveedor" => $idProveedor,
        ]);
    }
}
