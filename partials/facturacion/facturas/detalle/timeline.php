<section class="invoice-timeline-card">
    <div class="invoice-section-header">
        <div>
            <h3>Historial de acciones</h3>
            <p>Registro cronológico de pagos, cambios de estado, entregas y cancelaciones.</p>
        </div>
    </div>

    <?php if (empty($historialEstados)): ?>
        <p class="empty-message">
            Esta factura todavía no tiene historial de acciones registrado.
        </p>
    <?php else: ?>
        <div class="invoice-timeline">
            <?php foreach ($historialEstados as $evento): ?>
                <article class="invoice-timeline-item">
                    <div class="invoice-timeline-dot"></div>

                    <div class="invoice-timeline-content">
                        <div class="invoice-timeline-header">
                            <strong><?= htmlspecialchars($evento["tipo_evento"]) ?></strong>
                            <span><?= htmlspecialchars(date("d/m/Y H:i", strtotime($evento["fecha_evento"]))) ?></span>
                        </div>

                        <p><?= htmlspecialchars($evento["comentario"] ?? "Cambio registrado.") ?></p>

                        <div class="invoice-timeline-meta">
                            <?php if (!empty($evento["estado_pago_nuevo"])): ?>
                                <span>Pago: <?= htmlspecialchars($evento["estado_pago_nuevo"]) ?></span>
                            <?php endif; ?>

                            <?php if (!empty($evento["estado_produccion_nuevo"])): ?>
                                <span>Producción: <?= htmlspecialchars($evento["estado_produccion_nuevo"]) ?></span>
                            <?php endif; ?>

                            <?php if ((float)($evento["monto_abonado"] ?? 0) > 0): ?>
                                <span>Abono: C$ <?= number_format((float)$evento["monto_abonado"], 2) ?></span>
                            <?php endif; ?>

                            <?php if (isset($evento["saldo_nuevo"])): ?>
                                <span>Saldo: C$ <?= number_format((float)$evento["saldo_nuevo"], 2) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>