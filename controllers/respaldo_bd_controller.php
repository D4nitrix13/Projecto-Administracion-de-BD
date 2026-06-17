<?php

require_once __DIR__ . "/../services/BackupService.php";
require_once __DIR__ . "/../helpers/notificaciones.php";

function obtenerDatosRespaldosBd(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $backupService = new BackupService(
        $connection,
        __DIR__ . "/../backups"
    );

    $backupService->eliminarRespaldosProgramados();

    $error = null;
    $success = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $action = $_POST["action"] ?? "";

        switch ($action) {
            case "backup":
                $resultado = procesarAccionBackup($backupService, $user, $_POST);
                $error = $resultado["error"];
                $success = $resultado["success"];
                break;

            case "restore":
                $resultado = procesarAccionRestore($backupService, $_POST);
                $error = $resultado["error"];
                $success = $resultado["success"];
                break;

            case "schedule_delete":
                $resultado = procesarAccionProgramarEliminacion($backupService, $_POST);
                $error = $resultado["error"];
                $success = $resultado["success"];
                break;

            case "cancel_delete":
                $resultado = procesarAccionCancelarEliminacion($backupService, $_POST);
                $error = $resultado["error"];
                $success = $resultado["success"];
                break;
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

    if ($resultado["success"]) {
        notificar("backup_manual", "Backup manual", $resultado["message"], [
            "id_usuario_origen" => $idUsuario,
            "rol_origen" => $user["rol"] ?? "",
        ]);
    }

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

    if ($resultado["success"]) {
        notificar("backup_restaurado", "BD restaurada", "La base de datos fue restaurada desde: {$archivo}", [
            "rol_origen" => "Administrador",
            "metadata" => ["archivo" => $archivo],
        ]);
    }

    return [
        "error" => $resultado["success"] ? null : $resultado["message"],
        "success" => $resultado["success"] ? $resultado["message"] : null,
    ];
}

function procesarAccionProgramarEliminacion(
    BackupService $backupService,
    array $post
): array {
    $archivo = basename($post["archivo"] ?? "");

    if ($archivo === "") {
        return [
            "error" => "Debe seleccionar un archivo válido para programar su eliminación.",
            "success" => null,
        ];
    }

    $resultado = $backupService->programarEliminacion($archivo, 24);

    return [
        "error" => $resultado["success"] ? null : $resultado["message"],
        "success" => $resultado["success"] ? $resultado["message"] : null,
    ];
}

function procesarAccionCancelarEliminacion(
    BackupService $backupService,
    array $post
): array {
    $archivo = basename($post["archivo"] ?? "");

    if ($archivo === "") {
        return [
            "error" => "Debe indicar el archivo cuya eliminación desea cancelar.",
            "success" => null,
        ];
    }

    $resultado = $backupService->cancelarEliminacion($archivo);

    return [
        "error" => $resultado["success"] ? null : $resultado["message"],
        "success" => $resultado["success"] ? $resultado["message"] : null,
    ];
}
