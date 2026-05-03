<style>
    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
    }

    body.catalog-public-body {
        min-height: 100vh;
        font-family: Arial, sans-serif;
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        color: #111827;
    }

    a {
        color: inherit;
    }

    .catalog-container {
        width: min(1500px, calc(100% - 40px));
        margin: 0 auto;
        padding: 28px 0 44px;
    }

    /* =========================
       NAVBAR
       ========================= */

    .catalog-navbar {
        background: #1e293b;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.14);
    }

    .catalog-navbar-inner {
        width: min(1500px, calc(100% - 40px));
        min-height: 74px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .catalog-brand,
    .catalog-logo,
    .catalog-brand-link {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        color: #ffffff;
        text-decoration: none;
        font-weight: 800;
    }

    .catalog-brand span,
    .catalog-logo span,
    .catalog-brand-link span,
    .catalog-brand strong,
    .catalog-logo strong,
    .catalog-brand-link strong {
        color: #ffffff;
    }

    .catalog-brand img,
    .catalog-logo img,
    .catalog-brand-logo {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        object-fit: cover;
        background: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.18);
        flex: 0 0 auto;
    }

    .catalog-navbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .catalog-navbar-actions a,
    .catalog-nav-link,
    .catalog-nav-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 999px;
        background: transparent;
        color: #e5e7eb;
        border: 1px solid transparent;
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: 800;
        white-space: nowrap;
        transition:
            background 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease;
    }

    .catalog-navbar-actions a:hover,
    .catalog-nav-link:hover,
    .catalog-nav-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .catalog-navbar-actions a:last-child,
    .catalog-nav-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 18px rgba(37, 99, 235, 0.18);
    }

    .catalog-navbar-actions a:last-child:hover,
    .catalog-nav-primary:hover {
        background: #1d4ed8;
        color: #ffffff;
    }

    /* =========================
       HERO
       ========================= */

    .catalog-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(260px, 0.8fr);
        gap: 18px;
        align-items: stretch;
        margin-bottom: 22px;
    }

    .catalog-hero-copy,
    .catalog-hero-info {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.06);
    }

    .catalog-hero-copy {
        padding: 28px;
    }

    .catalog-hero-info {
        padding: 18px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        align-content: center;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .catalog-eyebrow,
    .catalog-section-eyebrow {
        margin: 0 0 8px;
        color: #94a3b8;
        font-size: 0.78rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .catalog-hero-copy h1 {
        margin: 0;
        color: #0f172a;
        font-size: clamp(2rem, 4vw, 3.2rem);
        line-height: 1.02;
        font-weight: 900;
        letter-spacing: -0.05em;
    }

    .catalog-hero-text {
        margin: 14px 0 0;
        max-width: 760px;
        color: #475569;
        font-size: 1rem;
        line-height: 1.6;
    }

    .catalog-hero-mini-card {
        padding: 15px 16px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
    }

    .catalog-hero-mini-card span {
        display: block;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .catalog-hero-mini-card strong {
        display: block;
        color: #0f172a;
        font-size: 1.1rem;
        font-weight: 900;
    }

    /* =========================
       PANEL PRINCIPAL
       ========================= */

    .catalog-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.06);
    }

    /* =========================
       FILTROS
       ========================= */

    .catalog-filters,
    .catalog-filter-form,
    .filters-panel,
    .catalog-panel>form {
        display: grid;
        grid-template-columns: minmax(260px, 1.25fr) minmax(180px, 0.7fr) minmax(180px, 0.7fr) auto auto;
        gap: 12px;
        align-items: end;
        padding: 16px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #f8fafc;
    }

    .filter-field,
    .catalog-filter-field,
    .form-group,
    .catalog-panel>form label {
        display: grid;
        gap: 7px;
        min-width: 0;
        width: 100%;
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .catalog-panel input,
    .catalog-panel select,
    .input {
        width: 100%;
        min-width: 0;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 11px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        font-family: Arial, sans-serif;
        outline: none;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease;
    }

    .catalog-panel input::placeholder {
        color: #94a3b8;
    }

    .catalog-panel input:focus,
    .catalog-panel select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .filter-actions,
    .catalog-filter-actions {
        display: flex;
        align-items: end;
        justify-content: flex-end;
        gap: 10px;
    }

    .catalog-panel button,
    .catalog-panel>form a,
    .btn-primary-inline,
    .btn-secondary-inline,
    .catalog-btn-primary,
    .catalog-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        height: 42px;
        padding: 0 18px;
        border-radius: 11px;
        font-size: 0.94rem;
        font-weight: 800;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        border: 1px solid transparent;
        font-family: Arial, sans-serif;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            transform 0.15s ease;
    }

    .catalog-panel button,
    .btn-primary-inline,
    .catalog-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 18px rgba(37, 99, 235, 0.16);
    }

    .catalog-panel button:hover,
    .btn-primary-inline:hover,
    .catalog-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .catalog-panel>form a,
    .btn-secondary-inline,
    .catalog-btn-secondary {
        background: #ffffff;
        color: #374151;
        border-color: #cbd5e1;
    }

    .catalog-panel>form a:hover,
    .btn-secondary-inline:hover,
    .catalog-btn-secondary:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    /* =========================
       HEADER DE RESULTADOS
       ========================= */

    .catalog-results-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .catalog-results-header h2 {
        margin: 0 0 6px;
        font-size: 1.45rem;
        color: #111827;
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .catalog-results-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .catalog-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 7px 13px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-size: 0.82rem;
        font-weight: 900;
        white-space: nowrap;
    }

    /* =========================
       GRID
       ========================= */

    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(235px, 1fr));
        gap: 18px;
        align-items: stretch;
    }

    .catalog-card {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        transition:
            transform 0.15s ease,
            box-shadow 0.15s ease,
            border-color 0.15s ease;
    }

    .catalog-card:hover {
        transform: translateY(-2px);
        border-color: #bfdbfe;
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.10);
    }

    .catalog-image-wrap {
        width: 100%;
        height: 145px;
        background: #eef2f7;
        border-bottom: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .catalog-image-wrap a {
        display: block;
        width: 100%;
        height: 100%;
    }

    .catalog-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .catalog-image-placeholder {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #eef2f7 0%, #f8fafc 100%);
        color: #64748b;
        font-size: 0.88rem;
        font-weight: 900;
    }

    .catalog-card-content {
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
        padding: 16px;
    }

    .catalog-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .catalog-card-title {
        margin: 0;
        color: #111827;
        font-size: 1.04rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .catalog-card-code {
        margin-top: 4px;
        color: #6b7280;
        font-size: 0.84rem;
        line-height: 1.35;
    }

    .catalog-category {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 26px;
        max-width: 110px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 0 0 auto;
    }

    .catalog-card-description {
        margin: 0;
        color: #4b5563;
        font-size: 0.92rem;
        line-height: 1.5;
        min-height: 44px;
    }

    .catalog-price-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: auto;
        padding-top: 2px;
    }

    .catalog-price {
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.2;
        white-space: nowrap;
    }

    .catalog-stock {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 0.78rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .catalog-stock.stock-normal,
    .catalog-stock.stock-disponible,
    .catalog-stock.catalog-stock-available {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .catalog-stock.stock-bajo,
    .catalog-stock.catalog-stock-low {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .catalog-stock.stock-agotado,
    .catalog-stock.stock-empty,
    .catalog-stock.catalog-stock-empty {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .catalog-card-footer {
        margin-top: 2px;
    }

    .catalog-wa-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 11px;
        background: #16a34a;
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 900;
        white-space: nowrap;
        transition:
            background 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .catalog-wa-btn:hover {
        background: #15803d;
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(22, 163, 74, 0.15);
    }

    .catalog-wa-disabled {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 11px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 0.9rem;
        font-weight: 900;
        text-align: center;
    }

    .catalog-empty {
        padding: 34px 16px;
        color: #6b7280;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        line-height: 1.5;
    }

    .catalog-empty strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
    }

    .catalog-empty p {
        margin: 0;
    }

    /* =========================
       RESPONSIVE
       ========================= */

    @media (max-width: 1080px) {
        .catalog-hero {
            grid-template-columns: 1fr;
        }

        .catalog-filters,
        .catalog-filter-form,
        .filters-panel,
        .catalog-panel>form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-actions,
        .catalog-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }

    @media (max-width: 760px) {

        .catalog-container,
        .catalog-navbar-inner {
            width: min(100% - 24px, 1500px);
        }

        .catalog-container {
            padding: 22px 0 32px;
        }

        .catalog-navbar-inner {
            min-height: auto;
            padding: 14px 0;
            flex-direction: column;
            align-items: stretch;
        }

        .catalog-navbar-actions {
            flex-direction: column;
            align-items: stretch;
            justify-content: stretch;
        }

        .catalog-navbar-actions a,
        .catalog-nav-link,
        .catalog-nav-btn {
            width: 100%;
        }

        .catalog-hero-copy,
        .catalog-hero-info,
        .catalog-panel {
            padding: 18px;
            border-radius: 18px;
        }

        .catalog-hero-copy h1 {
            font-size: clamp(1.8rem, 9vw, 2.5rem);
        }

        .catalog-filters,
        .catalog-filter-form,
        .filters-panel,
        .catalog-panel>form {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .filter-actions,
        .catalog-filter-actions {
            justify-content: stretch;
            flex-direction: column;
            align-items: stretch;
        }

        .catalog-panel button,
        .catalog-panel>form a,
        .btn-primary-inline,
        .btn-secondary-inline,
        .catalog-btn-primary,
        .catalog-btn-secondary {
            width: 100%;
        }

        .catalog-results-header {
            flex-direction: column;
            align-items: stretch;
        }

        .catalog-count {
            width: fit-content;
        }

        .catalog-grid {
            grid-template-columns: 1fr;
        }

        .catalog-image-wrap {
            height: 210px;
        }

        .catalog-card-top,
        .catalog-price-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .catalog-category {
            max-width: 100%;
        }
    }

    .catalog-navbar {
        width: 100%;
        background: #1e2d45;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .catalog-navbar-inner {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 14px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .catalog-brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        color: #ffffff;
        text-decoration: none;
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .catalog-brand-logo {
        width: 42px;
        height: 42px;
        object-fit: contain;
        border-radius: 12px;
        background: #ffffff;
        padding: 6px;
        flex-shrink: 0;
    }

    .catalog-brand-separator {
        color: #cbd5e1;
        font-weight: 900;
    }

    .catalog-navbar-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-left: auto;
        min-width: 0;
    }

    .catalog-session-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        min-width: 170px;
        max-width: none;
        padding: 0 18px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.16);
        color: #f8fafc;
        font-size: 0.95rem;
        font-weight: 800;
        white-space: nowrap;
        overflow: visible;
        text-overflow: unset;
    }

    .catalog-navbar-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 22px;
        border-radius: 999px;
        background: #2563eb;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.22);
        transition:
            background 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .catalog-navbar-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    @media (max-width: 760px) {
        .catalog-navbar-inner {
            padding: 14px 18px;
            flex-direction: column;
            align-items: stretch;
        }

        .catalog-brand {
            justify-content: center;
            flex-wrap: wrap;
            text-align: center;
        }

        .catalog-navbar-actions {
            width: 100%;
            justify-content: center;
            flex-direction: column;
        }

        .catalog-session-label,
        .catalog-navbar-btn {
            width: 100%;
        }
    }
</style>