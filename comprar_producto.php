<?php
session_start();
$pageTitle = "Comprar producto - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

$error = null;
$success = null;

$id_producto = ($_SERVER["REQUEST_METHOD"] === "POST")
    ? (int)($_POST["id_producto"] ?? 0)
    : (int)($_GET["id"] ?? 0);

if ($id_producto <= 0) {
    $_SESSION["flash_error"] = "Producto no válido.";
    header("Location: productos.php");
    exit();
}

// Cargar datos del producto
$stmtProd = $connection->prepare("
    SELECT p.*, pr.id_proveedor, pr.nombre AS proveedor_nombre
    FROM Producto p
    LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
    WHERE p.id_producto = :id
");
$stmtProd->execute([":id" => $id_producto]);
$producto = $stmtProd->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    $_SESSION["flash_error"] = "El producto especificado no existe.";
    header("Location: productos.php");
    exit();
}

// Cargar proveedores para select
$stmtProv = $connection->query("SELECT id_proveedor, nombre FROM Proveedor ORDER BY nombre");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

// Valores por defecto del form
$id_proveedor = $producto["id_proveedor"] ?? "";
$cantidad     = "";
$costo_unit   = $producto["precio_compra"];

// Procesar compra
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_proveedor = $_POST["id_proveedor"] !== "" ? (int)$_POST["id_proveedor"] : null;
    $cantidad     = trim($_POST["cantidad"] ?? "");
    $costo_unit   = trim($_POST["costo_unitario"] ?? "");

    if ($id_proveedor === null) {
        $error = "Seleccione un proveedor.";
    } elseif ($cantidad === "" || !ctype_digit($cantidad) || (int)$cantidad <= 0) {
        $error = "La cantidad debe ser un entero mayor que 0.";
    } elseif ($costo_unit === "" || !is_numeric($costo_unit) || (float)$costo_unit < 0) {
        $error = "El costo unitario debe ser un número mayor o igual a 0.";
    } else {
        $cantidad_int = (int)$cantidad;
        $costo_float  = (float)$costo_unit;
        $total_linea  = $cantidad_int * $costo_float;

        try {
            $connection->beginTransaction();

            // Crear encabezado de compra
            $stmtCompra = $connection->prepare("
                INSERT INTO Compra (fecha, id_proveedor, id_usuario, total)
                VALUES (NOW(), :id_proveedor, :id_usuario, :total)
                RETURNING id_compra
            ");
            $stmtCompra->execute([
                ":id_proveedor" => $id_proveedor,
                ":id_usuario"   => (int)$user["id_usuario"],
                ":total"        => $total_linea,
            ]);
            $id_compra = $stmtCompra->fetchColumn();

            // Detalle de compra
            $stmtDet = $connection->prepare("
                INSERT INTO DetalleCompra
                    (id_compra, id_producto, cantidad, costo_unitario, total_linea)
                VALUES
                    (:id_compra, :id_producto, :cantidad, :costo_unitario, :total_linea)
            ");
            $stmtDet->execute([
                ":id_compra"      => $id_compra,
                ":id_producto"    => $id_producto,
                ":cantidad"       => $cantidad_int,
                ":costo_unitario" => $costo_float,
                ":total_linea"    => $total_linea,
            ]);

            // Actualizar stock y costo de compra del producto
            $stmtUpdProd = $connection->prepare("
                UPDATE Producto
                SET stock = stock + :cantidad,
                    precio_compra = :costo_unitario
                WHERE id_producto = :id_producto
            ");
            $stmtUpdProd->execute([
                ":cantidad"       => $cantidad_int,
                ":costo_unitario" => $costo_float,
                ":id_producto"    => $id_producto,
            ]);

            $connection->commit();

            $_SESSION["flash_success"] = "Compra registrada y stock actualizado.";
            header("Location: productos.php");
            exit();
        } catch (PDOException $e) {
            $connection->rollBack();
            $error = "Error al registrar la compra: " . $e->getMessage();
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
            <h1 class="dashboard-title">Registrar compra de producto</h1>
            <p class="dashboard-muted">
                Registre la compra para aumentar el stock del producto seleccionado.
            </p>

            <a href="productos.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver a productos
            </a>
        </section>

        <section class="dashboard-card">

            <h2 class="dashboard-card-title" style="margin-bottom:8px;">
                Producto: <?= htmlspecialchars($producto['nombre']) ?> (<?= htmlspecialchars($producto['codigo']) ?>)
            </h2>
            <p class="dashboard-muted" style="margin-bottom:16px;">
                Stock actual: <strong><?= (int)$producto['stock'] ?></strong>
            </p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="comprar_producto.php" method="POST" class="form-grid">
                <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>">

                <div class="form-group">
                    <label class="label">Proveedor</label>
                    <select name="id_proveedor" class="input" required>
                        <option value="">Seleccione un proveedor</option>
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
                    <label class="label">Cantidad</label>
                    <input
                        type="number"
                        step="1"
                        min="1"
                        name="cantidad"
                        class="input"
                        required
                        value="<?= htmlspecialchars($cantidad) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Costo unitario (C$)</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="costo_unitario"
                        class="input"
                        required
                        value="<?= htmlspecialchars($costo_unit) ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Registrar compra
                    </button>
                </div>
            </form>
        </section>

    </main>

</body>

</html>