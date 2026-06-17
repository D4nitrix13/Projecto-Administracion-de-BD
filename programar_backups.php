<?php

session_start();

$pageTitle = "Programar backups - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

$user = $_SESSION["user"];

$scheduleDir = __DIR__ . "/storage/system";
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

asegurarArchivoProgramacionBackup($scheduleDir, $scheduleFile);
asegurarArchivoHistorialMantenimiento($historyFile);

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
                : "La programación automática de backups fue pausada.";
        }
    }

    if ($action === "reset_schedule") {
        $schedule = obtenerProgramacionDefault();

        guardarProgramacionBackup($scheduleFile, $schedule);

        $success = "La programación fue restablecida a backup completo cada semana.";
    }

    if ($action === "reset_history") {
        reiniciarHistorialMantenimiento($historyFile);

        $success = "El historial de mantenimiento fue reiniciado correctamente.";
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

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/programar-backups/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/programar-backups/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/programar-backups/alerts.php"; ?>

        <?php require __DIR__ . "/partials/sistema/programar-backups/status.php"; ?>

        <section class="programar-layout">

            <?php require __DIR__ . "/partials/sistema/programar-backups/form.php"; ?>

            <aside class="programar-side">

                <article class="programar-card">
                    <div class="programar-card-header">
                        <div>
                            <span class="programar-kicker">Estado actual</span>
                            <h2>Resumen de programación</h2>
                        </div>
                    </div>

                    <div class="programar-info-list">
                        <div>
                            <span>Última ejecución</span>
                            <strong><?= htmlspecialchars(formatearFechaProgramacion($lastRunAt)) ?></strong>
                        </div>

                        <div>
                            <span>Próxima ejecución</span>
                            <strong><?= htmlspecialchars(formatearFechaProgramacion($nextRunAt)) ?></strong>
                        </div>

                        <div>
                            <span>Último cambio</span>
                            <strong><?= htmlspecialchars(formatearFechaProgramacion($updatedAt)) ?></strong>
                        </div>
                    </div>
                </article>

                <article class="programar-card">
                    <div class="programar-card-header">
                        <div>
                            <span class="programar-kicker">Recomendación</span>
                            <h2>Frecuencia sugerida</h2>
                        </div>
                    </div>

                    <div class="programar-info-list">
                        <div>
                            <span>Backup completo</span>
                            <strong>Semanal</strong>
                        </div>

                        <div>
                            <span>Backup diferencial</span>
                            <strong>Diario</strong>
                        </div>

                        <div>
                            <span>Revisión de logs</span>
                            <strong>Diaria</strong>
                        </div>
                    </div>
                </article>

                <article class="programar-card programar-danger-card">
                    <div class="programar-card-header">
                        <div>
                            <span class="programar-kicker programar-kicker-danger">Restablecer</span>
                            <h2>Configuración recomendada</h2>
                        </div>
                    </div>

                    <p class="programar-card-text">
                        Devuelve la programación a backup completo cada semana.
                    </p>

                    <form method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="reset_schedule">

                        <button
                            type="submit"
                            class="programar-danger-button"
                            onclick="return confirm('¿Desea restablecer la programación por defecto?');">
                            Restablecer programación
                        </button>
                    </form>
                </article>

                <article class="programar-card programar-danger-card">
                    <div class="programar-card-header">
                        <div>
                            <span class="programar-kicker programar-kicker-danger">Historial</span>
                            <h2>Reiniciar historial</h2>
                        </div>
                    </div>

                    <p class="programar-card-text">
                        Limpia el archivo de historial de mantenimiento y deja el registro vacío.
                    </p>

                    <form method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="reset_history">

                        <button
                            type="submit"
                            class="programar-danger-button"
                            onclick="return confirm('¿Desea reiniciar el historial de mantenimiento? Esta acción no se puede deshacer.');">
                            Reiniciar historial
                        </button>
                    </form>
                </article>

            </aside>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>