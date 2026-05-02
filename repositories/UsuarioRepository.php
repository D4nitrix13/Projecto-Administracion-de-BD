<?php

class UsuarioRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function crearUsuario(array $datos): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO Usuario (
                nombre,
                email,
                password,
                id_rol,
                id_seccion
            )
            VALUES (
                :nombre,
                :email,
                :password,
                :id_rol,
                :id_seccion
            )
        ");

        $statement->execute([
            ":nombre" => $datos["nombre"],
            ":email" => $datos["email"],
            ":password" => $datos["password"],
            ":id_rol" => $datos["id_rol"],
            ":id_seccion" => $datos["id_seccion"],
        ]);
    }

    public function obtenerUsuariosFiltrados(array $filtros): array
    {
        $busqueda = trim($filtros["busqueda"] ?? "");
        $rolFiltroInt = $filtros["rolFiltroInt"] ?? null;
        $seccionFiltro = $filtros["seccionFiltro"] ?? "";

        $sql = "
            SELECT 
                u.id_usuario,
                u.nombre,
                u.email,
                u.id_rol,
                u.id_seccion,
                r.nombre AS rol,
                s.nombre AS seccion
            FROM Usuario u
            INNER JOIN Rol r ON u.id_rol = r.id_rol
            LEFT JOIN Seccion s ON u.id_seccion = s.id_seccion
            WHERE u.id_usuario <> 1
        ";

        $params = [];

        if ($busqueda !== "") {
            $sql .= "
                AND (
                    u.nombre ILIKE :q
                    OR u.email ILIKE :q
                    OR r.nombre ILIKE :q
                    OR COALESCE(s.nombre, 'Todas las secciones') ILIKE :q
                )
            ";

            $params[":q"] = "%" . $busqueda . "%";
        }

        if ($rolFiltroInt !== null) {
            $sql .= " AND u.id_rol = :rol";
            $params[":rol"] = $rolFiltroInt;
        }

        if ($seccionFiltro === "none") {
            $sql .= " AND u.id_seccion IS NULL";
        } elseif (ctype_digit($seccionFiltro)) {
            $sql .= " AND u.id_seccion = :seccion";
            $params[":seccion"] = (int)$seccionFiltro;
        }

        $sql .= " ORDER BY u.nombre ASC";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
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

    public function obtenerUsuarioPorId(int $idUsuario): ?array
    {
        $statement = $this->connection->prepare("
        SELECT 
            id_usuario,
            nombre,
            email,
            id_rol,
            id_seccion
        FROM Usuario
        WHERE id_usuario = :id_usuario
    ");

        $statement->execute([
            ":id_usuario" => $idUsuario,
        ]);

        $usuario = $statement->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function actualizarUsuario(array $datos): void
    {
        $sql = "
        UPDATE Usuario
        SET 
            nombre = :nombre,
            email = :email,
            id_rol = :id_rol,
            id_seccion = :id_seccion
    ";

        $params = [
            ":nombre" => $datos["nombre"],
            ":email" => $datos["email"],
            ":id_rol" => $datos["id_rol"],
            ":id_seccion" => $datos["id_seccion"],
            ":id_usuario" => $datos["id_usuario"],
        ];

        if (!empty($datos["password"])) {
            $sql .= ", password = :password";
            $params[":password"] = $datos["password"];
        }

        $sql .= "
        WHERE id_usuario = :id_usuario
    ";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
    }
}
