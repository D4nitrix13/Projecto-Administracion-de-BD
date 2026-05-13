<?php
$estadoPago = $factura["estado_pago"] ?? "Pendiente";
$estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";

$resumenHistorial = $resumenHistorial ?? [];

function invoiceStatusClass(string $estado): string
{
    return match ($estado) {
        "Pagado", "Entregada", "Completada" => "success",
        "Parcial", "En producción", "Lista para entregar", "En seguimiento" => "info",
        "Cancelada" => "danger",
        default => "warning",
    };
}

$ultimoEvento = $resumenHistorial["ultimoEvento"] ?? null;
?>

<div class="invoice-summary-grid">

    <article class="invoice-main-panel">
        <div class="invoice-main-header">
            <div>
                <p class="invoice-label">Factura</p>
                <h2>#<?= (int)$factura["id_factura"] ?></h2>
            </div>

            <div class="invoice-badges">
                <span class="invoice-badge invoice-badge-primary">
                    <?= htmlspecialchars($factura["seccion"] ?? "Sin sección") ?>
                </span>

                <span class="invoice-badge">
                    <?= htmlspecialchars($factura["usuario"] ?? "Sin usuario") ?>
                </span>
            </div>
        </div>

        <div class="invoice-status-strip">
            <span class="status-badge status-<?= invoiceStatusClass($estadoPago) ?>">
                Pago: <?= htmlspecialchars($estadoPago) ?>
            </span>

            <span class="status-badge status-<?= invoiceStatusClass($estadoProduccion) ?>">
                Producción: <?= htmlspecialchars($estadoProduccion) ?>
            </span>

            <span class="status-badge status-<?= invoiceStatusClass((string)($resumenHistorial["estadoOperativo"] ?? "En seguimiento")) ?>">
                Operación: <?= htmlspecialchars($resumenHistorial["estadoOperativo"] ?? "En seguimiento") ?>
            </span>
        </div>

        <div class="invoice-info-grid">
            <div class="invoice-info-item">
                <span>Fecha de emisión</span>
                <strong>
                    <?= htmlspecialchars(date("d/m/Y H:i", strtotime($factura["fecha"] ?? "now"))) ?>
                </strong>
            </div>

            <div class="invoice-info-item">
                <span>Días desde creación</span>
                <strong>
                    <?= isset($resumenHistorial["diasDesdeCreacion"]) ? (int)$resumenHistorial["diasDesdeCreacion"] . " días" : "—" ?>
                </strong>
            </div>

            <div class="invoice-info-item">
                <span>Fecha estimada de entrega</span>
                <strong>
                    <?php if (!empty($factura["fecha_entrega_estimada"])): ?>
                        <?= htmlspecialchars(date("d/m/Y", strtotime($factura["fecha_entrega_estimada"]))) ?>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </strong>
            </div>

            <div class="invoice-info-item invoice-info-total">
                <span>Total</span>
                <strong>C$ <?= number_format((float)($factura["total"] ?? 0), 2) ?></strong>
            </div>
        </div>
    </article>

    <article class="invoice-client-panel">
        <p class="invoice-section-title">Cliente</p>

        <div class="invoice-client-list">
            <div>
                <span>Nombre</span>
                <strong><?= htmlspecialchars($factura["cliente"] ?? "—") ?></strong>
            </div>

            <div>
                <span>Teléfono</span>
                <strong><?= htmlspecialchars($factura["telefono"] ?? "—") ?></strong>
            </div>

            <div>
                <span>Dirección</span>
                <strong><?= htmlspecialchars($factura["direccion"] ?? "—") ?></strong>
            </div>
        </div>
    </article>

</div>

<section class="invoice-payment-panel">
    <div class="invoice-section-header">
        <div>
            <h3>Pago y producción</h3>
            <p>Resumen financiero y operativo generado a partir del estado actual y la bitácora de eventos.</p>
        </div>
    </div>

    <div class="invoice-payment-grid">
        <div class="invoice-payment-item">
            <span>Monto pagado</span>
            <strong>C$ <?= number_format((float)($resumenHistorial["montoPagado"] ?? 0), 2) ?></strong>
        </div>

        <div class="invoice-payment-item">
            <span>Saldo pendiente</span>
            <strong>C$ <?= number_format((float)($resumenHistorial["saldoPendiente"] ?? 0), 2) ?></strong>
        </div>

        <div class="invoice-payment-item">
            <span>Porcentaje pagado</span>
            <strong><?= number_format((float)($resumenHistorial["porcentajePagado"] ?? 0), 2) ?>%</strong>
        </div>

        <div class="invoice-payment-item">
            <span>Abonado según historial</span>
            <strong>C$ <?= number_format((float)($resumenHistorial["totalAbonadoHistorial"] ?? 0), 2) ?></strong>
        </div>
    </div>

    <div class="invoice-progress-wrapper">
        <div class="invoice-progress-header">
            <span>Avance de pago</span>
            <strong><?= number_format((float)($resumenHistorial["porcentajePagado"] ?? 0), 2) ?>%</strong>
        </div>

        <div class="invoice-progress-bar">
            <div style="width: <?= min(100, max(0, (float)($resumenHistorial["porcentajePagado"] ?? 0))) ?>%;"></div>
        </div>
    </div>
</section>

<section class="invoice-history-overview">
    <article class="invoice-history-kpi">
        <span>Total eventos</span>
        <strong><?= number_format((int)($resumenHistorial["totalEventos"] ?? 0)) ?></strong>
    </article>

    <article class="invoice-history-kpi">
        <span>Eventos de pago</span>
        <strong><?= number_format((int)($resumenHistorial["eventosPago"] ?? 0)) ?></strong>
    </article>

    <article class="invoice-history-kpi">
        <span>Eventos operativos</span>
        <strong><?= number_format((int)($resumenHistorial["eventosProduccion"] ?? 0)) ?></strong>
    </article>

    <article class="invoice-history-kpi <?= ((int)($resumenHistorial["eventosCancelacion"] ?? 0) > 0) ? "danger" : "" ?>">
        <span>Cancelaciones</span>
        <strong><?= number_format((int)($resumenHistorial["eventosCancelacion"] ?? 0)) ?></strong>
    </article>
</section>

<?php if ($ultimoEvento): ?>
    <section class="invoice-last-event">
        <div>
            <span>Última actividad registrada</span>
            <strong><?= htmlspecialchars($ultimoEvento["tipo_evento"] ?? "Evento registrado") ?></strong>
            <p><?= htmlspecialchars($ultimoEvento["comentario"] ?? "Cambio registrado.") ?></p>
        </div>

        <time>
            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($ultimoEvento["fecha_evento"] ?? "now"))) ?>
        </time>
    </section>
<?php endif; ?>