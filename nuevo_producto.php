<?php
session_start();
$pageTitle = "Nuevo producto - Panda Estampados / Kitsune";

// Proteger la página: solo usuarios logueados
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

$error = null;

// Cargar categorías y proveedores para los select
$stmtCat = $connection->query("SELECT id_categoria, nombre FROM Categoria ORDER BY nombre");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$stmtProv = $connection->query("SELECT id_proveedor, nombre FROM Proveedor ORDER BY nombre");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

// Valores por defecto (para rellenar el form si hay error)
$codigo        = "";
$nombre        = "";
$descripcion   = "";
$id_categoria  = "";
$id_proveedor  = "";
$precio_compra = "";
$precio_venta  = "";
$stock         = "0";

// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo        = trim($_POST["codigo"] ?? "");
    $nombre        = trim($_POST["nombre"] ?? "");
    $descripcion   = trim($_POST["descripcion"] ?? "");
    $id_categoria  = $_POST["id_categoria"] !== "" ? (int)$_POST["id_categoria"] : null;
    $id_proveedor  = $_POST["id_proveedor"] !== "" ? (int)$_POST["id_proveedor"] : null;
    $precio_compra = trim($_POST["precio_compra"] ?? "");
    $precio_venta  = trim($_POST["precio_venta"] ?? "");
    $stock         = trim($_POST["stock"] ?? "0");

    // Validaciones básicas
    if ($codigo === "" || $nombre === "" || $precio_compra === "" || $precio_venta === "") {
        $error = "Complete los campos obligatorios marcados con (*).";
    } elseif (!is_numeric($precio_compra) || !is_numeric($precio_venta)) {
        $error = "Los precios deben ser valores numéricos.";
    } elseif ($stock !== "" && !ctype_digit($stock)) {
        $error = "El stock debe ser un número entero mayor o igual a 0.";
    }

    // --- VALIDAR QUE LA IMAGEN SEA OBLIGATORIA ---
    if (!$error && empty($_FILES["imagen"]["name"])) {
        $error = "Debe seleccionar una imagen para el producto.";
    }

    // === MANEJO DE IMAGEN ===
    $nombreImagenBD = null;

    if (!$error && !empty($_FILES["imagen"]["name"])) {

        $file      = $_FILES["imagen"];
        $tmpName   = $file["tmp_name"];
        $origName  = $file["name"];
        $size      = $file["size"];
        $errorFile = $file["error"];

        if ($errorFile === UPLOAD_ERR_OK) {

            $maxBytes = 4 * 1024 * 1024; // 4 MB
            if ($size > $maxBytes) {
                $error = "La imagen excede el tamaño máximo permitido (4MB).";
            } else {

                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                $extPermitidas = ["jpg", "jpeg", "png", "gif", "webp"];

                if (!in_array($ext, $extPermitidas, true)) {
                    $error = "Formato de imagen no permitido. Usa JPG, PNG, GIF o WEBP.";
                } else {
                    // Generar nombre único
                    $nombreImagenBD = uniqid("prod_", true) . "." . $ext;

                    // Carpeta destino
                    $destinoCarpeta = __DIR__ . "/uploads/productos";
                    if (!is_dir($destinoCarpeta)) {
                        mkdir($destinoCarpeta, 0775, true);
                    }

                    $destinoRuta = $destinoCarpeta . "/" . $nombreImagenBD;

                    if (!move_uploaded_file($tmpName, $destinoRuta)) {
                        $error = "No se pudo guardar la imagen en el servidor.";
                    }
                }
            }
        } else {
            $error = "Error al subir la imagen (código $errorFile).";
        }
    }

    // Si todo está bien, guardar en BD
    if (!$error) {
        try {
            $stmt = $connection->prepare("
                INSERT INTO Producto
                    (codigo, nombre, descripcion, imagen,
                     id_categoria, id_proveedor,
                     precio_compra, precio_venta, stock)
                VALUES
                    (:codigo, :nombre, :descripcion, :imagen,
                     :id_categoria, :id_proveedor,
                     :precio_compra, :precio_venta, :stock)
            ");

            $stmt->execute([
                ":codigo"        => $codigo,
                ":nombre"        => $nombre,
                ":descripcion"   => $descripcion,
                ":imagen"        => $nombreImagenBD,
                ":id_categoria"  => $id_categoria,
                ":id_proveedor"  => $id_proveedor,
                ":precio_compra" => (float)$precio_compra,
                ":precio_venta"  => (float)$precio_venta,
                ":stock"         => (int)$stock,
            ]);

            // ✅ Mensaje flash y redirección al listado
            $_SESSION["flash_success"] = "Producto registrado correctamente.";
            header("Location: productos.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe un producto con ese código.";
            } else {
                $error = "Error al guardar el producto: " . $e->getMessage();
            }
        }
    }
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
            <h1 class="dashboard-title">Agregar nuevo producto</h1>

            <p class="dashboard-muted">
                Registre un nuevo producto para el inventario de Panda Estampados y Kitsune.
            </p>

            <a href="productos.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver al listado de productos
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- IMPORTANTE: enctype para archivos -->
            <form action="nuevo_producto.php" method="POST" enctype="multipart/form-data" class="form-grid">

                <div class="form-group">
                    <label class="label">Código (*)</label>
                    <input
                        type="text"
                        name="codigo"
                        class="input"
                        maxlength="50"
                        required
                        value="<?= htmlspecialchars($codigo) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Nombre del producto (*)</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($nombre) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Descripción</label>
                    <textarea
                        name="descripcion"
                        class="input"
                        rows="3"
                        placeholder="Detalles del producto..."><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="label">Imagen del producto (*)</label>
                    <input
                        type="file"
                        name="imagen"
                        class="input"
                        accept="image/*"
                        required>
                    <small class="dashboard-muted">Formatos permitidos: JPG, PNG, GIF, WEBP (máx 4MB)</small>
                </div>

                <div class="form-group">
                    <label class="label">Categoría</label>
                    <select name="id_categoria" class="input">
                        <option value="">(Sin categoría)</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option
                                value="<?= (int)$cat['id_categoria'] ?>"
                                <?= ($id_categoria == $cat['id_categoria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label">Proveedor</label>
                    <select name="id_proveedor" class="input">
                        <option value="">(Sin proveedor)</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option
                                value="<?= (int)$prov['id_proveedor'] ?>"
                                <?= ($id_proveedor == $prov['id_proveedor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prov['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label">Precio de compra (*)</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="precio_compra"
                        class="input"
                        required
                        value="<?= htmlspecialchars($precio_compra) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Precio de venta (*)</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="precio_venta"
                        class="input"
                        required
                        value="<?= htmlspecialchars($precio_venta) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Stock inicial</label>
                    <input
                        type="number"
                        step="1"
                        min="0"
                        name="stock"
                        class="input"
                        value="<?= htmlspecialchars($stock) ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Guardar producto
                    </button>
                </div>
            </form>
        </section>

    </main>

</body>

</html>