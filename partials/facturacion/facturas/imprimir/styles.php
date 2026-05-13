<style>
    .invoice-print-body {
        background: #eef2f7;
    }

    .invoice-print-main {
        min-height: 100vh;
        padding: 20px;
    }

    .invoice-toolbar {
        width: min(100%, 21cm);
        margin: 0 auto 18px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .invoice-toolbar h1 {
        margin: 0 0 8px;
        color: #111827;
        font-size: 1.55rem;
        font-weight: 900;
        letter-spacing: -0.035em;
    }

    .invoice-toolbar p {
        margin: 0;
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.45;
    }

    .invoice-toolbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
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
        cursor: pointer;
        transition: 0.15s ease;
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
        transform: translateY(-1px);
    }

    .invoice-paper {
        width: 21cm;
        min-height: 29.7cm;
        margin: 0 auto;
        padding: 1.35cm;
        box-sizing: border-box;
        background: linear-gradient(to bottom, #ffffff, #fcfcfd);
        color: #111827;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.14);
        font-family: Arial, sans-serif;
    }

    .invoice-paper-header {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 24px;
        align-items: start;
        padding-bottom: 18px;
        margin-bottom: 18px;
        border-bottom: 3px solid #111827;
    }

    .invoice-paper-header h2 {
        margin: 0 0 8px;
        color: #111827;
        font-size: 1.55rem;
        font-weight: 900;
        letter-spacing: -0.03em;
        line-height: 1.15;
    }

    .invoice-paper-header p {
        margin: 3px 0;
        color: #4b5563;
        font-size: 0.92rem;
        line-height: 1.35;
    }

    .invoice-paper-meta {
        min-width: 4cm;
        text-align: right;
    }

    .invoice-paper-meta span {
        display: block;
        margin-bottom: 5px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.78rem;
        font-weight: 900;
    }

    .invoice-paper-meta strong {
        display: block;
        margin-bottom: 8px;
        color: #111827;
        font-size: 2rem;
        font-weight: 900;
        line-height: 1;
    }

    .invoice-paper-meta p {
        margin: 0;
        color: #4b5563;
        font-size: 0.92rem;
    }

    .invoice-status-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .print-status {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 900;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #374151;
    }

    .print-status-payment {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .print-status-production {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .invoice-info-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .invoice-info-box {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 14px;
        background: #ffffff;
    }

    .invoice-info-box h3,
    .invoice-products h3 {
        margin: 0 0 10px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.8rem;
        font-weight: 900;
    }

    .invoice-info-row {
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr);
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        font-size: 0.92rem;
        line-height: 1.35;
    }

    .invoice-info-row:last-child {
        border-bottom: none;
    }

    .invoice-info-row span {
        color: #6b7280;
        font-weight: 800;
    }

    .invoice-info-row strong {
        color: #111827;
        font-weight: 900;
        overflow-wrap: anywhere;
    }

    .invoice-products {
        margin-bottom: 18px;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 0.9rem;
    }

    .invoice-table th {
        padding: 10px 8px;
        border: 1px solid #d1d5db;
        background: #f3f4f6;
        color: #374151;
        text-align: left;
        font-size: 0.82rem;
        font-weight: 900;
        vertical-align: middle;
    }

    .invoice-table td {
        padding: 10px 8px;
        border: 1px solid #e5e7eb;
        color: #111827;
        vertical-align: top;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .invoice-table th:nth-child(1),
    .invoice-table td:nth-child(1) {
        width: 42%;
    }

    .invoice-table th:nth-child(2),
    .invoice-table td:nth-child(2) {
        width: 10%;
        text-align: center;
    }

    .invoice-table th:nth-child(3),
    .invoice-table td:nth-child(3),
    .invoice-table th:nth-child(4),
    .invoice-table td:nth-child(4),
    .invoice-table th:nth-child(5),
    .invoice-table td:nth-child(5) {
        width: 16%;
    }

    .invoice-table small {
        display: block;
        margin-top: 3px;
        color: #6b7280;
        font-size: 0.78rem;
        line-height: 1.3;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .empty-row {
        padding: 18px;
        text-align: center;
        color: #6b7280;
    }

    .invoice-footer-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 7.4cm;
        gap: 18px;
        align-items: start;
        margin-top: 16px;
    }

    .invoice-note {
        min-height: 3cm;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px;
        background: #ffffff;
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .invoice-note strong {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-weight: 900;
    }

    .invoice-note p {
        margin: 0;
    }

    .invoice-totals {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 14px;
        background: #ffffff;
        font-size: 0.92rem;
    }

    .invoice-totals div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .invoice-totals div:last-child {
        border-bottom: none;
    }

    .invoice-totals span {
        color: #6b7280;
        font-weight: 800;
    }

    .invoice-totals strong {
        color: #111827;
        font-weight: 900;
        text-align: right;
        white-space: nowrap;
    }

    .invoice-total-final {
        margin-top: 6px;
        padding-top: 14px !important;
        border-top: 3px solid #111827;
        color: #111827;
        font-size: 1.08rem;
        font-weight: 900;
    }

    .invoice-total-final span {
        color: #111827;
        font-weight: 900;
    }

    .invoice-total-final strong {
        color: #1d4ed8;
        font-size: 1.35rem;
    }

    @media (max-width: 1000px) {

        .invoice-toolbar,
        .invoice-paper {
            width: 100%;
            max-width: 100%;
        }

        .invoice-paper {
            min-height: auto;
        }
    }

    @media (max-width: 760px) {
        .invoice-toolbar {
            flex-direction: column;
            align-items: stretch;
            border-radius: 16px;
        }

        .invoice-toolbar-actions {
            justify-content: stretch;
        }

        .invoice-toolbar-actions a,
        .invoice-toolbar-actions button {
            width: 100%;
        }

        .invoice-paper {
            padding: 18px;
        }

        .invoice-paper-header,
        .invoice-info-grid,
        .invoice-footer-grid {
            grid-template-columns: 1fr;
        }

        .invoice-paper-meta {
            text-align: left;
        }

        .invoice-info-row {
            grid-template-columns: 1fr;
            gap: 3px;
        }
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {

        html,
        body {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sidebar,
        .dashboard-topbar,
        .topbar,
        .invoice-toolbar,
        .sidebar-toggle-floating {
            display: none !important;
        }

        .dashboard-main,
        .invoice-print-main {
            width: 21cm !important;
            min-height: 29.7cm !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .invoice-paper {
            width: 21cm !important;
            min-height: 29.7cm !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 1.35cm !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .invoice-paper-header,
        .invoice-info-box,
        .invoice-note,
        .invoice-totals,
        .invoice-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>