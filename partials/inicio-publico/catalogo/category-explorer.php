<?php if (!empty($productosPorCategoria)): ?>
    <section class="catalog-explorer">
        <div class="catalog-section-header">
            <h2 class="catalog-section-title">Explorar por Categoría</h2>
            <a href="catalogo.php?disponibilidad=disponible" class="catalog-section-link">
                Ver todos los productos &rarr;
            </a>
        </div>

        <div class="catalog-explorer-grid">
            <?php foreach ($productosPorCategoria as $categoria): ?>
                <?php
                $totalProd = (int) ($categoria["total_productos"] ?? 0);
                $imagen = $categoria["primera_imagen"] ?? null;
                $rutaImagen = __DIR__ . "/../../../uploads/productos/" . ($imagen ?? "");
                $tieneImagen = $imagen !== null && is_file($rutaImagen);
                ?>

                <a
                    href="catalogo.php?categoria=<?= (int)$categoria["id_categoria"] ?>"
                    class="catalog-explorer-card"
                    title="<?= htmlspecialchars($categoria["nombre"]) ?> (<?= $totalProd ?> productos)">
                    <div class="catalog-explorer-image">
                        <?php if ($tieneImagen): ?>
                            <img
                                src="uploads/productos/<?= htmlspecialchars($imagen) ?>"
                                alt="<?= htmlspecialchars($categoria["nombre"]) ?>"
                                loading="lazy">
                        <?php else: ?>
                            <div class="catalog-explorer-placeholder">
                                <?= strtoupper(mb_substr($categoria["nombre"], 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="catalog-explorer-info">
                        <span class="catalog-explorer-name">
                            <?= htmlspecialchars($categoria["nombre"]) ?>
                        </span>
                        <span class="catalog-explorer-count">
                            <?= $totalProd ?> producto<?= $totalProd !== 1 ? "s" : "" ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
