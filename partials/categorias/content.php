<section class="dashboard-card categories-hero">
    <div>
        <p class="dashboard-eyebrow">Inventario</p>
        <h1 class="dashboard-title">Categorías de productos</h1>
        <p class="dashboard-muted">
            Administre las categorías utilizadas para organizar los productos de Panda Estampados y Kitsune.
        </p>
    </div>
</section>

<section class="dashboard-card categories-panel">

    <?php if ($flash_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>

    <?php if ($flash_success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php require __DIR__ . "/form.php"; ?>

    <?php require __DIR__ . "/table.php"; ?>

</section>