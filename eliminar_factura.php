<?php
// * Stored function or procedure has been executed

// eliminar_factura.php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

/** @var PDO $connection */
$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Factura no válida.";
    header("Location: facturas.php");
    exit();
}

try {
    $stmt = $connection->prepare("
        SELECT eliminar_factura_sistema(:id_factura) AS eliminado
    ");

    $stmt->execute([
        ":id_factura" => $id,
    ]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($resultado["eliminado"])) {
        $_SESSION["flash_success"] = "Factura eliminada correctamente y stock ajustado.";
    } else {
        $_SESSION["flash_error"] = "No se pudo eliminar la factura.";
    }
} catch (PDOException $e) {
    $_SESSION["flash_error"] = "No se pudo eliminar la factura: " . $e->getMessage();
}

header("Location: facturas.php");
exit();
