<?php
session_start();
$pageTitle = "Editar proveedor Panda Estampados / Kitsune";

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
    $id = isset($_POST["id_proveedor"]) ? (int)$_POST["id_proveedor"] : 0;
}

if ($id <= 0) {
    $_SESSION["flash_error"] = "Proveedor no válido.";
    header("Location: proveedores.php");
    exit();
}

// Si se envió el formulario, procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
            $stmtUpd = $connection->prepare("
                UPDATE Proveedor
                SET nombre = :nombre,
                    telefono = :telefono,
                    email = :email,
                    direccion = :direccion
                WHERE id_proveedor = :id
            ");
            $stmtUpd->execute([
                ":nombre"    => $nombre,
                ":telefono"  => $telefono !== "" ? $telefono : null,
                ":email"     => $email !== "" ? $email : null,
                ":direccion" => $direccion !== "" ? $direccion : null,
                ":id"        => $id,
            ]);

            if ($stmtUpd->rowCount() > 0) {
                $_SESSION["flash_success"] = "Proveedor actualizado correctamente.";
            } else {
                $_SESSION["flash_success"] = "No se realizaron cambios.";
            }

            header("Location: proveedores.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error al actualizar el proveedor: " . $e->getMessage();
        }
    }
}

// Obtener datos actuales del proveedor
$stmtProv = $connection->prepare("
    SELECT id_proveedor, nombre, telefono, email, direccion
    FROM Proveedor
    WHERE id_proveedor = :id
");
$stmtProv->execute([":id" => $id]);
$proveedor = $stmtProv->fetch(PDO::FETCH_ASSOC);

if (!$proveedor) {
    $_SESSION["flash_error"] = "El proveedor especificado no existe.";
    header("Location: proveedores.php");
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
            <h1 class="dashboard-title">Editar proveedor</h1>
            <p class="dashboard-muted">
                Modifique la información del proveedor seleccionado.
            </p>

            <a href="proveedores.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver a proveedores
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="editar_proveedor.php" method="POST" class="form-grid">
                <input type="hidden" name="id_proveedor" value="<?= (int)$proveedor['id_proveedor'] ?>">

                <div class="form-group">
                    <label class="label">Nombre del proveedor *</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($proveedor['nombre']) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Teléfono</label>
                    <input
                        type="text"
                        name="telefono"
                        class="input"
                        maxlength="30"
                        value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="input"
                        maxlength="120"
                        value="<?= htmlspecialchars($proveedor['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="label">Dirección</label>
                    <input
                        type="text"
                        name="direccion"
                        class="input"
                        maxlength="200"
                        value="<?= htmlspecialchars($proveedor['direccion'] ?? '') ?>">
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