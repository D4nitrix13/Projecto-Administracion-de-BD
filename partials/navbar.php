<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

if (!isset($user) && isset($_SESSION["user"])) {
    $user = $_SESSION["user"];
}
?>

<style>
    .navbar-logo {
        height: 28px;
        width: 28px;
        object-fit: cover;
        margin-right: 8px;
        vertical-align: middle;
        border-radius: 50%;
    }

    /* Menús ocultos por defecto */
    .dropdown-menu,
    .user-menu {
        display: none;
    }

    /* Menú abierto permanente */
    .dropdown.open .dropdown-menu,
    .user-dropdown.open .user-menu {
        display: block !important;
    }
</style>

<nav class="navbar">
    <div>

        <span class="navbar-brand">
            <img src="icono.png" alt="Logo" class="navbar-logo">
            Panda Estampados / Kitsune
        </span>

        <a class="nav-item" href="dashboard.php">Inicio</a>

        <div class="dropdown">
            <button class="dropdown-btn">Facturación ▾</button>
            <div class="dropdown-menu">
                <a href="facturas.php">Ver facturas</a>
                <a href="nueva_factura.php">Nueva factura</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropdown-btn">Inventario ▾</button>
            <div class="dropdown-menu">
                <a href="productos.php">Productos</a>
                <a href="categorias.php">Categorías</a>
                <a href="proveedores.php">Proveedores</a>
            </div>
        </div>

        <?php if ($user && ($user['rol'] ?? '') === 'Administrador'): ?>
            <div class="dropdown">
                <button class="dropdown-btn">Reportes ▾</button>
                <div class="dropdown-menu">
                    <a href="compras.php">Historial de compras</a>
                </div>
            </div>
        <?php endif; ?>

        <a class="nav-item" href="clientes.php">Clientes</a>

        <?php if ($user && ($user['rol'] ?? '') === 'Administrador'): ?>
            <a class="nav-item" href="usuarios.php">Trabajadores</a>
        <?php endif; ?>

        <?php if ($user && ($user['rol'] ?? '') === 'Administrador'): ?>
            <div class="dropdown">
                <button class="dropdown-btn">Sistema ▾</button>
                <div class="dropdown-menu">
                    <a href="respaldo_bd.php">Respaldos BD</a>
                    <a href="cambiar_numero_de_whatsapp.php">Numero WhatsApp</a>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <div class="navbar-right">

        <div class="dropdown user-dropdown">
            <button class="user-btn">
                <?= htmlspecialchars($user["nombre"] ?? "Usuario") ?> ▾
            </button>

            <div class="dropdown-menu user-menu">
                <a href="configurar_cuenta.php">Configurar Cuenta</a>
                <a class="logout" href="logout.php">Salir</a>
            </div>
        </div>

    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        // Botones de todos los dropdowns
        document.querySelectorAll(".dropdown-btn, .user-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();

                const parent = btn.parentElement;

                // Cierra todos antes de abrir el seleccionado
                document.querySelectorAll(".dropdown.open, .user-dropdown.open")
                    .forEach(d => d.classList.remove("open"));

                // Abrir/cerrar el actual
                parent.classList.toggle("open");
            });
        });

        // Click fuera → cerrar todo
        document.addEventListener("click", () => {
            document.querySelectorAll(".dropdown.open, .user-dropdown.open")
                .forEach(d => d.classList.remove("open"));
        });
    });
</script>