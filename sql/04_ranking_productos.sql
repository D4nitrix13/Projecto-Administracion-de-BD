-- Productos más vendidos del mes actual (top 10)
CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_mes()
RETURNS TABLE(id_producto INTEGER, producto VARCHAR, codigo VARCHAR, cantidad_vendida BIGINT, total_vendido NUMERIC) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id_producto, p.nombre::VARCHAR, p.codigo,
           COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
           COALESCE(SUM(df.total_linea), 0)::NUMERIC AS total_vendido
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('month', CURRENT_DATE)
        AND f.fecha < date_trunc('month', CURRENT_DATE) + INTERVAL '1 month'
    GROUP BY p.id_producto, p.nombre, p.codigo
    ORDER BY cantidad_vendida DESC
    LIMIT 10;
END;
$$ LANGUAGE plpgsql;

-- Productos menos vendidos del mes actual (top 5, stock > 0)
CREATE OR REPLACE FUNCTION obtener_productos_menos_vendidos_mes()
RETURNS TABLE(id_producto INTEGER, producto VARCHAR, codigo VARCHAR, cantidad_vendida BIGINT, stock_actual INTEGER) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id_producto, p.nombre::VARCHAR, p.codigo,
           COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
           p.stock AS stock_actual
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('month', CURRENT_DATE)
        AND f.fecha < date_trunc('month', CURRENT_DATE) + INTERVAL '1 month'
    WHERE p.stock > 0
    GROUP BY p.id_producto, p.nombre, p.codigo, p.stock
    ORDER BY cantidad_vendida ASC
    LIMIT 5;
END;
$$ LANGUAGE plpgsql;

-- Productos más vendidos de la semana actual (top 10)
CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_semana()
RETURNS TABLE(id_producto INTEGER, producto VARCHAR, codigo VARCHAR, cantidad_vendida BIGINT, total_vendido NUMERIC) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id_producto, p.nombre::VARCHAR, p.codigo,
           COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
           COALESCE(SUM(df.total_linea), 0)::NUMERIC AS total_vendido
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('week', CURRENT_DATE)
        AND f.fecha < date_trunc('week', CURRENT_DATE) + INTERVAL '1 week'
    GROUP BY p.id_producto, p.nombre, p.codigo
    ORDER BY cantidad_vendida DESC
    LIMIT 10;
END;
$$ LANGUAGE plpgsql;

-- Productos menos vendidos de la semana actual (top 5, stock > 0)
CREATE OR REPLACE FUNCTION obtener_productos_menos_vendidos_semana()
RETURNS TABLE(id_producto INTEGER, producto VARCHAR, codigo VARCHAR, cantidad_vendida BIGINT, stock_actual INTEGER) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id_producto, p.nombre::VARCHAR, p.codigo,
           COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
           p.stock AS stock_actual
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('week', CURRENT_DATE)
        AND f.fecha < date_trunc('week', CURRENT_DATE) + INTERVAL '1 week'
    WHERE p.stock > 0
    GROUP BY p.id_producto, p.nombre, p.codigo, p.stock
    ORDER BY cantidad_vendida ASC
    LIMIT 5;
END;
$$ LANGUAGE plpgsql;

-- Productos más vendidos del año actual (top 10)
CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_anio()
RETURNS TABLE(id_producto INTEGER, producto VARCHAR, codigo VARCHAR, cantidad_vendida BIGINT, total_vendido NUMERIC) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id_producto, p.nombre::VARCHAR, p.codigo,
           COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
           COALESCE(SUM(df.total_linea), 0)::NUMERIC AS total_vendido
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('year', CURRENT_DATE)
        AND f.fecha < date_trunc('year', CURRENT_DATE) + INTERVAL '1 year'
    GROUP BY p.id_producto, p.nombre, p.codigo
    ORDER BY cantidad_vendida DESC
    LIMIT 10;
END;
$$ LANGUAGE plpgsql;

-- Productos menos vendidos del año actual (top 5, stock > 0)
CREATE OR REPLACE FUNCTION obtener_productos_menos_vendidos_anio()
RETURNS TABLE(id_producto INTEGER, producto VARCHAR, codigo VARCHAR, cantidad_vendida BIGINT, stock_actual INTEGER) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id_producto, p.nombre::VARCHAR, p.codigo,
           COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
           p.stock AS stock_actual
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
        AND f.fecha >= date_trunc('year', CURRENT_DATE)
        AND f.fecha < date_trunc('year', CURRENT_DATE) + INTERVAL '1 year'
    WHERE p.stock > 0
    GROUP BY p.id_producto, p.nombre, p.codigo, p.stock
    ORDER BY cantidad_vendida ASC
    LIMIT 5;
END;
$$ LANGUAGE plpgsql;

-- Categorías con menos productos (top 5)
CREATE OR REPLACE FUNCTION obtener_categorias_menos_productos()
RETURNS TABLE(id_categoria INTEGER, categoria VARCHAR, cantidad_productos BIGINT, stock_total BIGINT) AS $$
BEGIN
    RETURN QUERY
    SELECT c.id_categoria, c.nombre::VARCHAR,
           COUNT(p.id_producto)::BIGINT AS cantidad_productos,
           COALESCE(SUM(p.stock), 0)::BIGINT AS stock_total
    FROM Categoria c
    LEFT JOIN Producto p ON p.id_categoria = c.id_categoria
    GROUP BY c.id_categoria, c.nombre
    ORDER BY cantidad_productos ASC
    LIMIT 5;
END;
$$ LANGUAGE plpgsql;

-- Productos con stock bajo (<= 5)
CREATE OR REPLACE FUNCTION obtener_productos_stock_bajo()
RETURNS TABLE(id_producto INTEGER, producto VARCHAR, codigo VARCHAR, stock_actual INTEGER, nombre_categoria VARCHAR) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id_producto, p.nombre::VARCHAR, p.codigo, p.stock, c.nombre::VARCHAR
    FROM Producto p
    LEFT JOIN Categoria c ON c.id_categoria = p.id_categoria
    WHERE p.stock <= 5
    ORDER BY p.stock ASC;
END;
$$ LANGUAGE plpgsql;
