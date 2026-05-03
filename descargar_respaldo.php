<?php

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/services/BackupService.php";

requireAdmin();

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

$backupService = new BackupService(
    $connection,
    __DIR__ . "/backups"
);

$archivo = basename($_GET["archivo"] ?? "");
$ruta = $backupService->obtenerRutaRespaldo($archivo);

if ($ruta === null || !is_file($ruta)) {
    http_response_code(404);
    exit("Archivo no encontrado.");
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
header("Content-Length: " . filesize($ruta));
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Expires: 0");

readfile($ruta);
exit;
