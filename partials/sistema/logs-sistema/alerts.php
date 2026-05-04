<?php if ($error): ?>
    <div class="logs-alert logs-alert-error">
        <?= nl2br(htmlspecialchars($error)) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="logs-alert logs-alert-success">
        <?= nl2br(htmlspecialchars($success)) ?>
    </div>
<?php endif; ?>