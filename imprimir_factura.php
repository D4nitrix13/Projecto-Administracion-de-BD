<?php
session_start();
$pageTitle = "Detalle de factura - Panda Estampados / Kitsune";

// Proteger la página
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

/** @var PDO $connection */
$connection = require "./sql/db.php";

$idFactura = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($idFactura <= 0) {
    $_SESSION["flash_error"] = "Factura no especificada.";
    header("Location: facturas.php");
    exit();
}

/*
 * 1) Traer datos generales de la factura
 *    Incluye cliente, usuario y sección
 */
$stmt = $connection->prepare("
    SELECT 
        f.*,
        c.nombres        AS cli_nombres,
        c.apellidos      AS cli_apellidos,
        c.telefono       AS cli_telefono,
        c.direccion      AS cli_direccion,
        c.identificacion AS cli_identificacion,
        u.nombre         AS usuario_nombre,
        s.nombre         AS seccion_nombre
    FROM Factura f
    JOIN Cliente c   ON f.id_cliente = c.id_cliente
    JOIN Usuario u   ON f.id_usuario = u.id_usuario
    JOIN Seccion s   ON f.id_seccion = s.id_seccion
    WHERE f.id_factura = :id
");
$stmt->execute([":id" => $idFactura]);
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$factura) {
    $_SESSION["flash_error"] = "La factura no existe.";
    header("Location: facturas.php");
    exit();
}

/*
 * 2) Traer detalle de productos
 */
$stmtDet = $connection->prepare("
    SELECT 
        df.*,
        p.nombre AS producto_nombre,
        p.codigo AS producto_codigo
    FROM DetalleFactura df
    JOIN Producto p ON df.id_producto = p.id_producto
    WHERE df.id_factura = :id
    ORDER BY df.id_detalle
");
$stmtDet->execute([":id" => $idFactura]);
$detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

/*
 * 3) Datos de la empresa (puedes luego mover esto a una tabla Config o similar)
 */
$empresaNombre    = "Panda Estampados / Kitsune";
$empresaDireccion = "Managua, Nicaragua";

// Leer teléfono desde numero_de_whatsapp.txt
$empresaTelefono  = "";
$archivoWhatsApp  = __DIR__ . "/numero_de_whatsapp.txt";

if (is_readable($archivoWhatsApp)) {
    $numero = trim(file_get_contents($archivoWhatsApp));

    // Normalizar espacios múltiples a un solo espacio
    $numero = preg_replace('/\s+/', ' ', $numero);

    // Formatos permitidos:
    // +505 7696 3266
    // +50576963266
    // +505 76963266
    // 5057696 3266
    // 505 7696 3266
    $patron = '/^(?:\+505 ?\d{4} ?\d{4}|505 ?\d{4} ?\d{4})$/';

    if (preg_match($patron, $numero)) {
        $empresaTelefono = $numero;   // usamos el número tal como está en el archivo
    }
}

// Procesar datos de cliente según si es Habitual/Fugaz
$esFugaz = ($factura["tipo_cliente_venta"] ?? "Habitual") === "Fugaz";
$nombreClienteMostrar = $esFugaz
    ? ($factura["nombre_cliente_fugaz"] ?: "Cliente fugaz")
    : trim(($factura["cli_nombres"] ?? "") . " " . ($factura["cli_apellidos"] ?? ""));

$fechaFactura = date("d/m/Y H:i", strtotime($factura["fecha"] ?? "now"));
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<head>
    <style>
        /* ---------- CONTENEDOR GENERAL ---------- */
        .invoice-page {
            max-width: 14cm;
            /* mismo ancho que la página */
            margin: 0 auto;
            padding: 6mm 8mm;
            background: #ffffff;
            font-size: 0.9rem;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
        }

        .invoice-company h1 {
            font-size: 1.1rem;
            margin: 0 0 2px 0;
        }

        .invoice-company div {
            line-height: 1.2;
        }

        .invoice-meta {
            text-align: right;
            font-size: 0.85rem;
        }

        .invoice-meta h2 {
            margin: 0 0 2px 0;
            font-size: 1rem;
        }

        /* ---------- CUADROS ORDENADOS ---------- */

        .invoice-grid {
            display: flex;
            gap: 6px;
            margin-top: 6px;
            margin-bottom: 6px;
        }

        .invoice-grid>.box {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 6px;
        }

        .box-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.86rem;
        }

        .info-table td {
            padding: 1px 0;
        }

        .info-table td.label {
            width: 40%;
            font-weight: 600;
            color: #374151;
            vertical-align: top;
        }

        .info-table td.value {
            color: #111827;
        }

        /* ---------- TABLA DE PRODUCTOS ---------- */

        .invoice-section-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin: 8px 0 3px 0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 0.86rem;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 5px;
        }

        .invoice-table th {
            background: #f9fafb;
            text-align: left;
            font-size: 0.78rem;
        }

        .text-right {
            text-align: right;
        }

        /* ---------- TOTALES ---------- */

        /* ---------- TOTALES PERFECTAMENTE ALINEADOS ---------- */

        .totals-box {
            margin-left: auto;
            margin-top: 6px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 10px;
            width: 50%;
            /* 🔹 Puedes ajustar */
            min-width: 6.5cm;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .totals-table tr td {
            padding: 4px 0;
        }

        .totals-table td.label {
            text-align: left;
            /* 🔹 A la izquierda */
            font-weight: 600;
            padding-left: 4px;
        }

        .totals-table td.value {
            text-align: right;
            /* 🔹 A la derecha */
            font-weight: 500;
            padding-right: 4px;
        }

        .totals-table tr.total-final td {
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            font-weight: 700;
            font-size: 1rem;
        }


        /* ---------- BOTONES (SOLO EN PANTALLA) ---------- */
        .print-actions {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .btn-print,
        .btn-back {
            border-radius: 9999px;
            padding: 6px 14px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-print {
            background: #111827;
            color: #f9fafb;
        }

        .btn-back {
            background: #e5e7eb;
            color: #111827;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        /* ---------- CONFIGURACIÓN DE PÁGINA (MEDIA CARTA) ---------- */
        @page {
            size: 14cm 21.5cm;
            /* ancho x alto */
            margin: 5mm 8mm;
            /* Esto pide al navegador NO imprimir encabezados/ pies */
            marks: none;
        }

        @media print {

            html,
            body {
                background: #ffffff !important;
                margin: 0;
                padding: 0;
                width: 14cm;
                height: 21.5cm;
            }

            .navbar,
            .dashboard-container>.dashboard-card.dashboard-welcome,
            .print-actions {
                display: none !important;
            }

            main.dashboard-container {
                padding: 0;
                margin: 0;
            }

            .invoice-page {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>

<body class="page-bg">

    <?php include __DIR__ . "/partials/navbar.php"; ?>

    <main class="dashboard-container">
        <!-- Tarjeta de encabezado normal (solo pantalla) -->
        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Facturación</p>
            <h1 class="dashboard-title">Detalle de factura</h1>
            <p class="dashboard-muted">
                Aquí puedes revisar e imprimir la factura seleccionada.
            </p>
            <a href="facturas.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver al historial de facturas
            </a>
        </section>

        <!-- Página de factura compacta -->
        <section class="invoice-page">

            <!-- ENCABEZADO EMPRESA / FACTURA -->
            <div class="invoice-header">
                <div class="invoice-company">
                    <h1><?= htmlspecialchars($empresaNombre) ?></h1>
                    <div><?= htmlspecialchars($empresaDireccion) ?></div>
                    <div><?= htmlspecialchars($empresaTelefono) ?></div>
                </div>
                <div class="invoice-meta">
                    <h2>Factura #<?= (int)$factura["id_factura"] ?></h2>
                    <div><strong>Fecha:</strong> <?= htmlspecialchars($fechaFactura) ?></div>
                    <div><strong>Sección:</strong> <?= htmlspecialchars($factura["seccion_nombre"] ?? "") ?></div>
                    <div><strong>Atendido por:</strong> <?= htmlspecialchars($factura["usuario_nombre"] ?? "") ?></div>
                </div>
            </div>

            <!-- CUADROS ORDENADOS: CLIENTE / INFORMACIÓN DE LA VENTA -->
            <div class="invoice-grid">
                <!-- Cliente -->
                <div class="box">
                    <div class="box-title">Cliente</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nombre:</td>
                            <td class="value"><?= htmlspecialchars($nombreClienteMostrar) ?></td>
                        </tr>
                        <?php if (!$esFugaz): ?>
                            <tr>
                                <td class="label">Teléfono:</td>
                                <td class="value"><?= htmlspecialchars($factura["cli_telefono"] ?? "N/D") ?></td>
                            </tr>
                            <tr>
                                <td class="label">Dirección:</td>
                                <td class="value"><?= htmlspecialchars($factura["cli_direccion"] ?? "N/D") ?></td>
                            </tr>
                            <tr>
                                <td class="label">Identificación:</td>
                                <td class="value"><?= htmlspecialchars($factura["cli_identificacion"] ?? "N/D") ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td class="label">Tipo:</td>
                                <td class="value">Cliente fugaz</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <!-- Información de la venta -->
                <div class="box">
                    <div class="box-title">Información de la venta</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Tipo de cliente:</td>
                            <td class="value"><?= htmlspecialchars($factura["tipo_cliente_venta"] ?? "Habitual") ?></td>
                        </tr>
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="value">C$ <?= number_format((float)$factura["subtotal"], 2) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Descuento total:</td>
                            <td class="value">C$ <?= number_format((float)$factura["descuento"], 2) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Impuesto (IVA):</td>
                            <td class="value">C$ <?= number_format((float)$factura["impuesto"], 2) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- DETALLE DE PRODUCTOS -->
            <div class="invoice-section-title">Detalle de productos</div>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Código</th>
                        <th class="text-right">Cant.</th>
                        <th class="text-right">P. unitario</th>
                        <th class="text-right">Desc. línea</th>
                        <th class="text-right">Total línea</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($detalles)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:10px;">
                                No hay productos registrados en esta factura.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($detalles as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d["producto_nombre"] ?? "") ?></td>
                                <td><?= htmlspecialchars($d["producto_codigo"] ?? "") ?></td>
                                <td class="text-right"><?= (int)$d["cantidad"] ?></td>
                                <td class="text-right">C$ <?= number_format((float)$d["precio_unitario"], 2) ?></td>
                                <td class="text-right">C$ <?= number_format((float)$d["descuento_linea"], 2) ?></td>
                                <td class="text-right">C$ <?= number_format((float)$d["total_linea"], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- RESUMEN DE TOTALES BIEN ALINEADO -->
            <div class="totals-box">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">C$ <?= number_format((float)$factura["subtotal"], 2) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Descuento</td>
                        <td class="value">C$ <?= number_format((float)$factura["descuento"], 2) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Impuesto</td>
                        <td class="value">C$ <?= number_format((float)$factura["impuesto"], 2) ?></td>
                    </tr>
                    <tr class="total-final">
                        <td class="label">Total a pagar</td>
                        <td class="value">C$ <?= number_format((float)$factura["total"], 2) ?></td>
                    </tr>
                </table>
            </div>


            <!-- BOTONES (NO SE IMPRIMEN) -->
            <div class="print-actions">
                <a href="facturas.php" class="btn-back">← Volver</a>
                <button type="button" class="btn-print" onclick="window.print()">Imprimir factura</button>
            </div>

        </section>
    </main>

</body>

</html>