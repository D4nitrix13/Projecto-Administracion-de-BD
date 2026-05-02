<?php

require_once __DIR__ . "/../repositories/ClienteRepository.php";

function obtenerDatosEditarCliente(): array
{
    $user = $_SESSION["user"];
    $idRol = (int)($user["id_rol"] ?? 0);

    $isRestrictedRole = in_array($idRol, [2, 3], true);

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $clienteRepository = new ClienteRepository($connection);

    $error = null;

    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if ($id <= 0) {
        $_SESSION["flash_error"] = "Cliente no válido.";
        header("Location: clientes.php");
        exit();
    }

    $cliente = $clienteRepository->obtenerClientePorId($id);

    if (!$cliente) {
        $_SESSION["flash_error"] = "Cliente no encontrado.";
        header("Location: clientes.php");
        exit();
    }

    if ($isRestrictedRole && $cliente["tipo_cliente"] !== "Detallista") {
        $_SESSION["flash_error"] = "Su rol solo permite editar clientes detallistas.";
        header("Location: clientes.php");
        exit();
    }

    $nombres = $cliente["nombres"];
    $apellidos = $cliente["apellidos"];
    $telefono = $cliente["telefono"];
    $direccion = $cliente["direccion"];
    $identificacion = $cliente["identificacion"];
    $tipoCliente = $cliente["tipo_cliente"];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombres = trim($_POST["nombres"] ?? "");
        $apellidos = trim($_POST["apellidos"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $direccion = trim($_POST["direccion"] ?? "");
        $identificacion = trim($_POST["identificacion"] ?? "");

        if ($isRestrictedRole) {
            $tipoCliente = "Detallista";
        } else {
            $tipoCliente = $_POST["tipo_cliente"] ?? "Detallista";
        }

        $error = validarDatosClienteEditado(
            $nombres,
            $apellidos,
            $tipoCliente
        );

        if ($error === null) {
            try {
                $clienteRepository->actualizarCliente($id, [
                    "nombres" => $nombres,
                    "apellidos" => $apellidos,
                    "telefono" => $telefono,
                    "direccion" => $direccion,
                    "identificacion" => $identificacion,
                    "tipo_cliente" => $tipoCliente,
                ]);

                $_SESSION["flash_success"] = "Cliente actualizado correctamente.";

                header("Location: clientes.php");
                exit();
            } catch (PDOException $exception) {
                $error = "Error al actualizar el cliente: " . $exception->getMessage();
            }
        }
    }

    return [
        "user" => $user,
        "id" => $id,
        "error" => $error,
        "nombres" => $nombres,
        "apellidos" => $apellidos,
        "telefono" => $telefono,
        "direccion" => $direccion,
        "identificacion" => $identificacion,
        "tipoCliente" => $tipoCliente,
        "isRestrictedRole" => $isRestrictedRole,
    ];
}

function validarDatosClienteEditado(
    string $nombres,
    string $apellidos,
    string $tipoCliente
): ?string {
    if ($nombres === "" || $apellidos === "") {
        return "Complete los campos obligatorios marcados con (*).";
    }

    if (!in_array($tipoCliente, ["Mayorista", "Detallista"], true)) {
        return "Tipo de cliente no válido.";
    }

    return null;
}
