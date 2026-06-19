<?php

session_start();

$pageTitle = "Notificaciones - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/helpers/pagination.php";
require_once __DIR__ . "/services/NotificacionService.php";

requireLogin();

$user = $_SESSION["user"];
$idUsuario = (int)$user["id_usuario"];
$esAdmin = ($user["rol"] ?? "") === "Administrador";

$service = new NotificacionService();
$allNotificaciones = $service->obtenerParaUsuario($idUsuario);
$sinLeer = $service->contarSinLeer($idUsuario);

$totalNotificaciones = count($allNotificaciones);
$paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));
$paginacion = calcularPaginacion($totalNotificaciones, $paginaActual);
$notificaciones = array_slice($allNotificaciones, $paginacion["offset"], $paginacion["porPagina"]);

$flashSuccess = $_SESSION["flash_success"] ?? null;
$flashError = $_SESSION["flash_error"] ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);
?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>

<style>
    .notif-actions { display: flex; gap: 8px; flex-wrap: wrap; align-self: center; }
    .notif-badge-count { background: #dc2626; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; margin-left: 8px; }
    .notif-list { display: grid; gap: 8px; }
    .notif-item {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 16px 18px; background: var(--card-bg); border: 1px solid var(--border);
        border-radius: 14px; transition: all 0.15s ease; position: relative;
    }
    .notif-item:hover { border-color: #dbe3ee; box-shadow: var(--shadow-hover); }
    .notif-item.unread { border-left: 4px solid var(--primary); background: #f0f7ff; }
    .notif-icon {
        width: 40px; height: 40px; min-width: 40px; border-radius: 12px;
        display: grid; place-items: center; color: #fff; font-size: 1.1rem;
    }
    .notif-content { flex: 1; min-width: 0; }
    .notif-title { font-weight: 800; font-size: 0.95rem; margin-bottom: 4px; }
    .notif-message { color: var(--text-muted); font-size: 0.88rem; line-height: 1.45; }
    .notif-time { color: var(--text-muted); font-size: 0.78rem; margin-top: 6px; }
    .notif-actions-inline { display: flex; gap: 6px; margin-top: 8px; }
    .notif-btn {
        border: none; background: none; cursor: pointer; padding: 4px 8px;
        border-radius: 8px; font-size: 0.78rem; font-weight: 700; transition: all 0.15s;
    }
    .notif-btn-read { color: var(--primary); }
    .notif-btn-read:hover { background: #eff6ff; }
    .notif-btn-delete { color: var(--danger); }
    .notif-btn-delete:hover { background: #fef2f2; }
    .notif-empty { text-align: center; padding: 48px 16px; color: var(--text-muted); }
    .notif-empty svg { width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.4; }
    .btn-sm { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 800; border: 1px solid var(--border); background: #fff; color: var(--text-main); cursor: pointer; transition: all 0.15s; }
    .btn-sm:hover { background: #f8fafc; border-color: #dbe3ee; }
    .btn-sm-danger { color: var(--danger); border-color: #fecaca; }
    .btn-sm-danger:hover { background: #fef2f2; }
    .btn-sm-primary { color: var(--primary); border-color: #bfdbfe; }
    .btn-sm-primary:hover { background: #eff6ff; }
    .toast {
        position: fixed; bottom: 24px; right: 24px; padding: 14px 20px;
        border-radius: 12px; color: #fff; font-weight: 700; font-size: 0.88rem;
        z-index: 9999; opacity: 0; transform: translateY(10px);
        transition: all 0.3s ease; pointer-events: none;
    }
    .toast.show { opacity: 1; transform: translateY(0); pointer-events: auto; }
    .toast-success { background: #16a34a; }
    .toast-error { background: #dc2626; }
</style>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="dashboard-page-heading">
            <div class="notif-header">
                <div>
                    <h1>
                        Notificaciones
                        <?php if ($sinLeer > 0): ?>
                            <span class="notif-badge-count"><?= $sinLeer ?></span>
                        <?php endif; ?>
                    </h1>
                    <p>Historial de eventos del sistema</p>
                </div>

                <div class="notif-actions">
                    <?php if ($sinLeer > 0): ?>
                        <button class="btn-sm btn-sm-primary" onclick="marcarTodasLeidas()">
                            Marcar todo como leído
                        </button>
                    <?php endif; ?>

                    <?php if ($esAdmin && count($notificaciones) > 0): ?>
                        <button class="btn-sm btn-sm-danger" onclick="limpiarHistorial()">
                            Limpiar historial
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if ($flashSuccess): ?>
            <div class="alert alert-success" style="margin-bottom:16px;">
                <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="alert alert-danger" style="margin-bottom:16px;">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <div class="notif-list" id="notifList">
            <?php if (empty($notificaciones)): ?>
                <div class="notif-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p>No hay notificaciones.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notificaciones as $notif): ?>
                    <?php
                    $esLeida = !empty($notif["leida"]);
                    $color = $service->tipoColor($notif["tipo"]);
                    $icono = $service->tipoIcono($notif["tipo"]);
                    $label = $service->tipoLabel($notif["tipo"]);
                    $puedeEliminar = $esAdmin || (int)($notif["id_usuario_destino"] ?? 0) === $idUsuario;
                    ?>
                    <div class="notif-item <?= $esLeida ? "" : "unread" ?>" id="notif-<?= htmlspecialchars($notif["id"]) ?>">
                        <div class="notif-icon" style="background: <?= $color ?>">
                            <?= $label[0] ?>
                        </div>

                        <div class="notif-content">
                            <div class="notif-title"><?= htmlspecialchars($notif["titulo"]) ?></div>
                            <div class="notif-message"><?= htmlspecialchars($notif["mensaje"]) ?></div>
                            <div class="notif-time">
                                <?= htmlspecialchars($label) ?> &middot;
                                <?= htmlspecialchars($notif["fecha"]) ?>
                            </div>

                            <div class="notif-actions-inline">
                                <?php if (!$esLeida): ?>
                                    <button class="notif-btn notif-btn-read" onclick="marcarLeida('<?= htmlspecialchars($notif["id"]) ?>')">
                                        Marcar leído
                                    </button>
                                <?php endif; ?>

                                <?php if ($puedeEliminar): ?>
                                    <button class="notif-btn notif-btn-delete" onclick="eliminarNotif('<?= htmlspecialchars($notif["id"]) ?>')">
                                        Eliminar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php
        $baseUrl = "notificaciones.php";
        $filtrosActuales = [];
        require __DIR__ . "/partials/shared/pagination.php";
        ?>

    </main>

    <div class="toast" id="toast"></div>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

    <script>
        const CSRF_TOKEN = '<?= csrfToken() ?>';

        function showToast(msg, type) {
            const t = document.getElementById("toast");
            t.textContent = msg;
            t.className = "toast toast-" + type + " show";
            setTimeout(() => t.className = "toast", 3000);
        }

        function apiCall(action, extra) {
            const fd = new FormData();
            fd.append("action", action);
            fd.append("_token", CSRF_TOKEN);
            if (extra) fd.append("id", extra);

            return fetch("notificaciones_api.php", { method: "POST", body: fd })
                .then(r => r.json());
        }

        function marcarLeida(id) {
            apiCall("marcar_leida", id).then(res => {
                if (res.ok) {
                    const el = document.getElementById("notif-" + id);
                    if (el) el.classList.remove("unread");
                    showToast("Marcada como leída", "success");
                    setTimeout(() => location.reload(), 500);
                }
            });
        }

        function marcarTodasLeidas() {
            apiCall("marcar_todas").then(res => {
                if (res.ok) {
                    showToast(res.marcadas + " notificaciones marcadas", "success");
                    setTimeout(() => location.reload(), 500);
                }
            });
        }

        function eliminarNotif(id) {
            if (!confirm("¿Eliminar esta notificación?")) return;
            apiCall("eliminar", id).then(res => {
                if (res.ok) {
                    const el = document.getElementById("notif-" + id);
                    if (el) el.remove();
                    showToast("Notificación eliminada", "success");
                } else {
                    showToast("No se pudo eliminar", "error");
                }
            });
        }

        function limpiarHistorial() {
            if (!confirm("¿Eliminar todo el historial de notificaciones? Esta acción no se puede deshacer.")) return;
            apiCall("limpiar").then(res => {
                if (res.ok) {
                    showToast("Historial limpiado", "success");
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast("No se pudo limpiar", "error");
                }
            });
        }
    </script>

</body>

</html>
