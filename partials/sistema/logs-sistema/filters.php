<section class="logs-filter-card">
    <div class="logs-card-header">
        <div>
            <span class="logs-badge logs-badge-info">Filtros</span>

            <h2>Buscar logs</h2>

            <p>
                Use una búsqueda general y filtros simples para encontrar registros rápidamente.
            </p>
        </div>

        <a href="logs_sistema.php" class="logs-secondary-button">
            Limpiar
        </a>
    </div>

    <form method="GET" class="logs-filter-form logs-filter-form-clean">
        <div class="logs-field logs-field-search">
            <label for="search">Búsqueda general</label>

            <input
                type="search"
                name="search"
                id="search"
                placeholder="Buscar por archivo, tipo, origen o fecha..."
                value="<?= htmlspecialchars($searchFilter) ?>">
        </div>

        <div class="logs-field">
            <label for="source">Origen</label>

            <select name="source" id="source">
                <option value="">Todos</option>

                <?php foreach ($allowedSources as $sourceKey => $sourceData): ?>
                    <option
                        value="<?= htmlspecialchars($sourceKey) ?>"
                        <?= $sourceFilter === $sourceKey ? "selected" : "" ?>>
                        <?= htmlspecialchars($sourceData["label"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="logs-field">
            <label for="type">Tipo</label>

            <select name="type" id="type">
                <option value="">Todos</option>

                <?php foreach ($availableTypes as $type): ?>
                    <option
                        value="<?= htmlspecialchars($type) ?>"
                        <?= $typeFilter === $type ? "selected" : "" ?>>
                        <?= htmlspecialchars($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="logs-field">
            <label for="delete_status">Estado</label>

            <select name="delete_status" id="delete_status">
                <option value="" <?= $deleteFilter === "" ? "selected" : "" ?>>
                    Todos
                </option>

                <option value="active" <?= $deleteFilter === "active" ? "selected" : "" ?>>
                    Activos
                </option>

                <option value="pending" <?= $deleteFilter === "pending" ? "selected" : "" ?>>
                    Borrado pendiente
                </option>
            </select>
        </div>

        <div class="logs-field">
            <label for="date">Fecha</label>

            <input
                type="date"
                name="date"
                id="date"
                value="<?= htmlspecialchars($dateFilter) ?>">
        </div>

        <button type="submit" class="logs-primary-button">
            Filtrar
        </button>
    </form>
</section>