<?php

session_start();

$pageTitle = "Proveedores - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/proveedores_controller.php";

requireLogin();

$viewData = obtenerDatosProveedores();

$user             = $viewData["user"];
$idRol            = $viewData["idRol"];
$puedeGestionar   = $viewData["puedeGestionar"];
$error            = $viewData["error"];
$success          = $viewData["success"];
$flashSuccess     = $viewData["flashSuccess"];
$flashError       = $viewData["flashError"];
$busqueda         = $viewData["busqueda"];
$proveedores      = $viewData["proveedores"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/inventario/proveedores/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="proveedores-hero">
            <p class="dashboard-eyebrow">Inventario</p>

            <h1 class="dashboard-title">
                Proveedores
            </h1>

            <p class="dashboard-muted">
                Administre los proveedores que suministran productos a Panda Estampados y Kitsune.
            </p>
        </section>

        <?php require __DIR__ . "/partials/inventario/proveedores/alerts.php"; ?>

        <?php if ($puedeGestionar): ?>
            <section class="proveedores-card">
                <?php require __DIR__ . "/partials/inventario/proveedores/create-form.php"; ?>
            </section>
        <?php endif; ?>

        <section class="proveedores-card">
            <?php require __DIR__ . "/partials/inventario/proveedores/filters.php"; ?>

            <?php require __DIR__ . "/partials/inventario/proveedores/table.php"; ?>
        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>