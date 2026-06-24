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

    <div class="filtro-item">
        <label for="orden" class="label">Ordenar por</label>
        <select id="orden" name="orden" class="input">
            <option value="nombre" <?= $filtroOrden === 'nombre' ? 'selected' : '' ?>>Nombre (A-Z)</option>
            <option value="mas_vendidos_mes" <?= $filtroOrden === 'mas_vendidos_mes' ? 'selected' : '' ?>>Más vendidos (mes)</option>
            <option value="menos_vendidos_mes" <?= $filtroOrden === 'menos_vendidos_mes' ? 'selected' : '' ?>>Menos vendidos (mes)</option>
            <option value="mas_vendidos_semana" <?= $filtroOrden === 'mas_vendidos_semana' ? 'selected' : '' ?>>Más vendidos (semana)</option>
            <option value="menos_vendidos_semana" <?= $filtroOrden === 'menos_vendidos_semana' ? 'selected' : '' ?>>Menos vendidos (semana)</option>
            <option value="mas_vendidos_anio" <?= $filtroOrden === 'mas_vendidos_anio' ? 'selected' : '' ?>>Más vendidos (año)</option>
            <option value="menos_vendidos_anio" <?= $filtroOrden === 'menos_vendidos_anio' ? 'selected' : '' ?>>Menos vendidos (año)</option>
            <option value="total_ventas" <?= $filtroOrden === 'total_ventas' ? 'selected' : '' ?>>Total ventas (todo)</option>
            <option value="stock_bajo" <?= $filtroOrden === 'stock_bajo' ? 'selected' : '' ?>>Stock bajo primero</option>
            <option value="precio_mayor" <?= $filtroOrden === 'precio_mayor' ? 'selected' : '' ?>>Precio mayor</option>
            <option value="precio_menor" <?= $filtroOrden === 'precio_menor' ? 'selected' : '' ?>>Precio menor</option>
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