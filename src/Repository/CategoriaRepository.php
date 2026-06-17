<?php

declare(strict_types=1);

namespace App\Repository;

class CategoriaRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerCategoriasOrdenadas(): array
    {
        $statement = $this->connection->query("
            SELECT id_categoria, nombre
            FROM listar_categorias_ordenadas()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
