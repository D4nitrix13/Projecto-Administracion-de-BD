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

-- ============================================================
-- FUNCIÓN: Obtener categoría por ID
-- Uso: módulo editar categoría
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_categoria_por_id(
    p_id_categoria INT
)
RETURNS TABLE (
    id_categoria INT,
    nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_categoria IS NULL OR p_id_categoria <= 0 THEN
        RAISE EXCEPTION 'ID de categoría no válido';
    END IF;

    RETURN QUERY
    SELECT
        c.id_categoria,
        c.nombre
    FROM Categoria c
    WHERE c.id_categoria = p_id_categoria;
END;
$$;


-- ============================================================
-- FUNCIÓN: Actualizar categoría
-- Uso: módulo editar categoría
-- ============================================================

CREATE OR REPLACE FUNCTION actualizar_categoria(
    p_id_categoria INT,
    p_nombre VARCHAR
)
RETURNS TABLE (
    filas_afectadas INT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_filas_afectadas INT;
BEGIN
    IF p_id_categoria IS NULL OR p_id_categoria <= 0 THEN
        RAISE EXCEPTION 'ID de categoría no válido';
    END IF;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre de la categoría es obligatorio';
    END IF;

    IF LENGTH(TRIM(p_nombre)) > 80 THEN
        RAISE EXCEPTION 'El nombre de la categoría no debe superar los 80 caracteres';
    END IF;

    UPDATE Categoria
    SET nombre = TRIM(p_nombre)
    WHERE id_categoria = p_id_categoria;

    GET DIAGNOSTICS v_filas_afectadas = ROW_COUNT;

    RETURN QUERY
    SELECT v_filas_afectadas;
END;
$$;

-- ============================================================
-- FUNCIÓN: Obtener métricas generales del dashboard
-- Uso: dashboard principal
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_metricas_dashboard()
RETURNS TABLE (
    total_clientes BIGINT,
    total_productos BIGINT,
    total_facturas BIGINT,
    total_ventas NUMERIC,
    ventas_hoy NUMERIC,
    stock_bajo BIGINT
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        (SELECT COUNT(*) FROM Cliente) AS total_clientes,
        (SELECT COUNT(*) FROM Producto) AS total_productos,
        (SELECT COUNT(*) FROM Factura) AS total_facturas,
        (SELECT COALESCE(SUM(total), 0) FROM Factura) AS total_ventas,
        (SELECT COALESCE(SUM(total), 0) FROM Factura WHERE DATE(fecha) = CURRENT_DATE) AS ventas_hoy,
        (SELECT COUNT(*) FROM Producto WHERE stock <= 5) AS stock_bajo;
END;
$$;


-- ============================================================
-- FUNCIÓN: Obtener ventas de los últimos 30 días
-- Uso: gráfica de ventas del dashboard
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_ventas_dashboard(
    p_dias INT DEFAULT 30
)
RETURNS TABLE (
    dia DATE,
    total_dia NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_dias IS NULL OR p_dias <= 0 THEN
        RAISE EXCEPTION 'La cantidad de días debe ser mayor que cero';
    END IF;

    RETURN QUERY
    SELECT
        DATE(f.fecha) AS dia,
        COALESCE(SUM(f.total), 0) AS total_dia
    FROM Factura f
    WHERE f.fecha >= CURRENT_DATE - (p_dias * INTERVAL '1 day')
    GROUP BY DATE(f.fecha)
    ORDER BY dia ASC;
END;
$$;


-- ============================================================
-- FUNCIÓN: Obtener productos más vendidos
-- Uso: ranking del dashboard
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_dashboard(
    p_limite INT DEFAULT 5
)
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    cantidad_vendida BIGINT
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_limite IS NULL OR p_limite <= 0 THEN
        RAISE EXCEPTION 'El límite debe ser mayor que cero';
    END IF;

    RETURN QUERY
    SELECT
        p.id_producto,
        p.nombre AS producto,
        COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida
    FROM DetalleFactura df
    INNER JOIN Producto p ON p.id_producto = df.id_producto
    GROUP BY p.id_producto, p.nombre
    ORDER BY cantidad_vendida DESC
    LIMIT p_limite;
END;
$$;


-- ============================================================
-- FUNCIÓN: Obtener últimos productos vendidos
-- Uso: tabla de últimos movimientos del dashboard
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_ultimos_productos_vendidos_dashboard(
    p_limite INT DEFAULT 6
)
RETURNS TABLE (
    id_factura INT,
    id_producto INT,
    nombre VARCHAR,
    cantidad INT,
    subtotal NUMERIC,
    fecha TIMESTAMP
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_limite IS NULL OR p_limite <= 0 THEN
        RAISE EXCEPTION 'El límite debe ser mayor que cero';
    END IF;

    RETURN QUERY
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
    LIMIT p_limite;
END;
$$;


-- ============================================================
-- FUNCIÓN: Obtener facturas recientes
-- Uso: tabla de facturas recientes del dashboard
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_facturas_recientes_dashboard(
    p_limite INT DEFAULT 5
)
RETURNS TABLE (
    id_factura INT,
    fecha TIMESTAMP,
    total NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_limite IS NULL OR p_limite <= 0 THEN
        RAISE EXCEPTION 'El límite debe ser mayor que cero';
    END IF;

    RETURN QUERY
    SELECT
        f.id_factura,
        f.fecha,
        f.total
    FROM Factura f
    ORDER BY f.fecha DESC, f.id_factura DESC
    LIMIT p_limite;
END;
$$;


-- ============================================================
-- FUNCIÓN: Obtener clientes recientes
-- Uso: tabla de clientes recientes del dashboard
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_clientes_recientes_dashboard(
    p_limite INT DEFAULT 5
)
RETURNS TABLE (
    id_cliente INT,
    nombre TEXT,
    telefono VARCHAR,
    fecha_registro DATE
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_limite IS NULL OR p_limite <= 0 THEN
        RAISE EXCEPTION 'El límite debe ser mayor que cero';
    END IF;

    RETURN QUERY
    SELECT
        c.id_cliente,
        CONCAT(c.nombres, ' ', c.apellidos) AS nombre,
        c.telefono,
        c.fecha_registro
    FROM Cliente c
    ORDER BY c.fecha_registro DESC, c.id_cliente DESC
    LIMIT p_limite;
END;
$$;

-- ============================================================
-- FUNCIÓN: Eliminar usuario del sistema
-- Uso: módulo de usuarios / eliminar trabajador
-- ============================================================

CREATE OR REPLACE FUNCTION eliminar_usuario_sistema(
    p_id_usuario INT,
    p_id_usuario_actual INT
)
RETURNS TABLE (
    filas_afectadas INT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_filas_afectadas INT;
BEGIN
    IF p_id_usuario IS NULL OR p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'Trabajador no válido';
    END IF;

    IF p_id_usuario = 1 THEN
        RAISE EXCEPTION 'No se puede eliminar la cuenta del jefe';
    END IF;

    IF p_id_usuario_actual IS NOT NULL AND p_id_usuario = p_id_usuario_actual THEN
        RAISE EXCEPTION 'No puede eliminar su propia cuenta';
    END IF;

    DELETE FROM Usuario
    WHERE id_usuario = p_id_usuario;

    GET DIAGNOSTICS v_filas_afectadas = ROW_COUNT;

    RETURN QUERY
    SELECT v_filas_afectadas;
END;
$$;

-- ============================================================
-- FUNCIÓN: Listar categorías para formulario de producto
-- Uso: nuevo_producto.php
-- ============================================================

CREATE OR REPLACE FUNCTION listar_categorias_form_producto()
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
    ORDER BY c.nombre ASC;
END;
$$;


-- ============================================================
-- FUNCIÓN: Listar proveedores para formulario de producto
-- Uso: nuevo_producto.php
-- ============================================================

CREATE OR REPLACE FUNCTION listar_proveedores_form_producto()
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
    ORDER BY p.nombre ASC;
END;
$$;


-- ============================================================
-- FUNCIÓN: Registrar nuevo producto
-- Uso: nuevo_producto.php
-- ============================================================

CREATE OR REPLACE FUNCTION registrar_producto_formulario(
    p_codigo VARCHAR,
    p_nombre VARCHAR,
    p_descripcion TEXT,
    p_imagen VARCHAR,
    p_id_categoria INT,
    p_id_proveedor INT,
    p_precio_compra NUMERIC,
    p_precio_venta NUMERIC,
    p_stock INT
)
RETURNS TABLE (
    id_producto INT
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_codigo IS NULL OR TRIM(p_codigo) = '' THEN
        RAISE EXCEPTION 'El código del producto es obligatorio';
    END IF;

    IF LENGTH(TRIM(p_codigo)) > 50 THEN
        RAISE EXCEPTION 'El código del producto no debe superar los 50 caracteres';
    END IF;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre del producto es obligatorio';
    END IF;

    IF LENGTH(TRIM(p_nombre)) > 120 THEN
        RAISE EXCEPTION 'El nombre del producto no debe superar los 120 caracteres';
    END IF;

    IF p_imagen IS NULL OR TRIM(p_imagen) = '' THEN
        RAISE EXCEPTION 'La imagen del producto es obligatoria';
    END IF;

    IF p_precio_compra IS NULL OR p_precio_compra < 0 THEN
        RAISE EXCEPTION 'El precio de compra debe ser mayor o igual a cero';
    END IF;

    IF p_precio_venta IS NULL OR p_precio_venta < 0 THEN
        RAISE EXCEPTION 'El precio de venta debe ser mayor o igual a cero';
    END IF;

    IF p_stock IS NULL OR p_stock < 0 THEN
        RAISE EXCEPTION 'El stock debe ser mayor o igual a cero';
    END IF;

    RETURN QUERY
    INSERT INTO Producto (
        codigo,
        nombre,
        descripcion,
        imagen,
        id_categoria,
        id_proveedor,
        precio_compra,
        precio_venta,
        stock
    )
    VALUES (
        TRIM(p_codigo),
        TRIM(p_nombre),
        COALESCE(NULLIF(TRIM(p_descripcion), ''), 'Sin descripción'),
        TRIM(p_imagen),
        p_id_categoria,
        p_id_proveedor,
        p_precio_compra,
        p_precio_venta,
        p_stock
    )
    RETURNING Producto.id_producto;
END;
$$;

-- ============================================================
-- FUNCIÓN: Eliminar producto del sistema
-- Uso: módulo de productos / eliminar producto
-- ============================================================

CREATE OR REPLACE FUNCTION eliminar_producto_sistema(
    p_id_producto INT
)
RETURNS TABLE (
    filas_afectadas INT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_filas_afectadas INT;
BEGIN
    IF p_id_producto IS NULL OR p_id_producto <= 0 THEN
        RAISE EXCEPTION 'Producto no válido';
    END IF;

    DELETE FROM Producto
    WHERE id_producto = p_id_producto;

    GET DIAGNOSTICS v_filas_afectadas = ROW_COUNT;

    RETURN QUERY
    SELECT v_filas_afectadas;
END;
$$;