<?php

require_once __DIR__ . "/../repositories/CompraRepository.php";
require_once __DIR__ . "/../helpers/pagination.php";

function obtenerDatosCompras(): array
{
    $user = $_SESSION["user"] ?? [];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $repository = new CompraRepository($connection);

    $flashSuccess = $_SESSION["flash_success"] ?? null;
    $flashError   = $_SESSION["flash_error"] ?? null;

    unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

    $busqueda        = trim($_GET["q"] ?? "");
    $proveedorFiltro = $_GET["proveedor"] ?? "";
    $usuarioFiltro   = $_GET["usuario"] ?? "";
    $fechaDesde      = $_GET["desde"] ?? "";
    $fechaHasta      = $_GET["hasta"] ?? "";
    $paginaActual    = max(1, (int) ($_GET["pagina"] ?? 1));

    $proveedorFiltroInt = ctype_digit((string)$proveedorFiltro)
        ? (int)$proveedorFiltro
        : null;

    $usuarioFiltroInt = ctype_digit((string)$usuarioFiltro)
        ? (int)$usuarioFiltro
        : null;

    $proveedores = $repository->obtenerProveedores();
    $usuarios    = $repository->obtenerUsuarios();

    $totalRegistros = $repository->contarComprasFiltradas(
        busqueda: $busqueda,
        proveedorId: $proveedorFiltroInt,
        usuarioId: $usuarioFiltroInt,
        fechaDesde: $fechaDesde,
        fechaHasta: $fechaHasta
    );

    $paginacion = calcularPaginacion($totalRegistros, $paginaActual, 15);

    $compras = $repository->obtenerComprasFiltradas(
        busqueda: $busqueda,
        proveedorId: $proveedorFiltroInt,
        usuarioId: $usuarioFiltroInt,
        fechaDesde: $fechaDesde,
        fechaHasta: $fechaHasta,
        pagina: $paginacion["paginaActual"],
        porPagina: $paginacion["porPagina"]
    );

    $filtrosGET = array_filter([
        "q"        => $busqueda ?: null,
        "proveedor" => $proveedorFiltro ?: null,
        "usuario"  => $usuarioFiltro ?: null,
        "desde"    => $fechaDesde ?: null,
        "hasta"    => $fechaHasta ?: null,
    ], fn($v) => $v !== null);

    return [
        "user"               => $user,
        "flashSuccess"       => $flashSuccess,
        "flashError"         => $flashError,
        "busqueda"           => $busqueda,
        "proveedorFiltroInt" => $proveedorFiltroInt,
        "usuarioFiltroInt"   => $usuarioFiltroInt,
        "fechaDesde"         => $fechaDesde,
        "fechaHasta"         => $fechaHasta,
        "proveedores"        => $proveedores,
        "usuarios"           => $usuarios,
        "compras"            => $compras,
        "paginacion"         => $paginacion,
        "filtrosGET"         => $filtrosGET,
    ];
}
