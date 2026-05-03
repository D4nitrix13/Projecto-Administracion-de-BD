<?php
// * Stored function or procedure has been executed

$id = isset($_GET["id"]) && ctype_digit((string) $_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($id <= 0) {
    $_SESSION["flash_error"] = "ID de producto no válido.";
    header("Location: productos.php");
    exit();
}

$stmt = $connection->prepare("
    SELECT
        id_producto,
        codigo,
        nombre,
        descripcion,
        imagen,
        precio_venta,
        stock
    FROM obtener_producto_imagen(:id)
");

$stmt->execute([
    ":id" => $id
]);

$prod = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prod) {
    $_SESSION["flash_error"] = "Producto no encontrado.";
    header("Location: productos.php");
    exit();
}

$imagen = trim($prod["imagen"] ?? "");

$rutaImagen = $imagen !== ""
    ? "uploads/productos/" . $imagen
    : "assets/img/no-product.png";
