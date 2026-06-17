<?php if ($tipoReporte === "general" || $tipoReporte === "productos"): ?>
    <section class="reports-section">
        <div class="reports-section-header">
            <div>
                <h2>Reporte de productos</h2>
                <p class="dashboard-muted">
                    Productos ordenados por ventas y movimiento de inventario.
                </p>
            </div>

            <div class="reports-section-actions">
                <?php if (($user["rol"] ?? "") === "Administrador"): ?>
                    <a href="export.php?tipo=productos&desde=<?= htmlspecialchars($fechaDesde ?? '') ?>&hasta=<?= htmlspecialchars($fechaHasta ?? '') ?>"
                       class="btn-export" title="Exportar a Excel">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Excel
                    </a>
                <?php endif; ?>
                <a href="productos.php" class="btn-secondary-inline">
                    Ver inventario
                </a>
            </div>
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