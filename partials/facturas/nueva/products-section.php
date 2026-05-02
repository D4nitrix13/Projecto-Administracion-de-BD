<section class="invoice-form-section">
    <div class="invoice-form-section-header invoice-products-header">
        <div>
            <h3>Productos</h3>
            <p>Busque un producto por nombre o código y agréguelo a la factura.</p>
        </div>
    </div>

    <div class="product-picker">
        <div class="form-group product-picker-search">
            <label class="label">Buscar producto</label>

            <input
                type="text"
                id="producto-picker-input"
                class="input"
                list="productos-list"
                placeholder="Ejemplo: camiseta, taza, P001...">

            <datalist id="productos-list">
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= htmlspecialchars($producto["nombre"] . " (" . $producto["codigo"] . ")") ?>"></option>
                <?php endforeach; ?>
            </datalist>
        </div>

        <button
            type="button"
            class="btn-primary-inline product-picker-btn"
            id="btn-add-selected-product">
            Agregar producto
        </button>
    </div>

    <script type="application/json" id="productos-data">
        <?= json_encode(
            $productos,
            JSON_UNESCAPED_UNICODE |
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
        ) ?>
    </script>

    <div class="table-wrapper">
        <table class="table-products invoice-items-table" id="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Cantidad</th>
                    <th>Desc. línea</th>
                    <th>Total línea</th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="items-body">
                <!-- Las filas se agregan con JavaScript -->
            </tbody>
        </table>
    </div>

    <p class="dashboard-muted empty-products-message" id="empty-products-message">
        Todavía no ha agregado productos a esta factura.
    </p>
</section>