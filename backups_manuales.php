<?php

session_start();

$pageTitle = "Backups manuales - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/respaldo_bd_controller.php";

requireAdmin();

$viewData = obtenerDatosRespaldosBd();

$user     = $viewData["user"];
$error    = $viewData["error"];
$success  = $viewData["success"];
$archivos = $viewData["archivos"];

$totalArchivos = count($archivos);

$totalPendientes = count(
    array_filter(
        $archivos,
        fn(array $archivo): bool => (bool)($archivo["deletion_pending"] ?? false)
    )
);

$totalTamano = array_sum(
    array_map(
        fn(array $archivo): int => (int)($archivo["tamanio"] ?? 0),
        $archivos
    )
);

$ultimoArchivo = $archivos[0] ?? null;

function bmFormatearTamano(int $bytes): string
{
    if ($bytes >= 1024 * 1024 * 1024) {
        return number_format($bytes / (1024 * 1024 * 1024), 2) . " GB";
    }

    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 2) . " MB";
    }

    return number_format($bytes / 1024, 2) . " KB";
}

function bmFormatearFechaCorta(?string $fecha): string
{
    if (!$fecha) {
        return "Sin respaldo reciente";
    }

    $timestamp = strtotime($fecha);

    if ($timestamp === false) {
        return "Sin respaldo reciente";
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

?>

<!DOCTYPE html>
<html lang="es">



<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="dashboard-card dashboard-welcome backup-hero">
            <div>
                <p class="dashboard-eyebrow">Sistema</p>

                <h1 class="dashboard-title">Backups manuales</h1>

                <p class="dashboard-muted">
                    Genere respaldos manuales, descargue archivos y administre el borrado seguro con espera de 24 horas.
                </p>
            </div>

            <a href="dashboard.php" class="btn-secondary-inline backup-back-btn">
                Volver al panel
            </a>
        </section>

        <section class="dashboard-card backup-page-card backup-page-card-compact">

            <?php require __DIR__ . "/partials/sistema/backups-manuales/alerts.php"; ?>

            <section class="backup-summary-row">
                <article class="backup-summary-card">
                    <span>Total de respaldos</span>
                    <strong><?= $totalArchivos ?></strong>
                    <small>archivos disponibles</small>
                </article>

                <article class="backup-summary-card">
                    <span>Espacio ocupado</span>
                    <strong><?= htmlspecialchars(bmFormatearTamano((int)$totalTamano)) ?></strong>
                    <small>almacenado en el sistema</small>
                </article>

                <article class="backup-summary-card">
                    <span>Borrados pendientes</span>
                    <strong><?= $totalPendientes ?></strong>
                    <small>en espera de 24 horas</small>
                </article>

                <article class="backup-summary-card backup-summary-card-blue">
                    <span>Último respaldo</span>
                    <strong>
                        <?= $ultimoArchivo ? htmlspecialchars(bmFormatearTamano((int)($ultimoArchivo["tamanio"] ?? 0))) : "N/A" ?>
                    </strong>
                    <small>
                        <?= htmlspecialchars(bmFormatearFechaCorta($ultimoArchivo["fecha"] ?? null)) ?>
                    </small>
                </article>
            </section>

            <div class="backup-grid backup-grid-manual-compact">
                <?php require __DIR__ . "/partials/sistema/backups-manuales/backup-form.php"; ?>
            </div>

            <?php require __DIR__ . "/partials/sistema/backups-manuales/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/sistema/backups-manuales/styles.php"; ?>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>