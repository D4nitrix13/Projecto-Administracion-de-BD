<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Cliente no válido.";
    header("Location: clientes.php");
    exit();
}

try {
    // verificacion si tiene facturas asociadas antes de borrar

    $stmt = $connection->prepare("DELETE FROM Cliente WHERE id_cliente = :id");
    $stmt->execute([":id" => $id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION["flash_success"] = "Cliente eliminado correctamente.";
    } else {
        $_SESSION["flash_error"] = "No se encontró el cliente a eliminar.";
    }
} catch (PDOException $e) {
    $_SESSION["flash_error"] = "No se pudo eliminar el cliente: " . $e->getMessage();
}

header("Location: clientes.php");
exit();
