<?php

require_once __DIR__ . "/../repositories/FacturaRepository.php";
require_once __DIR__ . "/../repositories/SeccionRepository.php";
require_once __DIR__ . "/../repositories/UsuarioRepository.php";

function obtenerDatosFacturas(): array
{
    $user = $_SESSION["user"];
    $idRol = (int)($user["id_rol"] ?? 0);

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $facturaRepository = new FacturaRepository($connection);
    $seccionRepository = new SeccionRepository($connection);
    $usuarioRepository = new UsuarioRepository($connection);

    $flashSuccess = $_SESSION["flash_success"] ?? null;
    $flashError = $_SESSION["flash_error"] ?? null;

    unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

    $busqueda = trim($_GET["q"] ?? "");
    $seccionFiltro = $_GET["seccion"] ?? "";
    $usuarioFiltro = $_GET["usuario"] ?? "";
    $estadoPagoFiltro = $_GET["estado_pago"] ?? "";
    $estadoProduccionFiltro = $_GET["estado_produccion"] ?? "";
    $fechaDesde = $_GET["desde"] ?? "";
    $fechaHasta = $_GET["hasta"] ?? "";

    $estadosPagoPermitidos = ["Pendiente", "Parcial", "Pagado"];
    $estadosProduccionPermitidos = [
        "Pendiente",
        "En producción",
        "Lista para entregar",
        "Entregada",
        "Cancelada",
    ];

    if (!in_array($estadoPagoFiltro, $estadosPagoPermitidos, true)) {
        $estadoPagoFiltro = "";
    }

    if (!in_array($estadoProduccionFiltro, $estadosProduccionPermitidos, true)) {
        $estadoProduccionFiltro = "";
    }

    $seccionFiltroInt = ctype_digit($seccionFiltro) ? (int)$seccionFiltro : null;
    $usuarioFiltroInt = ctype_digit($usuarioFiltro) ? (int)$usuarioFiltro : null;

    if (esRolRestringidoFacturas($idRol)) {
        $secciones = $seccionRepository->obtenerSeccionKitsune();
    } else {
        $secciones = $seccionRepository->obtenerTodasLasSecciones();
    }

    $usuariosFiltro = $usuarioRepository->obtenerUsuariosOrdenados();

    $facturas = $facturaRepository->obtenerFacturasFiltradas([
        "idRol" => $idRol,
        "busqueda" => $busqueda,
        "seccionFiltroInt" => $seccionFiltroInt,
        "usuarioFiltroInt" => $usuarioFiltroInt,
        "estadoPagoFiltro" => $estadoPagoFiltro,
        "estadoProduccionFiltro" => $estadoProduccionFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
    ]);

    $textoSubtitulo = obtenerTextoSubtituloFacturas($idRol);

    return [
        "user" => $user,
        "idRol" => $idRol,
        "facturas" => $facturas,
        "secciones" => $secciones,
        "usuariosFiltro" => $usuariosFiltro,
        "busqueda" => $busqueda,
        "seccionFiltroInt" => $seccionFiltroInt,
        "usuarioFiltroInt" => $usuarioFiltroInt,
        "estadoPagoFiltro" => $estadoPagoFiltro,
        "estadoProduccionFiltro" => $estadoProduccionFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
        "textoSubtitulo" => $textoSubtitulo,
        "flashSuccess" => $flashSuccess,
        "flashError" => $flashError,
    ];
}

function esRolRestringidoFacturas(int $idRol): bool
{
    return in_array($idRol, [2, 3], true);
}

function obtenerTextoSubtituloFacturas(int $idRol): string
{
    if ($idRol === 1) {
        return "Revise todas las facturas emitidas y cómo se registraron las ventas de Panda Estampados y Kitsune.";
    }

    return "Revise todas las facturas emitidas y cómo se registraron las ventas de Kitsune.";
}
