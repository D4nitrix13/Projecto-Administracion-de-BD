<?php
$currentPage = basename($_SERVER["PHP_SELF"]);

function isActivePage(string $page, string $currentPage): string
{
    return $page === $currentPage ? "active" : "";
}
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">
            <span class="brand-icon">
                <img src="assets/img/icono.png" alt="Logo">
            </span>

            <strong class="brand-text">
                Pandas Estampados & Kitsune
            </strong>
        </div>

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Ocultar o mostrar menú">
            ☰
        </button>
    </div>

    <div class="sidebar-scroll">
        <p class="sidebar-label">Principal</p>

        <nav class="sidebar-nav">
            <a
                href="index.php"
                class="<?= isActivePage("index.php", $currentPage) ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Inicio público
            </a>

            <a
                href="dashboard.php"
                class="<?= isActivePage("dashboard.php", $currentPage) ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>

            <div class="sidebar-group">
                <span class="sidebar-group-title">Facturación</span>

                <a
                    href="nueva_factura.php"
                    class="sidebar-sublink <?= isActivePage("nueva_factura.php", $currentPage) ?>">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    Nueva factura
                </a>

                <a
                    href="facturas.php"
                    class="sidebar-sublink <?= isActivePage("facturas.php", $currentPage) ?>">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Ver facturas
                </a>

                <a
                    href="historial_estados_facturas.php"
                    class="sidebar-sublink <?= isActivePage("historial_estados_facturas.php", $currentPage) ?>">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Historial de estados
                </a>
            </div>

            <div class="sidebar-group">
                <span class="sidebar-group-title">Inventario</span>

                <a
                    href="productos.php"
                    class="sidebar-sublink <?= isActivePage("productos.php", $currentPage) ?>">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    Productos
                </a>

                <a
                    href="categorias.php"
                    class="sidebar-sublink <?= isActivePage("categorias.php", $currentPage) ?>">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Categorías
                </a>

                <a
                    href="proveedores.php"
                    class="sidebar-sublink <?= isActivePage("proveedores.php", $currentPage) ?>">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    Proveedores
                </a>
            </div>


            <div class="sidebar-group">
                <span class="sidebar-group-title">Clientes</span>
                <a
                    href="clientes.php"
                    class="<?= isActivePage("clientes.php", $currentPage) ?>">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Ver listado de clientes
                </a>
            </div>


            <?php if (($user["rol"] ?? "") === "Administrador"): ?>
                <div class="sidebar-group">
                    <span class="sidebar-group-title">Análisis</span>

                    <a
                        href="compras.php"
                        class="sidebar-sublink <?= isActivePage("compras.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Historial de compras
                    </a>

                    <a
                        href="reportes.php"
                        class="sidebar-sublink <?= isActivePage("reportes.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Reportes generales
                    </a>
                </div>

                <div class="sidebar-group">
                    <span class="sidebar-group-title">Sistema</span>

                    <a
                        href="trabajadores.php"
                        class="sidebar-sublink <?= isActivePage("trabajadores.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        Trabajadores
                    </a>

                    <a
                        href="cambiar_numero_de_whatsapp.php"
                        class="sidebar-sublink <?= isActivePage("cambiar_numero_de_whatsapp.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        Cambiar número de WhatsApp
                    </a>

                    <a
                        href="limite_de_venta_cliente_fugaz.php"
                        class="sidebar-sublink <?= isActivePage("limite_de_venta_cliente_fugaz.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Límite de venta cliente fugaz
                    </a>

                    <a
                        href="auditoria_eliminados.php"
                        class="sidebar-sublink <?= isActivePage("auditoria_eliminados.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        Registros eliminados
                    </a>

                    <a
                        href="backups_manuales.php"
                        class="sidebar-sublink <?= isActivePage("backups_manuales.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Backups manuales
                    </a>

                    <a
                        href="programar_backups.php"
                        class="sidebar-sublink <?= isActivePage("programar_backups.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Programar backups
                    </a>

                    <a
                        href="restaurar_bd.php"
                        class="sidebar-sublink <?= isActivePage("restaurar_bd.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                        Restaurar base de datos
                    </a>

                    <a
                        href="logs_sistema.php"
                        class="sidebar-sublink <?= isActivePage("logs_sistema.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        Logs del sistema
                    </a>

                    <a
                        href="archivos_wal.php"
                        class="sidebar-sublink <?= isActivePage("archivos_wal.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        Archivos WAL
                    </a>

                    <a
                        href="mantenimiento_bd.php"
                        class="sidebar-sublink <?= isActivePage("mantenimiento_bd.php", $currentPage) ?>">
                        <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Mantenimiento BD
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </div>

    <div class="sidebar-account">
        <p class="sidebar-label">Cuenta</p>

        <nav class="sidebar-nav">
            <a
                href="configurar_cuenta.php"
                class="<?= isActivePage("configurar_cuenta.php", $currentPage) ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configuración
            </a>

            <a href="logout.php" class="logout-link">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Cerrar sesión
            </a>
        </nav>
    </div>
</aside>

<button
    type="button"
    class="sidebar-toggle-floating"
    id="sidebarToggleFloating"
    aria-label="Mostrar menú">
    ☰
</button>