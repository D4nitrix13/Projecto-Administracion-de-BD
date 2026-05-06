<style>
    .facturas-hero {
        margin-bottom: 22px;
    }

    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
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

    .facturas-actions {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .dashboard-card>form {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        align-items: end;
        gap: 16px;
        padding: 18px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .dashboard-card>form label {
        display: grid;
        gap: 7px;
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .dashboard-card>form label:first-child {
        grid-column: auto;
    }

    .dashboard-card>form input,
    .dashboard-card>form select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease,
            background 0.15s ease;
    }

    .dashboard-card>form input::placeholder {
        color: #8b95a5;
    }

    .dashboard-card>form input:focus,
    .dashboard-card>form select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }

    .dashboard-card>form button,
    .dashboard-card>form a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        min-width: 112px;
        height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.94rem;
        text-decoration: none;
        cursor: pointer;
        align-self: end;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .dashboard-card>form button {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .dashboard-card>form button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .dashboard-card>form a {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #374151;
    }

    .dashboard-card>form a:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .filters-panel {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        align-items: end;
        gap: 16px;
        padding: 18px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .filter-field {
        display: grid;
        gap: 7px;
    }

    .filter-field-full {
        grid-column: 1 / -1;
    }

    .filter-field label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .filter-field input,
    .filter-field select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease,
            background 0.15s ease;
    }

    .filter-field input::placeholder {
        color: #8b95a5;
    }

    .filter-field input:focus,
    .filter-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }

    .filter-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 2px;
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
        text-decoration: none;
        cursor: pointer;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .btn-primary-inline {
        border: none;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .btn-primary-inline:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary-inline {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #374151;
    }

    .btn-secondary-inline:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 14px;
    }

    .section-heading h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .section-heading p {
        margin: 0;
        color: #6b7280;
        line-height: 1.45;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .table-wrapper table {
        width: 100%;
        border-collapse: collapse;
        min-width: 920px;
    }

    .table-wrapper thead {
        background: #f3f4f6;
    }

    .table-wrapper th {
        padding: 14px 16px;
        color: #667085;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .table-wrapper tbody tr:hover {
        background: #f8fafc;
    }

    .table-wrapper th.col-acciones,
    .table-wrapper td.acciones,
    .table-wrapper th:last-child,
    .table-wrapper td:last-child {
        text-align: center;
    }

    .table-wrapper th:last-child {
        width: 210px;
    }

    .table-wrapper td:last-child {
        vertical-align: middle;
    }

    .col-acciones {
        text-align: center;
    }

    .acciones {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
        text-align: center;
    }

    .btn-accion {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
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

    .btn-accion:hover {
        transform: translateY(-1px);
    }

    .btn-accion-ver,
    .btn-accion-editar {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .btn-accion-ver:hover,
    .btn-accion-editar:hover {
        background: #bae6fd;
        border-color: #7dd3fc;
    }

    .btn-accion-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .btn-accion-danger:hover {
        background: #fecaca;
        border-color: #fca5a5;
    }

    .empty-message {
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

        .dashboard-card>form,
        .filters-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .dashboard-card {
            padding: 20px;
            border-radius: 16px;
        }

        .section-heading {
            flex-direction: column;
        }

        .facturas-actions {
            justify-content: stretch;
        }

        .facturas-actions .btn-primary-inline,
        .facturas-actions .btn-secondary-inline {
            width: 100%;
        }
    }

    @media (max-width: 640px) {

        .dashboard-card>form,
        .filters-panel {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .filter-actions {
            justify-content: stretch;
            flex-direction: column;
        }

        .btn-primary-inline,
        .btn-secondary-inline,
        .dashboard-card>form button,
        .dashboard-card>form a {
            width: 100%;
        }

        .table-wrapper table {
            min-width: 780px;
        }
    }

    .btn-accion-editar {
        background: #eef2ff;
        color: #4338ca;
        border-color: #c7d2fe;
    }

    .btn-accion-editar:hover {
        background: #c7d2fe;
        border-color: #a5b4fc;
        color: #3730a3;
    }
</style>