<?php
// * Stored function or procedure has been executed

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/helpers/csrf.php";
require_once __DIR__ . "/helpers/notificaciones.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: clientes.php");
    exit();
}

csrfRequire();

$user = $_SESSION["user"];
$id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Cliente no válido.";
    header("Location: clientes.php");
    exit();
}

/** @var PDO $connection */
$connection = require "./sql/db.php";

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

        notificar("cliente_eliminado", "Cliente eliminado", "Se eliminó el cliente ID #{$id}", [
            "id_usuario_origen" => (int)$user["id_usuario"],
            "rol_origen" => $user["rol"] ?? "",
        ]);
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
