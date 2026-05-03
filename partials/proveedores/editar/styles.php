<style>
    .supplier-edit-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .supplier-edit-hero>div {
        min-width: 0;
    }

    .supplier-edit-hero p:first-child,
    .supplier-edit-hero .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .supplier-edit-hero h1,
    .supplier-edit-hero .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .supplier-edit-hero h1+p,
    .supplier-edit-hero .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .supplier-back-btn {
        white-space: nowrap;
    }

    .supplier-edit-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .supplier-edit-form {
        display: grid;
        gap: 22px;
    }

    .supplier-edit-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #f8fafc;
    }

    .supplier-edit-section-header {
        margin-bottom: 18px;
    }

    .supplier-edit-section-header h2,
    .supplier-edit-section-header h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .supplier-edit-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .supplier-edit-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .supplier-edit-grid .form-group-full,
    .form-group-full {
        grid-column: 1 / -1;
    }

    .form-group {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .label,
    .form-group label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .form-group input,
    .form-group textarea,
    .form-group select {
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
    .form-group textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.5;
        font-family: Arial, sans-serif;
    }

    .input::placeholder,
    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .supplier-edit-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .supplier-btn,
    .btn-primary-inline,
    .btn-secondary-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.94rem;
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

    .supplier-btn-save,
    .btn-primary-inline {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .supplier-btn-save:hover,
    .btn-primary-inline:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .supplier-btn-cancel,
    .btn-secondary-inline {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #374151;
    }

    .supplier-btn-cancel:hover,
    .btn-secondary-inline:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        color: #111827;
        transform: translateY(-1px);
    }

    .alert {
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        font-weight: 700;
        line-height: 1.45;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    @media (max-width: 1000px) {
        .supplier-edit-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {

        .supplier-edit-hero,
        .supplier-edit-card {
            padding: 20px;
            border-radius: 16px;
        }

        .supplier-edit-hero {
            flex-direction: column;
            align-items: stretch;
        }

        .supplier-edit-section {
            padding: 18px;
            border-radius: 14px;
        }

        .supplier-edit-grid {
            grid-template-columns: 1fr;
        }

        .supplier-edit-grid .form-group-full,
        .form-group-full {
            grid-column: auto;
        }

        .supplier-edit-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .supplier-btn,
        .btn-primary-inline,
        .btn-secondary-inline,
        .supplier-edit-actions a,
        .supplier-edit-actions button {
            width: 100%;
        }
    }
</style>