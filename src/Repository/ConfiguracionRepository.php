<?php

declare(strict_types=1);

namespace App\Repository;

class ConfiguracionRepository
{
    public function __construct(private \PDO $connection) {}

    public function obtenerUsuarioCuenta(int $idUsuario): ?array
    {
        $statement = $this->connection->prepare("
            SELECT id_usuario, nombre, email, password, id_rol, id_seccion, rol_nombre, seccion_nombre
            FROM obtener_usuario_configurar_cuenta(:id_usuario)
        ");

        $statement->execute([
            ":id_usuario" => $idUsuario,
        ]);

        $dbUser = $statement->fetch(\PDO::FETCH_ASSOC);

        return $dbUser ?: null;
    }

    public function actualizarCuenta(int $idUsuario, string $nombre, string $email, string $password): bool
    {
        $statement = $this->connection->prepare("
            SELECT actualizar_usuario_configurar_cuenta(
                :id_usuario, :nombre, :email, :password
            ) AS actualizado
        ");

        $statement->execute([
            ":id_usuario" => $idUsuario,
            ":nombre" => $nombre,
            ":email" => $email,
            ":password" => $password,
        ]);

        $resultado = $statement->fetch(\PDO::FETCH_ASSOC);

        return !empty($resultado["actualizado"]);
    }
}
