<aside class="sidebar">
    <div class="brand">
        <span class="brand-icon">◎</span>
        <strong>Panda <b>Px</b></strong>
    </div>

    <p class="sidebar-label">Principal</p>

    <nav class="sidebar-nav">
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="nueva_factura.php">Facturación</a>
        <a href="facturas.php">Órdenes</a>
        <a href="productos.php">Productos</a>
        <a href="categorias.php">Categorías</a>
        <a href="clientes.php">Clientes</a>
        <a href="proveedores.php">Proveedores</a>

        <?php if (($user["rol"] ?? "") === "Administrador"): ?>
            <a href="usuarios.php">Trabajadores</a>
            <a href="compras.php">Reportes</a>
            <a href="respaldo_bd.php">Sistema</a>
        <?php endif; ?>
    </nav>

    <p class="sidebar-label">Cuenta</p>

    <nav class="sidebar-nav">
        <a href="configurar_cuenta.php">Configuración</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</aside>