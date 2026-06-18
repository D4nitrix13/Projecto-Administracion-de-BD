<?php

require_once __DIR__ . "/../helpers/notificaciones.php";

function mantenimientoAsegurarDirectorio(string $directorio): void
{
    if (!is_dir($directorio)) {
        mkdir($directorio, 0775, true);
    }
}

function mantenimientoFormatearTamano(int $bytes): string
{
    if ($bytes >= 1024 * 1024 * 1024) {
        return number_format($bytes / (1024 * 1024 * 1024), 2) . " GB";
    }

    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 2) . " MB";
    }

    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . " KB";
    }

    return $bytes . " B";
}

function mantenimientoFormatearFecha(?int $timestamp): string
{
    if (!$timestamp) {
        return "No disponible";
    }

    $dias = [
        "domingo",
        "lunes",
        "martes",
        "miércoles",
        "jueves",
        "viernes",
        "sábado",
    ];

    $meses = [
        "enero",
        "febrero",
        "marzo",
        "abril",
        "mayo",
        "junio",
        "julio",
        "agosto",
        "septiembre",
        "octubre",
        "noviembre",
        "diciembre",
    ];

    $diaSemana = $dias[(int)date("w", $timestamp)];
    $dia = (int)date("j", $timestamp);
    $mes = $meses[(int)date("n", $timestamp) - 1];
    $anio = date("Y", $timestamp);
    $hora = date("h:i:s", $timestamp);
    $periodo = strtolower(date("A", $timestamp)) === "am" ? "a.m." : "p.m.";

    return "{$diaSemana} {$dia} de {$mes} {$anio} - {$hora} {$periodo}";
}

function mantenimientoObtenerArchivos(string $directorio, string $patron = "*"): array
{
    mantenimientoAsegurarDirectorio($directorio);

    $archivos = glob($directorio . "/" . $patron) ?: [];

    return array_values(
        array_filter(
            $archivos,
            fn(string $archivo): bool => is_file($archivo)
        )
    );
}

function mantenimientoObtenerResumenDirectorio(string $directorio, string $patron = "*"): array
{
    $archivos = mantenimientoObtenerArchivos($directorio, $patron);

    $totalTamano = array_sum(
        array_map(
            fn(string $archivo): int => (int)filesize($archivo),
            $archivos
        )
    );

    usort(
        $archivos,
        fn(string $a, string $b): int => (filemtime($b) ?: 0) <=> (filemtime($a) ?: 0)
    );

    $ultimoArchivo = $archivos[0] ?? null;

    return [
        "total" => count($archivos),
        "tamano" => $totalTamano,
        "ultimo_archivo" => $ultimoArchivo ? basename($ultimoArchivo) : "No disponible",
        "ultima_fecha" => $ultimoArchivo ? mantenimientoFormatearFecha(filemtime($ultimoArchivo) ?: null) : "No disponible",
    ];
}

function mantenimientoEjecutarScript(string $script, string $projectRoot): array
{
    if (!is_file($script)) {
        return [
            "success" => false,
            "message" => "No se encontró el script: " . basename($script),
        ];
    }

    if (!is_executable($script)) {
        @chmod($script, 0775);
    }

    $command = sprintf(
        "cd %s && bash %s 2>&1",
        escapeshellarg($projectRoot),
        escapeshellarg($script)
    );

    $output = [];
    $returnCode = 0;

    exec($command, $output, $returnCode);

    $message = trim(implode("\n", $output));

    return [
        "success" => $returnCode === 0,
        "message" => $message,
    ];
}

function procesarAccionMantenimiento(string $action, array $scripts, string $projectRoot): array
{
    $scriptSeleccionado = match ($action) {
        "backup_full" => $scripts["full"],
        "backup_diff" => $scripts["diff"],
        "backup_logs" => $scripts["logs"],
        "mantenimiento_completo" => $scripts["mantenimiento"],
        default => null,
    };

    if ($scriptSeleccionado === null) {
        return [
            "error" => "Acción no válida.",
            "errorDetail" => "La acción solicitada no corresponde a una tarea de mantenimiento disponible.",
            "success" => null,
            "successDetail" => null,
        ];
    }

    $resultado = mantenimientoEjecutarScript($scriptSeleccionado, $projectRoot);
    $mensaje = trim((string)($resultado["message"] ?? ""));

    if ($resultado["success"]) {
        notificar("mantenimiento_bd", "Mantenimiento BD", "Se ejecutó: {$action}", [
            "id_usuario_origen" => (int)$_SESSION["user"]["id_usuario"],
            "rol_origen" => $_SESSION["user"]["rol"] ?? "",
            "metadata" => ["accion" => $action],
        ]);

        $detail = $mensaje === "" || str_contains($mensaje, "El script terminó sin devolver salida")
            ? "La tarea finalizó correctamente. Puede revisar la carpeta backups/logs para ver el detalle del proceso."
            : $mensaje;

        return [
            "error" => null,
            "errorDetail" => null,
            "success" => "ok",
            "successDetail" => $detail,
        ];
    }

    return [
        "error" => "error",
        "errorDetail" => $mensaje !== ""
            ? $mensaje
            : "No se pudo completar la tarea. Revise backups/logs para obtener más información.",
        "success" => null,
        "successDetail" => null,
    ];
}

function obtenerDatosMantenimientoBd(): array
{
    $user = $_SESSION["user"];

    $projectRoot = __DIR__ . "/..";

    $scripts = [
        "full" => $projectRoot . "/scripts/backup_full.sh",
        "diff" => $projectRoot . "/scripts/backup_diff.sh",
        "logs" => $projectRoot . "/scripts/backup_logs.sh",
        "mantenimiento" => $projectRoot . "/scripts/mantenimiento_bd.sh",
    ];

    $dirs = [
        "full" => $projectRoot . "/backups/full",
        "diff" => $projectRoot . "/backups/diff",
        "logs" => $projectRoot . "/backups/logs",
        "database_logs" => $projectRoot . "/database/logs",
        "wal" => $projectRoot . "/database/wal_archive",
    ];

    $error = null;
    $errorDetail = null;
    $success = null;
    $successDetail = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $action = $_POST["action"] ?? "";
        $resultado = procesarAccionMantenimiento($action, $scripts, $projectRoot);

        $error = $resultado["error"];
        $errorDetail = $resultado["errorDetail"];
        $success = $resultado["success"];
        $successDetail = $resultado["successDetail"];
    }

    return [
        "user" => $user,
        "error" => $error,
        "errorDetail" => $errorDetail,
        "success" => $success,
        "successDetail" => $successDetail,
        "resumenFull" => mantenimientoObtenerResumenDirectorio($dirs["full"], "*.sql"),
        "resumenDiff" => mantenimientoObtenerResumenDirectorio($dirs["diff"], "*.sql"),
        "resumenBackupLogs" => mantenimientoObtenerResumenDirectorio($dirs["logs"], "*"),
        "resumenDatabaseLogs" => mantenimientoObtenerResumenDirectorio($dirs["database_logs"], "*"),
        "resumenWal" => mantenimientoObtenerResumenDirectorio($dirs["wal"], "*"),
    ];
}
