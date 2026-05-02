<?php

require_once __DIR__ . "/../services/BackupService.php";

function obtenerDatosRespaldosBd(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $backupService = new BackupService(
        $connection,
        __DIR__ . "/../backups"
    );

    $error = null;
    $success = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $action = $_POST["action"] ?? "";

        if ($action === "backup") {
            $resultado = procesarAccionBackup($backupService, $user, $_POST);
            $error = $resultado["error"];
            $success = $resultado["success"];
        }

        if ($action === "restore") {
            $resultado = procesarAccionRestore($backupService, $_POST);
            $error = $resultado["error"];
            $success = $resultado["success"];
        }
    }

    return [
        "user" => $user,
        "error" => $error,
        "success" => $success,
        "archivos" => $backupService->listarRespaldos(),
    ];
}

function procesarAccionBackup(
    BackupService $backupService,
    array $user,
    array $post
): array {
    $nombrePersonalizado = trim($post["nombre_archivo"] ?? "");
    $mensajePersonalizado = trim($post["mensaje"] ?? "");

    $nombrePersonalizado = $nombrePersonalizado !== ""
        ? $nombrePersonalizado
        : null;

    $mensajePersonalizado = $mensajePersonalizado !== ""
        ? $mensajePersonalizado
        : null;

    $idUsuario = isset($user["id_usuario"])
        ? (int)$user["id_usuario"]
        : null;

    $resultado = $backupService->generarRespaldoManual(
        $idUsuario,
        $nombrePersonalizado,
        $mensajePersonalizado
    );

    return [
        "error" => $resultado["success"] ? null : $resultado["message"],
        "success" => $resultado["success"] ? $resultado["message"] : null,
    ];
}

function procesarAccionRestore(
    BackupService $backupService,
    array $post
): array {
    $archivo = basename($post["archivo"] ?? "");

    if ($archivo === "") {
        return [
            "error" => "Debe seleccionar un archivo de respaldo.",
            "success" => null,
        ];
    }

    $confirmacion = trim($post["confirmacion_restore"] ?? "");

    if ($confirmacion !== "RESTAURAR") {
        return [
            "error" => "Para restaurar la base de datos debe escribir RESTAURAR como confirmación.",
            "success" => null,
        ];
    }

    $resultado = $backupService->restaurarDesdeArchivo($archivo);

    return [
        "error" => $resultado["success"] ? null : $resultado["message"],
        "success" => $resultado["success"] ? $resultado["message"] : null,
    ];
}
