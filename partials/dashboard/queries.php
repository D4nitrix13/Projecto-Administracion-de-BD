<?php

function fetchValue(PDO $connection, string $sql): mixed
{
    try {
        return $connection->query($sql)->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        return 0;
    }
}

$totalClientes = fetchValue($connection, "SELECT COUNT(*) FROM Cliente");
$totalProductos = fetchValue($connection, "SELECT COUNT(*) FROM Producto");
$totalFacturas = fetchValue($connection, "SELECT COUNT(*) FROM Factura");
$totalVentas = fetchValue($connection, "SELECT COALESCE(SUM(total), 0) FROM Factura");
$ventasHoy = fetchValue($connection, "SELECT COALESCE(SUM(total), 0) FROM Factura WHERE DATE(fecha) = CURRENT_DATE");
$stockBajo = fetchValue($connection, "SELECT COUNT(*) FROM Producto WHERE stock <= 5");

try {
    $stmt = $connection->query("
        SELECT nombre, stock, precio_venta
        FROM Producto
        ORDER BY id_producto DESC
        LIMIT 5
    ");

    $productosRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $productosRecientes = [];
}
