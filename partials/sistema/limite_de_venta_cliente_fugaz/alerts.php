<?php if ($error): ?>
    <div class="config-alert config-alert-error">
        <?= nl2br(htmlspecialchars($error)) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="config-alert config-alert-success">
        <?= nl2br(htmlspecialchars($success)) ?>
    </div>
<?php endif; ?>