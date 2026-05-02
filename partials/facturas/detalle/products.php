<section class="invoice-products-section">
    <div class="invoice-section-header">
        <div>
            <h3>Productos facturados</h3>
            <p>Detalle de productos incluidos en esta venta.</p>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table-products invoice-products-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>Precio unitario</th>
                    <th>Desc. línea</th>
                    <th>Total línea</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($detalles)): ?>
                    <tr>
                        <td colspan="7" class="dashboard-muted">
                            Esta factura no tiene detalles registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($detalles as $index => $detalle): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($detalle["codigo"]) ?></td>
                            <td><?= htmlspecialchars($detalle["nombre"]) ?></td>
                            <td><?= (int)$detalle["cantidad"] ?></td>
                            <td>C$ <?= number_format((float)$detalle["precio_unitario"], 2) ?></td>
                            <td>C$ <?= number_format((float)$detalle["descuento_linea"], 2) ?></td>
                            <td>
                                <strong>
                                    C$ <?= number_format((float)$detalle["total_linea"], 2) ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>