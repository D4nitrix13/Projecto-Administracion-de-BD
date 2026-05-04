<section class="summary-grid programar-summary-grid">
    <article class="summary-card programar-summary-card <?= $enabled ? "programar-summary-success" : "programar-summary-danger" ?>">
        <p>Estado</p>
        <h2><?= $enabled ? "Activo" : "Pausado" ?></h2>
        <span class="<?= $enabled ? "positive" : "negative" ?>">
            <?= $enabled ? "Programación habilitada" : "Programación detenida" ?>
        </span>
        <small>
            <?= $enabled ? "El sistema puede ejecutar backups automáticos." : "No se ejecutarán backups automáticos." ?>
        </small>
    </article>

    <article class="summary-card programar-summary-card">
        <p>Tipo de respaldo</p>
        <h2><?= htmlspecialchars($typeLabel) ?></h2>
        <span class="positive">Configurado</span>
        <small><?= htmlspecialchars($typeDescription) ?></small>
    </article>

    <article class="summary-card programar-summary-card">
        <p>Frecuencia</p>
        <h2><?= htmlspecialchars($frequencyLabel) ?></h2>
        <span class="positive">Intervalo actual</span>
        <small>Esta es la frecuencia guardada para la programación.</small>
    </article>

    <article class="summary-card programar-summary-card programar-summary-info">
        <p>Próxima ejecución</p>
        <h2><?= $enabled ? "Programada" : "Sin fecha" ?></h2>
        <span class="<?= $enabled ? "positive" : "negative" ?>">
            <?= $enabled ? "Pendiente" : "Pausada" ?>
        </span>
        <small><?= htmlspecialchars(formatearFechaProgramacion($nextRunAt)) ?></small>
    </article>
</section>