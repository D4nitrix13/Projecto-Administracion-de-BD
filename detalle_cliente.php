<?php

session_start();

$pageTitle = "Detalle del cliente - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireLogin();

$user = $_SESSION["user"];
$connection = require __DIR__ . "/sql/db.php";

require_once __DIR__ . "/helpers/format.php";
require __DIR__ . "/partials/detalle-cliente/queries.php";

$cliente = $cliente ?? $clienteDetalle ?? $detalleCliente ?? null;

$facturasCliente = $facturasCliente
    ?? $facturasRecientes
    ?? $facturas
    ?? [];

$totalFacturas = $totalFacturas
    ?? $cantidadFacturas
    ?? count($facturasCliente);

$totalComprado = $totalComprado
    ?? $montoTotalComprado
    ?? $totalVentasCliente
    ?? 0;

$promedioCompra = $promedioCompra
    ?? $promedioCompras
    ?? $ticketPromedio
    ?? 0;

function clienteValue(?array $cliente, array $keys, string $default = "No registrado"): string
{
    if ($cliente === null) {
        return $default;
    }

    foreach ($keys as $key) {
        if (isset($cliente[$key]) && trim((string)$cliente[$key]) !== "") {
            return trim((string)$cliente[$key]);
        }
    }

    return $default;
}

function clienteMoney(float|int|string|null $value): string
{
    return "C$ " . number_format((float)($value ?? 0), 2);
}

function clienteDate(?string $date): string
{
    if ($date === null || trim($date) === "") {
        return "No registrado";
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return $date;
    }

    return date("d/m/Y H:i", $timestamp);
}

$nombres = clienteValue($cliente, ["nombres", "nombre", "nombre_cliente"], "");
$apellidos = clienteValue($cliente, ["apellidos", "apellido"], "");
$nombreCompleto = trim($nombres . " " . $apellidos);

if ($nombreCompleto === "") {
    $nombreCompleto = clienteValue($cliente, ["cliente", "nombre_completo"], "Cliente no encontrado");
}

$tipoCliente = clienteValue($cliente, ["tipo_cliente", "tipo", "nombre_tipo"], "Cliente");
$identificacion = clienteValue($cliente, ["identificacion", "cedula", "ruc"]);
$telefono = clienteValue($cliente, ["telefono", "celular"]);
$direccion = clienteValue($cliente, ["direccion"]);
$fechaRegistro = clienteValue($cliente, ["fecha_registro", "created_at", "fecha_creacion"], "");
$idCliente = (int) clienteValue($cliente, ["id_cliente", "id"], "0");

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/clientes/editar/styles.php" ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/dashboard/topbar.php"; ?>

        <section class="cliente-page-heading">
            <p class="dashboard-eyebrow">Clientes</p>

            <h1 class="dashboard-title">
                Detalle del cliente
            </h1>

            <p class="dashboard-muted">
                Consulte la información general del cliente y el resumen de sus compras registradas.
            </p>
        </section>

        <section class="cliente-detail-card">

            <div class="cliente-detail-header">
                <div>
                    <p class="cliente-label">Cliente</p>

                    <h2>
                        <?= htmlspecialchars($nombreCompleto) ?>
                    </h2>

                    <span class="cliente-type-badge">
                        <?= htmlspecialchars($tipoCliente) ?>
                    </span>
                </div>

                <div class="cliente-detail-actions">
                    <a href="clientes.php" class="btn-secondary-inline">
                        Volver a clientes
                    </a>

                    <?php if ($idCliente > 0): ?>
                        <a href="editar_cliente.php?id=<?= $idCliente ?>" class="btn-primary-inline">
                            Editar cliente
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cliente-stats-grid">
                <article class="cliente-stat-card">
                    <span>Total facturas</span>
                    <strong><?= (int)$totalFacturas ?></strong>
                </article>

                <article class="cliente-stat-card">
                    <span>Total comprado</span>
                    <strong><?= clienteMoney($totalComprado) ?></strong>
                </article>

                <article class="cliente-stat-card">
                    <span>Promedio compra</span>
                    <strong><?= clienteMoney($promedioCompra) ?></strong>
                </article>
            </div>

            <div class="cliente-detail-grid">

                <section class="cliente-info-panel">
                    <div class="section-heading">
                        <div>
                            <h3>Información general</h3>
                            <p>Datos principales registrados para este cliente.</p>
                        </div>
                    </div>

                    <div class="cliente-info-list">
                        <div class="cliente-info-row">
                            <span>Identificación</span>
                            <strong><?= htmlspecialchars($identificacion) ?></strong>
                        </div>

                        <div class="cliente-info-row">
                            <span>Teléfono</span>
                            <strong><?= htmlspecialchars($telefono) ?></strong>
                        </div>

                        <div class="cliente-info-row">
                            <span>Dirección</span>
                            <strong><?= htmlspecialchars($direccion) ?></strong>
                        </div>

                        <div class="cliente-info-row">
                            <span>Tipo de cliente</span>
                            <strong><?= htmlspecialchars($tipoCliente) ?></strong>
                        </div>

                        <div class="cliente-info-row">
                            <span>Fecha de registro</span>
                            <strong><?= htmlspecialchars(clienteDate($fechaRegistro)) ?></strong>
                        </div>
                    </div>
                </section>

                <section class="cliente-info-panel">
                    <div class="section-heading section-heading-between">
                        <div>
                            <h3>Facturas recientes</h3>
                            <p>Últimas ventas asociadas a este cliente.</p>
                        </div>

                        <?php if ($idCliente > 0): ?>
                            <a href="facturas.php?cliente=<?= $idCliente ?>" class="btn-secondary-inline btn-small-inline">
                                Ver historial
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($facturasCliente)): ?>
                        <p class="empty-message">
                            Este cliente todavía no tiene facturas registradas.
                        </p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="cliente-facturas-table">
                                <thead>
                                    <tr>
                                        <th>Factura</th>
                                        <th>Fecha</th>
                                        <th>Sección</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($facturasCliente as $factura): ?>
                                        <?php
                                        $idFactura = (int)($factura["id_factura"] ?? $factura["id"] ?? 0);
                                        $fechaFactura = $factura["fecha"] ?? $factura["fecha_factura"] ?? $factura["created_at"] ?? "";
                                        $seccionFactura = $factura["seccion"] ?? $factura["nombre_seccion"] ?? "No registrada";
                                        $totalFactura = $factura["total"] ?? $factura["total_final"] ?? 0;
                                        ?>

                                        <tr>
                                            <td>
                                                <strong>#<?= $idFactura ?></strong>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(clienteDate((string)$fechaFactura)) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars((string)$seccionFactura) ?>
                                            </td>

                                            <td>
                                                <strong><?= clienteMoney($totalFactura) ?></strong>
                                            </td>

                                            <td>
                                                <?php if ($idFactura > 0): ?>
                                                    <a href="detalle_factura.php?id=<?= $idFactura ?>" class="btn-accion btn-accion-ver">
                                                        Ver detalle
                                                    </a>
                                                <?php else: ?>
                                                    <span class="cliente-muted">No disponible</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

            </div>

        </section>

    </main>

    <?php require __DIR__ . "/partials/dashboard/sidebar-script.php"; ?>

</body>

</html>