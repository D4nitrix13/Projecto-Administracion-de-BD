<?php

declare(strict_types=1);

/**
 * Ejecutor automático de backups.
 *
 * Este archivo debe ejecutarse periódicamente desde el servidor.
 * Lee storage/system/backup_schedule.json y ejecuta el respaldo
 * solamente cuando la fecha actual ya alcanzó next_run_at.
 */

date_default_timezone_set("America/Managua");

$projectRoot = dirname(__DIR__);

$scheduleFile = $projectRoot . "/storage/system/backup_schedule.json";
$historyFile = $projectRoot . "/storage/system/maintenance_history.json";
$lockFile = $projectRoot . "/storage/system/cron_backup.lock";
$logDir = $projectRoot . "/backups/logs";

$allowedTypes = [
    "full",
    "diff",
    "both",
    "maintenance",
];

$allowedUnits = [
    "minutes",
    "hours",
    "days",
    "weeks",
    "months",
];

function escribirSalida(string $mensaje): void
{
    echo "[" . date("Y-m-d H:i:s") . "] " . $mensaje . PHP_EOL;
}

function asegurarDirectorio(string $directorio): void
{
    if (!is_dir($directorio)) {
        mkdir($directorio, 0775, true);
    }
}

function leerJson(string $archivo, array $default): array
{
    if (!is_file($archivo)) {
        return $default;
    }

    $contenido = file_get_contents($archivo);

    if ($contenido === false || trim($contenido) === "") {
        return $default;
    }

    $data = json_decode($contenido, true);

    if (!is_array($data)) {
        return $default;
    }

    return array_merge($default, $data);
}

function guardarJson(string $archivo, array $data): void
{
    file_put_contents(
        $archivo,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

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

function ejecutarComando(string $comando, string $projectRoot): array
{
    chdir($projectRoot);

    $descriptorSpec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"],
    ];

    $process = proc_open($comando, $descriptorSpec, $pipes, $projectRoot);

    if (!is_resource($process)) {
        return [
            "success" => false,
            "exit_code" => 1,
            "output" => "",
            "error" => "No se pudo iniciar el proceso.",
        ];
    }

    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    return [
        "success" => $exitCode === 0,
        "exit_code" => $exitCode,
        "output" => trim((string)$output),
        "error" => trim((string)$error),
    ];
}

function registrarHistorial(string $historyFile, array $registro): void
{
    $historial = [];

    if (is_file($historyFile)) {
        $contenido = file_get_contents($historyFile);
        $data = json_decode((string)$contenido, true);

        if (is_array($data)) {
            $historial = $data;
        }
    }

    array_unshift($historial, $registro);

    $historial = array_slice($historial, 0, 100);

    guardarJson($historyFile, $historial);
}

function obtenerComandosPorTipo(string $tipo, string $projectRoot): array
{
    $scripts = [
        "full" => $projectRoot . "/scripts/backup_full.sh",
        "diff" => $projectRoot . "/scripts/backup_diff.sh",
        "logs" => $projectRoot . "/scripts/backup_logs.sh",
        "maintenance" => $projectRoot . "/scripts/mantenimiento_bd.sh",
    ];

    return match ($tipo) {
        "full" => [
            "bash " . escapeshellarg($scripts["full"]),
        ],
        "diff" => [
            "bash " . escapeshellarg($scripts["diff"]),
        ],
        "both" => [
            "bash " . escapeshellarg($scripts["full"]),
            "bash " . escapeshellarg($scripts["diff"]),
        ],
        "maintenance" => [
            "bash " . escapeshellarg($scripts["maintenance"]),
        ],
        default => [],
    };
}

asegurarDirectorio($projectRoot . "/storage/system");
asegurarDirectorio($logDir);

$logFile = $logDir . "/cron_backup_" . date("Y-m-d") . ".log";

ob_start();

escribirSalida("Revisando programación de backups.");

$lockHandle = fopen($lockFile, "c");

if ($lockHandle === false) {
    escribirSalida("No se pudo crear el archivo de bloqueo.");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    exit(1);
}

if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
    escribirSalida("Ya existe una ejecución en proceso. Se cancela esta revisión.");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    exit(0);
}

$defaultSchedule = [
    "enabled" => true,
    "type" => "full",
    "interval_value" => 1,
    "interval_unit" => "weeks",
    "last_run_at" => null,
    "next_run_at" => calcularSiguienteEjecucion(1, "weeks"),
    "updated_at" => date("Y-m-d H:i:s"),
];

$schedule = leerJson($scheduleFile, $defaultSchedule);

$enabled = (bool)($schedule["enabled"] ?? true);
$type = (string)($schedule["type"] ?? "full");
$intervalValue = (int)($schedule["interval_value"] ?? 1);
$intervalUnit = (string)($schedule["interval_unit"] ?? "weeks");
$nextRunAt = $schedule["next_run_at"] ?? null;

if (!$enabled) {
    escribirSalida("La programación automática está desactivada.");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(0);
}

if (!in_array($type, $allowedTypes, true)) {
    escribirSalida("Tipo de backup inválido: {$type}");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(1);
}

if (!in_array($intervalUnit, $allowedUnits, true)) {
    escribirSalida("Unidad de tiempo inválida: {$intervalUnit}");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(1);
}

if ($intervalValue < 1) {
    $intervalValue = 1;
}

if (!$nextRunAt) {
    $schedule["next_run_at"] = calcularSiguienteEjecucion($intervalValue, $intervalUnit);
    guardarJson($scheduleFile, $schedule);

    escribirSalida("No existía próxima ejecución. Se calculó una nueva fecha.");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(0);
}

$now = new DateTimeImmutable();
$nextRun = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", (string)$nextRunAt);

if (!$nextRun) {
    $schedule["next_run_at"] = calcularSiguienteEjecucion($intervalValue, $intervalUnit);
    guardarJson($scheduleFile, $schedule);

    escribirSalida("La fecha next_run_at era inválida. Se recalculó.");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(0);
}

if ($now < $nextRun) {
    escribirSalida("Aún no toca ejecutar backup. Próxima ejecución: " . $nextRun->format("Y-m-d H:i:s"));
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(0);
}

$comandos = obtenerComandosPorTipo($type, $projectRoot);

if (empty($comandos)) {
    escribirSalida("No hay comandos configurados para el tipo: {$type}");
    file_put_contents($logFile, ob_get_clean(), FILE_APPEND);
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    exit(1);
}

escribirSalida("Iniciando backup automático. Tipo: {$type}");

$resultados = [];
$todoCorrecto = true;

foreach ($comandos as $comando) {
    escribirSalida("Ejecutando: {$comando}");

    $resultado = ejecutarComando($comando, $projectRoot);
    $resultados[] = [
        "command" => $comando,
        "success" => $resultado["success"],
        "exit_code" => $resultado["exit_code"],
        "output" => $resultado["output"],
        "error" => $resultado["error"],
    ];

    if (!$resultado["success"]) {
        $todoCorrecto = false;
        escribirSalida("Error al ejecutar comando. Código: " . $resultado["exit_code"]);

        if ($resultado["error"] !== "") {
            escribirSalida("Detalle: " . $resultado["error"]);
        }

        break;
    }

    escribirSalida("Comando ejecutado correctamente.");
}

$nowText = date("Y-m-d H:i:s");

if ($todoCorrecto) {
    $schedule["last_run_at"] = $nowText;
    $schedule["next_run_at"] = calcularSiguienteEjecucion($intervalValue, $intervalUnit);
    $schedule["updated_at"] = $schedule["updated_at"] ?? $nowText;

    guardarJson($scheduleFile, $schedule);

    escribirSalida("Backup automático finalizado correctamente.");
    escribirSalida("Nueva próxima ejecución: " . $schedule["next_run_at"]);
} else {
    escribirSalida("Backup automático finalizado con errores.");
}

registrarHistorial($historyFile, [
    "type" => $type,
    "success" => $todoCorrecto,
    "executed_at" => $nowText,
    "next_run_at" => $schedule["next_run_at"] ?? null,
    "results" => $resultados,
]);

file_put_contents($logFile, ob_get_clean(), FILE_APPEND);

flock($lockHandle, LOCK_UN);
fclose($lockHandle);

exit($todoCorrecto ? 0 : 1);
