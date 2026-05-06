<style>
    .config-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .config-page-heading h1 {
        margin: 0 0 12px;
    }

    .config-page-heading p {
        margin: 0;
    }

    .config-eyebrow {
        margin: 0 0 12px;
        color: #111827;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    .config-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.75fr);
        gap: 28px;
        align-items: start;
    }

    .config-card,
    .config-side-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .config-card-header {
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .config-card-header h2,
    .config-side-card h2 {
        margin: 10px 0 8px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .config-card-header p,
    .config-side-card p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .config-badge {
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

    .config-badge-info {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .config-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .config-form {
        display: grid;
        gap: 20px;
    }

    .config-field {
        display: grid;
        gap: 8px;
    }

    .config-field label {
        color: #334155;
        font-size: 0.88rem;
        font-weight: 900;
    }

    .config-money-field {
        display: grid;
        grid-template-columns: 54px minmax(0, 1fr);
        overflow: hidden;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
    }

    .config-money-field span {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        color: #334155;
        font-size: 0.9rem;
        font-weight: 900;
        border-right: 1px solid #cbd5e1;
    }

    .config-money-field input {
        width: 100%;
        min-height: 44px;
        border: 0;
        padding: 10px 12px;
        color: #111827;
        font-family: Arial, sans-serif;
        font-size: 0.92rem;
        outline: none;
    }

    .config-field small {
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .config-note {
        padding: 16px;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        background: #eff6ff;
    }

    .config-note strong {
        display: block;
        margin-bottom: 6px;
        color: #1d4ed8;
        font-size: 0.9rem;
        font-weight: 900;
    }

    .config-note p {
        margin: 0;
        color: #334155;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .config-actions {
        display: flex;
        justify-content: flex-end;
    }

    .config-primary-button,
    .config-secondary-button,
    .config-danger-button {
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

    .config-primary-button {
        border: 0;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .config-primary-button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .config-secondary-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .config-secondary-button:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .config-danger-button {
        width: 100%;
        margin-top: 16px;
        border: 1px solid #fecaca;
        background: #fee2e2;
        color: #991b1b;
    }

    .config-danger-button:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }

    .config-side {
        display: grid;
        gap: 20px;
    }

    .config-info-list {
        display: grid;
        gap: 12px;
        margin-top: 18px;
    }

    .config-info-list div {
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .config-info-list span {
        display: block;
        margin-bottom: 6px;
        color: #667085;
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .config-info-list strong {
        display: block;
        color: #111827;
        font-size: 0.9rem;
        font-weight: 900;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .config-danger-card {
        border-color: #fecaca;
        background: #fffafa;
    }

    .config-alert {
        margin-bottom: 18px;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.5;
    }

    .config-alert-error {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .config-alert-success {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    @media (max-width: 1100px) {
        .config-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .config-page-heading {
            flex-direction: column;
            align-items: flex-start;
        }

        .config-secondary-button,
        .config-primary-button {
            width: 100%;
        }

        .config-card,
        .config-side-card {
            padding: 20px;
        }

        .config-actions {
            justify-content: stretch;
        }
    }
</style>