<section class="wal-card">
    <div class="wal-card-header">
        <div>
            <span class="wal-badge wal-badge-info">Archivos</span>

            <h2>Segmentos archivados</h2>

            <p>
                Consulte, descargue o programe el borrado de archivos WAL.
            </p>
        </div>

        <span class="wal-count">
            <?= htmlspecialchars((string)$totalFiltrados) ?> archivo(s)
        </span>
    </div>

    <?php if (empty($filteredWal)): ?>
        <div class="wal-empty">
            <strong>No se encontraron archivos WAL.</strong>
            <p>Pruebe limpiando los filtros o revise si PostgreSQL ya generó segmentos archivados.</p>
        </div>
    <?php else: ?>
        <div class="wal-list">
            <?php foreach ($filteredWal as $archivo): ?>
                <article class="wal-item <?= $archivo["delete_pending"] ? "wal-item-pending" : "" ?>">
                    <div class="wal-item-main">
                        <div class="wal-file-icon">
                            WAL
                        </div>

                        <div class="wal-file-info">
                            <h3 title="<?= htmlspecialchars($archivo["filename"]) ?>">
                                <?= htmlspecialchars($archivo["filename"]) ?>
                            </h3>

                            <p>
                                Archivo generado por el sistema de archivado WAL de PostgreSQL.
                            </p>

                            <div class="wal-meta-row">
                                <span class="wal-chip">
                                    <?= htmlspecialchars($archivo["type"]) ?>
                                </span>

                                <span class="wal-chip">
                                    <?= htmlspecialchars($archivo["size_label"]) ?>
                                </span>

                                <?php if ($archivo["delete_pending"]): ?>
                                    <span class="wal-chip wal-chip-danger">
                                        Borrado pendiente
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($archivo["delete_pending"]): ?>
                                <small class="wal-delete-note">
                                    Programado para eliminarse el:
                                    <strong><?= htmlspecialchars((string)$archivo["delete_at_label"]) ?></strong>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="wal-date-box">
                        <span>Última modificación</span>

                        <strong>
                            <?= htmlspecialchars($archivo["modified_label"]) ?>
                        </strong>
                    </div>

                    <div class="wal-actions">
                        <a
                            href="descargar_archivo_wal.php?file=<?= urlencode($archivo["filename"]) ?>"
                            class="wal-action-button wal-action-button-primary">
                            Descargar
                        </a>

                        <?php if ($archivo["delete_pending"]): ?>
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="cancel_delete">
                                <input type="hidden" name="filename" value="<?= htmlspecialchars($archivo["filename"]) ?>">

                                <button
                                    type="submit"
                                    class="wal-action-button wal-action-button-warning"
                                    onclick="return confirm('¿Desea cancelar el borrado programado de este archivo WAL?');">
                                    Cancelar borrado
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="schedule_delete">
                                <input type="hidden" name="filename" value="<?= htmlspecialchars($archivo["filename"]) ?>">

                                <button
                                    type="submit"
                                    class="wal-action-button wal-action-button-danger"
                                    onclick="return confirm('¿Desea programar el borrado de este archivo WAL? Se eliminará después de 24 horas.');">
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
$baseUrl = "archivos_wal.php";
$filtrosActuales = $filtrosGET ?? [];
require __DIR__ . "/../../shared/pagination.php";
?>