<?php if ($canManageCategories): ?>
    <div class="category-section-header">
        <div>
            <h2>Agregar nueva categoría</h2>
            <p>Registre una categoría para clasificar productos dentro del inventario.</p>
        </div>
    </div>

    <form action="categorias.php" method="POST" class="category-form">
        <div class="form-group">
            <label class="label" for="nombre">Nombre de la categoría</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                class="input"
                maxlength="80"
                placeholder="Ej. Camisetas personalizadas"
                required>
        </div>

        <button type="submit" class="btn-primary-inline">
            Guardar categoría
        </button>
    </form>
<?php else: ?>
    <div class="readonly-box">
        Su rol solo permite visualizar y filtrar categorías. Para crear, editar o eliminar contacte a un administrador.
    </div>
<?php endif; ?>

<form method="get" class="category-filter">
    <div class="form-group">
        <label for="q" class="label">Buscar categoría</label>
        <input
            type="text"
            id="q"
            name="q"
            class="input"
            placeholder="Nombre de la categoría..."
            value="<?= htmlspecialchars($busqueda) ?>">
    </div>

    <div class="filter-actions">
        <button type="submit" class="btn-primary-inline">
            Aplicar filtros
        </button>

        <a href="categorias.php" class="btn-secondary-inline">
            Limpiar
        </a>
    </div>
</form>