<?php if (!empty($masVendidos)): ?>
    <section class="catalog-most-sold">
        <div class="catalog-section-header">
            <h2 class="catalog-section-title">Los Más Vendidos</h2>
        </div>

        <div class="catalog-most-sold-scroll">
            <?php foreach ($masVendidos as $producto): ?>
                <?php
                $stock = (int) $producto["stock"];
                $vendidos = (int) ($producto["total_vendido"] ?? 0);
                $rutaImagen = __DIR__ . "/../../../uploads/productos/" . ($producto["imagen"] ?? "");
                $tieneImagen = !empty($producto["imagen"]) && is_file($rutaImagen);
                ?>

                <a
                    href="ver_detalle_producto.php?id=<?= (int)$producto["id_producto"] ?>"
                    class="catalog-most-sold-item"
                    title="<?= htmlspecialchars($producto["nombre"]) ?>">
                    <div class="catalog-most-sold-image">
                        <?php if ($tieneImagen): ?>
                            <img
                                src="uploads/productos/<?= htmlspecialchars($producto["imagen"]) ?>"
                                alt="<?= htmlspecialchars($producto["nombre"]) ?>"
                                loading="lazy">
                        <?php else: ?>
                            <div class="catalog-most-sold-placeholder">Sin imagen</div>
                        <?php endif; ?>

                        <?php if ($vendidos > 0): ?>
                            <span class="catalog-most-sold-badge">
                                <?= $vendidos ?> vendidos
                            </span>
                        <?php endif; ?>
                    </div>

                    <span class="catalog-most-sold-name">
                        <?= htmlspecialchars(recortarTextoCatalogo($producto["nombre"], 35)) ?>
                    </span>

                    <span class="catalog-most-sold-price">
                        C$ <?= number_format((float) $producto["precio_venta"], 2) ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
