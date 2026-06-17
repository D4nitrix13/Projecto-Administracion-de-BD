<section class="config-layout">

    <article class="config-card">
        <div class="config-card-header">
            <div>
                <span class="config-badge config-badge-info">Parámetros</span>

                <h2>Límite para cliente fugaz</h2>

                <p>
                    Defina el monto máximo que puede facturar un cliente fugaz antes de exigir registro como cliente habitual.
                </p>
            </div>
        </div>

        <form method="POST" class="config-form">
        <?= csrfField() ?>
            <input type="hidden" name="action" value="save_config">

            <div class="config-field">
                <label for="limite_de_venta_cliente_fugaz">Monto máximo permitido</label>

                <div class="config-money-field">
                    <span>C$</span>

                    <input
                        type="number"
                        name="limite_de_venta_cliente_fugaz"
                        id="limite_de_venta_cliente_fugaz"
                        min="1"
                        max="999999"
                        step="0.01"
                        inputmode="decimal"
                        value="<?= htmlspecialchars(number_format($limiteClienteFugaz, 2, ".", "")) ?>"
                        required>
                </div>

                <small>
                    Valor actual: C$ <?= htmlspecialchars(number_format($limiteClienteFugaz, 2)) ?>.
                </small>
            </div>

            <div class="config-note">
                <strong>¿Para qué sirve?</strong>

                <p>
                    Si una venta supera este monto y el cliente es fugaz, el sistema solicitará registrar al cliente como habitual.
                </p>
            </div>

            <div class="config-actions">
                <button type="submit" class="config-primary-button">
                    Guardar configuración
                </button>
            </div>
        </form>
    </article>

    <aside class="config-side">

        <article class="config-side-card">
            <span class="config-badge config-badge-info">Información</span>

            <h2>¿Qué hace este límite?</h2>

            <p>
                Este parámetro controla el monto máximo permitido para ventas realizadas a clientes fugaces.
            </p>

            <div class="config-info-list">
                <div>
                    <span>Límite actual</span>

                    <strong>
                        C$ <?= htmlspecialchars(number_format($limiteClienteFugaz, 2)) ?>
                    </strong>
                </div>

                <div>
                    <span>Tipo de cliente</span>
                    <strong>Cliente fugaz</strong>
                </div>

                <div>
                    <span>Acción del sistema</span>
                    <strong>Solicitar registro como cliente habitual</strong>
                </div>
            </div>
        </article>

        <article class="config-side-card config-danger-card">
            <span class="config-badge config-badge-danger">Restablecer</span>

            <h2>Valores por defecto</h2>

            <p>
                Devuelve el límite del cliente fugaz a C$ 1,000.00.
            </p>

            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="reset_config">

                <button
                    type="submit"
                    class="config-danger-button"
                    onclick="return confirm('¿Desea restablecer la configuración por defecto?');">
                    Restablecer configuración
                </button>
            </form>
        </article>

    </aside>

</section>