<form method="GET" class="catalog-filter-bar">
    <div class="form-group">
        <label for="q" class="label">Buscar producto</label>

        <input
            type="text"
            id="q"
            name="q"
            placeholder="Nombre, código, descripción o categoría..."
            value="<?= htmlspecialchars($busquedaTexto) ?>"
            class="input">
    </div>

    <div class="form-group">
        <label for="categoria" class="label">Categoría</label>

        <select id="categoria" name="categoria" class="input">
            <option value="">Todas las categorías</option>

            <?php foreach ($categorias as $categoria): ?>
                <option
                    value="<?= (int)$categoria["id_categoria"] ?>"
                    <?= ($filtroCategoria === (int)$categoria["id_categoria"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($categoria["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="disponibilidad" class="label">Disponibilidad</label>

        <select id="disponibilidad" name="disponibilidad" class="input">
            <option value="">Todos</option>

            <option value="disponible" <?= $filtroDisponibilidad === "disponible" ? "selected" : "" ?>>
                Disponible
            </option>

            <option value="stock_bajo" <?= $filtroDisponibilidad === "stock_bajo" ? "selected" : "" ?>>
                Stock bajo
            </option>

            <option value="agotado" <?= $filtroDisponibilidad === "agotado" ? "selected" : "" ?>>
                Agotado
            </option>
        </select>
    </div>

    <div class="catalog-filter-actions">
        <button type="submit" class="catalog-btn catalog-btn-primary">
            Aplicar filtros
        </button>

        <a href="index.php" class="catalog-btn catalog-btn-secondary">
            Limpiar
        </a>
    </div>
</form>