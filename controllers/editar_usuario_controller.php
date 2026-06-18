<?php

require_once __DIR__ . "/../repositories/UsuarioRepository.php";
require_once __DIR__ . "/../repositories/RolRepository.php";

use App\Service\EmailService;

function obtenerDatosEditarUsuario(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $usuarioRepository = new UsuarioRepository($connection);
    $rolRepository = new RolRepository($connection);

    $error = null;

    $idUsuario = obtenerIdUsuarioDesdeRequest();

    if ($idUsuario <= 0) {
        $_SESSION["flash_error"] = "Trabajador no válido.";
        header("Location: trabajadores.php");
        exit();
    }

    $roles = $rolRepository->obtenerRolesOrdenados();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = actualizarTrabajadorDesdePost(
            $usuarioRepository,
            $idUsuario,
            $_POST
        );

        if ($resultado["success"]) {
            $_SESSION["flash_success"] = "Trabajador actualizado correctamente.";
            header("Location: trabajadores.php");
            exit();
        }

        $error = $resultado["message"];
    }

    $trabajador = $usuarioRepository->obtenerUsuarioPorId($idUsuario);

    if (!$trabajador) {
        $_SESSION["flash_error"] = "El trabajador especificado no existe.";
        header("Location: trabajadores.php");
        exit();
    }

    $seccionTextoActual = obtenerTextoSeccionPorRol((int)$trabajador["id_rol"]);

    return [
        "user" => $user,
        "error" => $error,
        "trabajador" => $trabajador,
        "roles" => $roles,
        "seccionTextoActual" => $seccionTextoActual,
    ];
}

function obtenerIdUsuarioDesdeRequest(): int
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        return (int)($_POST["id_usuario"] ?? 0);
    }

    return (int)($_GET["id"] ?? 0);
}

function actualizarTrabajadorDesdePost(
    UsuarioRepository $usuarioRepository,
    int $idUsuario,
    array $post
): array {
    $nombre = trim($post["nombre"] ?? "");
    $email = trim($post["email"] ?? "");
    $password = trim($post["password"] ?? "");
    $idRol = isset($post["id_rol"]) ? (int)$post["id_rol"] : 0;

    $error = validarDatosEditarTrabajador($nombre, $email, $password, $idRol);

    if ($error !== null) {
        return [
            "success" => false,
            "message" => $error,
        ];
    }

    $idSeccion = resolverSeccionUsuarioPorRol($idRol);

    $datos = [
        "id_usuario" => $idUsuario,
        "nombre" => $nombre,
        "email" => $email,
        "id_rol" => $idRol,
        "id_seccion" => $idSeccion,
        "password" => null,
    ];

    if ($password !== "") {
        $datos["password"] = password_hash($password, PASSWORD_DEFAULT);
    }

    try {
        $usuarioRepository->actualizarUsuario($datos);

        if ($password !== "") {
            $emailService = new EmailService();
            $emailService->sendPasswordChangedNotification($email, $nombre);
        }

        return [
            "success" => true,
            "message" => null,
        ];
    } catch (PDOException $exception) {
        if ($exception->getCode() === "23505") {
            return [
                "success" => false,
                "message" => "Ya existe un usuario con ese email.",
            ];
        }

        return [
            "success" => false,
            "message" => "Error al actualizar el trabajador: " . $exception->getMessage(),
        ];
    }
}

function validarDatosEditarTrabajador(
    string $nombre,
    string $email,
    string $password,
    int $idRol
): ?string {
    if ($nombre === "" || $email === "" || $idRol <= 0) {
        return "Nombre, email y rol son obligatorios.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El email no tiene un formato válido.";
    }

    if ($password !== "" && strlen($password) < 6) {
        return "La nueva contraseña debe tener al menos 6 caracteres.";
    }

    return null;
}

function resolverSeccionUsuarioPorRol(int $idRol): ?int
{
    if ($idRol === 1) {
        return null;
    }

    if ($idRol === 2 || $idRol === 3) {
        return 2;
    }

    return null;
}

function obtenerTextoSeccionPorRol(int $idRol): string
{
    if ($idRol === 1) {
        return "Todas las secciones";
    }

    if ($idRol === 2 || $idRol === 3) {
        return "Kitsune";
    }

    return "Seleccione un rol";
}
