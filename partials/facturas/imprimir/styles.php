<style>
    .invoice-print-body {
        background: #eef2f7;
    }

    .invoice-print-main {
        min-height: 100vh;
        padding: 32px 36px 60px;
    }

    .invoice-toolbar {
        width: min(100%, 14cm);
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
        line-height: 1.12;
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

    .invoice-paper {
        width: 14cm;
        min-height: 21.5cm;
        margin: 0 auto;
        padding: 0.95cm;
        box-sizing: border-box;
        background: #ffffff;
        color: #111827;
        border-radius: 8px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.14);
        font-family: Arial, sans-serif;
    }

    .invoice-paper-header {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
        align-items: start;
        padding-bottom: 14px;
        margin-bottom: 16px;
        border-bottom: 2px solid #111827;
    }

    .invoice-paper-header h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.1rem;
        font-weight: 900;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .invoice-paper-header p {
        margin: 2px 0;
        color: #4b5563;
        font-size: 0.8rem;
        line-height: 1.35;
    }

    .invoice-paper-meta {
        min-width: 3.2cm;
        text-align: right;
    }

    .invoice-paper-meta span {
        display: block;
        margin-bottom: 4px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.72rem;
        font-weight: 900;
    }

    .invoice-paper-meta strong {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 1.45rem;
        font-weight: 900;
        line-height: 1;
    }

    .invoice-paper-meta p {
        margin: 0;
        color: #4b5563;
        font-size: 0.8rem;
        line-height: 1.35;
    }

    .invoice-info-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }

    .invoice-info-box {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        background: #ffffff;
    }

    .invoice-info-box h3,
    .invoice-products h3 {
        margin: 0 0 8px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.74rem;
        font-weight: 900;
    }

    .invoice-info-row {
        display: grid;
        grid-template-columns: 90px minmax(0, 1fr);
        gap: 8px;
        padding: 5px 0;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        font-size: 0.8rem;
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
        margin-bottom: 14px;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 0.78rem;
    }

    .invoice-table th {
        padding: 7px 6px;
        border: 1px solid #d1d5db;
        background: #f3f4f6;
        color: #374151;
        text-align: left;
        font-size: 0.72rem;
        font-weight: 900;
        vertical-align: middle;
    }

    .invoice-table td {
        padding: 7px 6px;
        border: 1px solid #e5e7eb;
        color: #111827;
        vertical-align: top;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .invoice-table th:nth-child(1),
    .invoice-table td:nth-child(1) {
        width: 38%;
    }

    .invoice-table th:nth-child(2),
    .invoice-table td:nth-child(2) {
        width: 12%;
        text-align: center;
    }

    .invoice-table th:nth-child(3),
    .invoice-table td:nth-child(3),
    .invoice-table th:nth-child(4),
    .invoice-table td:nth-child(4),
    .invoice-table th:nth-child(5),
    .invoice-table td:nth-child(5) {
        width: 16.6%;
    }

    .invoice-table small {
        display: block;
        margin-top: 2px;
        color: #6b7280;
        font-size: 0.72rem;
        line-height: 1.3;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .empty-row {
        padding: 16px;
        text-align: center;
        color: #6b7280;
    }

    .invoice-footer-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 6.4cm;
        gap: 14px;
        align-items: start;
        margin-top: 12px;
    }

    .invoice-note {
        min-height: 2.45cm;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        background: #ffffff;
        color: #6b7280;
        font-size: 0.78rem;
        line-height: 1.45;
    }

    .invoice-note strong {
        display: block;
        margin-bottom: 5px;
        color: #111827;
        font-weight: 900;
    }

    .invoice-note p {
        margin: 0;
    }

    .invoice-totals {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        background: #ffffff;
        font-size: 0.82rem;
    }

    .invoice-totals div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 6px 0;
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
        margin-top: 4px;
        padding-top: 10px !important;
        border-top: 2px solid #111827;
        color: #111827;
        font-size: 0.96rem;
        font-weight: 900;
    }

    .invoice-total-final span {
        color: #111827;
        font-weight: 900;
    }

    .invoice-total-final strong {
        color: #1d4ed8;
        font-size: 1rem;
    }

    @media (max-width: 1000px) {
        .invoice-print-main {
            margin-left: 0;
            padding: 24px 18px 60px;
        }

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
            gap: 2px;
        }

        .invoice-totals {
            width: 100%;
        }
    }

    @page {
        size: 14cm 21.5cm;
        margin: 0;
    }

    @media print {

        html,
        body {
            width: 14cm;
            min-height: 21.5cm;
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
            width: 14cm !important;
            min-height: 21.5cm !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .invoice-paper {
            width: 14cm !important;
            min-height: 21.5cm !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0.95cm !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .invoice-paper-header {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .invoice-info-box,
        .invoice-note,
        .invoice-totals,
        .invoice-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>