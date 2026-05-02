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
require_once __DIR__ . "/helpers/format.php";
?>

<!DOCTYPE html>
<html lang="es">

<?php require "partials/header.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <section class="summary-grid">
            <article class="summary-card clickable-card" onclick="window.location.href='clientes.php'">
                <p>Clientes</p>
                <h2><?= htmlspecialchars((string)$totalClientes) ?></h2>
                <span class="positive">↑ Registrados</span>
                <small>Click para ver clientes</small>
            </article>

            <article class="summary-card clickable-card" onclick="window.location.href='facturas.php'">
                <p>Facturas</p>
                <h2><?= htmlspecialchars((string)$totalFacturas) ?></h2>
                <span class="positive">↑ Emitidas</span>
                <small>Click para ver facturas</small>
            </article>

            <article class="summary-card clickable-card" onclick="window.location.href='reportes.php?tipo=ventas'">
                <p>Ventas totales</p>
                <h2>C$ <?= number_format((float)$totalVentas, 2) ?></h2>
                <span class="positive">↑ Ingresos acumulados</span>
                <small>Click para revisar ventas</small>
            </article>

            <article class="summary-card clickable-card" onclick="window.location.href='productos.php?stock=bajo'">
                <p>Stock bajo</p>
                <h2><?= htmlspecialchars((string)$stockBajo) ?></h2>
                <span class="negative">↓ Revisar inventario</span>
                <small>Click para ver productos</small>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="chart-card clickable-card" onclick="window.location.href='reportes.php?tipo=ventas'">
                <div class="card-header">
                    <div>
                        <h3>Ingresos recientes</h3>
                        <span>Click para ver el historial de ventas</span>
                    </div>
                </div>

                <div class="chart-box">
                    <canvas id="ventasSemanaChart"></canvas>
                </div>
            </article>

            <article class="sales-card clickable-card" onclick="window.location.href='reportes.php?tipo=productos'">
                <div class="card-header">
                    <div>
                        <h3>Productos más vendidos</h3>
                        <span>Click para ver el reporte de productos</span>
                    </div>
                </div>

                <div class="chart-box">
                    <canvas id="productosVendidosChart"></canvas>
                </div>
            </article>
        </section>

        <section class="bottom-grid">
            <article class="table-card">
                <div class="card-header">
                    <div>
                        <h3>Últimos productos vendidos</h3>
                        <span>Movimientos recientes de facturación</span>
                    </div>
                    <a href="facturas.php">Ver facturas</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimosProductosVendidos)): ?>
                            <tr>
                                <td colspan="4" class="empty-table">
                                    Todavía no hay productos vendidos registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ultimosProductosVendidos as $item): ?>
                                <tr
                                    class="clickable-row"
                                    onclick="window.location.href='detalle_factura.php?id=<?= urlencode((string)$item["id_factura"]) ?>'">
                                    <td><?= htmlspecialchars($item["nombre"]) ?></td>
                                    <td><?= htmlspecialchars((string)$item["cantidad"]) ?></td>
                                    <td>C$ <?= number_format((float)$item["subtotal"], 2) ?></td>
                                    <td><?= htmlspecialchars(formatearFechaExtendida($item["fecha"])) ?></td>
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

        <section class="bottom-grid">
            <article class="table-card">
                <div class="card-header">
                    <div>
                        <h3>Facturas recientes</h3>
                        <span>Últimas ventas registradas</span>
                    </div>
                    <a href="facturas.php">Ver todas</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Fecha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($facturasRecientes)): ?>
                            <tr>
                                <td colspan="3" class="empty-table">
                                    Todavía no hay facturas registradas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($facturasRecientes as $factura): ?>
                                <tr
                                    class="clickable-row"
                                    onclick="window.location.href='detalle_factura.php?id=<?= urlencode((string)$factura["id_factura"]) ?>'">
                                    <td>#<?= htmlspecialchars((string)$factura["id_factura"]) ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($factura["fecha"]))) ?></td>
                                    <td>C$ <?= number_format((float)$factura["total"], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </article>

            <article class="table-card">
                <div class="card-header">
                    <div>
                        <h3>Clientes recientes</h3>
                        <span>Últimos clientes agregados</span>
                    </div>
                    <a href="clientes.php">Ver clientes</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientesRecientes)): ?>
                            <tr>
                                <td colspan="3" class="empty-table">
                                    Todavía no hay clientes registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clientesRecientes as $cliente): ?>
                                <tr
                                    class="clickable-row"
                                    onclick="window.location.href='detalle_cliente.php?id=<?= urlencode((string)$cliente["id_cliente"]) ?>'">
                                    <td><?= htmlspecialchars($cliente["nombre"]) ?></td>
                                    <td><?= htmlspecialchars($cliente["telefono"] ?? "No registrado") ?></td>
                                    <td><?= htmlspecialchars(formatearFechaExtendida($cliente["fecha_registro"])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </article>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        const ventasSemanaLabels = <?= json_encode(array_column($ventasSemana, "dia")) ?>;
        const ventasSemanaData = <?= json_encode(array_map("floatval", array_column($ventasSemana, "total_dia"))) ?>;

        const ventasLabelsFinal = ventasSemanaLabels.length > 0 ?
            ventasSemanaLabels : ["Sin datos"];

        const ventasDataFinal = ventasSemanaData.length > 0 ?
            ventasSemanaData : [0];

        const ventasChart = new Chart(document.getElementById("ventasSemanaChart"), {
            type: "line",
            data: {
                labels: ventasLabelsFinal,
                datasets: [{
                    label: "Ingresos C$",
                    data: ventasDataFinal,
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                interaction: {
                    mode: "index",
                    intersect: false
                },
                onClick: function(event, elements) {
                    if (elements.length > 0 && ventasSemanaLabels.length > 0) {
                        const index = elements[0].index;
                        const fecha = ventasSemanaLabels[index];
                        window.location.href = "reportes.php?tipo=ventas&fecha=" + encodeURIComponent(fecha);
                    } else {
                        window.location.href = "reportes.php?tipo=ventas";
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return "Ingresos: C$ " + Number(context.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const productosLabels = <?= json_encode(array_column($productosMasVendidos, "producto")) ?>;
        const productosData = <?= json_encode(array_map("intval", array_column($productosMasVendidos, "cantidad_vendida"))) ?>;
        const productosIds = <?= json_encode(array_column($productosMasVendidos, "id_producto")) ?>;

        const productosLabelsFinal = productosLabels.length > 0 ?
            productosLabels : ["Sin ventas"];

        const productosDataFinal = productosData.length > 0 ?
            productosData : [1];

        const productosChart = new Chart(document.getElementById("productosVendidosChart"), {
            type: "doughnut",
            data: {
                labels: productosLabelsFinal,
                datasets: [{
                    label: "Cantidad vendida",
                    data: productosDataFinal,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                onClick: function(event, elements) {
                    if (elements.length > 0 && productosIds.length > 0) {
                        const index = elements[0].index;
                        const idProducto = productosIds[index];
                        window.location.href = "productos.php?id=" + encodeURIComponent(idProducto);
                    } else {
                        window.location.href = "reportes.php?tipo=productos";
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.label === "Sin ventas") {
                                    return "Todavía no hay ventas registradas";
                                }

                                return context.label + ": " + context.raw + " unidades vendidas";
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>