<section class="historial-stats-grid">
    <article class="historial-stat-card">
        <span>Total eventos</span>
        <strong><?= number_format((int)$resumen["total_eventos"]) ?></strong>
    </article>

    <article class="historial-stat-card">
        <span>Pagos registrados</span>
        <strong><?= number_format((int)$resumen["pagos_registrados"]) ?></strong>
    </article>

    <article class="historial-stat-card">
        <span>Entregadas</span>
        <strong><?= number_format((int)$resumen["entregadas"]) ?></strong>
    </article>

    <article class="historial-stat-card danger">
        <span>Canceladas</span>
        <strong><?= number_format((int)$resumen["canceladas"]) ?></strong>
    </article>
</section>