<?php if ($tipoReporte === "general" || $tipoReporte === "clientes"): ?>
    <section class="reports-section">
        <div class="reports-section-header">
            <div>
                <h2>Reporte de clientes</h2>
                <p class="dashboard-muted">
                    Clientes ordenados por monto comprado y cantidad de facturas.
                </p>
            </div>

            <div class="reports-section-actions">
                <?php if (($user["rol"] ?? "") === "Administrador"): ?>
                    <a href="export.php?tipo=clientes&desde=<?= htmlspecialchars($fechaDesde ?? '') ?>&hasta=<?= htmlspecialchars($fechaHasta ?? '') ?>"
                       class="btn-export" title="Exportar a Excel">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Excel
                    </a>
                <?php endif; ?>
                <a href="clientes.php" class="btn-secondary-inline">
                    Ver clientes
                </a>
            </div>
        </div>

        <?php if (empty($clientesReporte)): ?>
            <p class="dashboard-muted">No hay clientes disponibles para mostrar.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table-products">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Tipo</th>
                            <th>Facturas</th>
                            <th>Total comprado</th>
                            <th class="col-acciones">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($clientesReporte as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente["cliente"]) ?></td>
                                <td><?= htmlspecialchars($cliente["telefono"] ?? "No registrado") ?></td>
                                <td><?= htmlspecialchars($cliente["tipo_cliente"] ?? "No definido") ?></td>
                                <td><?= (int)$cliente["cantidad_facturas"] ?></td>
                                <td>C$ <?= number_format((float)$cliente["total_comprado"], 2) ?></td>
                                <td class="acciones">
                                    <a
                                        href="detalle_cliente.php?id=<?= (int)$cliente["id_cliente"] ?>"
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