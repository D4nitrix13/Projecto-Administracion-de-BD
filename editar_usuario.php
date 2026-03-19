<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

// Solo administradores
if (($user["rol"] ?? "") !== "Administrador") {
    $_SESSION["flash_error"] = "No tiene permiso para editar trabajadores.";
    header("Location: usuarios.php");
    exit();
}

$pageTitle = "Editar trabajador - Panda Estampados / Kitsune";

// ID del trabajador a editar (por GET o POST)
$id = ($_SERVER["REQUEST_METHOD"] === "POST")
    ? (int)($_POST["id_usuario"] ?? 0)
    : (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["flash_error"] = "Trabajador no válido.";
    header("Location: usuarios.php");
    exit();
}

// Cargar roles (ya no necesitamos secciones porque son automáticas)
$stmtRoles = $connection->query("SELECT id_rol, nombre FROM Rol ORDER BY nombre");
$roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

$error = null;

/* ==============================
 *  ACTUALIZAR TRABAJADOR (POST)
 * ============================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST["nombre"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? ""); // opcional
    $id_rol   = isset($_POST["id_rol"]) ? (int)$_POST["id_rol"] : 0;

    // Asignar sección AUTOMÁTICA según rol
    if ($id_rol === 1) {
        // Administrador: todas las secciones
        $id_seccion = null;
    } elseif ($id_rol === 2 || $id_rol === 3) {
        // Supervisor y Facturador: Kitsune (id 2)
        $id_seccion = 2;
    } else {
        $id_seccion = null;
    }

    if ($nombre === "" || $email === "" || $id_rol <= 0) {
        $error = "Nombre, email y rol son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no tiene un formato válido.";
    } else {
        try {
            $updatePassword = ($password !== "");

            $sql = "
                UPDATE Usuario
                SET nombre = :nombre,
                    email = :email,
                    id_rol = :id_rol,
                    id_seccion = :id_seccion";

            if ($updatePassword) {
                $sql .= ", password = :password";
            }

            $sql .= " WHERE id_usuario = :id";

            $stmtUpd = $connection->prepare($sql);

            $params = [
                ":nombre"     => $nombre,
                ":email"      => $email,
                ":id_rol"     => $id_rol,
                ":id_seccion" => $id_seccion,
                ":id"         => $id,
            ];

            if ($updatePassword) {
                // Guardamos hash, igual que en usuarios.php
                $params[":password"] = password_hash($password, PASSWORD_DEFAULT);
            }

            $stmtUpd->execute($params);

            $_SESSION["flash_success"] = "Trabajador actualizado correctamente.";
            header("Location: usuarios.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe un usuario con ese email.";
            } else {
                $error = "Error al actualizar el trabajador: " . $e->getMessage();
            }
        }
    }
}

/* ==============================
 *  CARGAR DATOS ACTUALES
 * ============================== */
$stmtUser = $connection->prepare("
    SELECT id_usuario, nombre, email, id_rol, id_seccion
    FROM Usuario
    WHERE id_usuario = :id
");
$stmtUser->execute([":id" => $id]);
$trabajador = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) {
    $_SESSION["flash_error"] = "El trabajador especificado no existe.";
    header("Location: usuarios.php");
    exit();
}

// Texto inicial de sección para mostrar en el input readonly
if ((int)$trabajador["id_rol"] === 1) {
    $seccionTextoActual = "Todas las secciones";
} else {
    $seccionTextoActual = "Kitsune";
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require __DIR__ . "/partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . "/partials/navbar.php"; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Seguridad</p>
            <h1 class="dashboard-title">Editar trabajador</h1>
            <p class="dashboard-muted">
                Modifique los datos del trabajador y su rol. La sección se asigna automáticamente.
            </p>

            <a href="usuarios.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver a trabajadores
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="editar_usuario.php" method="POST" class="form-grid">
                <input type="hidden" name="id_usuario" value="<?= (int)$trabajador['id_usuario'] ?>">

                <div class="form-group">
                    <label class="label">Nombre completo *</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        required
                        maxlength="100"
                        value="<?= htmlspecialchars($trabajador['nombre']) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Email *</label>
                    <input
                        type="email"
                        name="email"
                        class="input"
                        required
                        maxlength="120"
                        value="<?= htmlspecialchars($trabajador['email']) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Contraseña (dejar en blanco para no cambiar)</label>
                    <input
                        type="password"
                        name="password"
                        class="input"
                        minlength="6"
                        placeholder="Nueva contraseña opcional">
                </div>

                <div class="form-group">
                    <label class="label">Rol *</label>
                    <select name="id_rol" id="id_rol" class="input" required>
                        <option value="">Seleccione un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option
                                value="<?= (int)$rol['id_rol'] ?>"
                                <?= ((int)$trabajador['id_rol'] === (int)$rol['id_rol']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="dashboard-muted" style="font-size:12px;margin-top:4px;">
                        La sección se asigna automáticamente según el rol.
                    </p>
                </div>

                <!-- Sección informativa, igual que en usuarios.php -->
                <div class="form-group">
                    <label class="label">Sección</label>
                    <input
                        type="text"
                        id="seccion_info"
                        class="input"
                        value="<?= htmlspecialchars($seccionTextoActual) ?>"
                        readonly>
                    <p class="dashboard-muted" style="font-size:12px;margin-top:4px;">
                        Admin: todas las secciones. Supervisor y Facturador: Kitsune.
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rolSelect = document.getElementById('id_rol');
            const seccionInfo = document.getElementById('seccion_info');

            if (!rolSelect || !seccionInfo) return;

            function actualizarSeccion() {
                const valor = rolSelect.value;

                if (valor === '1') {
                    seccionInfo.value = 'Todas las secciones';
                } else if (valor === '2' || valor === '3') {
                    seccionInfo.value = 'Kitsune';
                } else {
                    seccionInfo.value = 'Seleccione un rol';
                }
            }

            rolSelect.addEventListener('change', actualizarSeccion);
            // Para que coincida con el rol actual al cargar la página
            actualizarSeccion();
        });
    </script>
</body>

</html>