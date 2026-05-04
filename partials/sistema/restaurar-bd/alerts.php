<?php if ($error): ?>
    <div class="restore-alert restore-alert-error">
        <?= nl2br(htmlspecialchars($error)) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="restore-alert restore-alert-success">
        <?= nl2br(htmlspecialchars($success)) ?>
    </div>
<?php endif; ?>