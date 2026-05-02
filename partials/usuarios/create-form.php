<section class="usuarios-section">
    <h2 class="dashboard-card-title" style="margin-bottom:10px;">
        Registrar nuevo trabajador
    </h2>

    <form action="usuarios.php" method="POST" class="form-grid">

        <div class="form-group">
            <label class="label">Nombre completo *</label>

            <input
                type="text"
                name="nombre"
                class="input"
                required
                maxlength="100"
                placeholder="Nombre y apellido">
        </div>

        <div class="form-group">
            <label class="label">Email *</label>

            <input
                type="email"
                name="email"
                class="input"
                required
                maxlength="120"
                placeholder="correo@ejemplo.com">
        </div>

        <div class="form-group">
            <label class="label">Contraseña *</label>

            <input
                type="password"
                name="password"
                class="input"
                required
                minlength="6"
                placeholder="Contraseña inicial">
        </div>

        <div class="form-group">
            <label class="label">Rol *</label>

            <select name="id_rol" id="id_rol" class="input" required>
                <option value="">Seleccione un rol</option>

                <?php foreach ($roles as $rol): ?>
                    <option value="<?= (int)$rol["id_rol"] ?>">
                        <?= htmlspecialchars($rol["nombre"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <p class="dashboard-muted" style="font-size:12px;margin-top:4px;">
                La sección se asigna automáticamente según el rol.
            </p>
        </div>

        <div class="form-group">
            <label class="label">Sección asignada</label>

            <input
                type="text"
                id="seccion_info"
                class="input"
                value="Seleccione un rol"
                readonly>

            <p class="dashboard-muted" style="font-size:12px;margin-top:4px;">
                Admin: todas las secciones. Supervisor y Facturador: Kitsune.
            </p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                Guardar trabajador
            </button>
        </div>
    </form>
</section>