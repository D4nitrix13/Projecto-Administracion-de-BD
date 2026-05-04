<style>
    .logs-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .logs-page-heading h1 {
        margin: 0 0 12px;
    }

    .logs-page-heading p {
        margin: 0;
    }

    .logs-eyebrow {
        margin: 0 0 12px;
        color: #111827;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    .logs-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 24px;
    }

    .logs-summary-card,
    .logs-card,
    .logs-filter-card,
    .logs-viewer-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .logs-summary-card {
        display: grid;
        gap: 7px;
    }

    .logs-summary-card span {
        color: #667085;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
    }

    .logs-summary-card strong {
        color: #111827;
        font-size: 1.45rem;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.03em;
    }

    .logs-summary-card small {
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.4;
    }

    .logs-summary-card-blue {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .logs-summary-card-blue span,
    .logs-summary-card-blue small {
        color: #1e40af;
    }

    .logs-summary-card-blue strong {
        color: #1d4ed8;
    }

    .logs-filter-card {
        margin-bottom: 24px;
    }

    .logs-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(360px, 0.85fr);
        gap: 28px;
        align-items: start;
    }

    .logs-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .logs-card-header h2 {
        margin: 10px 0 8px;
        color: #111827;
        font-size: 1.2rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .logs-card-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .logs-badge {
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

    .logs-badge-info {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .logs-filter-form {
        display: grid;
        grid-template-columns: minmax(160px, 0.8fr) minmax(180px, 1fr) minmax(160px, 0.8fr) minmax(220px, 1.2fr) auto;
        gap: 16px;
        align-items: end;
    }

    .logs-field {
        display: grid;
        gap: 8px;
        min-width: 0;
    }

    .logs-field label {
        color: #334155;
        font-size: 0.86rem;
        font-weight: 900;
    }

    .logs-field input,
    .logs-field select {
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

    .logs-field input:focus,
    .logs-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .logs-primary-button,
    .logs-secondary-button,
    .logs-action-button {
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

    .logs-primary-button {
        border: 0;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .logs-primary-button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .logs-secondary-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .logs-secondary-button:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .logs-count {
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

    .logs-table-wrapper {
        overflow-x: auto;
    }

    .logs-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 860px;
    }

    .logs-table thead {
        background: #f3f4f6;
    }

    .logs-table th {
        text-align: left;
        color: #667085;
        background: #f3f4f6;
        padding: 14px 16px;
        font-size: 0.84rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .logs-table td {
        padding: 14px 16px;
        border-top: 1px solid #e5e7eb;
        color: #111827;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .logs-table td strong {
        display: block;
        margin-bottom: 4px;
        color: #111827;
        font-weight: 900;
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .logs-table td small {
        display: block;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.35;
    }

    .logs-chip {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        border-radius: 999px;
        padding: 0 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .logs-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .logs-action-button {
        min-height: 36px;
        padding: 0 12px;
        font-size: 0.82rem;
    }

    .logs-action-button-primary {
        border: 0;
        background: #2563eb;
        color: #ffffff;
    }

    .logs-action-button-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .logs-action-button-dark {
        border: 0;
        background: #0f172a;
        color: #ffffff;
    }

    .logs-action-button-dark:hover {
        background: #020617;
        transform: translateY(-1px);
    }

    .logs-selected-info {
        display: grid;
        gap: 12px;
        margin-bottom: 16px;
    }

    .logs-selected-info div {
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .logs-selected-info span {
        display: block;
        margin-bottom: 6px;
        color: #667085;
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .logs-selected-info strong {
        display: block;
        color: #111827;
        font-size: 0.9rem;
        font-weight: 900;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .logs-preview {
        max-height: 520px;
        overflow: auto;
        margin: 0;
        padding: 16px;
        border-radius: 14px;
        background: #0f172a;
        color: #e5e7eb;
        font-size: 0.82rem;
        line-height: 1.55;
        white-space: pre-wrap;
    }

    .logs-preview code {
        font-family: Consolas, "Liberation Mono", monospace;
    }

    .logs-viewer-download {
        width: 100%;
        margin-top: 16px;
    }

    .logs-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background: #f8fafc;
        padding: 24px;
        color: #64748b;
        text-align: center;
    }

    .logs-empty strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
        font-weight: 900;
    }

    .logs-empty p {
        margin: 0;
        line-height: 1.5;
    }

    .logs-alert {
        margin-bottom: 18px;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.5;
    }

    .logs-alert-error {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .logs-alert-success {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    @media (max-width: 1280px) {
        .logs-layout {
            grid-template-columns: 1fr;
        }

        .logs-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .logs-primary-button {
            width: 100%;
        }
    }

    @media (max-width: 1100px) {
        .logs-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {

        .logs-page-heading,
        .logs-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .logs-summary-grid,
        .logs-filter-form {
            grid-template-columns: 1fr;
        }

        .logs-secondary-button,
        .logs-primary-button {
            width: 100%;
        }

        .logs-summary-card,
        .logs-card,
        .logs-filter-card,
        .logs-viewer-card {
            padding: 20px;
        }
    }
</style>