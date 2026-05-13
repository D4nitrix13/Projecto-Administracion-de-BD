<?php
$estadoPago = $factura["estado_pago"] ?? "Pendiente";
$estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";

function invoiceStatusClass(string $estado): string
{
    return match ($estado) {
        "Pagado", "Entregada" => "success",
        "Parcial", "En producción", "Lista para entregar" => "info",
        "Cancelada" => "danger",
        default => "warning",
    };
}
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
        </div>

        <div class="invoice-info-grid">
            <div class="invoice-info-item">
                <span>Fecha</span>
                <strong>
                    <?= htmlspecialchars(date("d/m/Y H:i", strtotime($factura["fecha"] ?? "now"))) ?>
                </strong>
            </div>

            <div class="invoice-info-item">
                <span>Subtotal</span>
                <strong>C$ <?= number_format((float)($factura["subtotal"] ?? 0), 2) ?></strong>
            </div>

            <div class="invoice-info-item">
                <span>Impuesto</span>
                <strong>C$ <?= number_format((float)($factura["impuesto"] ?? 0), 2) ?></strong>
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
            <p>Seguimiento del abono realizado, saldo pendiente y estado operativo de la factura.</p>
        </div>
    </div>

    <div class="invoice-payment-grid">
        <div class="invoice-payment-item">
            <span>Monto pagado</span>
            <strong>C$ <?= number_format((float)($factura["monto_pagado"] ?? 0), 2) ?></strong>
        </div>

        <div class="invoice-payment-item">
            <span>Saldo pendiente</span>
            <strong>C$ <?= number_format((float)($factura["saldo_pendiente"] ?? 0), 2) ?></strong>
        </div>

        <div class="invoice-payment-item">
            <span>Porcentaje pagado</span>
            <strong><?= number_format((float)($factura["porcentaje_pagado"] ?? 0), 2) ?>%</strong>
        </div>

        <div class="invoice-payment-item">
            <span>Fecha estimada de entrega</span>
            <strong>
                <?php if (!empty($factura["fecha_entrega_estimada"])): ?>
                    <?= htmlspecialchars(date("d/m/Y", strtotime($factura["fecha_entrega_estimada"]))) ?>
                <?php else: ?>
                    —
                <?php endif; ?>
            </strong>
        </div>
    </div>
</section>