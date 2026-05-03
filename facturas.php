<?php

session_start();

$pageTitle = "Facturas - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/facturas_controller.php";

requireLogin();

$viewData = obtenerDatosFacturas();

$user              = $viewData["user"];
$idRol             = $viewData["idRol"];
$facturas          = $viewData["facturas"];
$secciones         = $viewData["secciones"];
$usuariosFiltro    = $viewData["usuariosFiltro"];
$busqueda          = $viewData["busqueda"];
$seccionFiltroInt  = $viewData["seccionFiltroInt"];
$usuarioFiltroInt  = $viewData["usuarioFiltroInt"];
$fechaDesde        = $viewData["fechaDesde"];
$fechaHasta        = $viewData["fechaHasta"];
$textoSubtitulo    = $viewData["textoSubtitulo"];
$flashSuccess      = $viewData["flashSuccess"];
$flashError        = $viewData["flashError"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/facturas/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/facturas/header.php"; ?>

        <section class="dashboard-card">

            <?php require __DIR__ . "/partials/facturas/alerts.php"; ?>

            <?php require __DIR__ . "/partials/facturas/actions.php"; ?>

            <?php require __DIR__ . "/partials/facturas/filters.php"; ?>

            <?php require __DIR__ . "/partials/facturas/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>