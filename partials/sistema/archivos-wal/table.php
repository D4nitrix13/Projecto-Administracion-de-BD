<section class="wal-card">
    <div class="wal-card-header">
        <div>
            <span class="wal-badge wal-badge-info">Archivos</span>

            <h2>Segmentos archivados</h2>

            <p>
                Estos archivos provienen del directorio de archivado WAL configurado para PostgreSQL.
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
                <article class="wal-item">
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
                                <span class="wal-chip"><?= htmlspecialchars($archivo["type"]) ?></span>
                                <span class="wal-chip"><?= htmlspecialchars($archivo["size_label"]) ?></span>
                            </div>
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
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>