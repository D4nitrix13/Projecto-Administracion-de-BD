<?php
session_start();

require_once __DIR__ . "/includes/auth_guard.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: facturas.php");
    exit();
}

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

if ($idRol > 2) {
    $_SESSION["flash_error"] = "No tiene permiso para cambiar estados de producción.";
    header("Location: facturas.php");
    exit();
}

csrfRequire();

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

$idFactura = isset($_POST["id_factura"]) ? (int)$_POST["id_factura"] : 0;
$nuevoEstado = trim($_POST["nuevo_estado"] ?? "");
$comentario = trim($_POST["comentario"] ?? "");

$estadosValidos = [
    "En producción"     => "En producción",
    "Lista para entregar" => "Lista para entregar",
    "Entregada"         => "Entregada",
    "Cancelada"         => "Cancelada",
];

if ($idFactura <= 0) {
    $_SESSION["flash_error"] = "Factura no válida.";
    header("Location: facturas.php");
    exit();
}

if (!isset($estadosValidos[$nuevoEstado])) {
    $_SESSION["flash_error"] = "Estado de producción no válido.";
    header("Location: detalle_factura.php?id=" . $idFactura);
    exit();
}

$stmt = $connection->prepare("SELECT id_factura, estado_produccion FROM Factura WHERE id_factura = :id");
$stmt->execute([":id" => $idFactura]);
$factura = $stmt->fetch(\PDO::FETCH_ASSOC);

if (!$factura) {
    $_SESSION["flash_error"] = "Factura no encontrada.";
    header("Location: facturas.php");
    exit();
}

$estadoActual = $factura["estado_produccion"];

$transicionesPermitidas = [
    "Pendiente"         => ["En producción", "Cancelada"],
    "En producción"     => ["Lista para entregar", "Cancelada"],
    "Lista para entregar" => ["Entregada", "Cancelada"],
    "Entregada"         => [],
    "Cancelada"         => [],
];

if (!in_array($nuevoEstado, $transicionesPermitidas[$estadoActual] ?? [], true)) {
    $_SESSION["flash_error"] = "No se puede transicionar de \"{$estadoActual}\" a \"{$nuevoEstado}\".";
    header("Location: detalle_factura.php?id=" . $idFactura);
    exit();
}

$stmtPago = $connection->prepare("SELECT porcentaje_pagado FROM Factura WHERE id_factura = :id");
$stmtPago->execute([":id" => $idFactura]);
$filaPago = $stmtPago->fetch(\PDO::FETCH_ASSOC);
$porcentajePagado = (float) ($filaPago["porcentaje_pagado"] ?? 0);

if ($nuevoEstado === "En producción" && $porcentajePagado < 50.0) {
    $_SESSION["flash_error"] = "No se puede iniciar producción. El cliente debe haber pagado al menos el 50% (actual: " . number_format($porcentajePagado, 1) . "%).";
    header("Location: detalle_factura.php?id=" . $idFactura);
    exit();
}

if ($nuevoEstado === "Lista para entregar" && $porcentajePagado < 100.0) {
    $_SESSION["flash_error"] = "No se puede marcar como lista para entregar. El cliente debe haber pagado el 100% (actual: " . number_format($porcentajePagado, 1) . "%).";
    header("Location: detalle_factura.php?id=" . $idFactura);
    exit();
}

$fechaEntregaReal = null;
if ($nuevoEstado === "Entregada") {
    $fechaEntregaReal = date("Y-m-d");
}

$updateFields = [
    "estado_produccion" => $nuevoEstado,
];

$updateParams = [":id" => $idFactura];

$sql = "UPDATE Factura SET estado_produccion = :estado";

if ($fechaEntregaReal !== null) {
    $sql .= ", fecha_entrega_real = :fecha_real";
    $updateParams[":fecha_real"] = $fechaEntregaReal;
}

$sql .= " WHERE id_factura = :id";
$updateParams[":estado"] = $nuevoEstado;

$stmt = $connection->prepare($sql);
$stmt->execute($updateParams);

$nombreUsuario = $user["nombre"] ?? "Usuario";
$_SESSION["flash_success"] = "Estado de producción actualizado a \"{$nuevoEstado}\" por {$nombreUsuario}.";
header("Location: detalle_factura.php?id=" . $idFactura);
exit();
