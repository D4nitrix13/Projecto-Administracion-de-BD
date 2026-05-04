<?php
// * Stored function or procedure has been executed

$idCliente = isset($_GET["id"]) && ctype_digit((string) $_GET["id"])
    ? (int) $_GET["id"]
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
    FROM obtener_cliente_por_id(:id_cliente)
");

$stmt->execute([
    ":id_cliente" => $idCliente
]);

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
    FROM obtener_ultimas_facturas_cliente(:id_cliente, :limite)
");

$stmtFacturas->execute([
    ":id_cliente" => $idCliente,
    ":limite" => 10
]);

$facturasCliente = $stmtFacturas->fetchAll(PDO::FETCH_ASSOC);

$stmtResumen = $connection->prepare("
    SELECT
        total_facturas,
        total_comprado,
        promedio_compra
    FROM obtener_resumen_cliente(:id_cliente)
");

$stmtResumen->execute([
    ":id_cliente" => $idCliente
]);

$resumenCliente = $stmtResumen->fetch(PDO::FETCH_ASSOC);

$nombreCompleto = trim(($cliente["nombres"] ?? "") . " " . ($cliente["apellidos"] ?? ""));
