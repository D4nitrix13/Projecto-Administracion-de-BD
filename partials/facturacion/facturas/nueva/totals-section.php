<section class="invoice-checkout-section">

    <div class="invoice-summary-card">
        <div class="invoice-summary-header">
            <span>Resumen</span>
            <h3>Totales</h3>
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
            <h3>Plazos</h3>
        </div>

        <div class="invoice-plazos-info">
            <p>Sin pago inicial. El primer abono se registra desde el detalle de la factura.</p>

            <div class="plazos-info-cards">
                <div class="plazos-info-card">
                    <div class="plazos-info-card-icon plazos-info-icon-help">?</div>
                    <div class="plazos-info-card-content">
                        <strong>¿Cómo funciona?</strong>
                        <p>Divida el saldo en cuotas. Las fechas se distribuyen cada 15 días.</p>
                    </div>
                </div>

                <div class="plazos-info-card">
                    <div class="plazos-info-card-icon plazos-info-icon-produccion">P</div>
                    <div class="plazos-info-card-content">
                        <strong>Producción</strong>
                        <p>Comienza al pagar al menos el 50% del total.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-plazos-fields">
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
                <small class="plazos-help-text">
                    Entre 1 y 24 cuotas. Fechas cada 15 días.
                </small>
            </div>
        </div>

        <div
            class="invoice-payment-warning"
            id="invoice-payment-warning"
            style="display:none;">
            <div class="invoice-payment-warning-icon">!</div>
            <div>
                <strong>Plan de plazos</strong>
                <p>Se configurará un plan de pagos para el total de la factura.</p>
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

<style>
    .plazos-info-cards {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .plazos-info-card {
        display: flex;
        gap: 8px;
        padding: 8px 10px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        flex: 1;
        min-width: 0;
    }

    .plazos-info-card-icon {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 900;
    }

    .plazos-info-icon-help {
        background: #dbeafe;
        color: #2563eb;
    }

    .plazos-info-icon-produccion {
        background: #dcfce7;
        color: #16a34a;
    }

    .plazos-info-card-content strong {
        display: block;
        color: #111827;
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 1px;
    }

    .plazos-info-card-content p {
        margin: 0;
        color: #6b7280;
        font-size: 0.74rem;
        line-height: 1.3;
    }

    .plazos-help-text {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 0.78rem;
        line-height: 1.3;
    }

    @media (max-width: 640px) {
        .plazos-info-cards {
            flex-direction: column;
        }
    }
</style>