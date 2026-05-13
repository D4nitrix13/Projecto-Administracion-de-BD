<style>
    .historial-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .historial-stat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    }

    .historial-stat-card span {
        display: block;
        color: #6b7280;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .historial-stat-card strong {
        display: block;
        color: #111827;
        font-size: 1.45rem;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .historial-stat-card.danger strong {
        color: #991b1b;
    }

    .historial-filter-card {
        padding: 18px;
        margin-bottom: 18px;
    }

    .historial-filters {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        align-items: end;
    }

    .historial-filter-field {
        display: grid;
        gap: 7px;
    }

    .historial-filter-search {
        grid-column: span 2;
    }

    .historial-filter-field span {
        color: #111827;
        font-size: 0.86rem;
        font-weight: 900;
    }

    .historial-filter-field input,
    .historial-filter-field select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 0.92rem;
        outline: none;
    }

    .historial-filter-field input:focus,
    .historial-filter-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .historial-filter-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .historial-card {
        padding: 20px;
    }

    .historial-card-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 16px;
    }

    .historial-card-header h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .historial-card-header p {
        margin: 0;
        color: #6b7280;
        line-height: 1.45;
    }

    .historial-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .historial-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
    }

    .historial-table thead {
        background: #f3f4f6;
    }

    .historial-table th {
        padding: 13px 14px;
        color: #667085;
        font-size: 0.78rem;
        font-weight: 900;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .historial-table td {
        padding: 14px;
        border-top: 1px solid #e5e7eb;
        color: #111827;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .historial-table tbody tr:hover {
        background: #f8fafc;
    }

    .historial-table td strong {
        display: block;
        font-weight: 900;
        color: #111827;
    }

    .historial-table td small {
        display: block;
        margin-top: 3px;
        color: #6b7280;
        font-weight: 700;
    }

    .historial-comment {
        max-width: 360px;
        color: #6b7280 !important;
        line-height: 1.45;
    }

    @media (max-width: 1200px) {
        .historial-filters {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .historial-filter-search {
            grid-column: span 3;
        }
    }

    @media (max-width: 900px) {
        .historial-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .historial-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .historial-filter-search {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 640px) {

        .historial-stats-grid,
        .historial-filters {
            grid-template-columns: 1fr;
        }

        .historial-filter-actions {
            flex-direction: column;
        }

        .historial-filter-actions .btn-primary-inline,
        .historial-filter-actions .btn-secondary-inline {
            width: 100%;
        }
    }
</style>