<style>
    .invoice-detail-card {
        display: grid;
        gap: 24px;
        padding: 28px;
    }

    .invoice-summary-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 22px;
    }

    .invoice-main-panel,
    .invoice-client-panel {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 18px;
        padding: 22px;
    }

    .invoice-main-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .invoice-label {
        margin: 0 0 6px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .invoice-main-header h2 {
        margin: 0;
        color: #111827;
        font-size: 2rem;
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
        padding: 7px 12px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .invoice-badge-primary {
        background: #e0e7ff;
        color: #1d4ed8;
    }

    .invoice-info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .invoice-info-item {
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #eef2f7;
        padding: 14px;
    }

    .invoice-info-item span,
    .invoice-client-list span {
        display: block;
        color: #6b7280;
        font-size: 0.82rem;
        margin-bottom: 6px;
    }

    .invoice-info-item strong,
    .invoice-client-list strong {
        color: #111827;
        font-size: 0.95rem;
    }

    .invoice-info-total {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .invoice-info-total strong {
        color: #1d4ed8;
        font-size: 1.1rem;
    }

    .invoice-section-title {
        margin: 0 0 16px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #111827;
    }

    .invoice-client-list {
        display: grid;
        gap: 14px;
    }

    .invoice-products-section {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 22px;
        background: #ffffff;
    }

    .invoice-section-header {
        margin-bottom: 18px;
    }

    .invoice-section-header h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.15rem;
    }

    .invoice-section-header p {
        margin: 0;
        color: #6b7280;
    }

    .invoice-products-table td strong {
        color: #111827;
    }

    .invoice-totals-section {
        display: flex;
        justify-content: flex-end;
    }

    .invoice-totals-card {
        width: min(100%, 420px);
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 20px;
    }

    .invoice-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 10px 0;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
    }

    .invoice-total-row:last-child {
        border-bottom: none;
    }

    .invoice-total-final {
        margin-top: 8px;
        padding-top: 16px;
        font-size: 1.15rem;
        color: #111827;
    }

    .invoice-total-final strong {
        color: #1d4ed8;
        font-size: 1.3rem;
    }

    .invoice-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    @media (max-width: 1100px) {

        .invoice-summary-grid,
        .invoice-info-grid {
            grid-template-columns: 1fr 1fr;
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

        .invoice-actions a {
            text-align: center;
        }
    }
</style>