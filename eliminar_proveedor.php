<?php
// * Stored function or procedure has been executed

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/helpers/csrf.php";
require_once __DIR__ . "/helpers/notificaciones.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: proveedores.php");
    exit();
}

csrfRequire();

$user = $_SESSION["user"];
$id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Proveedor no válido.";
    header("Location: proveedores.php");
    exit();
}

/** @var PDO $connection */
$connection = require "./sql/db.php";

try {
    $stmtDel = $connection->prepare("
        SELECT eliminar_proveedor_sistema(:id_proveedor) AS eliminado
    ");

    $stmtDel->execute([
        ":id_proveedor" => $id,
    ]);

    $resultado = $stmtDel->fetch(PDO::FETCH_ASSOC);

    if (!empty($resultado["eliminado"])) {
        $_SESSION["flash_success"] = "Proveedor eliminado correctamente.";

        notificar("proveedor_eliminado", "Proveedor eliminado", "Se eliminó el proveedor ID #{$id}", [
            "id_usuario_origen" => (int)$user["id_usuario"],
            "rol_origen" => $user["rol"] ?? "",
        ]);
    } else {
        $_SESSION["flash_error"] = "El proveedor especificado no existe.";
    }
} catch (PDOException $e) {
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar el proveedor porque está asociado a compras o productos.";
    } else {
        $_SESSION["flash_error"] = "Error al eliminar el proveedor: " . $e->getMessage();
    }
}

header("Location: proveedores.php");
exit();
