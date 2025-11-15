<?php
session_start();
$pageTitle = "Editar categoría - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

$error = null;

// Obtener ID
$id = null;

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
} else { // POST
    $id = isset($_POST["id_categoria"]) ? (int)$_POST["id_categoria"] : 0;
}

if ($id <= 0) {
    $_SESSION["flash_error"] = "Categoría no válida.";
    header("Location: categorias.php");
    exit();
}

// Si se envió el formulario, procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");

    if ($nombre === "") {
        $error = "El nombre de la categoría es obligatorio.";
    } elseif (mb_strlen($nombre) > 80) {
        $error = "El nombre de la categoría no debe superar los 80 caracteres.";
    } else {
        try {
            $stmtUpd = $connection->prepare("
                UPDATE Categoria
                SET nombre = :nombre
                WHERE id_categoria = :id
            ");
            $stmtUpd->execute([
                ":nombre" => $nombre,
                ":id"     => $id
            ]);

            if ($stmtUpd->rowCount() > 0) {
                $_SESSION["flash_success"] = "Categoría actualizada correctamente.";
            } else {
                $_SESSION["flash_success"] = "No se realizaron cambios.";
            }

            header("Location: categorias.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe otra categoría con ese nombre.";
            } else {
                $error = "Error al actualizar la categoría: " . $e->getMessage();
            }
        }
    }
}

// Obtener datos actuales de la categoría para mostrar en el formulario
$stmtCat = $connection->prepare("
    SELECT id_categoria, nombre
    FROM Categoria
    WHERE id_categoria = :id
");
$stmtCat->execute([":id" => $id]);
$categoria = $stmtCat->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    $_SESSION["flash_error"] = "La categoría especificada no existe.";
    header("Location: categorias.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<?php require "partials/header.php"; ?>

<body class="page-bg">

    <!-- NAVBAR -->
    <?php include __DIR__ . '/partials/navbar.php'; ?>


    <!-- CONTENIDO -->
    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Inventario</p>
            <h1 class="dashboard-title">Editar categoría</h1>
            <p class="dashboard-muted">
                Modifique el nombre de la categoría seleccionada.
            </p>

            <a href="categorias.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver a categorías
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="editar_categoria.php" method="POST" class="form-grid">
                <input type="hidden" name="id_categoria" value="<?= (int)$categoria['id_categoria'] ?>">

                <div class="form-group">
                    <label class="label">Nombre de la categoría</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="80"
                        required
                        value="<?= htmlspecialchars($categoria['nombre']) ?>">
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