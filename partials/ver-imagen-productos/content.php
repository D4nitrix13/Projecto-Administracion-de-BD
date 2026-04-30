<section class="image-product-card">

    <div class="image-product-header">
        <div>
            <p class="dashboard-eyebrow">Vista de producto</p>
            <h1><?= htmlspecialchars($prod["nombre"]) ?></h1>
            <span>Código: <?= htmlspecialchars($prod["codigo"]) ?></span>
        </div>

        <a href="productos.php?id=<?= (int)$prod["id_producto"] ?>" class="back-btn">
            Volver al producto
        </a>
    </div>

    <div class="image-preview-box">
        <img
            src="<?= htmlspecialchars($rutaImagen) ?>"
            alt="<?= htmlspecialchars($prod["nombre"]) ?>">
    </div>

    <div class="image-product-info">
        <p>
            <?= nl2br(htmlspecialchars($prod["descripcion"] ?: "Sin descripción disponible.")) ?>
        </p>

        <div class="product-mini-stats">
            <span>Precio venta: <strong>C$ <?= number_format((float)$prod["precio_venta"], 2) ?></strong></span>
            <span>Stock: <strong><?= (int)$prod["stock"] ?></strong></span>
        </div>
    </div>

</section>