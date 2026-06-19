<section class="invoice-checkout-section">

    <div class="invoice-summary-card">
        <div class="invoice-summary-header">
            <span>Resumen financiero</span>
            <h3>Total de la factura</h3>
        </div>

        <div class="invoice-total-row">
            <span>Subtotal</span>
            <strong id="subtotal-view">C$ 0.00</strong>
        </div>

        <div class="invoice-total-row">
            <span>Descuento</span>
            <strong id="descuento-global-view">C$ 0.00</strong>
        </div>

        <div class="invoice-total-row">
            <span>IVA 15%</span>
            <strong id="impuesto-view">C$ 0.00</strong>
        </div>

        <div class="invoice-total-row invoice-total-final">
            <span>Total</span>
            <strong id="total-view">C$ 0.00</strong>
        </div>
    </div>

    <div class="invoice-plazos-section" id="invoice-plazos-section">

        <div class="invoice-plazos-header">
            <span>Plan de pagos</span>
            <h3>Configure el pago y plazos</h3>
        </div>

        <div class="invoice-plazos-fields">
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
            </div>

            <div class="form-group">
                <label class="label">Fecha entrega estimada</label>
                <input
                    type="date"
                    name="fecha_entrega_estimada"
                    id="fecha_entrega_estimada"
                    class="input"
                    value="<?= htmlspecialchars($fechaEntregaEstimada ?? "") ?>">
            </div>

            <div class="form-group">
                <label class="label">¿En cuántos pagos?</label>
                <input
                    type="number"
                    id="plazos-numero"
                    name="numero_pagos"
                    class="input plazos-numero-input"
                    min="1"
                    max="24"
                    value="1"
                    step="1">
            </div>
        </div>

        <div class="invoice-plazos-summary">
            <div class="invoice-plazos-mini-card">
                <span>Porcentaje pagado</span>
                <strong id="minimo-requerido-view">0%</strong>
            </div>

            <div class="invoice-plazos-mini-card">
                <span>Saldo pendiente</span>
                <strong id="saldo-pendiente-view">C$ 0.00</strong>
            </div>

            <div class="invoice-plazos-mini-card">
                <span>Estado</span>
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
                <strong>Pago parcial</strong>
                <p>Se configurará un plan de plazos para el saldo pendiente.</p>
            </div>
        </div>

        <div class="invoice-plazos-table-wrapper" id="plazos-table-wrapper" style="display: none;">
            <table class="invoice-plazos-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Porcentaje %</th>
                        <th>Monto C$</th>
                        <th>Fecha de pago</th>
                        <th>Observaciones</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="plazos-tbody">
                </tbody>
            </table>
        </div>

        <input type="hidden" name="plazos_data" id="plazos-data-input" value="">
    </div>

</section>