<style>
    .products-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
    }

    .products-hero>div {
        min-width: 0;
    }

    .products-hero p:first-child,
    .products-hero .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .products-hero h1,
    .products-hero .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .products-hero h1+p,
    .products-hero .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
        text-transform: none;
        letter-spacing: normal;
        font-weight: 400;
    }

    .products-panel {
        margin-top: 0;
    }

    .productos-filtros-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: end;
        padding: 18px;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .productos-filtros-bar .filtro-item {
        flex: 1 1 160px;
        min-width: 0;
    }

    .filtro-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .filtro-item {
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 0;
    }

    .label {
        color: #111827;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .input {
        width: 100%;
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        background: #ffffff;
        color: #111827;
        font-size: 0.94rem;
        outline: none;
        box-sizing: border-box;
        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease,
            background 0.15s ease;
    }

    .input::placeholder {
        color: #8b95a5;
    }

    .input:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .filtro-actions {
        display: flex;
        align-items: end;
        gap: 8px;
    }

    .btn-primary-inline,
    .btn-secondary-inline {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.94rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease,
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .btn-primary-inline {
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

    .products-result-header {
        margin: 18px 0 14px;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.45;
    }

    .products-result-header strong {
        color: #111827;
        font-weight: 900;
    }

    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(315px, 1fr));
        gap: 18px;
        margin-top: 16px;
    }

    .producto-card {
        display: flex;
        flex-direction: column;
        min-height: 288px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        transition:
            transform 0.15s ease,
            box-shadow 0.15s ease,
            border-color 0.15s ease;
    }

    .producto-card:hover {
        transform: translateY(-2px);
        border-color: #dbe3ee;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.09);
    }

    .producto-card-header {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        min-width: 0;
    }

    .producto-card-img {
        width: 68px;
        height: 68px;
        min-width: 68px;
        flex: 0 0 68px;
        border-radius: 14px;
        overflow: hidden;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        display: block;
        text-decoration: none;
    }

    .producto-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .producto-card-header>div {
        min-width: 0;
        flex: 1;
    }

    .producto-card-title {
        margin: 0 0 5px;
        color: #111827;
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .producto-card-title a,
    .producto-card-header a:not(.producto-card-img) {
        color: inherit;
        text-decoration: none;
    }

    .producto-card-title a:hover,
    .producto-card-header a:not(.producto-card-img):hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .producto-card-sub {
        margin: 0;
        color: #6b7280;
        font-size: 0.84rem;
        line-height: 1.35;
    }

    .producto-card-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
        margin-top: 14px;
        color: #4b5563;
        font-size: 0.9rem;
        line-height: 1.42;
    }

    .producto-card-body p {
        margin: 0;
    }

    .producto-meta {
        display: grid;
        gap: 5px;
    }

    .producto-meta span,
    .producto-meta p {
        margin: 0;
        color: #4b5563;
        line-height: 1.35;
    }

    .producto-meta strong {
        color: #374151;
        font-weight: 900;
    }

    .producto-precios {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: auto;
        color: #374151;
        font-size: 0.9rem;
    }

    .producto-precios span {
        min-width: 0;
    }

    .producto-precios strong {
        color: #111827;
        font-weight: 900;
    }

    .producto-precios span:last-child {
        text-align: right;
    }

    .producto-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: nowrap;
    }

    .producto-stock-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 78px;
        min-height: 30px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .stock-normal {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .stock-bajo {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .producto-ventas-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
        flex-shrink: 0;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .producto-card-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
        margin-left: auto;
        min-width: 0;
    }

    .btn-accion-xs {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 6px 9px;
        border-radius: 999px;
        text-decoration: none;
        cursor: pointer;
        font-size: 0.74rem;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
        flex-shrink: 0;
        border: 1px solid transparent;
        transition:
            background 0.15s ease,
            border-color 0.15s ease,
            transform 0.15s ease;
    }

    .btn-accion-xs:hover {
        transform: translateY(-1px);
    }

    .btn-accion-editar-xs {
        background: #eef2ff;
        color: #3730a3;
        border-color: #c7d2fe;
    }

    .btn-accion-editar-xs:hover {
        background: #e0e7ff;
    }

    .btn-accion-comprar-xs {
        background: #ecfdf5;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .btn-accion-comprar-xs:hover {
        background: #dcfce7;
    }

    .btn-accion-eliminar-xs {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .btn-accion-eliminar-xs:hover {
        background: #fee2e2;
    }

    .solo-lectura {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        font-size: 0.74rem;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
    }

    .empty-products {
        margin-top: 18px;
        padding: 30px 16px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        text-align: center;
        color: #6b7280;
        line-height: 1.5;
    }

    @media (max-width: 1200px) {
        .productos-filtros-bar .filtro-item {
            flex: 1 1 200px;
        }

        .filtro-actions {
            justify-content: flex-end;
        }
    }

    @media (max-width: 760px) {
        .products-hero {
            padding: 20px;
            border-radius: 16px;
            flex-direction: column;
            align-items: stretch;
        }

        .products-hero .btn-primary-inline,
        .products-hero .btn-secondary-inline {
            width: 100%;
        }

        .productos-filtros-bar {
            flex-direction: column;
            padding: 16px;
        }

        .productos-filtros-bar .filtro-item {
            flex: 1 1 100%;
        }

        .filtro-actions {
            flex-direction: column;
        }

        .filtro-actions .btn-primary-inline,
        .filtro-actions .btn-secondary-inline {
            width: 100%;
        }

        .productos-grid {
            grid-template-columns: 1fr;
        }

        .producto-precios {
            grid-template-columns: 1fr;
        }

        .producto-precios span:last-child {
            text-align: left;
        }

        .producto-card-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .producto-card-actions {
            justify-content: flex-start;
            margin-left: 0;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 420px) {
        .producto-card-header {
            flex-direction: column;
        }

        .producto-card-img {
            width: 82px;
            height: 82px;
            min-width: 82px;
            flex-basis: 82px;
        }
    }

    .products-alert {
        display: grid;
        gap: 4px;
        margin: 0 0 18px;
        padding: 14px 16px;
        border-radius: 14px;
        font-size: 0.92rem;
        line-height: 1.45;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }

    .products-alert strong {
        font-size: 0.88rem;
        font-weight: 900;
    }

    .products-alert span {
        font-weight: 700;
    }

    .products-alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .products-alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
</style>