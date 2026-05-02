<header class="catalog-navbar">
    <div class="catalog-navbar-inner">
        <a href="index.php" class="catalog-brand">
            <img
                src="assets/img/icono.png"
                alt="Logo Panda Estampados y Kitsune"
                class="catalog-brand-logo">

            <span>Panda Estampados</span>
            <span class="catalog-brand-separator">/</span>
            <span>Kitsune</span>
        </a>

        <div class="catalog-navbar-actions">
            <?php if ($usuario): ?>
                <span class="catalog-session-label">
                    <?= htmlspecialchars($usuario["nombre"] ?? "Usuario") ?>
                </span>

                <a href="dashboard.php" class="catalog-navbar-btn">
                    Panel de gestión
                </a>
            <?php else: ?>
                <a href="login.php" class="catalog-navbar-btn">
                    Acceso trabajadores
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>