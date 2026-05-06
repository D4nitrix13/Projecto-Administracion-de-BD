<style>
    .restore-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .restore-page-heading h1 {
        margin: 0 0 12px;
    }

    .restore-page-heading p {
        margin: 0;
    }

    .restore-eyebrow {
        margin: 0 0 12px;
        color: #111827;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    .restore-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.75fr);
        gap: 28px;
        align-items: start;
    }

    .restore-card,
    .restore-side-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .restore-card-header {
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .restore-card-header h2,
    .restore-side-card h2 {
        margin: 10px 0 8px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .restore-card-header p,
    .restore-side-card p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .restore-badge {
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

    .restore-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .restore-badge-info {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .restore-form {
        display: grid;
        gap: 20px;
    }

    .restore-field {
        display: grid;
        gap: 8px;
    }

    .restore-field label {
        color: #334155;
        font-size: 0.88rem;
        font-weight: 900;
    }

    .restore-field input,
    .restore-field select {
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

    .restore-field input:focus,
    .restore-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .restore-field small {
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .restore-warning {
        border: 1px solid #fecaca;
        border-radius: 14px;
        padding: 16px;
        background: #fff1f2;
    }

    .restore-warning strong {
        display: block;
        margin-bottom: 6px;
        color: #991b1b;
        font-size: 0.9rem;
        font-weight: 900;
    }

    .restore-warning p {
        margin: 0;
        color: #7f1d1d;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .restore-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .restore-secondary-button,
    .restore-danger-button,
    .restore-primary-link {
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
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }

    .restore-secondary-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        white-space: nowrap;
    }

    .restore-secondary-button:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .restore-danger-button {
        border: 0;
        background: #dc2626;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(220, 38, 38, 0.18);
    }

    .restore-danger-button:hover {
        background: #b91c1c;
        transform: translateY(-1px);
    }

    .restore-primary-link {
        margin-top: 14px;
        border: 0;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .restore-primary-link:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .restore-side {
        display: grid;
        gap: 20px;
    }

    .restore-info-list {
        display: grid;
        gap: 12px;
        margin-top: 18px;
    }

    .restore-info-list div {
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .restore-info-list span {
        display: block;
        margin-bottom: 6px;
        color: #667085;
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .restore-info-list strong {
        display: block;
        color: #111827;
        font-size: 0.92rem;
        font-weight: 900;
        line-height: 1.45;
    }

    .restore-side-danger {
        border-color: #fecaca;
        background: #fffafa;
    }

    .restore-check-list {
        display: grid;
        gap: 10px;
        margin: 14px 0 0;
        padding-left: 18px;
        color: #7f1d1d;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .restore-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background: #f8fafc;
        padding: 24px;
        color: #64748b;
        text-align: center;
    }

    .restore-empty strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
        font-weight: 900;
    }

    .restore-empty p {
        margin: 0;
        line-height: 1.5;
    }

    .restore-alert {
        margin-bottom: 18px;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.5;
    }

    .restore-alert-error {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .restore-alert-success {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    @media (max-width: 1100px) {
        .restore-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .restore-page-heading {
            flex-direction: column;
            align-items: flex-start;
        }

        .restore-secondary-button,
        .restore-danger-button,
        .restore-primary-link {
            width: 100%;
        }

        .restore-actions {
            justify-content: stretch;
        }

        .restore-card,
        .restore-side-card {
            padding: 20px;
        }
    }

    .restore-backup-list {
        display: grid;
        gap: 10px;
        max-height: 360px;
        overflow-y: auto;
        padding: 6px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .restore-backup-option {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        cursor: pointer;
        transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    }

    .restore-backup-option:hover {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .restore-backup-option input {
        width: 18px;
        height: 18px;
        margin-top: 3px;
        accent-color: #dc2626;
        flex: 0 0 auto;
    }

    .restore-backup-option:has(input:checked) {
        border-color: #dc2626;
        background: #fff1f2;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.10);
    }

    .restore-backup-content {
        display: grid;
        gap: 4px;
        min-width: 0;
    }

    .restore-backup-content strong {
        color: #111827;
        font-size: 0.92rem;
        font-weight: 900;
    }

    .restore-backup-content small {
        color: #334155;
        font-size: 0.84rem;
        line-height: 1.4;
        word-break: break-word;
    }

    .restore-backup-content em {
        color: #64748b;
        font-size: 0.8rem;
        font-style: normal;
        line-height: 1.4;
    }
</style>