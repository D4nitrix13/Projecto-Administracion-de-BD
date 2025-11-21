<?php
session_start();
$pageTitle = "Detalle de compra - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
/** @var PDO $connection */
$connection = require "./sql/db.php";

// 1) Leer id_compra desde GET
$id_compra = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id_compra <= 0) {
    $compra   = null;
    $detalles = [];
    $error    = "Identificador de compra inválido.";
} else {
    $error = null;

    // 2) Datos generales de la compra
    $stmtCompra = $connection->prepare("
        SELECT 
            c.id_compra,
            c.fecha,
            c.total,
            p.nombre  AS proveedor,
            p.telefono AS proveedor_telefono,
            p.email    AS proveedor_email,
            u.nombre   AS usuario
        FROM Compra c
        INNER JOIN Proveedor p ON c.id_proveedor = p.id_proveedor
        INNER JOIN Usuario   u ON c.id_usuario   = u.id_usuario
        WHERE c.id_compra = :id
    ");
    $stmtCompra->execute([":id" => $id_compra]);
    $compra = $stmtCompra->fetch(PDO::FETCH_ASSOC);

    if (!$compra) {
        $error    = "No se encontró la compra solicitada.";
        $detalles = [];
    } else {
        // 3) Detalle de productos
        $stmtDet = $connection->prepare("
            SELECT 
                dc.id_detalle,
                dc.cantidad,
                dc.costo_unitario,
                dc.total_linea,
                pr.codigo AS producto_codigo,
                pr.nombre AS producto_nombre
            FROM DetalleCompra dc
            INNER JOIN Producto pr ON dc.id_producto = pr.id_producto
            WHERE dc.id_compra = :id
            ORDER BY dc.id_detalle
        ");
        $stmtDet->execute([":id" => $id_compra]);
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <!-- Estilos específicos de esta pantalla -->
        <style>
            .purchase-page-header-extra {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 12px;
            }

            .purchase-page-header-extra .back-link-btn {
                font-size: 0.9rem;
                color: #2563eb;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .purchase-page-header-extra .back-link-btn:hover {
                text-decoration: underline;
            }

            .purchase-summary {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
                gap: 18px;
                margin-bottom: 20px;
            }

            .purchase-summary-card {
                border-radius: 14px;
                border: 1px solid rgba(15, 23, 42, 0.06);
                background: #f9fafb;
                padding: 14px 16px;
            }

            .purchase-summary-title {
                font-size: 0.78rem;
                letter-spacing: .06em;
                text-transform: uppercase;
                color: #9ca3af;
                margin-bottom: 6px;
            }

            .purchase-summary-main {
                font-size: 0.95rem;
                color: #111827;
                margin-bottom: 6px;
            }

            .purchase-summary-meta {
                font-size: 0.85rem;
                color: #6b7280;
            }

            .purchase-total-chip {
                font-size: 1.05rem;
                font-weight: 700;
                color: #16a34a;
            }

            .purchase-total-label {
                font-size: 0.86rem;
                color: #6b7280;
            }

            .table-products tfoot th {
                font-weight: 600;
            }

            .table-products tfoot tr {
                background: #f3f4f6;
            }
        </style>

        <!-- Cabecera -->
        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Inventario</p>

            <?php if (!empty($compra)): ?>
                <h1 class="dashboard-title">
                    Detalle de compra #<?= (int)$compra["id_compra"] ?>
                </h1>
                <p class="dashboard-muted">
                    Revise los productos comprados y cómo se actualizó el inventario.
                </p>
            <?php else: ?>
                <h1 class="dashboard-title">Detalle de compra</h1>
            <?php endif; ?>

            <div class="purchase-page-header-extra">
                <a href="compras.php" class="back-link-btn">
                    ← Volver al historial de compras
                </a>
            </div>
        </section>

        <!-- Contenido -->
        <section class="dashboard-card">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($compra)): ?>

                <!-- Resumen en 3 tarjetas -->
                <div class="purchase-summary">
                    <div class="purchase-summary-card">
                        <div class="purchase-summary-title">Proveedor</div>
                        <div class="purchase-summary-main">
                            <?= htmlspecialchars($compra["proveedor"]) ?>
                        </div>
                        <div class="purchase-summary-meta">
                            <?php if (!empty($compra["proveedor_telefono"])): ?>
                                Tel: <?= htmlspecialchars($compra["proveedor_telefono"]) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($compra["proveedor_email"])): ?>
                                Email: <?= htmlspecialchars($compra["proveedor_email"]) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="purchase-summary-card">
                        <div class="purchase-summary-title">Datos de la compra</div>
                        <div class="purchase-summary-main">
                            Fecha: <?= date("d/m/Y H:i", strtotime($compra["fecha"])) ?>
                        </div>
                        <div class="purchase-summary-meta">
                            Registrada por: <strong><?= htmlspecialchars($compra["usuario"]) ?></strong>
                        </div>
                    </div>

                    <div class="purchase-summary-card">
                        <div class="purchase-summary-title">Total de la compra</div>
                        <div class="purchase-summary-main purchase-total-chip">
                            C$ <?= number_format((float)$compra["total"], 2) ?>
                        </div>
                        <div class="purchase-summary-meta purchase-total-label">
                            Monto registrado en el inventario
                        </div>
                    </div>
                </div>

                <!-- Detalle de productos -->
                <h2 class="dashboard-card-title" style="margin-bottom:10px;">Productos comprados</h2>
                <p class="dashboard-muted" style="margin-bottom:12px;">
                    Cada línea muestra el código, descripción, cantidad comprada y el costo asumido por unidad.
                </p>

                <?php if (empty($detalles)): ?>
                    <p class="dashboard-muted">
                        No se encontraron líneas de detalle para esta compra.
                    </p>
                <?php else: ?>
                    <?php
                    $subtotal = 0.0;
                    foreach ($detalles as $d) {
                        $subtotal += (float)$d["total_linea"];
                    }
                    ?>
                    <div class="table-wrapper">
                        <table class="table-products">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th style="text-align:center;">Cantidad</th>
                                    <th>Costo unitario</th>
                                    <th>Total línea</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $det): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($det["producto_codigo"]) ?></td>
                                        <td><?= htmlspecialchars($det["producto_nombre"]) ?></td>
                                        <td style="text-align:center;"><?= (int)$det["cantidad"] ?></td>
                                        <td>C$ <?= number_format((float)$det["costo_unitario"], 2) ?></td>
                                        <td>C$ <?= number_format((float)$det["total_linea"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right;">Total calculado</th>
                                    <th>C$ <?= number_format($subtotal, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>