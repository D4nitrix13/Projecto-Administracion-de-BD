<?php

session_start();

// If already logged in, redirect to dashboard immediately
if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . "/bootstrap.php";

use App\Controller\ForgotPasswordController;

$pageTitle = "Recuperar Contraseña — Panda Estampados / Kitsune";

$controller = new ForgotPasswordController();
$viewData = $controller->handleRequest();

$error  = $viewData["error"];
$success = $viewData["success"];
$sent   = $viewData["sent"];
?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/login/styles.php"; ?>

<body class="login-page">

    <main class="login-wrapper">
        <section class="login-card">
            <div class="login-brand">
                <img src="assets/img/icono.png" alt="Logo Panda Estampados / Kitsune">
            </div>

            <h1 class="login-title">Recuperar Contraseña</h1>

            <p class="login-subtitle">
                Ingrese su correo electrónico y le enviaremos un enlace para restablecer su contraseña.
            </p>

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

            <?php if (!$sent): ?>
            <form action="forgot_password.php" method="POST" class="login-form">
                <?= csrfField() ?>
                <div class="form-group">
                    <label for="email" class="label">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="input"
                        placeholder="Ingrese su email"
                        autocomplete="email"
                        required>
                </div>

                <button type="submit" class="btn-primary">
                    Enviar enlace de recuperación
                </button>
            </form>
            <?php endif; ?>

            <a href="login.php" class="back-link">
                ← Volver al inicio de sesión
            </a>
        </section>
    </main>

</body>

</html>
