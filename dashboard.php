<?php
session_start();
$pageTitle = "Panel de Control - Sistema de Facturación y Inventario Panda Estampados / Kitsune";

// Si no hay usuario en sesión, regresar al login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$connection = require "./sql/db.php";

// Es admin global si es Administrador y NO tiene sección asignada (id_seccion NULL)
$isAdminGlobal = ($user["rol"] === "Administrador" && $user["id_seccion"] === null);

// Obtener secciones para el texto de sección
$secciones = [];

if ($isAdminGlobal) {
    $stmtSec = $connection->query(
        "SELECT id_seccion, nombre FROM Seccion ORDER BY id_seccion"
    );
    $secciones = $stmtSec->fetchAll(PDO::FETCH_ASSOC);
} elseif ($user["id_seccion"] !== null) {
    $stmtSec = $connection->prepare(
        "SELECT id_seccion, nombre FROM Seccion WHERE id_seccion = :id"
    );
    $stmtSec->execute(["id" => $user["id_seccion"]]);
    $fila = $stmtSec->fetch(PDO::FETCH_ASSOC);
    if ($fila) $secciones[] = $fila;
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <!-- NAVBAR -->
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <!-- Bienvenida -->
        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Panel principal</p>
            <h1 class="dashboard-title">
                Bienvenido, <?= htmlspecialchars($user["nombre"]) ?>
            </h1>

            <!-- BLOQUE DE ROL + SECCIÓN -->
            <style>
                .dashboard-card.dashboard-welcome {
                    text-align: left;
                }

                .dashboard-card.dashboard-welcome .dashboard-role-line {
                    display: inline-flex;
                    align-items: center;
                    justify-content: flex-start;
                    gap: 12px;
                    margin-top: 12px;
                    flex-wrap: wrap;
                }

                .dashboard-card.dashboard-welcome .role-badge {
                    background: #2563eb;
                    color: #ffffff;
                    padding: 6px 14px;
                    border-radius: 999px;
                    font-size: 0.85rem;
                    font-weight: 600;
                    white-space: nowrap;
                }

                .dashboard-card.dashboard-welcome .dashboard-muted {
                    font-size: 0.9rem;
                    color: #6b7280;
                    white-space: nowrap;
                }
            </style>

            <div class="dashboard-role-line">
                <span class="role-badge">
                    <?= htmlspecialchars($user["rol"]) ?>
                </span>

                <?php if ($isAdminGlobal): ?>
                    <span class="dashboard-muted">
                        Acceso total a <strong>Panda Estampados</strong> y <strong>Kitsune</strong>.
                    </span>
                <?php elseif (!empty($secciones)): ?>
                    <span class="dashboard-muted">
                        Sección: <strong><?= htmlspecialchars($secciones[0]["nombre"]) ?></strong>
                    </span>
                <?php endif; ?>
            </div>
        </section>


        <!-- Módulos del sistema -->
        <section class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h2 class="dashboard-card-title">Módulos del sistema</h2>
                    <p class="dashboard-card-subtitle">
                        Acceso rápido a las funciones principales del sistema según su rol.
                    </p>
                </div>

                <?php if ($isAdminGlobal): ?>
                    <div class="chip-list">
                        <span class="chip chip-primary">Panda Estampados</span>
                        <span class="chip chip-primary">Kitsune</span>
                    </div>
                <?php else: ?>
                    <?php if (!empty($secciones)): ?>
                        <div class="chip-list">
                            <span class="chip chip-primary">
                                <?= htmlspecialchars($secciones[0]["nombre"]) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="modules-grid">

                <article class="module-card">
                    <h3>Facturación</h3>
                    <p class="module-desc">Gestiona las facturas emitidas y registra nuevas ventas.</p>
                    <div class="module-links">
                        <a href="nueva_factura.php">➜ Crear nueva factura</a>
                        <a href="facturas.php">➜ Ver historial de facturas</a>
                    </div>
                </article>

                <article class="module-card">
                    <h3>Clientes</h3>
                    <p class="module-desc">Administra la base de datos de clientes.</p>
                    <div class="module-links">
                        <a href="clientes.php">➜ Listado de clientes</a>
                        <a href="clientes.php">➜ Registrar / editar clientes</a>
                    </div>
                </article>

                <article class="module-card">
                    <h3>Inventario</h3>
                    <p class="module-desc">Controla productos, categorías y proveedores.</p>
                    <div class="module-links">
                        <a href="productos.php">➜ Productos</a>
                        <a href="categorias.php">➜ Categorías</a>
                        <a href="proveedores.php">➜ Proveedores</a>
                    </div>
                </article>

                <?php if ($user && ($user['rol'] ?? '') === 'Administrador'): ?>
                    <article class="module-card">
                        <h3>Reportes</h3>
                        <p class="module-desc">Consulta reportes de ventas.</p>
                        <div class="module-links">
                            <a href="compras.php">➜ Historial de compras</a>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if ($isAdminGlobal): ?>
                    <article class="module-card">
                        <h3>Trabajadores</h3>
                        <p class="module-desc">Gestiona cuentas de usuarios.</p>
                        <div class="module-links">
                            <a href="usuarios.php">➜ Gestionar trabajadores</a>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if ($user && ($user['rol'] ?? '') === 'Administrador'): ?>
                    <article class="module-card">
                        <h3>Sistema</h3>
                        <p class="module-desc">Opciones avanzadas del sistema.</p>
                        <div class="module-links">
                            <a href="respaldo_bd.php">➜ Respaldos de base de datos</a>
                            <a href="cambiar_numero_de_whatsapp.php">➜ Número de WhatsApp</a>
                        </div>
                    </article>
                <?php endif; ?>

                <article class="module-card">
                    <h3>Configurar cuenta</h3>
                    <p class="module-desc">Cambia tu nombre, correo y contraseña.</p>
                    <div class="module-links">
                        <a href="configurar_cuenta.php">➜ Ir a configuración de cuenta</a>
                    </div>
                </article>

            </div>
        </section>

    </main>
</body>

</html>