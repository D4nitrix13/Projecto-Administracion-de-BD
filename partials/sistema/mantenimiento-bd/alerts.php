<?php if (!empty($error)): ?>
    <div class="mantenimiento-alert mantenimiento-alert-error">
        <div class="mantenimiento-alert-content">
            <strong class="mantenimiento-alert-title">
                Error al ejecutar el proceso.
            </strong>

            <?php if (!empty($errorDetail)): ?>
                <p class="mantenimiento-alert-text">
                    <?= nl2br(htmlspecialchars($errorDetail)) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="mantenimiento-alert mantenimiento-alert-success">
        <div class="mantenimiento-alert-content">
            <strong class="mantenimiento-alert-title">
                Proceso ejecutado correctamente.
            </strong>

            <?php if (!empty($successDetail)): ?>
                <p class="mantenimiento-alert-text">
                    <?= nl2br(htmlspecialchars($successDetail)) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>