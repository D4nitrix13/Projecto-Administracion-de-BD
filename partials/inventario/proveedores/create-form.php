<section>
    <h2 class="dashboard-card-title" style="margin-bottom:10px;">
        Agregar nuevo proveedor
    </h2>

    <form action="proveedores.php" method="POST" class="form-grid" style="margin-bottom:18px;">
        <?= csrfField() ?>

        <div class="form-group">
            <label class="label">Nombre del proveedor *</label>

            <input
                type="text"
                name="nombre"
                class="input"
                maxlength="120"
                required
                placeholder="Ej. Textiles Managua S.A.">
        </div>

        <div class="form-group">
            <label class="label">Teléfono</label>

            <input
                type="text"
                name="telefono"
                class="input"
                maxlength="30"
                placeholder="Ej. +505 8888 8888">
        </div>

        <div class="form-group">
            <label class="label">Email</label>

            <input
                type="email"
                name="email"
                class="input"
                maxlength="120"
                placeholder="Ej. contacto@proveedor.com">
        </div>

        <div class="form-group">
            <label class="label">Dirección</label>

            <input
                type="text"
                name="direccion"
                class="input"
                maxlength="200"
                placeholder="Ciudad, barrio, referencia...">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                Guardar proveedor
            </button>
        </div>
    </form>
</section>