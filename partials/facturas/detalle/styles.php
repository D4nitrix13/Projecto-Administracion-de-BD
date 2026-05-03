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

    .invoice-detail-card {
        display: grid;
        gap: 22px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .invoice-summary-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 18px;
        align-items: stretch;
    }

    .invoice-main-panel,
    .invoice-client-panel {
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        border-radius: 16px;
        padding: 18px;
    }

    .invoice-main-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .invoice-label {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .invoice-main-header h2 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .invoice-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .invoice-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 6px 11px;
        border-radius: 999px;
        background: #ffffff;
        color: #374151;
        border: 1px solid #e5e7eb;
        font-size: 0.8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-badge-primary {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .invoice-info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .invoice-info-item {
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        padding: 13px;
    }

    .invoice-info-item span,
    .invoice-client-list span {
        display: block;
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .invoice-info-item strong,
    .invoice-client-list strong {
        display: block;
        color: #111827;
        font-size: 0.95rem;
        font-weight: 900;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .invoice-info-total {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .invoice-info-total strong {
        color: #1d4ed8;
        font-size: 1.08rem;
    }

    .invoice-section-title {
        margin: 0 0 14px;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-client-list {
        display: grid;
        gap: 12px;
    }

    .invoice-products-section {
        background: #ffffff;
        border: none;
        border-radius: 0;
        padding: 0;
    }

    .invoice-section-header {
        margin-bottom: 14px;
    }

    .invoice-section-header h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-section-header p {
        margin: 0;
        color: #6b7280;
        line-height: 1.45;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .table-wrapper table,
    .invoice-products-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 840px;
    }

    .table-wrapper thead,
    .invoice-products-table thead {
        background: #f3f4f6;
    }

    .table-wrapper th,
    .invoice-products-table th {
        padding: 14px 16px;
        color: #667085;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td,
    .invoice-products-table td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .table-wrapper tbody tr,
    .invoice-products-table tbody tr {
        transition: background 0.15s ease;
    }

    .table-wrapper tbody tr:hover,
    .invoice-products-table tbody tr:hover {
        background: #f8fafc;
    }

    .invoice-products-table td strong {
        color: #111827;
        font-weight: 900;
    }

    .invoice-totals-section {
        display: flex;
        justify-content: flex-end;
        margin-top: -2px;
    }

    .invoice-totals-card {
        width: min(100%, 420px);
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        padding: 18px;
    }

    .invoice-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 10px 0;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
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

    .invoice-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 0;
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

    .empty-message {
        margin: 0;
        padding: 30px 16px;
        color: #6b7280;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        line-height: 1.5;
    }

    @media (max-width: 1100px) {

        .invoice-summary-grid,
        .invoice-info-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 760px) {

        .invoice-page-heading,
        .invoice-detail-card {
            padding: 20px;
            border-radius: 16px;
        }

        .invoice-main-panel,
        .invoice-client-panel {
            border-radius: 14px;
        }
    }

    @media (max-width: 700px) {

        .invoice-summary-grid,
        .invoice-info-grid {
            grid-template-columns: 1fr;
        }

        .invoice-main-header,
        .invoice-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .invoice-badges {
            justify-content: flex-start;
        }

        .invoice-actions a,
        .invoice-actions button {
            width: 100%;
            text-align: center;
        }

        .invoice-totals-section {
            justify-content: stretch;
        }

        .table-wrapper table,
        .invoice-products-table {
            min-width: 720px;
        }
    }
</style>