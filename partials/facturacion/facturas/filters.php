<form method="get" class="proveedores-filtros-bar facturas-filtros-bar">
    <div class="filtro-item-full">
        <label for="q" class="label">Buscar</label>

        <input
            type="text"
            id="q"
            name="q"
            class="input"
            placeholder="Cliente, usuario, sección o ID de factura..."
            value="<?= htmlspecialchars($busqueda) ?>">
    </div>

    <div class="filtro-item">
        <label for="seccion" class="label">Sección</label>

        <select id="seccion" name="seccion" class="input">
            <option value="">Todas</option>

            <?php foreach ($secciones as $seccion): ?>
                <option
                    value="<?= (int)$seccion["id_seccion"] ?>"
                    <?= ($seccionFiltroInt === (int)$seccion["id_seccion"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($seccion["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filtro-item">
        <label for="usuario" class="label">Usuario</label>

        <select id="usuario" name="usuario" class="input">
            <option value="">Todos</option>

            <?php foreach ($usuariosFiltro as $usuario): ?>
                <option
                    value="<?= (int)$usuario["id_usuario"] ?>"
                    <?= ($usuarioFiltroInt === (int)$usuario["id_usuario"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($usuario["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filtro-item">
        <label for="estado_pago" class="label">Estado de pago</label>

        <select id="estado_pago" name="estado_pago" class="input">
            <option value="">Todos</option>
            <option value="Pendiente" <?= ($estadoPagoFiltro === "Pendiente") ? "selected" : "" ?>>Pendiente</option>
            <option value="Parcial" <?= ($estadoPagoFiltro === "Parcial") ? "selected" : "" ?>>Parcial</option>
            <option value="Pagado" <?= ($estadoPagoFiltro === "Pagado") ? "selected" : "" ?>>Pagado</option>
        </select>
    </div>

    <div class="filtro-item">
        <label for="estado_produccion" class="label">Estado de producción</label>

        <select id="estado_produccion" name="estado_produccion" class="input">
            <option value="">Todos</option>
            <option value="Pendiente" <?= ($estadoProduccionFiltro === "Pendiente") ? "selected" : "" ?>>Pendiente</option>
            <option value="En producción" <?= ($estadoProduccionFiltro === "En producción") ? "selected" : "" ?>>En producción</option>
            <option value="Lista para entregar" <?= ($estadoProduccionFiltro === "Lista para entregar") ? "selected" : "" ?>>Lista para entregar</option>
            <option value="Entregada" <?= ($estadoProduccionFiltro === "Entregada") ? "selected" : "" ?>>Entregada</option>
            <option value="Cancelada" <?= ($estadoProduccionFiltro === "Cancelada") ? "selected" : "" ?>>Cancelada</option>
        </select>
    </div>

    <div class="filtro-item">
        <label for="desde" class="label">Desde</label>

        <input
            type="date"
            id="desde"
            name="desde"
            class="input"
            value="<?= htmlspecialchars($fechaDesde) ?>">
    </div>

    <div class="filtro-item">
        <label for="hasta" class="label">Hasta</label>

        <input
            type="date"
            id="hasta"
            name="hasta"
            class="input"
            value="<?= htmlspecialchars($fechaHasta) ?>">
    </div>

    <div class="filtro-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>

        <a href="facturas.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>