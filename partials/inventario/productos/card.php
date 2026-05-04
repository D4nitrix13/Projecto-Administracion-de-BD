<?php
$descripcion = trim($prod["descripcion"] ?? "");

if ($descripcion === "") {
    $descripcion = "Sin descripción disponible.";
}

if (mb_strlen($descripcion) > 135) {
    $descripcion = mb_substr($descripcion, 0, 132) . "...";
}

$stock = (int)$prod["stock"];
$stockClass = $stock <= 5 ? "stock-bajo" : "stock-normal";

$imagen = trim($prod["imagen"] ?? "");
$rutaImagen = $imagen !== "" ? "uploads/productos/" . $imagen : "assets/img/no-product.png";
?>

<article class="producto-card">
    <div class="producto-card-header">
        <a href="ver_imagen_producto.php?id=<?= (int)$prod["id_producto"] ?>" class="producto-card-img">
            <img
                src="<?= htmlspecialchars($rutaImagen) ?>"
                alt="<?= htmlspecialchars($prod["nombre"]) ?>">
        </a>

        <div>
            <h3 class="producto-card-title">
                <?= htmlspecialchars($prod["nombre"]) ?>
            </h3>
            <p class="producto-card-sub">
                Código: <?= htmlspecialchars($prod["codigo"]) ?>
            </p>
        </div>
    </div>

    <div class="producto-card-body">
        <p><?= htmlspecialchars($descripcion) ?></p>

        <div class="producto-meta">
            <span><strong>Categoría:</strong> <?= htmlspecialchars($prod["categoria"] ?? "Sin categoría") ?></span>
            <span><strong>Proveedor:</strong> <?= htmlspecialchars($prod["proveedor"] ?? "Sin proveedor") ?></span>
        </div>

        <div class="producto-precios">
            <span><strong>Compra:</strong> C$ <?= number_format((float)$prod["precio_compra"], 2) ?></span>
            <span><strong>Venta:</strong> C$ <?= number_format((float)$prod["precio_venta"], 2) ?></span>
        </div>
    </div>

    <div class="producto-card-footer">
        <span class="producto-stock-badge <?= $stockClass ?>">
            Stock: <?= $stock ?>
        </span>

        <div class="producto-card-actions">
            <?php if ($idRol === 3): ?>
                <span class="solo-lectura">Solo lectura</span>
            <?php else: ?>
                <a href="editar_producto.php?id=<?= (int)$prod["id_producto"] ?>" class="btn-accion-xs btn-accion-editar-xs">
                    Editar
                </a>

                <?php if ($idRol === 1): ?>
                    <a href="comprar_producto.php?id=<?= (int)$prod["id_producto"] ?>" class="btn-accion-xs btn-accion-comprar-xs">
                        Comprar
                    </a>
                <?php endif; ?>

                <a
                    href="eliminar_producto.php?id=<?= (int)$prod["id_producto"] ?>"
                    class="btn-accion-xs btn-accion-eliminar-xs"
                    onclick="return confirm('¿Seguro que desea eliminar este producto?');">
                    Eliminar
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>