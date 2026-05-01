<?php

$idCliente = isset($_GET["id"]) && ctype_digit((string)$_GET["id"])
    ? (int)$_GET["id"]
    : 0;

if ($idCliente <= 0) {
    $_SESSION["flash_error"] = "ID de cliente no válido.";
    header("Location: clientes.php");
    exit();
}

$stmt = $connection->prepare("
    SELECT
        id_cliente,
        nombres,
        apellidos,
        telefono,
        direccion,
        identificacion,
        tipo_cliente,
        fecha_registro
    FROM Cliente
    WHERE id_cliente = :id_cliente
");

$stmt->execute([":id_cliente" => $idCliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    $_SESSION["flash_error"] = "Cliente no encontrado.";
    header("Location: clientes.php");
    exit();
}

$stmtFacturas = $connection->prepare("
    SELECT
        id_factura,
        fecha,
        subtotal,
        descuento,
        impuesto,
        total
    FROM Factura
    WHERE id_cliente = :id_cliente
    ORDER BY fecha DESC, id_factura DESC
    LIMIT 10
");

$stmtFacturas->execute([":id_cliente" => $idCliente]);
$facturasCliente = $stmtFacturas->fetchAll(PDO::FETCH_ASSOC);

$stmtResumen = $connection->prepare("
    SELECT
        COUNT(*) AS total_facturas,
        COALESCE(SUM(total), 0) AS total_comprado,
        COALESCE(AVG(total), 0) AS promedio_compra
    FROM Factura
    WHERE id_cliente = :id_cliente
");

$stmtResumen->execute([":id_cliente" => $idCliente]);
$resumenCliente = $stmtResumen->fetch(PDO::FETCH_ASSOC);

$nombreCompleto = trim(($cliente["nombres"] ?? "") . " " . ($cliente["apellidos"] ?? ""));
