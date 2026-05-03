<style>
    .categories-hero {
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

    .categories-hero>div {
        min-width: 0;
    }

    .categories-hero p:first-child,
    .categories-hero .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .categories-hero h1,
    .categories-hero .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .categories-hero h1+p,
    .categories-hero .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .categories-panel {
        display: flex;
        flex-direction: column;
        gap: 20px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .category-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }

    .category-section-header h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .category-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .category-form,
    .category-filter {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: end;
        padding: 18px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
    }

    .form-group {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input {
        width: 100%;
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        outline: none;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        box-sizing: border-box;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease,
            background 0.15s ease;
    }

    .input::placeholder {
        color: #8b95a5;
    }

    .input:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .filter-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

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
        border: none;
        cursor: pointer;
        white-space: nowrap;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .btn-primary-inline {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline {
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .category-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-weight: 900;
        font-size: 0.82rem;
        white-space: nowrap;
    }

    .category-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .category-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
        background: #ffffff;
    }

    .category-table thead {
        background: #f3f4f6;
    }

    .category-table th {
        background: #f3f4f6;
        color: #667085;
        text-align: left;
        padding: 14px 16px;
        font-size: 0.86rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .category-table td {
        padding: 14px 16px;
        border-top: 1px solid #e5e7eb;
        border-bottom: none;
        color: #111827;
        font-size: 0.92rem;
        vertical-align: middle;
    }

    .category-table tbody tr {
        transition: background 0.15s ease;
    }

    .category-table tbody tr:hover {
        background: #f8fafc;
    }

    .category-table th:first-child,
    .category-table td:first-child {
        width: 120px;
    }

    .category-table th:last-child,
    .category-table td:last-child {
        width: 260px;
        min-width: 260px;
        text-align: center;
    }

    .category-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 82px;
        min-height: 32px;
        padding: 6px 12px;
        border-radius: 9px;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 800;
        border: 1px solid transparent;
        cursor: pointer;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease;
    }

    .btn-action:hover {
        transform: translateY(-1px);
    }

    .btn-edit {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .btn-edit:hover {
        background: #bae6fd;
        border-color: #7dd3fc;
    }

    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .btn-delete:hover {
        background: #fecaca;
        border-color: #fca5a5;
    }

    .readonly-box,
    .empty-box {
        padding: 30px 16px;
        border-radius: 14px;
        background: #f8fafc;
        color: #6b7280;
        border: 1px dashed #cbd5e1;
        text-align: center;
        line-height: 1.5;
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

        .category-form,
        .category-filter {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            justify-content: flex-end;
        }
    }

    @media (max-width: 760px) {

        .categories-hero,
        .categories-panel {
            padding: 20px;
            border-radius: 16px;
        }

        .categories-hero,
        .category-section-header {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline,
        .btn-secondary-inline {
            width: 100%;
        }

        .category-table {
            min-width: 640px;
        }

        .category-table th:last-child,
        .category-table td:last-child {
            width: 220px;
            min-width: 220px;
        }

        .category-actions {
            gap: 8px;
        }
    }
</style>