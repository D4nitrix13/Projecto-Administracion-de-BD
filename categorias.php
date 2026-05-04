<?php

session_start();

$pageTitle = "Categorías - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireLogin();

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);
$canManageCategories = $idRol === 1;

$connection = require __DIR__ . "/sql/db.php";

require __DIR__ . "/partials/inventario/categorias/queries.php";

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/inventario/categorias/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/inventario/categorias/content.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>