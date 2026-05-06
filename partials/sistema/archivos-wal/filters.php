<section class="wal-filter-card">
    <div class="wal-card-header">
        <div>
            <span class="wal-badge wal-badge-info">Filtros</span>

            <h2>Buscar archivos WAL</h2>

            <p>
                Use búsqueda general, estado, tipo y fecha para localizar archivos sin repetir filtros innecesarios.
            </p>
        </div>

        <a href="archivos_wal.php" class="wal-secondary-button">
            Limpiar
        </a>
    </div>

    <form method="GET" class="wal-filter-form wal-filter-form-clean">
        <div class="wal-field wal-field-search">
            <label for="search">Búsqueda general</label>

            <input
                type="search"
                name="search"
                id="search"
                placeholder="Buscar por nombre, tipo, tamaño o fecha..."
                value="<?= htmlspecialchars($searchFilter) ?>">
        </div>

        <div class="wal-field">
            <label for="type">Tipo</label>

            <select name="type" id="type">
                <option value="">Todos</option>

                <?php foreach ($tiposDisponibles as $tipo): ?>
                    <option
                        value="<?= htmlspecialchars($tipo) ?>"
                        <?= $typeFilter === $tipo ? "selected" : "" ?>>
                        <?= htmlspecialchars($tipo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="wal-field">
            <label for="delete_status">Estado</label>

            <select name="delete_status" id="delete_status">
                <option value="" <?= $deleteFilter === "" ? "selected" : "" ?>>Todos</option>
                <option value="active" <?= $deleteFilter === "active" ? "selected" : "" ?>>Activos</option>
                <option value="pending" <?= $deleteFilter === "pending" ? "selected" : "" ?>>Borrado pendiente</option>
            </select>
        </div>

        <div class="wal-field">
            <label for="date">Fecha</label>

            <input
                type="date"
                name="date"
                id="date"
                value="<?= htmlspecialchars($dateFilter) ?>">
        </div>

        <div class="wal-field">
            <label for="sort">Orden</label>

            <select name="sort" id="sort">
                <option value="date_desc" <?= $sortFilter === "date_desc" ? "selected" : "" ?>>Recientes</option>
                <option value="date_asc" <?= $sortFilter === "date_asc" ? "selected" : "" ?>>Antiguos</option>
                <option value="size_desc" <?= $sortFilter === "size_desc" ? "selected" : "" ?>>Mayor tamaño</option>
                <option value="size_asc" <?= $sortFilter === "size_asc" ? "selected" : "" ?>>Menor tamaño</option>
                <option value="name_asc" <?= $sortFilter === "name_asc" ? "selected" : "" ?>>Nombre A-Z</option>
                <option value="name_desc" <?= $sortFilter === "name_desc" ? "selected" : "" ?>>Nombre Z-A</option>
            </select>
        </div>

        <button type="submit" class="wal-primary-button">
            Filtrar
        </button>
    </form>
</section>