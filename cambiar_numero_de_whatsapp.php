<?php

session_start();

$pageTitle = "Cambiar número de WhatsApp - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/services/WhatsAppConfigService.php";

requireAdmin();

$user = $_SESSION["user"];

$whatsAppService = new WhatsAppConfigService(
    __DIR__ . "/numero_de_whatsapp.txt"
);

$error = null;
$success = null;

$numeroActual = $whatsAppService->obtenerNumeroActual();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevoNumero = trim($_POST["numero"] ?? "");

    $resultado = $whatsAppService->actualizarNumero($nuevoNumero);

    if ($resultado["success"]) {
        $success = $resultado["message"];
        $numeroActual = $resultado["numero"];
    } else {
        $error = $resultado["message"];
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/whatsapp/page-header.php"; ?>

        <?php require __DIR__ . "/partials/whatsapp/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>