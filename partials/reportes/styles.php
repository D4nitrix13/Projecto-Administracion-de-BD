<style>
    .reports-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .reports-back-btn {
        white-space: nowrap;
    }

    .reports-summary {
        margin-bottom: 24px;
    }

    .reports-main-card {
        display: grid;
        gap: 24px;
        padding: 28px;
    }

    .reports-filter-bar {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 180px 180px auto;
        gap: 16px;
        align-items: end;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px;
        background: #ffffff;
    }

    .reports-filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .reports-charts-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.9fr);
        gap: 22px;
    }

    .reports-chart-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 22px;
    }

    .reports-chart-box {
        height: 320px;
        margin-top: 16px;
    }

    .reports-section {
        border-top: 1px solid #e5e7eb;
        padding-top: 24px;
    }

    .reports-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .reports-section-header h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.15rem;
    }

    .report-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .report-status-ok {
        background: #dcfce7;
        color: #166534;
    }

    .report-status-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 1100px) {

        .reports-filter-bar,
        .reports-charts-grid {
            grid-template-columns: 1fr;
        }

        .reports-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 760px) {

        .reports-hero,
        .reports-section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .reports-back-btn,
        .reports-section-header a,
        .reports-filter-actions,
        .reports-filter-actions a,
        .reports-filter-actions button {
            width: 100%;
        }

        .reports-filter-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>