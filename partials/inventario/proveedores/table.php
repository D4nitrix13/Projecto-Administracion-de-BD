<h2 class="dashboard-card-title" style="margin-bottom:8px;">
    Listado de proveedores
</h2>

<p class="dashboard-muted" style="margin-bottom:12px;">
    Estos proveedores están disponibles para asociarse a compras e inventario.
</p>

<?php if (empty($proveedores)): ?>
    <p class="dashboard-muted">
        No se encontraron proveedores con los filtros aplicados.
    </p>
<?php else: ?>
    <div class="table-wrapper">
        <table class="table-products">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($proveedores as $proveedor): ?>
                    <tr>
                        <td><?= (int)$proveedor["id_proveedor"] ?></td>

                        <td><?= htmlspecialchars($proveedor["nombre"]) ?></td>

                        <td><?= htmlspecialchars($proveedor["telefono"] ?? "—") ?></td>

                        <td><?= htmlspecialchars($proveedor["email"] ?? "—") ?></td>

                        <td><?= htmlspecialchars($proveedor["direccion"] ?? "—") ?></td>

                        <td class="acciones">
                            <?php if ($puedeGestionar): ?>
                                <a
                                    href="editar_proveedor.php?id=<?= (int)$proveedor["id_proveedor"] ?>"
                                    class="btn-accion btn-accion-editar">
                                    Editar
                                </a>

                                <a
                                    href="eliminar_proveedor.php?id=<?= (int)$proveedor["id_proveedor"] ?>"
                                    class="btn-accion btn-accion-eliminar"
                                    onclick="return confirm('¿Seguro que desea eliminar este proveedor?');">
                                    Eliminar
                                </a>
                            <?php else: ?>
                                <span class="dashboard-muted">Solo lectura</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>