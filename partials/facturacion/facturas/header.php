<section class="dashboard-card dashboard-welcome">
    <p class="dashboard-eyebrow">Facturación</p>

    <h1 class="dashboard-title">Historial de facturas</h1>

    <p class="dashboard-muted">
        <?= htmlspecialchars($textoSubtitulo) ?>
    </p>
</section>

<details class="dashboard-card facturas-status-help-compact" open>
    <summary>
        <span>Guía rápida de estados</span>
        <small>Ver significado de pago y producción</small>
    </summary>

    <div class="status-guide-table">
        <div class="status-guide-group">
            <h3>Pago</h3>

            <div class="status-guide-row">
                <span class="status-badge badge-warning">Pendiente</span>
                <p>No se ha registrado ningún pago.</p>
            </div>

            <div class="status-guide-row">
                <span class="status-badge badge-info">Parcial</span>
                <p>El cliente realizó un abono.</p>
            </div>

            <div class="status-guide-row">
                <span class="status-badge badge-success">Pagado</span>
                <p>Factura cancelada completamente.</p>
            </div>
        </div>

        <div class="status-guide-group">
            <h3>Producción</h3>

            <div class="status-guide-row">
                <span class="status-badge badge-warning">Pendiente</span>
                <p>La orden aún no inicia.</p>
            </div>

            <div class="status-guide-row">
                <span class="status-badge badge-primary">En producción</span>
                <p>El pedido está siendo trabajado.</p>
            </div>

            <div class="status-guide-row">
                <span class="status-badge badge-info">Lista para entregar</span>
                <p>El pedido está terminado.</p>
            </div>

            <div class="status-guide-row">
                <span class="status-badge badge-success">Entregada</span>
                <p>El pedido fue entregado.</p>
            </div>

            <div class="status-guide-row">
                <span class="status-badge badge-danger">Cancelada</span>
                <p>La orden fue anulada.</p>
            </div>
        </div>
    </div>
</details>