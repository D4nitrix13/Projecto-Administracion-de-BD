<style>
    .usuarios-hero {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .usuarios-hero .dashboard-eyebrow,
    .usuarios-hero p:first-child {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .usuarios-hero .dashboard-title,
    .usuarios-hero h1 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .usuarios-hero .dashboard-muted,
    .usuarios-hero h1+p {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .usuarios-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
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

    .usuarios-card h2,
    .usuarios-card h3,
    .section-heading h2,
    .section-heading h3,
    .usuarios-section-header h2,
    .usuarios-section-header h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .usuarios-card p,
    .section-heading p,
    .usuarios-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .section-heading,
    .usuarios-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .usuarios-form,
    .user-form,
    .create-user-form,
    .usuarios-card form:not(.filter-form):not(.usuarios-filter):not(.usuarios-filters):not(.filters-panel) {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: end;
        padding: 18px;
        margin-top: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .filter-form,
    .usuarios-filter,
    .usuarios-filters,
    .filters-panel {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(180px, 0.45fr) minmax(180px, 0.45fr) auto auto;
        gap: 12px;
        align-items: end;
        padding: 18px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .form-group,
    .filter-field,
    .user-field {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .form-group-full,
    .filter-field-full,
    .user-field-full {
        grid-column: 1 / -1;
    }

    .label,
    .form-group label,
    .filter-field label,
    .user-field label,
    .usuarios-form label,
    .user-form label,
    .create-user-form label,
    .filter-form label,
    .usuarios-filter label,
    .usuarios-filters label,
    .filters-panel label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .form-group input,
    .form-group select,
    .filter-field input,
    .filter-field select,
    .user-field input,
    .user-field select,
    .usuarios-form input,
    .usuarios-form select,
    .user-form input,
    .user-form select,
    .create-user-form input,
    .create-user-form select,
    .filter-form input,
    .filter-form select,
    .usuarios-filter input,
    .usuarios-filter select,
    .usuarios-filters input,
    .usuarios-filters select,
    .filters-panel input,
    .filters-panel select {
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
    .filter-field input::placeholder,
    .user-field input::placeholder,
    .usuarios-form input::placeholder,
    .user-form input::placeholder,
    .create-user-form input::placeholder,
    .filter-form input::placeholder,
    .usuarios-filter input::placeholder,
    .usuarios-filters input::placeholder,
    .filters-panel input::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .form-group input:focus,
    .form-group select:focus,
    .filter-field input:focus,
    .filter-field select:focus,
    .user-field input:focus,
    .user-field select:focus,
    .usuarios-form input:focus,
    .usuarios-form select:focus,
    .user-form input:focus,
    .user-form select:focus,
    .create-user-form input:focus,
    .create-user-form select:focus,
    .filter-form input:focus,
    .filter-form select:focus,
    .usuarios-filter input:focus,
    .usuarios-filter select:focus,
    .usuarios-filters input:focus,
    .usuarios-filters select:focus,
    .filters-panel input:focus,
    .filters-panel select:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .form-actions,
    .filter-actions,
    .user-actions {
        display: flex;
        justify-content: flex-end;
        align-items: end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .form-actions {
        grid-column: 1 / -1;
        margin-top: 2px;
    }

    .filter-actions {
        grid-column: auto;
        margin: 0;
        align-self: end;
    }

    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-primary,
    .btn-secondary,
    .usuarios-card form button,
    .usuarios-card form a {
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
    .usuarios-card form button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover,
    .btn-primary:hover,
    .usuarios-card form button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline,
    .btn-secondary,
    .usuarios-card form a {
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover,
    .btn-secondary:hover,
    .usuarios-card form a:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .table-wrapper,
    .usuarios-table-wrapper,
    .user-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .table-wrapper table,
    .usuarios-table,
    .user-table,
    .table-usuarios {
        width: 100%;
        border-collapse: collapse;
        min-width: 1080px;
    }

    .table-wrapper thead,
    .usuarios-table thead,
    .user-table thead,
    .table-usuarios thead {
        background: #f3f4f6;
    }

    .table-wrapper th,
    .usuarios-table th,
    .user-table th,
    .table-usuarios th {
        padding: 14px 16px;
        color: #667085;
        background: #f3f4f6;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td,
    .usuarios-table td,
    .user-table td,
    .table-usuarios td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-wrapper tbody tr,
    .usuarios-table tbody tr,
    .user-table tbody tr,
    .table-usuarios tbody tr {
        transition: background 0.15s ease;
    }

    .table-wrapper tbody tr:hover,
    .usuarios-table tbody tr:hover,
    .user-table tbody tr:hover,
    .table-usuarios tbody tr:hover {
        background: #f8fafc;
    }

    .table-wrapper th:last-child,
    .table-wrapper td:last-child,
    .usuarios-table th:last-child,
    .usuarios-table td:last-child,
    .user-table th:last-child,
    .user-table td:last-child,
    .table-usuarios th:last-child,
    .table-usuarios td:last-child {
        width: 280px;
        min-width: 280px;
        max-width: 280px;
        text-align: center;
        overflow: visible;
    }

    .acciones,
    .usuario-actions,
    .user-row-actions,
    .table-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
        white-space: nowrap;
        width: 100%;
        min-width: 240px;
        overflow: visible;
    }

    .btn-accion,
    .btn-action,
    .btn-edit,
    .btn-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        min-width: 86px;
        padding: 6px 12px;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        flex: 0 0 auto;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease;
    }

    .btn-accion:hover,
    .btn-action:hover,
    .btn-edit:hover,
    .btn-delete:hover {
        transform: translateY(-1px);
    }

    .btn-accion-editar,
    .btn-action-edit,
    .btn-edit {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .btn-accion-editar:hover,
    .btn-action-edit:hover,
    .btn-edit:hover {
        background: #bae6fd;
        border-color: #7dd3fc;
    }

    .btn-accion-danger,
    .btn-action-delete,
    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .btn-accion-danger:hover,
    .btn-action-delete:hover,
    .btn-delete:hover {
        background: #fecaca;
        border-color: #fca5a5;
    }

    .badge,
    .usuario-badge,
    .user-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
        font-size: 0.78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-primary,
    .usuario-badge-primary,
    .user-badge-primary {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .badge-success,
    .usuario-badge-success,
    .user-badge-success {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .badge-warning,
    .usuario-badge-warning,
    .user-badge-warning {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .empty-message,
    .empty-box,
    .readonly-box {
        margin: 0;
        padding: 30px 16px;
        color: #6b7280;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        line-height: 1.5;
    }

    @media (max-width: 1200px) {

        .usuarios-form,
        .user-form,
        .create-user-form,
        .usuarios-card form:not(.filter-form):not(.usuarios-filter):not(.usuarios-filters):not(.filters-panel),
        .filter-form,
        .usuarios-filter,
        .usuarios-filters,
        .filters-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }

    @media (max-width: 760px) {

        .usuarios-hero,
        .usuarios-card {
            padding: 20px;
            border-radius: 16px;
        }

        .section-heading,
        .usuarios-section-header {
            flex-direction: column;
        }

        .usuarios-form,
        .user-form,
        .create-user-form,
        .usuarios-card form:not(.filter-form):not(.usuarios-filter):not(.usuarios-filters):not(.filters-panel),
        .filter-form,
        .usuarios-filter,
        .usuarios-filters,
        .filters-panel {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .form-actions,
        .filter-actions,
        .user-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline,
        .btn-secondary-inline,
        .btn-primary,
        .btn-secondary,
        .usuarios-card form button,
        .usuarios-card form a {
            width: 100%;
        }

        .table-wrapper table,
        .usuarios-table,
        .user-table,
        .table-usuarios {
            min-width: 980px;
        }
    }
</style>