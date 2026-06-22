<?php

session_start();

$pageTitle = "Logs del sistema - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

require_once __DIR__ . "/controllers/logs_sistema_controller.php";

$viewData = obtenerDatosLogsSistema();

$error = $viewData["error"];
$success = $viewData["success"];
$logs = $viewData["logs"];
$filteredLogs = $viewData["filteredLogs"];
$totalLogs = $viewData["totalLogs"];
$totalFiltered = $viewData["totalFiltered"];
$totalPendingDelete = $viewData["totalPendingDelete"];
$totalSize = $viewData["totalSize"];
$lastLog = $viewData["lastLog"];
$availableTypes = $viewData["availableTypes"];
$selectedLog = $viewData["selectedLog"];
$selectedContent = $viewData["selectedContent"];
$sourceFilter = $viewData["sourceFilter"];
$typeFilter = $viewData["typeFilter"];
$searchFilter = $viewData["searchFilter"];
$dateFilter = $viewData["dateFilter"];
$deleteFilter = $viewData["deleteFilter"];
$selectedSource = $viewData["selectedSource"];
$selectedFile = $viewData["selectedFile"];
$allowedSources = $viewData["allowedSources"];
$paginacion = $viewData["paginacion"];
$user = $viewData["user"];

$filtrosGET = ["source" => $sourceFilter, "type" => $typeFilter, "search" => $searchFilter, "date" => $dateFilter, "delete_status" => $deleteFilter];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/logs-sistema/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/logs-sistema/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/logs-sistema/alerts.php"; ?>

        <section class="logs-summary-grid">
            <article class="logs-summary-card">
                <span>Total de archivos</span>
                <strong><?= htmlspecialchars((string)$totalLogs) ?></strong>
                <small>logs encontrados</small>
            </article>

            <article class="logs-summary-card">
                <span>Resultado actual</span>
                <strong><?= htmlspecialchars((string)$totalFiltered) ?></strong>
                <small>según filtros aplicados</small>
            </article>

            <article class="logs-summary-card">
                <span>Borrado pendiente</span>
                <strong><?= htmlspecialchars((string)$totalPendingDelete) ?></strong>
                <small>se eliminarán después de 24 horas</small>
            </article>

            <article class="logs-summary-card logs-summary-card-blue">
                <span>Último log</span>
                <strong><?= $lastLog ? htmlspecialchars($lastLog["type"]) : "N/A" ?></strong>
                <small><?= $lastLog ? htmlspecialchars($lastLog["modified_label"]) : "Sin registros" ?></small>
            </article>
        </section>

        <?php require __DIR__ . "/partials/sistema/logs-sistema/filters.php"; ?>

        <section class="logs-layout">
            <?php require __DIR__ . "/partials/sistema/logs-sistema/table.php"; ?>

            <?php require __DIR__ . "/partials/sistema/logs-sistema/viewer.php"; ?>
        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

    <?php if ($selectedLog): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var viewer = document.getElementById("logViewer");
            if (viewer) {
                setTimeout(function() {
                    viewer.scrollIntoView({ behavior: "smooth", block: "start" });
                }, 100);
            }
        });
    </script>
    <?php endif; ?>

</body>

</html>