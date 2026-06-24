<?php

declare(strict_types=1);

require_once __DIR__ . "/../sql/db.php";
require_once __DIR__ . "/../src/Repository/CatalogoRepository.php";

use App\Repository\CatalogoRepository;

const PRODUCTOS_POR_PAGINA = 20;

function obtenerDatosCatalogo(): array
{
    $connection = createConnection();
    $catalogoRepo = new CatalogoRepository($connection);

    $usuario = $_SESSION["user"] ?? null;
    $numeroWhatsApp = obtenerNumeroWhatsAppCatalogo();

    $busquedaTexto = trim($_GET["q"] ?? "");
    $filtroCategoriaRaw = $_GET["categoria"] ?? "";
    $filtroDisponibilidad = $_GET["disponibilidad"] ?? "";
    $paginaRaw = $_GET["pagina"] ?? "1";

    $filtroCategoria = ctype_digit($filtroCategoriaRaw)
        ? (int) $filtroCategoriaRaw
        : null;

    $pagina = ctype_digit($paginaRaw) ? max(1, (int) $paginaRaw) : 1;

    $disponibilidadesPermitidas = ["disponible", "stock_bajo", "agotado"];

    if (!in_array($filtroDisponibilidad, $disponibilidadesPermitidas, true)) {
        $filtroDisponibilidad = "";
    }

    $hayFiltros = $busquedaTexto !== ""
        || $filtroCategoria !== null
        || $filtroDisponibilidad !== "";

    $productosPorCategoria = $hayFiltros
        ? []
        : $catalogoRepo->obtenerCategoriasConImagen(8);

    $masVendidos = $hayFiltros
        ? []
        : $catalogoRepo->obtenerMasVendidos(12);

    $offset = ($pagina - 1) * PRODUCTOS_POR_PAGINA;

    $productos = $catalogoRepo->buscarProductos(
        $busquedaTexto,
        $filtroCategoria,
        $filtroDisponibilidad,
        PRODUCTOS_POR_PAGINA,
        $offset
    );

    $totalRegistros = 0;

    if (!empty($productos)) {
        $totalRegistros = (int) $productos[0]["total_registros"];
    }

    $totalPaginas = max(1, (int) ceil($totalRegistros / PRODUCTOS_POR_PAGINA));

    return [
        "usuario"              => $usuario,
        "numeroWhatsApp"       => $numeroWhatsApp,
        "busquedaTexto"        => $busquedaTexto,
        "filtroCategoria"      => $filtroCategoria,
        "filtroDisponibilidad" => $filtroDisponibilidad,
        "hayFiltros"           => $hayFiltros,
        "categorias"           => $catalogoRepo->obtenerCategorias(),
        "productos"            => $productos,
        "productosPorCategoria" => $productosPorCategoria,
        "masVendidos"           => $masVendidos,
        "pagina"               => $pagina,
        "totalPaginas"         => $totalPaginas,
        "totalRegistros"       => $totalRegistros,
    ];
}

function obtenerNumeroWhatsAppCatalogo(): string
{
    $archivoWhatsApp = __DIR__ . "/../numero_de_whatsapp.txt";

    if (!is_readable($archivoWhatsApp)) {
        return "";
    }

    $contenido = trim(file_get_contents($archivoWhatsApp));

    if ($contenido === "") {
        return "";
    }

    return preg_replace("/\D+/", "", $contenido);
}

function recortarTextoCatalogo(?string $texto, int $limite = 145): string
{
    $texto = trim((string) $texto);

    if ($texto === "") {
        return "Sin descripción disponible.";
    }

    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }

    return mb_substr($texto, 0, $limite - 3) . "...";
}

function obtenerClaseStockCatalogo(int $stock): string
{
    if ($stock <= 0) {
        return "stock-agotado";
    }

    if ($stock <= 5) {
        return "stock-bajo";
    }

    return "stock-disponible";
}

function obtenerTextoStockCatalogo(int $stock): string
{
    if ($stock <= 0) {
        return "Agotado";
    }

    if ($stock <= 5) {
        return "Stock bajo: " . $stock;
    }

    return "Stock: " . $stock;
}

function obtenerUrlWhatsAppCatalogo(
    string $numeroWhatsApp,
    array $producto
): string {
    if ($numeroWhatsApp === "") {
        return "";
    }

    $texto = "Hola, estoy interesado en el producto "
        . $producto["nombre"]
        . " (código "
        . $producto["codigo"]
        . "). ¿Podrían brindarme más información?";

    return "https://wa.me/" . preg_replace("/[^0-9]/", "", $numeroWhatsApp) . "?text=" . urlencode($texto);
}
