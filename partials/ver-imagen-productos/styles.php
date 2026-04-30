<style>
    .image-product-card {
        background: #ffffff;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        border: 1px solid #eef2f7;
    }

    .image-product-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .image-product-header h1 {
        margin: 4px 0 6px;
        font-size: 1.8rem;
        color: #111827;
    }

    .image-product-header span {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .image-preview-box {
        width: 100%;
        background: #f8fafc;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .image-preview-box img {
        width: 100%;
        max-height: 520px;
        object-fit: contain;
        display: block;
    }

    .image-product-info {
        margin-top: 22px;
        color: #4b5563;
        line-height: 1.6;
    }

    .product-mini-stats {
        margin-top: 18px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .product-mini-stats span {
        background: #f3f4f6;
        color: #374151;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 0.9rem;
    }

    .back-btn {
        background: #111827;
        color: #ffffff;
        padding: 11px 16px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.86rem;
        white-space: nowrap;
    }

    .back-btn:hover {
        background: #1f2937;
    }

    @media (max-width: 700px) {
        .image-product-header {
            flex-direction: column;
        }

        .back-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>