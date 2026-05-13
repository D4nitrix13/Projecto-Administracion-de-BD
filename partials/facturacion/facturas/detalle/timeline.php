<?php
function invoiceTimelineBadgeClass(?string $estado): string
{
    return match ($estado) {
        "Pagado", "Entregada" => "status-success",
        "Parcial", "Lista para entregar" => "status-info",
        "En producción" => "status-info",
        "Cancelada" => "status-danger",
        default => "status-warning",
    };
}

function invoiceTimelineEventClass(?string $tipoEvento): string
{
    $tipoEvento = strtolower((string)$tipoEvento);

    if (str_contains($tipoEvento, "cancel")) {
        return "danger";
    }

    if (
        str_contains($tipoEvento, "pago") ||
        str_contains($tipoEvento, "abono")
    ) {
        return "success";
    }

    if (
        str_contains($tipoEvento, "producción") ||
        str_contains($tipoEvento, "produccion") ||
        str_contains($tipoEvento, "entrega") ||
        str_contains($tipoEvento, "lista")
    ) {
        return "info";
    }

    return "default";
}
?>

<section class="invoice-timeline-card">
    <div class="invoice-section-header invoice-section-header-actions">
        <div>
            <h3>Trazabilidad de la factura</h3>
            <p>Línea de tiempo con pagos, cambios de estado, producción, entregas y cancelaciones.</p>
        </div>

        <a
            href="historial_estados_facturas.php?q=<?= urlencode((string)$factura["id_factura"]) ?>"
            class="btn-secondary-inline">
            Ver bitácora general
        </a>
    </div>

    <?php if (empty($historialEstados)): ?>
        <p class="empty-message">
            Esta factura todavía no tiene historial de acciones registrado.
        </p>
    <?php else: ?>
        <div class="invoice-timeline">
            <?php foreach ($historialEstados as $index => $evento): ?>
                <?php
                $tipoEvento = $evento["tipo_evento"] ?? "Evento registrado";
                $eventClass = invoiceTimelineEventClass($tipoEvento);

                $estadoPagoAnterior = $evento["estado_pago_anterior"] ?? null;
                $estadoPagoNuevo = $evento["estado_pago_nuevo"] ?? null;

                $estadoProduccionAnterior = $evento["estado_produccion_anterior"] ?? null;
                $estadoProduccionNuevo = $evento["estado_produccion_nuevo"] ?? null;

                $montoAbonado = (float)($evento["monto_abonado"] ?? 0);
                $saldoNuevo = $evento["saldo_nuevo"] ?? null;
                ?>

                <article class="invoice-timeline-item invoice-timeline-item-<?= htmlspecialchars($eventClass) ?>">
                    <div class="invoice-timeline-marker">
                        <span><?= $index + 1 ?></span>
                    </div>

                    <div class="invoice-timeline-content">
                        <div class="invoice-timeline-header">
                            <div>
                                <strong><?= htmlspecialchars($tipoEvento) ?></strong>

                                <small>
                                    Evento #<?= $index + 1 ?> de <?= count($historialEstados) ?>
                                </small>
                            </div>

                            <time>
                                <?= htmlspecialchars(date("d/m/Y H:i", strtotime($evento["fecha_evento"]))) ?>
                            </time>
                        </div>

                        <p class="invoice-timeline-comment">
                            <?= htmlspecialchars($evento["comentario"] ?? "Cambio registrado.") ?>
                        </p>

                        <div class="invoice-timeline-change-grid">
                            <div class="invoice-timeline-change">
                                <span>Pago</span>

                                <div>
                                    <?php if (!empty($estadoPagoAnterior)): ?>
                                        <span class="status-badge <?= invoiceTimelineBadgeClass($estadoPagoAnterior) ?>">
                                            <?= htmlspecialchars($estadoPagoAnterior) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="invoice-empty-pill">Sin dato</span>
                                    <?php endif; ?>

                                    <strong>→</strong>

                                    <?php if (!empty($estadoPagoNuevo)): ?>
                                        <span class="status-badge <?= invoiceTimelineBadgeClass($estadoPagoNuevo) ?>">
                                            <?= htmlspecialchars($estadoPagoNuevo) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="invoice-empty-pill">Sin cambio</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="invoice-timeline-change">
                                <span>Producción</span>

                                <div>
                                    <?php if (!empty($estadoProduccionAnterior)): ?>
                                        <span class="status-badge <?= invoiceTimelineBadgeClass($estadoProduccionAnterior) ?>">
                                            <?= htmlspecialchars($estadoProduccionAnterior) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="invoice-empty-pill">Sin dato</span>
                                    <?php endif; ?>

                                    <strong>→</strong>

                                    <?php if (!empty($estadoProduccionNuevo)): ?>
                                        <span class="status-badge <?= invoiceTimelineBadgeClass($estadoProduccionNuevo) ?>">
                                            <?= htmlspecialchars($estadoProduccionNuevo) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="invoice-empty-pill">Sin cambio</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="invoice-timeline-meta">
                            <?php if ($montoAbonado > 0): ?>
                                <span>Abono registrado: C$ <?= number_format($montoAbonado, 2) ?></span>
                            <?php endif; ?>

                            <?php if ($saldoNuevo !== null): ?>
                                <span>Saldo después del evento: C$ <?= number_format((float)$saldoNuevo, 2) ?></span>
                            <?php endif; ?>

                            <?php if (!empty($evento["id_historial"])): ?>
                                <span>ID historial: <?= (int)$evento["id_historial"] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>