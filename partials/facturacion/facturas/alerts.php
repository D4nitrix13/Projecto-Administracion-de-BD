<?php if ($flashSuccess): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>