<header class="dashboard-topbar">
    <?php
    $rolNombre = $user["rol"] ?? match ((int)($user["id_rol"] ?? 0)) {
        1 => "Administrador",
        2 => "Supervisor",
        3 => "Facturador",
        default => "Usuario"
    };

    $rolClase = strtolower($rolNombre);
    ?>

    <div class="topbar-actions">
        <span class="topbar-pill">
            <?= date("d/m/Y") ?>
        </span>

        <span class="topbar-pill user-name">
            <?= htmlspecialchars($user["nombre"] ?? "Usuario") ?>
        </span>

        <span class="topbar-pill role-pill role-<?= htmlspecialchars($rolClase) ?>">
            <?= htmlspecialchars($rolNombre) ?>
        </span>
    </div>
</header>