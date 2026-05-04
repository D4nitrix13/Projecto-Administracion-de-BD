<section class="restore-layout">

    <article class="restore-card">
        <div class="restore-card-header">
            <div>
                <span class="restore-badge restore-badge-danger">
                    Restauración
                </span>

                <h2>Seleccionar respaldo</h2>

                <p>
                    La restauración reemplaza la información actual de la base de datos con el contenido del archivo seleccionado.
                </p>
            </div>
        </div>

        <?php if (empty($archivos)): ?>
            <div class="restore-empty">
                <strong>No hay respaldos disponibles.</strong>

                <p>
                    Primero genere un respaldo manual o espere a que exista un respaldo automático.
                </p>

                <a href="backups_manuales.php" class="restore-primary-link">
                    Ir a backups manuales
                </a>
            </div>
        <?php else: ?>
            <form method="POST" class="restore-form">
                <input type="hidden" name="action" value="restore">

                <div class="restore-field">
                    <label for="archivo">Archivo de respaldo</label>

                    <select name="archivo" id="archivo" required>
                        <option value="">Seleccione un archivo .sql</option>

                        <?php foreach ($archivos as $archivo): ?>
                            <?php
                            $nombre = $archivo["nombre"] ?? "";
                            $tipo = $archivo["tipo"] ?? "Respaldo";
                            $fecha = restaurarFormatearFecha($archivo["fecha"] ?? null);
                            $tamano = restaurarFormatearTamano((int)($archivo["tamanio"] ?? 0));
                            $pendiente = (bool)($archivo["deletion_pending"] ?? false);
                            ?>

                            <?php if (!$pendiente): ?>
                                <option value="<?= htmlspecialchars($nombre) ?>">
                                    <?= htmlspecialchars($tipo) ?> —
                                    <?= htmlspecialchars($nombre) ?> —
                                    <?= htmlspecialchars($tamano) ?> —
                                    <?= htmlspecialchars($fecha) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <small>
                        Solo se muestran respaldos disponibles. Los archivos con borrado programado no se recomiendan para restauración.
                    </small>
                </div>

                <div class="restore-warning">
                    <strong>Advertencia importante</strong>

                    <p>
                        Esta acción reemplazará los datos actuales. Antes de restaurar, asegúrese de haber generado un respaldo reciente.
                    </p>
                </div>

                <div class="restore-field">
                    <label for="confirmacion">Confirmación</label>

                    <input
                        type="text"
                        name="confirmacion"
                        id="confirmacion"
                        autocomplete="off"
                        placeholder="Escriba RESTAURAR para confirmar"
                        required>

                    <small>
                        Para evitar restauraciones accidentales, escriba exactamente RESTAURAR en mayúsculas.
                    </small>
                </div>

                <div class="restore-actions">
                    <a href="backups_manuales.php" class="restore-secondary-button">
                        Ver respaldos
                    </a>

                    <button
                        type="submit"
                        class="restore-danger-button"
                        onclick="return confirm('¿Está seguro de restaurar la base de datos con este respaldo? Esta acción reemplazará los datos actuales.');">
                        Restaurar base de datos
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </article>

    <aside class="restore-side">

        <article class="restore-side-card">
            <span class="restore-badge restore-badge-info">
                Información
            </span>

            <h2>¿Qué hace esta opción?</h2>

            <p>
                Toma un archivo de respaldo existente y lo carga nuevamente en la base de datos del sistema.
            </p>

            <div class="restore-info-list">
                <div>
                    <span>Archivos permitidos</span>
                    <strong>.sql</strong>
                </div>

                <div>
                    <span>Origen de respaldos</span>
                    <strong>Manual, completo y diferencial</strong>
                </div>

                <div>
                    <span>Confirmación requerida</span>
                    <strong>RESTAURAR</strong>
                </div>
            </div>
        </article>

        <article class="restore-side-card restore-side-danger">
            <span class="restore-badge restore-badge-danger">
                Precaución
            </span>

            <h2>Antes de restaurar</h2>

            <ul class="restore-check-list">
                <li>Verifique que seleccionó el respaldo correcto.</li>
                <li>Confirme que el archivo no esté vacío.</li>
                <li>Genere un respaldo nuevo antes de reemplazar datos.</li>
                <li>No cierre el sistema mientras la restauración se ejecuta.</li>
            </ul>
        </article>

    </aside>

</section>