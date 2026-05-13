<?php

// * Stored function or procedure has been executed

session_start();

$pageTitle = "Nuevo producto - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireLogin();

$user = $_SESSION["user"];
$connection = require __DIR__ . "/sql/db.php";

$error = null;

$stmtCat = $connection->query("
    SELECT
        id_categoria,
        nombre
    FROM listar_categorias_form_producto()
");

$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$stmtProv = $connection->query("
    SELECT
        id_proveedor,
        nombre
    FROM listar_proveedores_form_producto()
");

$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

$codigo = "";
$nombre = "";
$descripcion = "";
$id_categoria = "";
$id_proveedor = "";
$precio_compra = "";
$precio_venta = "";
$stock = "0";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo = trim($_POST["codigo"] ?? "");
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $id_categoria = ($_POST["id_categoria"] ?? "") !== "" ? (int) $_POST["id_categoria"] : null;
    $id_proveedor = ($_POST["id_proveedor"] ?? "") !== "" ? (int) $_POST["id_proveedor"] : null;
    $precio_compra = trim($_POST["precio_compra"] ?? "");
    $precio_venta = trim($_POST["precio_venta"] ?? "");
    $stock = trim($_POST["stock"] ?? "0");

    if ($codigo === "" || $nombre === "" || $precio_compra === "" || $precio_venta === "") {
        $error = "Complete los campos obligatorios marcados con (*).";
    } elseif (!is_numeric($precio_compra) || !is_numeric($precio_venta)) {
        $error = "Los precios deben ser valores numéricos.";
    } elseif ((float) $precio_compra < 0 || (float) $precio_venta < 0) {
        $error = "Los precios no pueden ser negativos.";
    } elseif ($stock !== "" && !ctype_digit($stock)) {
        $error = "El stock debe ser un número entero mayor o igual a 0.";
    }

    if (!$error && empty($_FILES["imagen"]["name"])) {
        $error = "Debe seleccionar una imagen para el producto.";
    }

    $nombreImagenBD = null;

    if (!$error && !empty($_FILES["imagen"]["name"])) {
        $file = $_FILES["imagen"];
        $tmpName = $file["tmp_name"];
        $origName = $file["name"];
        $size = $file["size"];
        $errorFile = $file["error"];

        if ($errorFile === UPLOAD_ERR_OK) {
            $maxMegabytes = 10;
            $maxBytes = $maxMegabytes * 1024 * 1024;
            $maxSizeLabel = $maxMegabytes . "MB";

            if ($size > $maxBytes) {
                $error = "La imagen excede el tamaño máximo permitido ({$maxSizeLabel}).";
            } else {
                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                $extPermitidas = ["jpg", "jpeg", "png", "gif", "webp"];

                if (!in_array($ext, $extPermitidas, true)) {
                    $error = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
                } else {
                    $nombreImagenBD = uniqid("prod_", true) . "." . $ext;

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
            $error = "Error al subir la imagen del producto.";
        }
    }

    if (!$error) {
        try {
            $stmt = $connection->prepare("
                SELECT id_producto
                FROM registrar_producto_formulario(
                    :codigo,
                    :nombre,
                    :descripcion,
                    :imagen,
                    :id_categoria,
                    :id_proveedor,
                    :precio_compra,
                    :precio_venta,
                    :stock
                )
            ");

            $stmt->execute([
                ":codigo" => $codigo,
                ":nombre" => $nombre,
                ":descripcion" => $descripcion,
                ":imagen" => $nombreImagenBD,
                ":id_categoria" => $id_categoria,
                ":id_proveedor" => $id_proveedor,
                ":precio_compra" => (float) $precio_compra,
                ":precio_venta" => (float) $precio_venta,
                ":stock" => (int) $stock,
            ]);

            $_SESSION["flash_success"] = "Producto registrado correctamente.";
            header("Location: productos.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === "23505") {
                $error = "Ya existe un producto con ese código.";
            } else {
                error_log("crear producto error: " . $e->getMessage());
                $error = "Error al guardar el producto.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/inventario/productos/nuevo/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="product-page-heading">
            <div class="product-page-heading-top">
                <div class="product-page-heading-text">
                    <p class="product-eyebrow">Inventario</p>
                    <h1 class="product-title">Agregar nuevo producto</h1>

                    <p class="product-muted">
                        Registre un nuevo producto para el inventario de Panda Estampados y Kitsune.
                    </p>
                </div>

                <a href="productos.php" class="btn-secondary-inline">
                    Volver al listado
                </a>
            </div>
        </section>

        <section class="product-form-card">

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="nuevo_producto.php" method="POST" enctype="multipart/form-data" class="form-grid">

                <div class="form-group">
                    <label for="codigo" class="label">Código (*)</label>
                    <input
                        id="codigo"
                        type="text"
                        name="codigo"
                        class="input"
                        maxlength="50"
                        required
                        value="<?= htmlspecialchars($codigo) ?>">
                </div>

                <div class="form-group">
                    <label for="nombre" class="label">Nombre del producto (*)</label>
                    <input
                        id="nombre"
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($nombre) ?>">
                </div>

                <div class="form-group form-group-full">
                    <label for="descripcion" class="label">Descripción</label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="input"
                        rows="3"
                        placeholder="Detalles del producto..."><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="form-group form-group-full">
                    <label for="imagen" class="label">Imagen del producto (*)</label>
                    <input
                        id="imagen"
                        type="file"
                        name="imagen"
                        class="input"
                        accept="image/*"
                        required>
                    <p class="field-help">
                        Formatos permitidos: JPG, PNG, GIF o WEBP. Tamaño máximo: 4 MB.
                    </p>
                </div>

                <div class="form-group">
                    <label for="id_categoria" class="label">Categoría</label>
                    <select id="id_categoria" name="id_categoria" class="input">
                        <option value="">Sin categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option
                                value="<?= (int) $cat["id_categoria"] ?>"
                                <?= ((string) $id_categoria === (string) $cat["id_categoria"]) ? "selected" : "" ?>>
                                <?= htmlspecialchars($cat["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_proveedor" class="label">Proveedor</label>
                    <select id="id_proveedor" name="id_proveedor" class="input">
                        <option value="">Sin proveedor</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option
                                value="<?= (int) $prov["id_proveedor"] ?>"
                                <?= ((string) $id_proveedor === (string) $prov["id_proveedor"]) ? "selected" : "" ?>>
                                <?= htmlspecialchars($prov["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="precio_compra" class="label">Precio de compra (*)</label>
                    <input
                        id="precio_compra"
                        type="number"
                        step="0.01"
                        min="0"
                        name="precio_compra"
                        class="input"
                        required
                        value="<?= htmlspecialchars($precio_compra) ?>">
                </div>

                <div class="form-group">
                    <label for="precio_venta" class="label">Precio de venta (*)</label>
                    <input
                        id="precio_venta"
                        type="number"
                        step="0.01"
                        min="0"
                        name="precio_venta"
                        class="input"
                        required
                        value="<?= htmlspecialchars($precio_venta) ?>">
                </div>

                <div class="form-group">
                    <label for="stock" class="label">Stock inicial</label>
                    <input
                        id="stock"
                        type="number"
                        step="1"
                        min="0"
                        name="stock"
                        class="input"
                        value="<?= htmlspecialchars($stock) ?>">
                </div>

                <div class="form-actions">
                    <a href="productos.php" class="btn-secondary-inline">
                        Cancelar
                    </a>

                    <button type="submit" class="btn-primary-inline">
                        Guardar producto
                    </button>
                </div>
            </form>
        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>