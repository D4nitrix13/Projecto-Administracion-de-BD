<section class="dashboard-card">

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="editar_cliente.php?id=<?= (int)$id ?>" method="POST" class="form-grid">

        <div class="form-group">
            <label class="label">Nombres (*)</label>

            <input
                type="text"
                name="nombres"
                class="input"
                maxlength="80"
                required
                value="<?= htmlspecialchars($nombres) ?>">
        </div>

        <div class="form-group">
            <label class="label">Apellidos (*)</label>

            <input
                type="text"
                name="apellidos"
                class="input"
                maxlength="80"
                required
                value="<?= htmlspecialchars($apellidos) ?>">
        </div>

        <div class="form-group">
            <label class="label">Teléfono</label>

            <input
                type="text"
                name="telefono"
                class="input"
                maxlength="30"
                value="<?= htmlspecialchars($telefono ?? "") ?>">
        </div>

        <div class="form-group">
            <label class="label">Dirección</label>

            <input
                type="text"
                name="direccion"
                class="input"
                maxlength="200"
                value="<?= htmlspecialchars($direccion ?? "") ?>">
        </div>

        <div class="form-group">
            <label class="label">Identificación</label>

            <input
                type="text"
                name="identificacion"
                class="input"
                maxlength="40"
                value="<?= htmlspecialchars($identificacion ?? "") ?>">
        </div>

        <div class="form-group">
            <label class="label">Tipo de cliente</label>

            <select
                name="tipo_cliente"
                class="input"
                <?= $isRestrictedRole ? "disabled" : "" ?>>
                <option value="Detallista" <?= $tipoCliente === "Detallista" ? "selected" : "" ?>>
                    Detallista
                </option>

                <option value="Mayorista" <?= $tipoCliente === "Mayorista" ? "selected" : "" ?>>
                    Mayorista
                </option>
            </select>

            <?php if ($isRestrictedRole): ?>
                <small class="dashboard-muted" style="font-size:12px; display:block; margin-top:4px;">
                    Su rol solo permite atender clientes de tipo Detallista.
                </small>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                Guardar cambios
            </button>
        </div>

    </form>

</section>