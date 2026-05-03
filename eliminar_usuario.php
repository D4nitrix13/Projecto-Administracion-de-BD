<?php
// * Stored function or procedure has been executed

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];

// Solo administradores
if (($user["rol"] ?? "") !== "Administrador") {
    $_SESSION["flash_error"] = "No tiene permiso para eliminar trabajadores.";
    header("Location: usuarios.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Trabajador no válido.";
    header("Location: usuarios.php");
    exit();
}

try {
    $stmtDel = $connection->prepare("
        SELECT filas_afectadas
        FROM eliminar_usuario_sistema(:id_usuario, :id_usuario_actual)
    ");

    $stmtDel->execute([
        ":id_usuario" => $id,
        ":id_usuario_actual" => (int) $user["id_usuario"]
    ]);

    $resultado = $stmtDel->fetch(PDO::FETCH_ASSOC);
    $filasAfectadas = (int) ($resultado["filas_afectadas"] ?? 0);

    if ($filasAfectadas > 0) {
        $_SESSION["flash_success"] = "Trabajador eliminado correctamente.";
    } else {
        $_SESSION["flash_error"] = "El trabajador especificado no existe.";
    }
} catch (PDOException $e) {
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar el trabajador porque tiene facturas o compras asociadas.";
    } else {
        $_SESSION["flash_error"] = "Error al eliminar el trabajador: " . $e->getMessage();
    }
}

header("Location: usuarios.php");
exit();
