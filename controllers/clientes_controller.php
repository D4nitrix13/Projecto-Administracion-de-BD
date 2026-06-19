<?php

require_once __DIR__ . "/../repositories/ClienteRepository.php";
require_once __DIR__ . "/../helpers/pagination.php";

function obtenerDatosClientes(): array
{
    $user = $_SESSION["user"];
    $idRol = (int)($user["id_rol"] ?? 0);

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $flashSuccess = $_SESSION["flash_success"] ?? null;
    $flashError = $_SESSION["flash_error"] ?? null;

    unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

    $busqueda = trim($_GET["q"] ?? "");
    $tipoFiltro = trim($_GET["tipo"] ?? "");
    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));

    $soloDetallista = in_array($idRol, [2, 3], true);

    if ($soloDetallista) {
        $tipoFiltro = "Detallista";
    }

    $clienteRepository = new ClienteRepository($connection);

    $totalRegistros = $clienteRepository->contarClientesFiltrados($busqueda, $tipoFiltro);
    $paginacion = calcularPaginacion($totalRegistros, $paginaActual, 15);

    $clientes = $clienteRepository->obtenerClientesFiltrados(
        $busqueda,
        $tipoFiltro,
        $paginacion["paginaActual"],
        $paginacion["porPagina"]
    );

    $textoSubtitulo = obtenerTextoSubtituloClientes($idRol);

    $filtrosGET = array_filter([
        "q" => $busqueda ?: null,
        "tipo" => $tipoFiltro ?: null,
    ], fn($v) => $v !== null);

    return [
        "user" => $user,
        "idRol" => $idRol,
        "clientes" => $clientes,
        "busqueda" => $busqueda,
        "tipoFiltro" => $tipoFiltro,
        "soloDetallista" => $soloDetallista,
        "textoSubtitulo" => $textoSubtitulo,
        "flashSuccess" => $flashSuccess,
        "flashError" => $flashError,
        "paginacion" => $paginacion,
        "filtrosGET" => $filtrosGET,
    ];
}

function obtenerTextoSubtituloClientes(int $idRol): string
{
    if ($idRol === 2 || $idRol === 3) {
        return "Administre los clientes de Kitsune.";
    }

    return "Administre los clientes de Panda Estampados y Kitsune.";
}
