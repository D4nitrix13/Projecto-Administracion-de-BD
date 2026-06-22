-- ============================================================
-- Funciones PostgreSQL para Reportes
-- Productos más/menos vendidos + Categorías débiles + Clientes
-- ============================================================

-- -------------------------------------------------------
-- PRODUCTOS MÁS VENDIDOS — Mes actual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_mes()
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    codigo VARCHAR,
    cantidad_vendida BIGINT,
    total_vendido NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        p.id_producto,
        p.nombre AS producto,
        p.codigo,
        COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida,
        COALESCE(SUM(df.total_linea), 0) AS total_vendido
    FROM Producto p
    INNER JOIN DetalleFactura df ON df.id_producto = p.id_producto
    INNER JOIN Factura f ON f.id_factura = df.id_factura
    WHERE f.fecha >= date_trunc('month', CURRENT_DATE)
      AND f.fecha < date_trunc('month', CURRENT_DATE) + INTERVAL '1 month'
      AND f.estado_produccion != 'Cancelada'
    GROUP BY p.id_producto, p.nombre, p.codigo
    ORDER BY cantidad_vendida DESC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- PRODUCTOS MENOS VENDIDOS — Mes actual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_productos_menos_vendidos_mes()
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    codigo VARCHAR,
    cantidad_vendida BIGINT,
    stock_actual INT
)
LANGUAGE sql STABLE
AS $$
    SELECT
        p.id_producto,
        p.nombre AS producto,
        p.codigo,
        COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida,
        p.stock AS stock_actual
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('month', CURRENT_DATE)
        AND f.fecha < date_trunc('month', CURRENT_DATE) + INTERVAL '1 month'
        AND f.estado_produccion != 'Cancelada'
    WHERE p.stock > 0
    GROUP BY p.id_producto, p.nombre, p.codigo, p.stock
    ORDER BY cantidad_vendida ASC, p.stock DESC
    LIMIT 5;
$$;

-- -------------------------------------------------------
-- PRODUCTOS MÁS VENDIDOS — Semana actual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_semana()
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    codigo VARCHAR,
    cantidad_vendida BIGINT,
    total_vendido NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        p.id_producto,
        p.nombre AS producto,
        p.codigo,
        COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida,
        COALESCE(SUM(df.total_linea), 0) AS total_vendido
    FROM Producto p
    INNER JOIN DetalleFactura df ON df.id_producto = p.id_producto
    INNER JOIN Factura f ON f.id_factura = df.id_factura
    WHERE f.fecha >= date_trunc('week', CURRENT_DATE)
      AND f.fecha < date_trunc('week', CURRENT_DATE) + INTERVAL '1 week'
      AND f.estado_produccion != 'Cancelada'
    GROUP BY p.id_producto, p.nombre, p.codigo
    ORDER BY cantidad_vendida DESC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- PRODUCTOS MENOS VENDIDOS — Semana actual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_productos_menos_vendidos_semana()
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    codigo VARCHAR,
    cantidad_vendida BIGINT,
    stock_actual INT
)
LANGUAGE sql STABLE
AS $$
    SELECT
        p.id_producto,
        p.nombre AS producto,
        p.codigo,
        COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida,
        p.stock AS stock_actual
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('week', CURRENT_DATE)
        AND f.fecha < date_trunc('week', CURRENT_DATE) + INTERVAL '1 week'
        AND f.estado_produccion != 'Cancelada'
    WHERE p.stock > 0
    GROUP BY p.id_producto, p.nombre, p.codigo, p.stock
    ORDER BY cantidad_vendida ASC, p.stock DESC
    LIMIT 5;
$$;

-- -------------------------------------------------------
-- PRODUCTOS MÁS VENDIDOS — Año actual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_anio()
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    codigo VARCHAR,
    cantidad_vendida BIGINT,
    total_vendido NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        p.id_producto,
        p.nombre AS producto,
        p.codigo,
        COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida,
        COALESCE(SUM(df.total_linea), 0) AS total_vendido
    FROM Producto p
    INNER JOIN DetalleFactura df ON df.id_producto = p.id_producto
    INNER JOIN Factura f ON f.id_factura = df.id_factura
    WHERE f.fecha >= date_trunc('year', CURRENT_DATE)
      AND f.fecha < date_trunc('year', CURRENT_DATE) + INTERVAL '1 year'
      AND f.estado_produccion != 'Cancelada'
    GROUP BY p.id_producto, p.nombre, p.codigo
    ORDER BY cantidad_vendida DESC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- PRODUCTOS MENOS VENDIDOS — Año actual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_productos_menos_vendidos_anio()
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    codigo VARCHAR,
    cantidad_vendida BIGINT,
    stock_actual INT
)
LANGUAGE sql STABLE
AS $$
    SELECT
        p.id_producto,
        p.nombre AS producto,
        p.codigo,
        COALESCE(SUM(df.cantidad), 0) AS cantidad_vendida,
        p.stock AS stock_actual
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('year', CURRENT_DATE)
        AND f.fecha < date_trunc('year', CURRENT_DATE) + INTERVAL '1 year'
        AND f.estado_produccion != 'Cancelada'
    WHERE p.stock > 0
    GROUP BY p.id_producto, p.nombre, p.codigo, p.stock
    ORDER BY cantidad_vendida ASC, p.stock DESC
    LIMIT 5;
$$;

-- -------------------------------------------------------
-- CATEGORÍAS CON MENOS PRODUCTOS
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_categorias_menos_productos()
RETURNS TABLE (
    id_categoria INT,
    categoria VARCHAR,
    cantidad_productos BIGINT,
    stock_total BIGINT
)
LANGUAGE sql STABLE
AS $$
    SELECT
        c.id_categoria,
        c.nombre AS categoria,
        COUNT(p.id_producto) AS cantidad_productos,
        COALESCE(SUM(p.stock), 0) AS stock_total
    FROM Categoria c
    LEFT JOIN Producto p ON p.id_categoria = c.id_categoria
    GROUP BY c.id_categoria, c.nombre
    ORDER BY cantidad_productos ASC, stock_total ASC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- CLIENTES TOP COMPRAS — Semanal
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_top_compras_semanal()
RETURNS TABLE (
    id_cliente INT,
    cliente VARCHAR,
    telefono VARCHAR,
    tipo_cliente VARCHAR,
    cantidad_facturas BIGINT,
    total_comprado NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        cl.id_cliente,
        TRIM(cl.nombres || ' ' || cl.apellidos) AS cliente,
        COALESCE(cl.telefono, '') AS telefono,
        cl.tipo_cliente,
        COUNT(f.id_factura) AS cantidad_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado
    FROM Cliente cl
    INNER JOIN Factura f ON f.id_cliente = cl.id_cliente
    WHERE f.fecha >= date_trunc('week', CURRENT_DATE)
      AND f.fecha < date_trunc('week', CURRENT_DATE) + INTERVAL '1 week'
      AND f.estado_produccion != 'Cancelada'
    GROUP BY cl.id_cliente, cl.nombre, cl.apellido, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado DESC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- CLIENTES TOP COMPRAS — Mensual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_top_compras_mensual()
RETURNS TABLE (
    id_cliente INT,
    cliente VARCHAR,
    telefono VARCHAR,
    tipo_cliente VARCHAR,
    cantidad_facturas BIGINT,
    total_comprado NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        cl.id_cliente,
        TRIM(cl.nombres || ' ' || cl.apellidos) AS cliente,
        COALESCE(cl.telefono, '') AS telefono,
        cl.tipo_cliente,
        COUNT(f.id_factura) AS cantidad_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado
    FROM Cliente cl
    INNER JOIN Factura f ON f.id_cliente = cl.id_cliente
    WHERE f.fecha >= date_trunc('month', CURRENT_DATE)
      AND f.fecha < date_trunc('month', CURRENT_DATE) + INTERVAL '1 month'
      AND f.estado_produccion != 'Cancelada'
    GROUP BY cl.id_cliente, cl.nombre, cl.apellido, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado DESC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- CLIENTES TOP COMPRAS — Anual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_top_compras_anual()
RETURNS TABLE (
    id_cliente INT,
    cliente VARCHAR,
    telefono VARCHAR,
    tipo_cliente VARCHAR,
    cantidad_facturas BIGINT,
    total_comprado NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        cl.id_cliente,
        TRIM(cl.nombres || ' ' || cl.apellidos) AS cliente,
        COALESCE(cl.telefono, '') AS telefono,
        cl.tipo_cliente,
        COUNT(f.id_factura) AS cantidad_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado
    FROM Cliente cl
    INNER JOIN Factura f ON f.id_cliente = cl.id_cliente
    WHERE f.fecha >= date_trunc('year', CURRENT_DATE)
      AND f.fecha < date_trunc('year', CURRENT_DATE) + INTERVAL '1 year'
      AND f.estado_produccion != 'Cancelada'
    GROUP BY cl.id_cliente, cl.nombre, cl.apellido, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado DESC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- CLIENTES MENOS COMPRAS — Semanal
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_menos_compras_semanal()
RETURNS TABLE (
    id_cliente INT,
    cliente VARCHAR,
    telefono VARCHAR,
    tipo_cliente VARCHAR,
    cantidad_facturas BIGINT,
    total_comprado NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        cl.id_cliente,
        TRIM(cl.nombres || ' ' || cl.apellidos) AS cliente,
        COALESCE(cl.telefono, '') AS telefono,
        cl.tipo_cliente,
        COUNT(f.id_factura) AS cantidad_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado
    FROM Cliente cl
    LEFT JOIN Factura f ON f.id_cliente = cl.id_cliente
        AND f.fecha >= date_trunc('week', CURRENT_DATE)
        AND f.fecha < date_trunc('week', CURRENT_DATE) + INTERVAL '1 week'
        AND f.estado_produccion != 'Cancelada'
    WHERE cl.tipo_cliente = 'Mayorista'
    GROUP BY cl.id_cliente, cl.nombre, cl.apellido, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado ASC, cantidad_facturas ASC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- CLIENTES MENOS COMPRAS — Mensual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_menos_compras_mensual()
RETURNS TABLE (
    id_cliente INT,
    cliente VARCHAR,
    telefono VARCHAR,
    tipo_cliente VARCHAR,
    cantidad_facturas BIGINT,
    total_comprado NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        cl.id_cliente,
        TRIM(cl.nombres || ' ' || cl.apellidos) AS cliente,
        COALESCE(cl.telefono, '') AS telefono,
        cl.tipo_cliente,
        COUNT(f.id_factura) AS cantidad_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado
    FROM Cliente cl
    LEFT JOIN Factura f ON f.id_cliente = cl.id_cliente
        AND f.fecha >= date_trunc('month', CURRENT_DATE)
        AND f.fecha < date_trunc('month', CURRENT_DATE) + INTERVAL '1 month'
        AND f.estado_produccion != 'Cancelada'
    WHERE cl.tipo_cliente = 'Mayorista'
    GROUP BY cl.id_cliente, cl.nombre, cl.apellido, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado ASC, cantidad_facturas ASC
    LIMIT 10;
$$;

-- -------------------------------------------------------
-- CLIENTES MENOS COMPRAS — Anual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_menos_compras_anual()
RETURNS TABLE (
    id_cliente INT,
    cliente VARCHAR,
    telefono VARCHAR,
    tipo_cliente VARCHAR,
    cantidad_facturas BIGINT,
    total_comprado NUMERIC
)
LANGUAGE sql STABLE
AS $$
    SELECT
        cl.id_cliente,
        TRIM(cl.nombres || ' ' || cl.apellidos) AS cliente,
        COALESCE(cl.telefono, '') AS telefono,
        cl.tipo_cliente,
        COUNT(f.id_factura) AS cantidad_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado
    FROM Cliente cl
    LEFT JOIN Factura f ON f.id_cliente = cl.id_cliente
        AND f.fecha >= date_trunc('year', CURRENT_DATE)
        AND f.fecha < date_trunc('year', CURRENT_DATE) + INTERVAL '1 year'
        AND f.estado_produccion != 'Cancelada'
    WHERE cl.tipo_cliente = 'Mayorista'
    GROUP BY cl.id_cliente, cl.nombre, cl.apellido, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado ASC, cantidad_facturas ASC
    LIMIT 10;
$$;
