<section class="dashboard-card supplier-edit-card">

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="editar_proveedor.php" method="POST" class="supplier-edit-form">
        <?= csrfField() ?>
        <input
            type="hidden"
            name="id_proveedor"
            value="<?= (int)$proveedor["id_proveedor"] ?>">

        <section class="supplier-edit-section">
            <div class="supplier-edit-section-header">
                <h2>Datos del proveedor</h2>
                <p>Actualice los datos de contacto y referencia del proveedor.</p>
            </div>

            <div class="supplier-edit-grid">
                <div class="form-group">
                    <label class="label">Nombre del proveedor *</label>

                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($proveedor["nombre"]) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Teléfono</label>

                    <input
                        type="text"
                        name="telefono"
                        class="input"
                        maxlength="30"
                        placeholder="Ej. +505 8888 8888"
                        value="<?= htmlspecialchars($proveedor["telefono"] ?? "") ?>">
                </div>

                <div class="form-group">
                    <label class="label">Email</label>

                    <input
                        type="email"
                        name="email"
                        class="input"
                        maxlength="120"
                        placeholder="Ej. contacto@proveedor.com"
                        value="<?= htmlspecialchars($proveedor["email"] ?? "") ?>">
                </div>

                <div class="form-group form-group-full">
                    <label class="label">Dirección</label>

                    <input
                        type="text"
                        name="direccion"
                        class="input"
                        maxlength="200"
                        placeholder="Ciudad, barrio, referencia..."
                        value="<?= htmlspecialchars($proveedor["direccion"] ?? "") ?>">
                </div>
            </div>
        </section>

        <div class="supplier-edit-actions">
            <a href="proveedores.php" class="supplier-btn supplier-btn-cancel">
                Cancelar
            </a>

            <button type="submit" class="supplier-btn supplier-btn-save">
                Guardar cambios
            </button>
        </div>
    </form>

</section>