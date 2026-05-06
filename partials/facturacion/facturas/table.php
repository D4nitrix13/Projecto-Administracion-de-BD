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
                <th class="col-acciones">Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($facturas)): ?>
                <tr>
                    <td colspan="7" class="dashboard-muted">
                        No se encontraron facturas con los filtros aplicados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($facturas as $factura): ?>
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

                        <td class="acciones">
                            <a
                                href="detalle_factura.php?id=<?= (int)$factura["id_factura"] ?>"
                                class="btn-accion btn-accion-detalle">
                                Ver detalles
                            </a>
                            <a
                                href="editar_factura.php?id=<?= urlencode((string)$factura["id_factura"]) ?>"
                                class="btn-accion btn-accion-detalle">
                                Editar
                            </a>
                            <a
                                href="eliminar_factura.php?id=<?= (int)$factura["id_factura"] ?>"
                                class="btn-accion btn-accion-eliminar"
                                onclick="return confirm('¿Seguro que desea eliminar esta factura?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>