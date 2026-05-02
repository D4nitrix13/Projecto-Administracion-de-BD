<?php

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
        FROM Categoria
        ORDER BY nombre
    ");

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosCatalogo(
    PDO $connection,
    string $busquedaTexto,
    ?int $filtroCategoria,
    string $filtroDisponibilidad
): array {
    $sql = "
        SELECT
            p.id_producto,
            p.codigo,
            p.nombre,
            p.descripcion,
            p.imagen,
            c.nombre AS categoria,
            p.precio_venta,
            p.stock
        FROM Producto p
        LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
        WHERE 1 = 1
    ";

    $params = [];

    if ($busquedaTexto !== "") {
        $sql .= "
            AND (
                p.codigo ILIKE :q
                OR p.nombre ILIKE :q
                OR p.descripcion ILIKE :q
                OR c.nombre ILIKE :q
            )
        ";

        $params[":q"] = "%" . $busquedaTexto . "%";
    }

    if ($filtroCategoria !== null) {
        $sql .= " AND p.id_categoria = :categoria";
        $params[":categoria"] = $filtroCategoria;
    }

    if ($filtroDisponibilidad === "disponible") {
        $sql .= " AND p.stock > 5";
    } elseif ($filtroDisponibilidad === "stock_bajo") {
        $sql .= " AND p.stock > 0 AND p.stock <= 5";
    } elseif ($filtroDisponibilidad === "agotado") {
        $sql .= " AND p.stock <= 0";
    }

    $sql .= "
        ORDER BY 
            CASE 
                WHEN p.stock <= 0 THEN 2
                WHEN p.stock <= 5 THEN 1
                ELSE 0
            END,
            p.nombre ASC
    ";

    $statement = $connection->prepare($sql);
    $statement->execute($params);

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
