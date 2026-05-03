<?php

require_once __DIR__ . "/../repositories/UsuarioRepository.php";
require_once __DIR__ . "/../repositories/RolRepository.php";
require_once __DIR__ . "/../repositories/SeccionRepository.php";

function obtenerDatosUsuarios(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $usuarioRepository = new UsuarioRepository($connection);
    $rolRepository = new RolRepository($connection);
    $seccionRepository = new SeccionRepository($connection);

    $flashSuccess = $_SESSION["flash_success"] ?? null;
    $flashError = $_SESSION["flash_error"] ?? null;

    unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

    $error = null;

    $busqueda = trim($_GET["q"] ?? "");
    $rolFiltro = $_GET["rol"] ?? "";
    $seccionFiltro = $_GET["seccion"] ?? "";

    $rolFiltroInt = ctype_digit($rolFiltro) ? (int)$rolFiltro : null;

    $roles = $rolRepository->obtenerRolesOrdenados();
    $secciones = $seccionRepository->obtenerTodasLasSecciones();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = registrarTrabajadorDesdePost($usuarioRepository, $_POST);

        if ($resultado["success"]) {
            $_SESSION["flash_success"] = "Trabajador registrado correctamente.";
            header("Location: trabajadores.php");
            exit();
        }

        $error = $resultado["message"];
    }

    $usuarios = $usuarioRepository->obtenerUsuariosFiltrados([
        "busqueda" => $busqueda,
        "rolFiltroInt" => $rolFiltroInt,
        "seccionFiltro" => $seccionFiltro,
    ]);

    return [
        "user" => $user,
        "error" => $error,
        "flashSuccess" => $flashSuccess,
        "flashError" => $flashError,
        "roles" => $roles,
        "secciones" => $secciones,
        "usuarios" => $usuarios,
        "busqueda" => $busqueda,
        "rolFiltroInt" => $rolFiltroInt,
        "seccionFiltro" => $seccionFiltro,
    ];
}

function registrarTrabajadorDesdePost(
    UsuarioRepository $usuarioRepository,
    array $post
): array {
    $nombre = trim($post["nombre"] ?? "");
    $email = trim($post["email"] ?? "");
    $password = trim($post["password"] ?? "");
    $idRol = isset($post["id_rol"]) ? (int)$post["id_rol"] : 0;

    $error = validarDatosTrabajador($nombre, $email, $password, $idRol);

    if ($error !== null) {
        return [
            "success" => false,
            "message" => $error,
        ];
    }

    $idSeccion = resolverSeccionPorRol($idRol);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $usuarioRepository->crearUsuario([
            "nombre" => $nombre,
            "email" => $email,
            "password" => $passwordHash,
            "id_rol" => $idRol,
            "id_seccion" => $idSeccion,
        ]);

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
            "message" => "Error al registrar el trabajador: " . $exception->getMessage(),
        ];
    }
}

function validarDatosTrabajador(
    string $nombre,
    string $email,
    string $password,
    int $idRol
): ?string {
    if ($nombre === "" || $email === "" || $password === "" || $idRol <= 0) {
        return "Nombre, email, contraseña y rol son obligatorios.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El email no tiene un formato válido.";
    }

    if (strlen($password) < 6) {
        return "La contraseña debe tener al menos 6 caracteres.";
    }

    return null;
}

function resolverSeccionPorRol(int $idRol): ?int
{
    if ($idRol === 1) {
        return null;
    }

    if ($idRol === 2 || $idRol === 3) {
        return 2;
    }

    return null;
}
