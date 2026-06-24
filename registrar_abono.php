<?php

session_start();

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/repositories/FacturaRepository.php";
require_once __DIR__ . "/repositories/ProductoRepository.php";
require_once __DIR__ . "/services/FacturaService.php";

requireLogin();

$user = $_SESSION["user"];
$idRol = (int) ($user["id_rol"] ?? 0);

if ($idRol > 2) {
    $_SESSION["flash_error"] = "No tiene permisos para registrar abonos.";
    header("Location: facturas.php");
    exit();
}

csrfRequire();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: facturas.php");
    exit();
}

$idFactura = isset($_POST["id_factura"]) ? (int) $_POST["id_factura"] : 0;
$montoAbono = isset($_POST["monto_abono"]) ? (float) $_POST["monto_abono"] : 0.0;
$comentario = trim($_POST["comentario"] ?? "");

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

$facturaService = new FacturaService(
    $connection,
    new FacturaValidationService($connection),
    new FacturaCalculationService($connection, new ProductoRepository($connection)),
);

$resultado = $facturaService->registrarAbono($idFactura, $montoAbono, $comentario, $user);

if ($resultado["success"]) {
    $_SESSION["flash_success"] = $resultado["message"];
} else {
    $_SESSION["flash_error"] = $resultado["message"];
}

header("Location: detalle_factura.php?id=" . $idFactura);
exit();
