<style>
    .account-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .account-back-btn {
        white-space: nowrap;
    }

    .account-settings-card {
        padding: 28px;
    }

    .account-settings-form {
        display: grid;
        gap: 24px;
    }

    .account-section {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        background: #ffffff;
    }

    .account-section-header {
        margin-bottom: 18px;
    }

    .account-section-header h2 {
        margin: 0 0 6px;
        font-size: 1.12rem;
        color: #111827;
    }

    .account-section-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
    }

    .account-form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        align-items: start;
    }

    .account-help {
        font-size: 12px;
        margin-top: 6px;
        line-height: 1.4;
    }

    .account-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        padding-top: 4px;
    }

    .account-btn {
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

    .account-btn-cancel {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .account-btn-cancel:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }

    .account-btn-save {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .account-btn-save:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    @media (max-width: 1100px) {
        .account-form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .account-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .account-form-grid {
            grid-template-columns: 1fr;
        }

        .account-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .account-btn {
            width: 100%;
        }
    }
</style>