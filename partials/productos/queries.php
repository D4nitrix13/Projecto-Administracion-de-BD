<?php

$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error = $_SESSION["flash_error"] ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$busquedaTexto = trim($_GET["q"] ?? "");
$filtroCategoriaRaw = $_GET["categoria"] ?? "";
$filtroProveedorRaw = $_GET["proveedor"] ?? "";
$filtroIdRaw = $_GET["id"] ?? "";
$filtroStock = $_GET["stock"] ?? "";

$filtroCategoria = ctype_digit((string)$filtroCategoriaRaw) ? (int)$filtroCategoriaRaw : null;
$filtroProveedor = ctype_digit((string)$filtroProveedorRaw) ? (int)$filtroProveedorRaw : null;
$filtroIdProducto = ctype_digit((string)$filtroIdRaw) ? (int)$filtroIdRaw : null;
$filtroStockBajo = $filtroStock === "bajo";

$stmtCat = $connection->query("
    SELECT id_categoria, nombre
    FROM Categoria
    ORDER BY nombre
");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$stmtProv = $connection->query("
    SELECT id_proveedor, nombre
    FROM Proveedor
    ORDER BY nombre
");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

$sql = "
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.imagen,
        c.nombre AS categoria,
        pr.nombre AS proveedor,
        p.precio_compra,
        p.precio_venta,
        p.stock
    FROM Producto p
    LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
    LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
    WHERE 1 = 1
";

$params = [];

if ($filtroIdProducto !== null) {
    $sql .= " AND p.id_producto = :id_producto";
    $params[":id_producto"] = $filtroIdProducto;
}

if ($busquedaTexto !== "") {
    $sql .= "
        AND (
            LOWER(p.codigo) LIKE LOWER(:q)
            OR LOWER(p.nombre) LIKE LOWER(:q)
        )
    ";
    $params[":q"] = "%" . $busquedaTexto . "%";
}

if ($filtroCategoria !== null) {
    $sql .= " AND p.id_categoria = :categoria";
    $params[":categoria"] = $filtroCategoria;
}

if ($filtroProveedor !== null) {
    $sql .= " AND p.id_proveedor = :proveedor";
    $params[":proveedor"] = $filtroProveedor;
}

if ($filtroStockBajo) {
    $sql .= " AND p.stock <= 5";
}

$sql .= " ORDER BY p.nombre ASC";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($idRol === 1) {
    $textoSubtitulo = "Consulte, edite, compre stock o elimine productos del inventario.";
} elseif ($idRol === 2) {
    $textoSubtitulo = "Consulte, edite y administre productos del inventario.";
} elseif ($idRol === 3) {
    $textoSubtitulo = "Consulte la información de productos en modo solo lectura.";
} else {
    $textoSubtitulo = "Consulte la información de cada producto.";
}
