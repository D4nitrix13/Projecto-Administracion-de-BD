<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= nl2br(htmlspecialchars($error)) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?= nl2br(htmlspecialchars($success)) ?>
    </div>
<?php endif; ?>