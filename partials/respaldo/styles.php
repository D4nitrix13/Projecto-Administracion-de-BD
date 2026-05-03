<style>
    .backup-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .backup-back-btn {
        white-space: nowrap;
    }

    .backup-page-card {
        padding: 28px;
    }

    .backup-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 22px;
    }

    .backup-grid-manual {
        grid-template-columns: minmax(0, 1.25fr) minmax(360px, 0.75fr);
        align-items: start;
    }

    .backup-grid-manual .backup-panel {
        max-width: none;
        height: auto;
        align-self: start;
    }

    .backup-grid-manual .backup-form {
        max-width: none;
    }

    .backup-panel {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        display: flex;
        flex-direction: column;
        gap: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .backup-panel-danger {
        border-color: #fecaca;
        background: linear-gradient(180deg, #fffafa 0%, #fff5f5 100%);
    }

    .backup-panel-header h2 {
        margin: 10px 0 6px;
        color: #111827;
        font-size: 1.15rem;
    }

    .backup-panel-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.92rem;
        line-height: 1.45;
    }

    .backup-panel-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .backup-badge-safe {
        background: #dcfce7;
        color: #166534;
    }

    .backup-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .backup-form {
        display: grid;
        gap: 16px;
    }

    .backup-help {
        margin-top: 6px;
        font-size: 0.82rem;
        line-height: 1.4;
    }

    .backup-warning {
        border: 1px solid #fecaca;
        background: #fff1f2;
        border-radius: 14px;
        padding: 14px;
        color: #7f1d1d;
    }

    .backup-warning strong {
        display: block;
        margin-bottom: 4px;
        color: #991b1b;
    }

    .backup-warning p {
        margin: 0;
        line-height: 1.45;
        font-size: 0.9rem;
    }

    .backup-btn {
        height: 46px;
        border-radius: 12px;
        border: none;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .backup-btn:hover {
        transform: translateY(-1px);
    }

    .backup-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
    }

    .backup-btn-primary:hover {
        background: #1d4ed8;
    }

    .backup-btn-danger {
        background: #dc2626;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(220, 38, 38, 0.15);
    }

    .backup-btn-danger:hover {
        background: #b91c1c;
    }

    .backup-side-panel {
        align-self: start;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        gap: 18px;
        height: auto;
    }

    .backup-side-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        border-radius: 999px;
        padding: 6px 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .backup-side-header h2 {
        margin: 10px 0 6px;
        color: #0f172a;
        font-size: 1.2rem;
    }

    .backup-side-header p {
        margin: 0;
        color: #64748b;
        line-height: 1.55;
        font-size: 0.93rem;
    }

    .backup-side-stats {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .backup-stat-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        padding: 14px 16px;
        display: grid;
        gap: 4px;
    }

    .backup-stat-card span {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .backup-stat-card strong {
        color: #0f172a;
        font-size: 1.35rem;
        line-height: 1.1;
    }

    .backup-stat-card small {
        color: #64748b;
        font-size: 0.84rem;
    }

    .backup-side-highlight {
        border-radius: 18px;
        padding: 16px;
        background: linear-gradient(180deg, #eff6ff 0%, #e0f2fe 100%);
        border: 1px solid #bfdbfe;
        display: grid;
        gap: 8px;
    }

    .backup-side-highlight-label {
        margin: 0;
        color: #1d4ed8;
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .backup-side-highlight h3 {
        margin: 0;
        color: #0f172a;
        font-size: 1.05rem;
    }

    .backup-side-highlight-subtitle {
        margin: 0;
        color: #334155;
        font-size: 0.92rem;
        line-height: 1.45;
    }

    .backup-side-highlight-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 4px;
        color: #1e3a8a;
        font-size: 0.84rem;
        font-weight: 600;
    }

    .backup-side-tips {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fcfcfd;
        padding: 16px;
    }

    .backup-side-tips h3 {
        margin: 0 0 10px;
        color: #0f172a;
        font-size: 1rem;
    }

    .backup-side-tips ul {
        margin: 0;
        padding-left: 18px;
        color: #475569;
        display: grid;
        gap: 8px;
        line-height: 1.45;
        font-size: 0.9rem;
    }

    .backup-history {
        border-top: 1px solid #e5e7eb;
        padding-top: 24px;
    }

    .backup-history-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .backup-history-header h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 1.15rem;
    }

    .backup-count {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 7px 12px;
        background: #f3f4f6;
        color: #374151;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .backup-empty {
        border: 1px dashed #d1d5db;
        border-radius: 14px;
        background: #f9fafb;
        padding: 20px;
        color: #6b7280;
        text-align: center;
    }

    .backup-empty strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
    }

    .backup-empty p {
        margin: 0;
    }

    .backup-files-grid {
        display: grid;
        gap: 16px;
    }

    .backup-file-card {
        display: grid;
        grid-template-columns: minmax(320px, 1.6fr) minmax(240px, 1fr) auto;
        gap: 18px;
        align-items: center;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        padding: 18px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.04);
    }

    .backup-file-card-pending {
        border-color: #fbbf24;
        background: linear-gradient(180deg, #fffdf7 0%, #fffaf0 100%);
    }

    .backup-file-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }

    .backup-file-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        color: #1d4ed8;
        font-weight: 900;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #bfdbfe;
    }

    .backup-file-info {
        min-width: 0;
    }

    .backup-file-topline {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .backup-file-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.05rem;
        font-weight: 800;
    }

    .backup-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .backup-status-ok {
        background: #dcfce7;
        color: #166534;
    }

    .backup-status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .backup-file-subtitle {
        margin: 0 0 6px;
        color: #475569;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .backup-file-original-name {
        margin: 0 0 10px;
        color: #6b7280;
        font-size: 0.84rem;
        line-height: 1.45;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .backup-file-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .backup-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 6px 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .backup-file-side {
        display: grid;
        gap: 12px;
    }

    .backup-file-date-block {
        display: grid;
        gap: 4px;
    }

    .backup-file-date-label {
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .backup-file-date {
        color: #0f172a;
        font-size: 0.93rem;
        line-height: 1.45;
    }

    .backup-delete-box {
        border: 1px solid #fde68a;
        background: #fff7ed;
        border-radius: 14px;
        padding: 12px;
    }

    .backup-delete-box strong {
        display: block;
        margin-bottom: 4px;
        color: #9a3412;
        font-size: 0.87rem;
    }

    .backup-delete-box p {
        margin: 0;
        color: #7c2d12;
        font-size: 0.84rem;
        line-height: 1.45;
    }

    .backup-delete-box-neutral {
        border-color: #dbeafe;
        background: #eff6ff;
    }

    .backup-delete-box-neutral strong {
        color: #1d4ed8;
    }

    .backup-delete-box-neutral p {
        color: #1e40af;
    }

    .backup-file-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 190px;
    }

    .backup-inline-form {
        margin: 0;
    }

    .backup-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 42px;
        border-radius: 12px;
        border: none;
        padding: 0 14px;
        text-decoration: none;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .backup-action-btn:hover {
        transform: translateY(-1px);
    }

    .backup-action-btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
    }

    .backup-action-btn-primary:hover {
        background: #1d4ed8;
    }

    .backup-action-btn-danger {
        background: #fff1f2;
        color: #b91c1c;
        border: 1px solid #fecdd3;
    }

    .backup-action-btn-danger:hover {
        background: #ffe4e6;
    }

    .backup-action-btn-dark {
        background: #0f172a;
        color: #ffffff;
    }

    .backup-action-btn-dark:hover {
        background: #020617;
    }

    @media (max-width: 1200px) {
        .backup-grid-manual {
            grid-template-columns: 1fr;
        }

        .backup-file-card {
            grid-template-columns: 1fr;
        }

        .backup-file-actions {
            min-width: 0;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .backup-file-actions>* {
            flex: 1 1 220px;
        }
    }

    @media (max-width: 1100px) {
        .backup-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {

        .backup-hero,
        .backup-history-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .backup-back-btn {
            width: 100%;
            text-align: center;
        }

        .backup-file-main {
            flex-direction: column;
        }

        .backup-file-original-name {
            white-space: normal;
        }

        .backup-file-actions {
            flex-direction: column;
        }
    }

    .backup-filter-panel {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        padding: 18px;
        margin-bottom: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
    }

    .backup-filter-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .backup-filter-header h3 {
        margin: 8px 0 4px;
        color: #0f172a;
        font-size: 1.05rem;
    }

    .backup-filter-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.45;
    }

    .backup-filter-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        border-radius: 999px;
        padding: 5px 10px;
        background: #eef2ff;
        color: #3730a3;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .backup-filter-clear {
        border: 1px solid #dbeafe;
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 12px;
        min-height: 40px;
        padding: 0 14px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
    }

    .backup-filter-clear:hover {
        background: #dbeafe;
    }

    .backup-filter-grid {
        display: grid;
        grid-template-columns: minmax(280px, 1.5fr) repeat(3, minmax(180px, 1fr));
        gap: 14px;
    }

    .backup-filter-field {
        display: grid;
        gap: 7px;
    }

    .backup-filter-field label {
        color: #334155;
        font-size: 0.84rem;
        font-weight: 800;
    }

    .backup-no-results {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background: #f8fafc;
        padding: 20px;
        color: #64748b;
        text-align: center;
        margin-bottom: 16px;
    }

    .backup-no-results strong {
        display: block;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .backup-no-results p {
        margin: 0;
    }

    @media (max-width: 1200px) {
        .backup-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .backup-filter-header {
            flex-direction: column;
        }

        .backup-filter-clear {
            width: 100%;
        }

        .backup-filter-grid {
            grid-template-columns: 1fr;
        }
    }

    .backup-page-card-compact {
        min-height: auto !important;
        height: auto !important;
        padding: 24px;
    }

    .backup-summary-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .backup-summary-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        padding: 16px;
        display: grid;
        gap: 5px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.035);
    }

    .backup-summary-card span {
        color: #64748b;
        font-size: 0.76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .backup-summary-card strong {
        color: #0f172a;
        font-size: 1.35rem;
        line-height: 1.15;
    }

    .backup-summary-card small {
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.35;
    }

    .backup-summary-card-blue {
        border-color: #bfdbfe;
        background: linear-gradient(180deg, #eff6ff 0%, #e0f2fe 100%);
    }

    .backup-summary-card-blue span,
    .backup-summary-card-blue small {
        color: #1e40af;
    }

    .backup-grid-manual-compact {
        display: block;
        margin-bottom: 22px;
    }

    .backup-grid-manual-compact .backup-panel {
        width: 100%;
        max-width: none;
        min-height: auto !important;
        height: auto !important;
    }

    .backup-grid-manual-compact .backup-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .backup-grid-manual-compact .backup-form .backup-btn {
        grid-column: 1 / -1;
    }

    .backup-grid-manual-compact .backup-help {
        margin-bottom: 0;
    }

    .backup-grid-manual-compact .backup-panel {
        padding: 20px;
    }

    .backup-grid-manual-compact .backup-panel-header {
        margin-bottom: 2px;
    }

    @media (max-width: 1200px) {
        .backup-summary-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .backup-summary-row {
            grid-template-columns: 1fr;
        }

        .backup-grid-manual-compact .backup-form {
            grid-template-columns: 1fr;
        }
    }
</style>