<?php

session_start();

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

$walDir = __DIR__ . "/database/wal_archive";
$file = basename(trim($_GET["file"] ?? ""));

if ($file === "") {
    http_response_code(400);
    exit("Solicitud inválida.");
}

$filepath = $walDir . "/" . $file;

$realDirectory = realpath($walDir);
$realFile = realpath($filepath);

if (
    $realDirectory === false ||
    $realFile === false ||
    !str_starts_with($realFile, $realDirectory) ||
    !is_file($realFile)
) {
    http_response_code(404);
    exit("Archivo no encontrado.");
}

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($realFile));

readfile($realFile);
exit;
