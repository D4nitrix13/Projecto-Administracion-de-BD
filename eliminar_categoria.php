<?php
// * Stored function or procedure has been executed

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

/** @var PDO $connection */
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
        SELECT eliminar_categoria_sistema(:id_categoria) AS eliminado
    ");

    $stmtDel->execute([
        ":id_categoria" => $id,
    ]);

    $resultado = $stmtDel->fetch(PDO::FETCH_ASSOC);

    $_SESSION["flash_success"] = !empty($resultado["eliminado"])
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
