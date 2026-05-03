<?php
// * Stored function or procedure has been executed

try {
    $stmtMetricas = $connection->query("
        SELECT
            total_clientes,
            total_productos,
            total_facturas,
            total_ventas,
            ventas_hoy,
            stock_bajo
        FROM obtener_metricas_dashboard()
    ");

    $metricas = $stmtMetricas->fetch(PDO::FETCH_ASSOC) ?: [];

    $totalClientes = $metricas["total_clientes"] ?? 0;
    $totalProductos = $metricas["total_productos"] ?? 0;
    $totalFacturas = $metricas["total_facturas"] ?? 0;
    $totalVentas = $metricas["total_ventas"] ?? 0;
    $ventasHoy = $metricas["ventas_hoy"] ?? 0;
    $stockBajo = $metricas["stock_bajo"] ?? 0;
} catch (PDOException $e) {
    error_log("metricasDashboard error: " . $e->getMessage());

    $totalClientes = 0;
    $totalProductos = 0;
    $totalFacturas = 0;
    $totalVentas = 0;
    $ventasHoy = 0;
    $stockBajo = 0;
}

try {
    $stmt = $connection->prepare("
        SELECT
            dia,
            total_dia
        FROM obtener_ventas_dashboard(:dias)
    ");

    $stmt->execute([
        ":dias" => 30
    ]);

    $ventasSemana = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("ventasSemana error: " . $e->getMessage());
    $ventasSemana = [];
}

try {
    $stmt = $connection->prepare("
        SELECT
            id_producto,
            producto,
            cantidad_vendida
        FROM obtener_productos_mas_vendidos_dashboard(:limite)
    ");

    $stmt->execute([
        ":limite" => 5
    ]);

    $productosMasVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("productosMasVendidos error: " . $e->getMessage());
    $productosMasVendidos = [];
}

try {
    $stmt = $connection->prepare("
        SELECT
            id_factura,
            id_producto,
            nombre,
            cantidad,
            subtotal,
            fecha
        FROM obtener_ultimos_productos_vendidos_dashboard(:limite)
    ");

    $stmt->execute([
        ":limite" => 6
    ]);

    $ultimosProductosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("ultimosProductosVendidos error: " . $e->getMessage());
    $ultimosProductosVendidos = [];
}

try {
    $stmt = $connection->prepare("
        SELECT
            id_factura,
            fecha,
            total
        FROM obtener_facturas_recientes_dashboard(:limite)
    ");

    $stmt->execute([
        ":limite" => 5
    ]);

    $facturasRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("facturasRecientes error: " . $e->getMessage());
    $facturasRecientes = [];
}

try {
    $stmt = $connection->prepare("
        SELECT
            id_cliente,
            nombre,
            telefono,
            fecha_registro
        FROM obtener_clientes_recientes_dashboard(:limite)
    ");

    $stmt->execute([
        ":limite" => 5
    ]);

    $clientesRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("clientesRecientes error: " . $e->getMessage());
    $clientesRecientes = [];
}
