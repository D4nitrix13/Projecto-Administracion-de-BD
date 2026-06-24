<?php
session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/facturas_controller.php";

requireLogin();

$datos = obtenerDatosFacturas();

$user = $datos["user"];
$idRol = $datos["idRol"];
$facturas = $datos["facturas"];
$secciones = $datos["secciones"];
$usuariosFiltro = $datos["usuariosFiltro"];

$busqueda = $datos["busqueda"] ?? "";
$seccionFiltroInt = $datos["seccionFiltroInt"] ?? null;
$usuarioFiltroInt = $datos["usuarioFiltroInt"] ?? null;
$estadoPagoFiltro = $datos["estadoPagoFiltro"] ?? "";
$estadoProduccionFiltro = $datos["estadoProduccionFiltro"] ?? "";
$fechaDesde = $datos["fechaDesde"] ?? "";
$fechaHasta = $datos["fechaHasta"] ?? "";

$textoSubtitulo = $datos["textoSubtitulo"];
$flashSuccess = $datos["flashSuccess"];
$flashError = $datos["flashError"];
$paginacion = $datos["paginacion"];
$filtrosGET = $datos["filtrosGET"];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de facturas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/facturacion/facturas/styles.php"; ?>
</head>

<body class="dashboard-body">
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">
        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/facturacion/facturas/header.php"; ?>

        <section class="dashboard-card">
            <?php require __DIR__ . "/partials/facturacion/facturas/alerts.php"; ?>

            <?php require __DIR__ . "/partials/facturacion/facturas/actions.php"; ?>

            <?php require __DIR__ . "/partials/facturacion/facturas/filters.php"; ?>

            <?php require __DIR__ . "/partials/facturacion/facturas/table.php"; ?>
        </section>
    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/shared/toast.php"; ?>

    <script>
        document.querySelectorAll(".delete-factura-form").forEach(form => {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                const formRef = this;

                confirmAction(
                    "¿Seguro que desea eliminar esta factura? Esta acción no se puede deshacer.",
                    function () {
                        formRef.submit();
                    }
                );
            });
        });
    </script>
</body>

</html>