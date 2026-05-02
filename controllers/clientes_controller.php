<?php

require_once __DIR__ . "/../repositories/ClienteRepository.php";

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

    /*
        Reglas de visibilidad por rol:

        Admin (1):
        Puede ver Mayorista y Detallista.

        Supervisor (2) y Facturador (3):
        Solo pueden ver clientes Detallista.
    */
    $soloDetallista = in_array($idRol, [2, 3], true);

    if ($soloDetallista) {
        $tipoFiltro = "Detallista";
    }

    $clienteRepository = new ClienteRepository($connection);

    $clientes = $clienteRepository->obtenerClientesFiltrados(
        $busqueda,
        $tipoFiltro
    );

    $textoSubtitulo = obtenerTextoSubtituloClientes($idRol);

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
    ];
}

function obtenerTextoSubtituloClientes(int $idRol): string
{
    if ($idRol === 2 || $idRol === 3) {
        return "Administre los clientes de Kitsune.";
    }

    return "Administre los clientes de Panda Estampados y Kitsune.";
}
