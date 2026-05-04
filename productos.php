<?php
session_start();

$pageTitle = "Listado de productos - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

$connection = require "./sql/db.php";

require __DIR__ . "/partials/inventario/productos/queries.php";
?>

<!DOCTYPE html>
<html lang="es">

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/inventario/productos/header.php"; ?>

        <section class="dashboard-card products-panel">

            <?php if ($flash_error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($flash_error) ?>
                </div>
            <?php endif; ?>

            <?php if ($flash_success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($flash_success) ?>
                </div>
            <?php endif; ?>

            <?php require __DIR__ . "/partials/inventario/productos/filters.php"; ?>

            <?php if (empty($productos)): ?>
                <div class="empty-products">
                    <h3>No se encontraron productos</h3>
                    <p>No hay productos que coincidan con los filtros aplicados.</p>
                    <a href="productos.php" class="btn-secondary-inline">Limpiar filtros</a>
                </div>
            <?php else: ?>
                <div class="products-result-header">
                    <p>
                        Mostrando <strong><?= count($productos) ?></strong> producto(s)
                    </p>
                </div>

                <div class="productos-grid">
                    <?php foreach ($productos as $prod): ?>
                        <?php require __DIR__ . "/partials/inventario/productos/card.php"; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
    <?php require __DIR__ . "/partials/inventario/productos/styles.php"; ?>

</body>

</html>