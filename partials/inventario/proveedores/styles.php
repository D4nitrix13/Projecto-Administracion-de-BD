<style>
    .proveedores-hero {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .proveedores-hero .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .proveedores-hero .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .proveedores-hero .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
    }

    .proveedores-card {
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

    .proveedores-card h2,
    .proveedores-card h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .proveedores-card p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .section-heading,
    .proveedores-section-header,
    .provider-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .section-heading h2,
    .proveedores-section-header h2,
    .provider-section-header h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .section-heading p,
    .proveedores-section-header p,
    .provider-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .proveedor-form,
    .provider-form,
    .create-provider-form,
    .proveedores-card form:not(.filter-form):not(.proveedores-filter):not(.proveedores-filters):not(.filters-panel) {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        align-items: end;
        padding: 18px;
        margin-top: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .filter-form,
    .proveedores-filter,
    .proveedores-filters,
    .filters-panel {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 12px;
        align-items: end;
        padding: 18px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .proveedores-card form input[type="hidden"] {
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

    .form-group,
    .filter-field,
    .provider-field {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .form-group-full,
    .filter-field-full,
    .provider-field-full {
        grid-column: 1 / -1;
    }

    .label,
    .form-group label,
    .filter-field label,
    .provider-field label,
    .proveedor-form label,
    .provider-form label,
    .create-provider-form label,
    .filter-form label,
    .proveedores-filter label,
    .proveedores-filters label,
    .filters-panel label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input,
    .form-group input,
    .form-group textarea,
    .form-group select,
    .filter-field input,
    .filter-field select,
    .provider-field input,
    .provider-field textarea,
    .provider-field select,
    .proveedor-form input,
    .proveedor-form textarea,
    .proveedor-form select,
    .provider-form input,
    .provider-form textarea,
    .provider-form select,
    .create-provider-form input,
    .create-provider-form textarea,
    .create-provider-form select,
    .filter-form input,
    .filter-form select,
    .proveedores-filter input,
    .proveedores-filter select,
    .proveedores-filters input,
    .proveedores-filters select,
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

    textarea.input,
    .form-group textarea,
    .provider-field textarea,
    .proveedor-form textarea,
    .provider-form textarea,
    .create-provider-form textarea {
        min-height: 96px;
        resize: vertical;
        line-height: 1.5;
    }

    .input::placeholder,
    .form-group input::placeholder,
    .form-group textarea::placeholder,
    .filter-field input::placeholder,
    .provider-field input::placeholder,
    .provider-field textarea::placeholder,
    .proveedor-form input::placeholder,
    .proveedor-form textarea::placeholder,
    .provider-form input::placeholder,
    .provider-form textarea::placeholder,
    .create-provider-form input::placeholder,
    .create-provider-form textarea::placeholder,
    .filter-form input::placeholder,
    .proveedores-filter input::placeholder,
    .proveedores-filters input::placeholder,
    .filters-panel input::placeholder {
        color: #8b95a5;
    }

    .input:focus,
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus,
    .filter-field input:focus,
    .filter-field select:focus,
    .provider-field input:focus,
    .provider-field textarea:focus,
    .provider-field select:focus,
    .proveedor-form input:focus,
    .proveedor-form textarea:focus,
    .proveedor-form select:focus,
    .provider-form input:focus,
    .provider-form textarea:focus,
    .provider-form select:focus,
    .create-provider-form input:focus,
    .create-provider-form textarea:focus,
    .create-provider-form select:focus,
    .filter-form input:focus,
    .filter-form select:focus,
    .proveedores-filter input:focus,
    .proveedores-filter select:focus,
    .proveedores-filters input:focus,
    .proveedores-filters select:focus,
    .filters-panel input:focus,
    .filters-panel select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }

    .form-actions,
    .filter-actions,
    .provider-actions {
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
    .proveedores-card form button {
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
    .proveedores-card form button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover,
    .btn-primary:hover,
    .proveedores-card form button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline,
    .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover,
    .btn-secondary:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .table-wrapper,
    .proveedores-table-wrapper,
    .provider-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .table-wrapper table,
    .proveedores-table,
    .provider-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .table-wrapper thead,
    .proveedores-table thead,
    .provider-table thead {
        background: #f3f4f6;
    }

    .table-wrapper th,
    .proveedores-table th,
    .provider-table th {
        padding: 14px 16px;
        color: #667085;
        background: #f3f4f6;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td,
    .proveedores-table td,
    .provider-table td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .table-wrapper tbody tr,
    .proveedores-table tbody tr,
    .provider-table tbody tr {
        transition: background 0.15s ease;
    }

    .table-wrapper tbody tr:hover,
    .proveedores-table tbody tr:hover,
    .provider-table tbody tr:hover {
        background: #f8fafc;
    }

    .table-wrapper th:last-child,
    .table-wrapper td:last-child,
    .proveedores-table th:last-child,
    .proveedores-table td:last-child,
    .provider-table th:last-child,
    .provider-table td:last-child {
        width: 220px;
        min-width: 220px;
        text-align: center;
    }

    .acciones,
    .provider-row-actions,
    .proveedor-actions,
    .table-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .btn-accion,
    .btn-action,
    .btn-edit,
    .btn-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        min-width: 82px;
        padding: 6px 12px;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
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

        .proveedor-form,
        .provider-form,
        .create-provider-form,
        .proveedores-card form:not(.filter-form):not(.proveedores-filter):not(.proveedores-filters):not(.filters-panel),
        .filter-form,
        .proveedores-filter,
        .proveedores-filters,
        .filters-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }

    @media (max-width: 760px) {

        .proveedores-hero,
        .proveedores-card {
            padding: 20px;
            border-radius: 16px;
        }

        .section-heading,
        .proveedores-section-header,
        .provider-section-header {
            flex-direction: column;
        }

        .proveedor-form,
        .provider-form,
        .create-provider-form,
        .proveedores-card form:not(.filter-form):not(.proveedores-filter):not(.proveedores-filters):not(.filters-panel),
        .filter-form,
        .proveedores-filter,
        .proveedores-filters,
        .filters-panel {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .form-actions,
        .filter-actions,
        .provider-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline,
        .btn-secondary-inline,
        .btn-primary,
        .btn-secondary,
        .proveedores-card form button {
            width: 100%;
        }

        .table-wrapper table,
        .proveedores-table,
        .provider-table {
            min-width: 760px;
        }
    }
</style>