<style>
    .backup-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .backup-page-heading h1 {
        margin: 0 0 12px;
    }

    .backup-page-heading p {
        margin: 0;
    }

    .backup-eyebrow {
        margin: 0 0 12px;
        color: #111827;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    .backup-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #334155;
        font-size: 0.9rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }

    .backup-back-btn:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .backup-page-card {
        padding: 0;
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    .backup-page-card-compact {
        min-height: auto !important;
        height: auto !important;
    }

    .backup-summary-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 24px;
    }

    .backup-summary-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 22px;
        display: grid;
        gap: 7px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .backup-summary-card span {
        color: #667085;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
    }

    .backup-summary-card strong {
        color: #111827;
        font-size: 1.45rem;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.03em;
    }

    .backup-summary-card small {
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.4;
    }

    .backup-summary-card-blue {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .backup-summary-card-blue span,
    .backup-summary-card-blue small {
        color: #1e40af;
    }

    .backup-summary-card-blue strong {
        color: #1d4ed8;
    }

    .backup-grid {
        display: grid;
        gap: 22px;
        margin-bottom: 24px;
    }

    .backup-grid-manual-compact {
        display: block;
        margin-bottom: 28px;
    }

    .backup-panel {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .backup-panel-header {
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .backup-panel-header h2 {
        margin: 10px 0 8px;
        color: #111827;
        font-size: 1.2rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .backup-panel-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .backup-panel-badge {
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

    .backup-badge-safe {
        background: #dcfce7;
        color: #166534;
    }

    .backup-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .backup-panel-danger {
        border-color: #fecaca;
        background: #fffafa;
    }

    .backup-form {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(260px, 1fr);
        gap: 18px;
        align-items: end;
    }

    .backup-form .form-group {
        display: grid;
        gap: 8px;
        min-width: 0;
    }

    .backup-form label,
    .backup-filter-field label {
        color: #334155;
        font-size: 0.86rem;
        font-weight: 900;
    }

    .backup-form input,
    .backup-form select,
    .backup-form textarea,
    .backup-filter-field input,
    .backup-filter-field select,
    .backup-filter-field textarea,
    .input {
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
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .backup-form input:focus,
    .backup-form select:focus,
    .backup-form textarea:focus,
    .backup-filter-field input:focus,
    .backup-filter-field select:focus,
    .backup-filter-field textarea:focus,
    .input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .backup-form {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(260px, 1fr);
        column-gap: 18px;
        row-gap: 14px;
        align-items: start;
    }

    .backup-form .form-group {
        display: grid;
        grid-template-rows: auto 44px auto;
        gap: 8px;
        min-width: 0;
        align-items: start;
    }

    .backup-form .form-group label,
    .backup-form label,
    .backup-filter-field label {
        min-height: 18px;
        color: #334155;
        font-size: 0.86rem;
        font-weight: 900;
    }

    .backup-form input,
    .backup-form select,
    .backup-form textarea,
    .backup-filter-field input,
    .backup-filter-field select,
    .backup-filter-field textarea,
    .input {
        width: 100%;
        min-height: 44px;
        height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        background: #ffffff;
        color: #111827;
        font-family: Arial, sans-serif;
        font-size: 0.92rem;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .backup-form textarea,
    .backup-filter-field textarea {
        min-height: 92px;
        height: auto;
        resize: vertical;
    }

    .backup-form input:focus,
    .backup-form select:focus,
    .backup-form textarea:focus,
    .backup-filter-field input:focus,
    .backup-filter-field select:focus,
    .backup-filter-field textarea:focus,
    .input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .backup-form .backup-btn {
        grid-column: 1 / -1;
        margin-top: 0;
    }

    .backup-help {
        margin: 0;
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .backup-form .backup-help {
        margin-top: 2px;
    }

    .backup-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 44px;
        border: 0;
        border-radius: 10px;
        padding: 0 16px;
        font-size: 0.88rem;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
    }

    .backup-btn:hover {
        transform: translateY(-1px);
    }

    .backup-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .backup-btn-primary:hover {
        background: #1d4ed8;
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.28);
    }

    .backup-btn-danger {
        background: #dc2626;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(220, 38, 38, 0.18);
    }

    .backup-btn-danger:hover {
        background: #b91c1c;
    }

    .backup-grid-manual-compact .backup-form .backup-btn {
        grid-column: 1 / -1;
        margin-top: 2px;
    }

    .backup-warning {
        grid-column: 1 / -1;
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
        font-weight: 900;
    }

    .backup-warning p {
        margin: 0;
        line-height: 1.45;
        font-size: 0.9rem;
    }

    .backup-history {
        padding-top: 26px;
        border-top: 1px solid #e5e7eb;
    }

    .backup-history-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .backup-history-header h2 {
        margin: 0 0 8px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .backup-history-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .backup-count {
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

    .backup-filter-panel {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .backup-filter-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .backup-filter-header h3 {
        margin: 8px 0 6px;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
    }

    .backup-filter-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.45;
    }

    .backup-filter-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 26px;
        padding: 0 10px;
        border-radius: 999px;
        background: #eef2ff;
        color: #3730a3;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .backup-filter-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 14px;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.86rem;
        font-weight: 900;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }

    .backup-filter-clear:hover {
        background: #dbeafe;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .backup-filter-grid {
        display: grid;
        grid-template-columns: minmax(280px, 1.5fr) repeat(3, minmax(180px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .backup-filter-field {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .backup-files-grid {
        display: grid;
        gap: 16px;
    }

    .backup-file-card {
        display: grid;
        grid-template-columns: minmax(320px, 1.45fr) minmax(260px, 0.9fr) minmax(180px, 0.55fr);
        gap: 22px;
        align-items: center;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }

    .backup-file-card:hover {
        border-color: #dbe3ee;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.09);
        transform: translateY(-1px);
    }

    .backup-file-card-pending {
        border-color: #fbbf24;
        background: #fffdf7;
    }

    .backup-file-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .backup-file-icon {
        width: 54px;
        height: 54px;
        min-width: 54px;
        border-radius: 16px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 900;
    }

    .backup-file-info {
        min-width: 0;
    }

    .backup-file-topline {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .backup-file-title {
        margin: 0;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -0.01em;
    }

    .backup-status {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        border-radius: 999px;
        padding: 0 9px;
        font-size: 0.73rem;
        font-weight: 900;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .backup-status-ok {
        background: #dcfce7;
        color: #166534;
    }

    .backup-status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .backup-file-subtitle {
        margin: 0 0 6px;
        color: #475569;
        font-size: 0.94rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .backup-file-original-name {
        margin: 0 0 10px;
        color: #667085;
        font-size: 0.84rem;
        line-height: 1.45;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .backup-file-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .backup-chip {
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

    .backup-file-side {
        display: grid;
        gap: 12px;
        min-width: 0;
    }

    .backup-file-date-block {
        display: grid;
        gap: 5px;
    }

    .backup-file-date-label {
        color: #667085;
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .backup-file-date {
        color: #111827;
        font-size: 0.92rem;
        font-weight: 800;
        line-height: 1.45;
    }

    .backup-delete-box {
        border: 1px solid #fde68a;
        background: #fffbeb;
        border-radius: 12px;
        padding: 12px;
    }

    .backup-delete-box strong {
        display: block;
        margin-bottom: 4px;
        color: #92400e;
        font-size: 0.86rem;
        font-weight: 900;
    }

    .backup-delete-box p {
        margin: 0;
        color: #78350f;
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .backup-delete-box-neutral {
        border-color: #dbeafe;
        background: #eff6ff;
    }

    .backup-delete-box-neutral strong {
        color: #1d4ed8;
    }

    .backup-delete-box-neutral p {
        color: #1e40af;
    }

    .backup-file-actions {
        display: grid;
        gap: 10px;
        min-width: 180px;
    }

    .backup-inline-form {
        margin: 0;
    }

    .backup-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 42px;
        border-radius: 10px;
        border: 0;
        padding: 0 14px;
        font-size: 0.88rem;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.15s ease, background 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .backup-action-btn:hover {
        transform: translateY(-1px);
    }

    .backup-action-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .backup-action-btn-primary:hover {
        background: #1d4ed8;
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.28);
    }

    .backup-action-btn-danger {
        background: #fff1f2;
        color: #b91c1c;
        border: 1px solid #fecdd3;
    }

    .backup-action-btn-danger:hover {
        background: #ffe4e6;
        border-color: #fda4af;
    }

    .backup-action-btn-dark {
        background: #0f172a;
        color: #ffffff;
    }

    .backup-action-btn-dark:hover {
        background: #020617;
    }

    .backup-empty,
    .backup-no-results {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background: #f8fafc;
        padding: 24px;
        color: #64748b;
        text-align: center;
        margin-bottom: 16px;
    }

    .backup-empty strong,
    .backup-no-results strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
        font-weight: 900;
    }

    .backup-empty p,
    .backup-no-results p {
        margin: 0;
        line-height: 1.5;
    }

    .alert {
        margin-bottom: 18px;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.5;
    }

    .alert-danger {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .alert-success {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    @media (max-width: 1280px) {
        .backup-file-card {
            grid-template-columns: 1fr;
        }

        .backup-file-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .backup-file-actions>* {
            min-width: 0;
        }
    }

    @media (max-width: 1200px) {
        .backup-summary-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .backup-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .backup-form {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {

        .backup-page-heading,
        .backup-history-header,
        .backup-filter-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .backup-back-btn,
        .backup-filter-clear {
            width: 100%;
        }

        .backup-summary-row,
        .backup-filter-grid,
        .backup-file-actions {
            grid-template-columns: 1fr;
        }

        .backup-file-main {
            flex-direction: column;
        }

        .backup-file-original-name {
            white-space: normal;
        }
    }

    .backup-form .form-group {
        grid-template-rows: auto 44px auto;
    }

    .backup-form input.input {
        height: 44px;
        min-height: 44px;
    }

    .backup-form textarea.input {
        min-height: 44px;
    }
</style>