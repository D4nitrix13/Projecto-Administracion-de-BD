<?php

class SeccionRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerTodasLasSecciones(): array
    {
        $statement = $this->connection->query("
            SELECT 
                id_seccion,
                nombre
            FROM Seccion
            ORDER BY nombre
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSeccionKitsune(): array
    {
        $statement = $this->connection->prepare("
            SELECT 
                id_seccion,
                nombre
            FROM Seccion
            WHERE nombre = :nombre
            ORDER BY nombre
        ");

        $statement->execute([
            ":nombre" => "Kitsune",
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSeccionPorId(int $idSeccion): ?array
    {
        $statement = $this->connection->prepare("
        SELECT 
            id_seccion,
            nombre
        FROM Seccion
        WHERE id_seccion = :id_seccion
    ");

        $statement->execute([
            ":id_seccion" => $idSeccion,
        ]);

        $seccion = $statement->fetch(PDO::FETCH_ASSOC);

        return $seccion ?: null;
    }

    
}
