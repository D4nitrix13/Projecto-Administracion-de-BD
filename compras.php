<?php

session_start();

$pageTitle = "Historial de compras - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/compras_controller.php";

requireLogin();

$viewData = obtenerDatosCompras();

$user               = $viewData["user"];
$flashSuccess       = $viewData["flashSuccess"];
$flashError         = $viewData["flashError"];
$busqueda           = $viewData["busqueda"];
$proveedorFiltroInt = $viewData["proveedorFiltroInt"];
$usuarioFiltroInt   = $viewData["usuarioFiltroInt"];
$fechaDesde         = $viewData["fechaDesde"];
$fechaHasta         = $viewData["fechaHasta"];
$proveedores        = $viewData["proveedores"];
$usuarios           = $viewData["usuarios"];
$compras            = $viewData["compras"];

?>

<!DOCTYPE html>
<html lang="es">


<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/analisis/compras/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/analisis/compras/header.php"; ?>

        <section class="dashboard-card">

            <?php require __DIR__ . "/partials/analisis/compras/alerts.php"; ?>

            <?php require __DIR__ . "/partials/analisis/compras/filters.php"; ?>

            <?php require __DIR__ . "/partials/analisis/compras/table.php"; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>