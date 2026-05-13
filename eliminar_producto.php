<?php
session_start();

require_once __DIR__ . "/includes/auth_guard.php";

requireLogin();

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

if ($idRol === 3) {
    $_SESSION["flash_error"] = "No tienes permisos para eliminar productos.";
    header("Location: productos.php");
    exit();
}

if (!isset($_GET["id"]) || !ctype_digit((string)$_GET["id"])) {
    $_SESSION["flash_error"] = "Producto no válido.";
    header("Location: productos.php");
    exit();
}

$idProducto = (int)$_GET["id"];

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

try {
    $stmt = $connection->prepare("
        DELETE FROM producto
        WHERE id_producto = :id_producto
    ");

    $stmt->execute([
        ":id_producto" => $idProducto,
    ]);

    if ($stmt->rowCount() === 0) {
        $_SESSION["flash_error"] = "El producto no existe o ya fue eliminado.";
        header("Location: productos.php");
        exit();
    }

    $_SESSION["flash_success"] = "Producto eliminado correctamente. Puede restaurarlo desde Registros eliminados.";
    header("Location: productos.php");
    exit();
} catch (Throwable $exception) {
    $_SESSION["flash_error"] = "No se pudo eliminar el producto: " . $exception->getMessage();
    header("Location: productos.php");
    exit();
}
