<aside class="logs-viewer-card">
    <div class="logs-card-header">
        <div>
            <span class="logs-badge logs-badge-info">Vista previa</span>

            <h2>Contenido del log</h2>

            <p>
                Se muestran las últimas líneas del archivo seleccionado.
            </p>
        </div>
    </div>

    <?php if (!$selectedLog): ?>
        <div class="logs-empty">
            <strong>Ningún log seleccionado.</strong>
            <p>Use el botón “Ver” en la tabla para cargar el contenido de un archivo.</p>
        </div>
    <?php else: ?>
        <div class="logs-selected-info">
            <div>
                <span>Archivo</span>
                <strong><?= htmlspecialchars($selectedLog["filename"]) ?></strong>
            </div>

            <div>
                <span>Origen</span>
                <strong><?= htmlspecialchars($selectedLog["source_label"]) ?></strong>
            </div>

            <div>
                <span>Tamaño</span>
                <strong><?= htmlspecialchars($selectedLog["size_label"]) ?></strong>
            </div>
        </div>

        <pre class="logs-preview"><code><?= htmlspecialchars((string)$selectedContent) ?></code></pre>

        <a
            href="descargar_log_sistema.php?source=<?= urlencode($selectedLog["source"]) ?>&file=<?= urlencode($selectedLog["filename"]) ?>"
            class="logs-primary-button logs-viewer-download">
            Descargar archivo
        </a>
    <?php endif; ?>
</aside>