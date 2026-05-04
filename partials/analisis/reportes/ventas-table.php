<?php if ($tipoReporte === "general" || $tipoReporte === "ventas"): ?>
    <section class="reports-section">
        <div class="reports-section-header">
            <div>
                <h2>Reporte de ventas</h2>
                <p class="dashboard-muted">
                    Últimas facturas emitidas según los filtros seleccionados.
                </p>
            </div>

            <a href="facturas.php" class="btn-secondary-inline">
                Ver historial completo
            </a>
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