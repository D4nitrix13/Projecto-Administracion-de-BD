<style>
    .invoice-create-card {
        padding: 28px;
    }

    .invoice-create-card form {
        display: grid;
        gap: 24px;
    }

    .invoice-form-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #ffffff;
    }

    .invoice-form-section-header {
        margin-bottom: 18px;
    }

    .invoice-form-section-header h3 {
        margin: 0 0 6px;
        font-size: 1.12rem;
        color: #111827;
    }

    .invoice-form-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
    }

    .invoice-form-grid {
        display: grid;
        gap: 18px;
    }

    .invoice-form-grid.cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .invoice-create-card .label {
        display: block;
        margin-bottom: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #111827;
    }

    .invoice-create-card .input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 11px 13px;
        font-size: 0.95rem;
        color: #111827;
        background: #ffffff;
        box-sizing: border-box;
    }

    .invoice-create-card .input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .invoice-products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .product-picker {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: end;
        margin-bottom: 18px;
    }

    .product-picker-search {
        max-width: 520px;
    }

    .product-picker-btn {
        height: 42px;
        white-space: nowrap;
    }

    .empty-products-message {
        margin-top: 12px;
        text-align: center;
        padding: 18px;
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        background: #f9fafb;
    }

    .invoice-items-table {
        table-layout: fixed;
    }

    .invoice-items-table th,
    .invoice-items-table td {
        vertical-align: middle !important;
    }

    .invoice-items-table th:nth-child(1),
    .invoice-items-table td:nth-child(1) {
        width: 26%;
    }

    .invoice-items-table th:nth-child(2),
    .invoice-items-table td:nth-child(2),
    .invoice-items-table th:nth-child(3),
    .invoice-items-table td:nth-child(3),
    .invoice-items-table th:nth-child(4),
    .invoice-items-table td:nth-child(4),
    .invoice-items-table th:nth-child(5),
    .invoice-items-table td:nth-child(5),
    .invoice-items-table th:nth-child(6),
    .invoice-items-table td:nth-child(6) {
        width: 14%;
    }

    .invoice-items-table th:nth-child(7),
    .invoice-items-table td:nth-child(7) {
        width: 56px;
    }

    .invoice-items-table .input {
        min-width: 0;
    }

    .invoice-items-table .product-name-cell strong {
        display: block;
        color: #111827;
        font-weight: 700;
    }

    .invoice-items-table .product-name-cell small {
        display: block;
        color: #6b7280;
        margin-top: 3px;
        font-size: 0.82rem;
    }

    .invoice-items-table .cell-remove {
        width: 56px;
        min-width: 56px;
        padding: 0 !important;
        vertical-align: middle !important;
    }

    .invoice-items-table .remove-button-wrap {
        min-height: 78px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .invoice-items-table .btn-remove-row {
        width: 34px;
        height: 34px;
        min-width: 34px;
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 0;
        border: 1px solid #fca5a5;
        border-radius: 10px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 1.2rem;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
    }

    .invoice-items-table .btn-remove-row:hover {
        background: #fecaca;
    }

    .invoice-totals-card {
        margin-left: auto;
        width: min(100%, 420px);
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
        padding: 20px;
    }

    .invoice-total-row {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
    }

    .invoice-total-row:last-child {
        border-bottom: none;
    }

    .invoice-total-final {
        margin-top: 8px;
        padding-top: 16px;
        border-top: 2px solid #111827;
        font-size: 1.12rem;
        color: #111827;
    }

    .invoice-total-final strong {
        color: #1d4ed8;
        font-size: 1.25rem;
    }

    .invoice-create-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    @media (max-width: 900px) {
        .invoice-form-grid.cols-2 {
            grid-template-columns: 1fr;
        }

        .invoice-products-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .product-picker {
            grid-template-columns: 1fr;
        }

        .product-picker-btn {
            width: 100%;
        }

        .invoice-create-actions {
            flex-direction: column;
        }

        .invoice-create-actions a,
        .invoice-create-actions button {
            width: 100%;
        }
    }
</style>