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
            <h3>Configure los plazos</h3>
        </div>

        <div class="invoice-plazos-info">
            <p>La factura se creará sin pago inicial. El primer abono se registra desde el detalle de la factura, y producción se iniciará automáticamente al alcanzar el 50%.</p>

            <div class="plazos-info-cards">
                <div class="plazos-info-card">
                    <div class="plazos-info-card-icon plazos-info-icon-help">?</div>
                    <div class="plazos-info-card-content">
                        <strong>¿Cómo funciona?</strong>
                        <p>Configure en cuántos pagos fraccionados desea dividir el saldo pendiente. Las fechas se distribuyen automáticamente cada 15 días.</p>
                    </div>
                </div>

                <div class="plazos-info-card">
                    <div class="plazos-info-card-icon plazos-info-icon-produccion">P</div>
                    <div class="plazos-info-card-content">
                        <strong>Inicio de producción</strong>
                        <p>La producción comienza cuando el cliente paga al menos el 50% del total de la factura.</p>
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
                    Puede configurar entre 1 y 24 cuotas. Las fechas se distribuyen cada 15 días calendario.
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
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 14px;
    }

    .plazos-info-card {
        display: flex;
        gap: 10px;
        padding: 12px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
    }

    .plazos-info-card-icon {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.8rem;
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
        font-size: 0.85rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .plazos-info-card-content p {
        margin: 0;
        color: #6b7280;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    .plazos-help-text {
        display: block;
        margin-top: 6px;
        color: #6b7280;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    @media (max-width: 640px) {
        .plazos-info-cards {
            grid-template-columns: 1fr;
        }
    }
</style>