<style>
    .mantenimiento-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .mantenimiento-page-heading h1 {
        margin: 0 0 12px;
    }

    .mantenimiento-page-heading p {
        margin: 0;
    }

    .mantenimiento-eyebrow {
        margin: 0 0 12px;
        color: #111827;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    .mantenimiento-secondary-button,
    .mantenimiento-primary-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        border-radius: 10px;
        padding: 0 16px;
        font-size: 0.88rem;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }

    .mantenimiento-secondary-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .mantenimiento-secondary-button:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .mantenimiento-primary-button {
        width: 100%;
        border: 0;
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .mantenimiento-primary-button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .mantenimiento-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 24px;
    }

    .mantenimiento-summary-card,
    .mantenimiento-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .mantenimiento-summary-card {
        display: grid;
        gap: 7px;
    }

    .mantenimiento-summary-card span {
        color: #667085;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
    }

    .mantenimiento-summary-card strong {
        color: #111827;
        font-size: 1.45rem;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.03em;
    }

    .mantenimiento-summary-card small {
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.4;
    }

    .mantenimiento-summary-card-blue {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .mantenimiento-summary-card-blue span,
    .mantenimiento-summary-card-blue small {
        color: #1e40af;
    }

    .mantenimiento-summary-card-blue strong {
        color: #1d4ed8;
    }

    .mantenimiento-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(340px, 0.75fr);
        gap: 28px;
        align-items: start;
    }

    .mantenimiento-side {
        display: grid;
        gap: 22px;
    }

    .mantenimiento-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .mantenimiento-card-header h2 {
        margin: 10px 0 8px;
        color: #111827;
        font-size: 1.2rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .mantenimiento-card-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .mantenimiento-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 26px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .mantenimiento-badge-info {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .mantenimiento-badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .mantenimiento-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .mantenimiento-action-card {
        display: grid;
        gap: 16px;
        align-content: space-between;
        min-height: 210px;
        margin: 0;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .mantenimiento-action-card-blue {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .mantenimiento-action-card span {
        display: block;
        margin-bottom: 8px;
        color: #2563eb;
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.07em;
    }

    .mantenimiento-action-card h3 {
        margin: 0 0 8px;
        color: #111827;
        font-size: 1.08rem;
        font-weight: 900;
    }

    .mantenimiento-action-card p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .mantenimiento-info-list,
    .mantenimiento-frequency-list {
        display: grid;
        gap: 12px;
    }

    .mantenimiento-info-list div,
    .mantenimiento-frequency-list div {
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .mantenimiento-info-list span,
    .mantenimiento-frequency-list span {
        display: block;
        margin-bottom: 6px;
        color: #667085;
        font-size: 0.76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .mantenimiento-info-list strong,
    .mantenimiento-frequency-list strong {
        display: block;
        color: #111827;
        font-size: 0.92rem;
        font-weight: 900;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .mantenimiento-info-list small {
        display: block;
        margin-top: 5px;
        color: #64748b;
        line-height: 1.4;
    }

    .mantenimiento-card-warning {
        border-color: #fde68a;
        background: #fffdf5;
    }

    .mantenimiento-alert {
        width: 100%;
        margin-bottom: 18px;
        border-radius: 14px;
        padding: 14px 18px;
        border: 1px solid transparent;
    }

    .mantenimiento-alert-content {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .mantenimiento-alert-title {
        display: block;
        margin: 0;
        font-size: 0.96rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .mantenimiento-alert-text {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 500;
        line-height: 1.5;
        white-space: normal;
    }

    .mantenimiento-alert-error {
        color: #991b1b;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .mantenimiento-alert-success {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    @media (max-width: 1200px) {
        .mantenimiento-layout {
            grid-template-columns: 1fr;
        }

        .mantenimiento-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {

        .mantenimiento-page-heading,
        .mantenimiento-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .mantenimiento-summary-grid,
        .mantenimiento-actions-grid {
            grid-template-columns: 1fr;
        }

        .mantenimiento-secondary-button,
        .mantenimiento-primary-button {
            width: 100%;
        }

        .mantenimiento-summary-card,
        .mantenimiento-card {
            padding: 20px;
        }
    }
</style>