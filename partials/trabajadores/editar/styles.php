<style>
    .worker-edit-hero,
    .usuario-edit-hero,
    .user-edit-hero,
    .dashboard-page-heading {
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

    .worker-edit-hero>div,
    .usuario-edit-hero>div,
    .user-edit-hero>div,
    .dashboard-page-heading>div {
        min-width: 0;
    }

    .worker-edit-hero p:first-child,
    .usuario-edit-hero p:first-child,
    .user-edit-hero p:first-child,
    .dashboard-page-heading p:first-child,
    .worker-edit-hero .dashboard-eyebrow,
    .usuario-edit-hero .dashboard-eyebrow,
    .user-edit-hero .dashboard-eyebrow,
    .dashboard-page-heading .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .worker-edit-hero h1,
    .usuario-edit-hero h1,
    .user-edit-hero h1,
    .dashboard-page-heading h1,
    .worker-edit-hero .dashboard-title,
    .usuario-edit-hero .dashboard-title,
    .user-edit-hero .dashboard-title,
    .dashboard-page-heading .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .worker-edit-hero h1+p,
    .usuario-edit-hero h1+p,
    .user-edit-hero h1+p,
    .dashboard-page-heading h1+p,
    .worker-edit-hero .dashboard-muted,
    .usuario-edit-hero .dashboard-muted,
    .user-edit-hero .dashboard-muted,
    .dashboard-page-heading .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .worker-back-btn,
    .usuario-back-btn,
    .user-back-btn {
        white-space: nowrap;
    }

    .worker-edit-card,
    .usuario-edit-card,
    .user-edit-card,
    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .worker-edit-form,
    .usuario-edit-form,
    .user-edit-form {
        display: grid;
        gap: 22px;
    }

    .worker-edit-section,
    .usuario-edit-section,
    .user-edit-section,
    .form-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #f8fafc;
    }

    .worker-edit-section-header,
    .usuario-edit-section-header,
    .user-edit-section-header,
    .form-section-header {
        margin-bottom: 18px;
    }

    .worker-edit-section-header h2,
    .worker-edit-section-header h3,
    .usuario-edit-section-header h2,
    .usuario-edit-section-header h3,
    .user-edit-section-header h2,
    .user-edit-section-header h3,
    .form-section-header h2,
    .form-section-header h3,
    .worker-edit-card h2,
    .worker-edit-card h3,
    .usuario-edit-card h2,
    .usuario-edit-card h3,
    .user-edit-card h2,
    .user-edit-card h3,
    .dashboard-card h2,
    .dashboard-card h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .worker-edit-section-header p,
    .usuario-edit-section-header p,
    .user-edit-section-header p,
    .form-section-header p,
    .worker-edit-card p,
    .usuario-edit-card p,
    .user-edit-card p,
    .dashboard-card p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .worker-edit-grid,
    .usuario-edit-grid,
    .user-edit-grid,
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .worker-edit-grid.cols-3,
    .usuario-edit-grid.cols-3,
    .user-edit-grid.cols-3,
    .form-grid.cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .form-group {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .form-group-full,
    .worker-edit-grid .form-group-full,
    .usuario-edit-grid .form-group-full,
    .user-edit-grid .form-group-full,
    .form-grid .form-group-full {
        grid-column: 1 / -1;
    }

    .label,
    .form-group label,
    .worker-edit-card label,
    .usuario-edit-card label,
    .user-edit-card label,
    .dashboard-card label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .worker-edit-card input,
    .worker-edit-card select,
    .worker-edit-card textarea,
    .usuario-edit-card input,
    .usuario-edit-card select,
    .usuario-edit-card textarea,
    .user-edit-card input,
    .user-edit-card select,
    .user-edit-card textarea,
    .dashboard-card input,
    .dashboard-card select,
    .dashboard-card textarea {
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
    .worker-edit-card textarea,
    .usuario-edit-card textarea,
    .user-edit-card textarea,
    .dashboard-card textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.5;
        font-family: Arial, sans-serif;
    }

    .input::placeholder,
    .form-group input::placeholder,
    .form-group textarea::placeholder,
    .worker-edit-card input::placeholder,
    .worker-edit-card textarea::placeholder,
    .usuario-edit-card input::placeholder,
    .usuario-edit-card textarea::placeholder,
    .user-edit-card input::placeholder,
    .user-edit-card textarea::placeholder,
    .dashboard-card input::placeholder,
    .dashboard-card textarea::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .worker-edit-card input:focus,
    .worker-edit-card select:focus,
    .worker-edit-card textarea:focus,
    .usuario-edit-card input:focus,
    .usuario-edit-card select:focus,
    .usuario-edit-card textarea:focus,
    .user-edit-card input:focus,
    .user-edit-card select:focus,
    .user-edit-card textarea:focus,
    .dashboard-card input:focus,
    .dashboard-card select:focus,
    .dashboard-card textarea:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .field-help,
    .form-help,
    small {
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .worker-current-section,
    .usuario-current-section,
    .user-current-section,
    .current-section-box {
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        color: #374151;
        line-height: 1.45;
    }

    .worker-current-section strong,
    .usuario-current-section strong,
    .user-current-section strong,
    .current-section-box strong {
        color: #111827;
        font-weight: 900;
    }

    .worker-edit-actions,
    .usuario-edit-actions,
    .user-edit-actions,
    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .worker-btn,
    .usuario-btn,
    .user-btn,
    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-primary,
    .btn-secondary,
    .worker-edit-actions button,
    .usuario-edit-actions button,
    .user-edit-actions button,
    .form-actions button,
    .dashboard-card button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
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

    .worker-btn-save,
    .usuario-btn-save,
    .user-btn-save,
    .btn-primary-inline,
    .btn-primary,
    .worker-edit-actions button,
    .usuario-edit-actions button,
    .user-edit-actions button,
    .form-actions button,
    .dashboard-card button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .worker-btn-save:hover,
    .usuario-btn-save:hover,
    .user-btn-save:hover,
    .btn-primary-inline:hover,
    .btn-primary:hover,
    .worker-edit-actions button:hover,
    .usuario-edit-actions button:hover,
    .user-edit-actions button:hover,
    .form-actions button:hover,
    .dashboard-card button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .worker-btn-cancel,
    .usuario-btn-cancel,
    .user-btn-cancel,
    .btn-secondary-inline,
    .btn-secondary {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #374151;
    }

    .worker-btn-cancel:hover,
    .usuario-btn-cancel:hover,
    .user-btn-cancel:hover,
    .btn-secondary-inline:hover,
    .btn-secondary:hover {
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

        .worker-edit-grid,
        .usuario-edit-grid,
        .user-edit-grid,
        .form-grid,
        .worker-edit-grid.cols-3,
        .usuario-edit-grid.cols-3,
        .user-edit-grid.cols-3,
        .form-grid.cols-3 {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 760px) {

        .worker-edit-hero,
        .usuario-edit-hero,
        .user-edit-hero,
        .dashboard-page-heading,
        .worker-edit-card,
        .usuario-edit-card,
        .user-edit-card,
        .dashboard-card {
            padding: 20px;
            border-radius: 16px;
        }

        .worker-edit-hero,
        .usuario-edit-hero,
        .user-edit-hero,
        .dashboard-page-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .worker-edit-section,
        .usuario-edit-section,
        .user-edit-section,
        .form-section {
            padding: 18px;
            border-radius: 14px;
        }

        .worker-edit-grid,
        .usuario-edit-grid,
        .user-edit-grid,
        .form-grid,
        .worker-edit-grid.cols-3,
        .usuario-edit-grid.cols-3,
        .user-edit-grid.cols-3,
        .form-grid.cols-3 {
            grid-template-columns: 1fr;
        }

        .form-group-full,
        .worker-edit-grid .form-group-full,
        .usuario-edit-grid .form-group-full,
        .user-edit-grid .form-group-full,
        .form-grid .form-group-full {
            grid-column: auto;
        }

        .worker-edit-actions,
        .usuario-edit-actions,
        .user-edit-actions,
        .form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .worker-btn,
        .usuario-btn,
        .user-btn,
        .btn-primary-inline,
        .btn-secondary-inline,
        .btn-primary,
        .btn-secondary,
        .worker-edit-actions a,
        .worker-edit-actions button,
        .usuario-edit-actions a,
        .usuario-edit-actions button,
        .user-edit-actions a,
        .user-edit-actions button,
        .form-actions a,
        .form-actions button {
            width: 100%;
        }
    }
</style>