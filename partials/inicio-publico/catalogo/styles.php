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
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: end;
        padding: 16px;
        margin-bottom: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
    }

    .filter-field,
    .catalog-filter-field,
    .form-group,
    .catalog-panel>form label {
        display: grid;
        gap: 7px;
        min-width: 200px;
        flex: 1;
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
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .catalog-card {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        transition:
            transform 0.15s ease,
            box-shadow 0.15s ease,
            border-color 0.15s ease;
    }

    .catalog-card:hover {
        transform: translateY(-2px);
        border-color: #bfdbfe;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.10);
    }

    .catalog-image-wrap {
        width: 100%;
        height: 180px;
        background: #f8fafc;
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
        object-fit: contain;
        display: block;
        padding: 8px;
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
        gap: 10px;
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
        max-width: 140px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 0.72rem;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-shrink: 0;
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
       SECTION HEADERS
       ========================= */

    .catalog-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .catalog-section-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 900;
    }

    .catalog-section-link {
        color: #007185;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .catalog-section-link:hover {
        color: #c7511f;
        text-decoration: underline;
    }

    /* =========================
       MOST SOLD
       ========================= */

    .catalog-most-sold {
        margin-bottom: 36px;
    }

    .catalog-most-sold-scroll {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 8px;
    }

    .catalog-most-sold-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .catalog-most-sold-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .catalog-most-sold-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .catalog-most-sold-item {
        display: flex;
        flex-direction: column;
        min-width: 170px;
        max-width: 170px;
        scroll-snap-align: start;
        text-decoration: none;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition:
            box-shadow 0.15s ease,
            border-color 0.15s ease;
    }

    .catalog-most-sold-item:hover {
        border-color: #bfdbfe;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.10);
    }

    .catalog-most-sold-image {
        width: 100%;
        height: 140px;
        background: #f8fafc;
        overflow: hidden;
        display: grid;
        place-items: center;
        position: relative;
    }

    .catalog-most-sold-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .catalog-most-sold-placeholder {
        color: #94a3b8;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .catalog-most-sold-badge {
        position: absolute;
        top: 6px;
        left: 6px;
        padding: 3px 8px;
        border-radius: 6px;
        background: #f97316;
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .catalog-most-sold-name {
        padding: 10px 10px 2px;
        color: #111827;
        font-size: 0.82rem;
        font-weight: 700;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .catalog-most-sold-price {
        padding: 2px 10px 10px;
        color: #b12704;
        font-size: 0.9rem;
        font-weight: 900;
    }

    /* =========================
       CATEGORY EXPLORER
       ========================= */

    .catalog-explorer {
        margin-bottom: 36px;
    }

    .catalog-explorer-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .catalog-explorer-card {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        transition:
            box-shadow 0.15s ease,
            border-color 0.15s ease;
    }

    .catalog-explorer-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.10);
    }

    .catalog-explorer-image {
        width: 100%;
        height: 120px;
        background: #f8fafc;
        overflow: hidden;
        display: grid;
        place-items: center;
    }

    .catalog-explorer-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .catalog-explorer-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        font-size: 1rem;
        font-weight: 900;
        display: grid;
        place-items: center;
    }

    .catalog-explorer-info {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .catalog-explorer-name {
        color: #111827;
        font-size: 0.9rem;
        font-weight: 800;
        line-height: 1.3;
    }

    .catalog-explorer-count {
        color: #6b7280;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* =========================
       CATEGORY CARDS (Amazon style)
       ========================= */

    .catalog-categories {
        margin-bottom: 32px;
    }

    .catalog-categories-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .catalog-category-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
        transition:
            box-shadow 0.15s ease,
            border-color 0.15s ease;
    }

    .catalog-category-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.10);
    }

    .catalog-category-title {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 1.1rem;
        font-weight: 900;
        line-height: 1.3;
    }

    .catalog-category-main-image {
        width: 100%;
        height: 160px;
        background: #f8fafc;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .catalog-category-main-image a {
        display: block;
        width: 100%;
        height: 100%;
    }

    .catalog-category-main-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .catalog-category-grid-2x2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 14px;
    }

    .catalog-category-subitem {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        border-radius: 8px;
        overflow: hidden;
        background: #f8fafc;
        transition: background 0.15s ease;
    }

    .catalog-category-subitem:hover {
        background: #f1f5f9;
    }

    .catalog-category-subitem img {
        width: 100%;
        height: 80px;
        object-fit: contain;
        padding: 4px;
    }

    .catalog-category-subitem-placeholder {
        width: 100%;
        height: 80px;
        display: grid;
        place-items: center;
        color: #94a3b8;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .catalog-category-subitem-name {
        padding: 4px 6px;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 700;
        text-align: center;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }

    .catalog-category-explore {
        margin-top: auto;
        padding-top: 10px;
        color: #007185;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
    }

    .catalog-category-explore:hover {
        color: #c7511f;
        text-decoration: underline;
    }

    /* =========================
       PRODUCT ROWS (Amazon style)
       ========================= */

    .catalog-product-rows {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .catalog-product-row {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }

    .catalog-product-row-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .catalog-product-row-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.2rem;
        font-weight: 900;
    }

    .catalog-product-row-see-all {
        color: #007185;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .catalog-product-row-see-all:hover {
        color: #c7511f;
        text-decoration: underline;
    }

    .catalog-product-row-scroll {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 8px;
    }

    .catalog-product-row-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .catalog-product-row-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .catalog-product-row-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .catalog-product-row-item {
        display: flex;
        flex-direction: column;
        min-width: 160px;
        max-width: 160px;
        scroll-snap-align: start;
        text-decoration: none;
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.15s ease;
    }

    .catalog-product-row-item:hover {
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.10);
    }

    .catalog-product-row-image {
        width: 100%;
        height: 140px;
        background: #f8fafc;
        overflow: hidden;
        display: grid;
        place-items: center;
    }

    .catalog-product-row-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .catalog-product-row-placeholder {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .catalog-product-row-name {
        padding: 8px 8px 2px;
        color: #111827;
        font-size: 0.82rem;
        font-weight: 700;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .catalog-product-row-price {
        padding: 2px 8px 8px;
        color: #b12704;
        font-size: 0.88rem;
        font-weight: 900;
    }

    /* =========================
       PAGINATION
       ========================= */

    .catalog-pagination {
        margin-top: 28px;
        display: flex;
        justify-content: center;
    }

    .catalog-pagination-list {
        display: flex;
        align-items: center;
        gap: 6px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .catalog-pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease;
    }

    .catalog-pagination-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #111827;
    }

    .catalog-pagination-active {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        cursor: default;
    }

    .catalog-pagination-ellipsis {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        color: #9ca3af;
        font-size: 0.9rem;
    }

    /* =========================
       RESPONSIVE
       ========================= */

    @media (max-width: 1080px) {
        .catalog-hero {
            grid-template-columns: 1fr;
        }

        .catalog-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .catalog-categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .catalog-explorer-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 760px) {
        .catalog-categories-grid {
            grid-template-columns: 1fr;
        }

        .catalog-explorer-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .catalog-section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .catalog-most-sold-item {
            min-width: 140px;
            max-width: 140px;
        }
    }

    @media (max-width: 520px) {
        .catalog-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .catalog-explorer-grid {
            grid-template-columns: 1fr;
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
            padding: 16px;
        }

        .filter-field,
        .catalog-filter-field,
        .form-group,
        .catalog-panel>form label {
            min-width: 100%;
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