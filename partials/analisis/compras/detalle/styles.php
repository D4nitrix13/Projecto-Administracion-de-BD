<style>
    .purchase-page-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .purchase-page-heading>div {
        min-width: 0;
    }

    .purchase-page-heading .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .purchase-page-heading .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .purchase-page-heading .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
    }

    .purchase-detail-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .purchase-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .purchase-summary-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 16px;
        background: #f8fafc;
    }

    .purchase-summary-card span {
        display: block;
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .purchase-summary-card strong {
        display: block;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .purchase-summary-card p {
        margin: 8px 0 0;
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.45;
    }

    .purchase-summary-card b {
        color: #111827;
        font-weight: 900;
    }

    .purchase-summary-total {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .purchase-summary-total strong {
        color: #1d4ed8;
        font-size: 1.25rem;
    }

    .purchase-products-section {
        display: grid;
        gap: 14px;
    }

    .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }

    .section-heading h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .section-heading p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .purchase-products-table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
        background: #ffffff;
    }

    .purchase-products-table thead {
        background: #f3f4f6;
    }

    .purchase-products-table th {
        padding: 14px 16px;
        color: #667085;
        background: #f3f4f6;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .purchase-products-table td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
        white-space: nowrap;
    }

    .purchase-products-table tbody tr {
        transition: background 0.15s ease;
    }

    .purchase-products-table tbody tr:hover {
        background: #f8fafc;
    }

    .purchase-products-table td strong {
        color: #111827;
        font-weight: 900;
    }

    .purchase-products-table th:nth-child(3),
    .purchase-products-table td:nth-child(3) {
        text-align: center;
        width: 120px;
    }

    .purchase-products-table th:nth-child(4),
    .purchase-products-table td:nth-child(4),
    .purchase-products-table th:nth-child(5),
    .purchase-products-table td:nth-child(5) {
        text-align: right;
        width: 170px;
    }

    .purchase-products-table tfoot th {
        padding: 14px 16px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #111827;
        font-size: 0.92rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .purchase-products-table tfoot th:first-child {
        text-align: right;
    }

    .purchase-products-table tfoot th:last-child {
        color: #1d4ed8;
        text-align: right;
        font-size: 1rem;
    }

    .text-center {
        text-align: center;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        font-weight: 700;
        line-height: 1.45;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .btn-secondary-inline,
    .btn-primary-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.94rem;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        box-sizing: border-box;
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
        .purchase-summary-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 760px) {

        .purchase-page-heading,
        .purchase-detail-card {
            padding: 20px;
            border-radius: 16px;
        }

        .purchase-page-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .purchase-summary-grid {
            grid-template-columns: 1fr;
        }

        .section-heading {
            flex-direction: column;
        }

        .btn-secondary-inline,
        .btn-primary-inline {
            width: 100%;
        }

        .purchase-products-table {
            min-width: 760px;
        }
    }
</style>