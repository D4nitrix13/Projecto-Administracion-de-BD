<?php if (!empty($flash_success)): ?>
    <div class="products-alert products-alert-success">
        <span><?= htmlspecialchars($flash_success) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($flash_error)): ?>
    <div class="products-alert products-alert-danger">
        <strong>No se pudo completar la operación</strong>
        <span><?= htmlspecialchars($flash_error) ?></span>
    </div>
<?php endif; ?>