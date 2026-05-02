<style>
    .compras-hero {
        margin-bottom: 22px;
    }

    .dashboard-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        margin-bottom: 24px;
    }

    .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: 1.9rem;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 1rem;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        font-weight: 700;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .filters-panel {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        padding: 16px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .filter-field {
        display: grid;
        gap: 6px;
    }

    .filter-field-full {
        grid-column: 1 / -1;
    }

    .filter-field label {
        color: #111827;
        font-size: 0.9rem;
        font-weight: 800;
    }

    .filter-field input,
    .filter-field select {
        width: 100%;
        min-height: 40px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
    }

    .filter-field input:focus,
    .filter-field select:focus {
        border-color: #111827;
        box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.1);
    }

    .filter-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-primary-inline,
    .btn-secondary-inline {
        min-height: 40px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-primary-inline {
        border: none;
        background: #2563eb;
        color: #ffffff;
    }

    .btn-primary-inline:hover {
        background: #1d4ed8;
    }

    .btn-secondary-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #374151;
    }

    .btn-secondary-inline:hover {
        background: #f3f4f6;
    }

    .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 12px;
    }

    .section-heading h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
    }

    .section-heading p {
        margin: 0;
        color: #6b7280;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .col-acciones {
        text-align: center;
    }

    .acciones {
        text-align: center;
        white-space: nowrap;
    }

    .btn-accion {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
    }

    .btn-accion-editar {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .btn-accion-editar:hover {
        background: #bae6fd;
    }

    .empty-message {
        margin: 0;
        padding: 28px 16px;
        color: #6b7280;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
    }

    @media (max-width: 1100px) {
        .filters-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .filters-panel {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            justify-content: stretch;
            flex-direction: column;
        }

        .btn-primary-inline,
        .btn-secondary-inline {
            width: 100%;
        }
    }
</style>