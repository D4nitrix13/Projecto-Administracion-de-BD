<?php

// * Stored function or procedure has been executed

session_start();

$pageTitle = "Detalle de compra - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireLogin();

$user = $_SESSION["user"];

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

$id_compra = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id_compra <= 0) {
    $compra   = null;
    $detalles = [];
    $error    = "Identificador de compra inválido.";
} else {
    $error = null;

    $stmtCompra = $connection->prepare("
        SELECT
            id_compra,
            fecha,
            total,
            proveedor,
            proveedor_telefono,
            proveedor_email,
            usuario
        FROM obtener_compra_por_id(:id_compra)
    ");

    $stmtCompra->execute([
        ":id_compra" => $id_compra,
    ]);

    $compra = $stmtCompra->fetch(PDO::FETCH_ASSOC);

    if (!$compra) {
        $error    = "No se encontró la compra solicitada.";
        $detalles = [];
    } else {
        $stmtDet = $connection->prepare("
            SELECT
                id_detalle,
                cantidad,
                costo_unitario,
                total_linea,
                producto_codigo,
                producto_nombre
            FROM obtener_detalles_compra(:id_compra)
        ");

        $stmtDet->execute([
            ":id_compra" => $id_compra,
        ]);

        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
    }
}

$subtotal = 0.0;

foreach ($detalles as $detalle) {
    $subtotal += (float) $detalle["total_linea"];
}

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/analisis/compras/detalle/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="purchase-page-heading">
            <div>
                <p class="dashboard-eyebrow">Inventario</p>

                <?php if (!empty($compra)): ?>
                    <h1 class="dashboard-title">
                        Detalle de compra #<?= (int)$compra["id_compra"] ?>
                    </h1>

                    <p class="dashboard-muted">
                        Revise los productos comprados y cómo se actualizó el inventario.
                    </p>
                <?php else: ?>
                    <h1 class="dashboard-title">
                        Detalle de compra
                    </h1>

                    <p class="dashboard-muted">
                        No fue posible cargar la información de la compra solicitada.
                    </p>
                <?php endif; ?>
            </div>

            <a href="compras.php" class="btn-secondary-inline">
                Volver al historial
            </a>
        </section>

        <section class="purchase-detail-card">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($compra)): ?>

                <div class="purchase-summary-grid">
                    <article class="purchase-summary-card">
                        <span>Proveedor</span>

                        <strong>
                            <?= htmlspecialchars($compra["proveedor"]) ?>
                        </strong>

                        <p>
                            <?php if (!empty($compra["proveedor_telefono"])): ?>
                                Teléfono: <?= htmlspecialchars($compra["proveedor_telefono"]) ?><br>
                            <?php endif; ?>

                            <?php if (!empty($compra["proveedor_email"])): ?>
                                Email: <?= htmlspecialchars($compra["proveedor_email"]) ?>
                            <?php endif; ?>

                            <?php if (empty($compra["proveedor_telefono"]) && empty($compra["proveedor_email"])): ?>
                                Sin datos de contacto registrados.
                            <?php endif; ?>
                        </p>
                    </article>

                    <article class="purchase-summary-card">
                        <span>Datos de la compra</span>

                        <strong>
                            <?= date("d/m/Y H:i", strtotime($compra["fecha"])) ?>
                        </strong>

                        <p>
                            Registrada por:
                            <b><?= htmlspecialchars($compra["usuario"]) ?></b>
                        </p>
                    </article>

                    <article class="purchase-summary-card purchase-summary-total">
                        <span>Total de la compra</span>

                        <strong>
                            C$ <?= number_format((float)$compra["total"], 2) ?>
                        </strong>

                        <p>
                            Monto registrado en el inventario.
                        </p>
                    </article>
                </div>

                <section class="purchase-products-section">
                    <div class="section-heading">
                        <div>
                            <h2>Productos comprados</h2>

                            <p>
                                Cada línea muestra el código, producto, cantidad comprada y costo registrado.
                            </p>
                        </div>
                    </div>

                    <?php if (empty($detalles)): ?>
                        <p class="empty-message">
                            No se encontraron líneas de detalle para esta compra.
                        </p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="purchase-products-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Costo unitario</th>
                                        <th>Total línea</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($detalles as $detalle): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($detalle["producto_codigo"]) ?>
                                            </td>

                                            <td>
                                                <strong>
                                                    <?= htmlspecialchars($detalle["producto_nombre"]) ?>
                                                </strong>
                                            </td>

                                            <td class="text-center">
                                                <?= (int)$detalle["cantidad"] ?>
                                            </td>

                                            <td>
                                                C$ <?= number_format((float)$detalle["costo_unitario"], 2) ?>
                                            </td>

                                            <td>
                                                <strong>
                                                    C$ <?= number_format((float)$detalle["total_linea"], 2) ?>
                                                </strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="4">Total calculado</th>
                                        <th>C$ <?= number_format($subtotal, 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

            <?php endif; ?>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>