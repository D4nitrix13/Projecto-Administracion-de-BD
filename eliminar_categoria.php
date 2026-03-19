<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Categoría no válida.";
    header("Location: categorias.php");
    exit();
}

try {
    $stmtDel = $connection->prepare("
        DELETE FROM Categoria
        WHERE id_categoria = :id
    ");
    $stmtDel->execute([":id" => $id]);

    if ($stmtDel->rowCount() > 0) {
        $_SESSION["flash_success"] = "Categoría eliminada correctamente.";
    } else {
        $_SESSION["flash_error"] = "La categoría especificada no existe.";
    }
} catch (PDOException $e) {
    // 23503 = violación de restricción de llave foránea (tiene productos)
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar la categoría porque tiene productos asociados.";
    } else {
        $_SESSION["flash_error"] = "Error al eliminar la categoría: " . $e->getMessage();
    }
}

header("Location: categorias.php");
exit();
