<?php if ($tipoReporte === "general" || $tipoReporte === "clientes"): ?>
    <section class="reports-section">
        <div class="reports-section-header">
            <div>
                <h2>Reporte de clientes</h2>
                <p class="dashboard-muted">
                    Clientes ordenados por monto comprado y cantidad de facturas.
                </p>
            </div>

            <a href="clientes.php" class="btn-secondary-inline">
                Ver clientes
            </a>
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