<?php
session_start();
$pageTitle = "Dashboard - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

require __DIR__ . "/partials/dashboard/queries.php";
?>

<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <section class="summary-grid">
            <article class="summary-card">
                <p>Clientes</p>
                <h2><?= $totalClientes ?></h2>
                <span class="positive">↑ Sistema activo</span>
                <small>Registrados en el sistema</small>
            </article>

            <article class="summary-card">
                <p>Facturas</p>
                <h2><?= $totalFacturas ?></h2>
                <span class="positive">↑ Ventas registradas</span>
                <small>Total de facturas emitidas</small>
            </article>

            <article class="summary-card">
                <p>Ventas totales</p>
                <h2>C$ <?= number_format((float)$totalVentas, 2) ?></h2>
                <span class="positive">↑ Ingresos acumulados</span>
                <small>Desde el inicio</small>
            </article>

            <article class="summary-card">
                <p>Stock bajo</p>
                <h2><?= $stockBajo ?></h2>
                <span class="negative">↓ Requiere revisión</span>
                <small>Productos con poca existencia</small>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="chart-card">
                <div class="card-header">
                    <h3>Ingresos semanales</h3>
                    <span>Últimos movimientos</span>
                </div>

                <div class="fake-chart">
                    <div style="height: 35%"></div>
                    <div style="height: 55%"></div>
                    <div style="height: 42%"></div>
                    <div style="height: 75%"></div>
                    <div style="height: 62%"></div>
                    <div style="height: 88%"></div>
                    <div style="height: 70%"></div>
                </div>

                <div class="chart-days">
                    <span>Lun</span>
                    <span>Mar</span>
                    <span>Mié</span>
                    <span>Jue</span>
                    <span>Vie</span>
                    <span>Sáb</span>
                    <span>Dom</span>
                </div>
            </article>

            <article class="sales-card">
                <div class="card-header">
                    <h3>Ventas de hoy</h3>
                    <span>Resumen diario</span>
                </div>

                <div class="donut">
                    <div>
                        <strong>C$ <?= number_format((float)$ventasHoy, 2) ?></strong>
                        <small>Hoy</small>
                    </div>
                </div>
            </article>
        </section>

        <section class="bottom-grid">
            <article class="table-card">
                <div class="card-header">
                    <h3>Productos recientes</h3>
                    <a href="productos.php">Ver inventario</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productosRecientes)): ?>
                            <tr>
                                <td colspan="3">No hay productos registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productosRecientes as $producto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($producto["nombre"]) ?></td>
                                    <td>C$ <?= number_format((float)$producto["precio_venta"], 2) ?></td>
                                    <td><?= htmlspecialchars($producto["stock"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </article>

            <article class="quick-card">
                <h3>Acciones rápidas</h3>

                <a href="nueva_factura.php">Nueva factura</a>
                <a href="productos.php">Gestionar productos</a>
                <a href="clientes.php">Gestionar clientes</a>
                <a href="facturas.php">Historial de facturas</a>

                <?php if (($user["rol"] ?? "") === "Administrador"): ?>
                    <a href="usuarios.php">Trabajadores</a>
                    <a href="respaldo_bd.php">Respaldos</a>
                <?php endif; ?>
            </article>
        </section>

    </main>

    <?php require __DIR__ . "/partials/dashboard/styles.php"; ?>

</body>

</html>