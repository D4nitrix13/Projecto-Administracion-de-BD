<style>
    .facturas-hero {
        margin-bottom: 22px;
    }

    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        font-weight: 700;
        line-height: 1.45;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .factura-edit-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .factura-edit-block {
        margin: 0;
    }

    .factura-edit-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .factura-edit-field {
        position: relative;
        display: grid;
        gap: 7px;
    }

    .factura-edit-field span {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .factura-edit-field input,
    .factura-edit-field select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
    }

    .factura-edit-field input:focus,
    .factura-edit-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .factura-edit-section {
        margin-top: 24px;
        padding-top: 22px;
        border-top: 1px solid #e5e7eb;
    }

    .factura-edit-section h2 {
        margin: 0 0 14px;
        color: #111827;
        font-size: 1.15rem;
        font-weight: 900;
    }

    .factura-edit-section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }

    .factura-edit-section-title h2 {
        margin: 0 0 4px;
    }

    .factura-edit-section-title p {
        margin: 0;
        color: #6b7280;
    }

    .factura-edit-client-type {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .factura-edit-client-type label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 14px;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #f8fafc;
        color: #111827;
        font-weight: 800;
    }

    .factura-edit-client-type input {
        accent-color: #2563eb;
    }

    .factura-edit-filter-list,
    .producto-lista {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 50;
        display: grid;
        gap: 6px;
        max-height: 230px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 12px;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.14);
        padding: 8px;
    }

    .factura-edit-filter-list:empty,
    .producto-lista:empty {
        display: none;
    }

    .factura-edit-filter-option {
        border: none;
        background: transparent;
        padding: 10px 12px;
        border-radius: 10px;
        text-align: left;
        cursor: pointer;
    }

    .factura-edit-filter-option:hover {
        background: #eff6ff;
    }

    .factura-edit-filter-option strong {
        display: block;
        color: #111827;
        font-size: 0.9rem;
    }

    .factura-edit-filter-option small {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 0.78rem;
    }

    .factura-edit-filter-empty {
        padding: 12px;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .factura-edit-products {
        display: grid;
        gap: 14px;
    }

    .factura-edit-product-row {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 110px 130px 140px 92px;
        gap: 12px;
        align-items: stretch;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .factura-edit-product-main {
        display: grid;
        gap: 10px;
        min-width: 0;
    }

    .factura-edit-product-search input {
        min-height: 44px;
    }

    .factura-edit-product-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .factura-edit-product-meta div {
        min-height: 48px;
        display: grid;
        align-content: center;
        gap: 3px;
        padding: 8px 10px;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #ffffff;
    }

    .factura-edit-product-meta span {
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
    }

    .factura-edit-product-meta strong {
        color: #1d4ed8;
        font-size: 0.9rem;
    }

    .factura-edit-qty-field,
    .factura-edit-discount-field {
        align-content: start;
    }

    .factura-edit-line-total {
        min-height: 86px;
        display: grid;
        align-content: center;
        gap: 4px;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
    }

    .factura-edit-line-total span {
        color: #6b7280;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
    }

    .factura-edit-line-total strong {
        color: #111827;
        font-size: 0.95rem;
    }

    .factura-edit-line-warning {
        display: block;
        margin-top: 4px;
        color: #b91c1c;
        font-size: 0.72rem;
        font-weight: 800;
    }

    .factura-edit-remove-btn {
        min-height: 86px;
        align-self: stretch;
    }

    .factura-edit-row-error {
        border-color: #fecaca;
        background: #fff1f2;
    }

    .factura-edit-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 22px;
    }

    .factura-edit-summary div {
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .factura-edit-summary span {
        display: block;
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 900;
        text-transform: uppercase;
    }

    .factura-edit-summary strong {
        display: block;
        margin-top: 6px;
        color: #111827;
    }

    .factura-edit-summary .total {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .factura-edit-summary .total strong {
        color: #1d4ed8;
    }

    .factura-edit-alert-inline {
        margin-top: 16px;
    }

    .factura-edit-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 22px;
    }

    .factura-edit-btn {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.92rem;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .factura-edit-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .factura-edit-btn-primary:hover {
        background: #1d4ed8;
    }

    .factura-edit-btn-secondary {
        background: #ffffff;
        color: #374151;
        border-color: #cbd5e1;
    }

    .factura-edit-btn-secondary:hover {
        background: #f3f4f6;
    }

    .factura-edit-btn-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .factura-edit-btn-danger:hover {
        background: #fecaca;
    }

    @media (max-width: 1280px) {
        .factura-edit-product-row {
            grid-template-columns: minmax(280px, 1fr) 100px 120px 130px 86px;
        }
    }

    @media (max-width: 1100px) {

        .factura-edit-grid,
        .factura-edit-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .factura-edit-product-row {
            grid-template-columns: 1fr 1fr;
        }

        .factura-edit-product-main {
            grid-column: 1 / -1;
        }

        .factura-edit-line-total,
        .factura-edit-remove-btn {
            min-height: 64px;
        }
    }

    @media (max-width: 760px) {

        .factura-edit-card,
        .dashboard-card {
            padding: 20px;
        }

        .factura-edit-grid,
        .factura-edit-summary,
        .factura-edit-product-row,
        .factura-edit-product-meta {
            grid-template-columns: 1fr;
        }

        .factura-edit-actions,
        .factura-edit-section-title {
            flex-direction: column;
            align-items: stretch;
        }

        .factura-edit-btn {
            width: 100%;
        }
    }
</style>