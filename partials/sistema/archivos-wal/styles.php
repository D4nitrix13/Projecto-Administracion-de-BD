<style>
    .wal-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .wal-page-heading h1 {
        margin: 0 0 12px;
    }

    .wal-page-heading p {
        margin: 0;
    }

    .wal-eyebrow {
        margin: 0 0 12px;
        color: #111827;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    .wal-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 24px;
    }

    .wal-summary-card,
    .wal-card,
    .wal-filter-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .wal-summary-card {
        display: grid;
        gap: 7px;
    }

    .wal-summary-card span {
        color: #667085;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
    }

    .wal-summary-card strong {
        color: #111827;
        font-size: 1.45rem;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.03em;
    }

    .wal-summary-card small {
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.4;
    }

    .wal-summary-card-blue {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .wal-summary-card-blue span,
    .wal-summary-card-blue small {
        color: #1e40af;
    }

    .wal-summary-card-blue strong {
        color: #1d4ed8;
    }

    .wal-filter-card {
        margin-bottom: 24px;
    }

    .wal-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .wal-card-header h2 {
        margin: 10px 0 8px;
        color: #111827;
        font-size: 1.2rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .wal-card-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .wal-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 26px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .wal-badge-info {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .wal-filter-form {
        display: grid;
        grid-template-columns: minmax(240px, 1.4fr) minmax(180px, 0.8fr) minmax(160px, 0.7fr) minmax(190px, 0.9fr) auto;
        gap: 16px;
        align-items: end;
    }

    .wal-field {
        display: grid;
        gap: 8px;
        min-width: 0;
    }

    .wal-field label {
        color: #334155;
        font-size: 0.86rem;
        font-weight: 900;
    }

    .wal-field input,
    .wal-field select {
        width: 100%;
        min-height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        background: #ffffff;
        color: #111827;
        font-family: Arial, sans-serif;
        font-size: 0.92rem;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .wal-field input:focus,
    .wal-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .wal-primary-button,
    .wal-secondary-button,
    .wal-action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        border-radius: 10px;
        padding: 0 16px;
        font-size: 0.88rem;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }

    .wal-primary-button,
    .wal-action-button-primary {
        border: 0;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .wal-primary-button:hover,
    .wal-action-button-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .wal-secondary-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .wal-secondary-button:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .wal-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        font-size: 0.84rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .wal-list {
        display: grid;
        gap: 14px;
    }

    .wal-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 240px 150px;
        gap: 18px;
        align-items: center;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }

    .wal-item:hover {
        border-color: #dbe3ee;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        transform: translateY(-1px);
    }

    .wal-item-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .wal-file-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 14px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 900;
    }

    .wal-file-info {
        min-width: 0;
    }

    .wal-file-info h3 {
        margin: 0 0 5px;
        color: #111827;
        font-size: 0.96rem;
        font-weight: 900;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .wal-file-info p {
        margin: 0 0 10px;
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .wal-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .wal-chip {
        display: inline-flex;
        align-items: center;
        min-height: 26px;
        border-radius: 999px;
        padding: 0 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .wal-date-box {
        display: grid;
        gap: 6px;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .wal-date-box span {
        color: #667085;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .wal-date-box strong {
        color: #111827;
        font-size: 0.84rem;
        font-weight: 900;
        line-height: 1.45;
    }

    .wal-actions {
        display: grid;
        gap: 8px;
    }

    .wal-action-button {
        width: 100%;
        min-height: 38px;
        padding: 0 12px;
        font-size: 0.82rem;
    }

    .wal-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background: #f8fafc;
        padding: 24px;
        color: #64748b;
        text-align: center;
    }

    .wal-empty strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
        font-weight: 900;
    }

    .wal-empty p {
        margin: 0;
        line-height: 1.5;
    }

    .wal-alert {
        margin-bottom: 18px;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.5;
    }

    .wal-alert-error {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .wal-alert-success {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    @media (max-width: 1280px) {
        .wal-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .wal-primary-button {
            width: 100%;
        }

        .wal-item {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 1100px) {
        .wal-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {

        .wal-page-heading,
        .wal-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .wal-summary-grid,
        .wal-filter-form {
            grid-template-columns: 1fr;
        }

        .wal-secondary-button,
        .wal-primary-button {
            width: 100%;
        }

        .wal-summary-card,
        .wal-card,
        .wal-filter-card {
            padding: 20px;
        }

        .wal-item-main {
            flex-direction: column;
        }

        .wal-file-info h3 {
            white-space: normal;
        }
    }
</style>