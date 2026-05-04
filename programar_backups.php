<?php

session_start();

$pageTitle = "Programar backups - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

$user = $_SESSION["user"];

$scheduleDir = __DIR__ . "/storage/system";
$scheduleFile = $scheduleDir . "/backup_schedule.json";

$error = null;
$success = null;

$tiposBackup = [
    "full" => "Backup completo",
    "diff" => "Backup diferencial académico",
    "both" => "Backup completo + diferencial",
    "maintenance" => "Plan completo de mantenimiento",
];

$unidadesIntervalo = [
    "seconds" => "Segundos",
    "minutes" => "Minutos",
    "hours" => "Horas",
    "days" => "Días",
    "weeks" => "Semanas",
    "months" => "Meses",
];

function asegurarArchivoProgramacionBackup(string $scheduleDir, string $scheduleFile): void
{
    if (!is_dir($scheduleDir)) {
        mkdir($scheduleDir, 0775, true);
    }

    if (!is_file($scheduleFile)) {
        $default = [
            "enabled" => true,
            "type" => "full",
            "interval_value" => 1,
            "interval_unit" => "weeks",
            "last_run_at" => null,
            "next_run_at" => calcularSiguienteEjecucion(1, "weeks"),
            "updated_at" => date("Y-m-d H:i:s"),
        ];

        file_put_contents(
            $scheduleFile,
            json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
}

function leerProgramacionBackup(string $scheduleFile): array
{
    $contenido = file_get_contents($scheduleFile);

    if ($contenido === false || trim($contenido) === "") {
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

    $data = json_decode($contenido, true);

    if (!is_array($data)) {
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

    return array_merge(
        [
            "enabled" => true,
            "type" => "full",
            "interval_value" => 1,
            "interval_unit" => "weeks",
            "last_run_at" => null,
            "next_run_at" => calcularSiguienteEjecucion(1, "weeks"),
            "updated_at" => date("Y-m-d H:i:s"),
        ],
        $data
    );
}

function guardarProgramacionBackup(string $scheduleFile, array $data): void
{
    file_put_contents(
        $scheduleFile,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function calcularSiguienteEjecucion(int $valor, string $unidad): string
{
    $valor = max(1, $valor);

    $unidadDateTime = match ($unidad) {
        "seconds" => "seconds",
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
        "full" => "Genera una copia completa de toda la base de datos.",
        "diff" => "Genera un respaldo académico de las tablas críticas del sistema.",
        "both" => "Ejecuta backup completo y backup diferencial académico.",
        "maintenance" => "Ejecuta el plan completo: backup full, backup diferencial y copia de logs.",
        default => "Genera respaldos automáticos según la configuración seleccionada.",
    };
}

function obtenerTextoFrecuencia(int $valor, string $unidad, array $unidadesIntervalo): string
{
    $nombreUnidad = strtolower($unidadesIntervalo[$unidad] ?? "semanas");

    if ($valor === 1) {
        $singulares = [
            "seconds" => "segundo",
            "minutes" => "minuto",
            "hours" => "hora",
            "days" => "día",
            "weeks" => "semana",
            "months" => "mes",
        ];

        return "Cada 1 " . ($singulares[$unidad] ?? "semana");
    }

    return "Cada {$valor} {$nombreUnidad}";
}

asegurarArchivoProgramacionBackup($scheduleDir, $scheduleFile);

$schedule = leerProgramacionBackup($scheduleFile);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "save_schedule") {
        $enabled = ($_POST["enabled"] ?? "0") === "1";
        $type = trim($_POST["type"] ?? "full");
        $intervalValue = (int)($_POST["interval_value"] ?? 1);
        $intervalUnit = trim($_POST["interval_unit"] ?? "weeks");

        if (!array_key_exists($type, $tiposBackup)) {
            $error = "Debe seleccionar un tipo de backup válido.";
        } elseif (!array_key_exists($intervalUnit, $unidadesIntervalo)) {
            $error = "Debe seleccionar una unidad de tiempo válida.";
        } elseif ($intervalValue < 1) {
            $error = "El intervalo debe ser mayor o igual a 1.";
        } elseif ($intervalValue > 999) {
            $error = "El intervalo no puede ser mayor a 999.";
        } else {
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
                : "La programación automática de backups fue desactivada.";
        }
    }

    if ($action === "reset_schedule") {
        $schedule = [
            "enabled" => true,
            "type" => "full",
            "interval_value" => 1,
            "interval_unit" => "weeks",
            "last_run_at" => $schedule["last_run_at"] ?? null,
            "next_run_at" => calcularSiguienteEjecucion(1, "weeks"),
            "updated_at" => date("Y-m-d H:i:s"),
        ];

        guardarProgramacionBackup($scheduleFile, $schedule);

        $success = "La programación fue restablecida a backup completo cada semana.";
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

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="dashboard-card dashboard-welcome backup-hero">
            <div>
                <p class="dashboard-eyebrow">Sistema</p>

                <h1 class="dashboard-title">Programar backups</h1>

                <p class="dashboard-muted">
                    Configure la frecuencia automática para generar respaldos de la base de datos.
                </p>
            </div>

            <a href="dashboard.php" class="btn-secondary-inline backup-back-btn">
                Volver al panel
            </a>
        </section>

        <section class="dashboard-card backup-page-card backup-page-card-compact">

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= nl2br(htmlspecialchars($error)) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= nl2br(htmlspecialchars($success)) ?>
                </div>
            <?php endif; ?>

            <section class="backup-summary-row">
                <article class="backup-summary-card <?= $enabled ? "backup-summary-card-green" : "backup-summary-card-red" ?>">
                    <span>Estado</span>
                    <strong><?= $enabled ? "Activo" : "Inactivo" ?></strong>
                    <small><?= $enabled ? "La programación automática está habilitada." : "No se ejecutarán backups automáticos." ?></small>
                </article>

                <article class="backup-summary-card">
                    <span>Tipo configurado</span>
                    <strong><?= htmlspecialchars($typeLabel) ?></strong>
                    <small><?= htmlspecialchars($typeDescription) ?></small>
                </article>

                <article class="backup-summary-card">
                    <span>Frecuencia</span>
                    <strong><?= htmlspecialchars($frequencyLabel) ?></strong>
                    <small>Intervalo actual configurado.</small>
                </article>

                <article class="backup-summary-card backup-summary-card-blue">
                    <span>Próxima ejecución</span>
                    <strong><?= $enabled ? "Programada" : "Pausada" ?></strong>
                    <small><?= htmlspecialchars(formatearFechaProgramacion($nextRunAt)) ?></small>
                </article>
            </section>

            <div class="backup-schedule-layout">
                <section class="backup-panel backup-schedule-panel">
                    <div class="backup-panel-header">
                        <span class="backup-panel-badge backup-badge-safe">
                            Automatización
                        </span>

                        <h2>Configurar backup automático</h2>

                        <p>
                            Por defecto, el sistema queda configurado para generar un backup completo cada semana.
                        </p>
                    </div>

                    <form method="POST" class="backup-form backup-schedule-form">
                        <input type="hidden" name="action" value="save_schedule">

                        <div class="form-group backup-toggle-group">
                            <label class="label">Estado de la programación</label>

                            <div class="backup-toggle-options">
                                <label class="backup-radio-card">
                                    <input
                                        type="radio"
                                        name="enabled"
                                        value="1"
                                        <?= $enabled ? "checked" : "" ?>>

                                    <span>
                                        <strong>Activado</strong>
                                        <small>Permite ejecutar backups automáticos.</small>
                                    </span>
                                </label>

                                <label class="backup-radio-card">
                                    <input
                                        type="radio"
                                        name="enabled"
                                        value="0"
                                        <?= !$enabled ? "checked" : "" ?>>

                                    <span>
                                        <strong>Desactivado</strong>
                                        <small>Pausa la programación automática.</small>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="type" class="label">Tipo de backup</label>

                            <select name="type" id="type" class="input" required>
                                <?php foreach ($tiposBackup as $value => $label): ?>
                                    <option
                                        value="<?= htmlspecialchars($value) ?>"
                                        <?= $type === $value ? "selected" : "" ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <p class="dashboard-muted backup-help">
                                El plan completo ejecuta backup full, backup diferencial y copia de logs.
                            </p>
                        </div>

                        <div class="backup-schedule-inline">
                            <div class="form-group">
                                <label for="interval_value" class="label">Cada cuánto</label>

                                <input
                                    type="number"
                                    name="interval_value"
                                    id="interval_value"
                                    class="input"
                                    min="1"
                                    max="999"
                                    value="<?= htmlspecialchars((string)$intervalValue) ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="interval_unit" class="label">Unidad de tiempo</label>

                                <select name="interval_unit" id="interval_unit" class="input" required>
                                    <?php foreach ($unidadesIntervalo as $value => $label): ?>
                                        <option
                                            value="<?= htmlspecialchars($value) ?>"
                                            <?= $intervalUnit === $value ? "selected" : "" ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="backup-warning backup-warning-soft">
                            <strong>Importante</strong>
                            <p>
                                Esta pantalla guarda la frecuencia del backup. Para que se ejecute automáticamente,
                                el servidor debe llamar periódicamente a <code>utils/cron_backup.php</code>.
                            </p>
                        </div>

                        <div class="backup-schedule-actions">
                            <button type="submit" class="backup-btn backup-btn-primary">
                                Guardar programación
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="backup-schedule-side">
                    <section class="backup-schedule-card">
                        <span class="backup-side-badge">Estado actual</span>

                        <h2>Resumen de programación</h2>

                        <div class="backup-schedule-status-list">
                            <div>
                                <span>Última ejecución</span>
                                <strong><?= htmlspecialchars(formatearFechaProgramacion($lastRunAt)) ?></strong>
                            </div>

                            <div>
                                <span>Próxima ejecución</span>
                                <strong><?= htmlspecialchars(formatearFechaProgramacion($nextRunAt)) ?></strong>
                            </div>

                            <div>
                                <span>Última actualización</span>
                                <strong><?= htmlspecialchars(formatearFechaProgramacion($updatedAt)) ?></strong>
                            </div>
                        </div>
                    </section>

                    <section class="backup-schedule-card">
                        <span class="backup-side-badge">Cron</span>

                        <h2>Ejecución automática</h2>

                        <p class="backup-schedule-text">
                            En Linux, puede usar cron para revisar si ya toca ejecutar el backup.
                        </p>

                        <pre class="backup-command-box"><code>* * * * * php <?= htmlspecialchars(__DIR__) ?>/utils/cron_backup.php</code></pre>

                        <p class="backup-schedule-note">
                            Este comando revisa cada minuto la configuración guardada. Si la fecha actual ya alcanzó
                            la próxima ejecución, el script puede generar el respaldo correspondiente.
                        </p>
                    </section>

                    <section class="backup-schedule-card backup-schedule-danger-card">
                        <span class="backup-side-badge backup-side-badge-danger">Restablecer</span>

                        <h2>Configuración por defecto</h2>

                        <p class="backup-schedule-text">
                            Devuelve la programación a backup completo cada semana.
                        </p>

                        <form method="POST">
                            <input type="hidden" name="action" value="reset_schedule">

                            <button
                                type="submit"
                                class="backup-action-btn backup-action-btn-danger"
                                onclick="return confirm('¿Desea restablecer la programación por defecto?');">
                                Restablecer programación
                            </button>
                        </form>
                    </section>
                </aside>
            </div>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/sistema/backups-manuales/styles.php"; ?>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>