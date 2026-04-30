<header class="topbar">
    <div>
        <h1>Dashboard</h1>
        <p>Resumen general de ventas, facturación e inventario.</p>
    </div>

    <div class="topbar-user">
        <span><?= date("d/m/Y") ?></span>
        <strong><?= htmlspecialchars($user["nombre"]) ?></strong>
    </div>
</header>