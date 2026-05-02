<?php

require_once __DIR__ . "/../repositories/FacturaRepository.php";

function obtenerDatosDetalleFactura(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $facturaRepository = new FacturaRepository($connection);

    $idFactura = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if ($idFactura <= 0) {
        $_SESSION["flash_error"] = "Factura no válida.";
        header("Location: facturas.php");
        exit();
    }

    $factura = $facturaRepository->obtenerFacturaPorId($idFactura);

    if (!$factura) {
        $_SESSION["flash_error"] = "Factura no encontrada.";
        header("Location: facturas.php");
        exit();
    }

    $detalles = $facturaRepository->obtenerDetalleFactura($idFactura);

    return [
        "user" => $user,
        "factura" => $factura,
        "detalles" => $detalles,
    ];
}
