<section class="invoice-paper">

    <header class="invoice-paper-header">
        <div>
            <h2><?= htmlspecialchars($empresaNombre) ?></h2>
            <p><?= htmlspecialchars($empresaDireccion) ?></p>
            <p><?= htmlspecialchars($empresaTelefono) ?></p>
        </div>

        <div class="invoice-paper-meta">
            <span>Factura</span>
            <strong>#<?= (int)$factura["id_factura"] ?></strong>
            <p><?= htmlspecialchars($fechaFactura) ?></p>
        </div>
    </header>

    <section class="invoice-info-grid">
        <article class="invoice-info-box">
            <h3>Cliente</h3>

            <div class="invoice-info-row">
                <span>Nombre</span>
                <strong><?= htmlspecialchars($nombreClienteMostrar) ?></strong>
            </div>

            <?php if (!$esFugaz): ?>
                <div class="invoice-info-row">
                    <span>Teléfono</span>
                    <strong><?= htmlspecialchars($factura["cli_telefono"] ?? "N/D") ?></strong>
                </div>

                <div class="invoice-info-row">
                    <span>Dirección</span>
                    <strong><?= htmlspecialchars($factura["cli_direccion"] ?? "N/D") ?></strong>
                </div>

                <div class="invoice-info-row">
                    <span>Identificación</span>
                    <strong><?= htmlspecialchars($factura["cli_identificacion"] ?? "N/D") ?></strong>
                </div>
            <?php else: ?>
                <div class="invoice-info-row">
                    <span>Tipo</span>
                    <strong>Cliente fugaz</strong>
                </div>
            <?php endif; ?>
        </article>

        <article class="invoice-info-box">
            <h3>Venta</h3>

            <div class="invoice-info-row">
                <span>Sección</span>
                <strong><?= htmlspecialchars($factura["seccion_nombre"] ?? "") ?></strong>
            </div>

            <div class="invoice-info-row">
                <span>Atendió</span>
                <strong><?= htmlspecialchars($factura["usuario_nombre"] ?? "") ?></strong>
            </div>

            <div class="invoice-info-row">
                <span>Tipo</span>
                <strong><?= htmlspecialchars($factura["tipo_cliente_venta"] ?? "Habitual") ?></strong>
            </div>
        </article>
    </section>

    <section class="invoice-products">
        <h3>Detalle</h3>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Desc.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($detalles)): ?>
                    <tr>
                        <td colspan="5" class="empty-row">
                            No hay productos registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($detalle["producto_nombre"] ?? "") ?></strong>
                                <small><?= htmlspecialchars($detalle["producto_codigo"] ?? "") ?></small>
                            </td>

                            <td class="text-center">
                                <?= (int)$detalle["cantidad"] ?>
                            </td>

                            <td class="text-right">
                                C$ <?= number_format((float)$detalle["precio_unitario"], 2) ?>
                            </td>

                            <td class="text-right">
                                C$ <?= number_format((float)$detalle["descuento_linea"], 2) ?>
                            </td>

                            <td class="text-right">
                                <strong>C$ <?= number_format((float)$detalle["total_linea"], 2) ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="invoice-footer-grid">
        <div class="invoice-note">
            <strong>Nota</strong>
            <p>Documento generado por el sistema de facturación e inventario.</p>
        </div>

        <div class="invoice-totals">
            <div>
                <span>Subtotal</span>
                <strong>C$ <?= number_format((float)$factura["subtotal"], 2) ?></strong>
            </div>

            <div>
                <span>Descuento</span>
                <strong>C$ <?= number_format((float)$factura["descuento"], 2) ?></strong>
            </div>

            <div>
                <span>IVA</span>
                <strong>C$ <?= number_format((float)$factura["impuesto"], 2) ?></strong>
            </div>

            <div class="invoice-total-final">
                <span>Total</span>
                <strong>C$ <?= number_format((float)$factura["total"], 2) ?></strong>
            </div>
        </div>
    </section>

</section>