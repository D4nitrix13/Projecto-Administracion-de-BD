<?php
session_start();
$pageTitle = "Configurar cuenta - Panda Estampados / Kitsune";

// Proteger la página
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

$error = null;
$success = null;

// Cargar datos frescos del usuario desde la BD
$stmt = $connection->prepare("
    SELECT 
        u.id_usuario,
        u.nombre,
        u.email,
        u.password,
        u.id_seccion,
        s.nombre AS seccion_nombre
    FROM Usuario u
    LEFT JOIN Seccion s ON u.id_seccion = s.id_seccion
    WHERE u.id_usuario = :id
");
$stmt->execute([":id" => (int)$user["id_usuario"]]);
$dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dbUser) {
    // Algo raro: el usuario ya no existe
    session_destroy();
    header("Location: login.php");
    exit();
}

// Valores iniciales
$nombre          = $dbUser["nombre"];
$email           = $dbUser["email"];
$seccionNombreBD = $dbUser["seccion_nombre"];
$id_seccion      = $dbUser["id_seccion"]; // puede ser null

if ($id_seccion === null) {
    $seccionTexto = "Administrador general de todas las secciones";
} else {
    $seccionTexto = $seccionNombreBD ?: "Sección asignada";
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre  = trim($_POST["nombre"] ?? "");
    $email   = trim($_POST["email"] ?? "");
    $pass_actual   = $_POST["password_actual"] ?? "";
    $pass_nueva    = $_POST["password_nueva"] ?? "";
    $pass_confirm  = $_POST["password_confirm"] ?? "";

    // Validaciones básicas de nombre y correo
    if ($nombre === "") {
        $error = "El nombre no puede estar vacío.";
    } elseif ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Debe ingresar un correo electrónico válido.";
    }

    // Manejo de cambio de contraseña (opcional)
    $passwordHashFinal = $dbUser["password"]; // por defecto se mantiene

    // Si alguno de los campos de contraseña tiene algo, se interpreta como cambio
    $quiereCambiarPass = ($pass_actual !== "" || $pass_nueva !== "" || $pass_confirm !== "");

    if ($error === null && $quiereCambiarPass) {
        if ($pass_actual === "" || $pass_nueva === "" || $pass_confirm === "") {
            $error = "Para cambiar la contraseña debe completar todos los campos de contraseña.";
        } elseif (!password_verify($pass_actual, $dbUser["password"])) {
            $error = "La contraseña actual no es correcta.";
        } elseif (strlen($pass_nueva) < 8) {
            $error = "La nueva contraseña debe tener al menos 8 caracteres.";
        } elseif ($pass_nueva !== $pass_confirm) {
            $error = "La confirmación de contraseña no coincide.";
        } else {
            // Todo OK, generamos el nuevo hash
            $passwordHashFinal = password_hash($pass_nueva, PASSWORD_BCRYPT, ["cost" => 12]);
        }
    }

    if ($error === null) {
        try {
            $stmtUpd = $connection->prepare("
                UPDATE Usuario
                SET nombre = :nombre,
                    email  = :email,
                    password = :password
                WHERE id_usuario = :id
            ");

            $stmtUpd->execute([
                ":nombre"   => $nombre,
                ":email"    => $email,
                ":password" => $passwordHashFinal,
                ":id"       => (int)$dbUser["id_usuario"],
            ]);

            // Actualizar también la sesión
            $_SESSION["user"]["nombre"] = $nombre;
            $_SESSION["user"]["email"]  = $email;

            $success = "Datos de cuenta actualizados correctamente.";
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                // Violación de UNIQUE (probablemente email repetido)
                $error = "Ya existe un usuario registrado con ese correo electrónico.";
            } else {
                $error = "Error al actualizar la cuenta: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Configuración</p>
            <h1 class="dashboard-title">Configurar cuenta</h1>

            <p class="dashboard-muted">
                Actualice sus datos personales y credenciales de acceso.
            </p>

            <a href="dashboard.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver al panel principal
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="configurar_cuenta.php" method="POST">

                <!-- Datos básicos -->
                <h2 style="margin-bottom:12px;">Datos personales</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="label">Nombre completo</label>
                        <input
                            type="text"
                            name="nombre"
                            class="input"
                            maxlength="100"
                            required
                            value="<?= htmlspecialchars($nombre) ?>">
                    </div>

                    <div class="form-group">
                        <label class="label">Correo electrónico</label>
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
                    </div>
                </div>

                <!-- Cambio de contraseña -->
                <h2 style="margin-top:24px; margin-bottom:12px;">Cambiar contraseña</h2>
                <p class="dashboard-muted" style="margin-bottom:16px;">
                    Estos campos son opcionales. Complete todos si desea cambiar su contraseña.
                </p>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="label">Contraseña actual</label>
                        <input
                            type="password"
                            name="password_actual"
                            class="input"
                            autocomplete="current-password">
                    </div>

                    <div class="form-group">
                        <label class="label">Nueva contraseña</label>
                        <input
                            type="password"
                            name="password_nueva"
                            class="input"
                            autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label class="label">Confirmar nueva contraseña</label>
                        <input
                            type="password"
                            name="password_confirm"
                            class="input"
                            autocomplete="new-password">
                    </div>
                </div>

                <div class="form-actions" style="margin-top:24px;">
                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </section>

    </main>

</body>

</html>