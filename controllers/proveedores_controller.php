<?php

require_once __DIR__ . "/../repositories/ProveedorRepository.php";
require_once __DIR__ . "/../helpers/pagination.php";

function obtenerDatosProveedores(): array
{
    $user = $_SESSION["user"];
    $idRol = (int)($user["id_rol"] ?? 0);
    $puedeGestionar = puedeGestionarProveedores($idRol);

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $proveedorRepository = new ProveedorRepository($connection);

    $flashSuccess = $_SESSION["flash_success"] ?? null;
    $flashError = $_SESSION["flash_error"] ?? null;

    unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

    $error = null;
    $success = null;

    $busqueda = trim($_GET["q"] ?? "");
    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!$puedeGestionar) {
            $error = "No tiene permisos para registrar nuevos proveedores.";
        } else {
            $resultado = registrarProveedorDesdePost($proveedorRepository, $_POST);

            if ($resultado["success"]) {
                $success = "Proveedor registrado correctamente.";
            } else {
                $error = $resultado["message"];
            }
        }
    }

    $totalRegistros = $proveedorRepository->contarProveedoresFiltrados($busqueda);
    $paginacion = calcularPaginacion($totalRegistros, $paginaActual, 15);

    $proveedores = $proveedorRepository->obtenerProveedoresFiltrados(
        $busqueda,
        $paginacion["paginaActual"],
        $paginacion["porPagina"]
    );

    $filtrosGET = array_filter([
        "q" => $busqueda ?: null,
    ], fn($v) => $v !== null);

    return [
        "user" => $user,
        "idRol" => $idRol,
        "puedeGestionar" => $puedeGestionar,
        "error" => $error,
        "success" => $success,
        "flashSuccess" => $flashSuccess,
        "flashError" => $flashError,
        "busqueda" => $busqueda,
        "proveedores" => $proveedores,
        "paginacion" => $paginacion,
        "filtrosGET" => $filtrosGET,
    ];
}

function puedeGestionarProveedores(int $idRol): bool
{
    return in_array($idRol, [1, 2], true);
}

function registrarProveedorDesdePost(
    ProveedorRepository $proveedorRepository,
    array $post
): array {
    $nombre = trim($post["nombre"] ?? "");
    $telefono = trim($post["telefono"] ?? "");
    $email = trim($post["email"] ?? "");
    $direccion = trim($post["direccion"] ?? "");

    $error = validarDatosProveedor($nombre, $telefono, $email, $direccion);

    if ($error !== null) {
        return [
            "success" => false,
            "message" => $error,
        ];
    }

    try {
        $proveedorRepository->crearProveedor([
            "nombre" => $nombre,
            "telefono" => $telefono !== "" ? $telefono : null,
            "email" => $email !== "" ? $email : null,
            "direccion" => $direccion !== "" ? $direccion : null,
        ]);

        return [
            "success" => true,
            "message" => null,
        ];
    } catch (PDOException $exception) {
        return [
            "success" => false,
            "message" => "Error al registrar el proveedor: " . $exception->getMessage(),
        ];
    }
}

function validarDatosProveedor(
    string $nombre,
    string $telefono,
    string $email,
    string $direccion
): ?string {
    if ($nombre === "") {
        return "El nombre del proveedor es obligatorio.";
    }

    if (mb_strlen($nombre) > 120) {
        return "El nombre no debe superar los 120 caracteres.";
    }

    if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El email no tiene un formato válido.";
    }

    if ($email !== "" && mb_strlen($email) > 120) {
        return "El email no debe superar los 120 caracteres.";
    }

    if (mb_strlen($telefono) > 30) {
        return "El teléfono no debe superar los 30 caracteres.";
    }

    if (mb_strlen($direccion) > 200) {
        return "La dirección no debe superar los 200 caracteres.";
    }

    return null;
}
