<?php

session_start();

$pageTitle = "Editar trabajadores - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/editar_usuario_controller.php";

requireAdmin();

$viewData = obtenerDatosEditarUsuario();

$user                = $viewData["user"];
$error               = $viewData["error"];
$trabajadores          = $viewData["trabajadores"];
$roles               = $viewData["roles"];
$seccionTextoActual  = $viewData["seccionTextoActual"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/trabajadores/editar/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/trabajadores/editar/header.php"; ?>

        <?php require __DIR__ . "/partials/trabajadores/editar/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/trabajadores/editar/scripts.php"; ?>

</body>

</html>