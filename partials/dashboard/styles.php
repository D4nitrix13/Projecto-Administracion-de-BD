<style>
    .dashboard-body {
        margin: 0;
        background: #f3f5f9;
        color: #111827;
        font-family: Arial, sans-serif;
    }

    .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        width: 250px;
        background: #243044;
        color: #fff;
        padding: 32px 28px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        margin-bottom: 60px;
    }

    .brand b {
        color: #ff2f63;
    }

    .brand-icon {
        color: #ff2f63;
        font-size: 1.5rem;
    }

    .sidebar-label {
        color: #9ca3af;
        margin: 28px 0 14px;
    }

    .sidebar-nav {
        display: grid;
        gap: 8px;
    }

    .sidebar-nav a {
        color: #f9fafb;
        text-decoration: none;
        padding: 13px 16px;
        border-radius: 12px;
    }

    .sidebar-nav a:hover,
    .sidebar-nav a.active {
        background: rgba(255, 255, 255, 0.1);
    }

    .dashboard-main {
        margin-left: 306px;
        padding: 36px 42px;
    }

    .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .topbar h1 {
        margin: 0;
        font-size: 1.9rem;
    }

    .topbar p {
        color: #6b7280;
        margin: 6px 0 0;
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .topbar-user span,
    .topbar-user strong {
        background: #fff;
        padding: 10px 18px;
        border-radius: 999px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
        margin-bottom: 24px;
    }

    .summary-card,
    .chart-card,
    .sales-card,
    .table-card,
    .quick-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .summary-card p {
        margin: 0 0 12px;
        color: #6b7280;
    }

    .summary-card h2 {
        margin: 0 0 22px;
        font-size: 1.8rem;
    }

    .summary-card small {
        display: block;
        color: #6b7280;
        margin-top: 10px;
    }

    .positive {
        color: #22c55e;
        font-weight: 700;
    }

    .negative {
        color: #ff2f63;
        font-weight: 700;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
    }

    .card-header h3 {
        margin: 0;
    }

    .card-header span,
    .card-header a {
        color: #6b7280;
        text-decoration: none;
    }

    .fake-chart {
        height: 230px;
        display: flex;
        align-items: end;
        gap: 22px;
        border-bottom: 1px solid #e5e7eb;
        padding: 20px;
    }

    .fake-chart div {
        flex: 1;
        background: linear-gradient(180deg, #ff2f63, #7c3aed);
        border-radius: 999px 999px 0 0;
    }

    .chart-days {
        display: flex;
        justify-content: space-around;
        color: #6b7280;
        font-size: 0.85rem;
        margin-top: 14px;
    }

    .donut {
        width: 220px;
        height: 220px;
        margin: 20px auto;
        border-radius: 50%;
        background: conic-gradient(#ff2f63 0 45%, #7c3aed 45% 75%, #fb923c 75% 100%);
        display: grid;
        place-items: center;
    }

    .donut div {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: #fff;
        display: grid;
        place-items: center;
        text-align: center;
    }

    .donut strong,
    .donut small {
        display: block;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        color: #6b7280;
        background: #f3f4f6;
        padding: 14px;
    }

    td {
        padding: 16px 14px;
        border-bottom: 1px solid #e5e7eb;
    }

    .quick-card {
        display: grid;
        gap: 12px;
        align-content: start;
    }

    .quick-card h3 {
        margin-top: 0;
    }

    .quick-card a {
        display: block;
        padding: 14px 16px;
        background: #f3f5f9;
        color: #111827;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
    }

    .quick-card a:hover {
        background: #e0e7ff;
        color: #1d4ed8;
    }

    @media (max-width: 1000px) {
        .sidebar {
            position: static;
            width: auto;
        }

        .dashboard-main {
            margin-left: 0;
        }

        .summary-grid,
        .dashboard-grid,
        .bottom-grid {
            grid-template-columns: 1fr;
        }
    }

    .summary-card,
    .chart-card,
    .sales-card,
    .table-card,
    .quick-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .summary-card:hover,
    .chart-card:hover,
    .sales-card:hover,
    .table-card:hover,
    .quick-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.14);
    }

    table tbody tr {
        transition: background 0.2s ease;
    }

    table tbody tr:hover {
        background: #f8fafc;
    }

    .quick-card a {
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .quick-card a:hover {
        transform: translateX(5px);
    }

    canvas {
        max-height: 280px;
    }


    /** */
    .dashboard-grid,
    .bottom-grid {
        display: grid;
        gap: 28px;
        margin-bottom: 28px;
    }

    .dashboard-grid {
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
    }

    .bottom-grid {
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
    }

    .table-card,
    .quick-card,
    .chart-card,
    .sales-card {
        min-height: 260px;
    }

    .clickable-row {
        cursor: pointer;
    }

    .clickable-row:hover {
        background: #eef2ff;
    }

    .card-header a {
        font-weight: 600;
    }

    .chart-card,
    .sales-card {
        cursor: pointer;
    }

    .chart-card canvas,
    .sales-card canvas {
        height: 260px !important;
    }

    .dashboard-grid,
    .bottom-grid {
        gap: 32px;
        margin-bottom: 32px;
    }

    .chart-box {
        height: 260px;
    }

    .clickable-card {
        cursor: pointer;
    }

    .clickable-row {
        cursor: pointer;
    }

    .clickable-row:hover {
        background: #eef2ff;
    }

    .empty-table {
        text-align: center;
        color: #6b7280;
        padding: 40px 16px;
    }

    .summary-card,
    .chart-card,
    .sales-card,
    .table-card,
    .quick-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .summary-card:hover,
    .chart-card:hover,
    .sales-card:hover,
    .table-card:hover,
    .quick-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.14);
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-name {
        background: #f3f4f6;
        padding: 10px 16px;
        border-radius: 999px;
        font-weight: 700;
        color: #111827;
    }

    .user-role {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    /* Colores por rol */
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

    .sidebar-group {
        margin-top: 8px;
    }

    .sidebar-group-title {
        display: block;
        padding: 12px 16px 8px;
        color: #e5e7eb;
        font-weight: 600;
    }

    .sidebar-sublink {
        display: block;
        margin-left: 14px;
        padding: 10px 16px;
        border-radius: 10px;
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.92rem;
    }

    .sidebar-sublink:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    .brand-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-icon img {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }
</style>