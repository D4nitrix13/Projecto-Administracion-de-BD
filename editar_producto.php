<?php
session_start();
$pageTitle = "Editar producto - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

$error = null;

// Obtener ID (desde GET o POST)
$id = ($_SERVER["REQUEST_METHOD"] === "POST")
    ? (int)($_POST["id_producto"] ?? 0)
    : (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["flash_error"] = "Producto no válido.";
    header("Location: productos.php");
    exit();
}

// Obtener datos actuales del producto (necesario para imagen/descripcion)
$stmtProd = $connection->prepare("
    SELECT *
    FROM Producto
    WHERE id_producto = :id
");
$stmtProd->execute([":id" => $id]);
$producto = $stmtProd->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    $_SESSION["flash_error"] = "El producto especificado no existe.";
    header("Location: productos.php");
    exit();
}

// Cargar categorías y proveedores
$stmtCat = $connection->query("SELECT id_categoria, nombre FROM Categoria ORDER BY nombre");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$stmtProv = $connection->query("SELECT id_proveedor, nombre FROM Proveedor ORDER BY nombre");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo        = trim($_POST["codigo"] ?? "");
    $nombre        = trim($_POST["nombre"] ?? "");
    $descripcion   = trim($_POST["descripcion"] ?? "");
    $id_categoria  = $_POST["id_categoria"] !== "" ? (int)$_POST["id_categoria"] : null;
    $id_proveedor  = $_POST["id_proveedor"] !== "" ? (int)$_POST["id_proveedor"] : null;
    $precio_compra = trim($_POST["precio_compra"] ?? "");
    $precio_venta  = trim($_POST["precio_venta"] ?? "");
    $stock         = trim($_POST["stock"] ?? "");

    if ($codigo === "" || $nombre === "" || $precio_compra === "" || $precio_venta === "") {
        $error = "Complete los campos obligatorios marcados con (*).";
    } elseif (!is_numeric($precio_compra) || !is_numeric($precio_venta)) {
        $error = "Los precios deben ser valores numéricos.";
    } elseif ($stock === "" || !ctype_digit($stock)) {
        $error = "El stock debe ser un número entero mayor o igual a 0.";
    }

    // === MANEJO DE IMAGEN (OPCIONAL AL EDITAR) ===
    $imagenActual   = $producto["imagen"] ?? null;
    $nombreImagenBD = $imagenActual; // por defecto se mantiene la misma

    // Si el usuario sube una nueva imagen, la validamos y reemplazamos
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
                    $nuevoNombreImagen = uniqid("prod_", true) . "." . $ext;

                    $destinoCarpeta = __DIR__ . "/uploads/productos";
                    if (!is_dir($destinoCarpeta)) {
                        mkdir($destinoCarpeta, 0775, true);
                    }

                    $destinoRuta = $destinoCarpeta . "/" . $nuevoNombreImagen;

                    if (!move_uploaded_file($tmpName, $destinoRuta)) {
                        $error = "No se pudo guardar la nueva imagen en el servidor.";
                    } else {
                        // Si todo OK, actualizamos el nombre que irá a la BD
                        $nombreImagenBD = $nuevoNombreImagen;

                        // Opcional: borrar la imagen anterior si existía
                        if (!empty($imagenActual)) {
                            $rutaAnterior = $destinoCarpeta . "/" . $imagenActual;
                            if (is_file($rutaAnterior)) {
                                @unlink($rutaAnterior);
                            }
                        }
                    }
                }
            }
        } else {
            $error = "Error al subir la imagen (código $errorFile).";
        }
    }

    // Si todo está bien, actualizar en BD
    if (!$error) {
        try {
            $stmtUpd = $connection->prepare("
                UPDATE Producto
                SET codigo        = :codigo,
                    nombre        = :nombre,
                    descripcion   = :descripcion,
                    imagen        = :imagen,
                    id_categoria  = :id_categoria,
                    id_proveedor  = :id_proveedor,
                    precio_compra = :precio_compra,
                    precio_venta  = :precio_venta,
                    stock         = :stock
                WHERE id_producto = :id
            ");

            $stmtUpd->execute([
                ":codigo"        => $codigo,
                ":nombre"        => $nombre,
                ":descripcion"   => $descripcion,
                ":imagen"        => $nombreImagenBD,
                ":id_categoria"  => $id_categoria,
                ":id_proveedor"  => $id_proveedor,
                ":precio_compra" => (float)$precio_compra,
                ":precio_venta"  => (float)$precio_venta,
                ":stock"         => (int)$stock,
                ":id"            => $id,
            ]);

            $_SESSION["flash_success"] = "Producto actualizado correctamente.";
            header("Location: productos.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe otro producto con ese código.";
            } else {
                $error = "Error al actualizar el producto: " . $e->getMessage();
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
            <h1 class="dashboard-title">Editar producto</h1>
            <p class="dashboard-muted">
                Modifique la información del producto seleccionado.
            </p>

            <a href="productos.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver a productos
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- IMPORTANTE: enctype para archivos -->
            <form action="editar_producto.php" method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>">

                <div class="form-group">
                    <label class="label">Código (*)</label>
                    <input
                        type="text"
                        name="codigo"
                        class="input"
                        maxlength="50"
                        required
                        value="<?= htmlspecialchars($producto['codigo']) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Nombre del producto (*)</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($producto['nombre']) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Descripción</label>
                    <textarea
                        name="descripcion"
                        class="input"
                        rows="3"
                        placeholder="Detalles del producto..."><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="label">Imagen actual</label>
                    <?php if (!empty($producto['imagen'])): ?>
                        <div style="margin-bottom:8px;">
                            <a
                                href="ver_imagen.php?id=<?= (int)$producto['id_producto'] ?>"
                                title="Ver imagen en grande">
                                <img
                                    src="uploads/productos/<?= htmlspecialchars($producto['imagen']) ?>"
                                    alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; cursor: zoom-in;">
                            </a>
                        </div>
                        <small class="dashboard-muted">
                            Haga clic en la imagen para verla en grande. Si selecciona una nueva imagen, reemplazará a la actual.
                        </small>
                    <?php else: ?>
                        <p class="dashboard-muted">Este producto no tiene imagen registrada.</p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="label">Cambiar imagen</label>
                    <input
                        type="file"
                        name="imagen"
                        class="input"
                        accept="image/*">
                    <small class="dashboard-muted">Formatos permitidos: JPG, PNG, GIF, WEBP (máx 4MB)</small>
                </div>

                <div class="form-group">
                    <label class="label">Categoría</label>
                    <select name="id_categoria" class="input">
                        <option value="">(Sin categoría)</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option
                                value="<?= (int)$cat['id_categoria'] ?>"
                                <?= ($producto['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
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
                                <?= ($producto['id_proveedor'] == $prov['id_proveedor']) ? 'selected' : '' ?>>
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
                        value="<?= htmlspecialchars($producto['precio_compra']) ?>">
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
                        value="<?= htmlspecialchars($producto['precio_venta']) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Stock</label>
                    <input
                        type="number"
                        step="1"
                        min="0"
                        name="stock"
                        class="input"
                        required
                        value="<?= htmlspecialchars($producto['stock']) ?>">
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