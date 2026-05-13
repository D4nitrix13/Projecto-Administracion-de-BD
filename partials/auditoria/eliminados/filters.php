<section class="audit-filters-card">
    <div class="audit-section-heading">
        <div>
            <h2>Filtros de recuperación</h2>
            <p>Localice registros eliminados por contenido, tabla o rango de fechas.</p>
        </div>
    </div>

    <form method="get">
        <div class="audit-filter-grid">
            <div class="audit-field">
                <label for="q">Buscar</label>

                <input
                    type="text"
                    id="q"
                    name="q"
                    placeholder="Buscar por ID, tabla, usuario o contenido eliminado..."
                    value="<?= htmlspecialchars($busqueda) ?>">
            </div>

            <div class="audit-field">
                <label for="tabla">Tabla</label>

                <select id="tabla" name="tabla">
                    <option value="">Todas</option>
                    <option value="producto" <?= ($tablaFiltro === "producto") ? "selected" : "" ?>>Productos</option>
                    <option value="cliente" <?= ($tablaFiltro === "cliente") ? "selected" : "" ?>>Clientes</option>
                    <option value="categoria" <?= ($tablaFiltro === "categoria") ? "selected" : "" ?>>Categorías</option>
                    <option value="proveedor" <?= ($tablaFiltro === "proveedor") ? "selected" : "" ?>>Proveedores</option>
                </select>
            </div>

            <div class="audit-field">
                <label for="desde">Desde</label>

                <input
                    type="date"
                    id="desde"
                    name="desde"
                    value="<?= htmlspecialchars($fechaDesde) ?>">
            </div>

            <div class="audit-field">
                <label for="hasta">Hasta</label>

                <input
                    type="date"
                    id="hasta"
                    name="hasta"
                    value="<?= htmlspecialchars($fechaHasta) ?>">
            </div>
        </div>

        <div class="audit-filter-actions">
            <button type="submit" class="audit-btn-primary">
                Aplicar filtros
            </button>

            <a href="auditoria_eliminados.php" class="audit-btn-secondary">
                Limpiar filtros
            </a>
        </div>
    </form>
</section>