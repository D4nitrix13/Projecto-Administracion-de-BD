<?php if ($tipoReporte === "general" || $tipoReporte === "ventas"): ?>
    <section class="reports-section">
        <div class="reports-section-header">
            <div>
                <h2>Reporte de ventas</h2>
                <p class="dashboard-muted">
                    Últimas facturas emitidas según los filtros seleccionados.
                </p>
            </div>

            <div class="reports-section-actions">
                <?php if (($user["rol"] ?? "") === "Administrador"): ?>
                    <a href="export.php?tipo=ventas&desde=<?= htmlspecialchars($fechaDesde ?? '') ?>&hasta=<?= htmlspecialchars($fechaHasta ?? '') ?>"
                       class="btn-export" title="Exportar a Excel">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Excel
                    </a>
                <?php endif; ?>
                <a href="facturas.php" class="btn-secondary-inline">
                    Ver historial completo
                </a>
            </div>
        </div>

        <?php if (empty($ventasDetalladas)): ?>
            <p class="dashboard-muted">No hay ventas registradas para los filtros aplicados.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table-products">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Sección</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th class="col-acciones">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($ventasDetalladas as $venta): ?>
                            <tr>
                                <td>#<?= (int)$venta["id_factura"] ?></td>
                                <td><?= htmlspecialchars(formatearFechaExtendida($venta["fecha"])) ?></td>
                                <td><?= htmlspecialchars($venta["cliente"]) ?></td>
                                <td><?= htmlspecialchars($venta["seccion"]) ?></td>
                                <td><?= htmlspecialchars($venta["usuario"]) ?></td>
                                <td>C$ <?= number_format((float)$venta["total"], 2) ?></td>
                                <td class="acciones">
                                    <a
                                        href="detalle_factura.php?id=<?= (int)$venta["id_factura"] ?>"
                                        class="btn-accion btn-accion-detalle">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>