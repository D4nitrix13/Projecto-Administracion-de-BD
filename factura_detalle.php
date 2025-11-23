<?php
// factura_detalle.php
session_start();
$pageTitle = "Detalle de factura - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$connection = require "./sql/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    $_SESSION["flash_error"] = "Factura no válida.";
    header("Location: facturas.php");
    exit();
}

// Encabezado de factura
$stmt = $connection->prepare("
    SELECT 
        f.id_factura,
        f.fecha,
        f.subtotal,
        f.descuento,
        f.impuesto,
        f.total,
        c.nombres || ' ' || c.apellidos AS cliente,
        c.telefono,
        c.direccion,
        u.nombre AS usuario,
        s.nombre AS seccion
    FROM Factura f
    JOIN Cliente c ON f.id_cliente = c.id_cliente
    JOIN Usuario u ON f.id_usuario = u.id_usuario
    JOIN Seccion s ON f.id_seccion = s.id_seccion
    WHERE f.id_factura = :id
");
$stmt->execute([":id" => $id]);
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$factura) {
    $_SESSION["flash_error"] = "Factura no encontrada.";
    header("Location: facturas.php");
    exit();
}

// Detalles
$stmtDet = $connection->prepare("
    SELECT 
        df.id_detalle,
        p.codigo,
        p.nombre,
        df.cantidad,
        df.precio_unitario,
        df.descuento_linea,
        df.total_linea
    FROM DetalleFactura df
    JOIN Producto p ON df.id_producto = p.id_producto
    WHERE df.id_factura = :id
");
$stmtDet->execute([":id" => $id]);
$detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <!-- CABECERA PRINCIPAL -->
        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Facturación</p>
            <h1 class="dashboard-title">Detalle de la factura #<?= (int)$factura["id_factura"] ?></h1>

            <p class="dashboard-muted">
                Revise la información completa de esta factura, incluyendo cliente, productos y totales.
            </p>

            <a href="facturas.php" class="back-link">
                ← Volver al historial de facturas
            </a>
        </section>

        <!-- CONTENIDO DETALLE FACTURA -->
        <section class="dashboard-card factura-card">

            <!-- BLOQUE SUPERIOR: RESUMEN + CLIENTE -->
            <div class="factura-layout">

                <!-- Resumen de factura -->
                <div class="factura-panel factura-panel-main">
                    <div class="factura-header-row">
                        <div>
                            <p class="factura-label">Factura</p>
                            <h2 class="factura-number">#<?= (int)$factura["id_factura"] ?></h2>
                        </div>
                        <div class="factura-chips">
                            <span class="chip chip-primary">
                                <?= htmlspecialchars($factura["seccion"]) ?>
                            </span>
                            <span class="chip">
                                Registrada por: <?= htmlspecialchars($factura["usuario"]) ?>
                            </span>
                        </div>
                    </div>

                    <div class="factura-meta">
                        <div class="factura-meta-item">
                            <span class="factura-meta-label">Fecha y hora</span>
                            <span class="factura-meta-value">
                                <?= date("d/m/Y H:i", strtotime($factura["fecha"])) ?>
                            </span>
                        </div>
                        <div class="factura-meta-item">
                            <span class="factura-meta-label">Subtotal</span>
                            <span class="factura-meta-value">
                                C$ <?= number_format((float)$factura["subtotal"], 2) ?>
                            </span>
                        </div>
                        <div class="factura-meta-item">
                            <span class="factura-meta-label">Impuesto</span>
                            <span class="factura-meta-value">
                                C$ <?= number_format((float)$factura["impuesto"], 2) ?>
                            </span>
                        </div>
                        <div class="factura-meta-item">
                            <span class="factura-meta-label">Total</span>
                            <span class="factura-total">
                                C$ <?= number_format((float)$factura["total"], 2) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Datos del cliente -->
                <div class="factura-panel factura-panel-client">
                    <p class="factura-block-title">Cliente</p>

                    <div class="factura-client-row">
                        <span class="factura-client-label">Nombre</span>
                        <span class="factura-client-value"><?= htmlspecialchars($factura["cliente"]) ?></span>
                    </div>
                    <div class="factura-client-row">
                        <span class="factura-client-label">Teléfono</span>
                        <span class="factura-client-value">
                            <?= $factura["telefono"] ? htmlspecialchars($factura["telefono"]) : "—" ?>
                        </span>
                    </div>
                    <div class="factura-client-row">
                        <span class="factura-client-label">Dirección</span>
                        <span class="factura-client-value">
                            <?= $factura["direccion"] ? htmlspecialchars($factura["direccion"]) : "—" ?>
                        </span>
                    </div>
                </div>

            </div>

            <!-- TABLA DE PRODUCTOS -->
            <div class="factura-table-wrapper">
                <h3 class="factura-block-title" style="margin-bottom:12px;">Productos facturados</h3>

                <div class="table-wrapper">
                    <table class="table-products">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Cant.</th>
                                <th>Precio unitario</th>
                                <th>Desc. línea</th>
                                <th>Total línea</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detalles)): ?>
                                <tr>
                                    <td colspan="7">Esta factura no tiene detalles registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($detalles as $i => $det): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($det["codigo"]) ?></td>
                                        <td><?= htmlspecialchars($det["nombre"]) ?></td>
                                        <td><?= (int)$det["cantidad"] ?></td>
                                        <td>C$ <?= number_format((float)$det["precio_unitario"], 2) ?></td>
                                        <td>C$ <?= number_format((float)$det["descuento_linea"], 2) ?></td>
                                        <td>C$ <?= number_format((float)$det["total_linea"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BLOQUE DE TOTALES FINALES -->
            <div class="factura-totales-bar">
                <div class="factura-totales-list">
                    <div class="factura-tot-row">
                        <span>Subtotal</span>
                        <span>C$ <?= number_format((float)$factura["subtotal"], 2) ?></span>
                    </div>
                    <div class="factura-tot-row">
                        <span>Descuento</span>
                        <span>- C$ <?= number_format((float)$factura["descuento"], 2) ?></span>
                    </div>
                    <div class="factura-tot-row">
                        <span>Impuesto</span>
                        <span>C$ <?= number_format((float)$factura["impuesto"], 2) ?></span>
                    </div>
                    <div class="factura-tot-row factura-tot-row-total">
                        <span>Total</span>
                        <span>C$ <?= number_format((float)$factura["total"], 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- ✅ NUEVA BARRA DE ACCIONES CON BOTÓN PARA IMPRIMIR -->
            <div class="factura-actions" style="margin-top:20px; display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <a href="facturas.php" class="back-link" style="margin:0;">
                    ← Volver al historial de facturas
                </a>

                <!-- AQUÍ REDIRIGES A LA PÁGINA QUE IMPRIME LA FACTURA -->
                <a
                    href="factura_imprimir.php?id=<?= (int)$factura['id_factura'] ?>"
                    class="btn-primary"
                    style="text-decoration:none; padding:8px 16px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center;">
                    Redirigir a imprimir factura
                </a>
            </div>
            <!-- FIN NUEVA BARRA DE ACCIONES -->

        </section>

    </main>

</body>

</html>