<style>
    .clientes-hero {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .clientes-hero .dashboard-eyebrow,
    .clientes-hero p:first-child {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .clientes-hero .dashboard-title,
    .clientes-hero h1 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .clientes-hero .dashboard-muted,
    .clientes-hero h1+p {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .clientes-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .clientes-header-actions,
    .productos-header-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
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

    .filters-panel,
    .clientes-filter,
    .clientes-filters,
    .filter-form,
    .clientes-card form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(180px, 0.45fr) auto auto;
        gap: 12px;
        align-items: end;
        padding: 18px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .filter-field,
    .form-group {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .filter-field-full,
    .form-group-full {
        grid-column: 1 / -1;
    }

    .label,
    .filter-field label,
    .form-group label,
    .clientes-card form label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .filter-field input,
    .filter-field select,
    .form-group input,
    .form-group select,
    .clientes-card form input,
    .clientes-card form select {
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

    .clientes-card form input[type="hidden"] {
        display: none !important;
    }

    .table-products .acciones form {
        display: inline !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        background: transparent !important;
        grid-template-columns: none !important;
        gap: 0 !important;
    }

    .input::placeholder,
    .filter-field input::placeholder,
    .form-group input::placeholder,
    .clientes-card form input::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .filter-field input:focus,
    .filter-field select:focus,
    .form-group input:focus,
    .form-group select:focus,
    .clientes-card form input:focus,
    .clientes-card form select:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .filter-actions,
    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-primary,
    .btn-secondary,
    .clientes-card form button,
    .clientes-card form a {
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
    .clientes-card form button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover,
    .btn-primary:hover,
    .clientes-card form button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline,
    .btn-secondary,
    .clientes-card form a {
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover,
    .btn-secondary:hover,
    .clientes-card form a:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .section-heading,
    .clientes-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 14px;
    }

    .section-heading h2,
    .clientes-section-header h2,
    .clientes-card h2,
    .clientes-card h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .section-heading p,
    .clientes-section-header p,
    .clientes-card p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .table-wrapper,
    .clientes-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .table-wrapper table,
    .clientes-table,
    .table-clientes {
        width: 100%;
        border-collapse: collapse;
        min-width: 1180px;
    }

    .table-wrapper thead,
    .clientes-table thead,
    .table-clientes thead {
        background: #f3f4f6;
    }

    .table-wrapper th,
    .clientes-table th,
    .table-clientes th {
        padding: 14px 16px;
        color: #667085;
        background: #f3f4f6;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td,
    .clientes-table td,
    .table-clientes td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-wrapper tbody tr,
    .clientes-table tbody tr,
    .table-clientes tbody tr {
        transition: background 0.15s ease;
    }

    .table-wrapper tbody tr:hover,
    .clientes-table tbody tr:hover,
    .table-clientes tbody tr:hover {
        background: #f8fafc;
    }

    .table-wrapper th:last-child,
    .table-wrapper td:last-child,
    .clientes-table th:last-child,
    .clientes-table td:last-child,
    .table-clientes th:last-child,
    .table-clientes td:last-child {
        width: 330px;
        min-width: 330px;
        max-width: 330px;
        text-align: center;
        overflow: visible;
    }

    .acciones,
    .cliente-actions,
    .table-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
        white-space: nowrap;
        width: 100%;
        min-width: 300px;
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

    .btn-accion-ver,
    .btn-action-view {
        background: #eef2ff;
        color: #3730a3;
        border-color: #c7d2fe;
    }

    .btn-accion-ver:hover,
    .btn-action-view:hover {
        background: #e0e7ff;
        border-color: #a5b4fc;
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
    .btn-accion-eliminar,
    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .btn-accion-danger:hover,
    .btn-action-delete:hover,
    .btn-accion-eliminar:hover,
    .btn-delete:hover {
        background: #fecaca;
        border-color: #fca5a5;
    }

    .table-products .acciones .btn-accion-eliminar,
    .table-products .acciones .btn-accion-danger {
        background: #fee2e2 !important;
        color: #991b1b !important;
        border-color: #fecaca !important;
        box-shadow: none !important;
    }

    .table-products .acciones .btn-accion-eliminar:hover,
    .table-products .acciones .btn-accion-danger:hover {
        background: #fecaca !important;
        border-color: #fca5a5 !important;
        transform: none !important;
    }

    .badge,
    .cliente-badge {
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
    .cliente-badge-primary {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .badge-success,
    .cliente-badge-success {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .badge-warning,
    .cliente-badge-warning {
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

    @media (max-width: 1100px) {

        .filters-panel,
        .clientes-filter,
        .clientes-filters,
        .filter-form,
        .clientes-card form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-actions,
        .form-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }

    @media (max-width: 760px) {

        .clientes-hero,
        .clientes-card {
            padding: 20px;
            border-radius: 16px;
        }

        .clientes-header-actions,
        .productos-header-actions {
            justify-content: stretch;
        }

        .clientes-header-actions a,
        .productos-header-actions a {
            width: 100%;
        }

        .section-heading,
        .clientes-section-header {
            flex-direction: column;
        }

        .filters-panel,
        .clientes-filter,
        .clientes-filters,
        .filter-form,
        .clientes-card form {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .filter-actions,
        .form-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline,
        .btn-secondary-inline,
        .btn-primary,
        .btn-secondary,
        .clientes-card form button,
        .clientes-card form a {
            width: 100%;
        }

        .table-wrapper table,
        .clientes-table,
        .table-clientes {
            min-width: 1080px;
        }

        .table-wrapper th:last-child,
        .table-wrapper td:last-child,
        .clientes-table th:last-child,
        .clientes-table td:last-child,
        .table-clientes th:last-child,
        .table-clientes td:last-child {
            width: 320px;
            min-width: 320px;
            max-width: 320px;
        }
    }
</style>