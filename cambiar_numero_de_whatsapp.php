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

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/whatsapp/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="whatsapp-page-heading">
            <?php require __DIR__ . "/partials/sistema/whatsapp/page-header.php"; ?>
        </section>

        <section class="whatsapp-card">
            <?php require __DIR__ . "/partials/sistema/whatsapp/form.php"; ?>
        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>