<?php
session_start();

$pageTitle = "Detalle del cliente - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

require_once __DIR__ . "/helpers/format.php";
require __DIR__ . "/partials/detalle-cliente/queries.php";
?>

<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/detalle-cliente/content.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/detalle-cliente/styles.php"; ?>

</body>

</html>