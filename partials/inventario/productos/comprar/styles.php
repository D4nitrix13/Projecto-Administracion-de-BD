<style>
    .comprar-hero {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .comprar-hero-text .dashboard-eyebrow {
        margin: 0 0 6px;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .comprar-hero-text .dashboard-title {
        margin: 0;
        color: #111827;
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.12;
    }

    .comprar-hero-text .dashboard-muted {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.55;
    }

    .comprar-hero-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #374151;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .comprar-hero-back:hover {
        background: #e5e7eb;
        border-color: #d1d5db;
    }

    .comprar-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 16px;
        font-weight: 700;
        font-size: 0.92rem;
        line-height: 1.45;
    }

    .comprar-alert svg {
        flex-shrink: 0;
    }

    .comprar-alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .comprar-alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .comprar-layout {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 24px;
        align-items: start;
    }

    .comprar-left {
        display: grid;
        gap: 20px;
        position: sticky;
        top: 24px;
    }

    .comprar-right {
        display: grid;
    }

    .comprar-producto-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .comprar-producto-img-wrapper {
        position: relative;
        width: 100%;
        height: 220px;
        background: #f3f4f6;
        overflow: hidden;
    }

    .comprar-producto-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .comprar-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .comprar-badge-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .comprar-badge-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .comprar-producto-body {
        padding: 20px 20px 0;
    }

    .comprar-producto-codigo {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 800;
        color: #6b7280;
        background: #f3f4f6;
        padding: 3px 10px;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .comprar-producto-nombre {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.15rem;
        font-weight: 900;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .comprar-producto-desc {
        margin: 0;
        color: #6b7280;
        font-size: 0.88rem;
        line-height: 1.5;
    }

    .comprar-producto-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: #e5e7eb;
        margin: 18px 0 0;
    }

    .comprar-stat {
        display: grid;
        gap: 4px;
        padding: 14px 20px;
        background: #ffffff;
    }

    .comprar-stat-label {
        font-size: 0.78rem;
        color: #6b7280;
        font-weight: 600;
    }

    .comprar-stat-value {
        font-size: 1rem;
        color: #111827;
        font-weight: 800;
    }

    .comprar-stat-value small {
        font-size: 0.78rem;
        font-weight: 600;
        color: #9ca3af;
    }

    .comprar-stat-warning {
        color: #d97706;
    }

    .comprar-preview-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .comprar-preview-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px 20px;
        background: #eff6ff;
        border-bottom: 1px solid #dbeafe;
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.88rem;
    }

    .comprar-preview-body {
        padding: 16px 20px;
    }

    .comprar-preview-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
    }

    .comprar-preview-row span {
        font-size: 0.88rem;
        color: #6b7280;
    }

    .comprar-preview-row strong {
        font-size: 0.95rem;
        color: #111827;
        font-weight: 800;
    }

    .comprar-preview-highlight strong {
        color: #2563eb;
        font-size: 1.1rem;
    }

    .comprar-preview-arrow {
        display: flex;
        justify-content: center;
        padding: 4px 0;
        color: #9ca3af;
    }

    .comprar-preview-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 8px 0;
    }

    .comprar-preview-total strong {
        color: #166534;
        font-size: 1.1rem;
    }

    .comprar-form-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .comprar-form-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 22px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .comprar-form-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #2563eb;
        color: #ffffff;
        flex-shrink: 0;
    }

    .comprar-form-header h2 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 900;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .comprar-form-header p {
        margin: 4px 0 0;
        font-size: 0.88rem;
        color: #6b7280;
    }

    .comprar-form {
        padding: 0;
    }

    .comprar-form-section {
        padding: 22px 24px;
        border-bottom: 1px solid #f3f4f6;
    }

    .comprar-form-section:last-of-type {
        border-bottom: none;
    }

    .comprar-form-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 16px;
        font-size: 0.85rem;
        font-weight: 800;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .comprar-form-section-title svg {
        color: #9ca3af;
    }

    .comprar-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .comprar-form-field {
        display: grid;
        gap: 7px;
    }

    .comprar-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: #374151;
    }

    .comprar-input {
        width: 100%;
        min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        background: #ffffff;
        color: #111827;
        font-size: 0.95rem;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .comprar-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .comprar-input::placeholder {
        color: #9ca3af;
    }

    .comprar-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .comprar-input-wrapper .comprar-input {
        padding-right: 60px;
    }

    .comprar-input-suffix {
        position: absolute;
        right: 14px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #9ca3af;
        pointer-events: none;
    }

    .comprar-input-wrapper .comprar-input-prefix-field {
        padding-left: 48px;
    }

    .comprar-input-prefix {
        position: absolute;
        left: 14px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #6b7280;
        pointer-events: none;
        z-index: 1;
    }

    .comprar-field-hint {
        margin: 2px 0 0;
        font-size: 0.8rem;
        color: #9ca3af;
    }

    .comprar-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 18px 24px;
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
    }

    .comprar-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 22px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.92rem;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
    }

    .comprar-btn-ghost {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .comprar-btn-ghost:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .comprar-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.16);
    }

    .comprar-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(37, 99, 235, 0.2);
    }

    .comprar-select-search {
        position: relative;
    }

    .comprar-select-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        background: #ffffff;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        outline: none;
    }

    .comprar-select-trigger svg:first-child {
        color: #9ca3af;
        flex-shrink: 0;
    }

    .comprar-select-trigger span {
        flex: 1;
        font-size: 0.95rem;
        color: #111827;
        text-align: left;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .comprar-select-trigger span.placeholder {
        color: #9ca3af;
    }

    .comprar-select-trigger svg:last-child {
        color: #9ca3af;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .comprar-select-trigger.active {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .comprar-select-trigger.active svg:last-child {
        transform: rotate(180deg);
    }

    .comprar-select-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
        z-index: 100;
        overflow: hidden;
    }

    .comprar-select-dropdown.open {
        display: block;
    }

    .comprar-select-search-box {
        padding: 10px;
        border-bottom: 1px solid #f3f4f6;
    }

    .comprar-select-search-input {
        width: 100%;
        min-height: 38px;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        font-size: 0.9rem;
        color: #111827;
        outline: none;
        box-sizing: border-box;
    }

    .comprar-select-search-input:focus {
        border-color: #2563eb;
        background: #ffffff;
    }

    .comprar-select-options {
        list-style: none;
        margin: 0;
        padding: 6px;
        max-height: 240px;
        overflow-y: auto;
    }

    .comprar-select-option {
        padding: 10px 14px;
        font-size: 0.92rem;
        color: #374151;
        cursor: pointer;
        border-radius: 8px;
        transition: background 0.1s ease;
    }

    .comprar-select-option:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .comprar-select-empty {
        padding: 20px 14px;
        text-align: center;
        font-size: 0.88rem;
        color: #9ca3af;
    }

    @media (max-width: 1100px) {
        .comprar-layout {
            grid-template-columns: 1fr;
        }

        .comprar-left {
            position: static;
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .comprar-left {
            grid-template-columns: 1fr;
        }

        .comprar-form-grid {
            grid-template-columns: 1fr;
        }

        .comprar-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
