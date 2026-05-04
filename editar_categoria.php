<?php

session_start();

$pageTitle = "Editar categoría - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireLogin();

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

if ($idRol !== 1) {
    $_SESSION["flash_error"] = "Su rol no tiene permisos para editar categorías.";
    header("Location: categorias.php");
    exit();
}

$connection = require __DIR__ . "/sql/db.php";

require __DIR__ . "/partials/inventario/categorias/editar/queries.php";

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/inventario/categorias/editar/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/inventario/categorias/editar/content.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>