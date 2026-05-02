<section class="backup-history">
    <div class="backup-history-header">
        <div>
            <h2>Archivos de respaldo disponibles</h2>
            <p class="dashboard-muted">
                Historial de respaldos generados en el sistema.
            </p>
        </div>

        <span class="backup-count">
            <?= count($archivos) ?> archivo(s)
        </span>
    </div>

    <?php if (empty($archivos)): ?>
        <div class="backup-empty">
            <strong>No hay respaldos generados todavía.</strong>
            <p>Cuando genere un respaldo, aparecerá listado en esta sección.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table-products">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Fecha</th>
                        <th>Tamaño</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($archivos as $archivo): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($archivo["nombre"]) ?></strong>
                            </td>

                            <td><?= htmlspecialchars($archivo["fecha"]) ?></td>

                            <td>
                                <?= number_format($archivo["tamanio"] / 1024, 2) ?> KB
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>