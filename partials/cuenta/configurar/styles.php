<style>
    .account-page-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .account-page-heading .dashboard-eyebrow,
    .account-page-heading p:first-child {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .account-page-heading .dashboard-title,
    .account-page-heading h1 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .account-page-heading .dashboard-muted,
    .account-page-heading h1 + p {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .account-config-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .account-form,
    .account-config-form,
    .config-account-form,
    .account-config-card form {
        display: grid;
        gap: 22px;
    }

    .account-section,
    .account-form-section,
    .form-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #f8fafc;
    }

    .account-section-header,
    .account-form-section-header,
    .form-section-header,
    .section-heading {
        margin-bottom: 18px;
    }

    .account-section-header h2,
    .account-section-header h3,
    .account-form-section-header h2,
    .account-form-section-header h3,
    .form-section-header h2,
    .form-section-header h3,
    .section-heading h2,
    .section-heading h3,
    .account-config-card h2,
    .account-config-card h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .account-section-header p,
    .account-form-section-header p,
    .form-section-header p,
    .section-heading p,
    .account-config-card p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .account-form-grid,
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-group {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .form-group-full,
    .account-form-grid .form-group-full,
    .form-grid .form-group-full {
        grid-column: 1 / -1;
    }

    .label,
    .form-group label,
    .account-config-card label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .account-config-card input,
    .account-config-card select,
    .account-config-card textarea {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
        box-sizing: border-box;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease,
            background 0.15s ease;
    }

    textarea.input,
    .form-group textarea,
    .account-config-card textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.5;
        font-family: Arial, sans-serif;
    }

    .input::placeholder,
    .form-group input::placeholder,
    .form-group textarea::placeholder,
    .account-config-card input::placeholder,
    .account-config-card textarea::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .account-config-card input:focus,
    .account-config-card select:focus,
    .account-config-card textarea:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .account-info-box,
    .readonly-box,
    .current-section-box {
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        color: #374151;
        line-height: 1.45;
    }

    .account-info-box span,
    .readonly-box span,
    .current-section-box span {
        display: block;
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .account-info-box strong,
    .readonly-box strong,
    .current-section-box strong {
        color: #111827;
        font-weight: 900;
    }

    .field-help,
    .form-help,
    small {
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .account-actions,
    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-primary,
    .btn-secondary,
    .account-config-card button,
    .account-config-card a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.94rem;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        box-sizing: border-box;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .btn-primary-inline,
    .btn-primary,
    .account-config-card button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover,
    .btn-primary:hover,
    .account-config-card button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline,
    .btn-secondary,
    .account-config-card a {
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover,
    .btn-secondary:hover,
    .account-config-card a:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .alert {
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        font-weight: 700;
        line-height: 1.45;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    @media (max-width: 900px) {
        .account-form-grid,
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group-full,
        .account-form-grid .form-group-full,
        .form-grid .form-group-full {
            grid-column: auto;
        }
    }

    @media (max-width: 760px) {
        .account-page-heading,
        .account-config-card {
            padding: 20px;
            border-radius: 16px;
        }

        .account-section,
        .account-form-section,
        .form-section {
            padding: 18px;
            border-radius: 14px;
        }

        .account-actions,
        .form-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline,
        .btn-secondary-inline,
        .btn-primary,
        .btn-secondary,
        .account-config-card button,
        .account-config-card a {
            width: 100%;
        }
    }
</style>