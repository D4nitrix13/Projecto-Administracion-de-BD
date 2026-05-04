<section class="wal-filter-card">
    <div class="wal-card-header">
        <div>
            <span class="wal-badge wal-badge-info">Filtros</span>

            <h2>Buscar archivos WAL</h2>

            <p>
                Filtre por nombre, tipo, fecha o tamaño para encontrar segmentos archivados.
            </p>
        </div>

        <a href="archivos_wal.php" class="wal-secondary-button">
            Limpiar filtros
        </a>
    </div>

    <form method="GET" class="wal-filter-form">
        <div class="wal-field wal-field-search">
            <label for="search">Buscar por nombre</label>

            <input
                type="search"
                name="search"
                id="search"
                placeholder="Ej. 000000, backup, partial"
                value="<?= htmlspecialchars($searchFilter) ?>">
        </div>

        <div class="wal-field">
            <label for="type">Tipo</label>

            <select name="type" id="type">
                <option value="">Todos los tipos</option>

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
            <label for="date">Fecha</label>

            <input
                type="date"
                name="date"
                id="date"
                value="<?= htmlspecialchars($dateFilter) ?>">
        </div>

        <div class="wal-field">
            <label for="sort">Ordenar por</label>

            <select name="sort" id="sort">
                <option value="date_desc" <?= $sortFilter === "date_desc" ? "selected" : "" ?>>Más recientes primero</option>
                <option value="date_asc" <?= $sortFilter === "date_asc" ? "selected" : "" ?>>Más antiguos primero</option>
                <option value="size_desc" <?= $sortFilter === "size_desc" ? "selected" : "" ?>>Mayor tamaño primero</option>
                <option value="size_asc" <?= $sortFilter === "size_asc" ? "selected" : "" ?>>Menor tamaño primero</option>
                <option value="name_asc" <?= $sortFilter === "name_asc" ? "selected" : "" ?>>Nombre A-Z</option>
                <option value="name_desc" <?= $sortFilter === "name_desc" ? "selected" : "" ?>>Nombre Z-A</option>
            </select>
        </div>

        <button type="submit" class="wal-primary-button">
            Aplicar filtros
        </button>
    </form>
</section>