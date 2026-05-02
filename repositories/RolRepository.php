<?php

class RolRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerRolesOrdenados(): array
    {
        $statement = $this->connection->query("
            SELECT 
                id_rol,
                nombre
            FROM Rol
            ORDER BY nombre
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
