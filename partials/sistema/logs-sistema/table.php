<section class="logs-card logs-table-card">
    <div class="logs-card-header">
        <div>
            <span class="logs-badge logs-badge-info">Archivos</span>

            <h2>Registros disponibles</h2>

            <p>
                Seleccione un archivo para ver su contenido o descargarlo.
            </p>
        </div>

        <span class="logs-count">
            <?= htmlspecialchars((string)$totalFiltered) ?> archivo(s)
        </span>
    </div>

    <?php if (empty($filteredLogs)): ?>
        <div class="logs-empty">
            <strong>No se encontraron logs.</strong>
            <p>Pruebe limpiando los filtros o revisando si existen archivos en las carpetas de logs.</p>
        </div>
    <?php else: ?>
        <div class="logs-table-wrapper">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Origen</th>
                        <th>Tipo</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($filteredLogs as $log): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($log["filename"]) ?></strong>
                                <small><?= htmlspecialchars($log["source_description"]) ?></small>
                            </td>

                            <td>
                                <span class="logs-chip">
                                    <?= htmlspecialchars($log["source_label"]) ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["type"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["size_label"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["modified_label"]) ?>
                            </td>

                            <td>
                                <div class="logs-actions">
                                    <a
                                        href="logs_sistema.php?selected_source=<?= urlencode($log["source"]) ?>&selected_file=<?= urlencode($log["filename"]) ?>"
                                        class="logs-action-button logs-action-button-dark">
                                        Ver
                                    </a>

                                    <a
                                        href="descargar_log_sistema.php?source=<?= urlencode($log["source"]) ?>&file=<?= urlencode($log["filename"]) ?>"
                                        class="logs-action-button logs-action-button-primary">
                                        Descargar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>