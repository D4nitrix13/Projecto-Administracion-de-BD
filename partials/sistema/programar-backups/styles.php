<style>
    .programar-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .programar-page-heading h1 {
        margin-bottom: 10px;
    }

    .programar-page-heading p {
        max-width: 780px;
    }

    .programar-eyebrow,
    .programar-kicker {
        display: block;
        margin: 0 0 8px;
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .programar-kicker-danger {
        color: #dc2626;
    }

    .programar-heading-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .programar-secondary-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #334155;
        font-size: 0.88rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }

    .programar-secondary-button:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .programar-alert {
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 12px;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.5;
    }

    .programar-alert-error {
        color: #991b1b;
        border: 1px solid #fecaca;
        background: #fef2f2;
    }

    .programar-alert-success {
        color: #166534;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
    }

    .programar-summary-grid {
        margin-bottom: 24px;
    }

    .programar-summary-card h2 {
        font-size: 1.45rem;
        line-height: 1.18;
    }

    .programar-summary-success {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .programar-summary-danger {
        border-color: #fecaca;
        background: #fef2f2;
    }

    .programar-summary-info {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .programar-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(320px, 0.8fr);
        gap: 28px;
        align-items: start;
        margin-bottom: 28px;
    }

    .programar-side {
        display: grid;
        gap: 20px;
    }

    .programar-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .programar-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .programar-card-header h2 {
        margin: 0;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .programar-card-header p {
        margin: 8px 0 0;
        color: #6b7280;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .programar-card-text {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .programar-form {
        display: grid;
        gap: 20px;
    }

    .programar-field {
        display: grid;
        gap: 8px;
    }

    .programar-field label {
        color: #334155;
        font-size: 0.88rem;
        font-weight: 900;
    }

    .programar-field input[type="number"],
    .programar-field select {
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

    .programar-field input[type="number"]:focus,
    .programar-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .programar-field small {
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .programar-radio-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .programar-radio-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        min-height: 92px;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .programar-radio-card:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .programar-radio-card input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        accent-color: #2563eb;
        flex: 0 0 auto;
    }

    .programar-radio-card span {
        display: grid;
        gap: 5px;
    }

    .programar-radio-card strong {
        color: #111827;
        font-size: 0.94rem;
        font-weight: 900;
    }

    .programar-radio-card small {
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.45;
    }

    .programar-radio-card:has(input:checked) {
        background: #eff6ff;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
    }

    .programar-form-row {
        display: grid;
        grid-template-columns: minmax(160px, 0.55fr) minmax(220px, 1fr);
        gap: 16px;
    }

    .programar-note {
        padding: 16px;
        border: 1px solid #fde68a;
        border-radius: 14px;
        background: #fffbeb;
    }

    .programar-note strong {
        display: block;
        margin-bottom: 6px;
        color: #92400e;
        font-size: 0.9rem;
        font-weight: 900;
    }

    .programar-note p {
        margin: 0;
        color: #78350f;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .programar-actions {
        display: flex;
        justify-content: flex-end;
    }

    .programar-primary-button,
    .programar-danger-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
    }

    .programar-primary-button {
        border: 0;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .programar-primary-button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.28);
    }

    .programar-info-list {
        display: grid;
        gap: 12px;
    }

    .programar-info-list div {
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .programar-info-list span {
        display: block;
        margin-bottom: 6px;
        color: #667085;
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .programar-info-list strong {
        display: block;
        color: #111827;
        font-size: 0.92rem;
        font-weight: 900;
        line-height: 1.45;
    }

    .programar-danger-card {
        border-color: #fecaca;
        background: #fffafa;
    }

    .programar-danger-button {
        width: 100%;
        margin-top: 16px;
        border: 1px solid #fecaca;
        background: #fee2e2;
        color: #991b1b;
    }

    .programar-danger-button:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }

    @media (max-width: 1100px) {
        .programar-layout {
            grid-template-columns: 1fr;
        }

        .programar-radio-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .programar-page-heading {
            flex-direction: column;
            align-items: flex-start;
        }

        .programar-heading-actions,
        .programar-secondary-button {
            width: 100%;
        }

        .programar-form-row {
            grid-template-columns: 1fr;
        }

        .programar-card {
            padding: 20px;
        }

        .programar-actions {
            justify-content: stretch;
        }

        .programar-primary-button {
            width: 100%;
        }
    }

    .programar-type-help {
        margin-top: 8px;
        padding: 14px 16px;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        background: #eff6ff;
    }

    .programar-type-help strong {
        display: block;
        margin-bottom: 8px;
        color: #1d4ed8;
        font-size: 0.88rem;
        font-weight: 900;
    }

    .programar-type-help ul {
        display: grid;
        gap: 6px;
        margin: 0;
        padding-left: 18px;
    }

    .programar-type-help li {
        color: #334155;
        font-size: 0.86rem;
        line-height: 1.45;
    }

    .programar-type-help b {
        color: #111827;
    }
</style>