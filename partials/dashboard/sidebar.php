<aside class="sidebar" id="sidebar">
    <div class="brand">
        <span class="brand-icon">
            <img src="assets/img/icono.png" alt="Logo">
        </span>

        <strong class="brand-text">
            Pandas Estampados & Kitsune
        </strong>
    </div>

    <div class="sidebar-scroll">

        <p class="sidebar-label">Principal</p>

        <nav class="sidebar-nav">
            <a class="active" href="dashboard.php">Dashboard</a>

            <div class="sidebar-group">
                <span class="sidebar-group-title">Facturación</span>

                <a href="nueva_factura.php" class="sidebar-sublink">
                    Nueva factura
                </a>

                <a href="facturas.php" class="sidebar-sublink">
                    Ver facturas
                </a>
            </div>

            <a href="productos.php">Productos</a>
            <a href="categorias.php">Categorías</a>
            <a href="clientes.php">Clientes</a>
            <a href="proveedores.php">Proveedores</a>

            <?php if (($user["rol"] ?? "") === "Administrador"): ?>
                <a href="usuarios.php">Trabajadores</a>
                <a href="compras.php">Reportes</a>

                <div class="sidebar-group">
                    <span class="sidebar-group-title">Sistema</span>

                    <a href="cambiar_numero_de_whatsapp.php" class="sidebar-sublink">
                        Cambiar número de WhatsApp
                    </a>

                    <a href="respaldo_bd.php" class="sidebar-sublink">
                        Respaldo Base de Datos
                    </a>
                </div>
            <?php endif; ?>
        </nav>

        <p class="sidebar-label">Cuenta</p>

        <nav class="sidebar-nav">
            <a href="configurar_cuenta.php">Configuración</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>

    </div>
</aside>

<button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Ocultar o mostrar menú">
    ☰
</button>