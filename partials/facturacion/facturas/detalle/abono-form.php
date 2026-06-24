<?php
/** @var array $factura */
/** @var array $user */

$estadoPago = $factura["estado_pago"] ?? "Pendiente";
$estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";
$saldoPendiente = (float) ($factura["saldo_pendiente"] ?? 0);
$porcentajePagado = (float) ($factura["porcentaje_pagado"] ?? 0);
$total = (float) ($factura["total"] ?? 0);
$idFactura = (int) ($factura["id_factura"] ?? 0);
$idRol = (int) ($user["id_rol"] ?? 0);

$esTerminal = in_array($estadoProduccion, ["Entregada", "Cancelada"], true);
$tieneSaldo = $saldoPendiente > 0.01;
$puedeAbonar = !$esTerminal && $tieneSaldo && $idRol <= 2;
?>

<?php if ($puedeAbonar): ?>
<section class="invoice-abono-panel">
    <div class="invoice-section-header">
        <div>
            <h3>Registrar abono</h3>
            <p>Saldo pendiente: <strong>C$ <?= number_format($saldoPendiente, 2) ?></strong> (<?= number_format($porcentajePagado, 1) ?>% pagado)</p>
        </div>
    </div>

    <form method="POST" action="registrar_abono.php" class="abono-form" onsubmit="return confirmarAbono(event)">
        <?= csrfField() ?>
        <input type="hidden" name="id_factura" value="<?= $idFactura ?>">

        <div class="abono-fields">
            <div class="form-group">
                <label class="label">Monto del abono C$</label>
                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    max="<?= number_format($saldoPendiente, 2, '.', '') ?>"
                    name="monto_abono"
                    id="monto_abono"
                    class="input"
                    placeholder="0.00"
                    required>
            </div>

            <div class="form-group">
                <label class="label">Comentario (opcional)</label>
                <input
                    type="text"
                    name="comentario"
                    class="input"
                    placeholder="Ej: Primer abono, pago parcial...">
            </div>
        </div>

        <div class="abono-info">
            <?php if ($porcentajePagado < 50): ?>
                <p class="abono-hint">
                    <strong>Falta C$ <?= number_format(max(0, $total * 0.50 - ($total - $saldoPendiente)), 2) ?></strong> para alcanzar el 50% mínimo y activar producción.
                </p>
            <?php elseif ($porcentajePagado < 100): ?>
                <p class="abono-hint abono-hint-success">
                    Producción puede iniciarse. Faltan C$ <?= number_format($saldoPendiente, 2) ?> para pago completo.
                </p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-primary-inline">
            Registrar abono
        </button>
    </form>
</section>
<?php endif; ?>

<script>
function confirmarAbono(e) {
    const monto = parseFloat(document.getElementById("monto_abono").value);
    if (isNaN(monto) || monto <= 0) {
        e.preventDefault();
        alert("Ingrese un monto válido.");
        return false;
    }
    return confirm("¿Registrar abono de C$ " + monto.toFixed(2) + "?");
}
</script>
