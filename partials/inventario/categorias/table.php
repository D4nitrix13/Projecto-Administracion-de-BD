<?php
$baseUrl = "categorias.php";
$filtrosActuales = $filtrosGET ?? [];
?>

<div class="category-section-header">
    <div>
        <h2>Listado de categorías</h2>
        <p>Estas categorías están disponibles para asignarse a los productos.</p>
    </div>

    <span class="category-count">
        <?= count($categorias) ?> categoría(s)
    </span>
</div>

<form method="get" class="categorias-filtros-bar">
    <?php if (!empty($busqueda)): ?>
        <input type="hidden" name="q" value="<?= htmlspecialchars($busqueda) ?>">
    <?php endif; ?>

    <div class="filtro-item">
        <label for="orden-cat" class="label">Ordenar por</label>
        <select id="orden-cat" name="orden" class="input" onchange="this.form.submit()">
            <option value="nombre" <?= $filtroOrdenCat === 'nombre' ? 'selected' : '' ?>>Nombre (A-Z)</option>
            <option value="mas_vendidos_mes" <?= $filtroOrdenCat === 'mas_vendidos_mes' ? 'selected' : '' ?>>Más vendidos (mes)</option>
            <option value="menos_vendidos_mes" <?= $filtroOrdenCat === 'menos_vendidos_mes' ? 'selected' : '' ?>>Menos vendidos (mes)</option>
            <option value="mas_vendidos_semana" <?= $filtroOrdenCat === 'mas_vendidos_semana' ? 'selected' : '' ?>>Más vendidos (semana)</option>
            <option value="menos_vendidos_semana" <?= $filtroOrdenCat === 'menos_vendidos_semana' ? 'selected' : '' ?>>Menos vendidos (semana)</option>
            <option value="mas_vendidos_anio" <?= $filtroOrdenCat === 'mas_vendidos_anio' ? 'selected' : '' ?>>Más vendidos (año)</option>
            <option value="menos_vendidos_anio" <?= $filtroOrdenCat === 'menos_vendidos_anio' ? 'selected' : '' ?>>Menos vendidos (año)</option>
            <option value="total_ventas" <?= $filtroOrdenCat === 'total_ventas' ? 'selected' : '' ?>>Total ventas</option>
            <option value="mas_productos" <?= $filtroOrdenCat === 'mas_productos' ? 'selected' : '' ?>>Más productos</option>
            <option value="menos_productos" <?= $filtroOrdenCat === 'menos_productos' ? 'selected' : '' ?>>Menos productos</option>
            <option value="stock_total" <?= $filtroOrdenCat === 'stock_total' ? 'selected' : '' ?>>Stock total</option>
        </select>
    </div>
</form>

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
                    <th>Productos</th>
                    <th>Stock total</th>
                    <th>Vendido</th>
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
                        <td><?= (int)($cat["cantidad_productos"] ?? 0) ?></td>
                        <td><?= (int)($cat["stock_total"] ?? 0) ?></td>
                        <td>$<?= number_format((float)($cat["total_vendido"] ?? 0), 2) ?></td>

                        <?php if ($canManageCategories): ?>
                            <td>
                                <div class="category-actions">
                                    <a
                                        href="editar_categoria.php?id=<?= (int)$cat["id_categoria"] ?>"
                                        class="btn-action btn-edit">
                                        Editar
                                    </a>

                                    <form method="POST" action="eliminar_categoria.php" style="display:inline;"
                                          onsubmit="return confirm('¿Seguro que desea eliminar esta categoría?');">
                                        <input type="hidden" name="id" value="<?= (int)$cat["id_categoria"] ?>">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn-action btn-delete">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . "/../../shared/pagination.php"; ?>