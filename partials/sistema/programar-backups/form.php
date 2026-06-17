<section class="programar-card programar-form-card">
    <div class="programar-card-header">
        <div>
            <span class="programar-kicker">Configuración</span>

            <h2>Backup automático</h2>

            <p>
                Defina el tipo de respaldo y cada cuánto debe ejecutarse la programación automática.
            </p>
        </div>
    </div>

    <form method="POST" class="programar-form">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save_schedule">

        <div class="programar-field">
            <label>Estado de la programación</label>

            <div class="programar-radio-grid">
                <label class="programar-radio-card">
                    <input
                        type="radio"
                        name="enabled"
                        value="1"
                        <?= $enabled ? "checked" : "" ?>>

                    <span>
                        <strong>Activado</strong>
                        <small>Permite ejecutar backups automáticos según la frecuencia configurada.</small>
                    </span>
                </label>

                <label class="programar-radio-card">
                    <input
                        type="radio"
                        name="enabled"
                        value="0"
                        <?= !$enabled ? "checked" : "" ?>>

                    <span>
                        <strong>Desactivado</strong>
                        <small>Pausa temporalmente la programación automática.</small>
                    </span>
                </label>
            </div>
        </div>

        <div class="programar-field">
            <label for="type">Tipo de backup</label>

            <select name="type" id="type" required>
                <?php foreach ($tiposBackup as $value => $label): ?>
                    <option
                        value="<?= htmlspecialchars($value) ?>"
                        <?= $type === $value ? "selected" : "" ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <small>
                Elija el tipo de respaldo que desea generar automáticamente. La opción más completa es “Mantenimiento completo”.
            </small>

            <div class="programar-type-help">
                <strong>¿Qué significa cada opción?</strong>

                <ul>
                    <li>
                        <b>Respaldo completo:</b>
                        guarda una copia de toda la base de datos.
                    </li>

                    <li>
                        <b>Respaldo rápido de datos importantes:</b>
                        guarda solo la información principal que cambia constantemente.
                    </li>

                    <li>
                        <b>Completo + datos importantes:</b>
                        crea los dos respaldos anteriores.
                    </li>

                    <li>
                        <b>Mantenimiento completo:</b>
                        crea respaldos y también guarda registros del sistema.
                    </li>
                </ul>
            </div>
        </div>

        <div class="programar-form-row">
            <div class="programar-field">
                <label for="interval_value">Cada cuánto</label>

                <input
                    type="number"
                    name="interval_value"
                    id="interval_value"
                    min="1"
                    max="999"
                    value="<?= htmlspecialchars((string)$intervalValue) ?>"
                    required>
            </div>

            <div class="programar-field">
                <label for="interval_unit">Unidad de tiempo</label>

                <select name="interval_unit" id="interval_unit" required>
                    <?php foreach ($unidadesIntervalo as $value => $label): ?>
                        <option
                            value="<?= htmlspecialchars($value) ?>"
                            <?= $intervalUnit === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="programar-note">
            <strong>Información importante</strong>

            <p>
                Esta configuración define la frecuencia con la que el sistema preparará los respaldos automáticos de la base de datos.
            </p>
        </div>

        <div class="programar-actions">
            <button type="submit" class="programar-primary-button">
                Guardar programación
            </button>
        </div>
    </form>
</section>