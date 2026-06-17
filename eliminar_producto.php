<?php
// * Stored function or procedure has been executed

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/helpers/csrf.php";
require_once __DIR__ . "/helpers/notificaciones.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: productos.php");
    exit();
}

csrfRequire();

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

if ($idRol === 3) {
    $_SESSION["flash_error"] = "No tienes permisos para eliminar productos.";
    header("Location: productos.php");
    exit();
}

$idProducto = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

if ($idProducto <= 0) {
    $_SESSION["flash_error"] = "Producto no válido.";
    header("Location: productos.php");
    exit();
}

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

try {
    $stmt = $connection->prepare("
        SELECT eliminar_producto_sistema(:id_producto) AS eliminado
    ");

    $stmt->execute([
        ":id_producto" => $idProducto,
    ]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($resultado["eliminado"])) {
        $_SESSION["flash_success"] = "Producto eliminado correctamente.";

        notificar("producto_eliminado", "Producto eliminado", "Se eliminó el producto ID #{$idProducto}", [
            "id_usuario_origen" => (int)$user["id_usuario"],
            "rol_origen" => $user["rol"] ?? "",
        ]);
    } else {
        $_SESSION["flash_error"] = "El producto no existe o ya fue eliminado.";
    }
} catch (PDOException $e) {
    if ($e->getCode() === "23503") {
        $_SESSION["flash_error"] = "No se puede eliminar el producto porque tiene facturas asociadas.";
    } else {
        $_SESSION["flash_error"] = "No se pudo eliminar el producto: " . $e->getMessage();
    }
}

header("Location: productos.php");
exit();
