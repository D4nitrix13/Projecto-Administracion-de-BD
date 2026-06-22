<section class="logs-card logs-table-card">
    <div class="logs-card-header">
        <div>
            <span class="logs-badge logs-badge-info">Archivos</span>

            <h2>Registros disponibles</h2>

            <p>
                Consulte, descargue o programe el borrado de logs.
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
        <div class="logs-list">
            <?php foreach ($filteredLogs as $log): ?>
                <article class="logs-item <?= $log["delete_pending"] ? "logs-item-pending" : "" ?>">
                    <div class="logs-item-main">
                        <div class="logs-file-icon">
                            LOG
                        </div>

                        <div class="logs-file-info">
                            <h3 title="<?= htmlspecialchars($log["filename"]) ?>">
                                <?= htmlspecialchars($log["filename"]) ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($log["source_description"]) ?>
                            </p>

                            <div class="logs-meta-row">
                                <span class="logs-chip">
                                    <?= htmlspecialchars($log["source_label"]) ?>
                                </span>

                                <span class="logs-chip">
                                    <?= htmlspecialchars($log["type"]) ?>
                                </span>

                                <span class="logs-chip">
                                    <?= htmlspecialchars($log["size_label"]) ?>
                                </span>

                                <?php if ($log["delete_pending"]): ?>
                                    <span class="logs-chip logs-chip-danger">
                                        Borrado pendiente
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($log["delete_pending"]): ?>
                                <small class="logs-delete-note">
                                    Se eliminará: <?= htmlspecialchars((string)$log["delete_at_label"]) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="logs-date-box">
                        <span>Última modificación</span>

                        <strong>
                            <?= htmlspecialchars($log["modified_label"]) ?>
                        </strong>
                    </div>

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

                        <?php if ($log["delete_pending"]): ?>
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="cancel_delete">
                                <input type="hidden" name="source" value="<?= htmlspecialchars($log["source"]) ?>">
                                <input type="hidden" name="filename" value="<?= htmlspecialchars($log["filename"]) ?>">

                                <button
                                    type="submit"
                                    class="logs-action-button logs-action-button-warning"
                                    onclick="return confirm('¿Desea cancelar el borrado programado de este log?');">
                                    Cancelar borrado
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="schedule_delete">
                                <input type="hidden" name="source" value="<?= htmlspecialchars($log["source"]) ?>">
                                <input type="hidden" name="filename" value="<?= htmlspecialchars($log["filename"]) ?>">

                                <button
                                    type="submit"
                                    class="logs-action-button logs-action-button-danger"
                                    onclick="return confirm('¿Desea programar el borrado de este log? Se eliminará después de 24 horas.');">
                                    Borrar en 24h
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
$baseUrl = "logs_sistema.php";
$filtrosActuales = $filtrosGET ?? [];
require __DIR__ . "/../../shared/pagination.php";
?>