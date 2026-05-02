<?php

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
            FROM Proveedor
            ORDER BY nombre
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearProveedor(array $datos): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO Proveedor (
                nombre,
                telefono,
                email,
                direccion
            )
            VALUES (
                :nombre,
                :telefono,
                :email,
                :direccion
            )
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
        $sql = "
            SELECT 
                id_proveedor,
                nombre,
                telefono,
                email,
                direccion
            FROM Proveedor
            WHERE 1 = 1
        ";

        $params = [];

        if ($busqueda !== "") {
            $sql .= "
                AND (
                    CAST(id_proveedor AS TEXT) ILIKE :q
                    OR nombre ILIKE :q
                    OR telefono ILIKE :q
                    OR email ILIKE :q
                    OR direccion ILIKE :q
                )
            ";

            $params[":q"] = "%" . $busqueda . "%";
        }

        $sql .= " ORDER BY nombre ASC";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

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
            FROM Proveedor
            WHERE id_proveedor = :id_proveedor
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
            UPDATE Proveedor
            SET 
                nombre = :nombre,
                telefono = :telefono,
                email = :email,
                direccion = :direccion
            WHERE id_proveedor = :id_proveedor
        ");

        $statement->execute([
            ":nombre" => $datos["nombre"],
            ":telefono" => $datos["telefono"],
            ":email" => $datos["email"],
            ":direccion" => $datos["direccion"],
            ":id_proveedor" => $datos["id_proveedor"],
        ]);
    }

    public function eliminarProveedor(int $idProveedor): void
    {
        $statement = $this->connection->prepare("
            DELETE FROM Proveedor
            WHERE id_proveedor = :id_proveedor
        ");

        $statement->execute([
            ":id_proveedor" => $idProveedor,
        ]);
    }
}
