<form method="get" class="productos-filtros-bar">

    <?php if ($filtroIdProducto !== null): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$filtroIdProducto) ?>">
    <?php endif; ?>

    <div class="filtro-item">
        <label for="q" class="label">Buscar</label>
        <input
            type="text"
            id="q"
            name="q"
            placeholder="Código o nombre..."
            value="<?= htmlspecialchars($busquedaTexto) ?>"
            class="input">
    </div>

    <div class="filtro-item">
        <label for="categoria" class="label">Categoría</label>
        <select id="categoria" name="categoria" class="input">
            <option value="">Todas</option>
            <?php foreach ($categorias as $cat): ?>
                <option
                    value="<?= (int)$cat["id_categoria"] ?>"
                    <?= ($filtroCategoria === (int)$cat["id_categoria"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($cat["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filtro-item">
        <label for="proveedor" class="label">Proveedor</label>
        <select id="proveedor" name="proveedor" class="input">
            <option value="">Todos</option>
            <?php foreach ($proveedores as $prov): ?>
                <option
                    value="<?= (int)$prov["id_proveedor"] ?>"
                    <?= ($filtroProveedor === (int)$prov["id_proveedor"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($prov["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filtro-item">
        <label for="stock" class="label">Stock</label>
        <select id="stock" name="stock" class="input">
            <option value="">Todos</option>
            <option value="bajo" <?= $filtroStockBajo ? "selected" : "" ?>>
                Stock bajo
            </option>
        </select>
    </div>

    <div class="filtro-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>
        <a href="productos.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>