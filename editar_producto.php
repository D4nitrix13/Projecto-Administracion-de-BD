<?php

session_start();

$pageTitle = "Editar producto - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/editar_producto_controller.php";

requireLogin();

$viewData = obtenerDatosEditarProducto();

$user        = $viewData["user"];
$error       = $viewData["error"];
$producto    = $viewData["producto"];
$categorias  = $viewData["categorias"];
$proveedores = $viewData["proveedores"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/productos/editar/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <section class="product-page-heading">
            <?php require __DIR__ . "/partials/productos/editar/header.php"; ?>
        </section>

        <?php require __DIR__ . "/partials/productos/editar/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>