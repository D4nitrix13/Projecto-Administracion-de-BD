<?php
session_start();

$pageTitle = "Editar categoría - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

if ($idRol !== 1) {
    $_SESSION["flash_error"] = "Su rol no tiene permisos para editar categorías.";
    header("Location: categorias.php");
    exit();
}

$connection = require "./sql/db.php";

require __DIR__ . "/partials/categorias/editar/queries.php";
?>

<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/categorias/editar/content.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/categorias/editar/styles.php"; ?>

</body>

</html>