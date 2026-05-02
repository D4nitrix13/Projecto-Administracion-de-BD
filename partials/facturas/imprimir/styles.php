<style>
    .invoice-print-body {
        background: #f3f5f9;
    }

    .invoice-print-main {
        margin-left: 306px;
        padding: 36px 42px 60px;
    }

    .invoice-toolbar {
        max-width: 14cm;
        margin: 0 auto 18px;
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .invoice-toolbar h1 {
        margin: 0 0 4px;
        color: #111827;
        font-size: 1.35rem;
    }

    .invoice-toolbar p {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
    }

    .invoice-toolbar-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .invoice-paper {
        width: 14cm;
        min-height: 21.5cm;
        margin: 0 auto;
        background: #ffffff;
        padding: 1cm;
        box-sizing: border-box;
        border-radius: 4px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.15);
        color: #111827;
        font-family: Arial, sans-serif;
    }

    .invoice-paper-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding-bottom: 14px;
        border-bottom: 2px solid #111827;
        margin-bottom: 16px;
    }

    .invoice-paper-header h2 {
        margin: 0 0 5px;
        font-size: 1.05rem;
        color: #111827;
    }

    .invoice-paper-header p {
        margin: 2px 0;
        color: #4b5563;
        font-size: 0.82rem;
    }

    .invoice-paper-meta {
        text-align: right;
    }

    .invoice-paper-meta span {
        display: block;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.72rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .invoice-paper-meta strong {
        display: block;
        font-size: 1.45rem;
        color: #111827;
        margin-bottom: 4px;
    }

    .invoice-info-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 10px;
        margin-bottom: 16px;
    }

    .invoice-info-box {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
    }

    .invoice-info-box h3,
    .invoice-products h3 {
        margin: 0 0 8px;
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .invoice-info-row {
        display: grid;
        grid-template-columns: 90px minmax(0, 1fr);
        gap: 8px;
        padding: 4px 0;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.8rem;
    }

    .invoice-info-row:last-child {
        border-bottom: none;
    }

    .invoice-info-row span {
        color: #6b7280;
        font-weight: 700;
    }

    .invoice-info-row strong {
        color: #111827;
        font-weight: 700;
    }

    .invoice-products {
        margin-bottom: 14px;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }

    .invoice-table th {
        background: #f3f4f6;
        color: #374151;
        text-align: left;
        padding: 7px 6px;
        border: 1px solid #d1d5db;
        font-size: 0.72rem;
    }

    .invoice-table td {
        padding: 7px 6px;
        border: 1px solid #e5e7eb;
        vertical-align: top;
    }

    .invoice-table small {
        display: block;
        color: #6b7280;
        margin-top: 2px;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .empty-row {
        text-align: center;
        color: #6b7280;
        padding: 16px;
    }

    .invoice-footer-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 6.2cm;
        gap: 14px;
        align-items: start;
        margin-top: 12px;
    }

    .invoice-note {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        font-size: 0.78rem;
        color: #6b7280;
    }

    .invoice-note strong {
        display: block;
        color: #111827;
        margin-bottom: 4px;
    }

    .invoice-note p {
        margin: 0;
        line-height: 1.4;
    }

    .invoice-totals {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        font-size: 0.82rem;
    }

    .invoice-totals div {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 6px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .invoice-totals div:last-child {
        border-bottom: none;
    }

    .invoice-total-final {
        margin-top: 4px;
        padding-top: 10px !important;
        border-top: 2px solid #111827;
        font-size: 0.95rem;
        font-weight: 800;
    }

    @media (max-width: 1000px) {
        .invoice-print-main {
            margin-left: 0;
            padding: 24px;
        }

        .invoice-toolbar,
        .invoice-paper {
            max-width: 100%;
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
            height: 21.5cm;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        .sidebar,
        .topbar,
        .invoice-toolbar {
            display: none !important;
        }

        .invoice-print-main {
            margin: 0 !important;
            padding: 0 !important;
        }

        .invoice-paper {
            width: 14cm !important;
            min-height: 21.5cm !important;
            margin: 0 !important;
            padding: 1cm !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
    }
</style>