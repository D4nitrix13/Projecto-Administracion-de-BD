<?php

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

function aplicarFiltroFechas(string &$sql, array &$params, ?string $fechaDesdeSql, ?string $fechaHastaSql, string $campo = "f.fecha"): void
{
    if ($fechaDesdeSql !== null) {
        $sql .= " AND {$campo} >= :fecha_desde";
        $params[":fecha_desde"] = $fechaDesdeSql;
    }

    if ($fechaHastaSql !== null) {
        $sql .= " AND {$campo} <= :fecha_hasta";
        $params[":fecha_hasta"] = $fechaHastaSql;
    }
}

function obtenerTotalClientesReportes(PDO $connection): int
{
    $statement = $connection->query("
        SELECT COUNT(*) 
        FROM Cliente
    ");

    return (int)$statement->fetchColumn();
}

function obtenerTotalFacturasReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): int
{
    $sql = "
        SELECT COUNT(*)
        FROM Factura f
        WHERE 1 = 1
    ";

    $params = [];
    aplicarFiltroFechas($sql, $params, $fechaDesdeSql, $fechaHastaSql);

    $statement = $connection->prepare($sql);
    $statement->execute($params);

    return (int)$statement->fetchColumn();
}

function obtenerTotalVentasReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): float
{
    $sql = "
        SELECT COALESCE(SUM(f.total), 0)
        FROM Factura f
        WHERE 1 = 1
    ";

    $params = [];
    aplicarFiltroFechas($sql, $params, $fechaDesdeSql, $fechaHastaSql);

    $statement = $connection->prepare($sql);
    $statement->execute($params);

    return (float)$statement->fetchColumn();
}

function obtenerStockBajoReportes(PDO $connection): int
{
    $statement = $connection->query("
        SELECT COUNT(*)
        FROM Producto
        WHERE stock <= 5
    ");

    return (int)$statement->fetchColumn();
}

function obtenerVentasPorDiaReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $sql = "
        SELECT
            TO_CHAR(f.fecha::date, 'YYYY-MM-DD') AS dia,
            COALESCE(SUM(f.total), 0) AS total_dia,
            COUNT(*) AS cantidad_facturas
        FROM Factura f
        WHERE 1 = 1
    ";

    $params = [];
    aplicarFiltroFechas($sql, $params, $fechaDesdeSql, $fechaHastaSql);

    $sql .= "
        GROUP BY f.fecha::date
        ORDER BY f.fecha::date ASC
        LIMIT 30
    ";

    $statement = $connection->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosMasVendidosReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $sql = "
        SELECT
            p.id_producto,
            p.nombre AS producto,
            p.codigo,
            SUM(df.cantidad) AS cantidad_vendida,
            SUM(df.total_linea) AS total_vendido
        FROM DetalleFactura df
        INNER JOIN Factura f ON f.id_factura = df.id_factura
        INNER JOIN Producto p ON p.id_producto = df.id_producto
        WHERE 1 = 1
    ";

    $params = [];
    aplicarFiltroFechas($sql, $params, $fechaDesdeSql, $fechaHastaSql);

    $sql .= "
        GROUP BY p.id_producto, p.nombre, p.codigo
        ORDER BY cantidad_vendida DESC, total_vendido DESC
        LIMIT 10
    ";

    $statement = $connection->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerVentasDetalladasReportes(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $sql = "
        SELECT
            f.id_factura,
            f.fecha,
            f.subtotal,
            f.descuento,
            f.impuesto,
            f.total,
            COALESCE(NULLIF(f.nombre_cliente_fugaz, ''), c.nombres || ' ' || c.apellidos) AS cliente,
            u.nombre AS usuario,
            s.nombre AS seccion
        FROM Factura f
        INNER JOIN Cliente c ON c.id_cliente = f.id_cliente
        INNER JOIN Usuario u ON u.id_usuario = f.id_usuario
        INNER JOIN Seccion s ON s.id_seccion = f.id_seccion
        WHERE 1 = 1
    ";

    $params = [];
    aplicarFiltroFechas($sql, $params, $fechaDesdeSql, $fechaHastaSql);

    $sql .= "
        ORDER BY f.fecha DESC, f.id_factura DESC
        LIMIT 50
    ";

    $statement = $connection->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosReporte(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $sql = "
        SELECT
            p.id_producto,
            p.codigo,
            p.nombre,
            p.stock,
            COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida,
            COALESCE(SUM(df.total_linea), 0) AS total_vendido
        FROM Producto p
        LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
        LEFT JOIN Factura f ON f.id_factura = df.id_factura
        WHERE 1 = 1
    ";

    $params = [];

    if ($fechaDesdeSql !== null) {
        $sql .= " AND (f.fecha IS NULL OR f.fecha >= :fecha_desde)";
        $params[":fecha_desde"] = $fechaDesdeSql;
    }

    if ($fechaHastaSql !== null) {
        $sql .= " AND (f.fecha IS NULL OR f.fecha <= :fecha_hasta)";
        $params[":fecha_hasta"] = $fechaHastaSql;
    }

    $sql .= "
        GROUP BY p.id_producto, p.codigo, p.nombre, p.stock
        ORDER BY cantidad_vendida DESC, total_vendido DESC, p.nombre ASC
        LIMIT 50
    ";

    $statement = $connection->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerClientesReporte(PDO $connection, ?string $fechaDesdeSql, ?string $fechaHastaSql): array
{
    $sql = "
        SELECT
            c.id_cliente,
            c.nombres || ' ' || c.apellidos AS cliente,
            c.telefono,
            c.tipo_cliente,
            COUNT(f.id_factura) AS cantidad_facturas,
            COALESCE(SUM(f.total), 0) AS total_comprado
        FROM Cliente c
        LEFT JOIN Factura f ON f.id_cliente = c.id_cliente
        WHERE 1 = 1
    ";

    $params = [];

    if ($fechaDesdeSql !== null) {
        $sql .= " AND (f.fecha IS NULL OR f.fecha >= :fecha_desde)";
        $params[":fecha_desde"] = $fechaDesdeSql;
    }

    if ($fechaHastaSql !== null) {
        $sql .= " AND (f.fecha IS NULL OR f.fecha <= :fecha_hasta)";
        $params[":fecha_hasta"] = $fechaHastaSql;
    }

    $sql .= "
        GROUP BY c.id_cliente, c.nombres, c.apellidos, c.telefono, c.tipo_cliente
        ORDER BY total_comprado DESC, cantidad_facturas DESC, cliente ASC
        LIMIT 50
    ";

    $statement = $connection->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
