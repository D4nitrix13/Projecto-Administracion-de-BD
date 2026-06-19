<?php

require_once __DIR__ . "/../helpers/pagination.php";
require_once __DIR__ . "/../repositories/AuditoriaRepository.php";

function obtenerDatosAuditoriaEliminados(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $auditoriaRepository = new AuditoriaRepository($connection);

    $flashSuccess = $_SESSION["flash_success"] ?? null;
    $flashError = $_SESSION["flash_error"] ?? null;

    unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        procesarAccionAuditoriaEliminados($auditoriaRepository);
    }

    $busqueda = trim($_GET["q"] ?? "");
    $tablaFiltro = $_GET["tabla"] ?? "";
    $fechaDesde = $_GET["desde"] ?? "";
    $fechaHasta = $_GET["hasta"] ?? "";

    $tablasPermitidas = AuditoriaRepository::obtenerTablasRestaurables();

    if (!array_key_exists($tablaFiltro, $tablasPermitidas)) {
        $tablaFiltro = "";
    }

    $filtros = [
        "busqueda" => $busqueda,
        "tablaFiltro" => $tablaFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
    ];

    $paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));
    $totalRegistros = $auditoriaRepository->contarRegistrosEliminados($filtros);
    $paginacion = calcularPaginacion($totalRegistros, $paginaActual);

    $registros = $auditoriaRepository->obtenerRegistrosEliminados(
        $filtros,
        $paginacion["porPagina"],
        $paginacion["offset"]
    );

    $resumen = $auditoriaRepository->obtenerResumenEliminados();

    return [
        "user" => $user,
        "registros" => $registros,
        "resumen" => $resumen,
        "busqueda" => $busqueda,
        "tablaFiltro" => $tablaFiltro,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,
        "flashSuccess" => $flashSuccess,
        "flashError" => $flashError,
        "paginacion" => $paginacion,
    ];
}

function procesarAccionAuditoriaEliminados(AuditoriaRepository $auditoriaRepository): void
{
    $accion = $_POST["accion"] ?? "";
    $idAuditoria = $_POST["id_auditoria"] ?? "";

    if (!ctype_digit((string)$idAuditoria)) {
        $_SESSION["flash_error"] = "No se recibió un registro válido.";
        header("Location: auditoria_eliminados.php");
        exit();
    }

    try {
        if ($accion === "restaurar") {
            $auditoriaRepository->restaurarRegistroEliminado((int)$idAuditoria);
            $_SESSION["flash_success"] = "Registro restaurado correctamente.";
            header("Location: auditoria_eliminados.php");
            exit();
        }

        if ($accion === "eliminar_permanente") {
            $auditoriaRepository->eliminarAuditoriaPermanentemente((int)$idAuditoria);
            $_SESSION["flash_success"] = "Registro eliminado permanentemente del historial.";
            header("Location: auditoria_eliminados.php");
            exit();
        }

        $_SESSION["flash_error"] = "Acción no válida.";
        header("Location: auditoria_eliminados.php");
        exit();
    } catch (Throwable $exception) {
        $_SESSION["flash_error"] = $exception->getMessage();
        header("Location: auditoria_eliminados.php");
        exit();
    }
}
