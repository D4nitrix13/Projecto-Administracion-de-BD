<?php
session_start();
$pageTitle = "Proveedores - Panda Estampados / Kitsune";

// Proteger la página (login)
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user  = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

/** @var PDO $connection */
$connection = require "./sql/db.php";

// Mensajes flash (para editar/eliminar)
$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error   = $_SESSION["flash_error"]   ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$error = null;
$success = null;

/* ==============================
 * 1) LECTURA DE FILTRO (GET)
 * ============================== */
$busqueda = trim($_GET['q'] ?? "");

// Procesar alta de nuevo proveedor
// SOLO Admin (1) y Supervisor (2) pueden crear
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!in_array($idRol, [1, 2], true)) {
        $error = "No tiene permisos para registrar nuevos proveedores.";
    } else {
        $nombre    = trim($_POST["nombre"] ?? "");
        $telefono  = trim($_POST["telefono"] ?? "");
        $email     = trim($_POST["email"] ?? "");
        $direccion = trim($_POST["direccion"] ?? "");

        if ($nombre === "") {
            $error = "El nombre del proveedor es obligatorio.";
        } elseif (mb_strlen($nombre) > 120) {
            $error = "El nombre no debe superar los 120 caracteres.";
        } elseif ($email !== "" && mb_strlen($email) > 120) {
            $error = "El email no debe superar los 120 caracteres.";
        } elseif (mb_strlen($telefono) > 30) {
            $error = "El teléfono no debe superar los 30 caracteres.";
        } elseif (mb_strlen($direccion) > 200) {
            $error = "La dirección no debe superar los 200 caracteres.";
        } else {
            try {
                $stmtIns = $connection->prepare("
                    INSERT INTO Proveedor (nombre, telefono, email, direccion)
                    VALUES (:nombre, :telefono, :email, :direccion)
                ");
                $stmtIns->execute([
                    ":nombre"    => $nombre,
                    ":telefono"  => $telefono !== "" ? $telefono : null,
                    ":email"     => $email !== "" ? $email : null,
                    ":direccion" => $direccion !== "" ? $direccion : null,
                ]);
                $success = "Proveedor registrado correctamente.";
            } catch (PDOException $e) {
                $error = "Error al registrar el proveedor: " . $e->getMessage();
            }
        }
    }
}

/* ===========================================
 * 2) OBTENER PROVEEDORES CON FILTRO OPCIONAL
 * =========================================== */

$sqlProv = "
    SELECT id_proveedor, nombre, telefono, email, direccion
    FROM Proveedor
    WHERE 1 = 1
";
$params = [];

if ($busqueda !== "") {
    $sqlProv .= "
        AND (
            nombre ILIKE :q
            OR telefono ILIKE :q
            OR email ILIKE :q
            OR direccion ILIKE :q
        )
    ";
    $params[':q'] = '%' . $busqueda . '%';
}

$sqlProv .= " ORDER BY nombre";

$stmtProv = $connection->prepare($sqlProv);
$stmtProv->execute($params);
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <!-- NAVBAR -->
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- CONTENIDO -->
    <main class="dashboard-container">

        <!-- Encabezado -->
        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Inventario</p>
            <h1 class="dashboard-title">Proveedores</h1>
            <p class="dashboard-muted">
                <?php if (in_array($idRol, [1, 2], true)): ?>
                    Administre los proveedores que suministran productos a Panda Estampados y Kitsune.
                <?php else: ?>
                    Consulte el listado de proveedores registrados en el sistema.
                <?php endif; ?>
            </p>
        </section>

        <!-- Formulario + listado -->
        <section class="dashboard-card">

            <!-- Mensajes flash -->
            <?php if ($flash_error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>

            <?php if ($flash_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
            <?php endif; ?>

            <!-- Mensajes locales -->
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <!-- Formulario para agregar proveedor (SOLO Admin / Supervisor) -->
            <?php if (in_array($idRol, [1, 2], true)): ?>
                <h2 class="dashboard-card-title" style="margin-bottom:10px;">Agregar nuevo proveedor</h2>

                <form action="proveedores.php" method="POST" class="form-grid" style="margin-bottom:18px;">

                    <div class="form-group">
                        <label class="label">Nombre del proveedor *</label>
                        <input
                            type="text"
                            name="nombre"
                            class="input"
                            maxlength="120"
                            required
                            placeholder="Ej. Textiles Managua S.A.">
                    </div>

                    <div class="form-group">
                        <label class="label">Teléfono</label>
                        <input
                            type="text"
                            name="telefono"
                            class="input"
                            maxlength="30"
                            placeholder="Ej. +505 8888 8888">
                    </div>

                    <div class="form-group">
                        <label class="label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="input"
                            maxlength="120"
                            placeholder="Ej. contacto@proveedor.com">
                    </div>

                    <div class="form-group">
                        <label class="label">Dirección</label>
                        <input
                            type="text"
                            name="direccion"
                            class="input"
                            maxlength="200"
                            placeholder="Ciudad, barrio, referencia...">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            Guardar proveedor
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- FILTRO DE PROVEEDORES (TODOS LOS ROLES) -->
            <form method="get" class="proveedores-filtros-bar" style="margin-bottom:16px;">

                <div class="filtro-item-full">
                    <label for="q" class="label">Buscar proveedor</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="input"
                        placeholder="Nombre, teléfono, email o dirección..."
                        value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <div class="filtro-actions">
                    <button type="submit" class="btn-primary-inline">
                        Aplicar filtros
                    </button>
                    <a href="proveedores.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>


            <!-- Listado de proveedores -->
            <h2 class="dashboard-card-title" style="margin-bottom:8px;">Listado de proveedores</h2>
            <p class="dashboard-muted" style="margin-bottom:12px;">
                Estos proveedores están disponibles para asociarse a las compras e inventario.
            </p>

            <?php if (empty($proveedores)): ?>
                <p class="dashboard-muted">No se encontraron proveedores con los filtros aplicados.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-products">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th class="col-acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proveedores as $prov): ?>
                                <tr>
                                    <td><?= (int)$prov["id_proveedor"] ?></td>
                                    <td><?= htmlspecialchars($prov["nombre"]) ?></td>
                                    <td><?= htmlspecialchars($prov["telefono"] ?? "—") ?></td>
                                    <td><?= htmlspecialchars($prov["email"] ?? "—") ?></td>
                                    <td><?= htmlspecialchars($prov["direccion"] ?? "—") ?></td>
                                    <td class="acciones">
                                        <?php if (in_array($idRol, [1, 2], true)): ?>
                                            <a
                                                href="editar_proveedor.php?id=<?= (int)$prov['id_proveedor'] ?>"
                                                class="btn-accion btn-accion-editar">
                                                Editar
                                            </a>
                                            <a
                                                href="eliminar_proveedor.php?id=<?= (int)$prov['id_proveedor'] ?>"
                                                class="btn-accion btn-accion-eliminar"
                                                onclick="return confirm('¿Seguro que desea eliminar este proveedor?');">
                                                Eliminar
                                            </a>
                                        <?php else: ?>
                                            <span class="dashboard-muted">Solo lectura</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </section>

    </main>

</body>

</html>