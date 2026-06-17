<div class="table-wrapper">
    <table class="table-products">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Sección</th>
                <th>Usuario</th>
                <th>Total</th>
                <th>Pago</th>
                <th>Producción</th>
                <th class="col-acciones">Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($facturas)): ?>
                <tr>
                    <td colspan="9" class="dashboard-muted">
                        No se encontraron facturas con los filtros aplicados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($facturas as $factura): ?>
                    <?php
                    $estadoPago = $factura["estado_pago"] ?? "Pendiente";
                    $estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";

                    $clasePago = match ($estadoPago) {
                        "Pagado" => "badge-success",
                        "Parcial" => "badge-info",
                        default => "badge-warning",
                    };

                    $claseProduccion = match ($estadoProduccion) {
                        "Entregada" => "badge-success",
                        "Lista para entregar" => "badge-info",
                        "En producción" => "badge-primary",
                        "Cancelada" => "badge-danger",
                        default => "badge-warning",
                    };
                    ?>

                    <tr>
                        <td><?= (int)$factura["id_factura"] ?></td>

                        <td>
                            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($factura["fecha"]))) ?>
                        </td>

                        <td><?= htmlspecialchars($factura["cliente"]) ?></td>

                        <td><?= htmlspecialchars($factura["seccion"]) ?></td>

                        <td><?= htmlspecialchars($factura["usuario"]) ?></td>

                        <td>
                            C$ <?= number_format((float)$factura["total"], 2) ?>
                        </td>

                        <td>
                            <span class="status-badge <?= $clasePago ?>">
                                <?= htmlspecialchars($estadoPago) ?>
                            </span>
                        </td>

                        <td>
                            <span class="status-badge <?= $claseProduccion ?>">
                                <?= htmlspecialchars($estadoProduccion) ?>
                            </span>
                        </td>

                        <td class="acciones">
                            <a
                                href="detalle_factura.php?id=<?= (int)$factura["id_factura"] ?>"
                                class="btn-accion btn-accion-detalle">
                                Ver detalles
                            </a>

                            <a
                                href="editar_factura.php?id=<?= urlencode((string)$factura["id_factura"]) ?>"
                                class="btn-accion btn-accion-editar">
                                Editar
                            </a>

                            <form method="POST" action="eliminar_factura.php" style="display:inline;"
                                  onsubmit="return confirm('¿Seguro que desea eliminar esta factura?');">
                                <input type="hidden" name="id" value="<?= (int)$factura["id_factura"] ?>">
                                <?= csrfField() ?>
                                <button type="submit" class="btn-accion btn-accion-eliminar">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>