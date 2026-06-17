<?php
// * Stored function or procedure has been executed

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/helpers/csrf.php";
require_once __DIR__ . "/helpers/notificaciones.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: trabajadores.php");
    exit();
}

csrfRequire();

$user = $_SESSION["user"];

if (($user["rol"] ?? "") !== "Administrador") {
    $_SESSION["flash_error"] = "No tiene permiso para eliminar trabajadores.";
    header("Location: trabajadores.php");
    exit();
}

$id = isset($_POST["id"]) ? (int) $_POST["id"] : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "Trabajador no válido.";
    header("Location: trabajadores.php");
    exit();
}

$connection = require "./sql/db.php";

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

        notificar("usuario_eliminado", "Usuario eliminado", "Se eliminó el usuario ID #{$id}", [
            "id_usuario_origen" => (int)$user["id_usuario"],
            "rol_origen" => $user["rol"] ?? "",
        ]);
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

header("Location: trabajadores.php");
exit();
