<form method="get" class="proveedores-filtros-bar" style="margin-top:14px; margin-bottom:16px;">
    <div class="filtro-item-full">
        <label for="q" class="label">Buscar cliente</label>

        <input
            type="text"
            id="q"
            name="q"
            class="input"
            placeholder="Buscar por ID, nombre completo, teléfono, dirección, identificación o tipo..."
            value="<?= htmlspecialchars($busqueda) ?>">
    </div>

    <div class="filtro-item">
        <label for="tipo" class="label">Tipo de cliente</label>

        <select
            id="tipo"
            name="tipo"
            class="input"
            <?= $soloDetallista ? "disabled" : "" ?>>
            <option value="">Todos</option>

            <option value="Mayorista" <?= $tipoFiltro === "Mayorista" ? "selected" : "" ?>>
                Mayorista
            </option>

            <option value="Detallista" <?= $tipoFiltro === "Detallista" ? "selected" : "" ?>>
                Detallista
            </option>
        </select>

        <?php if ($soloDetallista): ?>
            <small class="dashboard-muted" style="font-size:12px;display:block;margin-top:4px;">
                Su rol solo permite ver clientes de tipo Detallista.
            </small>
        <?php endif; ?>
    </div>

    <div class="filtro-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>

        <a href="clientes.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>