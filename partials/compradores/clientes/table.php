<div class="table-wrapper">
    <table class="table-products">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Identificación</th>
                <th>Tipo</th>
                <th class="col-acciones">Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($clientes)): ?>
                <tr>
                    <td colspan="8" class="dashboard-muted">
                        No se encontraron clientes con los filtros aplicados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= (int)$cliente["id_cliente"] ?></td>

                        <td><?= htmlspecialchars($cliente["nombres"]) ?></td>

                        <td><?= htmlspecialchars($cliente["apellidos"]) ?></td>

                        <td><?= htmlspecialchars($cliente["telefono"] ?? "—") ?></td>

                        <td><?= htmlspecialchars($cliente["direccion"] ?? "—") ?></td>

                        <td><?= htmlspecialchars($cliente["identificacion"] ?? "—") ?></td>

                        <td><?= htmlspecialchars($cliente["tipo_cliente"]) ?></td>

                        <td class="acciones">
                            <a
                                href="detalle_cliente.php?id=<?= (int)$cliente["id_cliente"] ?>"
                                class="btn-accion btn-accion-detalle">
                                Ver detalles
                            </a>

                            <a
                                href="editar_cliente.php?id=<?= (int)$cliente["id_cliente"] ?>"
                                class="btn-accion btn-accion-editar">
                                Editar
                            </a>

                            <a
                                href="eliminar_cliente.php?id=<?= (int)$cliente["id_cliente"] ?>"
                                class="btn-accion btn-accion-eliminar"
                                onclick="return confirm('¿Seguro que desea eliminar este cliente?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>