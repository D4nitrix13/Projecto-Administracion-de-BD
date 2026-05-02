<form method="GET" class="reports-filter-bar">
    <div class="form-group">
        <label class="label">Tipo de reporte</label>

        <select name="tipo" class="input">
            <option value="general" <?= $tipoReporte === "general" ? "selected" : "" ?>>
                General
            </option>

            <option value="ventas" <?= $tipoReporte === "ventas" ? "selected" : "" ?>>
                Ventas
            </option>

            <option value="productos" <?= $tipoReporte === "productos" ? "selected" : "" ?>>
                Productos
            </option>

            <option value="clientes" <?= $tipoReporte === "clientes" ? "selected" : "" ?>>
                Clientes
            </option>
        </select>
    </div>

    <div class="form-group">
        <label class="label">Desde</label>

        <input
            type="date"
            name="desde"
            class="input"
            value="<?= htmlspecialchars($fechaDesde) ?>">
    </div>

    <div class="form-group">
        <label class="label">Hasta</label>

        <input
            type="date"
            name="hasta"
            class="input"
            value="<?= htmlspecialchars($fechaHasta) ?>">
    </div>

    <div class="reports-filter-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>

        <a href="reportes.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>