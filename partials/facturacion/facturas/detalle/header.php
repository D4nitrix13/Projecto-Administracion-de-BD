<section class="dashboard-card dashboard-welcome invoice-detail-hero">
    <div>
        <p class="dashboard-eyebrow">Facturación / Detalle</p>

        <h1 class="dashboard-title">
            Factura #<?= (int)$factura["id_factura"] ?>
        </h1>

        <p class="dashboard-muted">
            Consulte la información comercial, pagos, producción y trazabilidad histórica completa de esta factura.
        </p>
    </div>

    <div class="invoice-hero-actions">
        <a href="historial_estados_facturas.php?q=<?= urlencode((string)$factura["id_factura"]) ?>" class="btn-secondary-inline">
            Ver en bitácora general
        </a>

        <a href="editar_factura.php?id=<?= (int)$factura["id_factura"] ?>" class="btn-primary-inline">
            Editar factura
        </a>
    </div>
</section>