<section class="logs-filter-card">
    <div class="logs-card-header">
        <div>
            <span class="logs-badge logs-badge-info">Filtros</span>

            <h2>Buscar logs</h2>

            <p>
                Filtre los registros por origen, tipo, fecha o nombre de archivo.
            </p>
        </div>

        <a href="logs_sistema.php" class="logs-secondary-button">
            Limpiar filtros
        </a>
    </div>

    <form method="GET" class="logs-filter-form">
        <div class="logs-field">
            <label for="source">Origen</label>

            <select name="source" id="source">
                <option value="">Todos los orígenes</option>

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
                <option value="">Todos los tipos</option>

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
            <label for="date">Fecha</label>

            <input
                type="date"
                name="date"
                id="date"
                value="<?= htmlspecialchars($dateFilter) ?>">
        </div>

        <div class="logs-field logs-field-search">
            <label for="search">Buscar</label>

            <input
                type="search"
                name="search"
                id="search"
                placeholder="Ej. backup, postgres, cron"
                value="<?= htmlspecialchars($searchFilter) ?>">
        </div>

        <button type="submit" class="logs-primary-button">
            Aplicar filtros
        </button>
    </form>
</section>