<?php

session_start();

$pageTitle = "Configurar cuenta - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/configurar_cuenta_controller.php";

requireLogin();

$viewData = obtenerDatosConfigurarCuenta();

$user          = $viewData["user"];
$error         = $viewData["error"];
$success       = $viewData["success"];
$nombre        = $viewData["nombre"];
$email         = $viewData["email"];
$seccionTexto  = $viewData["seccionTexto"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/cuenta/configurar/header.php"; ?>

        <?php require __DIR__ . "/partials/cuenta/configurar/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/cuenta/configurar/styles.php"; ?>

</body>

</html>