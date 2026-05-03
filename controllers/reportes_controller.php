<?php
// * Stored function or procedure has been executed

function obtenerDatosReportes(): array
{
    $user = $_SESSION["user"];

    /** @var PDO $connection */
    $connection = require __DIR__ . "/../sql/db.php";

    $tipoReporte = $_GET["tipo"] ?? "general";
    $fechaDesde = $_GET["desde"] ?? "";
    $fechaHasta = $_GET["hasta"] ?? "";

    $tipoReporte = in_array($tipoReporte, ["general", "ventas", "productos", "clientes"], true)
        ? $tipoReporte
        : "general";

    $fechas = normalizarRangoFechasReportes($fechaDesde, $fechaHasta);
    $fechaDesdeSql = $fechas["desde_sql"];
    $fechaHastaSql = $fechas["hasta_sql"];

    return [
        "user" => $user,
        "tipoReporte" => $tipoReporte,
        "fechaDesde" => $fechaDesde,
        "fechaHasta" => $fechaHasta,

        "totalClientes" => obtenerTotalClientesReportes($connection),
        "totalFacturas" => obtenerTotalFacturasReportes($connection, $fechaDesdeSql, $fechaHastaSql),
        "totalVentas" => obtenerTotalVentasReportes($connection, $fechaDesdeSql, $fechaHastaSql),
        "stockBajo" => obtenerStockBajoReportes($connection),

        "ventasPorDia" => obtenerVentasPorDiaReportes($connection, $fechaDesdeSql, $fechaHastaSql),
        "productosMasVendidos" => obtenerProductosMasVendidosReportes($connection, $fechaDesdeSql, $fechaHastaSql),
        "ventasDetalladas" => obtenerVentasDetalladasReportes($connection, $fechaDesdeSql, $fechaHastaSql),
        "productosReporte" => obtenerProductosReporte($connection, $fechaDesdeSql, $fechaHastaSql),
        "clientesReporte" => obtenerClientesReporte($connection, $fechaDesdeSql, $fechaHastaSql),
    ];
}

function normalizarRangoFechasReportes(string $fechaDesde, string $fechaHasta): array
{
    $desdeSql = null;
    $hastaSql = null;

    if ($fechaDesde !== "") {
        $desdeSql = $fechaDesde . " 00:00:00";
    }

    if ($fechaHasta !== "") {
        $hastaSql = $fechaHasta . " 23:59:59";
    }

    return [
        "desde_sql" => $desdeSql,
        "hasta_sql" => $hastaSql,
    ];
}

function obtenerTotalClientesReportes(PDO $connection): int
{
    $statement = $connection->query("
        SELECT obtener_total_clientes_reportes()
    ");

    return (int)$statement->fetchColumn();
}

function obtenerTotalFacturasReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): int
{
    $statement = $connection->prepare("
        SELECT obtener_total_facturas_reportes(
            :fecha_desde,
            :fecha_hasta
        )
    ");

    $statement->execute([
        ":fecha_desde" => $fechaDesdeSql,
        ":fecha_hasta" => $fechaHastaSql,
    ]);

    return (int)$statement->fetchColumn();
}

function obtenerTotalVentasReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): float
{
    $statement = $connection->prepare("
        SELECT obtener_total_ventas_reportes(
            :fecha_desde,
            :fecha_hasta
        )
    ");

    $statement->execute([
        ":fecha_desde" => $fechaDesdeSql,
        ":fecha_hasta" => $fechaHastaSql,
    ]);

    return (float)$statement->fetchColumn();
}

function obtenerStockBajoReportes(PDO $connection): int
{
    $statement = $connection->query("
        SELECT obtener_stock_bajo_reportes()
    ");

    return (int)$statement->fetchColumn();
}

function obtenerVentasPorDiaReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $statement = $connection->prepare("
        SELECT
            dia,
            total_dia,
            cantidad_facturas
        FROM obtener_ventas_por_dia_reportes(
            :fecha_desde,
            :fecha_hasta
        )
    ");

    $statement->execute([
        ":fecha_desde" => $fechaDesdeSql,
        ":fecha_hasta" => $fechaHastaSql,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosMasVendidosReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $statement = $connection->prepare("
        SELECT
            id_producto,
            producto,
            codigo,
            cantidad_vendida,
            total_vendido
        FROM obtener_productos_mas_vendidos_reportes(
            :fecha_desde,
            :fecha_hasta
        )
    ");

    $statement->execute([
        ":fecha_desde" => $fechaDesdeSql,
        ":fecha_hasta" => $fechaHastaSql,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerVentasDetalladasReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $statement = $connection->prepare("
        SELECT
            id_factura,
            fecha,
            subtotal,
            descuento,
            impuesto,
            total,
            cliente,
            usuario,
            seccion
        FROM obtener_ventas_detalladas_reportes(
            :fecha_desde,
            :fecha_hasta
        )
    ");

    $statement->execute([
        ":fecha_desde" => $fechaDesdeSql,
        ":fecha_hasta" => $fechaHastaSql,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosReporte(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $statement = $connection->prepare("
        SELECT
            id_producto,
            codigo,
            nombre,
            stock,
            cantidad_vendida,
            total_vendido
        FROM obtener_productos_reporte(
            :fecha_desde,
            :fecha_hasta
        )
    ");

    $statement->execute([
        ":fecha_desde" => $fechaDesdeSql,
        ":fecha_hasta" => $fechaHastaSql,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerClientesReporte(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $statement = $connection->prepare("
        SELECT
            id_cliente,
            cliente,
            telefono,
            tipo_cliente,
            cantidad_facturas,
            total_comprado
        FROM obtener_clientes_reporte(
            :fecha_desde,
            :fecha_hasta
        )
    ");

    $statement->execute([
        ":fecha_desde" => $fechaDesdeSql,
        ":fecha_hasta" => $fechaHastaSql,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
