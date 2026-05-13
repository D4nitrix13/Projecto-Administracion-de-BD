<section class="dashboard-card historial-filter-card">
    <form method="GET" class="historial-filters">
        <label class="historial-filter-field historial-filter-search">
            <span>Buscar</span>
            <input
                type="text"
                name="q"
                placeholder="Factura, evento o comentario..."
                value="<?= htmlspecialchars($busqueda) ?>">
        </label>

        <label class="historial-filter-field">
            <span>Tipo de evento</span>
            <select name="tipo_evento">
                <option value="">Todos</option>

                <?php foreach ($tiposEvento as $tipoEvento): ?>
                    <option
                        value="<?= htmlspecialchars($tipoEvento) ?>"
                        <?= $tipoEventoFiltro === $tipoEvento ? "selected" : "" ?>>
                        <?= htmlspecialchars($tipoEvento) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="historial-filter-field">
            <span>Estado de pago</span>
            <select name="estado_pago">
                <option value="">Todos</option>
                <option value="Pendiente" <?= $estadoPagoFiltro === "Pendiente" ? "selected" : "" ?>>Pendiente</option>
                <option value="Parcial" <?= $estadoPagoFiltro === "Parcial" ? "selected" : "" ?>>Parcial</option>
                <option value="Pagado" <?= $estadoPagoFiltro === "Pagado" ? "selected" : "" ?>>Pagado</option>
            </select>
        </label>

        <label class="historial-filter-field">
            <span>Producción</span>
            <select name="estado_produccion">
                <option value="">Todos</option>
                <option value="Pendiente" <?= $estadoProduccionFiltro === "Pendiente" ? "selected" : "" ?>>Pendiente</option>
                <option value="En producción" <?= $estadoProduccionFiltro === "En producción" ? "selected" : "" ?>>En producción</option>
                <option value="Lista para entregar" <?= $estadoProduccionFiltro === "Lista para entregar" ? "selected" : "" ?>>Lista para entregar</option>
                <option value="Entregada" <?= $estadoProduccionFiltro === "Entregada" ? "selected" : "" ?>>Entregada</option>
                <option value="Cancelada" <?= $estadoProduccionFiltro === "Cancelada" ? "selected" : "" ?>>Cancelada</option>
            </select>
        </label>

        <label class="historial-filter-field">
            <span>Desde</span>
            <input
                type="date"
                name="desde"
                value="<?= htmlspecialchars($fechaDesde) ?>">
        </label>

        <label class="historial-filter-field">
            <span>Hasta</span>
            <input
                type="date"
                name="hasta"
                value="<?= htmlspecialchars($fechaHasta) ?>">
        </label>

        <div class="historial-filter-actions">
            <button type="submit" class="btn-primary-inline">
                Aplicar filtros
            </button>

            <a href="historial_estados_facturas.php" class="btn-secondary-inline">
                Limpiar
            </a>
        </div>
    </form>
</section>