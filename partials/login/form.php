<form action="auth.php" method="POST" class="login-form">
    <div class="form-group">
        <label for="email" class="label">Email</label>

        <input
            type="email"
            id="email"
            name="email"
            class="input"
            placeholder="Ingrese su email"
            autocomplete="email"
            required>
    </div>

    <div class="form-group">
        <label for="password" class="label">Contraseña</label>

        <input
            type="password"
            id="password"
            name="password"
            class="input"
            placeholder="Ingrese su contraseña"
            autocomplete="current-password"
            required>
    </div>

    <button type="submit" class="btn-primary">
        Iniciar sesión
    </button>
</form>