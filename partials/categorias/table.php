<div class="category-section-header">
    <div>
        <h2>Listado de categorías</h2>
        <p>Estas categorías están disponibles para asignarse a los productos.</p>
    </div>

    <span class="category-count">
        <?= count($categorias) ?> categoría(s)
    </span>
</div>

<?php if (empty($categorias)): ?>
    <div class="empty-box">
        No se encontraron categorías con los filtros aplicados.
    </div>
<?php else: ?>
    <div class="category-table-wrapper">
        <table class="category-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <?php if ($canManageCategories): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td>#<?= (int)$cat["id_categoria"] ?></td>
                        <td><?= htmlspecialchars($cat["nombre"]) ?></td>

                        <?php if ($canManageCategories): ?>
                            <td>
                                <div class="category-actions">
                                    <a
                                        href="editar_categoria.php?id=<?= (int)$cat["id_categoria"] ?>"
                                        class="btn-action btn-edit">
                                        Editar
                                    </a>

                                    <a
                                        href="eliminar_categoria.php?id=<?= (int)$cat["id_categoria"] ?>"
                                        class="btn-action btn-delete"
                                        onclick="return confirm('¿Seguro que desea eliminar esta categoría?');">
                                        Eliminar
                                    </a>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>