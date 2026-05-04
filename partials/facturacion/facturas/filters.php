<form method="get" class="proveedores-filtros-bar" style="margin-top:14px; margin-bottom:16px;">
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