<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Producto no válido.";
    header("Location: productos.php");
    exit();
}

try {
    $stmtDel = $connection->prepare("
        DELETE FROM Producto
        WHERE id_producto = :id
    ");
    $stmtDel->execute([":id" => $id]);

    if ($stmtDel->rowCount() > 0) {
        $_SESSION["flash_success"] = "Producto eliminado correctamente.";
    } else {
        $_SESSION["flash_error"] = "El producto especificado no existe.";
    }
} catch (PDOException $e) {
    // 23503 = violación de llave foránea (detalle de venta / compra)
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar el producto porque tiene ventas o compras asociadas.";
    } else {
        $_SESSION["flash_error"] = "Error al eliminar el producto: " . $e->getMessage();
    }
}

header("Location: productos.php");
exit();
