<?php
session_start();

$pageTitle = "Imagen del producto - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];

$connection = require "./sql/db.php";

require __DIR__ . "/partials/inventario/productos/ver-imagen/queries.php";
?>

<!DOCTYPE html>
<html lang="es">

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/inventario/productos/ver-imagen/content.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/inventario/productos/ver-imagen/styles.php"; ?>

</body>

</html>