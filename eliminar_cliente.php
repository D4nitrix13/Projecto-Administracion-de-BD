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
    $_SESSION["flash_error"] = "Cliente no válido.";
    header("Location: clientes.php");
    exit();
}

try {
    $stmt = $connection->prepare("
        SELECT eliminar_cliente_sistema(:id_cliente) AS eliminado
    ");

    $stmt->execute([
        ":id_cliente" => $id,
    ]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($resultado["eliminado"])) {
        $_SESSION["flash_success"] = "Cliente eliminado correctamente.";
    } else {
        $_SESSION["flash_error"] = "No se encontró el cliente a eliminar.";
    }
} catch (PDOException $e) {
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar el cliente porque tiene facturas asociadas.";
    } else {
        $_SESSION["flash_error"] = "No se pudo eliminar el cliente: " . $e->getMessage();
    }
}

header("Location: clientes.php");
exit();
