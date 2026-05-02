<section class="dashboard-card account-settings-card">

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

    <form action="configurar_cuenta.php" method="POST" class="account-settings-form">

        <section class="account-section">
            <div class="account-section-header">
                <h2>Datos personales</h2>
                <p>Información básica asociada a su cuenta de usuario.</p>
            </div>

            <div class="account-form-grid">
                <div class="form-group">
                    <label class="label">Nombre completo *</label>

                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="100"
                        required
                        value="<?= htmlspecialchars($nombre) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Correo electrónico *</label>

                    <input
                        type="email"
                        name="email"
                        class="input"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($email) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Sección asignada</label>

                    <input
                        type="text"
                        class="input"
                        value="<?= htmlspecialchars($seccionTexto) ?>"
                        disabled>

                    <p class="dashboard-muted account-help">
                        Este dato depende del rol asignado por el administrador.
                    </p>
                </div>
            </div>
        </section>

        <section class="account-section">
            <div class="account-section-header">
                <h2>Cambiar contraseña</h2>
                <p>Complete estos campos únicamente si desea reemplazar su contraseña actual.</p>
            </div>

            <div class="account-form-grid">
                <div class="form-group">
                    <label class="label">Contraseña actual</label>

                    <input
                        type="password"
                        name="password_actual"
                        class="input"
                        autocomplete="current-password"
                        placeholder="Contraseña actual">
                </div>

                <div class="form-group">
                    <label class="label">Nueva contraseña</label>

                    <input
                        type="password"
                        name="password_nueva"
                        class="input"
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Mínimo 8 caracteres">
                </div>

                <div class="form-group">
                    <label class="label">Confirmar nueva contraseña</label>

                    <input
                        type="password"
                        name="password_confirm"
                        class="input"
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Repita la nueva contraseña">
                </div>
            </div>
        </section>

        <div class="account-actions">
            <a href="dashboard.php" class="account-btn account-btn-cancel">
                Cancelar
            </a>

            <button type="submit" class="account-btn account-btn-save">
                Guardar cambios
            </button>
        </div>

    </form>

</section>