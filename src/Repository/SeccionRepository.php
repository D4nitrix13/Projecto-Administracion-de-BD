<?php

declare(strict_types=1);

namespace App\Repository;

class SeccionRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerTodasLasSecciones(): array
    {
        $statement = $this->connection->query("
            SELECT id_seccion, nombre
            FROM listar_secciones_ordenadas()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerSeccionKitsune(): array
    {
        $statement = $this->connection->prepare("
            SELECT id_seccion, nombre
            FROM obtener_seccion_por_nombre(:nombre)
        ");

        $statement->execute([":nombre" => "Kitsune"]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerSeccionPorId(int $idSeccion): ?array
    {
        $statement = $this->connection->prepare("
            SELECT id_seccion, nombre
            FROM obtener_seccion_por_id(:id_seccion)
        ");

        $statement->execute([":id_seccion" => $idSeccion]);
        $seccion = $statement->fetch(\PDO::FETCH_ASSOC);

        return $seccion ?: null;
    }
}
