<style>
    .catalog-public-body {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 32%),
            linear-gradient(180deg, #f3f6fb 0%, #eef2f7 100%);
        color: #111827;
    }

    .catalog-navbar {
        position: sticky;
        top: 0;
        z-index: 30;
        width: 100%;
        display: flex;
        justify-content: center;
        background: #243247;
        color: #e5e7eb;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
    }

    .catalog-navbar-inner {
        width: 100%;
        max-width: 1180px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .catalog-brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
        text-decoration: none;
        font-weight: 800;
        letter-spacing: 0.01em;
    }

    .catalog-brand-logo {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        object-fit: cover;
        background: #ffffff;
    }

    .catalog-brand-separator {
        opacity: 0.55;
    }

    .catalog-navbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .catalog-session-label {
        color: #cbd5e1;
        font-size: 0.86rem;
        font-weight: 600;
    }

    .catalog-navbar-btn {
        min-height: 40px;
        padding: 9px 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #2563eb;
        color: #ffffff;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 800;
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.24);
    }

    .catalog-navbar-btn:hover {
        background: #1d4ed8;
    }

    .catalog-container {
        max-width: 1180px;
        margin: 0 auto;
        padding: 28px 18px 48px;
    }

    .catalog-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 24px;
        align-items: center;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
    }

    .catalog-eyebrow {
        margin: 0 0 8px;
        color: #94a3b8;
        font-size: 0.78rem;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .catalog-title {
        margin: 0 0 12px;
        color: #111827;
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.05;
        letter-spacing: -0.045em;
    }

    .catalog-description {
        max-width: 760px;
        margin: 0;
        color: #64748b;
        line-height: 1.65;
        font-size: 0.98rem;
    }

    .catalog-hero-card {
        border: 1px solid #dbeafe;
        border-radius: 18px;
        padding: 18px;
        background: linear-gradient(135deg, #eff6ff, #ffffff);
        box-shadow: 0 12px 30px rgba(37, 99, 235, 0.08);
    }

    .catalog-hero-card-label {
        display: inline-flex;
        width: fit-content;
        margin-bottom: 10px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .catalog-hero-card strong {
        display: block;
        color: #111827;
        font-size: 1rem;
        margin-bottom: 6px;
    }

    .catalog-hero-card p {
        margin: 0;
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.45;
    }

    .catalog-panel {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
    }

    .catalog-filter-bar {
        display: grid;
        grid-template-columns: minmax(260px, 1.4fr) minmax(180px, 0.8fr) minmax(180px, 0.8fr) auto;
        gap: 14px;
        align-items: end;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #f8fafc;
        margin-bottom: 22px;
    }

    .catalog-filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .catalog-btn {
        min-height: 42px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-sizing: border-box;
        white-space: nowrap;
    }

    .catalog-btn-primary {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .catalog-btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    .catalog-btn-secondary {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .catalog-btn-secondary:hover {
        background: #f9fafb;
        color: #111827;
    }

    .catalog-results-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 18px;
    }

    .catalog-results-header h2 {
        margin: 0 0 5px;
        color: #111827;
        font-size: 1.18rem;
    }

    .catalog-results-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }

    .catalog-count {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 7px 12px;
        background: #eef2ff;
        color: #3730a3;
        font-weight: 900;
        font-size: 0.84rem;
        white-space: nowrap;
    }

    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 20px;
    }

    .catalog-card {
        display: flex;
        flex-direction: column;
        min-height: 365px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 22px;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .catalog-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
        border-color: rgba(37, 99, 235, 0.35);
    }

    .catalog-image-wrap {
        width: 100%;
        height: 190px;
        background: linear-gradient(135deg, #e5e7eb, #f8fafc);
        overflow: hidden;
        position: relative;
    }

    .catalog-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .catalog-image-placeholder {
        height: 100%;
        display: grid;
        place-items: center;
        color: #64748b;
        font-weight: 900;
        font-size: 0.95rem;
        background:
            radial-gradient(circle at top left, #dbeafe, transparent 42%),
            linear-gradient(135deg, #f8fafc, #e5e7eb);
    }

    .catalog-card-content {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 16px;
    }

    .catalog-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }

    .catalog-card-title {
        margin: 0 0 4px;
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .catalog-card-code {
        color: #94a3b8;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .catalog-category {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        border-radius: 999px;
        padding: 5px 9px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .catalog-card-description {
        margin: 0 0 12px;
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.45;
        flex: 1;
    }

    .catalog-price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .catalog-price {
        color: #111827;
        font-weight: 900;
        font-size: 1.02rem;
    }

    .catalog-stock {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.78rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .stock-disponible {
        background: #dcfce7;
        color: #166534;
    }

    .stock-bajo {
        background: #fef3c7;
        color: #92400e;
    }

    .stock-agotado {
        background: #fee2e2;
        color: #991b1b;
    }

    .catalog-card-footer {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        margin-top: 14px;
    }

    .catalog-wa-btn,
    .catalog-wa-disabled {
        width: 100%;
        min-height: 40px;
        border-radius: 12px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-sizing: border-box;
    }

    .catalog-wa-btn {
        border: 1px solid #22c55e;
        background: #22c55e;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(34, 197, 94, 0.25);
    }

    .catalog-wa-btn:hover {
        background: #16a34a;
        border-color: #16a34a;
    }

    .catalog-wa-disabled {
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #64748b;
    }

    .catalog-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        padding: 28px;
        text-align: center;
        background: #f8fafc;
        color: #64748b;
    }

    .catalog-empty strong {
        display: block;
        margin-bottom: 6px;
        color: #111827;
    }

    .catalog-empty p {
        margin: 0;
    }

    @media (max-width: 980px) {

        .catalog-hero,
        .catalog-filter-bar {
            grid-template-columns: 1fr;
        }

        .catalog-filter-actions {
            width: 100%;
        }

        .catalog-filter-actions a,
        .catalog-filter-actions button {
            width: 100%;
        }
    }

    @media (max-width: 700px) {
        .catalog-navbar-inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .catalog-navbar-actions {
            width: 100%;
            justify-content: space-between;
        }

        .catalog-container {
            padding: 18px 12px 36px;
        }

        .catalog-hero,
        .catalog-panel {
            border-radius: 18px;
            padding: 18px;
        }

        .catalog-results-header {
            flex-direction: column;
        }

        .catalog-grid {
            grid-template-columns: 1fr;
        }
    }
</style>