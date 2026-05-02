<?php

require_once __DIR__ . "/../repositories/ProveedorRepository.php";

function obtenerDatosEditarProveedor(): array
{
    $user = $_SESSION["user"];
    $idRol = (int)($user["id_rol"] ?? 0);

    if (!in_array($idRol, [1, 2], true)) {
        $_SESSION["flash_error"] = "No tiene permisos para editar proveedores.";
        header("Location: proveedores.php");
        exit();
    }

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $proveedorRepository = new ProveedorRepository($connection);

    $error = null;
    $idProveedor = obtenerIdProveedorDesdeRequest();

    if ($idProveedor <= 0) {
        $_SESSION["flash_error"] = "Proveedor no válido.";
        header("Location: proveedores.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $resultado = actualizarProveedorDesdePost(
            $proveedorRepository,
            $idProveedor,
            $_POST
        );

        if ($resultado["success"]) {
            $_SESSION["flash_success"] = $resultado["message"];
            header("Location: proveedores.php");
            exit();
        }

        $error = $resultado["message"];
    }

    $proveedor = $proveedorRepository->obtenerProveedorPorId($idProveedor);

    if (!$proveedor) {
        $_SESSION["flash_error"] = "El proveedor especificado no existe.";
        header("Location: proveedores.php");
        exit();
    }

    return [
        "user" => $user,
        "error" => $error,
        "proveedor" => $proveedor,
    ];
}

function obtenerIdProveedorDesdeRequest(): int
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        return (int)($_POST["id_proveedor"] ?? 0);
    }

    return (int)($_GET["id"] ?? 0);
}

function actualizarProveedorDesdePost(
    ProveedorRepository $proveedorRepository,
    int $idProveedor,
    array $post
): array {
    $nombre = trim($post["nombre"] ?? "");
    $telefono = trim($post["telefono"] ?? "");
    $email = trim($post["email"] ?? "");
    $direccion = trim($post["direccion"] ?? "");

    $error = validarDatosEditarProveedor($nombre, $telefono, $email, $direccion);

    if ($error !== null) {
        return [
            "success" => false,
            "message" => $error,
        ];
    }

    try {
        $proveedorRepository->actualizarProveedor([
            "id_proveedor" => $idProveedor,
            "nombre" => $nombre,
            "telefono" => $telefono !== "" ? $telefono : null,
            "email" => $email !== "" ? $email : null,
            "direccion" => $direccion !== "" ? $direccion : null,
        ]);

        return [
            "success" => true,
            "message" => "Proveedor actualizado correctamente.",
        ];
    } catch (PDOException $exception) {
        return [
            "success" => false,
            "message" => "Error al actualizar el proveedor: " . $exception->getMessage(),
        ];
    }
}

function validarDatosEditarProveedor(
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
