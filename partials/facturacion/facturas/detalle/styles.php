<style>
    .invoice-detail-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
    }

    .invoice-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .invoice-page-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .invoice-page-heading p:first-child,
    .invoice-page-heading .dashboard-eyebrow,
    .invoice-page-heading .invoice-label {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .invoice-page-heading h1 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .invoice-page-heading h1+p,
    .invoice-page-heading .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .invoice-detail-card {
        display: grid;
        gap: 22px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .invoice-summary-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 18px;
        align-items: stretch;
    }

    .invoice-main-panel,
    .invoice-client-panel,
    .invoice-payment-panel {
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        border-radius: 16px;
        padding: 18px;
    }

    .invoice-main-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .invoice-label {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .invoice-main-header h2 {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .invoice-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .invoice-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 6px 11px;
        border-radius: 999px;
        background: #ffffff;
        color: #374151;
        border: 1px solid #e5e7eb;
        font-size: 0.8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-badge-primary {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .invoice-info-grid,
    .invoice-payment-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .invoice-info-item,
    .invoice-payment-item {
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        padding: 13px;
    }

    .invoice-info-item span,
    .invoice-client-list span,
    .invoice-payment-item span {
        display: block;
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .invoice-info-item strong,
    .invoice-client-list strong,
    .invoice-payment-item strong {
        display: block;
        color: #111827;
        font-size: 0.95rem;
        font-weight: 900;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .invoice-info-total {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .invoice-info-total strong {
        color: #1d4ed8;
        font-size: 1.08rem;
    }

    .invoice-section-title {
        margin: 0 0 14px;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-client-list {
        display: grid;
        gap: 12px;
    }

    .invoice-products-section {
        background: #ffffff;
        border: none;
        border-radius: 0;
        padding: 0;
    }

    .invoice-section-header {
        margin-bottom: 14px;
    }

    .invoice-section-header-actions {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
    }

    .invoice-section-header h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .invoice-section-header p {
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

    .table-wrapper table,
    .invoice-products-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 840px;
    }

    .table-wrapper thead,
    .invoice-products-table thead {
        background: #f3f4f6;
    }

    .table-wrapper th,
    .invoice-products-table th {
        padding: 14px 16px;
        color: #667085;
        font-size: 0.86rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .table-wrapper td,
    .invoice-products-table td {
        padding: 14px 16px;
        color: #111827;
        font-size: 0.92rem;
        border-top: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .table-wrapper tbody tr,
    .invoice-products-table tbody tr {
        transition: background 0.15s ease;
    }

    .table-wrapper tbody tr:hover,
    .invoice-products-table tbody tr:hover {
        background: #f8fafc;
    }

    .invoice-products-table td strong {
        color: #111827;
        font-weight: 900;
    }

    .invoice-totals-section {
        display: flex;
        justify-content: flex-end;
        margin-top: -2px;
    }

    .invoice-totals-card {
        width: min(100%, 420px);
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        padding: 18px;
    }

    .invoice-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 10px 0;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
    }

    .invoice-total-row span {
        color: #6b7280;
        font-weight: 700;
    }

    .invoice-total-row strong {
        color: #111827;
        font-weight: 900;
    }

    .invoice-total-row:last-child {
        border-bottom: none;
    }

    .invoice-total-final {
        margin-top: 8px;
        padding-top: 16px;
        color: #111827;
        font-size: 1.15rem;
    }

    .invoice-total-final span {
        color: #111827;
        font-weight: 900;
    }

    .invoice-total-final strong {
        color: #1d4ed8;
        font-size: 1.3rem;
        font-weight: 900;
    }

    .invoice-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 0;
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

    .invoice-status-strip {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 18px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 6px 11px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 900;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .status-warning {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .status-info {
        background: #dbeafe;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .status-success {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .status-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .invoice-actions-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .invoice-progress-wrapper {
        margin-top: 16px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        padding: 14px;
    }

    .invoice-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .invoice-progress-header span {
        color: #6b7280;
        font-size: 0.86rem;
        font-weight: 800;
    }

    .invoice-progress-header strong {
        color: #111827;
        font-size: 0.9rem;
        font-weight: 900;
    }

    .invoice-progress-bar {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .invoice-progress-bar div {
        height: 100%;
        border-radius: inherit;
        background: #2563eb;
    }

    .invoice-history-overview {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .invoice-history-kpi {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 14px;
        padding: 16px;
    }

    .invoice-history-kpi span {
        display: block;
        margin-bottom: 8px;
        color: #6b7280;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .invoice-history-kpi strong {
        color: #111827;
        font-size: 1.35rem;
        font-weight: 900;
    }

    .invoice-history-kpi.danger strong {
        color: #991b1b;
    }

    .invoice-last-event {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        border-radius: 16px;
        padding: 18px;
    }

    .invoice-last-event span {
        display: block;
        color: #1d4ed8;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .invoice-last-event strong {
        display: block;
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .invoice-last-event p {
        margin: 0;
        color: #374151;
        line-height: 1.45;
    }

    .invoice-last-event time {
        color: #1d4ed8;
        font-weight: 900;
        white-space: nowrap;
    }

    .invoice-timeline-card {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 16px;
        padding: 18px;
    }

    .invoice-timeline {
        position: relative;
        display: grid;
        gap: 16px;
    }

    .invoice-timeline::before {
        content: "";
        position: absolute;
        top: 12px;
        bottom: 12px;
        left: 18px;
        width: 2px;
        background: #e5e7eb;
    }

    .invoice-timeline-item {
        position: relative;
        display: grid;
        grid-template-columns: 38px minmax(0, 1fr);
        gap: 14px;
    }

    .invoice-timeline-marker {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: center;
    }

    .invoice-timeline-marker span {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #2563eb;
        color: #ffffff;
        border: 4px solid #dbeafe;
        font-size: 0.78rem;
        font-weight: 900;
    }

    .invoice-timeline-item-success .invoice-timeline-marker span {
        background: #16a34a;
        border-color: #dcfce7;
    }

    .invoice-timeline-item-danger .invoice-timeline-marker span {
        background: #dc2626;
        border-color: #fee2e2;
    }

    .invoice-timeline-item-info .invoice-timeline-marker span {
        background: #2563eb;
        border-color: #dbeafe;
    }

    .invoice-timeline-content {
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .invoice-timeline-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .invoice-timeline-header strong {
        display: block;
        color: #111827;
        font-size: 0.98rem;
        font-weight: 900;
    }

    .invoice-timeline-header small {
        display: block;
        margin-top: 3px;
        color: #6b7280;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .invoice-timeline-header time {
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-timeline-comment {
        margin: 0 0 12px;
        color: #6b7280;
        line-height: 1.45;
    }

    .invoice-timeline-change-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .invoice-timeline-change {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 12px;
        padding: 12px;
    }

    .invoice-timeline-change>span {
        display: block;
        margin-bottom: 8px;
        color: #6b7280;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .invoice-timeline-change div {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .invoice-timeline-change strong {
        color: #94a3b8;
        font-weight: 900;
    }

    .invoice-empty-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 6px 11px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        font-size: 0.78rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .invoice-timeline-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .invoice-timeline-meta span {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: #374151;
        font-size: 0.78rem;
        font-weight: 800;
    }

    @media (max-width: 1100px) {

        .invoice-summary-grid,
        .invoice-info-grid,
        .invoice-payment-grid {
            grid-template-columns: 1fr 1fr;
        }

        .invoice-history-overview {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {

        .invoice-page-heading,
        .invoice-detail-card {
            padding: 20px;
            border-radius: 16px;
        }

        .invoice-detail-hero,
        .invoice-section-header-actions,
        .invoice-last-event {
            flex-direction: column;
            align-items: stretch;
        }

        .invoice-hero-actions {
            justify-content: stretch;
        }

        .invoice-hero-actions a {
            width: 100%;
        }

        .invoice-main-panel,
        .invoice-client-panel,
        .invoice-payment-panel {
            border-radius: 14px;
        }
    }

    @media (max-width: 700px) {

        .invoice-summary-grid,
        .invoice-info-grid,
        .invoice-payment-grid,
        .invoice-history-overview,
        .invoice-timeline-change-grid {
            grid-template-columns: 1fr;
        }

        .invoice-main-header,
        .invoice-actions,
        .invoice-timeline-header {
            flex-direction: column;
            align-items: stretch;
        }

        .invoice-badges {
            justify-content: flex-start;
        }

        .invoice-actions a,
        .invoice-actions button {
            width: 100%;
            text-align: center;
        }

        .invoice-actions-group {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .invoice-totals-section {
            justify-content: stretch;
        }

        .table-wrapper table,
        .invoice-products-table {
            min-width: 720px;
        }
    }

    .invoice-state-flow {
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        border-radius: 16px;
        padding: 18px;
    }

    .invoice-flow-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .invoice-flow-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 16px;
    }

    .invoice-flow-card h4 {
        margin: 0 0 12px;
        color: #111827;
        font-size: 0.95rem;
        font-weight: 900;
    }

    .invoice-flow-step {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        color: #94a3b8;
        font-weight: 800;
    }

    .invoice-flow-step span {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #e5e7eb;
        box-shadow: 0 0 0 4px #f3f4f6;
    }

    .invoice-flow-step.active {
        color: #111827;
    }

    .invoice-flow-step.active span {
        background: #2563eb;
        box-shadow: 0 0 0 4px #dbeafe;
    }

    .invoice-generated-note {
        margin: 0 0 14px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
        font-size: 0.9rem;
        line-height: 1.45;
    }

    @media (max-width: 700px) {
        .invoice-flow-grid {
            grid-template-columns: 1fr;
        }
    }

    .invoice-abono-panel {
        margin-top: 20px;
        padding: 24px;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #86efac;
        border-radius: 14px;
    }

    .invoice-abono-panel .invoice-section-header h3 {
        margin: 0 0 4px 0;
        font-size: 1rem;
        font-weight: 700;
        color: #166534;
    }

    .invoice-abono-panel .invoice-section-header p {
        margin: 0;
        color: #166534;
        font-size: 0.88rem;
    }

    .abono-form {
        margin-top: 16px;
    }

    .abono-fields {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .abono-fields .form-group {
        flex: 1;
        min-width: 200px;
    }

    .abono-fields .label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 4px;
    }

    .abono-fields .input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.9rem;
        background: #fff;
    }

    .abono-fields .input:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
    }

    .abono-info {
        margin-bottom: 12px;
    }

    .abono-hint {
        margin: 0;
        font-size: 0.85rem;
        color: #92400e;
        padding: 8px 12px;
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 8px;
    }

    .abono-hint-success {
        color: #166534;
        background: #dcfce7;
        border-color: #86efac;
    }
</style>