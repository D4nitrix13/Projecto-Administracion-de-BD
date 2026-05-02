<?php

class UsuarioRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerUsuariosOrdenados(): array
    {
        $statement = $this->connection->query("
            SELECT 
                id_usuario,
                nombre
            FROM Usuario
            ORDER BY nombre
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
