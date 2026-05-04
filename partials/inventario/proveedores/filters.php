<form method="get" class="proveedores-filtros-bar" style="margin-bottom:16px;">
    <div class="filtro-item-full">
        <label for="q" class="label">Buscar proveedor</label>

        <input
            type="text"
            id="q"
            name="q"
            class="input"
            placeholder="ID, nombre, teléfono, email o dirección..."
            value="<?= htmlspecialchars($busqueda) ?>">
    </div>

    <div class="filtro-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>

        <a href="proveedores.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>