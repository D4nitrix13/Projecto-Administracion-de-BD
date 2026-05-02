<form method="get" class="proveedores-filtros-bar" style="margin-bottom:16px;">
    <div class="filtro-item-full">
        <label for="q" class="label">Buscar trabajador</label>

        <input
            type="text"
            id="q"
            name="q"
            class="input"
            placeholder="Nombre, email, rol o sección..."
            value="<?= htmlspecialchars($busqueda) ?>">
    </div>

    <div class="filtro-item">
        <label for="rol" class="label">Rol</label>

        <select id="rol" name="rol" class="input">
            <option value="">Todos los roles</option>

            <?php foreach ($roles as $rol): ?>
                <option
                    value="<?= (int)$rol["id_rol"] ?>"
                    <?= ($rolFiltroInt === (int)$rol["id_rol"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($rol["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filtro-item">
        <label for="seccion" class="label">Sección</label>

        <select id="seccion" name="seccion" class="input">
            <option value="">Cualquier sección</option>

            <option value="none" <?= $seccionFiltro === "none" ? "selected" : "" ?>>
                Todas las secciones
            </option>

            <?php foreach ($secciones as $seccion): ?>
                <option
                    value="<?= (int)$seccion["id_seccion"] ?>"
                    <?= ($seccionFiltro === (string)(int)$seccion["id_seccion"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($seccion["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filtro-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>

        <a href="usuarios.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>