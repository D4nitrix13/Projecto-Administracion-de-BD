<?php
// * Stored function or procedure has been executed

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
            FROM listar_categorias_ordenadas()
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
