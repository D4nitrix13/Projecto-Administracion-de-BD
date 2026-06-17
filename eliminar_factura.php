<?php
// * Stored function or procedure has been executed

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/helpers/csrf.php";
require_once __DIR__ . "/helpers/notificaciones.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: facturas.php");
    exit();
}

csrfRequire();

$user = $_SESSION["user"];
$id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Factura no válida.";
    header("Location: facturas.php");
    exit();
}

/** @var PDO $connection */
$connection = require "./sql/db.php";

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

        notificar("factura_cancelada", "Factura cancelada", "Se eliminó la factura ID #{$id} y se restauró el stock", [
            "id_usuario_origen" => (int)$user["id_usuario"],
            "rol_origen" => $user["rol"] ?? "",
            "metadata" => ["factura_id" => $id],
        ]);
    } else {
        $_SESSION["flash_error"] = "No se pudo eliminar la factura.";
    }
} catch (PDOException $e) {
    $_SESSION["flash_error"] = "No se pudo eliminar la factura: " . $e->getMessage();
}

header("Location: facturas.php");
exit();
