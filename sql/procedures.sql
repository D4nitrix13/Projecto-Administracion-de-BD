-- ============================================================
-- FUNCIÓN: Obtener información de imagen de producto
-- Sistema: Panda Estampados / Kitsune
-- Uso: partials/ver-imagen-productos/queries.php
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_producto_imagen(
    p_id_producto INT
)
RETURNS TABLE (
    id_producto INT,
    codigo VARCHAR,
    nombre VARCHAR,
    descripcion TEXT,
    imagen VARCHAR,
    precio_venta NUMERIC,
    stock INT
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_producto IS NULL OR p_id_producto <= 0 THEN
        RAISE EXCEPTION 'ID de producto no válido';
    END IF;

    RETURN QUERY
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.imagen,
        p.precio_venta,
        p.stock
    FROM Producto p
    WHERE p.id_producto = p_id_producto;
END;
$$;

-- ============================================================
-- FUNCIÓN: Obtener cliente por ID
-- Uso: detalle de cliente / vista de cliente
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_cliente_por_id(
    p_id_cliente INT
)
RETURNS TABLE (
    id_cliente INT,
    nombres VARCHAR,
    apellidos VARCHAR,
    telefono VARCHAR,
    direccion VARCHAR,
    identificacion VARCHAR,
    tipo_cliente VARCHAR,
    fecha_registro DATE
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_cliente IS NULL OR p_id_cliente <= 0 THEN
        RAISE EXCEPTION 'ID de cliente no válido';
    END IF;

    RETURN QUERY
    SELECT
        c.id_cliente,
        c.nombres,
        c.apellidos,
        c.telefono,
        c.direccion,
        c.identificacion,
        c.tipo_cliente,
        c.fecha_registro
    FROM Cliente c
    WHERE c.id_cliente = p_id_cliente;
END;
$$;


-- ============================================================
-- FUNCIÓN: Obtener últimas facturas de un cliente
-- Uso: historial reciente de facturas del cliente
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_ultimas_facturas_cliente(
    p_id_cliente INT,
    p_limite INT DEFAULT 10
)
RETURNS TABLE (
    id_factura INT,
    fecha TIMESTAMP,
    subtotal NUMERIC,
    descuento NUMERIC,
    impuesto NUMERIC,
    total NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_cliente IS NULL OR p_id_cliente <= 0 THEN
        RAISE EXCEPTION 'ID de cliente no válido';
    END IF;

    IF p_limite IS NULL OR p_limite <= 0 THEN
        RAISE EXCEPTION 'El límite de facturas debe ser mayor que cero';
    END IF;

    RETURN QUERY
    SELECT
        f.id_factura,
        f.fecha,
        f.subtotal,
        f.descuento,
        f.impuesto,
        f.total
    FROM Factura f
    WHERE f.id_cliente = p_id_cliente
    ORDER BY f.fecha DESC, f.id_factura DESC
    LIMIT p_limite;
END;
$$;


-- ============================================================
-- FUNCIÓN: Obtener resumen de compras de un cliente
-- Uso: resumen estadístico del cliente
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_resumen_cliente(
    p_id_cliente INT
)
RETURNS TABLE (
    total_facturas BIGINT,
    total_comprado NUMERIC,
    promedio_compra NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_cliente IS NULL OR p_id_cliente <= 0 THEN
        RAISE EXCEPTION 'ID de cliente no válido';
    END IF;

    RETURN QUERY
    SELECT
        COUNT(*) AS total_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado,
        COALESCE(AVG(f.total), 0) AS promedio_compra
    FROM Factura f
    WHERE f.id_cliente = p_id_cliente;
END;
$$;

-- ============================================================
-- FUNCIÓN: Listar categorías para filtros de productos
-- Uso: módulo de productos / filtros
-- ============================================================

CREATE OR REPLACE FUNCTION listar_categorias_producto()
RETURNS TABLE (
    id_categoria INT,
    nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        c.id_categoria,
        c.nombre
    FROM Categoria c
    ORDER BY c.nombre;
END;
$$;


-- ============================================================
-- FUNCIÓN: Listar proveedores para filtros de productos
-- Uso: módulo de productos / filtros
-- ============================================================

CREATE OR REPLACE FUNCTION listar_proveedores_producto()
RETURNS TABLE (
    id_proveedor INT,
    nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        p.id_proveedor,
        p.nombre
    FROM Proveedor p
    ORDER BY p.nombre;
END;
$$;


-- ============================================================
-- FUNCIÓN: Buscar productos del inventario
-- Uso: módulo de productos con filtros
-- ============================================================

CREATE OR REPLACE FUNCTION buscar_productos_inventario(
    p_busqueda TEXT DEFAULT '',
    p_id_categoria INT DEFAULT NULL,
    p_id_proveedor INT DEFAULT NULL,
    p_id_producto INT DEFAULT NULL,
    p_stock_bajo BOOLEAN DEFAULT FALSE
)
RETURNS TABLE (
    id_producto INT,
    codigo VARCHAR,
    nombre VARCHAR,
    descripcion TEXT,
    imagen VARCHAR,
    categoria VARCHAR,
    proveedor VARCHAR,
    precio_compra NUMERIC,
    precio_venta NUMERIC,
    stock INT
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        prod.id_producto,
        prod.codigo,
        prod.nombre,
        prod.descripcion,
        prod.imagen,
        cat.nombre AS categoria,
        prov.nombre AS proveedor,
        prod.precio_compra,
        prod.precio_venta,
        prod.stock
    FROM Producto prod
    LEFT JOIN Categoria cat ON prod.id_categoria = cat.id_categoria
    LEFT JOIN Proveedor prov ON prod.id_proveedor = prov.id_proveedor
    WHERE
        (
            p_id_producto IS NULL
            OR prod.id_producto = p_id_producto
        )
        AND (
            COALESCE(TRIM(p_busqueda), '') = ''
            OR prod.codigo ILIKE '%' || TRIM(p_busqueda) || '%'
            OR prod.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
        )
        AND (
            p_id_categoria IS NULL
            OR prod.id_categoria = p_id_categoria
        )
        AND (
            p_id_proveedor IS NULL
            OR prod.id_proveedor = p_id_proveedor
        )
        AND (
            p_stock_bajo = FALSE
            OR prod.stock <= 5
        )
    ORDER BY prod.nombre ASC;
END;
$$;

-- ============================================================
-- FUNCIÓN: Registrar categoría
-- Uso: módulo de categorías / creación
-- ============================================================

CREATE OR REPLACE FUNCTION registrar_categoria(
    p_nombre VARCHAR
)
RETURNS TABLE (
    id_categoria INT,
    nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre de la categoría es obligatorio';
    END IF;

    IF LENGTH(TRIM(p_nombre)) > 80 THEN
        RAISE EXCEPTION 'El nombre de la categoría no debe superar los 80 caracteres';
    END IF;

    RETURN QUERY
    INSERT INTO Categoria (
        nombre
    )
    VALUES (
        TRIM(p_nombre)
    )
    RETURNING
        Categoria.id_categoria,
        Categoria.nombre;
END;
$$;


-- ============================================================
-- FUNCIÓN: Buscar categorías
-- Uso: módulo de categorías / listado y búsqueda
-- ============================================================

CREATE OR REPLACE FUNCTION buscar_categorias(
    p_busqueda TEXT DEFAULT ''
)
RETURNS TABLE (
    id_categoria INT,
    nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        c.id_categoria,
        c.nombre
    FROM Categoria c
    WHERE
        COALESCE(TRIM(p_busqueda), '') = ''
        OR c.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
    ORDER BY c.nombre ASC;
END;
$$;