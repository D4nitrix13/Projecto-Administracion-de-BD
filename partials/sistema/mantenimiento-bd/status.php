<section class="mantenimiento-summary-grid">
    <article class="mantenimiento-summary-card">
        <span>Backups completos</span>
        <strong><?= htmlspecialchars((string)$resumenFull["total"]) ?></strong>
        <small><?= htmlspecialchars($resumenFull["ultima_fecha"]) ?></small>
    </article>

    <article class="mantenimiento-summary-card">
        <span>Backups rápidos</span>
        <strong><?= htmlspecialchars((string)$resumenDiff["total"]) ?></strong>
        <small><?= htmlspecialchars($resumenDiff["ultima_fecha"]) ?></small>
    </article>

    <article class="mantenimiento-summary-card">
        <span>Logs del sistema</span>
        <strong><?= htmlspecialchars((string)($resumenBackupLogs["total"] + $resumenDatabaseLogs["total"])) ?></strong>
        <small>Backups y PostgreSQL</small>
    </article>

    <article class="mantenimiento-summary-card mantenimiento-summary-card-blue">
        <span>Archivos WAL</span>
        <strong><?= htmlspecialchars((string)$resumenWal["total"]) ?></strong>
        <small><?= htmlspecialchars(mantenimientoFormatearTamano((int)$resumenWal["tamano"])) ?></small>
    </article>
</section>