<div class="invoice-summary-grid">

    <article class="invoice-main-panel">
        <div class="invoice-main-header">
            <div>
                <p class="invoice-label">Factura</p>
                <h2>#<?= (int)$factura["id_factura"] ?></h2>
            </div>

            <div class="invoice-badges">
                <span class="invoice-badge invoice-badge-primary">
                    <?= htmlspecialchars($factura["seccion"]) ?>
                </span>

                <span class="invoice-badge">
                    <?= htmlspecialchars($factura["usuario"]) ?>
                </span>
            </div>
        </div>

        <div class="invoice-info-grid">
            <div class="invoice-info-item">
                <span>Fecha</span>
                <strong><?= htmlspecialchars(date("d/m/Y H:i", strtotime($factura["fecha"]))) ?></strong>
            </div>

            <div class="invoice-info-item">
                <span>Subtotal</span>
                <strong>C$ <?= number_format((float)$factura["subtotal"], 2) ?></strong>
            </div>

            <div class="invoice-info-item">
                <span>Impuesto</span>
                <strong>C$ <?= number_format((float)$factura["impuesto"], 2) ?></strong>
            </div>

            <div class="invoice-info-item invoice-info-total">
                <span>Total</span>
                <strong>C$ <?= number_format((float)$factura["total"], 2) ?></strong>
            </div>
        </div>
    </article>

    <article class="invoice-client-panel">
        <p class="invoice-section-title">Cliente</p>

        <div class="invoice-client-list">
            <div>
                <span>Nombre</span>
                <strong><?= htmlspecialchars($factura["cliente"]) ?></strong>
            </div>

            <div>
                <span>Teléfono</span>
                <strong><?= htmlspecialchars($factura["telefono"] ?: "—") ?></strong>
            </div>

            <div>
                <span>Dirección</span>
                <strong><?= htmlspecialchars($factura["direccion"] ?: "—") ?></strong>
            </div>
        </div>
    </article>

</div>