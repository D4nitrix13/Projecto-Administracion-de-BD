-- Migración: Agregar paginación (p_limit, p_offset) a funciones de listado
-- Fecha: 2026-06-18

-- =============================================
-- 1. buscar_facturas_filtradas
-- =============================================
CREATE OR REPLACE FUNCTION public.buscar_facturas_filtradas(
    p_id_rol integer DEFAULT NULL::integer,
    p_busqueda text DEFAULT NULL::text,
    p_id_seccion integer DEFAULT NULL::integer,
    p_id_usuario integer DEFAULT NULL::integer,
    p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_estado_pago text DEFAULT NULL::text,
    p_estado_produccion text DEFAULT NULL::text,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_factura integer, fecha timestamp without time zone, total numeric, cliente text, usuario character varying, seccion character varying, estado_pago character varying, estado_produccion character varying)
LANGUAGE plpgsql
AS $function$
BEGIN
    IF p_id_rol IS NOT NULL AND p_id_rol < 0 THEN
        RAISE EXCEPTION 'ID de rol no válido';
    END IF;

    IF p_id_seccion IS NOT NULL AND p_id_seccion <= 0 THEN
        RAISE EXCEPTION 'ID de sección no válido';
    END IF;

    IF p_id_usuario IS NOT NULL AND p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    RETURN QUERY
    SELECT
        f.id_factura,
        f.fecha,
        f.total,
        c.nombres || ' ' || c.apellidos AS cliente,
        u.nombre AS usuario,
        s.nombre AS seccion,
        f.estado_pago,
        f.estado_produccion
    FROM Factura f
    JOIN Cliente c ON f.id_cliente = c.id_cliente
    JOIN Usuario u ON f.id_usuario = u.id_usuario
    JOIN Seccion s ON f.id_seccion = s.id_seccion
    WHERE (
            p_id_rol IS NULL
            OR p_id_rol NOT IN (2, 3)
            OR (
                c.tipo_cliente = 'Detallista'
                AND s.nombre = 'Kitsune'
            )
        )
      AND (
            p_busqueda IS NULL
            OR TRIM(p_busqueda) = ''
            OR CAST(f.id_factura AS TEXT) ILIKE '%' || TRIM(p_busqueda) || '%'
            OR c.nombres ILIKE '%' || TRIM(p_busqueda) || '%'
            OR c.apellidos ILIKE '%' || TRIM(p_busqueda) || '%'
            OR c.nombres || ' ' || c.apellidos ILIKE '%' || TRIM(p_busqueda) || '%'
            OR u.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
            OR s.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
        )
      AND (
            p_id_seccion IS NULL
            OR f.id_seccion = p_id_seccion
        )
      AND (
            p_id_usuario IS NULL
            OR f.id_usuario = p_id_usuario
        )
      AND (
            p_fecha_desde IS NULL
            OR f.fecha >= p_fecha_desde
        )
      AND (
            p_fecha_hasta IS NULL
            OR f.fecha <= p_fecha_hasta
        )
      AND (
            p_estado_pago IS NULL
            OR TRIM(p_estado_pago) = ''
            OR f.estado_pago = p_estado_pago
        )
      AND (
            p_estado_produccion IS NULL
            OR TRIM(p_estado_produccion) = ''
            OR f.estado_produccion = p_estado_produccion
        )
    ORDER BY f.fecha DESC, f.id_factura DESC
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 2. buscar_clientes_filtrados
-- =============================================
CREATE OR REPLACE FUNCTION public.buscar_clientes_filtrados(
    p_busqueda text DEFAULT NULL::text,
    p_tipo_cliente character varying DEFAULT NULL::character varying,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_cliente integer, nombres character varying, apellidos character varying, telefono character varying, direccion character varying, identificacion character varying, tipo_cliente character varying)
LANGUAGE plpgsql
AS $function$
DECLARE
    v_busqueda TEXT;
    v_es_numerica BOOLEAN;
    v_palabras TEXT[];
BEGIN
    v_busqueda := REGEXP_REPLACE(TRIM(COALESCE(p_busqueda, '')), '\s+', ' ', 'g');
    v_es_numerica := v_busqueda ~ '^[0-9]+$';

    IF p_tipo_cliente IS NOT NULL
       AND TRIM(p_tipo_cliente) <> ''
       AND p_tipo_cliente NOT IN ('Mayorista', 'Detallista') THEN
        RAISE EXCEPTION 'Tipo de cliente no válido';
    END IF;

    IF v_busqueda <> '' AND NOT v_es_numerica THEN
        v_palabras := REGEXP_SPLIT_TO_ARRAY(v_busqueda, '\s+');
    END IF;

    RETURN QUERY
    SELECT
        c.id_cliente,
        c.nombres,
        c.apellidos,
        c.telefono,
        c.direccion,
        c.identificacion,
        c.tipo_cliente
    FROM Cliente c
    WHERE (
            v_busqueda = ''
            OR (
                v_es_numerica
                AND (
                    c.id_cliente = v_busqueda::INT
                    OR c.telefono ILIKE '%' || v_busqueda || '%'
                    OR c.identificacion ILIKE '%' || v_busqueda || '%'
                )
            )
            OR (
                NOT v_es_numerica
                AND (
                    SELECT BOOL_AND(
                        c.nombres ILIKE '%' || palabra || '%'
                        OR c.apellidos ILIKE '%' || palabra || '%'
                        OR c.telefono ILIKE '%' || palabra || '%'
                        OR c.direccion ILIKE '%' || palabra || '%'
                        OR c.identificacion ILIKE '%' || palabra || '%'
                        OR c.tipo_cliente ILIKE '%' || palabra || '%'
                        OR CONCAT_WS(' ', c.nombres, c.apellidos) ILIKE '%' || palabra || '%'
                        OR CONCAT_WS(' ', c.apellidos, c.nombres) ILIKE '%' || palabra || '%'
                        OR CONCAT_WS(
                            ' ',
                            c.nombres,
                            c.apellidos,
                            c.telefono,
                            c.direccion,
                            c.identificacion,
                            c.tipo_cliente
                        ) ILIKE '%' || palabra || '%'
                    )
                    FROM UNNEST(v_palabras) AS palabra
                )
            )
        )
      AND (
            p_tipo_cliente IS NULL
            OR TRIM(p_tipo_cliente) = ''
            OR c.tipo_cliente = p_tipo_cliente
        )
    ORDER BY c.id_cliente DESC
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 3. buscar_compras_filtradas
-- =============================================
CREATE OR REPLACE FUNCTION public.buscar_compras_filtradas(
    p_busqueda text DEFAULT NULL::text,
    p_id_proveedor integer DEFAULT NULL::integer,
    p_id_usuario integer DEFAULT NULL::integer,
    p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_compra integer, fecha timestamp without time zone, total numeric, proveedor character varying, usuario character varying)
LANGUAGE plpgsql
AS $function$
BEGIN
    IF p_id_proveedor IS NOT NULL AND p_id_proveedor <= 0 THEN
        RAISE EXCEPTION 'ID de proveedor no válido';
    END IF;

    IF p_id_usuario IS NOT NULL AND p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    RETURN QUERY
    SELECT
        c.id_compra,
        c.fecha,
        c.total,
        p.nombre AS proveedor,
        u.nombre AS usuario
    FROM Compra c
    INNER JOIN Proveedor p ON c.id_proveedor = p.id_proveedor
    INNER JOIN Usuario u ON c.id_usuario = u.id_usuario
    WHERE (
            p_busqueda IS NULL
            OR TRIM(p_busqueda) = ''
            OR p.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
            OR u.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
            OR CAST(c.id_compra AS TEXT) ILIKE '%' || TRIM(p_busqueda) || '%'
        )
      AND (
            p_id_proveedor IS NULL
            OR c.id_proveedor = p_id_proveedor
        )
      AND (
            p_id_usuario IS NULL
            OR c.id_usuario = p_id_usuario
        )
      AND (
            p_fecha_desde IS NULL
            OR c.fecha >= p_fecha_desde
        )
      AND (
            p_fecha_hasta IS NULL
            OR c.fecha <= p_fecha_hasta
        )
    ORDER BY c.fecha DESC
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 4. buscar_productos_inventario
-- =============================================
CREATE OR REPLACE FUNCTION public.buscar_productos_inventario(
    p_busqueda text DEFAULT ''::text,
    p_id_categoria integer DEFAULT NULL::integer,
    p_id_proveedor integer DEFAULT NULL::integer,
    p_id_producto integer DEFAULT NULL::integer,
    p_stock_bajo boolean DEFAULT false,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, descripcion text, imagen character varying, categoria character varying, proveedor character varying, precio_compra numeric, precio_venta numeric, stock integer)
LANGUAGE plpgsql
AS $function$
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
    ORDER BY prod.nombre ASC
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 5. buscar_proveedores_filtrados
-- =============================================
CREATE OR REPLACE FUNCTION public.buscar_proveedores_filtrados(
    p_busqueda text DEFAULT NULL::text,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_proveedor integer, nombre character varying, telefono character varying, email character varying, direccion character varying)
LANGUAGE plpgsql
AS $function$
BEGIN
    RETURN QUERY
    SELECT
        p.id_proveedor,
        p.nombre,
        p.telefono,
        p.email,
        p.direccion
    FROM Proveedor p
    WHERE (
            p_busqueda IS NULL
            OR TRIM(p_busqueda) = ''
            OR CAST(p.id_proveedor AS TEXT) ILIKE '%' || TRIM(p_busqueda) || '%'
            OR p.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
            OR p.telefono ILIKE '%' || TRIM(p_busqueda) || '%'
            OR p.email ILIKE '%' || TRIM(p_busqueda) || '%'
            OR p.direccion ILIKE '%' || TRIM(p_busqueda) || '%'
        )
    ORDER BY p.nombre ASC
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 6. buscar_usuarios_filtrados
-- =============================================
CREATE OR REPLACE FUNCTION public.buscar_usuarios_filtrados(
    p_busqueda text DEFAULT NULL::text,
    p_id_rol integer DEFAULT NULL::integer,
    p_seccion_filtro text DEFAULT NULL::text,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_usuario integer, nombre character varying, email character varying, id_rol integer, id_seccion integer, rol character varying, seccion character varying)
LANGUAGE plpgsql
AS $function$
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
    ORDER BY u.nombre ASC
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 7. buscar_categorias
-- =============================================
CREATE OR REPLACE FUNCTION public.buscar_categorias(
    p_busqueda text DEFAULT ''::text,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_categoria integer, nombre character varying)
LANGUAGE plpgsql
AS $function$
BEGIN
    RETURN QUERY
    SELECT
        c.id_categoria,
        c.nombre
    FROM Categoria c
    WHERE
        COALESCE(TRIM(p_busqueda), '') = ''
        OR c.nombre ILIKE '%' || TRIM(p_busqueda) || '%'
    ORDER BY c.nombre ASC
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 8. obtener_ventas_detalladas_reportes (quitar LIMIT 50 hardcoded)
-- =============================================
CREATE OR REPLACE FUNCTION public.obtener_ventas_detalladas_reportes(
    p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_factura integer, fecha timestamp without time zone, subtotal numeric, descuento numeric, impuesto numeric, total numeric, cliente text, usuario character varying, seccion character varying)
LANGUAGE plpgsql
AS $function$
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
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 9. obtener_productos_reporte (quitar LIMIT 50 hardcoded)
-- =============================================
CREATE OR REPLACE FUNCTION public.obtener_productos_reporte(
    p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, stock integer, cantidad_vendida bigint, total_vendido numeric)
LANGUAGE plpgsql
AS $function$
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
    LIMIT p_limit OFFSET p_offset;
END;
$function$;

-- =============================================
-- 10. obtener_clientes_reporte (quitar LIMIT 50 hardcoded)
-- =============================================
CREATE OR REPLACE FUNCTION public.obtener_clientes_reporte(
    p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone,
    p_limit integer DEFAULT 15,
    p_offset integer DEFAULT 0
)
RETURNS TABLE(id_cliente integer, cliente text, telefono character varying, tipo_cliente character varying, cantidad_facturas bigint, total_comprado numeric)
LANGUAGE plpgsql
AS $function$
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
    LIMIT p_limit OFFSET p_offset;
END;
$function$;
