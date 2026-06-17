<?php

session_start();
require_once __DIR__ . "/bootstrap.php";

$pageTitle = "Acceso de Trabajadores - Panda Estampados / Kitsune";

require_once __DIR__ . "/controllers/login_controller.php";

$viewData = obtenerDatosLogin();

$error = $viewData["error"];
$success = $viewData["success"];

?>

<!DOCTYPE html>
<html lang="es">


<?php require __DIR__ . "/partials/login/styles.php"; ?>

<body class="login-page">

    <main class="login-wrapper">
        <?php require __DIR__ . "/partials/login/card.php"; ?>
    </main>

</body>

</html>