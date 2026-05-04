<?php if ($tipoReporte === "general" || $tipoReporte === "productos"): ?>
    <section class="reports-section">
        <div class="reports-section-header">
            <div>
                <h2>Reporte de productos</h2>
                <p class="dashboard-muted">
                    Productos ordenados por ventas y movimiento de inventario.
                </p>
            </div>

            <a href="productos.php" class="btn-secondary-inline">
                Ver inventario
            </a>
        </div>

        <?php if (empty($productosReporte)): ?>
            <p class="dashboard-muted">No hay productos disponibles para mostrar.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table-products">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Cantidad vendida</th>
                            <th>Total vendido</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($productosReporte as $producto): ?>
                            <tr>
                                <td><?= htmlspecialchars($producto["codigo"]) ?></td>
                                <td><?= htmlspecialchars($producto["nombre"]) ?></td>
                                <td>
                                    <?php if ((int)$producto["stock"] <= 5): ?>
                                        <span class="report-status report-status-danger">
                                            <?= (int)$producto["stock"] ?> bajo
                                        </span>
                                    <?php else: ?>
                                        <span class="report-status report-status-ok">
                                            <?= (int)$producto["stock"] ?> disponible
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int)$producto["cantidad_vendida"] ?></td>
                                <td>C$ <?= number_format((float)$producto["total_vendido"], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>