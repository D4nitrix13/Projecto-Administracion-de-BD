<section class="invoice-totals-section">
    <div class="invoice-totals-card">
        <div class="invoice-total-row">
            <span>Subtotal</span>
            <strong>C$ <?= number_format((float)$factura["subtotal"], 2) ?></strong>
        </div>

        <div class="invoice-total-row">
            <span>Descuento</span>
            <strong>- C$ <?= number_format((float)$factura["descuento"], 2) ?></strong>
        </div>

        <div class="invoice-total-row">
            <span>Impuesto</span>
            <strong>C$ <?= number_format((float)$factura["impuesto"], 2) ?></strong>
        </div>

        <div class="invoice-total-row invoice-total-final">
            <span>Total final</span>
            <strong>C$ <?= number_format((float)$factura["total"], 2) ?></strong>
        </div>
    </div>
</section>