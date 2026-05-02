<style>
    .backup-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .backup-back-btn {
        white-space: nowrap;
    }

    .backup-page-card {
        padding: 28px;
    }

    .backup-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 28px;
    }

    .backup-panel {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 22px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .backup-panel-danger {
        border-color: #fecaca;
        background: #fffafa;
    }

    .backup-panel-header h2 {
        margin: 10px 0 6px;
        color: #111827;
        font-size: 1.15rem;
    }

    .backup-panel-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
        line-height: 1.45;
    }

    .backup-panel-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .backup-badge-safe {
        background: #dcfce7;
        color: #166534;
    }

    .backup-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .backup-form {
        display: grid;
        gap: 16px;
    }

    .backup-help {
        margin-top: 6px;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .backup-warning {
        border: 1px solid #fecaca;
        background: #fff1f2;
        border-radius: 14px;
        padding: 14px;
        color: #7f1d1d;
    }

    .backup-warning strong {
        display: block;
        margin-bottom: 4px;
        color: #991b1b;
    }

    .backup-warning p {
        margin: 0;
        line-height: 1.45;
        font-size: 0.9rem;
    }

    .backup-btn {
        height: 44px;
        border-radius: 10px;
        border: none;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
    }

    .backup-btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .backup-btn-primary:hover {
        background: #1d4ed8;
    }

    .backup-btn-danger {
        background: #dc2626;
        color: #ffffff;
    }

    .backup-btn-danger:hover {
        background: #b91c1c;
    }

    .backup-history {
        border-top: 1px solid #e5e7eb;
        padding-top: 24px;
    }

    .backup-history-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .backup-history-header h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.15rem;
    }

    .backup-count {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 7px 12px;
        background: #f3f4f6;
        color: #374151;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .backup-empty {
        border: 1px dashed #d1d5db;
        border-radius: 14px;
        background: #f9fafb;
        padding: 20px;
        color: #6b7280;
        text-align: center;
    }

    .backup-empty strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
    }

    .backup-empty p {
        margin: 0;
    }

    @media (max-width: 1100px) {
        .backup-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {

        .backup-hero,
        .backup-history-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .backup-back-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>