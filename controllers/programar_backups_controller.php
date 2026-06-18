<?php

function calcularSiguienteEjecucion(int $valor, string $unidad): string
{
    $valor = max(1, $valor);

    $unidadDateTime = match ($unidad) {
        "minutes" => "minutes",
        "hours" => "hours",
        "days" => "days",
        "weeks" => "weeks",
        "months" => "months",
        default => "weeks",
    };

    return (new DateTimeImmutable())
        ->modify("+{$valor} {$unidadDateTime}")
        ->format("Y-m-d H:i:s");
}

function obtenerProgramacionDefault(): array
{
    return [
        "enabled" => true,
        "type" => "full",
        "interval_value" => 1,
        "interval_unit" => "weeks",
        "last_run_at" => null,
        "next_run_at" => calcularSiguienteEjecucion(1, "weeks"),
        "updated_at" => date("Y-m-d H:i:s"),
    ];
}

function asegurarArchivoProgramacionBackup(string $scheduleDir, string $scheduleFile): void
{
    if (!is_dir($scheduleDir)) {
        mkdir($scheduleDir, 0775, true);
    }

    if (!is_file($scheduleFile)) {
        file_put_contents(
            $scheduleFile,
            json_encode(obtenerProgramacionDefault(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
}

function asegurarArchivoHistorialMantenimiento(string $historyFile): void
{
    if (!is_file($historyFile)) {
        file_put_contents($historyFile, "[]", LOCK_EX);
    }
}

function reiniciarHistorialMantenimiento(string $historyFile): void
{
    file_put_contents($historyFile, "[]", LOCK_EX);
}

function leerProgramacionBackup(string $scheduleFile): array
{
    $default = obtenerProgramacionDefault();
    $contenido = file_get_contents($scheduleFile);

    if ($contenido === false || trim($contenido) === "") {
        return $default;
    }

    $data = json_decode($contenido, true);

    if (!is_array($data)) {
        return $default;
    }

    return array_merge($default, $data);
}

function guardarProgramacionBackup(string $scheduleFile, array $data): void
{
    file_put_contents(
        $scheduleFile,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function formatearFechaProgramacion(?string $fecha): string
{
    if (!$fecha) {
        return "No disponible";
    }

    $timestamp = strtotime($fecha);

    if ($timestamp === false) {
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

function obtenerDescripcionTipoBackup(string $tipo): string
{
    return match ($tipo) {
        "full" => "Copia toda la base de datos. Es la opción más segura para restaurar el sistema completo.",
        "diff" => "Copia solo los datos principales que cambian con frecuencia, como productos, clientes, ventas y usuarios.",
        "both" => "Genera un respaldo completo y también un respaldo rápido separado de los datos importantes.",
        "maintenance" => "Ejecuta el respaldo completo, el respaldo rápido y guarda registros del sistema.",
        default => "Genera respaldos automáticos según la configuración seleccionada.",
    };
}

function obtenerTextoFrecuencia(int $valor, string $unidad, array $unidadesIntervalo): string
{
    if ($valor === 1) {
        $singulares = [
            "minutes" => "minuto",
            "hours" => "hora",
            "days" => "día",
            "weeks" => "semana",
            "months" => "mes",
        ];

        return "Cada 1 " . ($singulares[$unidad] ?? "semana");
    }

    $nombreUnidad = strtolower($unidadesIntervalo[$unidad] ?? "semanas");

    return "Cada {$valor} {$nombreUnidad}";
}

function procesarAccionGuardarProgramacion(
    string $scheduleFile,
    array $schedule,
    array $tiposBackup,
    array $unidadesIntervalo
): array {
    $enabled = ($_POST["enabled"] ?? "0") === "1";
    $type = trim($_POST["type"] ?? "full");
    $intervalValue = (int)($_POST["interval_value"] ?? 1);
    $intervalUnit = trim($_POST["interval_unit"] ?? "weeks");

    if (!array_key_exists($type, $tiposBackup)) {
        return ["error" => "Debe seleccionar un tipo de backup válido.", "success" => null, "schedule" => $schedule];
    }

    if (!array_key_exists($intervalUnit, $unidadesIntervalo)) {
        return ["error" => "Debe seleccionar una unidad de tiempo válida.", "success" => null, "schedule" => $schedule];
    }

    if ($intervalValue < 1) {
        return ["error" => "El intervalo debe ser mayor o igual a 1.", "success" => null, "schedule" => $schedule];
    }

    if ($intervalValue > 999) {
        return ["error" => "El intervalo no puede ser mayor a 999.", "success" => null, "schedule" => $schedule];
    }

    $schedule = [
        "enabled" => $enabled,
        "type" => $type,
        "interval_value" => $intervalValue,
        "interval_unit" => $intervalUnit,
        "last_run_at" => $schedule["last_run_at"] ?? null,
        "next_run_at" => $enabled
            ? calcularSiguienteEjecucion($intervalValue, $intervalUnit)
            : null,
        "updated_at" => date("Y-m-d H:i:s"),
    ];

    guardarProgramacionBackup($scheduleFile, $schedule);

    $success = $enabled
        ? "La programación de backups fue guardada correctamente."
        : "La programación automática de backups fue pausada.";

    return ["error" => null, "success" => $success, "schedule" => $schedule];
}

function procesarAccionRestablecerProgramacion(string $scheduleFile): array
{
    $schedule = obtenerProgramacionDefault();

    guardarProgramacionBackup($scheduleFile, $schedule);

    return [
        "error" => null,
        "success" => "La programación fue restablecida a backup completo cada semana.",
        "schedule" => $schedule,
    ];
}

function procesarAccionReiniciarHistorial(string $historyFile): array
{
    reiniciarHistorialMantenimiento($historyFile);

    return [
        "error" => null,
        "success" => "El historial de mantenimiento fue reiniciado correctamente.",
    ];
}

function obtenerDatosProgramarBackups(): array
{
    $user = $_SESSION["user"];

    $scheduleDir = __DIR__ . "/../storage/system";
    $scheduleFile = $scheduleDir . "/backup_schedule.json";
    $historyFile = $scheduleDir . "/maintenance_history.json";

    $error = null;
    $success = null;

    $tiposBackup = [
        "full" => "Respaldo completo",
        "diff" => "Respaldo rápido de datos importantes",
        "both" => "Completo + datos importantes",
        "maintenance" => "Mantenimiento completo",
    ];

    $unidadesIntervalo = [
        "minutes" => "Minutos",
        "hours" => "Horas",
        "days" => "Días",
        "weeks" => "Semanas",
        "months" => "Meses",
    ];

    asegurarArchivoProgramacionBackup($scheduleDir, $scheduleFile);
    asegurarArchivoHistorialMantenimiento($historyFile);

    $schedule = leerProgramacionBackup($scheduleFile);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $action = $_POST["action"] ?? "";

        if ($action === "save_schedule") {
            $resultado = procesarAccionGuardarProgramacion(
                $scheduleFile,
                $schedule,
                $tiposBackup,
                $unidadesIntervalo
            );
            $error = $resultado["error"];
            $success = $resultado["success"];
            $schedule = $resultado["schedule"];
        }

        if ($action === "reset_schedule") {
            $resultado = procesarAccionRestablecerProgramacion($scheduleFile);
            $error = $resultado["error"];
            $success = $resultado["success"];
            $schedule = $resultado["schedule"];
        }

        if ($action === "reset_history") {
            $resultado = procesarAccionReiniciarHistorial($historyFile);
            $error = $resultado["error"];
            $success = $resultado["success"];
        }
    }

    $schedule = leerProgramacionBackup($scheduleFile);

    $enabled = (bool)($schedule["enabled"] ?? true);
    $type = (string)($schedule["type"] ?? "full");
    $intervalValue = (int)($schedule["interval_value"] ?? 1);
    $intervalUnit = (string)($schedule["interval_unit"] ?? "weeks");
    $lastRunAt = $schedule["last_run_at"] ?? null;
    $nextRunAt = $schedule["next_run_at"] ?? null;
    $updatedAt = $schedule["updated_at"] ?? null;

    $typeLabel = $tiposBackup[$type] ?? "Backup completo";
    $frequencyLabel = obtenerTextoFrecuencia($intervalValue, $intervalUnit, $unidadesIntervalo);
    $typeDescription = obtenerDescripcionTipoBackup($type);

    return [
        "user" => $user,
        "error" => $error,
        "success" => $success,
        "tiposBackup" => $tiposBackup,
        "unidadesIntervalo" => $unidadesIntervalo,
        "enabled" => $enabled,
        "type" => $type,
        "intervalValue" => $intervalValue,
        "intervalUnit" => $intervalUnit,
        "lastRunAt" => $lastRunAt,
        "nextRunAt" => $nextRunAt,
        "updatedAt" => $updatedAt,
        "typeLabel" => $typeLabel,
        "frequencyLabel" => $frequencyLabel,
        "typeDescription" => $typeDescription,
    ];
}
