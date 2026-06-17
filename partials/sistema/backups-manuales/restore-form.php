<section class="backup-panel backup-panel-danger">
    <div class="backup-panel-header">
        <span class="backup-panel-badge backup-badge-danger">Restaurar</span>

        <h2>Restaurar base de datos</h2>

        <p>
            Esta acción reemplaza el contenido actual de la base de datos usando un archivo existente.
        </p>
    </div>

    <form method="POST" class="backup-form">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="restore">

        <div class="form-group">
            <label class="label">Archivo de respaldo</label>

            <select name="archivo" class="input" required>
                <option value="">Seleccione archivo .sql</option>

                <?php foreach ($archivos as $archivo): ?>
                    <option value="<?= htmlspecialchars($archivo["nombre"]) ?>">
                        <?= htmlspecialchars($archivo["nombre"]) ?>
                        — <?= htmlspecialchars($archivo["fecha"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="backup-warning">
            <strong>Advertencia</strong>
            <p>
                Restaurar puede sobrescribir todos los datos actuales. Use esta opción solo si está seguro.
            </p>
        </div>

        <div class="form-group">
            <label class="label">Confirmación</label>

            <input
                type="text"
                name="confirmacion_restore"
                class="input"
                placeholder="Escriba RESTAURAR para confirmar">

            <p class="dashboard-muted backup-help">
                Esta confirmación evita restauraciones accidentales.
            </p>
        </div>

        <button type="submit" class="backup-btn backup-btn-danger">
            Restaurar base de datos
        </button>
    </form>
</section>