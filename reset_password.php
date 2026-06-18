<?php

session_start();

// If already logged in, redirect to dashboard immediately
if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . "/bootstrap.php";

use App\Controller\ResetPasswordController;

$pageTitle = "Restablecer Contraseña — Panda Estampados / Kitsune";

$controller = new ResetPasswordController();

$token   = trim($_GET["token"] ?? $_POST["token"] ?? "");
$error   = null;
$success = null;
$message = null;
$user    = null;
$validToken = false;

// Validate token on page load
if ($token !== "") {
    $user = $controller->validateToken($token);
    $validToken = $user !== null;

    if (!$validToken) {
        $error = "El enlace no es válido o ha expirado. Solicite uno nuevo.";
    }
} else {
    $error = "No se proporcionó un enlace de recuperación válido.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrfRequire();

    $newPassword    = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $postToken      = $_POST["token"] ?? "";

    $result = $controller->handleReset($postToken, $newPassword, $confirmPassword);

    if ($result["success"]) {
        $success = $result["message"];
        $validToken = false; // Hide form after successful reset
    } else {
        $error = $result["error"];
    }
}
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

            <h1 class="login-title">Restablecer Contraseña</h1>

            <?php if ($user && $validToken): ?>
            <p class="login-subtitle">
                Hola, <strong><?= htmlspecialchars($user["nombre"]) ?></strong>. Ingrese su nueva contraseña.
            </p>
            <?php endif; ?>

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

            <?php if ($validToken && $user): ?>
            <form action="reset_password.php" method="POST" class="login-form">
                <?= csrfField() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="form-group">
                    <label for="new_password" class="label">Nueva contraseña</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        class="input"
                        placeholder="Mínimo 8 caracteres"
                        minlength="8"
                        required>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="label">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="input"
                        placeholder="Repita su contraseña"
                        minlength="8"
                        required>
                </div>

                <button type="submit" class="btn-primary">
                    Cambiar contraseña
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
