<style>
    .edit-category-card {
        background: #ffffff;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        border: 1px solid #eef2f7;
    }

    .edit-category-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .edit-category-header h1 {
        margin: 4px 0 6px;
        color: #111827;
        font-size: 1.8rem;
    }

    .edit-category-header span {
        color: #6b7280;
    }

    .edit-category-form {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 14px;
        align-items: end;
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .label {
        color: #111827;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .input {
        height: 42px;
        border: 1px solid #dbe1ea;
        border-radius: 12px;
        padding: 0 12px;
        outline: none;
        background: #ffffff;
        color: #111827;
    }

    .input:focus {
        border-color: #111827;
        box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.1);
    }

    .btn-primary-inline,
    .btn-secondary-inline {
        height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary-inline {
        background: #111827;
        color: #ffffff;
    }

    .btn-secondary-inline {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-primary-inline:hover {
        background: #1f2937;
    }

    .btn-secondary-inline:hover {
        background: #e5e7eb;
    }

    @media (max-width: 720px) {
        .edit-category-header {
            flex-direction: column;
        }

        .edit-category-form {
            grid-template-columns: 1fr;
        }
    }
</style>