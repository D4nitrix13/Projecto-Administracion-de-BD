<style>
    :root {
        --sidebar-width: 260px;
        --sidebar-bg: #243247;
        --sidebar-bg-dark: #202b3d;
        --primary: #ff2f63;
        --primary-dark: #dc1f50;
        --text-main: #111827;
        --text-muted: #6b7280;
        --page-bg: #f3f5f9;
        --card-bg: #ffffff;
        --border: #e5e7eb;
        --success: #16a34a;
        --danger: #dc2626;
        --info: #2563eb;
        --shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        --shadow-hover: 0 18px 35px rgba(15, 23, 42, 0.14);
    }

    * {
        box-sizing: border-box;
    }

    body.dashboard-body {
        margin: 0;
        min-height: 100vh;
        background: var(--page-bg);
        color: var(--text-main);
        font-family: Arial, sans-serif;
    }

    /* ==============================
       Sidebar
       ============================== */

    .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--sidebar-bg);
        color: #e5e7eb;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 900;
        transition: transform 0.25s ease;
    }

    .sidebar-header {
        min-height: 86px;
        padding: 20px 18px 14px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .brand-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        flex: 0 0 38px;
        border-radius: 12px;
        overflow: hidden;
        background: #ffffff;
        display: grid;
        place-items: center;
    }

    .brand-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .brand-text {
        color: #ffffff;
        font-size: 0.92rem;
        line-height: 1.25;
        font-weight: 800;
    }

    .sidebar-toggle {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border: 1px solid rgba(226, 232, 240, 0.22);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        font-size: 1rem;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .sidebar-toggle:hover {
        background: rgba(255, 255, 255, 0.14);
    }

    .sidebar-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 16px 18px 14px;
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: rgba(203, 213, 225, 0.35);
        border-radius: 999px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(203, 213, 225, 0.55);
    }

    .sidebar-label {
        margin: 14px 0 10px;
        color: #a8b3c5;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .sidebar-nav {
        display: grid;
        gap: 6px;
    }

    .sidebar-nav a,
    .sidebar-group-title {
        display: flex;
        align-items: center;
        min-height: 38px;
        padding: 9px 12px;
        border-radius: 12px;
        color: #e5e7eb;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .sidebar-nav a {
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    .sidebar-nav a.active {
        background: rgba(148, 163, 184, 0.22);
        color: #ffffff;
        font-weight: 700;
    }

    .sidebar-group {
        display: grid;
        gap: 4px;
        margin-top: 6px;
    }

    .sidebar-group-title {
        padding-left: 12px;
        color: #ffffff;
        font-weight: 800;
    }

    .sidebar-sublink {
        margin-left: 12px;
        font-size: 0.84rem !important;
        color: #dbe3ee !important;
    }

    .sidebar-account {
        padding: 12px 18px 18px;
        border-top: 1px solid rgba(148, 163, 184, 0.16);
        background: var(--sidebar-bg);
    }

    .sidebar-account .sidebar-label {
        margin-top: 0;
    }

    .logout-link {
        background: rgba(239, 68, 68, 0.10);
        color: #fecaca !important;
    }

    .logout-link:hover {
        background: rgba(239, 68, 68, 0.18) !important;
        color: #ffffff !important;
    }

    .sidebar-toggle-floating {
        position: fixed;
        top: 22px;
        left: 22px;
        z-index: 1000;
        width: 42px;
        height: 42px;
        display: none;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #ffffff;
        color: var(--text-main);
        font-size: 1.1rem;
        font-weight: 900;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
    }

    body.sidebar-collapsed .sidebar {
        transform: translateX(-100%);
    }

    body.sidebar-collapsed .sidebar-toggle-floating {
        display: inline-flex;
    }

    body.sidebar-collapsed .dashboard-main {
        margin-left: 0;
    }

    /* ==============================
       Layout principal
       ============================== */

    .dashboard-main {
        margin-left: var(--sidebar-width);
        padding: 32px 36px;
        transition: margin-left 0.25s ease;
    }

    .dashboard-topbar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 24px;
    }

    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .topbar-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 999px;
        background: #ffffff;
        color: var(--text-main);
        font-weight: 700;
        box-shadow: var(--shadow);
    }

    .user-name {
        font-weight: 800;
    }

    .role-pill {
        font-size: 0.82rem;
        text-transform: uppercase;
    }

    .role-administrador {
        background: #fee2e2;
        color: #b91c1c;
    }

    .role-supervisor {
        background: #e0f2fe;
        color: #0369a1;
    }

    .role-facturador {
        background: #ecfdf5;
        color: #15803d;
    }

    .role-usuario {
        background: #f3f4f6;
        color: #374151;
    }

    .dashboard-page-heading {
        margin-bottom: 28px;
    }

    .dashboard-page-heading h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .dashboard-page-heading p {
        margin: 0;
        color: var(--text-muted);
        font-size: 1rem;
    }

    /* ==============================
       Cards
       ============================== */

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 24px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 28px;
        margin-bottom: 28px;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 28px;
        margin-bottom: 28px;
    }

    .summary-card,
    .chart-card,
    .sales-card,
    .table-card,
    .quick-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .summary-card:hover,
    .chart-card:hover,
    .sales-card:hover,
    .table-card:hover,
    .quick-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
    }

    .clickable-card {
        cursor: pointer;
    }

    .summary-card p {
        margin: 0 0 12px;
        color: var(--text-muted);
    }

    .summary-card h2 {
        margin: 0 0 18px;
        font-size: 1.8rem;
    }

    .summary-card small {
        display: block;
        color: var(--text-muted);
        margin-top: 10px;
    }

    .positive {
        color: var(--success);
        font-weight: 800;
    }

    .negative {
        color: var(--danger);
        font-weight: 800;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 22px;
    }

    .card-header h3 {
        margin: 0 0 5px;
        color: var(--text-main);
    }

    .card-header span {
        color: var(--text-muted);
        font-size: 0.92rem;
    }

    .card-header a {
        color: var(--info);
        text-decoration: none;
        font-weight: 700;
        white-space: nowrap;
    }

    .card-header a:hover {
        text-decoration: underline;
    }

    .chart-card,
    .sales-card {
        min-height: 320px;
    }

    .chart-box {
        height: 260px;
    }

    canvas {
        max-height: 280px;
    }

    /* ==============================
       Tablas
       ============================== */

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        color: var(--text-muted);
        background: #f3f4f6;
        padding: 14px;
        font-size: 0.9rem;
    }

    td {
        padding: 16px 14px;
        border-bottom: 1px solid var(--border);
        font-size: 0.94rem;
    }

    table tbody tr {
        transition: background 0.2s ease;
    }

    table tbody tr:hover {
        background: #f8fafc;
    }

    .clickable-row {
        cursor: pointer;
    }

    .clickable-row:hover {
        background: #eef2ff;
    }

    .empty-table {
        text-align: center;
        color: var(--text-muted);
        padding: 40px 16px;
    }

    /* ==============================
       Acciones rápidas
       ============================== */

    .quick-card {
        display: grid;
        gap: 12px;
        align-content: start;
    }

    .quick-card h3 {
        margin: 0 0 8px;
    }

    .quick-card a {
        display: block;
        padding: 14px 16px;
        background: #f3f5f9;
        color: var(--text-main);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 700;
        transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .quick-card a:hover {
        transform: translateX(5px);
        background: #e0e7ff;
        color: #1d4ed8;
    }

    /* ==============================
       Responsive
       ============================== */

    @media (max-width: 1100px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid,
        .bottom-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-open .sidebar {
            transform: translateX(0);
        }

        body.sidebar-open .sidebar-toggle-floating {
            display: none;
        }

        .sidebar-toggle-floating {
            display: inline-flex;
        }

        .dashboard-main {
            margin-left: 0;
            padding: 24px 18px;
        }

        .dashboard-topbar {
            justify-content: flex-start;
            padding-left: 56px;
        }

        .topbar-pill {
            min-height: 36px;
            padding: 0 12px;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 640px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-page-heading h1 {
            font-size: 1.7rem;
        }

        .summary-card,
        .chart-card,
        .sales-card,
        .table-card,
        .quick-card {
            padding: 18px;
        }

        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
</style>