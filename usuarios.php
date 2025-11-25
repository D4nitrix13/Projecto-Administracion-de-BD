<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
/** @var PDO $connection */
$connection = require "./sql/db.php";

// Solo administradores pueden gestionar trabajadores
if (!isset($user["id_rol"]) || (int)$user["id_rol"] !== 1) {
    header("Location: acceso_restringido.php");
    exit();
}

$pageTitle = "Trabajadores - Panda Estampados / Kitsune";

// Mensajes flash
$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error   = $_SESSION["flash_error"]   ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$error = null;

/* ==============================
 * 1) LECTURA DE FILTROS (GET)
 * ============================== */
$busqueda       = trim($_GET['q'] ?? "");
$rolFiltro      = $_GET['rol'] ?? "";
$seccionFiltro  = $_GET['seccion'] ?? "";

// normalizar rol (solo números válidos)
$rolFiltroInt = ctype_digit($rolFiltro) ? (int)$rolFiltro : null;

/* ==============================
 * 2) CARGAR ROLES Y SECCIONES
 * ============================== */
$stmtRoles = $connection->query("SELECT id_rol, nombre FROM Rol ORDER BY nombre");
$roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

$stmtSec = $connection->query("SELECT id_seccion, nombre FROM Seccion ORDER BY nombre");
$secciones = $stmtSec->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
 * 3) ALTA DE NUEVO TRABAJADOR
 * ============================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre      = trim($_POST["nombre"] ?? "");
    $email       = trim($_POST["email"] ?? "");
    $password    = trim($_POST["password"] ?? "");
    $id_rol      = isset($_POST["id_rol"]) ? (int)$_POST["id_rol"] : 0;

    // La sección se asigna AUTOMÁTICAMENTE según el rol
    if ($id_rol === 1) {
        // Administrador: todas las secciones (NULL)
        $id_seccion = null;
    } elseif ($id_rol === 2 || $id_rol === 3) {
        // Supervisor y Facturador: Kitsune (id_seccion = 2)
        $id_seccion = 2;
    } else {
        $id_seccion = null;
    }

    if ($nombre === "" || $email === "" || $password === "" || $id_rol <= 0) {
        $error = "Nombre, email, contraseña y rol son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no tiene un formato válido.";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        try {
            $passwordToStore = password_hash($password, PASSWORD_DEFAULT);

            $stmtIns = $connection->prepare("
                INSERT INTO Usuario (nombre, email, password, id_rol, id_seccion)
                VALUES (:nombre, :email, :password, :id_rol, :id_seccion)
            ");
            $stmtIns->execute([
                ":nombre"     => $nombre,
                ":email"      => $email,
                ":password"   => $passwordToStore,
                ":id_rol"     => $id_rol,
                ":id_seccion" => $id_seccion,
            ]);

            $_SESSION["flash_success"] = "Trabajador registrado correctamente.";
            header("Location: usuarios.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe un usuario con ese email.";
            } else {
                $error = "Error al registrar el trabajador: " . $e->getMessage();
            }
        }
    }
}

/* ==============================
 * 4) LISTAR USUARIOS CON FILTRO
 * ============================== */

$sqlUsuarios = "
    SELECT u.id_usuario,
           u.nombre,
           u.email,
           r.nombre AS rol,
           s.nombre AS seccion,
           u.id_seccion
    FROM Usuario u
    INNER JOIN Rol r ON u.id_rol = r.id_rol
    LEFT JOIN Seccion s ON u.id_seccion = s.id_seccion
    WHERE u.id_usuario <> 1
";
$params = [];

// filtro texto (nombre o email)
if ($busqueda !== "") {
    $sqlUsuarios .= " AND (u.nombre ILIKE :q OR u.email ILIKE :q)";
    $params[':q'] = '%' . $busqueda . '%';
}

// filtro rol
if (!is_null($rolFiltroInt)) {
    $sqlUsuarios .= " AND u.id_rol = :rol";
    $params[':rol'] = $rolFiltroInt;
}

// filtro sección
if ($seccionFiltro === 'none') {
    $sqlUsuarios .= " AND u.id_seccion IS NULL";
} elseif (ctype_digit($seccionFiltro)) {
    $sqlUsuarios .= " AND u.id_seccion = :sec";
    $params[':sec'] = (int)$seccionFiltro;
}

$sqlUsuarios .= " ORDER BY u.nombre";

$stmtUsuarios = $connection->prepare($sqlUsuarios);
$stmtUsuarios->execute($params);
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<?php require __DIR__ . "/partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . "/partials/navbar.php"; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Seguridad</p>
            <h1 class="dashboard-title">Trabajadores</h1>
            <p class="dashboard-muted">
                Gestione las cuentas de los trabajadores, sus roles y secciones.
            </p>
        </section>

        <section class="dashboard-card">

            <?php if ($flash_error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>

            <?php if ($flash_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

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
                            <option value="<?= (int)$rol['id_rol'] ?>">
                                <?= htmlspecialchars($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="dashboard-muted" style="font-size:12px;margin-top:4px;">
                        La sección se asigna automáticamente según el rol.
                    </p>
                </div>

                <!-- Sección solo informativa, se actualiza con JS -->
                <div class="form-group">
                    <label class="label">Sección</label>
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

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">

            <!-- FILTROS DE TRABAJADORES -->
            <form method="get" class="proveedores-filtros-bar" style="margin-bottom:16px;">
                <div class="filtro-item-full">
                    <label for="q" class="label">Buscar trabajador</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="input"
                        placeholder="Nombre o email..."
                        value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <div class="filtro-item">
                    <label for="rol" class="label">Rol</label>
                    <select id="rol" name="rol" class="input">
                        <option value="">Todos los roles</option>
                        <?php foreach ($roles as $rol): ?>
                            <option
                                value="<?= (int)$rol['id_rol'] ?>"
                                <?= ($rolFiltroInt === (int)$rol['id_rol']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="seccion" class="label">Sección</label>
                    <select id="seccion" name="seccion" class="input">
                        <option value="">Cualquier sección</option>
                        <option value="none" <?= $seccionFiltro === 'none' ? 'selected' : '' ?>>
                            Sin sección (todas las secciones)
                        </option>
                        <?php foreach ($secciones as $sec): ?>
                            <option
                                value="<?= (int)$sec['id_seccion'] ?>"
                                <?= ($seccionFiltro === (string)(int)$sec['id_seccion']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sec['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro-actions">
                    <button type="submit" class="btn-primary-inline">
                        Aplicar filtros
                    </button>
                    <a href="usuarios.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>

            <h2 class="dashboard-card-title" style="margin-bottom:8px;">
                Listado de trabajadores
            </h2>

            <?php if (empty($usuarios)): ?>
                <p class="dashboard-muted">No se encontraron trabajadores con los filtros aplicados.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-products">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Sección</th>
                                <th class="col-acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u["nombre"]) ?></td>
                                    <td><?= htmlspecialchars($u["email"]) ?></td>
                                    <td><?= htmlspecialchars($u["rol"]) ?></td>
                                    <td>
                                        <?php if ($u["rol"] === 'Administrador'): ?>
                                            Todas las secciones
                                        <?php else: ?>
                                            Kitsune
                                        <?php endif; ?>
                                    </td>
                                    <td class="acciones">
                                        <a
                                            href="editar_usuario.php?id=<?= (int)$u['id_usuario'] ?>"
                                            class="btn-accion btn-accion-editar">
                                            Editar
                                        </a>
                                        <a
                                            href="eliminar_usuario.php?id=<?= (int)$u['id_usuario'] ?>"
                                            class="btn-accion btn-accion-eliminar"
                                            onclick="return confirm('¿Seguro que desea eliminar este trabajador?');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

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
            actualizarSeccion();
        });
    </script>
</body>

</html>