<section class="invoice-form-section">
    <div class="invoice-form-section-header invoice-products-header">
        <div>
            <h3>Productos</h3>
            <p>Busque un producto por nombre o código y agréguelo a la factura.</p>
        </div>
    </div>

    <div class="product-picker">
        <div class="form-group product-picker-search" style="position: relative;">
            <label class="label">Buscar producto</label>

            <input
                type="text"
                id="producto-picker-input"
                class="input"
                placeholder="Ejemplo: camiseta, taza, P001..."
                autocomplete="off">

            <div id="producto-picker-dropdown" class="producto-picker-dropdown" style="display: none;"></div>
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

<style>
    .producto-picker-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 100;
        max-height: 320px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
    }

    .producto-picker-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.1s ease;
    }

    .producto-picker-option:last-child {
        border-bottom: none;
    }

    .producto-picker-option:hover,
    .producto-picker-option.active {
        background: #f0f7ff;
    }

    .producto-picker-option-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .producto-picker-option-name {
        font-weight: 800;
        color: #111827;
        font-size: 0.92rem;
    }

    .producto-picker-option-code {
        color: #6b7280;
        font-size: 0.82rem;
    }

    .producto-picker-option-meta {
        display: flex;
        gap: 12px;
        font-size: 0.82rem;
        color: #6b7280;
    }

    .producto-picker-option-meta span {
        white-space: nowrap;
    }

    .producto-picker-option-stock-ok {
        color: #16a34a;
    }

    .producto-picker-option-stock-low {
        color: #d97706;
    }

    .producto-picker-option-stock-empty {
        color: #dc2626;
    }

    .producto-picker-empty {
        padding: 16px;
        text-align: center;
        color: #6b7280;
        font-size: 0.9rem;
    }
</style>