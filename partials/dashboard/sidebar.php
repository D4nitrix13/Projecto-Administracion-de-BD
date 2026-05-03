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
                Inicio público
            </a>

            <a
                href="dashboard.php"
                class="<?= isActivePage("dashboard.php", $currentPage) ?>">
                Dashboard
            </a>

            <div class="sidebar-group">
                <span class="sidebar-group-title">Facturación</span>

                <a
                    href="nueva_factura.php"
                    class="sidebar-sublink <?= isActivePage("nueva_factura.php", $currentPage) ?>">
                    Nueva factura
                </a>

                <a
                    href="facturas.php"
                    class="sidebar-sublink <?= isActivePage("facturas.php", $currentPage) ?>">
                    Ver facturas
                </a>
            </div>

            <div class="sidebar-group">
                <span class="sidebar-group-title">Inventario</span>

                <a
                    href="productos.php"
                    class="sidebar-sublink <?= isActivePage("productos.php", $currentPage) ?>">
                    Productos
                </a>

                <a
                    href="categorias.php"
                    class="sidebar-sublink <?= isActivePage("categorias.php", $currentPage) ?>">
                    Categorías
                </a>

                <a
                    href="proveedores.php"
                    class="sidebar-sublink <?= isActivePage("proveedores.php", $currentPage) ?>">
                    Proveedores
                </a>
            </div>

            <a
                href="clientes.php"
                class="<?= isActivePage("clientes.php", $currentPage) ?>">
                Clientes
            </a>

            <?php if (($user["rol"] ?? "") === "Administrador"): ?>
                <a
                    href="usuarios.php"
                    class="<?= isActivePage("usuarios.php", $currentPage) ?>">
                    Trabajadores
                </a>

                <div class="sidebar-group">
                    <span class="sidebar-group-title">Análisis</span>

                    <a
                        href="compras.php"
                        class="sidebar-sublink <?= isActivePage("compras.php", $currentPage) ?>">
                        Historial de compras
                    </a>

                    <a
                        href="reportes.php"
                        class="sidebar-sublink <?= isActivePage("reportes.php", $currentPage) ?>">
                        Reportes generales
                    </a>
                </div>

                <div class="sidebar-group">
                    <span class="sidebar-group-title">Sistema</span>

                    <a
                        href="cambiar_numero_de_whatsapp.php"
                        class="sidebar-sublink <?= isActivePage("cambiar_numero_de_whatsapp.php", $currentPage) ?>">
                        Cambiar número de WhatsApp
                    </a>

                    <a
                        href="backups_manuales.php"
                        class="sidebar-sublink <?= isActivePage("backups_manuales.php", $currentPage) ?>">
                        Backups manuales
                    </a>

                    <a
                        href="programar_backups.php"
                        class="sidebar-sublink <?= isActivePage("programar_backups.php", $currentPage) ?>">
                        Programar backups
                    </a>

                    <a
                        href="restaurar_bd.php"
                        class="sidebar-sublink <?= isActivePage("restaurar_bd.php", $currentPage) ?>">
                        Restaurar base de datos
                    </a>

                    <a
                        href="logs_sistema.php"
                        class="sidebar-sublink <?= isActivePage("logs_sistema.php", $currentPage) ?>">
                        Logs del sistema
                    </a>

                    <a
                        href="archivos_wal.php"
                        class="sidebar-sublink <?= isActivePage("archivos_wal.php", $currentPage) ?>">
                        Archivos WAL
                    </a>

                    <a
                        href="mantenimiento_bd.php"
                        class="sidebar-sublink <?= isActivePage("mantenimiento_bd.php", $currentPage) ?>">
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
                Configuración
            </a>

            <a href="logout.php" class="logout-link">
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