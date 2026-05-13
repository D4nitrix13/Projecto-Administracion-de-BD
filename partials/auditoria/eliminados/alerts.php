<?php if ($flashSuccess): ?>
    <div class="audit-alert audit-alert-success">
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div class="audit-alert audit-alert-danger">
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<div class="audit-note">
    <span class="audit-note-icon">i</span>

    <div>
        Si restaura un registro, volverá a estar disponible en su sección original. Si lo elimina permanentemente, ya no podrá recuperarlo desde este panel.
    </div>
</div>