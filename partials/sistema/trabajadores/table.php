<h2 class="dashboard-card-title" style="margin-bottom:8px;">
    Listado de trabajadores
</h2>

<?php if (empty($usuarios)): ?>
    <p class="dashboard-muted">
        No se encontraron trabajadores con los filtros aplicados.
    </p>
<?php else: ?>
    <div class="table-wrapper">
        <table class="table-products">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Sección</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario["nombre"]) ?></td>

                        <td><?= htmlspecialchars($usuario["email"]) ?></td>

                        <td><?= htmlspecialchars($usuario["rol"]) ?></td>

                        <td>
                            <?php if ((int)$usuario["id_rol"] === 1): ?>
                                Todas las secciones
                            <?php else: ?>
                                <?= htmlspecialchars($usuario["seccion"] ?? "Kitsune") ?>
                            <?php endif; ?>
                        </td>

                        <td class="acciones">
                            <a
                                href="editar_trabajador.php?id=<?= (int)$usuario["id_usuario"] ?>"
                                class="btn-accion btn-accion-editar">
                                Editar
                            </a>

                            <a
                                href="eliminar_usuario.php?id=<?= (int)$usuario["id_usuario"] ?>"
                                class="btn-accion btn-accion-eliminar"
                                onclick="return confirm('¿Seguro que desea eliminar este trabajador?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>