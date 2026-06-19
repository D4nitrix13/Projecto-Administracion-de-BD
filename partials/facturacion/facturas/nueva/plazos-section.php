<div
    class="invoice-plazos-section"
    id="invoice-plazos-section">

    <div class="invoice-plazos-header">
        <span>Plan de pagos</span>
        <h3>Configurar plazos de pago</h3>
        <p>
            Indique en cuántos pagos realizará el cliente hasta completar el saldo.
        </p>
    </div>

    <div class="invoice-plazos-summary">
        <div class="invoice-plazos-mini-card">
            <span>Saldo pendiente</span>
            <strong id="plazos-saldo-pendiente">C$ 0.00</strong>
        </div>

        <div class="invoice-plazos-mini-card">
            <span>Cantidad de pagos</span>
            <strong id="plazos-cantidad-cuotas">0</strong>
        </div>

        <div class="invoice-plazos-mini-card">
            <span>Total configurado</span>
            <strong id="plazos-total-configurado">C$ 0.00</strong>
        </div>
    </div>

    <div class="invoice-plazos-step-1">
        <div class="form-group">
            <label class="label">¿En cuántos pagos lo hará el cliente?</label>
            <input
                type="number"
                id="plazos-numero"
                class="input plazos-numero-input"
                min="1"
                max="24"
                value="2"
                step="1"
                placeholder="Ej: 3">
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