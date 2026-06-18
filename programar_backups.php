<?php

session_start();

$pageTitle = "Programar backups - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

require_once __DIR__ . "/controllers/programar_backups_controller.php";

$viewData = obtenerDatosProgramarBackups();

$user = $viewData["user"];
$error = $viewData["error"];
$success = $viewData["success"];
$tiposBackup = $viewData["tiposBackup"];
$unidadesIntervalo = $viewData["unidadesIntervalo"];
$enabled = $viewData["enabled"];
$type = $viewData["type"];
$intervalValue = $viewData["intervalValue"];
$intervalUnit = $viewData["intervalUnit"];
$lastRunAt = $viewData["lastRunAt"];
$nextRunAt = $viewData["nextRunAt"];
$updatedAt = $viewData["updatedAt"];
$typeLabel = $viewData["typeLabel"];
$frequencyLabel = $viewData["frequencyLabel"];
$typeDescription = $viewData["typeDescription"];

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
