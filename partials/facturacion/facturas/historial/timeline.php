<?php
function historialBadgeClass(?string $estado): string
{
    return match ($estado) {
        "Pagado", "Entregada" => "badge-success",
        "Parcial", "Lista para entregar" => "badge-info",
        "En producción" => "badge-primary",
        "Cancelada" => "badge-danger",
        default => "badge-warning",
    };
}
?>

<section class="dashboard-card historial-card">
    <div class="historial-card-header">
        <div>
            <h2>Bitácora general</h2>
            <p>Registro cronológico de acciones realizadas sobre las facturas.</p>
        </div>
    </div>

    <?php if (empty($historialEstados)): ?>
        <p class="empty-message">
            No se encontraron eventos con los filtros aplicados.
        </p>
    <?php else: ?>
        <div class="historial-table-wrapper">
            <table class="historial-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Factura</th>
                        <th>Evento</th>
                        <th>Pago</th>
                        <th>Producción</th>
                        <th>Abono</th>
                        <th>Saldo</th>
                        <th>Comentario</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($historialEstados as $evento): ?>
                        <?php
                        $estadoPago = $evento["estado_pago_nuevo"] ?? "Pendiente";
                        $estadoProduccion = $evento["estado_produccion_nuevo"] ?? "Pendiente";
                        ?>

                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars(date("d/m/Y", strtotime($evento["fecha_evento"]))) ?>
                                </strong>
                                <small>
                                    <?= htmlspecialchars(date("H:i", strtotime($evento["fecha_evento"]))) ?>
                                </small>
                            </td>

                            <td>
                                #<?= (int)$evento["id_factura"] ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($evento["tipo_evento"]) ?></strong>
                            </td>

                            <td>
                                <?php if (!empty($estadoPago)): ?>
                                    <span class="status-badge <?= historialBadgeClass($estadoPago) ?>">
                                        <?= htmlspecialchars($estadoPago) ?>
                                    </span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($estadoProduccion)): ?>
                                    <span class="status-badge <?= historialBadgeClass($estadoProduccion) ?>">
                                        <?= htmlspecialchars($estadoProduccion) ?>
                                    </span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ((float)($evento["monto_abonado"] ?? 0) > 0): ?>
                                    C$ <?= number_format((float)$evento["monto_abonado"], 2) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($evento["saldo_nuevo"] !== null): ?>
                                    C$ <?= number_format((float)$evento["saldo_nuevo"], 2) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td class="historial-comment">
                                <?= htmlspecialchars($evento["comentario"] ?? "Cambio registrado.") ?>
                            </td>

                            <td>
                                <a
                                    href="detalle_factura.php?id=<?= (int)$evento["id_factura"] ?>"
                                    class="btn-accion btn-accion-ver">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>