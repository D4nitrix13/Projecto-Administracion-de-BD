<style>
    .invoice-page-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 18px;
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
        padding: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 18px;
    }

    .invoice-create-card form {
        display: grid;
        gap: 14px;
    }

    .invoice-form-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 14px;
        background: #f8fafc;
    }

    .invoice-form-section-header {
        margin-bottom: 10px;
    }

    .invoice-form-section-header h3 {
        margin: 0 0 4px;
        color: #111827;
        font-size: 1.1rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-form-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .invoice-form-grid {
        display: grid;
        gap: 10px;
    }

    .invoice-form-grid.cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .invoice-create-card .label {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .invoice-create-card .input {
        width: 100%;
        min-height: 38px;
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
        gap: 10px;
        margin-bottom: 10px;
    }

    .product-picker {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
        align-items: end;
        margin-bottom: 10px;
    }

    .product-picker-search {
        max-width: 520px;
    }

    .product-picker-btn {
        height: 38px;
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
    }

    .table-wrapper thead {
        background: #f3f4f6;
    }

    .table-wrapper th {
        padding: 8px 10px;
        color: #667085;
        font-size: 0.76rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td {
        padding: 8px 10px;
        color: #111827;
        font-size: 0.85rem;
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
        line-height: 1.3;
        font-size: 0.85rem;
    }

    .invoice-items-table .product-name-cell small {
        display: block;
        margin-top: 2px;
        color: #6b7280;
        font-size: 0.76rem;
        line-height: 1.3;
    }

    .invoice-items-table .cell-remove {
        width: 64px;
        min-width: 64px;
        padding: 0 !important;
        text-align: center;
        vertical-align: middle !important;
    }

    .invoice-items-table .remove-button-wrap {
        min-height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .invoice-items-table .btn-remove-row {
        width: 30px;
        height: 30px;
        min-width: 30px;
        min-height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 0;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 1.05rem;
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
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
        font-size: 0.88rem;
    }

    .invoice-total-row span {
        color: #6b7280;
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-total-row strong {
        color: #111827;
        font-weight: 900;
        white-space: nowrap;
    }

    .invoice-total-row:last-child {
        border-bottom: none;
    }

    .invoice-total-final {
        margin-top: 6px;
        padding-top: 10px;
        border-top: 0;
        color: #111827;
        font-size: 1rem;
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
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-primary-inline,
    .btn-secondary-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 16px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.88rem;
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

    .invoice-payment-divider {
        width: 100%;
        height: 1px;
        background: #e2e8f0;
        margin: 22px 0;
    }

    .invoice-payment-section {
        display: grid;
        gap: 18px;
    }

    .invoice-payment-header h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1rem;
        font-weight: 800;
    }

    .invoice-payment-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    .invoice-payment-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .invoice-payment-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .invoice-payment-card {
        padding: 16px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .invoice-payment-card span {
        display: block;
        margin-bottom: 8px;
        color: #64748b;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .invoice-payment-card strong {
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
    }

    .invoice-payment-status.pending {
        color: #b45309;
    }

    .invoice-payment-status.partial {
        color: #2563eb;
    }

    .invoice-payment-status.paid {
        color: #15803d;
    }

    .invoice-payment-warning {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid #fed7aa;
        background: linear-gradient(135deg,
                #fff7ed 0%,
                #ffffff 100%);
    }

    .invoice-payment-warning-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        background: #ffedd5;
        color: #c2410c;
        font-weight: 900;
    }

    .invoice-payment-warning strong {
        display: block;
        margin-bottom: 5px;
        color: #9a3412;
        font-size: 0.9rem;
    }

    .invoice-payment-warning p {
        margin: 0;
        color: #7c2d12;
        font-size: 0.84rem;
        line-height: 1.45;
    }

    @media (max-width: 900px) {

        .invoice-payment-grid,
        .invoice-payment-summary {
            grid-template-columns: 1fr;
        }
    }

    .invoice-checkout-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
        align-items: start;
        margin-top: 8px;
    }

    .invoice-summary-card,
    .invoice-payment-card-main {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 16px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .invoice-summary-header,
    .invoice-payment-header {
        margin-bottom: 10px;
    }

    .invoice-summary-header span,
    .invoice-payment-header span {
        display: inline-flex;
        margin-bottom: 4px;
        color: #2563eb;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .invoice-summary-header h3,
    .invoice-payment-header h3 {
        margin: 0;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-payment-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .invoice-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 5px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
        font-size: 0.84rem;
    }

    .invoice-total-row span {
        color: #6b7280;
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-total-row strong {
        color: #111827;
        font-weight: 900;
        white-space: nowrap;
    }

    .invoice-total-final {
        margin-top: 0;
        padding-top: 8px;
        border-bottom: none;
        border-top: 1px solid #e5e7eb;
    }

    .invoice-total-final span {
        color: #111827;
        font-size: 0.92rem;
        font-weight: 900;
    }

    .invoice-total-final strong {
        color: #2563eb;
        font-size: 1.15rem;
        font-weight: 900;
    }

    .invoice-payment-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .invoice-payment-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .invoice-payment-mini-card {
        padding: 16px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .invoice-payment-mini-card span {
        display: block;
        margin-bottom: 8px;
        color: #64748b;
        font-size: 0.74rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .invoice-payment-mini-card strong {
        display: block;
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
    }

    .invoice-payment-status.pending {
        color: #b45309;
    }

    .invoice-payment-status.partial {
        color: #2563eb;
    }

    .invoice-payment-status.paid {
        color: #15803d;
    }

    .invoice-payment-warning {
        margin-top: 12px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #fed7aa;
        background: #fff7ed;
    }

    .invoice-payment-warning-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: #ffedd5;
        color: #c2410c;
        font-weight: 900;
        font-size: 0.85rem;
    }

    .invoice-payment-warning strong {
        display: block;
        margin-bottom: 3px;
        color: #9a3412;
        font-size: 0.85rem;
    }

    .invoice-payment-warning p {
        margin: 0;
        color: #7c2d12;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    /* checkout always single-column — no breakpoint needed */

    @media (max-width: 760px) {

        .invoice-summary-card,
        .invoice-payment-card-main {
            padding: 18px;
        }

        .invoice-payment-grid,
        .invoice-payment-summary {
            grid-template-columns: 1fr;
        }

        .invoice-total-final strong {
            font-size: 1.3rem;
        }
    }

    /* =========================================
       PLAZOS SECTION
       ========================================= */

    .invoice-plazos-section {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 16px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .invoice-plazos-header {
        margin-bottom: 10px;
    }

    .invoice-plazos-header span {
        display: inline-flex;
        margin-bottom: 4px;
        color: #7c3aed;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .invoice-plazos-header h3 {
        margin: 0;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-plazos-fields {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 10px;
        align-items: end;
        margin-bottom: 12px;
    }

    .invoice-plazos-fields .form-group {
        margin: 0;
    }

    .invoice-plazos-fields .label {
        display: block;
        margin-bottom: 4px;
        color: #111827;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .invoice-plazos-fields .input {
        width: 100%;
        min-height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 6px 12px;
        background: #ffffff;
        color: #111827;
        font-size: 0.88rem;
        outline: none;
        box-sizing: border-box;
    }

    .invoice-plazos-fields .input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .plazos-numero-input {
        font-size: 1rem !important;
        font-weight: 800 !important;
        text-align: center;
    }

    .invoice-plazos-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .invoice-plazos-mini-card {
        padding: 16px;
        border-radius: 16px;
        background: #f5f3ff;
        border: 1px solid #e9e5ff;
    }

    .invoice-plazos-mini-card span {
        display: block;
        margin-bottom: 8px;
        color: #6b7280;
        font-size: 0.74rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .invoice-plazos-mini-card strong {
        display: block;
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
    }

    .invoice-plazos-step-1 {
        margin-bottom: 18px;
    }

    .invoice-plazos-step-1 .form-group {
        max-width: 300px;
    }

    .plazos-numero-input {
        font-size: 1rem !important;
        font-weight: 800 !important;
        text-align: center;
        min-height: 42px !important;
    }

    .invoice-plazos-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .invoice-plazos-table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-plazos-table thead {
        background: #f5f3ff;
    }

    .invoice-plazos-table th {
        padding: 8px 10px;
        color: #6b7280;
        font-size: 0.76rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .invoice-plazos-table td {
        padding: 8px 10px;
        color: #111827;
        font-size: 0.85rem;
        border-top: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .invoice-plazos-table .input {
        min-width: 0;
        width: 100%;
    }

    .invoice-plazos-table td:nth-child(1) {
        width: 40px;
        text-align: center;
        font-weight: 700;
        color: #7c3aed;
    }

    .invoice-plazos-table td:nth-child(2) {
        width: 80px;
    }

    .invoice-plazos-table td:nth-child(3) {
        width: 140px;
    }

    .invoice-plazos-table td:nth-child(4) {
        width: 140px;
    }

    .invoice-plazos-table td:nth-child(5) {
        width: auto;
    }

    .invoice-plazos-table td:nth-child(6) {
        width: 40px;
        text-align: center;
    }

    .invoice-plazos-total-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
    }

    .invoice-plazos-total-row span {
        color: #6b7280;
        font-weight: 800;
        font-size: 0.88rem;
    }

    .invoice-plazos-total-row strong {
        color: #7c3aed;
        font-weight: 900;
        font-size: 1.05rem;
    }

    .btn-remove-plazo {
        width: 28px;
        height: 28px;
        min-width: 28px;
        min-height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #fca5a5;
        border-radius: 7px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 1rem;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .btn-remove-plazo:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }

    .plazos-validacion-error {
        color: #dc2626;
        font-weight: 700;
        font-size: 0.85rem;
    }

    @media (max-width: 900px) {
        .invoice-plazos-fields {
            grid-template-columns: 1fr;
        }
    }
</style>