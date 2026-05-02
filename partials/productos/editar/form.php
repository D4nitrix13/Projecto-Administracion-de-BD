<section class="dashboard-card product-edit-card">

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="editar_producto.php" method="POST" enctype="multipart/form-data" class="product-edit-form">
        <input
            type="hidden"
            name="id_producto"
            value="<?= (int)$producto["id_producto"] ?>">

        <div class="product-form-section">
            <div class="product-section-title">
                <h3>Información del producto</h3>
                <p>Datos principales que identifican el producto dentro del inventario.</p>
            </div>

            <div class="product-form-grid cols-2">
                <div class="form-group">
                    <label class="label">Código (*)</label>
                    <input
                        type="text"
                        name="codigo"
                        class="input"
                        maxlength="50"
                        required
                        value="<?= htmlspecialchars($producto["codigo"]) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Nombre del producto (*)</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($producto["nombre"]) ?>">
                </div>

                <div class="form-group form-group-full">
                    <label class="label">Descripción</label>
                    <textarea
                        name="descripcion"
                        class="input product-textarea"
                        rows="4"
                        placeholder="Detalles del producto..."><?= htmlspecialchars($producto["descripcion"] ?? "") ?></textarea>
                </div>
            </div>
        </div>

        <div class="product-form-section">
            <div class="product-section-title">
                <h3>Imagen del producto</h3>
                <p>Imagen visible en el catálogo o en la gestión interna del producto.</p>
            </div>

            <div class="product-image-row">
                <div class="product-image-preview">
                    <?php if (!empty($producto["imagen"])): ?>
                        <a
                            href="ver_imagen_producto.php?id=<?= (int)$producto["id_producto"] ?>"
                            title="Ver imagen en grande">
                            <img
                                src="uploads/productos/<?= htmlspecialchars($producto["imagen"]) ?>"
                                alt="<?= htmlspecialchars($producto["nombre"]) ?>"
                                class="product-thumb">
                        </a>
                    <?php else: ?>
                        <div class="product-thumb product-thumb-empty">
                            Sin imagen
                        </div>
                    <?php endif; ?>
                </div>

                <div class="product-image-input">
                    <label class="label">Cambiar imagen</label>

                    <input
                        type="file"
                        name="imagen"
                        class="input"
                        accept="image/*">

                    <small class="dashboard-muted">
                        Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 4MB.
                    </small>
                </div>
            </div>
        </div>

        <div class="product-form-section">
            <div class="product-section-title">
                <h3>Clasificación</h3>
                <p>Asocie el producto con una categoría y un proveedor.</p>
            </div>

            <div class="product-form-grid cols-2">
                <div class="form-group">
                    <label class="label">Categoría</label>

                    <select name="id_categoria" class="input">
                        <option value="">(Sin categoría)</option>

                        <?php foreach ($categorias as $categoria): ?>
                            <option
                                value="<?= (int)$categoria["id_categoria"] ?>"
                                <?= ((int)($producto["id_categoria"] ?? 0) === (int)$categoria["id_categoria"]) ? "selected" : "" ?>>
                                <?= htmlspecialchars($categoria["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label">Proveedor</label>

                    <select name="id_proveedor" class="input">
                        <option value="">(Sin proveedor)</option>

                        <?php foreach ($proveedores as $proveedor): ?>
                            <option
                                value="<?= (int)$proveedor["id_proveedor"] ?>"
                                <?= ((int)($producto["id_proveedor"] ?? 0) === (int)$proveedor["id_proveedor"]) ? "selected" : "" ?>>
                                <?= htmlspecialchars($proveedor["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="product-form-section">
            <div class="product-section-title">
                <h3>Precios e inventario</h3>
                <p>Configure los valores comerciales y la existencia disponible.</p>
            </div>

            <div class="product-form-grid cols-3">
                <div class="form-group">
                    <label class="label">Precio de compra (*)</label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="precio_compra"
                        class="input"
                        required
                        value="<?= htmlspecialchars((string)$producto["precio_compra"]) ?>">
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
                        value="<?= htmlspecialchars((string)$producto["precio_venta"]) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Stock (*)</label>

                    <input
                        type="number"
                        step="1"
                        min="0"
                        name="stock"
                        class="input"
                        required
                        value="<?= htmlspecialchars((string)$producto["stock"]) ?>">
                </div>
            </div>
        </div>

        <div class="product-form-actions">
            <a href="productos.php" class="btn-secondary-inline">
                Cancelar
            </a>

            <button type="submit" class="btn-primary-inline product-save-btn">
                Guardar cambios
            </button>
        </div>
    </form>

</section>