<?php

session_start();

$pageTitle = "Clientes - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/clientes_controller.php";

requireLogin();

$viewData = obtenerDatosClientes();

$user             = $viewData["user"];
$idRol            = $viewData["idRol"];
$clientes         = $viewData["clientes"];
$busqueda         = $viewData["busqueda"];
$tipoFiltro       = $viewData["tipoFiltro"];
$soloDetallista   = $viewData["soloDetallista"];
$textoSubtitulo   = $viewData["textoSubtitulo"];
$flashSuccess     = $viewData["flashSuccess"];
$flashError       = $viewData["flashError"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/clientes/header.php"; ?>

        <section class="dashboard-card">

            <?php require __DIR__ . "/partials/clientes/alerts.php"; ?>

            <div class="productos-header-actions">
                <a href="nuevo_cliente.php" class="btn-primary-inline">
                    + Agregar nuevo cliente
                </a>
            </div>

            <?php require __DIR__ . "/partials/clientes/filters.php"; ?>

            <?php require __DIR__ . "/partials/clientes/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>

</body>

</html>