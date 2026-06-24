<?php if (empty($productos)): ?>
    <div class="catalog-empty">
        <strong>No se encontraron productos.</strong>
        <p>Pruebe con otro nombre, código, categoría o disponibilidad.</p>
    </div>
<?php else: ?>
    <div class="catalog-grid">
        <?php foreach ($productos as $producto): ?>
            <?php
            $descripcion = recortarTextoCatalogo($producto["descripcion"] ?? "");
            $stock = (int)$producto["stock"];
            $stockClass = obtenerClaseStockCatalogo($stock);
            $stockLabel = obtenerTextoStockCatalogo($stock);

            $rutaImagenProducto = __DIR__ . "/../../../uploads/productos/" . ($producto["imagen"] ?? "");

            $tieneImagen = !empty($producto["imagen"]) && is_file($rutaImagenProducto);

            $urlWhatsApp = obtenerUrlWhatsAppCatalogo($numeroWhatsApp, $producto);
            ?>

            <article class="catalog-card">
                <div class="catalog-image-wrap">
                    <?php if ($tieneImagen): ?>
                        <a
                            href="ver_detalle_producto.php?id=<?= (int)$producto["id_producto"] ?>"
                            title="Ver imagen en grande">
                            <img
                                src="uploads/productos/<?= htmlspecialchars($producto["imagen"]) ?>"
                                alt="<?= htmlspecialchars($producto["nombre"]) ?>">
                        </a>
                    <?php else: ?>
                        <div class="catalog-image-placeholder">
                            <span>Sin imagen</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="catalog-card-content">
                    <div class="catalog-card-top">
                        <div>
                            <h3 class="catalog-card-title">
                                <?= htmlspecialchars($producto["nombre"]) ?>
                            </h3>

                            <div class="catalog-card-code">
                                Código: <?= htmlspecialchars($producto["codigo"]) ?>
                            </div>
                        </div>

                        <span class="catalog-category">
                            <?= htmlspecialchars($producto["categoria"] ?? "Sin categoría") ?>
                        </span>
                    </div>

                    <p class="catalog-card-description">
                        <?= htmlspecialchars($descripcion) ?>
                    </p>

                    <div class="catalog-price-row">
                        <span class="catalog-price">
                            C$ <?= number_format((float)$producto["precio_venta"], 2) ?>
                        </span>

                        <span class="catalog-stock <?= htmlspecialchars($stockClass) ?>">
                            <?= htmlspecialchars($stockLabel) ?>
                        </span>
                    </div>

                    <div class="catalog-card-footer">
                        <?php if ($stock <= 0): ?>
                            <span class="catalog-wa-disabled">
                                Producto agotado
                            </span>
                        <?php elseif ($urlWhatsApp === ""): ?>
                            <span class="catalog-wa-disabled">
                                WhatsApp no configurado
                            </span>
                        <?php else: ?>
                            <a
                                href="<?= htmlspecialchars($urlWhatsApp) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="catalog-wa-btn">
                                Consultar por WhatsApp
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>