<?php

require_once __DIR__ . "/../repositories/FacturaRepository.php";

function obtenerDatosImprimirFactura(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $facturaRepository = new FacturaRepository($connection);

    $idFactura = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if ($idFactura <= 0) {
        $_SESSION["flash_error"] = "Factura no especificada.";
        header("Location: facturas.php");
        exit();
    }

    $factura = $facturaRepository->obtenerFacturaParaImpresion($idFactura);

    if (!$factura) {
        $_SESSION["flash_error"] = "La factura no existe.";
        header("Location: facturas.php");
        exit();
    }

    $detalles = $facturaRepository->obtenerDetalleFacturaParaImpresion($idFactura);

    $empresaNombre = "Panda Estampados / Kitsune";
    $empresaDireccion = "Managua, Nicaragua";
    $empresaTelefono = obtenerTelefonoEmpresa();

    $esFugaz = ($factura["tipo_cliente_venta"] ?? "Habitual") === "Fugaz";

    $nombreClienteMostrar = $esFugaz
        ? ($factura["nombre_cliente_fugaz"] ?: "Cliente fugaz")
        : trim(($factura["cli_nombres"] ?? "") . " " . ($factura["cli_apellidos"] ?? ""));

    if ($nombreClienteMostrar === "") {
        $nombreClienteMostrar = "Cliente no registrado";
    }

    $fechaFactura = date("d/m/Y H:i", strtotime($factura["fecha"] ?? "now"));

    $fechaEntregaEstimada = !empty($factura["fecha_entrega_estimada"])
        ? date("d/m/Y", strtotime($factura["fecha_entrega_estimada"]))
        : "No definida";

    $fechaEntregaReal = !empty($factura["fecha_entrega_real"])
        ? date("d/m/Y", strtotime($factura["fecha_entrega_real"]))
        : "No registrada";

    return [
        "user" => $user,
        "factura" => $factura,
        "detalles" => $detalles,
        "empresaNombre" => $empresaNombre,
        "empresaDireccion" => $empresaDireccion,
        "empresaTelefono" => $empresaTelefono,
        "nombreClienteMostrar" => $nombreClienteMostrar,
        "esFugaz" => $esFugaz,
        "fechaFactura" => $fechaFactura,
        "fechaEntregaEstimada" => $fechaEntregaEstimada,
        "fechaEntregaReal" => $fechaEntregaReal,
    ];
}

function obtenerTelefonoEmpresa(): string
{
    $archivoWhatsApp = __DIR__ . "/../numero_de_whatsapp.txt";

    if (!is_readable($archivoWhatsApp)) {
        return "Teléfono no configurado";
    }

    $numero = trim((string)file_get_contents($archivoWhatsApp));
    $numero = preg_replace('/\s+/', ' ', $numero);

    $patron = '/^(?:\+505 ?\d{4} ?\d{4}|505 ?\d{4} ?\d{4})$/';

    if (!preg_match($patron, $numero)) {
        return "Teléfono no configurado";
    }

    return $numero;
}
