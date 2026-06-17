<section class="edit-category-card">
    <div class="edit-category-header">
        <div>
            <p class="dashboard-eyebrow">Inventario</p>
            <h1>Editar categoría</h1>
            <span>Modifique el nombre de la categoría seleccionada.</span>
        </div>

        <a href="categorias.php" class="btn-secondary-inline">
            Volver a categorías
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="editar_categoria.php" method="POST" class="edit-category-form">
        <?= csrfField() ?>
        <input
            type="hidden"
            name="id_categoria"
            value="<?= (int)$categoria["id_categoria"] ?>">

        <div class="form-group">
            <label class="label" for="nombre">Nombre de la categoría</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                class="input"
                maxlength="80"
                required
                value="<?= htmlspecialchars($categoria["nombre"]) ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary-inline">
                Guardar cambios
            </button>
        </div>
    </form>
</section>