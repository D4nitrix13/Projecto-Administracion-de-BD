<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

if ($idRol !== 1) {
    $_SESSION["flash_error"] = "Su rol no tiene permisos para eliminar categorías.";
    header("Location: categorias.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) && ctype_digit((string)$_GET["id"])
    ? (int)$_GET["id"]
    : 0;

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

    $stmtDel->execute([
        ":id" => $id
    ]);

    $_SESSION["flash_success"] = $stmtDel->rowCount() > 0
        ? "Categoría eliminada correctamente."
        : "La categoría especificada no existe.";
} catch (PDOException $e) {
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar la categoría porque tiene productos asociados.";
    } else {
        error_log("eliminar categoria error: " . $e->getMessage());
        $_SESSION["flash_error"] = "Error al eliminar la categoría.";
    }
}

header("Location: categorias.php");
exit();
