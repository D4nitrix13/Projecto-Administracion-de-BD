<style>
    .product-page-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .product-page-heading p:first-child,
    .product-page-heading .dashboard-eyebrow,
    .product-page-heading .product-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .product-page-heading h1,
    .product-page-heading .dashboard-title,
    .product-page-heading .product-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .product-page-heading h1+p,
    .product-page-heading .dashboard-muted,
    .product-page-heading .product-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .product-edit-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .product-edit-form {
        display: grid;
        gap: 22px;
    }

    .product-form-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #f8fafc;
    }

    .product-section-title {
        margin-bottom: 18px;
    }

    .product-section-title h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .product-section-title p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .product-form-grid {
        display: grid;
        gap: 18px;
    }

    .product-form-grid.cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .product-form-grid.cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .form-group {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .form-group-full {
        grid-column: 1 / -1;
    }

    .product-edit-card .label,
    .label {
        display: block;
        margin: 0;
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .product-edit-card .input,
    .input {
        width: 100%;
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
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

    .product-edit-card .input::placeholder,
    .input::placeholder {
        color: #8b95a5;
    }

    .product-edit-card .input:focus,
    .input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }

    .product-edit-card .input[type="file"],
    .input[type="file"] {
        padding: 9px 12px;
    }

    .product-textarea,
    textarea.input {
        min-height: 110px;
        resize: vertical;
        font-family: Arial, sans-serif;
        line-height: 1.5;
    }

    .dashboard-muted,
    .field-help {
        margin: 0;
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.4;
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

    .product-edit-card .product-image-row {
        display: grid;
        grid-template-columns: 150px minmax(0, 1fr);
        gap: 20px;
        align-items: center;
    }

    .product-edit-card .product-image-preview {
        width: 150px !important;
        height: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        overflow: hidden !important;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
    }

    .product-edit-card .product-image-preview a {
        width: 150px !important;
        height: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        display: block !important;
        overflow: hidden !important;
        border-radius: 14px;
        text-decoration: none;
    }

    .product-edit-card .product-thumb,
    .product-edit-card .product-image-preview img,
    .product-edit-card img.product-thumb {
        width: 150px !important;
        height: 150px !important;
        min-width: 150px !important;
        min-height: 150px !important;
        max-width: 150px !important;
        max-height: 150px !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 14px !important;
        border: none !important;
        background: #f3f4f6 !important;
    }

    .product-edit-card .product-thumb-empty {
        width: 150px !important;
        height: 150px !important;
        min-width: 150px !important;
        min-height: 150px !important;
        max-width: 150px !important;
        max-height: 150px !important;
        display: grid !important;
        place-items: center !important;
        color: #6b7280;
        background: #f8fafc;
        border-radius: 14px;
        font-size: 0.82rem;
        font-weight: 800;
        text-align: center;
        line-height: 1.35;
    }

    .product-image-input {
        display: grid;
        gap: 7px;
        max-width: 520px;
    }

    .product-image-input small {
        display: block;
        margin-top: 2px;
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .product-form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding-top: 4px;
    }

    .product-save-btn {
        min-width: 180px;
        text-align: center;
    }

    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-primary,
    .btn-secondary {
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
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .btn-primary-inline,
    .btn-primary {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover,
    .btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline,
    .btn-secondary {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #374151;
    }

    .btn-secondary-inline:hover,
    .btn-secondary:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    @media (max-width: 900px) {

        .product-form-grid.cols-2,
        .product-form-grid.cols-3 {
            grid-template-columns: 1fr;
        }

        .form-group-full {
            grid-column: auto;
        }

        .product-edit-card .product-image-row {
            grid-template-columns: 150px minmax(0, 1fr);
        }

        .product-form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .product-form-actions a,
        .product-form-actions button {
            width: 100%;
        }
    }

    @media (max-width: 760px) {

        .product-page-heading,
        .product-edit-card {
            padding: 20px;
            border-radius: 16px;
        }

        .product-form-section {
            padding: 18px;
            border-radius: 14px;
        }
    }

    @media (max-width: 520px) {
        .product-edit-card .product-image-row {
            grid-template-columns: 1fr;
        }

        .product-edit-card .product-image-preview,
        .product-edit-card .product-image-preview a,
        .product-edit-card .product-thumb,
        .product-edit-card .product-image-preview img,
        .product-edit-card img.product-thumb,
        .product-edit-card .product-thumb-empty {
            width: 150px !important;
            height: 150px !important;
            max-width: 150px !important;
            max-height: 150px !important;
        }
    }
</style>