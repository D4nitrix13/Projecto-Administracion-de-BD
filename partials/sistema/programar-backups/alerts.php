<?php if ($error): ?>
    <div class="programar-alert programar-alert-error">
        <?= nl2br(htmlspecialchars($error)) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="programar-alert programar-alert-success">
        <?= nl2br(htmlspecialchars($success)) ?>
    </div>
<?php endif; ?>