<style>
    .reports-page-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .reports-page-heading>div {
        min-width: 0;
    }

    .reports-page-heading .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .reports-page-heading .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .reports-page-heading .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
    }

    .reports-back-btn {
        white-space: nowrap;
    }

    .reports-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .reports-summary-card,
    .summary-card,
    .report-summary-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .reports-summary-card span,
    .summary-card span,
    .report-summary-card span {
        display: block;
        margin-bottom: 8px;
        color: #6b7280;
        font-size: 0.84rem;
        font-weight: 800;
    }

    .reports-summary-card strong,
    .summary-card strong,
    .report-summary-card strong {
        display: block;
        color: #111827;
        font-size: 1.35rem;
        font-weight: 900;
        line-height: 1.2;
    }

    .reports-main-card {
        display: grid;
        gap: 24px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .reports-filter-bar,
    .filters-panel,
    .report-filters,
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: end;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px;
        background: #f8fafc;
    }

    .filter-field,
    .form-group {
        display: grid;
        gap: 7px;
        min-width: 180px;
        flex: 1;
    }

    .label,
    .filter-field label,
    .form-group label,
    .reports-filter-bar label,
    .filters-panel label,
    .report-filters label,
    .filter-form label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .filter-field input,
    .filter-field select,
    .form-group input,
    .form-group select,
    .reports-filter-bar input,
    .reports-filter-bar select,
    .filters-panel input,
    .filters-panel select,
    .report-filters input,
    .report-filters select,
    .filter-form input,
    .filter-form select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
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

    .input::placeholder,
    .filter-field input::placeholder,
    .form-group input::placeholder,
    .reports-filter-bar input::placeholder,
    .filters-panel input::placeholder,
    .report-filters input::placeholder,
    .filter-form input::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .filter-field input:focus,
    .filter-field select:focus,
    .form-group input:focus,
    .form-group select:focus,
    .reports-filter-bar input:focus,
    .reports-filter-bar select:focus,
    .filters-panel input:focus,
    .filters-panel select:focus,
    .report-filters input:focus,
    .report-filters select:focus,
    .filter-form input:focus,
    .filter-form select:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .reports-filter-actions,
    .filter-actions,
    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: end;
        gap: 10px;
        flex-wrap: nowrap;
        align-self: end;
    }

    .reports-charts-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 22px;
        overflow: hidden;
    }

    .reports-chart-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 22px;
        overflow: hidden;
        min-width: 0;
    }

    .reports-chart-card h2,
    .reports-chart-card h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .reports-chart-card p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .reports-chart-box {
        height: 320px;
        margin-top: 16px;
    }

    .reports-chart-box canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .reports-section {
        border-top: 1px solid #e5e7eb;
        padding-top: 24px;
    }

    .reports-section-header,
    .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .reports-section-header h2,
    .reports-section-header h3,
    .section-heading h2,
    .section-heading h3,
    .reports-section h2,
    .reports-section h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .reports-section-header p,
    .section-heading p,
    .reports-section p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .reports-section-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px solid #16a34a;
        border-radius: 10px;
        background: #16a34a;
        color: #ffffff;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-export:hover {
        background: #15803d;
        border-color: #15803d;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
    }

    .btn-export:active {
        background: #166534;
    }

    .btn-export svg {
        flex-shrink: 0;
    }

    .report-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.8rem;
        font-weight: 900;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .report-status-ok {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .report-status-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .table-wrapper,
    .reports-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .table-wrapper table,
    .reports-table,
    .table-reports,
    .table-products,
    .table-clientes {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    .table-wrapper thead,
    .reports-table thead,
    .table-reports thead,
    .table-products thead,
    .table-clientes thead {
        background: #f3f4f6;
    }

    .table-wrapper th,
    .reports-table th,
    .table-reports th,
    .table-products th,
    .table-clientes th {
        padding: 14px 16px;
        color: #667085;
        background: #f3f4f6;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td,
    .reports-table td,
    .table-reports td,
    .table-products td,
    .table-clientes td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-wrapper tbody tr,
    .reports-table tbody tr,
    .table-reports tbody tr,
    .table-products tbody tr,
    .table-clientes tbody tr {
        transition: background 0.15s ease;
    }

    .table-wrapper tbody tr:hover,
    .reports-table tbody tr:hover,
    .table-reports tbody tr:hover,
    .table-products tbody tr:hover,
    .table-clientes tbody tr:hover {
        background: #f8fafc;
    }

    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-primary,
    .btn-secondary,
    .reports-filter-actions button,
    .filter-actions button,
    .reports-filter-actions a,
    .filter-actions a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        height: 42px;
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

    .btn-primary-inline,
    .btn-primary,
    .reports-filter-actions button,
    .filter-actions button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover,
    .btn-primary:hover,
    .reports-filter-actions button:hover,
    .filter-actions button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline,
    .btn-secondary,
    .reports-filter-actions a,
    .filter-actions a {
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover,
    .btn-secondary:hover,
    .reports-filter-actions a:hover,
    .filter-actions a:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .empty-message,
    .empty-box,
    .readonly-box {
        margin: 0;
        padding: 30px 16px;
        color: #6b7280;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        line-height: 1.5;
    }

    @media (max-width: 1200px) {
        .reports-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .reports-charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {

        .reports-page-heading,
        .reports-main-card,
        .reports-chart-card,
        .reports-summary-card,
        .summary-card,
        .report-summary-card {
            padding: 20px;
            border-radius: 16px;
        }

        .reports-page-heading,
        .reports-section-header,
        .section-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .reports-summary {
            grid-template-columns: 1fr;
        }

        .filter-field,
        .form-group {
            min-width: 100%;
        }

        .reports-filter-actions,
        .filter-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .reports-back-btn,
        .reports-section-header a,
        .btn-primary-inline,
        .btn-secondary-inline,
        .btn-primary,
        .btn-secondary,
        .reports-filter-actions button,
        .filter-actions button,
        .reports-filter-actions a,
        .filter-actions a {
            width: 100%;
        }

        .reports-chart-box {
            height: 280px;
        }

        .table-wrapper table,
        .reports-table,
        .table-reports,
        .table-products,
        .table-clientes {
            min-width: 820px;
        }
    }
</style>