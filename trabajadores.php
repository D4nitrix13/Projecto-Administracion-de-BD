<?php

session_start();

$pageTitle = "Trabajadores - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/usuarios_controller.php";

requireAdmin();

$viewData = obtenerDatosUsuarios();

$user             = $viewData["user"];
$error            = $viewData["error"];
$flashSuccess     = $viewData["flashSuccess"];
$flashError       = $viewData["flashError"];
$roles            = $viewData["roles"];
$secciones        = $viewData["secciones"];
$usuarios         = $viewData["usuarios"];
$busqueda         = $viewData["busqueda"];
$rolFiltroInt     = $viewData["rolFiltroInt"];
$seccionFiltro    = $viewData["seccionFiltro"];
$paginacion       = $viewData["paginacion"];
$filtrosGET       = $viewData["filtrosGET"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/trabajadores/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="usuarios-hero">
            <?php require __DIR__ . "/partials/sistema/trabajadores/header.php"; ?>
        </section>

        <?php require __DIR__ . "/partials/sistema/trabajadores/alerts.php"; ?>

        <section class="usuarios-card">
            <?php require __DIR__ . "/partials/sistema/trabajadores/create-form.php"; ?>
        </section>

        <section class="usuarios-card">
            <?php require __DIR__ . "/partials/sistema/trabajadores/filters.php"; ?>

            <?php require __DIR__ . "/partials/sistema/trabajadores/table.php"; ?>
        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/sistema/trabajadores/scripts.php"; ?>

</body>

</html>