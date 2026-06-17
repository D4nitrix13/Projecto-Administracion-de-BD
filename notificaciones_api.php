<?php

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/services/NotificacionService.php";

requireLogin();

$user = $_SESSION["user"];
$idUsuario = (int)$user["id_usuario"];
$esAdmin = ($user["rol"] ?? "") === "Administrador";

$service = new NotificacionService();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    switch ($action) {
        case "marcar_leida":
            $id = trim($_POST["id"] ?? "");
            if ($id === "") {
                http_response_code(400);
                echo json_encode(["ok" => false, "error" => "ID requerido"]);
                exit();
            }
            $ok = $service->marcarLeida($id, $idUsuario);
            echo json_encode(["ok" => $ok]);
            exit();

        case "marcar_todas":
            $marcadas = $service->marcarTodasLeidas($idUsuario);
            echo json_encode(["ok" => true, "marcadas" => $marcadas]);
            exit();

        case "eliminar":
            $id = trim($_POST["id"] ?? "");
            if ($id === "") {
                http_response_code(400);
                echo json_encode(["ok" => false, "error" => "ID requerido"]);
                exit();
            }
            $ok = $service->eliminar($id, $idUsuario, $esAdmin);
            echo json_encode(["ok" => $ok]);
            exit();

        case "limpiar":
            if (!$esAdmin) {
                http_response_code(403);
                echo json_encode(["ok" => false, "error" => "Solo administradores"]);
                exit();
            }
            $ok = $service->limpiarHistorial();
            echo json_encode(["ok" => $ok]);
            exit();

        default:
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "Acción no válida"]);
            exit();
    }
}

$notificaciones = $service->obtenerParaUsuario($idUsuario);
$sinLeer = $service->contarSinLeer($idUsuario);

echo json_encode([
    "ok" => true,
    "notificaciones" => $notificaciones,
    "sin_leer" => $sinLeer,
]);
