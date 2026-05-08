<section class="invoice-checkout-section">

    <div class="invoice-summary-card">
        <div class="invoice-summary-header">
            <span>Resumen financiero</span>
            <h3>Total de la factura</h3>
        </div>

        <div class="invoice-total-row">
            <span>Subtotal estimado</span>
            <strong id="subtotal-view">C$ 0.00</strong>
        </div>

        <div class="invoice-total-row">
            <span>Descuento global</span>
            <strong id="descuento-global-view">C$ 0.00</strong>
        </div>

        <div class="invoice-total-row">
            <span>Impuesto 15%</span>
            <strong id="impuesto-view">C$ 0.00</strong>
        </div>

        <div class="invoice-total-row invoice-total-final">
            <span>Total estimado</span>
            <strong id="total-view">C$ 0.00</strong>
        </div>
    </div>

    <div class="invoice-payment-card-main">
        <div class="invoice-payment-header">
            <span>Producción</span>
            <h3>Control de pago y entrega</h3>
            <p>
                Para iniciar producción, el cliente debe pagar como mínimo el 50% del total.
            </p>
        </div>

        <div class="invoice-payment-grid">
            <div class="form-group">
                <label class="label">Monto pagado inicial C$</label>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="monto_pagado"
                    id="monto_pagado"
                    class="input"
                    value="<?= htmlspecialchars($montoPagado ?? "0") ?>">

                <small class="dashboard-muted">
                    Puede pagar el 50% o cancelar el total completo.
                </small>
            </div>

            <div class="form-group">
                <label class="label">Fecha entrega estimada</label>

                <input
                    type="date"
                    name="fecha_entrega_estimada"
                    id="fecha_entrega_estimada"
                    class="input"
                    value="<?= htmlspecialchars($fechaEntregaEstimada ?? "") ?>">

                <small class="dashboard-muted">
                    Fecha prometida para entregar el pedido.
                </small>
            </div>
        </div>

        <div class="invoice-payment-summary">
            <div class="invoice-payment-mini-card">
                <span>Mínimo requerido</span>
                <strong id="minimo-requerido-view">C$ 0.00</strong>
            </div>

            <div class="invoice-payment-mini-card">
                <span>Saldo pendiente</span>
                <strong id="saldo-pendiente-view">C$ 0.00</strong>
            </div>

            <div class="invoice-payment-mini-card">
                <span>Estado de pago</span>
                <strong id="estado-pago-view" class="invoice-payment-status pending">
                    Pendiente
                </strong>
            </div>
        </div>

        <div
            class="invoice-payment-warning"
            id="invoice-payment-warning"
            style="display:none;">

            <div class="invoice-payment-warning-icon">!</div>

            <div>
                <strong>Pago insuficiente</strong>
                <p>
                    El cliente debe pagar al menos el 50% del total para iniciar la producción.
                </p>
            </div>
        </div>
    </div>

</section>