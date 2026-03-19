<?php
session_start();
$pageTitle = "Acceso de Trabajadores Panda Estampados / Kitsune";

$error   = $_SESSION["error"]   ?? null;
$success = $_SESSION["success"] ?? null;

unset($_SESSION["error"]);
unset($_SESSION["success"]);
?>
<!DOCTYPE html>
<html lang="es">

<?php require "partials/header.php"; ?>


<body class="page-bg">

    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-icon">🔒</div>

            <h2 class="login-title">Acceso de Trabajadores</h2>
            <p class="login-subtitle">Inicie sesión con su cuenta autorizada.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <label class="label">Email</label>
                <input
                    type="email"
                    name="email"
                    class="input"
                    placeholder="Ingrese su email"
                    required>

                <label class="label">Contraseña</label>
                <input
                    type="password"
                    name="password"
                    class="input"
                    placeholder="Ingrese su contraseña"
                    required>

                <button type="submit" class="btn-primary">
                    Iniciar Sesión
                </button>
            </form>

            <a href="/" class="back-link">← Volver a la página principal</a>
        </div>
    </div>

</body>

</html>