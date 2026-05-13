<style>
    .audit-hero {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 30px;
        margin-bottom: 24px;
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.18), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 58%, #eef2ff 100%);
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
    }

    .audit-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.65fr);
        gap: 24px;
        align-items: center;
    }

    .audit-kicker {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 30px;
        padding: 0 12px;
        margin-bottom: 14px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .audit-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.8rem, 3vw, 2.5rem);
        font-weight: 950;
        line-height: 1.05;
        letter-spacing: -0.055em;
    }

    .audit-description {
        max-width: 780px;
        margin: 14px 0 0;
        color: #4b5563;
        font-size: 1rem;
        line-height: 1.6;
    }

    .audit-hero-panel {
        padding: 20px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(203, 213, 225, 0.9);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.07);
    }

    .audit-hero-panel strong {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 1.05rem;
        font-weight: 900;
    }

    .audit-hero-panel p {
        margin: 0;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .audit-summary-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .audit-summary-card {
        position: relative;
        overflow: hidden;
        min-height: 118px;
        padding: 20px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .audit-summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.09);
        border-color: #cbd5e1;
    }

    .audit-summary-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: #2563eb;
    }

    .audit-summary-card.products::before {
        background: #7c3aed;
    }

    .audit-summary-card.clients::before {
        background: #0891b2;
    }

    .audit-summary-card.categories::before {
        background: #d97706;
    }

    .audit-summary-card.providers::before {
        background: #16a34a;
    }

    .audit-summary-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .audit-summary-top span {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .audit-summary-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 1rem;
        font-weight: 900;
    }

    .audit-summary-card strong {
        display: block;
        color: #111827;
        font-size: 2rem;
        font-weight: 950;
        letter-spacing: -0.04em;
    }

    .audit-summary-card small {
        display: block;
        margin-top: 6px;
        color: #94a3b8;
        font-weight: 700;
    }

    .audit-alert {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 18px;
        font-weight: 800;
        line-height: 1.45;
    }

    .audit-alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .audit-alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .audit-note {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 24px;
        padding: 16px 18px;
        border-radius: 18px;
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        line-height: 1.5;
        font-weight: 750;
    }

    .audit-note-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: #dbeafe;
        font-weight: 900;
    }

    .audit-filters-card {
        margin-bottom: 24px;
        padding: 22px;
        border-radius: 22px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
    }

    .audit-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 18px;
    }

    .audit-section-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 1.28rem;
        font-weight: 950;
        letter-spacing: -0.035em;
    }

    .audit-section-heading p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .audit-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(170px, 0.5fr));
        gap: 14px;
        align-items: end;
    }

    .audit-field {
        display: grid;
        gap: 7px;
    }

    .audit-field label {
        color: #111827;
        font-size: 0.84rem;
        font-weight: 900;
    }

    .audit-field input,
    .audit-field select {
        width: 100%;
        min-height: 44px;
        padding: 10px 13px;
        border: 1px solid #cbd5e1;
        border-radius: 13px;
        background: #f8fafc;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
        transition: background 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .audit-field input:focus,
    .audit-field select:focus {
        background: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .audit-filter-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: wrap;
    }

    .audit-btn-primary,
    .audit-btn-secondary,
    .audit-btn-success,
    .audit-btn-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 15px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 900;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: transform 0.15s ease, background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .audit-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
    }

    .audit-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .audit-btn-secondary {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    .audit-btn-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .audit-records-card {
        padding: 22px;
        border-radius: 22px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
    }

    .audit-record-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .audit-record {
        position: relative;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .audit-record:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.08);
    }

    .audit-record-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 18px 14px;
        border-bottom: 1px solid #e5e7eb;
        background: rgba(248, 250, 252, 0.72);
    }

    .audit-record-title {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        min-width: 0;
    }

    .audit-record-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #fee2e2;
        color: #991b1b;
        font-weight: 950;
    }

    .audit-record-title h3 {
        margin: 0;
        color: #111827;
        font-size: 1.04rem;
        font-weight: 950;
        letter-spacing: -0.025em;
    }

    .audit-record-title p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .audit-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 950;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .audit-badge-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .audit-badge-primary {
        background: #eef2ff;
        color: #4338ca;
        border-color: #c7d2fe;
    }

    .audit-record-body {
        padding: 18px;
    }

    .audit-data-list {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    .audit-data-row {
        display: grid;
        grid-template-columns: 125px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        padding-bottom: 10px;
        border-bottom: 1px dashed #e5e7eb;
    }

    .audit-data-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .audit-data-row span {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 900;
    }

    .audit-data-row strong {
        color: #111827;
        font-size: 0.9rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .audit-technical {
        margin-top: 14px;
    }

    .audit-technical summary {
        cursor: pointer;
        color: #2563eb;
        font-weight: 900;
        font-size: 0.86rem;
        list-style: none;
    }

    .audit-technical summary::-webkit-details-marker {
        display: none;
    }

    .audit-json {
        max-height: 220px;
        overflow: auto;
        margin: 12px 0 0;
        padding: 14px;
        border-radius: 14px;
        background: #0f172a;
        color: #e5e7eb;
        font-size: 0.78rem;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    .audit-record-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 18px 18px;
        border-top: 1px solid #e5e7eb;
    }

    .audit-record-meta {
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 750;
        line-height: 1.4;
    }

    .audit-actions {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }

    .audit-action-form {
        display: inline-flex;
        margin: 0;
    }

    .audit-btn-success {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .audit-btn-success:hover {
        background: #bbf7d0;
        transform: translateY(-1px);
    }

    .audit-btn-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .audit-btn-danger:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }

    .audit-empty {
        padding: 46px 22px;
        border-radius: 20px;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
    }

    .audit-empty strong {
        display: block;
        margin-bottom: 8px;
        color: #111827;
        font-size: 1.1rem;
        font-weight: 950;
    }

    .audit-empty p {
        margin: 0;
        color: #64748b;
        line-height: 1.5;
    }

    @media (max-width: 1200px) {
        .audit-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .audit-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .audit-record-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .audit-hero {
            padding: 22px;
            border-radius: 20px;
        }

        .audit-hero-grid {
            grid-template-columns: 1fr;
        }

        .audit-summary-grid,
        .audit-filter-grid {
            grid-template-columns: 1fr;
        }

        .audit-filter-actions,
        .audit-record-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .audit-actions,
        .audit-action-form,
        .audit-btn-success,
        .audit-btn-danger,
        .audit-btn-primary,
        .audit-btn-secondary {
            width: 100%;
        }

        .audit-data-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }

    .audit-hero-simple {
        padding: 28px 30px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .audit-simple-section {
        display: block;
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .audit-hero-simple .audit-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.6rem, 2vw, 2rem);
        font-weight: 900;
        letter-spacing: -0.035em;
    }

    .audit-hero-simple .audit-description {
        max-width: 760px;
        margin-top: 12px;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
    }

    .audit-hero-simple::before,
    .audit-hero-simple::after {
        display: none;
    }

    .audit-summary-icon {
        display: none;
    }

    .audit-record-grid {
        align-items: start;
    }

    .audit-record {
        height: auto;
        align-self: start;
    }

    .audit-record-body {
        padding-bottom: 14px;
    }

    .audit-record-footer {
        margin-top: 0;
    }

    .audit-data-list {
        margin-bottom: 10px;
    }

    .audit-technical {
        margin-top: 8px;
    }

    .audit-json {
        max-height: 180px;
    }
</style>