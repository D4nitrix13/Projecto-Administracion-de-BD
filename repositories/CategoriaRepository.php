<?php

class CategoriaRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerCategoriasOrdenadas(): array
    {
        $statement = $this->connection->query("
            SELECT 
                id_categoria,
                nombre
            FROM Categoria
            ORDER BY nombre
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
