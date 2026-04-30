<style>
    .client-detail-card {
        background: #ffffff;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        border: 1px solid #eef2f7;
    }

    .client-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .client-detail-header h1 {
        margin: 4px 0 6px;
        font-size: 1.8rem;
        color: #111827;
    }

    .client-detail-header span {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .client-detail-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-primary-inline,
    .btn-secondary-inline {
        height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary-inline {
        background: #111827;
        color: #ffffff;
    }

    .btn-primary-inline:hover {
        background: #1f2937;
    }

    .btn-secondary-inline {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary-inline:hover {
        background: #e5e7eb;
    }

    .client-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .client-summary-card {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 18px;
        padding: 18px;
    }

    .client-summary-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .client-summary-card h2 {
        margin: 0;
        color: #111827;
        font-size: 1.5rem;
    }

    .client-info-grid {
        display: grid;
        grid-template-columns: 0.8fr 1.2fr;
        gap: 18px;
    }

    .client-info-box {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 18px;
        padding: 20px;
    }

    .client-info-box h3 {
        margin: 0 0 16px;
        color: #111827;
    }

    .client-info-row {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .client-info-row:last-child {
        border-bottom: none;
    }

    .client-info-row span {
        color: #6b7280;
    }

    .client-info-row strong {
        color: #111827;
        text-align: right;
    }

    .client-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .client-section-header h3 {
        margin: 0;
    }

    .client-section-header a {
        color: #6b7280;
        font-weight: 800;
        text-decoration: none;
    }

    .client-section-header a:hover {
        color: #111827;
    }

    .client-table {
        width: 100%;
        border-collapse: collapse;
    }

    .client-table th {
        background: #f3f4f6;
        color: #6b7280;
        text-align: left;
        padding: 12px;
        font-size: 0.86rem;
    }

    .client-table td {
        padding: 13px 12px;
        border-bottom: 1px solid #eef2f7;
        color: #111827;
    }

    .client-table tr {
        cursor: pointer;
    }

    .client-table tbody tr:hover {
        background: #f8fafc;
    }

    .empty-client-text {
        color: #6b7280;
        background: #f8fafc;
        padding: 18px;
        border-radius: 14px;
        margin: 0;
    }

    @media (max-width: 900px) {
        .client-detail-header {
            flex-direction: column;
        }

        .client-summary-grid,
        .client-info-grid {
            grid-template-columns: 1fr;
        }

        .client-detail-actions {
            width: 100%;
        }

        .btn-primary-inline,
        .btn-secondary-inline {
            flex: 1;
        }
    }
</style>