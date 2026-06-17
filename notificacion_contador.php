<?php

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/services/NotificacionService.php";

requireLogin();

$user = $_SESSION["user"];
$idUsuario = (int)$user["id_usuario"];

$service = new NotificacionService();

header("Content-Type: application/json; charset=utf-8");

echo json_encode([
    "ok" => true,
    "sin_leer" => $service->contarSinLeer($idUsuario),
]);
