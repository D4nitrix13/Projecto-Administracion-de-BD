<section class="client-detail-card">

    <div class="client-detail-header">
        <div>
            <p class="dashboard-eyebrow">Detalle de cliente</p>
            <h1><?= htmlspecialchars($nombreCompleto) ?></h1>
            <span><?= htmlspecialchars($cliente["tipo_cliente"] ?? "Sin tipo") ?></span>
        </div>

        <div class="client-detail-actions">
            <a href="clientes.php" class="btn-secondary-inline">
                Volver a clientes
            </a>
            <a href="editar_cliente.php?id=<?= (int)$cliente["id_cliente"] ?>" class="btn-primary-inline">
                Editar cliente
            </a>
        </div>
    </div>

    <section class="client-summary-grid">
        <article class="client-summary-card">
            <p>Total facturas</p>
            <h2><?= (int)($resumenCliente["total_facturas"] ?? 0) ?></h2>
        </article>

        <article class="client-summary-card">
            <p>Total comprado</p>
            <h2>C$ <?= number_format((float)($resumenCliente["total_comprado"] ?? 0), 2) ?></h2>
        </article>

        <article class="client-summary-card">
            <p>Promedio compra</p>
            <h2>C$ <?= number_format((float)($resumenCliente["promedio_compra"] ?? 0), 2) ?></h2>
        </article>
    </section>

    <section class="client-info-grid">
        <article class="client-info-box">
            <h3>Información general</h3>

            <div class="client-info-row">
                <span>Identificación</span>
                <strong><?= htmlspecialchars($cliente["identificacion"] ?? "No registrada") ?></strong>
            </div>

            <div class="client-info-row">
                <span>Teléfono</span>
                <strong><?= htmlspecialchars($cliente["telefono"] ?? "No registrado") ?></strong>
            </div>

            <div class="client-info-row">
                <span>Dirección</span>
                <strong><?= htmlspecialchars($cliente["direccion"] ?? "No registrada") ?></strong>
            </div>

            <div class="client-info-row">
                <span>Tipo de cliente</span>
                <strong><?= htmlspecialchars($cliente["tipo_cliente"] ?? "No definido") ?></strong>
            </div>

            <div class="client-info-row">
                <span>Fecha de registro</span>
                <strong>
                    <?= htmlspecialchars(formatearFechaExtendida($cliente["fecha_registro"])) ?>
                </strong>
            </div>
        </article>

        <article class="client-info-box">
            <div class="client-section-header">
                <h3>Facturas recientes</h3>
                <a href="facturas.php?id_cliente=<?= (int)$cliente["id_cliente"] ?>">
                    Ver historial
                </a>
            </div>

            <?php if (empty($facturasCliente)): ?>
                <p class="empty-client-text">
                    Este cliente todavía no tiene facturas registradas.
                </p>
            <?php else: ?>
                <table class="client-table">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Fecha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facturasCliente as $factura): ?>
                            <tr onclick="window.location.href='detalle_factura.php?id=<?= (int)$factura["id_factura"] ?>'">
                                <td>#<?= htmlspecialchars((string)$factura["id_factura"]) ?></td>
                                <td><?= htmlspecialchars(date("d/m/Y", strtotime($factura["fecha"]))) ?></td>
                                <td>C$ <?= number_format((float)$factura["total"], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>
    </section>

</section>