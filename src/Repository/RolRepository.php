<?php

declare(strict_types=1);

namespace App\Repository;

class RolRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerRolesOrdenados(): array
    {
        $statement = $this->connection->query("
            SELECT id_rol, nombre
            FROM listar_roles_ordenados()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
