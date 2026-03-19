<?php
session_start();
$pageTitle = "Cambiar número de WhatsApp - Panda Estampados / Kitsune";

// Proteger la página (solo Admin)
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$idRol = (int)$user["id_rol"];

// Solo Admin
if ($idRol !== 1) {
    $_SESSION["flash_error"] = "No tienes permisos para modificar esta configuración.";
    header("Location: index.php");
    exit();
}

$archivoWhatsApp = __DIR__ . "/numero_de_whatsapp.txt";
$error = null;
$success = null;

// Leer número actual
$numeroActual = "No configurado";
if (is_readable($archivoWhatsApp)) {
    $numeroActual = trim(file_get_contents($archivoWhatsApp));
}

// Si envían formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevoNumero = trim($_POST["numero"] ?? "");

    if ($nuevoNumero === "") {
        $error = "El número no puede estar vacío.";
    } else {

        // Validar formato EXACTO permitido:
        // +505 7696 3266
        // +50576963266
        // +505 76963266
        // 5057696 3266
        // 505 7696 3266
        $regex = "/^(?:\\+?505)?\\s?\\d{4}\\s?\\d{4}$/";

        if (!preg_match($regex, $nuevoNumero)) {
            $error = "Formato inválido. Ejemplos válidos: +505 7696 3266, +50576963266, 50576963266.";
        } else {

            // Limpieza opcional: quitar espacios antes de guardar
            $numeroLimpio = str_replace(" ", "", $nuevoNumero);

            // Guardar número
            if (file_put_contents($archivoWhatsApp, $numeroLimpio) !== false) {
                $success = "Número de WhatsApp actualizado correctamente.";
                $numeroActual = $numeroLimpio;
            } else {
                $error = "No se pudo guardar el nuevo número. Verifica permisos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include "partials/navbar.php"; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Sistema</p>
            <h1 class="dashboard-title">Cambiar número de WhatsApp</h1>
            <p class="dashboard-muted">Este número será usado en el catálogo público para contactar por WhatsApp.</p>

            <a href="dashboard.php" class="back-link" style="margin-top:10px;">← Volver al inicio</a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="cambiar_numero_de_whatsapp.php" method="POST" class="form-grid">

                <div class="form-group">
                    <label class="label">Número actual</label>
                    <input type="text" class="input" value="<?= htmlspecialchars($numeroActual) ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="label">Nuevo número de WhatsApp (*)</label>
                    <input
                        type="text"
                        name="numero"
                        class="input"
                        placeholder="Ej: +505 7696 3266"
                        required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>
                </div>

            </form>

        </section>

    </main>

</body>

</html>