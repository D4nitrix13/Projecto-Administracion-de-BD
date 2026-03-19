<?php
// editar_seccion.php
session_start();
$pageTitle = "Editar sección - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
/** @var PDO $connection */
$connection = require "./sql/db.php";

// Solo administradores
if (!isset($user["id_rol"]) || (int)$user["id_rol"] !== 1) {
    header("Location: acceso_restringido.php");
    exit();
}

// Obtener ID de la sección
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    $_SESSION["flash_error"] = "Sección no válida.";
    header("Location: secciones.php");
    exit();
}

$error   = null;
$success = null;

/* ==============================
 * 1) CARGAR SECCIÓN ACTUAL
 * ============================== */
$stmt = $connection->prepare("
    SELECT id_seccion, nombre
    FROM Seccion
    WHERE id_seccion = :id
");
$stmt->execute([":id" => $id]);
$seccion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$seccion) {
    $_SESSION["flash_error"] = "La sección seleccionada no existe.";
    header("Location: secciones.php");
    exit();
}

/* ==============================
 * 2) PROCESAR ACTUALIZACIÓN
 * ============================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");

    if ($nombre === "") {
        $error = "El nombre de la sección es obligatorio.";
    } elseif (mb_strlen($nombre) > 30) {
        $error = "El nombre de la sección no debe superar los 30 caracteres.";
    } else {
        try {
            $stmtUpd = $connection->prepare("
                UPDATE Seccion
                SET nombre = :nombre
                WHERE id_seccion = :id
            ");
            $stmtUpd->execute([
                ":nombre" => $nombre,
                ":id"     => $id,
            ]);

            $_SESSION["flash_success"] = "Sección actualizada correctamente.";
            header("Location: secciones.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe una sección con ese nombre.";
            } else {
                $error = "Error al actualizar la sección: " . $e->getMessage();
            }
        }
    }

    // si hay error, mantenemos el valor escrito
    if ($error) {
        $seccion["nombre"] = $nombre;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">
    <?php include __DIR__ . "/partials/navbar.php"; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Sistema</p>
            <h1 class="dashboard-title">Editar sección</h1>
            <p class="dashboard-muted">
                Modifique el nombre de la sección seleccionada.
            </p>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="editar_seccion.php?id=<?= (int)$seccion['id_seccion'] ?>" method="POST" class="form-grid">
                <div class="form-group">
                    <label class="label">Nombre de la sección</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="30"
                        required
                        placeholder="Ej. Panda Estampados, Kitsune, Ambas"
                        value="<?= htmlspecialchars($seccion['nombre']) ?>">
                    <p class="dashboard-muted" style="margin-top:4px; font-size:13px;">
                        Usa nombres cortos como <strong>Panda Estampados</strong>, <strong>Kitsune</strong>
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>
                    <a href="secciones.php" class="btn-secondary-inline" style="margin-left:8px;">
                        Cancelar
                    </a>
                </div>
            </form>

        </section>

    </main>
</body>

</html>