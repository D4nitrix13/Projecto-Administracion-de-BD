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

-- ============================================================
-- AUTENTICACIÓN: Obtener usuario por email
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_usuario_login(
    p_email VARCHAR
)
RETURNS TABLE (
    id_usuario INT,
    nombre VARCHAR,
    email VARCHAR,
    password TEXT,
    id_rol INT,
    id_seccion INT,
    rol VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_email IS NULL OR TRIM(p_email) = '' THEN
        RAISE EXCEPTION 'El correo electrónico no puede estar vacío';
    END IF;

    RETURN QUERY
    SELECT
        u.id_usuario,
        u.nombre,
        u.email,
        u.password,
        u.id_rol,
        u.id_seccion,
        r.nombre AS rol
    FROM Usuario AS u
    INNER JOIN Rol AS r ON r.id_rol = u.id_rol
    WHERE u.email = TRIM(p_email)
    LIMIT 1;
END;
$$;


-- ============================================================
-- AUTENTICACIÓN: Actualizar hash de contraseña
-- ============================================================

CREATE OR REPLACE FUNCTION actualizar_password_usuario_login(
    p_id_usuario INT,
    p_password_hash TEXT
)
RETURNS BOOLEAN
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_usuario IS NULL OR p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    IF p_password_hash IS NULL OR TRIM(p_password_hash) = '' THEN
        RAISE EXCEPTION 'El hash de contraseña no puede estar vacío';
    END IF;

    UPDATE Usuario
    SET password = p_password_hash
    WHERE Usuario.id_usuario = p_id_usuario;

    RETURN FOUND;
END;
$$;

-- ============================================================
-- COMPRAS: Obtener datos generales de una compra
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_compra_por_id(
    p_id_compra INT
)
RETURNS TABLE (
    id_compra INT,
    fecha TIMESTAMP,
    total NUMERIC,
    proveedor VARCHAR,
    proveedor_telefono VARCHAR,
    proveedor_email VARCHAR,
    usuario VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_compra IS NULL OR p_id_compra <= 0 THEN
        RAISE EXCEPTION 'ID de compra no válido';
    END IF;

    RETURN QUERY
    SELECT
        c.id_compra,
        c.fecha,
        c.total,
        p.nombre AS proveedor,
        p.telefono AS proveedor_telefono,
        p.email AS proveedor_email,
        u.nombre AS usuario
    FROM Compra c
    INNER JOIN Proveedor p ON c.id_proveedor = p.id_proveedor
    INNER JOIN Usuario u ON c.id_usuario = u.id_usuario
    WHERE c.id_compra = p_id_compra;
END;
$$;


-- ============================================================
-- COMPRAS: Obtener detalles de productos de una compra
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_detalles_compra(
    p_id_compra INT
)
RETURNS TABLE (
    id_detalle INT,
    cantidad INT,
    costo_unitario NUMERIC,
    total_linea NUMERIC,
    producto_codigo VARCHAR,
    producto_nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_compra IS NULL OR p_id_compra <= 0 THEN
        RAISE EXCEPTION 'ID de compra no válido';
    END IF;

    RETURN QUERY
    SELECT
        dc.id_detalle,
        dc.cantidad,
        dc.costo_unitario,
        dc.total_linea,
        pr.codigo AS producto_codigo,
        pr.nombre AS producto_nombre
    FROM DetalleCompra dc
    INNER JOIN Producto pr ON dc.id_producto = pr.id_producto
    WHERE dc.id_compra = p_id_compra
    ORDER BY dc.id_detalle;
END;
$$;

-- ============================================================
-- FACTURAS: Eliminar factura y devolver stock
-- ============================================================

CREATE OR REPLACE FUNCTION eliminar_factura_sistema(
    p_id_factura INT
)
RETURNS BOOLEAN
LANGUAGE plpgsql
AS $$
DECLARE
    v_existe_factura BOOLEAN;
BEGIN
    IF p_id_factura IS NULL OR p_id_factura <= 0 THEN
        RAISE EXCEPTION 'ID de factura no válido';
    END IF;

    SELECT EXISTS (
        SELECT 1
        FROM Factura
        WHERE id_factura = p_id_factura
    )
    INTO v_existe_factura;

    IF NOT v_existe_factura THEN
        RAISE EXCEPTION 'La factura especificada no existe';
    END IF;

    -- 1) Devolver stock de los productos vendidos en la factura
    UPDATE Producto p
    SET stock = p.stock + df.cantidad
    FROM DetalleFactura df
    WHERE df.id_producto = p.id_producto
      AND df.id_factura = p_id_factura;

    -- 2) Eliminar detalles de la factura
    DELETE FROM DetalleFactura
    WHERE id_factura = p_id_factura;

    -- 3) Eliminar factura principal
    DELETE FROM Factura
    WHERE id_factura = p_id_factura;

    RETURN TRUE;
END;
$$;

-- ============================================================
-- CUENTA: Obtener datos de la cuenta actual
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_usuario_configurar_cuenta(
    p_id_usuario INT
)
RETURNS TABLE (
    id_usuario INT,
    nombre VARCHAR,
    email VARCHAR,
    password TEXT,
    id_rol INT,
    id_seccion INT,
    rol_nombre VARCHAR,
    seccion_nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_usuario IS NULL OR p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    RETURN QUERY
    SELECT
        u.id_usuario,
        u.nombre,
        u.email,
        u.password,
        u.id_rol,
        u.id_seccion,
        r.nombre AS rol_nombre,
        s.nombre AS seccion_nombre
    FROM Usuario u
    INNER JOIN Rol r ON r.id_rol = u.id_rol
    LEFT JOIN Seccion s ON s.id_seccion = u.id_seccion
    WHERE u.id_usuario = p_id_usuario;
END;
$$;


-- ============================================================
-- CUENTA: Actualizar datos de la cuenta actual
-- ============================================================

CREATE OR REPLACE FUNCTION actualizar_usuario_configurar_cuenta(
    p_id_usuario INT,
    p_nombre VARCHAR,
    p_email VARCHAR,
    p_password TEXT
)
RETURNS BOOLEAN
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_usuario IS NULL OR p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre no puede estar vacío';
    END IF;

    IF p_email IS NULL OR TRIM(p_email) = '' THEN
        RAISE EXCEPTION 'El correo electrónico no puede estar vacío';
    END IF;

    IF p_password IS NULL OR TRIM(p_password) = '' THEN
        RAISE EXCEPTION 'La contraseña no puede estar vacía';
    END IF;

    UPDATE Usuario
    SET
        nombre = TRIM(p_nombre),
        email = TRIM(p_email),
        password = p_password
    WHERE Usuario.id_usuario = p_id_usuario;

    RETURN FOUND;
END;
$$;

-- ============================================================
-- REPORTES: Total de clientes
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_total_clientes_reportes()
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_total INT;
BEGIN
    SELECT COUNT(*)
    INTO v_total
    FROM Cliente;

    RETURN v_total;
END;
$$;


-- ============================================================
-- REPORTES: Total de facturas por rango de fechas
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_total_facturas_reportes(
    p_fecha_desde TIMESTAMP DEFAULT NULL,
    p_fecha_hasta TIMESTAMP DEFAULT NULL
)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_total INT;
BEGIN
    SELECT COUNT(*)
    INTO v_total
    FROM Factura f
    WHERE (p_fecha_desde IS NULL OR f.fecha >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR f.fecha <= p_fecha_hasta);

    RETURN v_total;
END;
$$;


-- ============================================================
-- REPORTES: Total de ventas por rango de fechas
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_total_ventas_reportes(
    p_fecha_desde TIMESTAMP DEFAULT NULL,
    p_fecha_hasta TIMESTAMP DEFAULT NULL
)
RETURNS NUMERIC
LANGUAGE plpgsql
AS $$
DECLARE
    v_total NUMERIC;
BEGIN
    SELECT COALESCE(SUM(f.total), 0)
    INTO v_total
    FROM Factura f
    WHERE (p_fecha_desde IS NULL OR f.fecha >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR f.fecha <= p_fecha_hasta);

    RETURN v_total;
END;
$$;


-- ============================================================
-- REPORTES: Productos con stock bajo
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_stock_bajo_reportes()
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_total INT;
BEGIN
    SELECT COUNT(*)
    INTO v_total
    FROM Producto
    WHERE stock <= 5;

    RETURN v_total;
END;
$$;


-- ============================================================
-- REPORTES: Ventas por día
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_ventas_por_dia_reportes(
    p_fecha_desde TIMESTAMP DEFAULT NULL,
    p_fecha_hasta TIMESTAMP DEFAULT NULL
)
RETURNS TABLE (
    dia TEXT,
    total_dia NUMERIC,
    cantidad_facturas BIGINT
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        TO_CHAR(f.fecha::date, 'YYYY-MM-DD') AS dia,
        COALESCE(SUM(f.total), 0) AS total_dia,
        COUNT(*) AS cantidad_facturas
    FROM Factura f
    WHERE (p_fecha_desde IS NULL OR f.fecha >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR f.fecha <= p_fecha_hasta)
    GROUP BY f.fecha::date
    ORDER BY f.fecha::date ASC
    LIMIT 30;
END;
$$;


-- ============================================================
-- REPORTES: Productos más vendidos
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_productos_mas_vendidos_reportes(
    p_fecha_desde TIMESTAMP DEFAULT NULL,
    p_fecha_hasta TIMESTAMP DEFAULT NULL
)
RETURNS TABLE (
    id_producto INT,
    producto VARCHAR,
    codigo VARCHAR,
    cantidad_vendida BIGINT,
    total_vendido NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        p.id_producto,
        p.nombre AS producto,
        p.codigo,
        COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
        COALESCE(SUM(df.total_linea), 0) AS total_vendido
    FROM DetalleFactura df
    INNER JOIN Factura f ON f.id_factura = df.id_factura
    INNER JOIN Producto p ON p.id_producto = df.id_producto
    WHERE (p_fecha_desde IS NULL OR f.fecha >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR f.fecha <= p_fecha_hasta)
    GROUP BY p.id_producto, p.nombre, p.codigo
    ORDER BY cantidad_vendida DESC, total_vendido DESC
    LIMIT 10;
END;
$$;

-- ============================================================
-- REPORTES: Ventas detalladas
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_ventas_detalladas_reportes(
    p_fecha_desde TIMESTAMP DEFAULT NULL,
    p_fecha_hasta TIMESTAMP DEFAULT NULL
)
RETURNS TABLE (
    id_factura INT,
    fecha TIMESTAMP,
    subtotal NUMERIC,
    descuento NUMERIC,
    impuesto NUMERIC,
    total NUMERIC,
    cliente TEXT,
    usuario VARCHAR,
    seccion VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
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
    WHERE (p_fecha_desde IS NULL OR f.fecha >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR f.fecha <= p_fecha_hasta)
    ORDER BY f.fecha DESC, f.id_factura DESC
    LIMIT 50;
END;
$$;


-- ============================================================
-- REPORTES: Reporte de productos
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_productos_reporte(
    p_fecha_desde TIMESTAMP DEFAULT NULL,
    p_fecha_hasta TIMESTAMP DEFAULT NULL
)
RETURNS TABLE (
    id_producto INT,
    codigo VARCHAR,
    nombre VARCHAR,
    stock INT,
    cantidad_vendida BIGINT,
    total_vendido NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.stock,
        COALESCE(SUM(df.cantidad), 0)::BIGINT AS cantidad_vendida,
        COALESCE(SUM(df.total_linea), 0) AS total_vendido
    FROM Producto p
    LEFT JOIN DetalleFactura df ON df.id_producto = p.id_producto
    LEFT JOIN Factura f ON f.id_factura = df.id_factura
    WHERE (p_fecha_desde IS NULL OR f.fecha IS NULL OR f.fecha >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR f.fecha IS NULL OR f.fecha <= p_fecha_hasta)
    GROUP BY p.id_producto, p.codigo, p.nombre, p.stock
    ORDER BY cantidad_vendida DESC, total_vendido DESC, p.nombre ASC
    LIMIT 50;
END;
$$;


-- ============================================================
-- REPORTES: Reporte de clientes
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_clientes_reporte(
    p_fecha_desde TIMESTAMP DEFAULT NULL,
    p_fecha_hasta TIMESTAMP DEFAULT NULL
)
RETURNS TABLE (
    id_cliente INT,
    cliente TEXT,
    telefono VARCHAR,
    tipo_cliente VARCHAR,
    cantidad_facturas BIGINT,
    total_comprado NUMERIC
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        c.id_cliente,
        c.nombres || ' ' || c.apellidos AS cliente,
        c.telefono,
        c.tipo_cliente,
        COUNT(f.id_factura) AS cantidad_facturas,
        COALESCE(SUM(f.total), 0) AS total_comprado
    FROM Cliente c
    LEFT JOIN Factura f ON f.id_cliente = c.id_cliente
    WHERE (p_fecha_desde IS NULL OR f.fecha IS NULL OR f.fecha >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR f.fecha IS NULL OR f.fecha <= p_fecha_hasta)
    GROUP BY c.id_cliente, c.nombres, c.apellidos, c.telefono, c.tipo_cliente
    ORDER BY total_comprado DESC, cantidad_facturas DESC, cliente ASC
    LIMIT 50;
END;
$$;

-- ============================================================
-- CATÁLOGO: Listar categorías del catálogo público
-- ============================================================

CREATE OR REPLACE FUNCTION listar_categorias_catalogo()
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
-- CATÁLOGO: Buscar productos del catálogo público
-- ============================================================

CREATE OR REPLACE FUNCTION buscar_productos_catalogo(
    p_busqueda TEXT DEFAULT NULL,
    p_id_categoria INT DEFAULT NULL,
    p_disponibilidad VARCHAR DEFAULT NULL
)
RETURNS TABLE (
    id_producto INT,
    codigo VARCHAR,
    nombre VARCHAR,
    descripcion TEXT,
    imagen VARCHAR,
    categoria VARCHAR,
    precio_venta NUMERIC,
    stock INT
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_categoria IS NOT NULL AND p_id_categoria <= 0 THEN
        RAISE EXCEPTION 'ID de categoría no válido';
    END IF;

    IF p_disponibilidad IS NOT NULL
       AND p_disponibilidad <> ''
       AND p_disponibilidad NOT IN ('disponible', 'stock_bajo', 'agotado') THEN
        RAISE EXCEPTION 'Filtro de disponibilidad no válido';
    END IF;

    RETURN QUERY
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.imagen,
        c.nombre AS categoria,
        p.precio_venta,
        p.stock
    FROM Producto p
    LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
    WHERE (
            p_busqueda IS NULL
            OR TRIM(p_busqueda) = ''
            OR p.codigo ILIKE '%' || TRIM(p_busqueda) || '%'
            OR p.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
            OR p.descripcion ILIKE '%' || TRIM(p_busqueda) || '%'
            OR c.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
        )
      AND (
            p_id_categoria IS NULL
            OR p.id_categoria = p_id_categoria
        )
      AND (
            p_disponibilidad IS NULL
            OR p_disponibilidad = ''
            OR (
                p_disponibilidad = 'disponible'
                AND p.stock > 5
            )
            OR (
                p_disponibilidad = 'stock_bajo'
                AND p.stock > 0
                AND p.stock <= 5
            )
            OR (
                p_disponibilidad = 'agotado'
                AND p.stock <= 0
            )
        )
    ORDER BY
        CASE
            WHEN p.stock <= 0 THEN 2
            WHEN p.stock <= 5 THEN 1
            ELSE 0
        END,
        p.nombre ASC;
END;
$$;

-- ============================================================
-- ROLES: Listar roles ordenados
-- ============================================================

CREATE OR REPLACE FUNCTION listar_roles_ordenados()
RETURNS TABLE (
    id_rol INT,
    nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        r.id_rol,
        r.nombre
    FROM Rol r
    ORDER BY r.nombre;
END;
$$;

-- ============================================================
-- CATEGORÍAS: Listar categorías ordenadas
-- ============================================================

CREATE OR REPLACE FUNCTION listar_categorias_ordenadas()
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
-- PRODUCTOS: Obtener producto por ID para edición
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_producto_edicion_por_id(
    p_id_producto INT
)
RETURNS TABLE (
    id_producto INT,
    codigo VARCHAR,
    nombre VARCHAR,
    descripcion TEXT,
    imagen VARCHAR,
    id_categoria INT,
    id_proveedor INT,
    precio_compra NUMERIC,
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
        p.id_categoria,
        p.id_proveedor,
        p.precio_compra,
        p.precio_venta,
        p.stock
    FROM Producto p
    WHERE p.id_producto = p_id_producto;
END;
$$;


-- ============================================================
-- PRODUCTOS: Actualizar producto desde formulario de edición
-- ============================================================

CREATE OR REPLACE FUNCTION actualizar_producto_edicion(
    p_id_producto INT,
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
RETURNS BOOLEAN
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_producto IS NULL OR p_id_producto <= 0 THEN
        RAISE EXCEPTION 'ID de producto no válido';
    END IF;

    IF p_codigo IS NULL OR TRIM(p_codigo) = '' THEN
        RAISE EXCEPTION 'El código del producto no puede estar vacío';
    END IF;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre del producto no puede estar vacío';
    END IF;

    IF p_precio_compra IS NULL OR p_precio_compra < 0 THEN
        RAISE EXCEPTION 'El precio de compra no puede ser negativo';
    END IF;

    IF p_precio_venta IS NULL OR p_precio_venta < 0 THEN
        RAISE EXCEPTION 'El precio de venta no puede ser negativo';
    END IF;

    IF p_stock IS NULL OR p_stock < 0 THEN
        RAISE EXCEPTION 'El stock no puede ser negativo';
    END IF;

    UPDATE Producto
    SET
        codigo = TRIM(p_codigo),
        nombre = TRIM(p_nombre),
        descripcion = COALESCE(NULLIF(TRIM(p_descripcion), ''), 'Sin descripción'),
        imagen = p_imagen,
        id_categoria = p_id_categoria,
        id_proveedor = p_id_proveedor,
        precio_compra = p_precio_compra,
        precio_venta = p_precio_venta,
        stock = p_stock
    WHERE Producto.id_producto = p_id_producto;

    RETURN FOUND;
END;
$$;


-- ============================================================
-- PRODUCTOS: Listar productos para facturación
-- ============================================================

CREATE OR REPLACE FUNCTION listar_productos_para_factura()
RETURNS TABLE (
    id_producto INT,
    codigo VARCHAR,
    nombre VARCHAR,
    precio_venta NUMERIC,
    stock INT
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.precio_venta,
        p.stock
    FROM Producto p
    ORDER BY p.nombre;
END;
$$;

-- ============================================================
-- USUARIOS: Crear usuario
-- ============================================================

CREATE OR REPLACE FUNCTION crear_usuario_sistema(
    p_nombre VARCHAR,
    p_email VARCHAR,
    p_password TEXT,
    p_id_rol INT,
    p_id_seccion INT DEFAULT NULL
)
RETURNS BOOLEAN
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre del usuario no puede estar vacío';
    END IF;

    IF p_email IS NULL OR TRIM(p_email) = '' THEN
        RAISE EXCEPTION 'El correo electrónico no puede estar vacío';
    END IF;

    IF p_password IS NULL OR TRIM(p_password) = '' THEN
        RAISE EXCEPTION 'La contraseña no puede estar vacía';
    END IF;

    IF p_id_rol IS NULL OR p_id_rol <= 0 THEN
        RAISE EXCEPTION 'El rol del usuario no es válido';
    END IF;

    INSERT INTO Usuario (
        nombre,
        email,
        password,
        id_rol,
        id_seccion
    )
    VALUES (
        TRIM(p_nombre),
        TRIM(p_email),
        p_password,
        p_id_rol,
        p_id_seccion
    );

    RETURN TRUE;
END;
$$;


-- ============================================================
-- USUARIOS: Buscar usuarios con filtros
-- ============================================================

CREATE OR REPLACE FUNCTION buscar_usuarios_filtrados(
    p_busqueda TEXT DEFAULT NULL,
    p_id_rol INT DEFAULT NULL,
    p_seccion_filtro TEXT DEFAULT NULL
)
RETURNS TABLE (
    id_usuario INT,
    nombre VARCHAR,
    email VARCHAR,
    id_rol INT,
    id_seccion INT,
    rol VARCHAR,
    seccion VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_rol IS NOT NULL AND p_id_rol <= 0 THEN
        RAISE EXCEPTION 'ID de rol no válido';
    END IF;

    RETURN QUERY
    SELECT
        u.id_usuario,
        u.nombre,
        u.email,
        u.id_rol,
        u.id_seccion,
        r.nombre AS rol,
        s.nombre AS seccion
    FROM Usuario u
    INNER JOIN Rol r ON u.id_rol = r.id_rol
    LEFT JOIN Seccion s ON u.id_seccion = s.id_seccion
    WHERE u.id_usuario <> 1
      AND (
            p_busqueda IS NULL
            OR TRIM(p_busqueda) = ''
            OR u.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
            OR u.email ILIKE '%' || TRIM(p_busqueda) || '%'
            OR r.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
            OR COALESCE(s.nombre, 'Todas las secciones') ILIKE '%' || TRIM(p_busqueda) || '%'
        )
      AND (
            p_id_rol IS NULL
            OR u.id_rol = p_id_rol
        )
      AND (
            p_seccion_filtro IS NULL
            OR TRIM(p_seccion_filtro) = ''
            OR (
                p_seccion_filtro = 'none'
                AND u.id_seccion IS NULL
            )
            OR (
                p_seccion_filtro ~ '^[0-9]+$'
                AND u.id_seccion = p_seccion_filtro::INT
            )
        )
    ORDER BY u.nombre ASC;
END;
$$;


-- ============================================================
-- USUARIOS: Listar usuarios ordenados
-- ============================================================

CREATE OR REPLACE FUNCTION listar_usuarios_ordenados()
RETURNS TABLE (
    id_usuario INT,
    nombre VARCHAR
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        u.id_usuario,
        u.nombre
    FROM Usuario u
    ORDER BY u.nombre;
END;
$$;


-- ============================================================
-- USUARIOS: Obtener usuario por ID para edición
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_usuario_edicion_por_id(
    p_id_usuario INT
)
RETURNS TABLE (
    id_usuario INT,
    nombre VARCHAR,
    email VARCHAR,
    id_rol INT,
    id_seccion INT
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_usuario IS NULL OR p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    RETURN QUERY
    SELECT
        u.id_usuario,
        u.nombre,
        u.email,
        u.id_rol,
        u.id_seccion
    FROM Usuario u
    WHERE u.id_usuario = p_id_usuario;
END;
$$;


-- ============================================================
-- USUARIOS: Actualizar usuario
-- ============================================================

CREATE OR REPLACE FUNCTION actualizar_usuario_sistema(
    p_id_usuario INT,
    p_nombre VARCHAR,
    p_email VARCHAR,
    p_id_rol INT,
    p_id_seccion INT DEFAULT NULL,
    p_password TEXT DEFAULT NULL
)
RETURNS BOOLEAN
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_id_usuario IS NULL OR p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre del usuario no puede estar vacío';
    END IF;

    IF p_email IS NULL OR TRIM(p_email) = '' THEN
        RAISE EXCEPTION 'El correo electrónico no puede estar vacío';
    END IF;

    IF p_id_rol IS NULL OR p_id_rol <= 0 THEN
        RAISE EXCEPTION 'El rol del usuario no es válido';
    END IF;

    UPDATE Usuario
    SET
        nombre = TRIM(p_nombre),
        email = TRIM(p_email),
        id_rol = p_id_rol,
        id_seccion = p_id_seccion,
        password = CASE
            WHEN p_password IS NULL OR TRIM(p_password) = ''
                THEN Usuario.password
            ELSE p_password
        END
    WHERE Usuario.id_usuario = p_id_usuario;

    RETURN FOUND;
END;
$$;