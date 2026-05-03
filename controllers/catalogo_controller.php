<?php
// * Stored function or procedure has been executed

function obtenerDatosCatalogo(): array
{
    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $usuario = $_SESSION["user"] ?? null;

    $numeroWhatsApp = obtenerNumeroWhatsAppCatalogo();

    $busquedaTexto = trim($_GET["q"] ?? "");
    $filtroCategoriaRaw = $_GET["categoria"] ?? "";
    $filtroDisponibilidad = $_GET["disponibilidad"] ?? "";

    $filtroCategoria = ctype_digit($filtroCategoriaRaw)
        ? (int)$filtroCategoriaRaw
        : null;

    $disponibilidadesPermitidas = [
        "disponible",
        "stock_bajo",
        "agotado",
    ];

    if (!in_array($filtroDisponibilidad, $disponibilidadesPermitidas, true)) {
        $filtroDisponibilidad = "";
    }

    return [
        "usuario" => $usuario,
        "numeroWhatsApp" => $numeroWhatsApp,
        "busquedaTexto" => $busquedaTexto,
        "filtroCategoria" => $filtroCategoria,
        "filtroDisponibilidad" => $filtroDisponibilidad,
        "categorias" => obtenerCategoriasCatalogo($connection),
        "productos" => obtenerProductosCatalogo(
            $connection,
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

function obtenerCategoriasCatalogo(PDO $connection): array
{
    $statement = $connection->query("
        SELECT
            id_categoria,
            nombre
        FROM listar_categorias_catalogo()
    ");

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosCatalogo(
    PDO $connection,
    string $busquedaTexto,
    ?int $filtroCategoria,
    string $filtroDisponibilidad
): array {
    $statement = $connection->prepare("
        SELECT
            id_producto,
            codigo,
            nombre,
            descripcion,
            imagen,
            categoria,
            precio_venta,
            stock
        FROM buscar_productos_catalogo(
            :busqueda,
            :id_categoria,
            :disponibilidad
        )
    ");

    $statement->execute([
        ":busqueda" => $busquedaTexto,
        ":id_categoria" => $filtroCategoria,
        ":disponibilidad" => $filtroDisponibilidad,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function recortarTextoCatalogo(?string $texto, int $limite = 145): string
{
    $texto = trim((string)$texto);

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
