<?php

session_start();

$pageTitle = "Archivos WAL - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

date_default_timezone_set("America/Managua");

require_once __DIR__ . "/controllers/archivos_wal_controller.php";

$viewData = obtenerDatosArchivosWal();

$user = $viewData["user"];
$error = $viewData["error"];
$success = $viewData["success"];
$archivosWal = $viewData["archivosWal"];
$filteredWal = $viewData["filteredWal"];
$tiposDisponibles = $viewData["tiposDisponibles"];
$totalArchivos = $viewData["totalArchivos"];
$totalFiltrados = $viewData["totalFiltrados"];
$totalTamano = $viewData["totalTamano"];
$totalPendingDelete = $viewData["totalPendingDelete"];
$ultimoArchivo = $viewData["ultimoArchivo"];
$searchFilter = $viewData["searchFilter"];
$typeFilter = $viewData["typeFilter"];
$dateFilter = $viewData["dateFilter"];
$deleteFilter = $viewData["deleteFilter"];
$sortFilter = $viewData["sortFilter"];
$paginacion = $viewData["paginacion"];

$filtrosGET = ["search" => $searchFilter, "type" => $typeFilter, "date" => $dateFilter, "delete_status" => $deleteFilter, "sort" => $sortFilter];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/archivos-wal/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/alerts.php"; ?>

        <section class="wal-summary-grid">
            <article class="wal-summary-card">
                <span>Total de archivos</span>
                <strong><?= htmlspecialchars((string)$totalArchivos) ?></strong>
                <small>archivos WAL archivados</small>
            </article>

            <article class="wal-summary-card">
                <span>Resultado actual</span>
                <strong><?= htmlspecialchars((string)$totalFiltrados) ?></strong>
                <small>según filtros aplicados</small>
            </article>

            <article class="wal-summary-card">
                <span>Borrado pendiente</span>
                <strong><?= htmlspecialchars((string)$totalPendingDelete) ?></strong>
                <small>se eliminarán después de 24 horas</small>
            </article>

            <article class="wal-summary-card wal-summary-card-blue">
                <span>Último archivo</span>
                <strong><?= $ultimoArchivo ? htmlspecialchars($ultimoArchivo["type"]) : "N/A" ?></strong>
                <small><?= $ultimoArchivo ? htmlspecialchars($ultimoArchivo["modified_label"]) : "Sin registros" ?></small>
            </article>
        </section>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/filters.php"; ?>

        <?php require __DIR__ . "/partials/sistema/archivos-wal/table.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>
