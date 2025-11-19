<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Proveedor no válido.";
    header("Location: proveedores.php");
    exit();
}

try {
    $stmtDel = $connection->prepare("
        DELETE FROM Proveedor
        WHERE id_proveedor = :id
    ");
    $stmtDel->execute([":id" => $id]);

    if ($stmtDel->rowCount() > 0) {
        $_SESSION["flash_success"] = "Proveedor eliminado correctamente.";
    } else {
        $_SESSION["flash_error"] = "El proveedor especificado no existe.";
    }
} catch (PDOException $e) {
    // 23503 = violación de restricción de llave foránea en PostgreSQL
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar el proveedor porque está asociado a compras o productos.";
    } else {
        $_SESSION["flash_error"] = "Error al eliminar el proveedor: " . $e->getMessage();
    }
}

header("Location: proveedores.php");
exit();
