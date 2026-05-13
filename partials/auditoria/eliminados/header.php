<section class="audit-hero audit-hero-simple">
    <div>
        <span class="audit-simple-section">Sistema</span>

        <h1 class="audit-title">
            Registros eliminados
        </h1>

        <p class="audit-description">
            Administre los registros que fueron eliminados del sistema. Puede revisarlos, restaurarlos o eliminarlos de forma permanente.
        </p>
    </div>
</section>

<section class="audit-summary-grid">
    <article class="audit-summary-card">
        <div class="audit-summary-top">
            <span>Total eliminados</span>
        </div>

        <strong><?= (int)($resumen["total_eliminados"] ?? 0) ?></strong>
        <small>Registros disponibles</small>
    </article>

    <article class="audit-summary-card products">
        <div class="audit-summary-top">
            <span>Productos</span>
        </div>

        <strong><?= (int)($resumen["productos"] ?? 0) ?></strong>
        <small>Productos eliminados</small>
    </article>

    <article class="audit-summary-card clients">
        <div class="audit-summary-top">
            <span>Clientes</span>
        </div>

        <strong><?= (int)($resumen["clientes"] ?? 0) ?></strong>
        <small>Clientes eliminados</small>
    </article>

    <article class="audit-summary-card categories">
        <div class="audit-summary-top">
            <span>Categorías</span>
        </div>

        <strong><?= (int)($resumen["categorias"] ?? 0) ?></strong>
        <small>Categorías eliminadas</small>
    </article>

    <article class="audit-summary-card providers">
        <div class="audit-summary-top">
            <span>Proveedores</span>
        </div>

        <strong><?= (int)($resumen["proveedores"] ?? 0) ?></strong>
        <small>Proveedores eliminados</small>
    </article>
</section>