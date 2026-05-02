<style>
    .supplier-edit-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .supplier-back-btn {
        white-space: nowrap;
    }

    .supplier-edit-card {
        padding: 28px;
    }

    .supplier-edit-form {
        display: grid;
        gap: 24px;
    }

    .supplier-edit-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #ffffff;
    }

    .supplier-edit-section-header {
        margin-bottom: 18px;
    }

    .supplier-edit-section-header h2 {
        margin: 0 0 6px;
        font-size: 1.12rem;
        color: #111827;
    }

    .supplier-edit-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
    }

    .supplier-edit-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .supplier-edit-grid .form-group-full {
        grid-column: 1 / -1;
    }

    .supplier-edit-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
    }

    .supplier-btn {
        width: 170px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 700;
        text-decoration: none;
        box-sizing: border-box;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .supplier-btn-cancel {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .supplier-btn-cancel:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }

    .supplier-btn-save {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .supplier-btn-save:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    @media (max-width: 1000px) {
        .supplier-edit-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .supplier-edit-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .supplier-edit-grid {
            grid-template-columns: 1fr;
        }

        .supplier-edit-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .supplier-btn {
            width: 100%;
        }
    }
</style>