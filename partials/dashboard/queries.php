<?php

function fetchValue(PDO $connection, string $sql): mixed
{
    try {
        $value = $connection->query($sql)->fetchColumn();
        return $value !== false ? $value : 0;
    } catch (PDOException $e) {
        error_log("Dashboard query error: " . $e->getMessage());
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
        SELECT 
            DATE(fecha) AS dia,
            COALESCE(SUM(total), 0) AS total_dia
        FROM Factura
        WHERE fecha >= CURRENT_DATE - INTERVAL '30 days'
        GROUP BY DATE(fecha)
        ORDER BY dia ASC
    ");
    $ventasSemana = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("ventasSemana error: " . $e->getMessage());
    $ventasSemana = [];
}

try {
    $stmt = $connection->query("
        SELECT 
            p.id_producto,
            p.nombre AS producto,
            COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida
        FROM DetalleFactura df
        INNER JOIN Producto p ON p.id_producto = df.id_producto
        GROUP BY p.id_producto, p.nombre
        ORDER BY cantidad_vendida DESC
        LIMIT 5
    ");
    $productosMasVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("productosMasVendidos error: " . $e->getMessage());
    $productosMasVendidos = [];
}

try {
    $stmt = $connection->query("
        SELECT 
            f.id_factura,
            p.id_producto,
            p.nombre,
            df.cantidad,
            df.total_linea AS subtotal,
            f.fecha
        FROM DetalleFactura df
        INNER JOIN Producto p ON p.id_producto = df.id_producto
        INNER JOIN Factura f ON f.id_factura = df.id_factura
        ORDER BY f.fecha DESC, f.id_factura DESC
        LIMIT 6
    ");
    $ultimosProductosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("ultimosProductosVendidos error: " . $e->getMessage());
    $ultimosProductosVendidos = [];
}

try {
    $stmt = $connection->query("
        SELECT 
            id_factura,
            fecha,
            total
        FROM Factura
        ORDER BY fecha DESC, id_factura DESC
        LIMIT 5
    ");
    $facturasRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("facturasRecientes error: " . $e->getMessage());
    $facturasRecientes = [];
}

try {
    $stmt = $connection->query("
        SELECT 
            id_cliente,
            CONCAT(nombres, ' ', apellidos) AS nombre,
            telefono
        FROM Cliente
        ORDER BY id_cliente DESC
        LIMIT 5
    ");
    $clientesRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("clientesRecientes error: " . $e->getMessage());
    $clientesRecientes = [];
}
