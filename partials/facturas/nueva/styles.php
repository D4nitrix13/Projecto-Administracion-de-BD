<style>
    .invoice-page-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .invoice-page-heading p:first-child,
    .invoice-page-heading .dashboard-eyebrow,
    .invoice-page-heading .invoice-label {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .invoice-page-heading h1 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .invoice-page-heading h1+p,
    .invoice-page-heading .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .invoice-create-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .invoice-create-card form {
        display: grid;
        gap: 22px;
    }

    .invoice-form-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #f8fafc;
    }

    .invoice-form-section-header {
        margin-bottom: 18px;
    }

    .invoice-form-section-header h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-form-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
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
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .invoice-create-card .input {
        width: 100%;
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
        box-sizing: border-box;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease,
            background 0.15s ease;
    }

    .invoice-create-card .input::placeholder {
        color: #8b95a5;
    }

    .invoice-create-card .input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }

    .client-picker {
        display: grid;
        gap: 8px;
    }

    .client-picker-actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-start;
    }

    .client-picker-help {
        display: block;
        margin-top: 8px;
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .invoice-products-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 18px;
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

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .table-wrapper table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    .table-wrapper thead {
        background: #f3f4f6;
    }

    .table-wrapper th {
        padding: 14px 16px;
        color: #667085;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .table-wrapper tbody tr:hover {
        background: #f8fafc;
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
        width: 64px;
        text-align: center;
    }

    .invoice-items-table .input {
        min-width: 0;
    }

    .invoice-items-table .product-name-cell strong {
        display: block;
        color: #111827;
        font-weight: 900;
        line-height: 1.35;
    }

    .invoice-items-table .product-name-cell small {
        display: block;
        margin-top: 3px;
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.35;
    }

    .invoice-items-table .cell-remove {
        width: 64px;
        min-width: 64px;
        padding: 0 !important;
        text-align: center;
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
        font-weight: 900;
        line-height: 1;
        cursor: pointer;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            transform 0.15s ease;
    }

    .invoice-items-table .btn-remove-row:hover {
        background: #fecaca;
        border-color: #f87171;
        transform: translateY(-1px);
    }

    .empty-products-message {
        margin-top: 12px;
        text-align: center;
        padding: 30px 16px;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        background: #ffffff;
        color: #6b7280;
        line-height: 1.5;
    }

    .invoice-totals-card {
        margin-left: auto;
        width: min(100%, 420px);
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        padding: 18px;
    }

    .invoice-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
        font-size: 0.95rem;
    }

    .invoice-total-row span {
        color: #6b7280;
        font-weight: 700;
    }

    .invoice-total-row strong {
        color: #111827;
        font-weight: 900;
    }

    .invoice-total-row:last-child {
        border-bottom: none;
    }

    .invoice-total-final {
        margin-top: 8px;
        padding-top: 16px;
        border-top: 0;
        color: #111827;
        font-size: 1.15rem;
    }

    .invoice-total-final span {
        color: #111827;
        font-weight: 900;
    }

    .invoice-total-final strong {
        color: #1d4ed8;
        font-size: 1.3rem;
        font-weight: 900;
    }

    .invoice-create-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-primary-inline,
    .btn-secondary-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.94rem;
        text-decoration: none;
        cursor: pointer;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .btn-primary-inline {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #374151;
    }

    .btn-secondary-inline:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    @media (max-width: 1100px) {
        .invoice-form-grid.cols-2 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {

        .invoice-page-heading,
        .invoice-create-card {
            padding: 20px;
            border-radius: 16px;
        }

        .invoice-form-section {
            padding: 18px;
            border-radius: 14px;
        }

        .invoice-products-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .product-picker {
            grid-template-columns: 1fr;
        }

        .product-picker-search {
            max-width: none;
        }

        .product-picker-btn {
            width: 100%;
        }

        .invoice-create-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .invoice-create-actions a,
        .invoice-create-actions button {
            width: 100%;
        }

        .invoice-totals-card {
            width: 100%;
        }
    }

    @media (max-width: 640px) {
        .table-wrapper table {
            min-width: 860px;
        }
    }
</style>