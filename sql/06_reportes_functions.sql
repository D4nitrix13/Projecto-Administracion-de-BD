-- ============================================================
-- Funciones PostgreSQL para Reportes
-- Solo funciones de CLIENTES y CATEGORÍAS (las de PRODUCTOS
-- ya están en 04_ranking_productos.sql con p_limit)
-- ============================================================

-- -------------------------------------------------------
-- CATEGORÍAS CON MENOS PRODUCTOS
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_categorias_menos_productos(p_limit integer DEFAULT 10)
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
    LIMIT p_limit;
$$;

-- -------------------------------------------------------
-- CLIENTES TOP COMPRAS — Semanal
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_top_compras_semanal(p_limit integer DEFAULT 10)
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
    GROUP BY cl.id_cliente, cl.nombres, cl.apellidos, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado DESC
    LIMIT p_limit;
$$;

-- -------------------------------------------------------
-- CLIENTES TOP COMPRAS — Mensual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_top_compras_mensual(p_limit integer DEFAULT 10)
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
    GROUP BY cl.id_cliente, cl.nombres, cl.apellidos, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado DESC
    LIMIT p_limit;
$$;

-- -------------------------------------------------------
-- CLIENTES TOP COMPRAS — Anual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_top_compras_anual(p_limit integer DEFAULT 10)
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
    GROUP BY cl.id_cliente, cl.nombres, cl.apellidos, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado DESC
    LIMIT p_limit;
$$;

-- -------------------------------------------------------
-- CLIENTES MENOS COMPRAS — Semanal
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_menos_compras_semanal(p_limit integer DEFAULT 10)
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
    GROUP BY cl.id_cliente, cl.nombres, cl.apellidos, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado ASC, cantidad_facturas ASC
    LIMIT p_limit;
$$;

-- -------------------------------------------------------
-- CLIENTES MENOS COMPRAS — Mensual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_menos_compras_mensual(p_limit integer DEFAULT 10)
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
    GROUP BY cl.id_cliente, cl.nombres, cl.apellidos, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado ASC, cantidad_facturas ASC
    LIMIT p_limit;
$$;

-- -------------------------------------------------------
-- CLIENTES MENOS COMPRAS — Anual
-- -------------------------------------------------------
CREATE OR REPLACE FUNCTION obtener_clientes_menos_compras_anual(p_limit integer DEFAULT 10)
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
    GROUP BY cl.id_cliente, cl.nombres, cl.apellidos, cl.telefono, cl.tipo_cliente
    ORDER BY total_comprado ASC, cantidad_facturas ASC
    LIMIT p_limit;
$$;
