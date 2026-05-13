<?php
session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/repositories/FacturaEstadoHistorialRepository.php";

requireLogin();

$user = $_SESSION["user"];

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

$historialRepository = new FacturaEstadoHistorialRepository($connection);
$historialEstados = $historialRepository->obtenerHistorialGeneral();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de estados</title>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
    <?php require __DIR__ . "/partials/facturacion/facturas/styles.php"; ?>
    <?php require __DIR__ . "/partials/facturacion/facturas/detalle/styles.php"; ?>
</head>

<body class="dashboard-body">
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">
        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="dashboard-card facturas-hero">
            <p class="dashboard-eyebrow">Facturación</p>

            <h1 class="dashboard-title">Historial de estados</h1>

            <p class="dashboard-muted">
                Consulte los cambios registrados en pagos, producción, entregas y cancelaciones de facturas.
            </p>
        </section>

        <section class="invoice-timeline-card">
            <div class="invoice-section-header">
                <div>
                    <h3>Bitácora general</h3>
                    <p>Registro cronológico de acciones realizadas sobre las facturas.</p>
                </div>
            </div>

            <?php if (empty($historialEstados)): ?>
                <p class="empty-message">
                    Todavía no hay historial de estados registrado.
                </p>
            <?php else: ?>
                <div class="invoice-timeline">
                    <?php foreach ($historialEstados as $evento): ?>
                        <article class="invoice-timeline-item">
                            <div class="invoice-timeline-dot"></div>

                            <div class="invoice-timeline-content">
                                <div class="invoice-timeline-header">
                                    <strong>
                                        Factura #<?= (int)$evento["id_factura"] ?> ·
                                        <?= htmlspecialchars($evento["tipo_evento"]) ?>
                                    </strong>

                                    <span>
                                        <?= htmlspecialchars(date("d/m/Y H:i", strtotime($evento["fecha_evento"]))) ?>
                                    </span>
                                </div>

                                <p>
                                    <?= htmlspecialchars($evento["comentario"] ?? "Cambio registrado.") ?>
                                </p>

                                <div class="invoice-timeline-meta">
                                    <?php if (!empty($evento["estado_pago_nuevo"])): ?>
                                        <span>Pago: <?= htmlspecialchars($evento["estado_pago_nuevo"]) ?></span>
                                    <?php endif; ?>

                                    <?php if (!empty($evento["estado_produccion_nuevo"])): ?>
                                        <span>Producción: <?= htmlspecialchars($evento["estado_produccion_nuevo"]) ?></span>
                                    <?php endif; ?>

                                    <?php if ((float)($evento["monto_abonado"] ?? 0) > 0): ?>
                                        <span>Abono: C$ <?= number_format((float)$evento["monto_abonado"], 2) ?></span>
                                    <?php endif; ?>

                                    <?php if (isset($evento["saldo_nuevo"])): ?>
                                        <span>Saldo: C$ <?= number_format((float)$evento["saldo_nuevo"], 2) ?></span>
                                    <?php endif; ?>

                                    <a href="detalle_factura.php?id=<?= (int)$evento["id_factura"] ?>">
                                        Ver factura
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
</body>

</html>