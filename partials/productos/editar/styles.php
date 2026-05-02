<style>
    .product-edit-card {
        padding: 28px;
    }

    .product-edit-form {
        display: grid;
        gap: 24px;
    }

    .product-form-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #ffffff;
    }

    .product-section-title {
        margin-bottom: 18px;
    }

    .product-section-title h3 {
        margin: 0 0 6px;
        font-size: 1.1rem;
        color: #111827;
    }

    .product-section-title p {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
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

    .form-group-full {
        grid-column: 1 / -1;
    }

    .product-edit-card .label {
        display: block;
        margin-bottom: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #111827;
    }

    .product-edit-card .input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 11px 13px;
        font-size: 0.95rem;
        color: #111827;
        background: #fff;
        box-sizing: border-box;
    }

    .product-edit-card .input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .product-textarea {
        min-height: 110px;
        resize: vertical;
        font-family: Arial, sans-serif;
        line-height: 1.5;
    }

    /* ==============================
       Imagen del producto
       Tamaño mediano controlado
       ============================== */

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
    }

    .product-edit-card .product-image-preview a {
        width: 150px !important;
        height: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        display: block !important;
        overflow: hidden !important;
        border-radius: 14px;
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
        border: 1px solid #e5e7eb !important;
        background: #f3f4f6 !important;
    }

    .product-edit-card .product-thumb-empty {
        display: grid !important;
        place-items: center !important;
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 700;
        text-align: center;
    }

    .product-image-input {
        max-width: 520px;
    }

    .product-image-input small {
        display: block;
        margin-top: 8px;
    }

    .product-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 4px;
    }

    .product-save-btn {
        min-width: 180px;
        text-align: center;
    }

    @media (max-width: 900px) {

        .product-form-grid.cols-2,
        .product-form-grid.cols-3 {
            grid-template-columns: 1fr;
        }

        .product-edit-card .product-image-row {
            grid-template-columns: 150px minmax(0, 1fr);
        }

        .product-form-actions {
            flex-direction: column;
        }

        .product-form-actions a,
        .product-form-actions button {
            width: 100%;
        }
    }

    @media (max-width: 520px) {
        .product-edit-card {
            padding: 20px;
        }

        .product-form-section {
            padding: 18px;
        }

        .product-edit-card .product-image-row {
            grid-template-columns: 1fr;
        }

        .product-edit-card .product-image-preview,
        .product-edit-card .product-image-preview a,
        .product-edit-card .product-thumb,
        .product-edit-card .product-image-preview img,
        .product-edit-card img.product-thumb {
            width: 150px !important;
            height: 150px !important;
            max-width: 150px !important;
            max-height: 150px !important;
        }
    }
</style>