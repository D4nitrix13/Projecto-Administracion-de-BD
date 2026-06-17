<?php

declare(strict_types=1);

namespace App\Repository;

class UsuarioRepository
{
    public function __construct(private \PDO $connection) {}

    public function crearUsuario(array $datos): void
    {
        $statement = $this->connection->prepare("
            SELECT crear_usuario_sistema(
                :nombre, :email, :password, :id_rol, :id_seccion
            ) AS creado
        ");

        $statement->execute([
            ":nombre"     => $datos["nombre"],
            ":email"      => $datos["email"],
            ":password"   => $datos["password"],
            ":id_rol"     => $datos["id_rol"],
            ":id_seccion" => $datos["id_seccion"],
        ]);
    }

    public function obtenerUsuariosFiltrados(array $filtros): array
    {
        $statement = $this->connection->prepare("
            SELECT
                id_usuario, nombre, email, id_rol, id_seccion, rol, seccion
            FROM buscar_usuarios_filtrados(
                :busqueda, :id_rol, :seccion_filtro
            )
        ");

        $statement->execute([
            ":busqueda"      => trim($filtros["busqueda"] ?? ""),
            ":id_rol"        => $filtros["rolFiltroInt"] ?? null,
            ":seccion_filtro" => $filtros["seccionFiltro"] ?? "",
        ]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerUsuariosOrdenados(): array
    {
        $statement = $this->connection->query("
            SELECT id_usuario, nombre
            FROM listar_usuarios_ordenados()
        ");

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarioPorId(int $idUsuario): ?array
    {
        $statement = $this->connection->prepare("
            SELECT id_usuario, nombre, email, id_rol, id_seccion
            FROM obtener_usuario_edicion_por_id(:id_usuario)
        ");

        $statement->execute([":id_usuario" => $idUsuario]);
        $usuario = $statement->fetch(\PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function actualizarUsuario(array $datos): void
    {
        $statement = $this->connection->prepare("
            SELECT actualizar_usuario_sistema(
                :id_usuario, :nombre, :email, :id_rol, :id_seccion, :password
            ) AS actualizado
        ");

        $statement->execute([
            ":id_usuario" => $datos["id_usuario"],
            ":nombre"     => $datos["nombre"],
            ":email"      => $datos["email"],
            ":id_rol"     => $datos["id_rol"],
            ":id_seccion" => $datos["id_seccion"],
            ":password"   => $datos["password"] ?? null,
        ]);
    }
}
