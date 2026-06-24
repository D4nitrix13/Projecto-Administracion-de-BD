<?php

require_once __DIR__ . "/../repositories/FacturaRepository.php";
require_once __DIR__ . "/../repositories/SeccionRepository.php";
require_once __DIR__ . "/../repositories/UsuarioRepository.php";
require_once __DIR__ . "/../helpers/pagination.php";

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
    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));

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

    if ($fechaDesde !== "" && $fechaHasta !== "" && $fechaDesde > $fechaHasta) {
        $fechaDesde = "";
        $fechaHasta = "";
    }

    $seccionFiltroInt = ctype_digit($seccionFiltro) ? (int)$seccionFiltro : null;
    $usuarioFiltroInt = ctype_digit($usuarioFiltro) ? (int)$usuarioFiltro : null;

    if (esRolRestringidoFacturas($idRol)) {
        $secciones = $seccionRepository->obtenerSeccionKitsune();
    } else {
        $secciones = $seccionRepository->obtenerTodasLasSecciones();
    }

    $usuariosFiltro = $usuarioRepository->obtenerUsuariosOrdenados();

    $filtros = [
        "idRol" => $idRol,
        "busqueda" => $busqueda,
        "seccionFiltroInt" => $seccionFiltroInt,
        "usuarioFiltroInt" => $usuarioFiltroInt,
        "estadoPagoFiltro" => $estadoPagoFiltro,
        "estadoProduccionFiltro" => $estadoProduccionFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
    ];

    $totalRegistros = $facturaRepository->contarFacturasFiltradas($filtros);
    $paginacion = calcularPaginacion($totalRegistros, $paginaActual, 15);

    $facturas = $facturaRepository->obtenerFacturasFiltradas(array_merge($filtros, [
        "pagina" => $paginacion["paginaActual"],
        "porPagina" => $paginacion["porPagina"],
    ]));

    $textoSubtitulo = obtenerTextoSubtituloFacturas($idRol);

    $filtrosGET = array_filter([
        "q" => $busqueda ?: null,
        "seccion" => $seccionFiltro ?: null,
        "usuario" => $usuarioFiltro ?: null,
        "estado_pago" => $estadoPagoFiltro ?: null,
        "estado_produccion" => $estadoProduccionFiltro ?: null,
        "desde" => $fechaDesde ?: null,
        "hasta" => $fechaHasta ?: null,
    ], fn($v) => $v !== null);

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
        "paginacion" => $paginacion,
        "filtrosGET" => $filtrosGET,
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
