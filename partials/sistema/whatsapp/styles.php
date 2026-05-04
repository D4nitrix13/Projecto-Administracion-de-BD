<style>
    .whatsapp-page-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .whatsapp-page-heading .dashboard-eyebrow,
    .whatsapp-page-heading p:first-child {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .whatsapp-page-heading .dashboard-title,
    .whatsapp-page-heading h1 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .whatsapp-page-heading .dashboard-muted,
    .whatsapp-page-heading h1+p {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .whatsapp-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .whatsapp-form,
    .whatsapp-card form {
        display: grid;
        gap: 18px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .whatsapp-current-box,
    .current-whatsapp-box,
    .info-box {
        display: grid;
        gap: 6px;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .whatsapp-current-box span,
    .current-whatsapp-box span,
    .info-box span {
        color: #6b7280;
        font-size: 0.84rem;
        font-weight: 800;
    }

    .whatsapp-current-box strong,
    .current-whatsapp-box strong,
    .info-box strong {
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
    }

    .form-group {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .label,
    .form-group label,
    .whatsapp-card label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .form-group input,
    .whatsapp-card input {
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

    .input::placeholder,
    .form-group input::placeholder,
    .whatsapp-card input::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .form-group input:focus,
    .whatsapp-card input:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .field-help,
    .form-help,
    .whatsapp-help,
    small {
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .form-actions,
    .whatsapp-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-primary,
    .btn-secondary,
    .whatsapp-card button,
    .whatsapp-card a {
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
    .whatsapp-card button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover,
    .btn-primary:hover,
    .whatsapp-card button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline,
    .btn-secondary,
    .whatsapp-card a {
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover,
    .btn-secondary:hover,
    .whatsapp-card a:hover {
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

    @media (max-width: 760px) {

        .whatsapp-page-heading,
        .whatsapp-card {
            padding: 20px;
            border-radius: 16px;
        }

        .whatsapp-form,
        .whatsapp-card form {
            padding: 16px;
        }

        .form-actions,
        .whatsapp-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline,
        .btn-secondary-inline,
        .btn-primary,
        .btn-secondary,
        .whatsapp-card button,
        .whatsapp-card a {
            width: 100%;
        }
    }
</style>