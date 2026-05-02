<?php
session_start();

$pageTitle = "Categorías - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: /login.php");
    exit();
}

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);
$canManageCategories = $idRol === 1;

$connection = require __DIR__ . "/sql/db.php";

require __DIR__ . "/partials/categorias/queries.php";
?>

<!DOCTYPE html>
<html lang="es">
<?php require __DIR__ . "/partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/categorias/content.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/categorias/styles.php"; ?>

</body>

</html>