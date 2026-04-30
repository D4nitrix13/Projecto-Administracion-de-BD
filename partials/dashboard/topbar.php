<header class="topbar">
    <div>
        <h1>Dashboard</h1>
        <p>Resumen general de ventas, facturación e inventario.</p>
    </div>

    <div class="topbar-user">
        <span><?= date("d/m/Y") ?></span>
        <?php
        $rolNombre = $user["rol"] ?? match ((int)($user["id_rol"] ?? 0)) {
            1 => "Administrador",
            2 => "Supervisor",
            3 => "Facturador",
            default => "Usuario"
        };
        ?>

        <div class="topbar-user">
            <span class="user-name">
                <?= htmlspecialchars($user["nombre"]) ?>
            </span>

            <span class="user-role role-<?= strtolower($rolNombre) ?>">
                <?= htmlspecialchars($rolNombre) ?>
            </span>
        </div>
    </div>
</header>