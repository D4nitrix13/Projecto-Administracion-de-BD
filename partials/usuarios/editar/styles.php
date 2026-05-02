<style>
    .user-edit-card {
        padding: 28px;
    }

    .user-edit-form {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 22px;
        align-items: start;
    }

    .user-edit-help {
        font-size: 12px;
        margin-top: 6px;
        line-height: 1.4;
    }

    .user-edit-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #e5e7eb;
    }

    .user-edit-btn {
        width: 160px;
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

    .user-edit-btn-cancel {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .user-edit-btn-cancel:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }

    .user-edit-btn-save {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .user-edit-btn-save:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    @media (max-width: 1100px) {
        .user-edit-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .user-edit-form {
            grid-template-columns: 1fr;
        }

        .user-edit-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .user-edit-btn {
            width: 100%;
        }
    }
</style>