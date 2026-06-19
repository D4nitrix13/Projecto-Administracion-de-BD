<?php
session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/auditoria_eliminados_controller.php";

requireAdmin();

$datos = obtenerDatosAuditoriaEliminados();

$user = $datos["user"];
$registros = $datos["registros"];
$resumen = $datos["resumen"];
$busqueda = $datos["busqueda"];
$tablaFiltro = $datos["tablaFiltro"];
$fechaDesde = $datos["fechaDesde"];
$fechaHasta = $datos["fechaHasta"];
$flashSuccess = $datos["flashSuccess"];
$flashError = $datos["flashError"];
$paginacion = $datos["paginacion"];

$filtrosGET = ["q" => $busqueda, "tabla" => $tablaFiltro, "desde" => $fechaDesde, "hasta" => $fechaHasta];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registros eliminados</title>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/auditoria/eliminados/styles.php"; ?>
</head>

<body class="dashboard-body">
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">
        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/auditoria/eliminados/header.php"; ?>

        <?php require __DIR__ . "/partials/auditoria/eliminados/alerts.php"; ?>

        <?php require __DIR__ . "/partials/auditoria/eliminados/filters.php"; ?>

        <?php require __DIR__ . "/partials/auditoria/eliminados/cards.php"; ?>
    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
</body>

</html>