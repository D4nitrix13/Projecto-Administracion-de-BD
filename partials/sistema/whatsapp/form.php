<section class="dashboard-card">

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form action="cambiar_numero_de_whatsapp.php" method="POST" class="form-grid">

        <div class="form-group">
            <label class="label">Número actual</label>

            <input
                type="text"
                class="input"
                value="<?= htmlspecialchars($numeroActual) ?>"
                readonly>
        </div>

        <div class="form-group">
            <label class="label">Nuevo número de WhatsApp (*)</label>

            <input
                type="text"
                name="numero"
                class="input"
                placeholder="Ej: +505 7696 3266"
                required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                Guardar cambios
            </button>
        </div>

    </form>

</section>