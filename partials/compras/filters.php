<form method="get" class="filters-panel">
    <div class="filter-field filter-field-full">
        <label for="q">Buscar compra</label>
        <input
            type="text"
            id="q"
            name="q"
            placeholder="Proveedor, usuario o ID de compra..."
            value="<?= htmlspecialchars($busqueda) ?>">
    </div>

    <div class="filter-field">
        <label for="proveedor">Proveedor</label>
        <select id="proveedor" name="proveedor">
            <option value="">Todos</option>

            <?php foreach ($proveedores as $proveedor): ?>
                <option
                    value="<?= (int)$proveedor["id_proveedor"] ?>"
                    <?= ($proveedorFiltroInt === (int)$proveedor["id_proveedor"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($proveedor["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-field">
        <label for="usuario">Usuario</label>
        <select id="usuario" name="usuario">
            <option value="">Todos</option>

            <?php foreach ($usuarios as $usuario): ?>
                <option
                    value="<?= (int)$usuario["id_usuario"] ?>"
                    <?= ($usuarioFiltroInt === (int)$usuario["id_usuario"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($usuario["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-field">
        <label for="desde">Desde</label>
        <input
            type="date"
            id="desde"
            name="desde"
            value="<?= htmlspecialchars($fechaDesde) ?>">
    </div>

    <div class="filter-field">
        <label for="hasta">Hasta</label>
        <input
            type="date"
            id="hasta"
            name="hasta"
            value="<?= htmlspecialchars($fechaHasta) ?>">
    </div>

    <div class="filter-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>

        <a href="compras.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>