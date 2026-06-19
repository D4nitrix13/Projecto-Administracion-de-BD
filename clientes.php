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
$paginacion       = $viewData["paginacion"];
$filtrosGET       = $viewData["filtrosGET"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/compradores/clientes/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="clientes-hero">
            <?php require __DIR__ . "/partials/compradores/clientes/header.php"; ?>
        </section>

        <section class="clientes-card">

            <?php require __DIR__ . "/partials/compradores/clientes/alerts.php"; ?>

            <div class="clientes-header-actions">
                <a href="nuevo_cliente.php" class="btn-primary-inline">
                    + Agregar nuevo cliente
                </a>
            </div>

            <?php require __DIR__ . "/partials/compradores/clientes/filters.php"; ?>

            <?php require __DIR__ . "/partials/compradores/clientes/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>