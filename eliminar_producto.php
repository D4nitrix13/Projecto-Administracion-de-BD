<?php
// * Stored function or procedure has been executed

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Producto no válido.";
    header("Location: productos.php");
    exit();
}

try {
    $stmtDel = $connection->prepare("
        SELECT filas_afectadas
        FROM eliminar_producto_sistema(:id_producto)
    ");

    $stmtDel->execute([
        ":id_producto" => $id
    ]);

    $resultado = $stmtDel->fetch(PDO::FETCH_ASSOC);
    $filasAfectadas = (int) ($resultado["filas_afectadas"] ?? 0);

    if ($filasAfectadas > 0) {
        $_SESSION["flash_success"] = "Producto eliminado correctamente.";
    } else {
        $_SESSION["flash_error"] = "El producto especificado no existe.";
    }
} catch (PDOException $e) {
    // 23503 = violación de llave foránea: detalle de venta o compra
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar el producto porque tiene ventas o compras asociadas.";
    } else {
        error_log("eliminar producto error: " . $e->getMessage());
        $_SESSION["flash_error"] = "Error al eliminar el producto.";
    }
}

header("Location: productos.php");
exit();
