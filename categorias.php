<?php
session_start();
$pageTitle = "Categorías - Panda Estampados / Kitsune";

// Proteger la página
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user  = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

// Solo el Administrador puede gestionar categorías
$canManageCategories = ($idRol === 1);

/** @var PDO $connection */
$connection = require "./sql/db.php";

// Mensajes flash (para editar / eliminar)
$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error   = $_SESSION["flash_error"]   ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$error = null;
$success = null;

/* ==============================
 * 1) LECTURA DE FILTRO (GET)
 * ============================== */
$busqueda = trim($_GET['q'] ?? "");

// Procesar alta de nueva categoría (CREATE) SOLO si puede gestionar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$canManageCategories) {
        $error = "Su rol no tiene permisos para crear categorías.";
    } else {
        $nombre = trim($_POST["nombre"] ?? "");

        if ($nombre === "") {
            $error = "El nombre de la categoría es obligatorio.";
        } elseif (mb_strlen($nombre) > 80) {
            $error = "El nombre de la categoría no debe superar los 80 caracteres.";
        } else {
            try {
                $stmtIns = $connection->prepare("
                    INSERT INTO Categoria (nombre)
                    VALUES (:nombre)
                ");
                $stmtIns->execute([":nombre" => $nombre]);
                $success = "Categoría registrada correctamente.";
            } catch (PDOException $e) {
                if ($e->getCode() === "23505") { // UNIQUE
                    $error = "Ya existe una categoría con ese nombre.";
                } else {
                    $error = "Error al registrar la categoría: " . $e->getMessage();
                }
            }
        }
    }
}

/* ===========================================
 * 2) OBTENER CATEGORÍAS CON FILTRO OPCIONAL
 * =========================================== */

$sqlCat = "
    SELECT id_categoria, nombre
    FROM Categoria
    WHERE 1 = 1
";
$params = [];

if ($busqueda !== "") {
    $sqlCat .= " AND nombre ILIKE :q";
    $params[':q'] = '%' . $busqueda . '%';
}

$sqlCat .= " ORDER BY nombre";

$stmtCat = $connection->prepare($sqlCat);
$stmtCat->execute($params);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
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
            <h1 class="dashboard-title">Categorías de productos</h1>
            <p class="dashboard-muted">
                Administre las categorías que se utilizan para organizar los productos
                de Panda Estampados y Kitsune.
            </p>
        </section>

        <!-- Formulario + listado -->
        <section class="dashboard-card">

            <!-- Mensajes flash (editar / eliminar) -->
            <?php if ($flash_error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>

            <?php if ($flash_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
            <?php endif; ?>

            <!-- Mensajes locales (crear) -->
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <!-- Formulario para agregar categoría (solo Admin) -->
            <?php if ($canManageCategories): ?>
                <h2 class="dashboard-card-title" style="margin-bottom:10px;">Agregar nueva categoría</h2>

                <form action="categorias.php" method="POST" class="form-grid" style="margin-bottom:18px;">
                    <div class="form-group">
                        <label class="label">Nombre de la categoría</label>
                        <input
                            type="text"
                            name="nombre"
                            class="input"
                            maxlength="80"
                            placeholder="Ej. Camisetas personalizadas"
                            required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            Guardar categoría
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <p class="dashboard-muted" style="margin-bottom:16px;">
                    Su rol solo permite visualizar y filtrar categorías. Para crear, editar o eliminar
                    contacte a un administrador.
                </p>
            <?php endif; ?>

            <!-- FILTRO DE CATEGORÍAS -->
            <form method="get" class="productos-filtros-bar" style="margin-bottom:12px;">
                <div class="filtro-item">
                    <label for="q" class="label">Buscar categoría</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="input"
                        placeholder="Nombre de la categoría..."
                        value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <div class="filtro-actions">
                    <button type="submit" class="btn-primary-inline">
                        Aplicar filtros
                    </button>
                    <a href="categorias.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>

            <!-- Listado de categorías -->
            <h2 class="dashboard-card-title" style="margin-bottom:8px;">Listado de categorías</h2>
            <p class="dashboard-muted" style="margin-bottom:12px;">
                Estas categorías están disponibles para asignarse a los productos.
            </p>

            <?php if (empty($categorias)): ?>
                <p class="dashboard-muted">No se encontraron categorías con los filtros aplicados.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-products">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <?php if ($canManageCategories): ?>
                                    <th class="col-acciones">Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td><?= (int)$cat["id_categoria"] ?></td>
                                    <td><?= htmlspecialchars($cat["nombre"]) ?></td>

                                    <?php if ($canManageCategories): ?>
                                        <td class="acciones">
                                            <a
                                                href="editar_categoria.php?id=<?= (int)$cat['id_categoria'] ?>"
                                                class="btn-accion btn-accion-editar">
                                                Editar
                                            </a>
                                            <a
                                                href="eliminar_categoria.php?id=<?= (int)$cat['id_categoria'] ?>"
                                                class="btn-accion btn-accion-eliminar"
                                                onclick="return confirm('¿Seguro que desea eliminar esta categoría?');">
                                                Eliminar
                                            </a>
                                        </td>
                                    <?php endif; ?>
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