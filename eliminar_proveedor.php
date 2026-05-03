<?php
// * Stored function or procedure has been executed

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

/** @var PDO $connection */
$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Proveedor no válido.";
    header("Location: proveedores.php");
    exit();
}

try {
    $stmtDel = $connection->prepare("
        SELECT eliminar_proveedor_sistema(:id_proveedor) AS eliminado
    ");

    $stmtDel->execute([
        ":id_proveedor" => $id,
    ]);

    $resultado = $stmtDel->fetch(PDO::FETCH_ASSOC);

    if (!empty($resultado["eliminado"])) {
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
