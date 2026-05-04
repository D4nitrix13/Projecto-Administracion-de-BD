<?php

session_start();

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

$allowedSources = [
    "database" => __DIR__ . "/database/logs",
    "backup" => __DIR__ . "/backups/logs",
];

$source = trim($_GET["source"] ?? "");
$file = basename(trim($_GET["file"] ?? ""));

if ($source === "" || $file === "" || !isset($allowedSources[$source])) {
    http_response_code(400);
    exit("Solicitud inválida.");
}

$directory = $allowedSources[$source];
$filepath = $directory . "/" . $file;

$realDirectory = realpath($directory);
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

$allowedExtensions = [
    ".log",
    ".txt",
    ".json",
    ".tar.gz",
];

$isAllowed = false;
$lowerFile = strtolower($file);

foreach ($allowedExtensions as $extension) {
    if (str_ends_with($lowerFile, $extension)) {
        $isAllowed = true;
        break;
    }
}

if (!$isAllowed) {
    http_response_code(403);
    exit("Tipo de archivo no permitido.");
}

$contentType = "application/octet-stream";

if (str_ends_with($lowerFile, ".log") || str_ends_with($lowerFile, ".txt")) {
    $contentType = "text/plain";
}

if (str_ends_with($lowerFile, ".json")) {
    $contentType = "application/json";
}

if (str_ends_with($lowerFile, ".tar.gz")) {
    $contentType = "application/gzip";
}

header("Content-Type: {$contentType}");
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($realFile));

readfile($realFile);
exit;
