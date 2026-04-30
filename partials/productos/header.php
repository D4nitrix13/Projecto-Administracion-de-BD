<section class="dashboard-card dashboard-welcome products-hero">
    <div>
        <p class="dashboard-eyebrow">Inventario</p>
        <h1 class="dashboard-title">Listado de productos</h1>
        <p class="dashboard-muted">
            <?= htmlspecialchars($textoSubtitulo) ?>
        </p>
    </div>

    <?php if ($idRol !== 3): ?>
        <a href="nuevo_producto.php" class="btn-primary-inline">
            + Agregar producto
        </a>
    <?php endif; ?>
</section>