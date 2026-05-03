<style>
    .cliente-page-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .cliente-page-heading .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cliente-page-heading .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .cliente-page-heading .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
    }

    .cliente-detail-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .cliente-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 22px;
    }

    .cliente-label {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cliente-detail-header h2 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .cliente-type-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        margin-top: 10px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-size: 0.82rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .cliente-detail-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cliente-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .cliente-stat-card {
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .cliente-stat-card span {
        display: block;
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .cliente-stat-card strong {
        display: block;
        color: #111827;
        font-size: 1.15rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .cliente-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.35fr);
        gap: 18px;
        align-items: stretch;
    }

    .cliente-info-panel {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #ffffff;
    }

    .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .section-heading-between {
        align-items: center;
    }

    .section-heading h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .section-heading p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .cliente-info-list {
        display: grid;
        gap: 0;
    }

    .cliente-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .cliente-info-row:last-child {
        border-bottom: none;
    }

    .cliente-info-row span {
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .cliente-info-row strong {
        color: #111827;
        font-size: 0.95rem;
        font-weight: 900;
        line-height: 1.35;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .cliente-facturas-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    .cliente-facturas-table thead {
        background: #f3f4f6;
    }

    .cliente-facturas-table th {
        padding: 14px 16px;
        color: #667085;
        background: #f3f4f6;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .cliente-facturas-table td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
        white-space: nowrap;
    }

    .cliente-facturas-table tbody tr:hover {
        background: #f8fafc;
    }

    .cliente-facturas-table th:last-child,
    .cliente-facturas-table td:last-child {
        width: 170px;
        min-width: 170px;
        text-align: center;
    }

    .btn-primary-inline,
    .btn-secondary-inline,
    .btn-accion {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
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
        background: #ffffff;
        color: #374151;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-inline:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .btn-small-inline {
        min-height: 34px;
        padding: 0 12px;
        border-radius: 9px;
        font-size: 0.82rem;
    }

    .btn-accion {
        min-height: 32px;
        min-width: 92px;
        padding: 6px 12px;
        border-radius: 9px;
        font-size: 0.82rem;
        border: 1px solid transparent;
        box-shadow: none;
    }

    .btn-accion-ver {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .btn-accion-ver:hover {
        background: #bae6fd;
        border-color: #7dd3fc;
        transform: translateY(-1px);
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

    .cliente-muted {
        color: #6b7280;
        font-size: 0.88rem;
    }

    @media (max-width: 1100px) {
        .cliente-detail-grid {
            grid-template-columns: 1fr;
        }

        .cliente-stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 760px) {

        .cliente-page-heading,
        .cliente-detail-card {
            padding: 20px;
            border-radius: 16px;
        }

        .cliente-detail-header,
        .section-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .cliente-stats-grid {
            grid-template-columns: 1fr;
        }

        .cliente-info-panel {
            padding: 18px;
            border-radius: 14px;
        }

        .cliente-detail-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline,
        .btn-secondary-inline {
            width: 100%;
        }

        .cliente-facturas-table {
            min-width: 720px;
        }
    }
</style>