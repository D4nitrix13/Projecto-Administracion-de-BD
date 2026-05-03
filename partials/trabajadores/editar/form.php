<section class="dashboard-card user-edit-card">

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="editar_trabajador.php" method="POST" class="form-grid user-edit-form">
        <input
            type="hidden"
            name="id_usuario"
            value="<?= (int)$trabajador["id_usuario"] ?>">

        <div class="form-group">
            <label class="label">Nombre completo *</label>

            <input
                type="text"
                name="nombre"
                class="input"
                required
                maxlength="100"
                value="<?= htmlspecialchars($trabajador["nombre"]) ?>">
        </div>

        <div class="form-group">
            <label class="label">Email *</label>

            <input
                type="email"
                name="email"
                class="input"
                required
                maxlength="120"
                value="<?= htmlspecialchars($trabajador["email"]) ?>">
        </div>

        <div class="form-group">
            <label class="label">Contraseña nueva</label>

            <input
                type="password"
                name="password"
                class="input"
                minlength="6"
                placeholder="Dejar en blanco para no cambiar">

            <p class="dashboard-muted user-edit-help">
                Solo escriba una contraseña si desea reemplazar la actual.
            </p>
        </div>

        <div class="form-group">
            <label class="label">Rol *</label>

            <select name="id_rol" id="id_rol" class="input" required>
                <option value="">Seleccione un rol</option>

                <?php foreach ($roles as $rol): ?>
                    <option
                        value="<?= (int)$rol["id_rol"] ?>"
                        <?= ((int)$trabajador["id_rol"] === (int)$rol["id_rol"]) ? "selected" : "" ?>>
                        <?= htmlspecialchars($rol["nombre"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <p class="dashboard-muted user-edit-help">
                La sección se asigna automáticamente según el rol.
            </p>
        </div>

        <div class="form-group">
            <label class="label">Sección asignada</label>

            <input
                type="text"
                id="seccion_info"
                class="input"
                value="<?= htmlspecialchars($seccionTextoActual) ?>"
                readonly>

            <p class="dashboard-muted user-edit-help">
                Admin: todas las secciones. Supervisor y Facturador: Kitsune.
            </p>
        </div>
        <div class="user-edit-actions">
            <a href="trabajadores.php" class="user-edit-btn user-edit-btn-cancel">
                Cancelar
            </a>

            <button type="submit" class="user-edit-btn user-edit-btn-save">
                Guardar cambios
            </button>
        </div>
    </form>

</section>