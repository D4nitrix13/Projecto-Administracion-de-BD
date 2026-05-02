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
}
