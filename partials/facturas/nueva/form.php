<section class="dashboard-card invoice-create-card">

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="nueva_factura.php" method="POST" id="form-factura">

        <?php require __DIR__ . "/client-section.php"; ?>

        <?php require __DIR__ . "/products-section.php"; ?>

        <?php require __DIR__ . "/totals-section.php"; ?>

        <div class="invoice-create-actions">
            <a href="facturas.php" class="btn-secondary-inline">
                Cancelar
            </a>

            <button type="submit" class="btn-primary-inline">
                Guardar factura
            </button>
        </div>

    </form>

</section>