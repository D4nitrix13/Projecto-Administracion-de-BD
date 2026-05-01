<style>
    .categories-hero {
        margin-bottom: 22px;
    }

    .categories-panel {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .category-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }

    .category-section-header h2 {
        margin: 0;
        color: #111827;
        font-size: 1.1rem;
    }

    .category-section-header p {
        margin: 5px 0 0;
        color: #6b7280;
    }

    .category-form,
    .category-filter {
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

    .filter-actions {
        display: flex;
        gap: 8px;
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

    .btn-primary-inline:hover {
        background: #1f2937;
    }

    .btn-secondary-inline {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary-inline:hover {
        background: #e5e7eb;
    }

    .category-count {
        padding: 8px 12px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        font-weight: 800;
        font-size: 0.82rem;
    }

    .category-table-wrapper {
        overflow-x: auto;
        border-radius: 16px;
        border: 1px solid #eef2f7;
    }

    .category-table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
    }

    .category-table th {
        background: #f3f4f6;
        color: #6b7280;
        text-align: left;
        padding: 14px;
        font-size: 0.86rem;
    }

    .category-table td {
        padding: 14px;
        border-bottom: 1px solid #eef2f7;
        color: #111827;
    }

    .category-table tbody tr:hover {
        background: #f8fafc;
    }

    .category-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 7px 11px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .btn-edit {
        background: #eef2ff;
        color: #3730a3;
    }

    .btn-delete {
        background: #fef2f2;
        color: #b91c1c;
    }

    .readonly-box,
    .empty-box {
        padding: 18px;
        border-radius: 16px;
        background: #f8fafc;
        color: #6b7280;
        border: 1px solid #eef2f7;
    }

    @media (max-width: 720px) {

        .category-form,
        .category-filter {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .category-section-header {
            flex-direction: column;
        }
    }
</style>