<?php
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

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Trabajador no válido.";
    header("Location: usuarios.php");
    exit();
}

// No permitir borrar al jefe (id 1)
if ($id === 1) {
    $_SESSION["flash_error"] = "No se puede eliminar la cuenta del jefe.";
    header("Location: usuarios.php");
    exit();
}

// Evitar que un admin se borre a sí mismo (opcional)
if ($id === (int)$user["id_usuario"]) {
    $_SESSION["flash_error"] = "No puede eliminar su propia cuenta.";
    header("Location: usuarios.php");
    exit();
}

try {
    $stmtDel = $connection->prepare("
        DELETE FROM Usuario
        WHERE id_usuario = :id
    ");
    $stmtDel->execute([":id" => $id]);

    if ($stmtDel->rowCount() > 0) {
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
