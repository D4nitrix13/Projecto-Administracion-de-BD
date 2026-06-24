<?php
// * Stored function or procedure has been executed

$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error = $_SESSION["flash_error"] ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$busquedaTexto = trim($_GET["q"] ?? "");
$filtroCategoriaRaw = $_GET["categoria"] ?? "";
$filtroProveedorRaw = $_GET["proveedor"] ?? "";
$filtroIdRaw = $_GET["id"] ?? "";
$filtroStock = $_GET["stock"] ?? "";
$filtroOrden = $_GET["orden"] ?? "nombre";

$filtroCategoria = ctype_digit((string) $filtroCategoriaRaw)
    ? (int) $filtroCategoriaRaw
    : null;

$filtroProveedor = ctype_digit((string) $filtroProveedorRaw)
    ? (int) $filtroProveedorRaw
    : null;

$filtroIdProducto = ctype_digit((string) $filtroIdRaw)
    ? (int) $filtroIdRaw
    : null;

$filtroStockBajo = $filtroStock === "bajo";

$ordenesPermitidos = [
    'nombre', 'mas_vendidos_mes', 'menos_vendidos_mes',
    'mas_vendidos_semana', 'menos_vendidos_semana',
    'mas_vendidos_anio', 'menos_vendidos_anio',
    'total_ventas', 'stock_bajo', 'precio_mayor', 'precio_menor'
];
$filtroOrden = in_array($filtroOrden, $ordenesPermitidos) ? $filtroOrden : 'nombre';

$stmtCat = $connection->query("
    SELECT
        id_categoria,
        nombre
    FROM listar_categorias_producto()
");

$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$stmtProv = $connection->query("
    SELECT
        id_proveedor,
        nombre
    FROM listar_proveedores_producto()
");

$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

$stmt = $connection->prepare("
    SELECT
        id_producto,
        codigo,
        nombre,
        descripcion,
        imagen,
        categoria,
        proveedor,
        precio_compra,
        precio_venta,
        stock,
        total_vendido
    FROM buscar_productos_inventario(
        :busqueda,
        :id_categoria,
        :id_proveedor,
        :id_producto,
        :stock_bajo,
        :orden
    )
");

$stmt->bindValue(":busqueda", $busquedaTexto, PDO::PARAM_STR);

if ($filtroCategoria !== null) {
    $stmt->bindValue(":id_categoria", $filtroCategoria, PDO::PARAM_INT);
} else {
    $stmt->bindValue(":id_categoria", null, PDO::PARAM_NULL);
}

if ($filtroProveedor !== null) {
    $stmt->bindValue(":id_proveedor", $filtroProveedor, PDO::PARAM_INT);
} else {
    $stmt->bindValue(":id_proveedor", null, PDO::PARAM_NULL);
}

if ($filtroIdProducto !== null) {
    $stmt->bindValue(":id_producto", $filtroIdProducto, PDO::PARAM_INT);
} else {
    $stmt->bindValue(":id_producto", null, PDO::PARAM_NULL);
}

$stmt->bindValue(":stock_bajo", $filtroStockBajo, PDO::PARAM_BOOL);
$stmt->bindValue(":orden", $filtroOrden, PDO::PARAM_STR);

$stmt->execute();

$allProductos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalProductos = count($allProductos);
$paginaActual = max(1, (int) ($_GET["pagina"] ?? 1));
$porPagina = 15;
$paginacionProductosList = calcularPaginacion($totalProductos, $paginaActual, $porPagina);
$productos = array_slice($allProductos, $paginacionProductosList["offset"], $porPagina);

if ($idRol === 1) {
    $textoSubtitulo = "Consulte, edite, compre stock o elimine productos del inventario.";
} elseif ($idRol === 2) {
    $textoSubtitulo = "Consulte, edite y administre productos del inventario.";
} elseif ($idRol === 3) {
    $textoSubtitulo = "Consulte la información de productos en modo solo lectura.";
} else {
    $textoSubtitulo = "Consulte la información de cada producto.";
}
