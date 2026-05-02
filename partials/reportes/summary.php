<section class="summary-grid reports-summary">
    <article class="summary-card clickable-card" onclick="window.location.href='clientes.php'">
        <p>Clientes</p>
        <h2><?= htmlspecialchars((string)$totalClientes) ?></h2>
        <span class="positive">Registrados</span>
        <small>Ver clientes</small>
    </article>

    <article class="summary-card clickable-card" onclick="window.location.href='facturas.php'">
        <p>Facturas</p>
        <h2><?= htmlspecialchars((string)$totalFacturas) ?></h2>
        <span class="positive">Emitidas</span>
        <small>Ver facturas</small>
    </article>

    <article class="summary-card">
        <p>Ventas reportadas</p>
        <h2>C$ <?= number_format((float)$totalVentas, 2) ?></h2>
        <span class="positive">Ingresos acumulados</span>
        <small>Según filtros aplicados</small>
    </article>

    <article class="summary-card clickable-card" onclick="window.location.href='productos.php?stock=bajo'">
        <p>Stock bajo</p>
        <h2><?= htmlspecialchars((string)$stockBajo) ?></h2>
        <span class="negative">Requiere revisión</span>
        <small>Ver productos</small>
    </article>
</section>