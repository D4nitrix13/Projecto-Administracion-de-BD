<?php
// eliminar_seccion.php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
/** @var PDO $connection */
$connection = require "./sql/db.php";

// Solo administradores
if (($user["rol"] ?? "") !== "Administrador") {
    $_SESSION["flash_error"] = "No tiene permiso para gestionar secciones.";
    header("Location: dashboard.php");
    exit();
}

// ID de la sección
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    $_SESSION["flash_error"] = "Sección no válida.";
    header("Location: secciones.php");
    exit();
}

try {
    // Intentar eliminar
    $stmtDel = $connection->prepare("
        DELETE FROM Seccion
        WHERE id_seccion = :id
    ");
    $stmtDel->execute([":id" => $id]);

    if ($stmtDel->rowCount() > 0) {
        $_SESSION["flash_success"] = "Sección eliminada correctamente.";
    } else {
        $_SESSION["flash_error"] = "La sección especificada no existe.";
    }
} catch (PDOException $e) {
    // 23503 = violación de llave foránea (está en uso)
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar la sección porque está siendo utilizada por otros registros (usuarios, facturas, etc.).";
    } else {
        $_SESSION["flash_error"] = "Error al eliminar la sección: " . $e->getMessage();
    }
}

header("Location: secciones.php");
exit();
