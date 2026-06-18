<?php

declare(strict_types=1);

require_once __DIR__ . "/../sql/db.php";
require_once __DIR__ . "/../src/Repository/CatalogoRepository.php";

use App\Repository\CatalogoRepository;

function obtenerDatosCatalogo(): array
{
    $connection = createConnection();
    $catalogoRepo = new CatalogoRepository($connection);

    $usuario = $_SESSION["user"] ?? null;
    $numeroWhatsApp = obtenerNumeroWhatsAppCatalogo();

    $busquedaTexto = trim($_GET["q"] ?? "");
    $filtroCategoriaRaw = $_GET["categoria"] ?? "";
    $filtroDisponibilidad = $_GET["disponibilidad"] ?? "";

    $filtroCategoria = ctype_digit($filtroCategoriaRaw)
        ? (int) $filtroCategoriaRaw
        : null;

    $disponibilidadesPermitidas = ["disponible", "stock_bajo", "agotado"];

    if (!in_array($filtroDisponibilidad, $disponibilidadesPermitidas, true)) {
        $filtroDisponibilidad = "";
    }

    return [
        "usuario"             => $usuario,
        "numeroWhatsApp"      => $numeroWhatsApp,
        "busquedaTexto"       => $busquedaTexto,
        "filtroCategoria"     => $filtroCategoria,
        "filtroDisponibilidad" => $filtroDisponibilidad,
        "categorias"          => $catalogoRepo->obtenerCategorias(),
        "productos"           => $catalogoRepo->buscarProductos(
            $busquedaTexto,
            $filtroCategoria,
            $filtroDisponibilidad
        ),
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

    return "https://wa.me/" . $numeroWhatsApp . "?text=" . urlencode($texto);
}
