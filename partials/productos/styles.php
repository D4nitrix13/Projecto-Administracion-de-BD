<style>
    .products-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 22px;
    }

    .products-panel {
        margin-top: 0;
    }

    .productos-filtros-bar {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1fr 0.8fr auto auto;
        gap: 12px;
        align-items: end;
        padding: 16px;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid #eef2f7;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }

    .filtro-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .label {
        font-weight: 700;
        font-size: 0.78rem;
        color: #111827;
    }

    .input {
        height: 42px;
        border: 1px solid #dbe1ea;
        border-radius: 12px;
        padding: 0 12px;
        background: #f8fafc;
        color: #111827;
        outline: none;
    }

    .input:focus {
        border-color: #1f2937;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.10);
    }

    .filtro-actions {
        display: flex;
        gap: 8px;
    }

    .btn-primary-inline,
    .btn-secondary-inline {
        height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary-inline {
        background: #111827;
        color: #ffffff;
    }

    .btn-primary-inline:hover {
        background: #1f2937;
    }

    .btn-secondary-inline {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary-inline:hover {
        background: #e5e7eb;
    }

    .products-result-header {
        margin: 18px 0 10px;
        color: #6b7280;
    }

    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(315px, 1fr));
        gap: 18px;
        margin-top: 16px;
    }

    .producto-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.07);
        border: 1px solid #eef2f7;
        display: flex;
        flex-direction: column;
        min-height: 260px;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .producto-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
    }

    .producto-card-header {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .producto-card-img {
        width: 68px;
        height: 68px;
        border-radius: 14px;
        overflow: hidden;
        flex-shrink: 0;
        background: #111827;
    }

    .producto-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .producto-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        margin: 0 0 4px;
    }

    .producto-card-sub {
        margin: 0;
        color: #6b7280;
        font-size: 0.82rem;
    }

    .producto-card-body {
        margin-top: 12px;
        color: #4b5563;
        font-size: 0.86rem;
        line-height: 1.35;
        flex: 1;
    }

    .producto-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .producto-precios {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
        color: #374151;
    }

    .producto-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 14px;
        flex-wrap: nowrap;
    }

    .producto-stock-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 74px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .stock-normal {
        background: #e0f2fe;
        color: #0369a1;
    }

    .stock-bajo {
        background: #fee2e2;
        color: #b91c1c;
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
        font-size: 0.74rem;
        padding: 6px 9px;
        border-radius: 999px;
        text-decoration: none;
        cursor: pointer;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .btn-accion-editar-xs {
        background: #eef2ff;
        color: #3730a3;
    }

    .btn-accion-comprar-xs {
        background: #ecfdf5;
        color: #15803d;
    }

    .btn-accion-eliminar-xs {
        background: #fef2f2;
        color: #b91c1c;
    }

    .solo-lectura {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 0.74rem;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
    }

    .empty-products {
        margin-top: 18px;
        padding: 26px;
        border-radius: 16px;
        background: #f8fafc;
        text-align: center;
        color: #64748b;
    }

    @media (max-width: 1100px) {
        .productos-filtros-bar {
            grid-template-columns: 1fr 1fr;
        }

        .filtro-actions {
            grid-column: span 2;
        }
    }

    @media (max-width: 640px) {
        .products-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .productos-filtros-bar {
            grid-template-columns: 1fr;
        }

        .filtro-actions {
            grid-column: auto;
            flex-direction: column;
        }

        .productos-grid {
            grid-template-columns: 1fr;
        }

        .producto-precios {
            flex-direction: column;
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
</style>