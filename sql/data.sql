--
-- PostgreSQL database dump
--

\restrict axiMUggbOKzce8AyZv953D8NfZ77f7m8iiLlPGu3mJzpRthKudvmliRAg0pggI1

-- Dumped from database version 18.3 (Debian 18.3-1.pgdg13+1)
-- Dumped by pg_dump version 18.3 (Debian 18.3-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: actualizar_categoria(integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.actualizar_categoria(p_id_categoria integer, p_nombre character varying) RETURNS TABLE(filas_afectadas integer)
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


ALTER FUNCTION public.actualizar_categoria(p_id_categoria integer, p_nombre character varying) OWNER TO postgres;

--
-- Name: actualizar_cliente_sistema(integer, character varying, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.actualizar_cliente_sistema(p_id_cliente integer, p_nombres character varying, p_apellidos character varying, p_telefono character varying DEFAULT NULL::character varying, p_direccion character varying DEFAULT NULL::character varying, p_identificacion character varying DEFAULT NULL::character varying, p_tipo_cliente character varying DEFAULT 'Detallista'::character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_cliente IS NULL OR p_id_cliente <= 0 THEN
        RAISE EXCEPTION 'ID de cliente no válido';
    END IF;

    IF p_nombres IS NULL OR TRIM(p_nombres) = '' THEN
        RAISE EXCEPTION 'Los nombres del cliente no pueden estar vacíos';
    END IF;

    IF p_apellidos IS NULL OR TRIM(p_apellidos) = '' THEN
        RAISE EXCEPTION 'Los apellidos del cliente no pueden estar vacíos';
    END IF;

    IF p_tipo_cliente IS NULL
       OR p_tipo_cliente NOT IN ('Mayorista', 'Detallista') THEN
        RAISE EXCEPTION 'Tipo de cliente no válido';
    END IF;

    UPDATE Cliente
    SET
        nombres = TRIM(p_nombres),
        apellidos = TRIM(p_apellidos),
        telefono = NULLIF(TRIM(p_telefono), ''),
        direccion = NULLIF(TRIM(p_direccion), ''),
        identificacion = NULLIF(TRIM(p_identificacion), ''),
        tipo_cliente = p_tipo_cliente
    WHERE Cliente.id_cliente = p_id_cliente;

    RETURN FOUND;
END;
$$;


ALTER FUNCTION public.actualizar_cliente_sistema(p_id_cliente integer, p_nombres character varying, p_apellidos character varying, p_telefono character varying, p_direccion character varying, p_identificacion character varying, p_tipo_cliente character varying) OWNER TO postgres;

--
-- Name: actualizar_password_usuario_login(integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.actualizar_password_usuario_login(p_id_usuario integer, p_password_hash text) RETURNS boolean
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


ALTER FUNCTION public.actualizar_password_usuario_login(p_id_usuario integer, p_password_hash text) OWNER TO postgres;

--
-- Name: actualizar_producto_edicion(integer, character varying, character varying, text, character varying, integer, integer, numeric, numeric, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.actualizar_producto_edicion(p_id_producto integer, p_codigo character varying, p_nombre character varying, p_descripcion text, p_imagen character varying, p_id_categoria integer, p_id_proveedor integer, p_precio_compra numeric, p_precio_venta numeric, p_stock integer) RETURNS boolean
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


ALTER FUNCTION public.actualizar_producto_edicion(p_id_producto integer, p_codigo character varying, p_nombre character varying, p_descripcion text, p_imagen character varying, p_id_categoria integer, p_id_proveedor integer, p_precio_compra numeric, p_precio_venta numeric, p_stock integer) OWNER TO postgres;

--
-- Name: actualizar_proveedor_sistema(integer, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.actualizar_proveedor_sistema(p_id_proveedor integer, p_nombre character varying, p_telefono character varying DEFAULT NULL::character varying, p_email character varying DEFAULT NULL::character varying, p_direccion character varying DEFAULT NULL::character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_proveedor IS NULL OR p_id_proveedor <= 0 THEN
        RAISE EXCEPTION 'ID de proveedor no válido';
    END IF;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre del proveedor no puede estar vacío';
    END IF;

    UPDATE Proveedor
    SET
        nombre = TRIM(p_nombre),
        telefono = NULLIF(TRIM(p_telefono), ''),
        email = NULLIF(TRIM(p_email), ''),
        direccion = NULLIF(TRIM(p_direccion), '')
    WHERE Proveedor.id_proveedor = p_id_proveedor;

    RETURN FOUND;
END;
$$;


ALTER FUNCTION public.actualizar_proveedor_sistema(p_id_proveedor integer, p_nombre character varying, p_telefono character varying, p_email character varying, p_direccion character varying) OWNER TO postgres;

--
-- Name: actualizar_total_compra(integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.actualizar_total_compra(IN p_id_compra integer)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_total NUMERIC(10,2);
BEGIN
    SELECT calcular_total_compra(p_id_compra)
    INTO v_total;

    UPDATE Compra
    SET total = v_total
    WHERE id_compra = p_id_compra;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'No existe una compra con el ID %', p_id_compra;
    END IF;
END;
$$;


ALTER PROCEDURE public.actualizar_total_compra(IN p_id_compra integer) OWNER TO postgres;

--
-- Name: actualizar_totales_factura(integer, numeric, numeric); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.actualizar_totales_factura(IN p_id_factura integer, IN p_descuento numeric DEFAULT 0, IN p_porcentaje_impuesto numeric DEFAULT 0.15)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_subtotal NUMERIC(10,2);
    v_impuesto NUMERIC(10,2);
    v_total NUMERIC(10,2);
BEGIN
    IF p_descuento < 0 THEN
        RAISE EXCEPTION 'El descuento no puede ser negativo';
    END IF;

    IF p_porcentaje_impuesto < 0 THEN
        RAISE EXCEPTION 'El porcentaje de impuesto no puede ser negativo';
    END IF;

    SELECT calcular_subtotal_factura(p_id_factura)
    INTO v_subtotal;

    IF p_descuento > v_subtotal THEN
        RAISE EXCEPTION 'El descuento no puede ser mayor que el subtotal';
    END IF;

    v_impuesto := ROUND((v_subtotal - p_descuento) * p_porcentaje_impuesto, 2);
    v_total := ROUND((v_subtotal - p_descuento) + v_impuesto, 2);

    UPDATE Factura
    SET subtotal = v_subtotal,
        descuento = p_descuento,
        impuesto = v_impuesto,
        total = v_total
    WHERE id_factura = p_id_factura;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'No existe una factura con el ID %', p_id_factura;
    END IF;
END;
$$;


ALTER PROCEDURE public.actualizar_totales_factura(IN p_id_factura integer, IN p_descuento numeric, IN p_porcentaje_impuesto numeric) OWNER TO postgres;

--
-- Name: actualizar_usuario_configurar_cuenta(integer, character varying, character varying, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.actualizar_usuario_configurar_cuenta(p_id_usuario integer, p_nombre character varying, p_email character varying, p_password text) RETURNS boolean
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


ALTER FUNCTION public.actualizar_usuario_configurar_cuenta(p_id_usuario integer, p_nombre character varying, p_email character varying, p_password text) OWNER TO postgres;

--
-- Name: actualizar_usuario_sistema(integer, character varying, character varying, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.actualizar_usuario_sistema(p_id_usuario integer, p_nombre character varying, p_email character varying, p_id_rol integer, p_id_seccion integer DEFAULT NULL::integer, p_password text DEFAULT NULL::text) RETURNS boolean
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


ALTER FUNCTION public.actualizar_usuario_sistema(p_id_usuario integer, p_nombre character varying, p_email character varying, p_id_rol integer, p_id_seccion integer, p_password text) OWNER TO postgres;

--
-- Name: agregar_detalle_compra(integer, integer, integer, numeric); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.agregar_detalle_compra(IN p_id_compra integer, IN p_id_producto integer, IN p_cantidad integer, IN p_costo_unitario numeric)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_total_linea NUMERIC(10,2);
BEGIN
    IF p_cantidad <= 0 THEN
        RAISE EXCEPTION 'La cantidad debe ser mayor que cero';
    END IF;

    IF p_costo_unitario < 0 THEN
        RAISE EXCEPTION 'El costo unitario no puede ser negativo';
    END IF;

    v_total_linea := ROUND(p_cantidad * p_costo_unitario, 2);

    INSERT INTO DetalleCompra (
        id_compra,
        id_producto,
        cantidad,
        costo_unitario,
        total_linea
    )
    VALUES (
        p_id_compra,
        p_id_producto,
        p_cantidad,
        p_costo_unitario,
        v_total_linea
    );

    CALL aumentar_stock_producto(p_id_producto, p_cantidad);

    CALL actualizar_total_compra(p_id_compra);
END;
$$;


ALTER PROCEDURE public.agregar_detalle_compra(IN p_id_compra integer, IN p_id_producto integer, IN p_cantidad integer, IN p_costo_unitario numeric) OWNER TO postgres;

--
-- Name: agregar_detalle_factura(integer, integer, integer, numeric); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.agregar_detalle_factura(IN p_id_factura integer, IN p_id_producto integer, IN p_cantidad integer, IN p_descuento_linea numeric DEFAULT 0)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_precio_unitario NUMERIC(10,2);
    v_stock_actual INT;
    v_total_linea NUMERIC(10,2);
BEGIN
    IF p_cantidad <= 0 THEN
        RAISE EXCEPTION 'La cantidad debe ser mayor que cero';
    END IF;

    IF p_descuento_linea < 0 THEN
        RAISE EXCEPTION 'El descuento de línea no puede ser negativo';
    END IF;

    SELECT precio_venta, stock
    INTO v_precio_unitario, v_stock_actual
    FROM Producto
    WHERE id_producto = p_id_producto;

    IF v_precio_unitario IS NULL THEN
        RAISE EXCEPTION 'No existe un producto con el ID %', p_id_producto;
    END IF;

    IF v_stock_actual < p_cantidad THEN
        RAISE EXCEPTION 'Stock insuficiente. Stock actual: %, cantidad solicitada: %',
            v_stock_actual,
            p_cantidad;
    END IF;

    v_total_linea := ROUND((p_cantidad * v_precio_unitario) - p_descuento_linea, 2);

    IF v_total_linea < 0 THEN
        RAISE EXCEPTION 'El total de línea no puede ser negativo';
    END IF;

    INSERT INTO DetalleFactura (
        id_factura,
        id_producto,
        cantidad,
        precio_unitario,
        descuento_linea,
        total_linea
    )
    VALUES (
        p_id_factura,
        p_id_producto,
        p_cantidad,
        v_precio_unitario,
        p_descuento_linea,
        v_total_linea
    );

    CALL disminuir_stock_producto(p_id_producto, p_cantidad);

    CALL actualizar_totales_factura(p_id_factura, 0, 0.15);
END;
$$;


ALTER PROCEDURE public.agregar_detalle_factura(IN p_id_factura integer, IN p_id_producto integer, IN p_cantidad integer, IN p_descuento_linea numeric) OWNER TO postgres;

--
-- Name: aumentar_stock_producto(integer, integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.aumentar_stock_producto(IN p_id_producto integer, IN p_cantidad integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_cantidad <= 0 THEN
        RAISE EXCEPTION 'La cantidad a agregar debe ser mayor que cero';
    END IF;

    UPDATE Producto
    SET stock = stock + p_cantidad
    WHERE id_producto = p_id_producto;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'No existe un producto con el ID %', p_id_producto;
    END IF;
END;
$$;


ALTER PROCEDURE public.aumentar_stock_producto(IN p_id_producto integer, IN p_cantidad integer) OWNER TO postgres;

--
-- Name: buscar_categorias(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_categorias(p_busqueda text DEFAULT ''::text) RETURNS TABLE(id_categoria integer, nombre character varying)
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


ALTER FUNCTION public.buscar_categorias(p_busqueda text) OWNER TO postgres;

--
-- Name: buscar_clientes_filtrados(text, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_clientes_filtrados(p_busqueda text DEFAULT NULL::text, p_tipo_cliente character varying DEFAULT NULL::character varying) RETURNS TABLE(id_cliente integer, nombres character varying, apellidos character varying, telefono character varying, direccion character varying, identificacion character varying, tipo_cliente character varying)
    LANGUAGE plpgsql
    AS $_$
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
    ORDER BY c.id_cliente DESC;
END;
$_$;


ALTER FUNCTION public.buscar_clientes_filtrados(p_busqueda text, p_tipo_cliente character varying) OWNER TO postgres;

--
-- Name: buscar_compras_filtradas(text, integer, integer, timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_compras_filtradas(p_busqueda text DEFAULT NULL::text, p_id_proveedor integer DEFAULT NULL::integer, p_id_usuario integer DEFAULT NULL::integer, p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS TABLE(id_compra integer, fecha timestamp without time zone, total numeric, proveedor character varying, usuario character varying)
    LANGUAGE plpgsql
    AS $$
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
    ORDER BY c.fecha DESC;
END;
$$;


ALTER FUNCTION public.buscar_compras_filtradas(p_busqueda text, p_id_proveedor integer, p_id_usuario integer, p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: buscar_facturas_filtradas(integer, text, integer, integer, timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_facturas_filtradas(p_id_rol integer DEFAULT NULL::integer, p_busqueda text DEFAULT NULL::text, p_id_seccion integer DEFAULT NULL::integer, p_id_usuario integer DEFAULT NULL::integer, p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS TABLE(id_factura integer, fecha timestamp without time zone, total numeric, cliente text, usuario character varying, seccion character varying)
    LANGUAGE plpgsql
    AS $$
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
        s.nombre AS seccion
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
    ORDER BY f.fecha DESC, f.id_factura DESC;
END;
$$;


ALTER FUNCTION public.buscar_facturas_filtradas(p_id_rol integer, p_busqueda text, p_id_seccion integer, p_id_usuario integer, p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: buscar_productos_catalogo(text, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_productos_catalogo(p_busqueda text DEFAULT NULL::text, p_id_categoria integer DEFAULT NULL::integer, p_disponibilidad character varying DEFAULT NULL::character varying) RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, descripcion text, imagen character varying, categoria character varying, precio_venta numeric, stock integer)
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


ALTER FUNCTION public.buscar_productos_catalogo(p_busqueda text, p_id_categoria integer, p_disponibilidad character varying) OWNER TO postgres;

--
-- Name: buscar_productos_inventario(text, integer, integer, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_productos_inventario(p_busqueda text DEFAULT ''::text, p_id_categoria integer DEFAULT NULL::integer, p_id_proveedor integer DEFAULT NULL::integer, p_id_producto integer DEFAULT NULL::integer, p_stock_bajo boolean DEFAULT false) RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, descripcion text, imagen character varying, categoria character varying, proveedor character varying, precio_compra numeric, precio_venta numeric, stock integer)
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


ALTER FUNCTION public.buscar_productos_inventario(p_busqueda text, p_id_categoria integer, p_id_proveedor integer, p_id_producto integer, p_stock_bajo boolean) OWNER TO postgres;

--
-- Name: buscar_proveedores_filtrados(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_proveedores_filtrados(p_busqueda text DEFAULT NULL::text) RETURNS TABLE(id_proveedor integer, nombre character varying, telefono character varying, email character varying, direccion character varying)
    LANGUAGE plpgsql
    AS $$
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
    ORDER BY p.nombre ASC;
END;
$$;


ALTER FUNCTION public.buscar_proveedores_filtrados(p_busqueda text) OWNER TO postgres;

--
-- Name: buscar_usuarios_filtrados(text, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_usuarios_filtrados(p_busqueda text DEFAULT NULL::text, p_id_rol integer DEFAULT NULL::integer, p_seccion_filtro text DEFAULT NULL::text) RETURNS TABLE(id_usuario integer, nombre character varying, email character varying, id_rol integer, id_seccion integer, rol character varying, seccion character varying)
    LANGUAGE plpgsql
    AS $_$
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
$_$;


ALTER FUNCTION public.buscar_usuarios_filtrados(p_busqueda text, p_id_rol integer, p_seccion_filtro text) OWNER TO postgres;

--
-- Name: calcular_subtotal_factura(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.calcular_subtotal_factura(p_id_factura integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_subtotal NUMERIC(10,2);
BEGIN
    SELECT COALESCE(SUM(total_linea), 0)
    INTO v_subtotal
    FROM DetalleFactura
    WHERE id_factura = p_id_factura;

    RETURN v_subtotal;
END;
$$;


ALTER FUNCTION public.calcular_subtotal_factura(p_id_factura integer) OWNER TO postgres;

--
-- Name: calcular_total_compra(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.calcular_total_compra(p_id_compra integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_total NUMERIC(10,2);
BEGIN
    SELECT COALESCE(SUM(total_linea), 0)
    INTO v_total
    FROM DetalleCompra
    WHERE id_compra = p_id_compra;

    RETURN v_total;
END;
$$;


ALTER FUNCTION public.calcular_total_compra(p_id_compra integer) OWNER TO postgres;

--
-- Name: crear_proveedor_sistema(character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.crear_proveedor_sistema(p_nombre character varying, p_telefono character varying DEFAULT NULL::character varying, p_email character varying DEFAULT NULL::character varying, p_direccion character varying DEFAULT NULL::character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre del proveedor no puede estar vacío';
    END IF;

    INSERT INTO Proveedor (
        nombre,
        telefono,
        email,
        direccion
    )
    VALUES (
        TRIM(p_nombre),
        NULLIF(TRIM(p_telefono), ''),
        NULLIF(TRIM(p_email), ''),
        NULLIF(TRIM(p_direccion), '')
    );

    RETURN TRUE;
END;
$$;


ALTER FUNCTION public.crear_proveedor_sistema(p_nombre character varying, p_telefono character varying, p_email character varying, p_direccion character varying) OWNER TO postgres;

--
-- Name: crear_usuario_sistema(character varying, character varying, text, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.crear_usuario_sistema(p_nombre character varying, p_email character varying, p_password text, p_id_rol integer, p_id_seccion integer DEFAULT NULL::integer) RETURNS boolean
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


ALTER FUNCTION public.crear_usuario_sistema(p_nombre character varying, p_email character varying, p_password text, p_id_rol integer, p_id_seccion integer) OWNER TO postgres;

--
-- Name: disminuir_stock_producto(integer, integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.disminuir_stock_producto(IN p_id_producto integer, IN p_cantidad integer)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_stock_actual INT;
BEGIN
    IF p_cantidad <= 0 THEN
        RAISE EXCEPTION 'La cantidad a descontar debe ser mayor que cero';
    END IF;

    SELECT stock
    INTO v_stock_actual
    FROM Producto
    WHERE id_producto = p_id_producto;

    IF v_stock_actual IS NULL THEN
        RAISE EXCEPTION 'No existe un producto con el ID %', p_id_producto;
    END IF;

    IF v_stock_actual < p_cantidad THEN
        RAISE EXCEPTION 'Stock insuficiente. Stock actual: %, cantidad solicitada: %',
            v_stock_actual,
            p_cantidad;
    END IF;

    UPDATE Producto
    SET stock = stock - p_cantidad
    WHERE id_producto = p_id_producto;
END;
$$;


ALTER PROCEDURE public.disminuir_stock_producto(IN p_id_producto integer, IN p_cantidad integer) OWNER TO postgres;

--
-- Name: eliminar_categoria_sistema(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.eliminar_categoria_sistema(p_id_categoria integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_categoria IS NULL OR p_id_categoria <= 0 THEN
        RAISE EXCEPTION 'ID de categoría no válido';
    END IF;

    DELETE FROM Categoria
    WHERE Categoria.id_categoria = p_id_categoria;

    RETURN FOUND;
END;
$$;


ALTER FUNCTION public.eliminar_categoria_sistema(p_id_categoria integer) OWNER TO postgres;

--
-- Name: eliminar_cliente_sistema(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.eliminar_cliente_sistema(p_id_cliente integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_cliente IS NULL OR p_id_cliente <= 0 THEN
        RAISE EXCEPTION 'ID de cliente no válido';
    END IF;

    DELETE FROM Cliente
    WHERE Cliente.id_cliente = p_id_cliente;

    RETURN FOUND;
END;
$$;


ALTER FUNCTION public.eliminar_cliente_sistema(p_id_cliente integer) OWNER TO postgres;

--
-- Name: eliminar_factura_sistema(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.eliminar_factura_sistema(p_id_factura integer) RETURNS boolean
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


ALTER FUNCTION public.eliminar_factura_sistema(p_id_factura integer) OWNER TO postgres;

--
-- Name: eliminar_producto_sistema(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.eliminar_producto_sistema(p_id_producto integer) RETURNS TABLE(filas_afectadas integer)
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


ALTER FUNCTION public.eliminar_producto_sistema(p_id_producto integer) OWNER TO postgres;

--
-- Name: eliminar_proveedor_sistema(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.eliminar_proveedor_sistema(p_id_proveedor integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_proveedor IS NULL OR p_id_proveedor <= 0 THEN
        RAISE EXCEPTION 'ID de proveedor no válido';
    END IF;

    DELETE FROM Proveedor
    WHERE Proveedor.id_proveedor = p_id_proveedor;

    RETURN FOUND;
END;
$$;


ALTER FUNCTION public.eliminar_proveedor_sistema(p_id_proveedor integer) OWNER TO postgres;

--
-- Name: eliminar_usuario_sistema(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.eliminar_usuario_sistema(p_id_usuario integer, p_id_usuario_actual integer) RETURNS TABLE(filas_afectadas integer)
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


ALTER FUNCTION public.eliminar_usuario_sistema(p_id_usuario integer, p_id_usuario_actual integer) OWNER TO postgres;

--
-- Name: listar_categorias_catalogo(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_categorias_catalogo() RETURNS TABLE(id_categoria integer, nombre character varying)
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


ALTER FUNCTION public.listar_categorias_catalogo() OWNER TO postgres;

--
-- Name: listar_categorias_form_producto(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_categorias_form_producto() RETURNS TABLE(id_categoria integer, nombre character varying)
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


ALTER FUNCTION public.listar_categorias_form_producto() OWNER TO postgres;

--
-- Name: listar_categorias_ordenadas(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_categorias_ordenadas() RETURNS TABLE(id_categoria integer, nombre character varying)
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


ALTER FUNCTION public.listar_categorias_ordenadas() OWNER TO postgres;

--
-- Name: listar_categorias_producto(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_categorias_producto() RETURNS TABLE(id_categoria integer, nombre character varying)
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


ALTER FUNCTION public.listar_categorias_producto() OWNER TO postgres;

--
-- Name: listar_clientes_habituales(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_clientes_habituales() RETURNS TABLE(id_cliente integer, nombres character varying, apellidos character varying, telefono character varying, direccion character varying, identificacion character varying, tipo_cliente character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
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
    WHERE c.identificacion IS DISTINCT FROM 'FUGAZ'
    ORDER BY c.nombres, c.apellidos;
END;
$$;


ALTER FUNCTION public.listar_clientes_habituales() OWNER TO postgres;

--
-- Name: listar_productos_para_factura(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_productos_para_factura() RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, precio_venta numeric, stock integer)
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


ALTER FUNCTION public.listar_productos_para_factura() OWNER TO postgres;

--
-- Name: listar_proveedores_form_producto(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_proveedores_form_producto() RETURNS TABLE(id_proveedor integer, nombre character varying)
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


ALTER FUNCTION public.listar_proveedores_form_producto() OWNER TO postgres;

--
-- Name: listar_proveedores_ordenados(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_proveedores_ordenados() RETURNS TABLE(id_proveedor integer, nombre character varying)
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


ALTER FUNCTION public.listar_proveedores_ordenados() OWNER TO postgres;

--
-- Name: listar_proveedores_para_compras(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_proveedores_para_compras() RETURNS TABLE(id_proveedor integer, nombre character varying)
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


ALTER FUNCTION public.listar_proveedores_para_compras() OWNER TO postgres;

--
-- Name: listar_proveedores_producto(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_proveedores_producto() RETURNS TABLE(id_proveedor integer, nombre character varying)
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


ALTER FUNCTION public.listar_proveedores_producto() OWNER TO postgres;

--
-- Name: listar_roles_ordenados(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_roles_ordenados() RETURNS TABLE(id_rol integer, nombre character varying)
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


ALTER FUNCTION public.listar_roles_ordenados() OWNER TO postgres;

--
-- Name: listar_secciones_ordenadas(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_secciones_ordenadas() RETURNS TABLE(id_seccion integer, nombre character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT
        s.id_seccion,
        s.nombre
    FROM Seccion s
    ORDER BY s.nombre;
END;
$$;


ALTER FUNCTION public.listar_secciones_ordenadas() OWNER TO postgres;

--
-- Name: listar_usuarios_ordenados(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_usuarios_ordenados() RETURNS TABLE(id_usuario integer, nombre character varying)
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


ALTER FUNCTION public.listar_usuarios_ordenados() OWNER TO postgres;

--
-- Name: listar_usuarios_para_compras(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.listar_usuarios_para_compras() RETURNS TABLE(id_usuario integer, nombre character varying)
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


ALTER FUNCTION public.listar_usuarios_para_compras() OWNER TO postgres;

--
-- Name: obtener_categoria_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_categoria_por_id(p_id_categoria integer) RETURNS TABLE(id_categoria integer, nombre character varying)
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


ALTER FUNCTION public.obtener_categoria_por_id(p_id_categoria integer) OWNER TO postgres;

--
-- Name: obtener_cliente_edicion_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_cliente_edicion_por_id(p_id_cliente integer) RETURNS TABLE(id_cliente integer, nombres character varying, apellidos character varying, telefono character varying, direccion character varying, identificacion character varying, tipo_cliente character varying)
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
        c.tipo_cliente
    FROM Cliente c
    WHERE c.id_cliente = p_id_cliente;
END;
$$;


ALTER FUNCTION public.obtener_cliente_edicion_por_id(p_id_cliente integer) OWNER TO postgres;

--
-- Name: obtener_cliente_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_cliente_por_id(p_id_cliente integer) RETURNS TABLE(id_cliente integer, nombres character varying, apellidos character varying, telefono character varying, direccion character varying, identificacion character varying, tipo_cliente character varying, fecha_registro date)
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


ALTER FUNCTION public.obtener_cliente_por_id(p_id_cliente integer) OWNER TO postgres;

--
-- Name: obtener_clientes_recientes_dashboard(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_clientes_recientes_dashboard(p_limite integer DEFAULT 5) RETURNS TABLE(id_cliente integer, nombre text, telefono character varying, fecha_registro date)
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


ALTER FUNCTION public.obtener_clientes_recientes_dashboard(p_limite integer) OWNER TO postgres;

--
-- Name: obtener_clientes_reporte(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_clientes_reporte(p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS TABLE(id_cliente integer, cliente text, telefono character varying, tipo_cliente character varying, cantidad_facturas bigint, total_comprado numeric)
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


ALTER FUNCTION public.obtener_clientes_reporte(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: obtener_compra_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_compra_por_id(p_id_compra integer) RETURNS TABLE(id_compra integer, fecha timestamp without time zone, total numeric, proveedor character varying, proveedor_telefono character varying, proveedor_email character varying, usuario character varying)
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


ALTER FUNCTION public.obtener_compra_por_id(p_id_compra integer) OWNER TO postgres;

--
-- Name: obtener_detalles_compra(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_detalles_compra(p_id_compra integer) RETURNS TABLE(id_detalle integer, cantidad integer, costo_unitario numeric, total_linea numeric, producto_codigo character varying, producto_nombre character varying)
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


ALTER FUNCTION public.obtener_detalles_compra(p_id_compra integer) OWNER TO postgres;

--
-- Name: obtener_factura_detalle_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_factura_detalle_por_id(p_id_factura integer) RETURNS TABLE(id_factura integer, fecha timestamp without time zone, subtotal numeric, descuento numeric, impuesto numeric, total numeric, cliente text, telefono character varying, direccion character varying, usuario character varying, seccion character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_factura IS NULL OR p_id_factura <= 0 THEN
        RAISE EXCEPTION 'ID de factura no válido';
    END IF;

    RETURN QUERY
    SELECT
        f.id_factura,
        f.fecha,
        f.subtotal,
        f.descuento,
        f.impuesto,
        f.total,
        c.nombres || ' ' || c.apellidos AS cliente,
        c.telefono,
        c.direccion,
        u.nombre AS usuario,
        s.nombre AS seccion
    FROM Factura f
    JOIN Cliente c ON f.id_cliente = c.id_cliente
    JOIN Usuario u ON f.id_usuario = u.id_usuario
    JOIN Seccion s ON f.id_seccion = s.id_seccion
    WHERE f.id_factura = p_id_factura;
END;
$$;


ALTER FUNCTION public.obtener_factura_detalle_por_id(p_id_factura integer) OWNER TO postgres;

--
-- Name: obtener_factura_para_impresion(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_factura_para_impresion(p_id_factura integer) RETURNS TABLE(id_factura integer, fecha timestamp without time zone, id_cliente integer, id_usuario integer, id_seccion integer, subtotal numeric, descuento numeric, impuesto numeric, total numeric, tipo_cliente_venta character varying, nombre_cliente_fugaz character varying, cli_nombres character varying, cli_apellidos character varying, cli_telefono character varying, cli_direccion character varying, cli_identificacion character varying, usuario_nombre character varying, seccion_nombre character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_factura IS NULL OR p_id_factura <= 0 THEN
        RAISE EXCEPTION 'ID de factura no válido';
    END IF;

    RETURN QUERY
    SELECT
        f.id_factura,
        f.fecha,
        f.id_cliente,
        f.id_usuario,
        f.id_seccion,
        f.subtotal,
        f.descuento,
        f.impuesto,
        f.total,
        f.tipo_cliente_venta,
        f.nombre_cliente_fugaz,
        c.nombres AS cli_nombres,
        c.apellidos AS cli_apellidos,
        c.telefono AS cli_telefono,
        c.direccion AS cli_direccion,
        c.identificacion AS cli_identificacion,
        u.nombre AS usuario_nombre,
        s.nombre AS seccion_nombre
    FROM Factura f
    JOIN Cliente c ON f.id_cliente = c.id_cliente
    JOIN Usuario u ON f.id_usuario = u.id_usuario
    JOIN Seccion s ON f.id_seccion = s.id_seccion
    WHERE f.id_factura = p_id_factura;
END;
$$;


ALTER FUNCTION public.obtener_factura_para_impresion(p_id_factura integer) OWNER TO postgres;

--
-- Name: obtener_facturas_recientes_dashboard(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_facturas_recientes_dashboard(p_limite integer DEFAULT 5) RETURNS TABLE(id_factura integer, fecha timestamp without time zone, total numeric)
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


ALTER FUNCTION public.obtener_facturas_recientes_dashboard(p_limite integer) OWNER TO postgres;

--
-- Name: obtener_id_cliente_fugaz(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_id_cliente_fugaz() RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_id_cliente INT;
BEGIN
    SELECT c.id_cliente
    INTO v_id_cliente
    FROM Cliente c
    WHERE c.identificacion = 'FUGAZ'
    LIMIT 1;

    RETURN COALESCE(v_id_cliente, 0);
END;
$$;


ALTER FUNCTION public.obtener_id_cliente_fugaz() OWNER TO postgres;

--
-- Name: obtener_lineas_detalle_factura(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_lineas_detalle_factura(p_id_factura integer) RETURNS TABLE(id_detalle integer, codigo character varying, nombre character varying, cantidad integer, precio_unitario numeric, descuento_linea numeric, total_linea numeric)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_factura IS NULL OR p_id_factura <= 0 THEN
        RAISE EXCEPTION 'ID de factura no válido';
    END IF;

    RETURN QUERY
    SELECT
        df.id_detalle,
        p.codigo,
        p.nombre,
        df.cantidad,
        df.precio_unitario,
        df.descuento_linea,
        df.total_linea
    FROM DetalleFactura df
    JOIN Producto p ON df.id_producto = p.id_producto
    WHERE df.id_factura = p_id_factura
    ORDER BY df.id_detalle ASC;
END;
$$;


ALTER FUNCTION public.obtener_lineas_detalle_factura(p_id_factura integer) OWNER TO postgres;

--
-- Name: obtener_lineas_factura_para_impresion(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_lineas_factura_para_impresion(p_id_factura integer) RETURNS TABLE(id_detalle integer, cantidad integer, precio_unitario numeric, descuento_linea numeric, total_linea numeric, producto_nombre character varying, producto_codigo character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_factura IS NULL OR p_id_factura <= 0 THEN
        RAISE EXCEPTION 'ID de factura no válido';
    END IF;

    RETURN QUERY
    SELECT
        df.id_detalle,
        df.cantidad,
        df.precio_unitario,
        df.descuento_linea,
        df.total_linea,
        p.nombre AS producto_nombre,
        p.codigo AS producto_codigo
    FROM DetalleFactura df
    JOIN Producto p ON df.id_producto = p.id_producto
    WHERE df.id_factura = p_id_factura
    ORDER BY df.id_detalle ASC;
END;
$$;


ALTER FUNCTION public.obtener_lineas_factura_para_impresion(p_id_factura integer) OWNER TO postgres;

--
-- Name: obtener_metricas_dashboard(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_metricas_dashboard() RETURNS TABLE(total_clientes bigint, total_productos bigint, total_facturas bigint, total_ventas numeric, ventas_hoy numeric, stock_bajo bigint)
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


ALTER FUNCTION public.obtener_metricas_dashboard() OWNER TO postgres;

--
-- Name: obtener_producto_edicion_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_producto_edicion_por_id(p_id_producto integer) RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, descripcion text, imagen character varying, id_categoria integer, id_proveedor integer, precio_compra numeric, precio_venta numeric, stock integer)
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


ALTER FUNCTION public.obtener_producto_edicion_por_id(p_id_producto integer) OWNER TO postgres;

--
-- Name: obtener_producto_imagen(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_producto_imagen(p_id_producto integer) RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, descripcion text, imagen character varying, precio_venta numeric, stock integer)
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


ALTER FUNCTION public.obtener_producto_imagen(p_id_producto integer) OWNER TO postgres;

--
-- Name: obtener_productos_factura_por_ids(integer[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_productos_factura_por_ids(p_ids_productos integer[]) RETURNS TABLE(id_producto integer, precio_venta numeric, stock integer, nombre character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_ids_productos IS NULL OR ARRAY_LENGTH(p_ids_productos, 1) IS NULL THEN
        RAISE EXCEPTION 'Debe seleccionar al menos un producto';
    END IF;

    RETURN QUERY
    SELECT
        p.id_producto,
        p.precio_venta,
        p.stock,
        p.nombre
    FROM Producto p
    WHERE p.id_producto = ANY(p_ids_productos);
END;
$$;


ALTER FUNCTION public.obtener_productos_factura_por_ids(p_ids_productos integer[]) OWNER TO postgres;

--
-- Name: obtener_productos_mas_vendidos_dashboard(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_productos_mas_vendidos_dashboard(p_limite integer DEFAULT 5) RETURNS TABLE(id_producto integer, producto character varying, cantidad_vendida bigint)
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


ALTER FUNCTION public.obtener_productos_mas_vendidos_dashboard(p_limite integer) OWNER TO postgres;

--
-- Name: obtener_productos_mas_vendidos_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_productos_mas_vendidos_reportes(p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS TABLE(id_producto integer, producto character varying, codigo character varying, cantidad_vendida bigint, total_vendido numeric)
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


ALTER FUNCTION public.obtener_productos_mas_vendidos_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: obtener_productos_reporte(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_productos_reporte(p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS TABLE(id_producto integer, codigo character varying, nombre character varying, stock integer, cantidad_vendida bigint, total_vendido numeric)
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


ALTER FUNCTION public.obtener_productos_reporte(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: obtener_proveedor_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_proveedor_por_id(p_id_proveedor integer) RETURNS TABLE(id_proveedor integer, nombre character varying, telefono character varying, email character varying, direccion character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_proveedor IS NULL OR p_id_proveedor <= 0 THEN
        RAISE EXCEPTION 'ID de proveedor no válido';
    END IF;

    RETURN QUERY
    SELECT
        p.id_proveedor,
        p.nombre,
        p.telefono,
        p.email,
        p.direccion
    FROM Proveedor p
    WHERE p.id_proveedor = p_id_proveedor;
END;
$$;


ALTER FUNCTION public.obtener_proveedor_por_id(p_id_proveedor integer) OWNER TO postgres;

--
-- Name: obtener_resumen_cliente(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_resumen_cliente(p_id_cliente integer) RETURNS TABLE(total_facturas bigint, total_comprado numeric, promedio_compra numeric)
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


ALTER FUNCTION public.obtener_resumen_cliente(p_id_cliente integer) OWNER TO postgres;

--
-- Name: obtener_seccion_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_seccion_por_id(p_id_seccion integer) RETURNS TABLE(id_seccion integer, nombre character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_id_seccion IS NULL OR p_id_seccion <= 0 THEN
        RAISE EXCEPTION 'ID de sección no válido';
    END IF;

    RETURN QUERY
    SELECT
        s.id_seccion,
        s.nombre
    FROM Seccion s
    WHERE s.id_seccion = p_id_seccion;
END;
$$;


ALTER FUNCTION public.obtener_seccion_por_id(p_id_seccion integer) OWNER TO postgres;

--
-- Name: obtener_seccion_por_nombre(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_seccion_por_nombre(p_nombre character varying) RETURNS TABLE(id_seccion integer, nombre character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        RAISE EXCEPTION 'El nombre de la sección no puede estar vacío';
    END IF;

    RETURN QUERY
    SELECT
        s.id_seccion,
        s.nombre
    FROM Seccion s
    WHERE s.nombre = TRIM(p_nombre)
    ORDER BY s.nombre;
END;
$$;


ALTER FUNCTION public.obtener_seccion_por_nombre(p_nombre character varying) OWNER TO postgres;

--
-- Name: obtener_stock_bajo_reportes(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_stock_bajo_reportes() RETURNS integer
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


ALTER FUNCTION public.obtener_stock_bajo_reportes() OWNER TO postgres;

--
-- Name: obtener_total_clientes_reportes(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_total_clientes_reportes() RETURNS integer
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


ALTER FUNCTION public.obtener_total_clientes_reportes() OWNER TO postgres;

--
-- Name: obtener_total_facturas_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_total_facturas_reportes(p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS integer
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


ALTER FUNCTION public.obtener_total_facturas_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: obtener_total_ventas_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_total_ventas_reportes(p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS numeric
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


ALTER FUNCTION public.obtener_total_ventas_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: obtener_ultimas_facturas_cliente(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_ultimas_facturas_cliente(p_id_cliente integer, p_limite integer DEFAULT 10) RETURNS TABLE(id_factura integer, fecha timestamp without time zone, subtotal numeric, descuento numeric, impuesto numeric, total numeric)
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


ALTER FUNCTION public.obtener_ultimas_facturas_cliente(p_id_cliente integer, p_limite integer) OWNER TO postgres;

--
-- Name: obtener_ultimos_productos_vendidos_dashboard(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_ultimos_productos_vendidos_dashboard(p_limite integer DEFAULT 6) RETURNS TABLE(id_factura integer, id_producto integer, nombre character varying, cantidad integer, subtotal numeric, fecha timestamp without time zone)
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


ALTER FUNCTION public.obtener_ultimos_productos_vendidos_dashboard(p_limite integer) OWNER TO postgres;

--
-- Name: obtener_usuario_configurar_cuenta(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_usuario_configurar_cuenta(p_id_usuario integer) RETURNS TABLE(id_usuario integer, nombre character varying, email character varying, password text, id_rol integer, id_seccion integer, rol_nombre character varying, seccion_nombre character varying)
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


ALTER FUNCTION public.obtener_usuario_configurar_cuenta(p_id_usuario integer) OWNER TO postgres;

--
-- Name: obtener_usuario_edicion_por_id(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_usuario_edicion_por_id(p_id_usuario integer) RETURNS TABLE(id_usuario integer, nombre character varying, email character varying, id_rol integer, id_seccion integer)
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


ALTER FUNCTION public.obtener_usuario_edicion_por_id(p_id_usuario integer) OWNER TO postgres;

--
-- Name: obtener_usuario_login(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_usuario_login(p_email character varying) RETURNS TABLE(id_usuario integer, nombre character varying, email character varying, password text, id_rol integer, id_seccion integer, rol character varying)
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


ALTER FUNCTION public.obtener_usuario_login(p_email character varying) OWNER TO postgres;

--
-- Name: obtener_ventas_dashboard(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_ventas_dashboard(p_dias integer DEFAULT 30) RETURNS TABLE(dia date, total_dia numeric)
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


ALTER FUNCTION public.obtener_ventas_dashboard(p_dias integer) OWNER TO postgres;

--
-- Name: obtener_ventas_detalladas_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_ventas_detalladas_reportes(p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS TABLE(id_factura integer, fecha timestamp without time zone, subtotal numeric, descuento numeric, impuesto numeric, total numeric, cliente text, usuario character varying, seccion character varying)
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


ALTER FUNCTION public.obtener_ventas_detalladas_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: obtener_ventas_por_dia_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.obtener_ventas_por_dia_reportes(p_fecha_desde timestamp without time zone DEFAULT NULL::timestamp without time zone, p_fecha_hasta timestamp without time zone DEFAULT NULL::timestamp without time zone) RETURNS TABLE(dia text, total_dia numeric, cantidad_facturas bigint)
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


ALTER FUNCTION public.obtener_ventas_por_dia_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone) OWNER TO postgres;

--
-- Name: registrar_auditoria(character varying, character varying, character varying, text); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.registrar_auditoria(IN p_usuario character varying, IN p_accion character varying, IN p_tabla_afectada character varying, IN p_descripcion text)
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO Auditoria (
        usuario,
        accion,
        tabla_afectada,
        descripcion,
        fecha_registro
    )
    VALUES (
        p_usuario,
        p_accion,
        p_tabla_afectada,
        p_descripcion,
        NOW()
    );
END;
$$;


ALTER PROCEDURE public.registrar_auditoria(IN p_usuario character varying, IN p_accion character varying, IN p_tabla_afectada character varying, IN p_descripcion text) OWNER TO postgres;

--
-- Name: registrar_categoria(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.registrar_categoria(p_nombre character varying) RETURNS TABLE(id_categoria integer, nombre character varying)
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


ALTER FUNCTION public.registrar_categoria(p_nombre character varying) OWNER TO postgres;

--
-- Name: registrar_cliente(character varying, character varying, character varying, character varying, character varying, character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.registrar_cliente(IN p_nombres character varying, IN p_apellidos character varying, IN p_telefono character varying, IN p_direccion character varying, IN p_identificacion character varying, IN p_tipo_cliente character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_tipo_cliente NOT IN ('Mayorista', 'Detallista') THEN
        RAISE EXCEPTION 'Tipo de cliente inválido. Debe ser Mayorista o Detallista';
    END IF;

    INSERT INTO Cliente (
        nombres,
        apellidos,
        telefono,
        direccion,
        identificacion,
        tipo_cliente
    )
    VALUES (
        p_nombres,
        p_apellidos,
        p_telefono,
        p_direccion,
        p_identificacion,
        p_tipo_cliente
    );
END;
$$;


ALTER PROCEDURE public.registrar_cliente(IN p_nombres character varying, IN p_apellidos character varying, IN p_telefono character varying, IN p_direccion character varying, IN p_identificacion character varying, IN p_tipo_cliente character varying) OWNER TO postgres;

--
-- Name: registrar_cliente_sistema(character varying, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.registrar_cliente_sistema(p_nombres character varying, p_apellidos character varying, p_telefono character varying DEFAULT NULL::character varying, p_direccion character varying DEFAULT NULL::character varying, p_identificacion character varying DEFAULT NULL::character varying, p_tipo_cliente character varying DEFAULT 'Detallista'::character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_nombres IS NULL OR TRIM(p_nombres) = '' THEN
        RAISE EXCEPTION 'Los nombres del cliente no pueden estar vacíos';
    END IF;

    IF p_apellidos IS NULL OR TRIM(p_apellidos) = '' THEN
        RAISE EXCEPTION 'Los apellidos del cliente no pueden estar vacíos';
    END IF;

    IF p_tipo_cliente IS NULL
       OR p_tipo_cliente NOT IN ('Mayorista', 'Detallista') THEN
        RAISE EXCEPTION 'Tipo de cliente no válido';
    END IF;

    INSERT INTO Cliente (
        nombres,
        apellidos,
        telefono,
        direccion,
        identificacion,
        tipo_cliente
    )
    VALUES (
        TRIM(p_nombres),
        TRIM(p_apellidos),
        NULLIF(TRIM(p_telefono), ''),
        NULLIF(TRIM(p_direccion), ''),
        NULLIF(TRIM(p_identificacion), ''),
        p_tipo_cliente
    );

    RETURN TRUE;
END;
$$;


ALTER FUNCTION public.registrar_cliente_sistema(p_nombres character varying, p_apellidos character varying, p_telefono character varying, p_direccion character varying, p_identificacion character varying, p_tipo_cliente character varying) OWNER TO postgres;

--
-- Name: registrar_factura_sistema(integer, integer, integer, numeric, numeric, numeric, numeric, character varying, character varying, jsonb); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.registrar_factura_sistema(p_id_cliente integer, p_id_usuario integer, p_id_seccion integer, p_subtotal numeric, p_descuento numeric, p_impuesto numeric, p_total numeric, p_tipo_cliente_venta character varying, p_nombre_cliente_fugaz character varying, p_items jsonb) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_id_factura INT;
    v_item JSONB;
    v_id_producto INT;
    v_cantidad INT;
    v_precio_unitario NUMERIC;
    v_descuento_linea NUMERIC;
    v_total_linea NUMERIC;
BEGIN
    IF p_id_cliente IS NULL OR p_id_cliente <= 0 THEN
        RAISE EXCEPTION 'Debe seleccionar un cliente válido';
    END IF;

    IF p_id_usuario IS NULL OR p_id_usuario <= 0 THEN
        RAISE EXCEPTION 'ID de usuario no válido';
    END IF;

    IF p_id_seccion IS NULL OR p_id_seccion <= 0 THEN
        RAISE EXCEPTION 'Debe seleccionar una sección válida';
    END IF;

    IF p_tipo_cliente_venta NOT IN ('Habitual', 'Fugaz') THEN
        RAISE EXCEPTION 'Tipo de cliente de venta no válido';
    END IF;

    IF p_items IS NULL OR JSONB_TYPEOF(p_items) <> 'array' OR JSONB_ARRAY_LENGTH(p_items) = 0 THEN
        RAISE EXCEPTION 'Debe agregar al menos un producto a la factura';
    END IF;

    IF p_subtotal IS NULL OR p_subtotal < 0 THEN
        RAISE EXCEPTION 'Subtotal no válido';
    END IF;

    IF p_descuento IS NULL OR p_descuento < 0 THEN
        RAISE EXCEPTION 'Descuento no válido';
    END IF;

    IF p_impuesto IS NULL OR p_impuesto < 0 THEN
        RAISE EXCEPTION 'Impuesto no válido';
    END IF;

    IF p_total IS NULL OR p_total < 0 THEN
        RAISE EXCEPTION 'Total no válido';
    END IF;

    INSERT INTO Factura (
        id_cliente,
        id_usuario,
        id_seccion,
        subtotal,
        descuento,
        impuesto,
        total,
        tipo_cliente_venta,
        nombre_cliente_fugaz
    )
    VALUES (
        p_id_cliente,
        p_id_usuario,
        p_id_seccion,
        p_subtotal,
        p_descuento,
        p_impuesto,
        p_total,
        p_tipo_cliente_venta,
        CASE
            WHEN p_tipo_cliente_venta = 'Fugaz'
                THEN NULLIF(TRIM(p_nombre_cliente_fugaz), '')
            ELSE NULL
        END
    )
    RETURNING id_factura INTO v_id_factura;

    FOR v_item IN
        SELECT value
        FROM JSONB_ARRAY_ELEMENTS(p_items)
    LOOP
        v_id_producto := (v_item->>'id_producto')::INT;
        v_cantidad := (v_item->>'cantidad')::INT;
        v_precio_unitario := (v_item->>'precio_unitario')::NUMERIC;
        v_descuento_linea := (v_item->>'descuento_linea')::NUMERIC;
        v_total_linea := (v_item->>'total_linea')::NUMERIC;

        IF v_id_producto IS NULL OR v_id_producto <= 0 THEN
            RAISE EXCEPTION 'Producto no válido en el detalle de factura';
        END IF;

        IF v_cantidad IS NULL OR v_cantidad <= 0 THEN
            RAISE EXCEPTION 'Cantidad no válida en el detalle de factura';
        END IF;

        IF v_precio_unitario IS NULL OR v_precio_unitario < 0 THEN
            RAISE EXCEPTION 'Precio unitario no válido en el detalle de factura';
        END IF;

        IF v_descuento_linea IS NULL OR v_descuento_linea < 0 THEN
            RAISE EXCEPTION 'Descuento de línea no válido';
        END IF;

        IF v_total_linea IS NULL OR v_total_linea < 0 THEN
            RAISE EXCEPTION 'Total de línea no válido';
        END IF;

        UPDATE Producto
        SET stock = stock - v_cantidad
        WHERE Producto.id_producto = v_id_producto
          AND Producto.stock >= v_cantidad;

        IF NOT FOUND THEN
            RAISE EXCEPTION 'Stock insuficiente o producto no encontrado para el producto ID %', v_id_producto;
        END IF;

        INSERT INTO DetalleFactura (
            id_factura,
            id_producto,
            cantidad,
            precio_unitario,
            descuento_linea,
            total_linea
        )
        VALUES (
            v_id_factura,
            v_id_producto,
            v_cantidad,
            v_precio_unitario,
            v_descuento_linea,
            v_total_linea
        );
    END LOOP;

    RETURN v_id_factura;
END;
$$;


ALTER FUNCTION public.registrar_factura_sistema(p_id_cliente integer, p_id_usuario integer, p_id_seccion integer, p_subtotal numeric, p_descuento numeric, p_impuesto numeric, p_total numeric, p_tipo_cliente_venta character varying, p_nombre_cliente_fugaz character varying, p_items jsonb) OWNER TO postgres;

--
-- Name: registrar_producto(character varying, character varying, text, character varying, integer, integer, numeric, numeric, integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.registrar_producto(IN p_codigo character varying, IN p_nombre character varying, IN p_descripcion text, IN p_imagen character varying, IN p_id_categoria integer, IN p_id_proveedor integer, IN p_precio_compra numeric, IN p_precio_venta numeric, IN p_stock integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF p_precio_compra < 0 THEN
        RAISE EXCEPTION 'El precio de compra no puede ser negativo';
    END IF;

    IF p_precio_venta < 0 THEN
        RAISE EXCEPTION 'El precio de venta no puede ser negativo';
    END IF;

    IF p_stock < 0 THEN
        RAISE EXCEPTION 'El stock no puede ser negativo';
    END IF;

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
        p_codigo,
        p_nombre,
        COALESCE(p_descripcion, 'Sin descripción'),
        p_imagen,
        p_id_categoria,
        p_id_proveedor,
        p_precio_compra,
        p_precio_venta,
        p_stock
    );
END;
$$;


ALTER PROCEDURE public.registrar_producto(IN p_codigo character varying, IN p_nombre character varying, IN p_descripcion text, IN p_imagen character varying, IN p_id_categoria integer, IN p_id_proveedor integer, IN p_precio_compra numeric, IN p_precio_venta numeric, IN p_stock integer) OWNER TO postgres;

--
-- Name: registrar_producto_formulario(character varying, character varying, text, character varying, integer, integer, numeric, numeric, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.registrar_producto_formulario(p_codigo character varying, p_nombre character varying, p_descripcion text, p_imagen character varying, p_id_categoria integer, p_id_proveedor integer, p_precio_compra numeric, p_precio_venta numeric, p_stock integer) RETURNS TABLE(id_producto integer)
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


ALTER FUNCTION public.registrar_producto_formulario(p_codigo character varying, p_nombre character varying, p_descripcion text, p_imagen character varying, p_id_categoria integer, p_id_proveedor integer, p_precio_compra numeric, p_precio_venta numeric, p_stock integer) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: auditoria; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.auditoria (
    id_auditoria integer NOT NULL,
    usuario character varying(100) NOT NULL,
    accion character varying(50) NOT NULL,
    tabla_afectada character varying(100) NOT NULL,
    descripcion text,
    fecha_registro timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.auditoria OWNER TO postgres;

--
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.auditoria_id_auditoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auditoria_id_auditoria_seq OWNER TO postgres;

--
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.auditoria_id_auditoria_seq OWNED BY public.auditoria.id_auditoria;


--
-- Name: categoria; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categoria (
    id_categoria integer NOT NULL,
    nombre character varying(80) NOT NULL
);


ALTER TABLE public.categoria OWNER TO postgres;

--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categoria_id_categoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categoria_id_categoria_seq OWNER TO postgres;

--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categoria_id_categoria_seq OWNED BY public.categoria.id_categoria;


--
-- Name: cliente; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cliente (
    id_cliente integer NOT NULL,
    nombres character varying(80) NOT NULL,
    apellidos character varying(80) NOT NULL,
    telefono character varying(30),
    direccion character varying(200),
    identificacion character varying(40),
    tipo_cliente character varying(12) DEFAULT 'Detallista'::character varying NOT NULL,
    fecha_registro date DEFAULT CURRENT_DATE NOT NULL,
    CONSTRAINT ck_cliente_tipo CHECK (((tipo_cliente)::text = ANY ((ARRAY['Mayorista'::character varying, 'Detallista'::character varying])::text[])))
);


ALTER TABLE public.cliente OWNER TO postgres;

--
-- Name: cliente_id_cliente_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cliente_id_cliente_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cliente_id_cliente_seq OWNER TO postgres;

--
-- Name: cliente_id_cliente_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cliente_id_cliente_seq OWNED BY public.cliente.id_cliente;


--
-- Name: compra; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.compra (
    id_compra integer NOT NULL,
    fecha timestamp without time zone DEFAULT now() NOT NULL,
    id_proveedor integer NOT NULL,
    id_usuario integer NOT NULL,
    total numeric(10,2) DEFAULT 0 NOT NULL
);


ALTER TABLE public.compra OWNER TO postgres;

--
-- Name: compra_id_compra_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.compra_id_compra_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.compra_id_compra_seq OWNER TO postgres;

--
-- Name: compra_id_compra_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.compra_id_compra_seq OWNED BY public.compra.id_compra;


--
-- Name: detallecompra; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detallecompra (
    id_detalle integer NOT NULL,
    id_compra integer NOT NULL,
    id_producto integer NOT NULL,
    cantidad integer NOT NULL,
    costo_unitario numeric(10,2) NOT NULL,
    total_linea numeric(10,2) NOT NULL,
    CONSTRAINT detallecompra_cantidad_check CHECK ((cantidad > 0)),
    CONSTRAINT detallecompra_costo_unitario_check CHECK ((costo_unitario >= (0)::numeric))
);


ALTER TABLE public.detallecompra OWNER TO postgres;

--
-- Name: detallecompra_id_detalle_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.detallecompra_id_detalle_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detallecompra_id_detalle_seq OWNER TO postgres;

--
-- Name: detallecompra_id_detalle_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.detallecompra_id_detalle_seq OWNED BY public.detallecompra.id_detalle;


--
-- Name: detallefactura; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detallefactura (
    id_detalle integer NOT NULL,
    id_factura integer NOT NULL,
    id_producto integer NOT NULL,
    cantidad integer NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    descuento_linea numeric(10,2) DEFAULT 0 NOT NULL,
    total_linea numeric(10,2) NOT NULL,
    CONSTRAINT detallefactura_cantidad_check CHECK ((cantidad > 0)),
    CONSTRAINT detallefactura_precio_unitario_check CHECK ((precio_unitario >= (0)::numeric))
);


ALTER TABLE public.detallefactura OWNER TO postgres;

--
-- Name: detallefactura_id_detalle_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.detallefactura_id_detalle_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detallefactura_id_detalle_seq OWNER TO postgres;

--
-- Name: detallefactura_id_detalle_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.detallefactura_id_detalle_seq OWNED BY public.detallefactura.id_detalle;


--
-- Name: factura; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.factura (
    id_factura integer NOT NULL,
    fecha timestamp without time zone DEFAULT now() NOT NULL,
    id_cliente integer NOT NULL,
    id_usuario integer NOT NULL,
    id_seccion integer NOT NULL,
    subtotal numeric(10,2) DEFAULT 0 NOT NULL,
    descuento numeric(10,2) DEFAULT 0 NOT NULL,
    impuesto numeric(10,2) DEFAULT 0 NOT NULL,
    total numeric(10,2) DEFAULT 0 NOT NULL,
    tipo_cliente_venta character varying(10) DEFAULT 'Habitual'::character varying NOT NULL,
    nombre_cliente_fugaz character varying(150),
    CONSTRAINT factura_tipo_cliente_venta_check CHECK (((tipo_cliente_venta)::text = ANY ((ARRAY['Habitual'::character varying, 'Fugaz'::character varying])::text[])))
);


ALTER TABLE public.factura OWNER TO postgres;

--
-- Name: factura_id_factura_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.factura_id_factura_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.factura_id_factura_seq OWNER TO postgres;

--
-- Name: factura_id_factura_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.factura_id_factura_seq OWNED BY public.factura.id_factura;


--
-- Name: producto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.producto (
    id_producto integer NOT NULL,
    codigo character varying(50) NOT NULL,
    nombre character varying(120) NOT NULL,
    descripcion text DEFAULT 'Sin descripción'::text,
    imagen character varying(200),
    id_categoria integer,
    id_proveedor integer,
    precio_compra numeric(10,2) NOT NULL,
    precio_venta numeric(10,2) NOT NULL,
    stock integer DEFAULT 0 NOT NULL,
    CONSTRAINT producto_precio_compra_check CHECK ((precio_compra >= (0)::numeric)),
    CONSTRAINT producto_precio_venta_check CHECK ((precio_venta >= (0)::numeric)),
    CONSTRAINT producto_stock_check CHECK ((stock >= 0))
);


ALTER TABLE public.producto OWNER TO postgres;

--
-- Name: producto_id_producto_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.producto_id_producto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.producto_id_producto_seq OWNER TO postgres;

--
-- Name: producto_id_producto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.producto_id_producto_seq OWNED BY public.producto.id_producto;


--
-- Name: proveedor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.proveedor (
    id_proveedor integer NOT NULL,
    nombre character varying(120) NOT NULL,
    telefono character varying(30),
    email character varying(120),
    direccion character varying(200)
);


ALTER TABLE public.proveedor OWNER TO postgres;

--
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.proveedor_id_proveedor_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.proveedor_id_proveedor_seq OWNER TO postgres;

--
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.proveedor_id_proveedor_seq OWNED BY public.proveedor.id_proveedor;


--
-- Name: rol; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rol (
    id_rol integer NOT NULL,
    nombre character varying(30) NOT NULL
);


ALTER TABLE public.rol OWNER TO postgres;

--
-- Name: rol_id_rol_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rol_id_rol_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rol_id_rol_seq OWNER TO postgres;

--
-- Name: rol_id_rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rol_id_rol_seq OWNED BY public.rol.id_rol;


--
-- Name: seccion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.seccion (
    id_seccion integer NOT NULL,
    nombre character varying(30) NOT NULL
);


ALTER TABLE public.seccion OWNER TO postgres;

--
-- Name: seccion_id_seccion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.seccion_id_seccion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.seccion_id_seccion_seq OWNER TO postgres;

--
-- Name: seccion_id_seccion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.seccion_id_seccion_seq OWNED BY public.seccion.id_seccion;


--
-- Name: usuario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuario (
    id_usuario integer NOT NULL,
    nombre character varying(100) NOT NULL,
    email character varying(120) NOT NULL,
    password text NOT NULL,
    id_rol integer NOT NULL,
    id_seccion integer
);


ALTER TABLE public.usuario OWNER TO postgres;

--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuario_id_usuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuario_id_usuario_seq OWNER TO postgres;

--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuario_id_usuario_seq OWNED BY public.usuario.id_usuario;


--
-- Name: auditoria id_auditoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria ALTER COLUMN id_auditoria SET DEFAULT nextval('public.auditoria_id_auditoria_seq'::regclass);


--
-- Name: categoria id_categoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria ALTER COLUMN id_categoria SET DEFAULT nextval('public.categoria_id_categoria_seq'::regclass);


--
-- Name: cliente id_cliente; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cliente ALTER COLUMN id_cliente SET DEFAULT nextval('public.cliente_id_cliente_seq'::regclass);


--
-- Name: compra id_compra; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compra ALTER COLUMN id_compra SET DEFAULT nextval('public.compra_id_compra_seq'::regclass);


--
-- Name: detallecompra id_detalle; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallecompra ALTER COLUMN id_detalle SET DEFAULT nextval('public.detallecompra_id_detalle_seq'::regclass);


--
-- Name: detallefactura id_detalle; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallefactura ALTER COLUMN id_detalle SET DEFAULT nextval('public.detallefactura_id_detalle_seq'::regclass);


--
-- Name: factura id_factura; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factura ALTER COLUMN id_factura SET DEFAULT nextval('public.factura_id_factura_seq'::regclass);


--
-- Name: producto id_producto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto ALTER COLUMN id_producto SET DEFAULT nextval('public.producto_id_producto_seq'::regclass);


--
-- Name: proveedor id_proveedor; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proveedor ALTER COLUMN id_proveedor SET DEFAULT nextval('public.proveedor_id_proveedor_seq'::regclass);


--
-- Name: rol id_rol; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rol ALTER COLUMN id_rol SET DEFAULT nextval('public.rol_id_rol_seq'::regclass);


--
-- Name: seccion id_seccion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seccion ALTER COLUMN id_seccion SET DEFAULT nextval('public.seccion_id_seccion_seq'::regclass);


--
-- Name: usuario id_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario ALTER COLUMN id_usuario SET DEFAULT nextval('public.usuario_id_usuario_seq'::regclass);


--
-- Data for Name: auditoria; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.auditoria (id_auditoria, usuario, accion, tabla_afectada, descripcion, fecha_registro) FROM stdin;
1	admin	INSERT	Producto	Se registró un nuevo producto desde procedimiento almacenado	2026-05-03 02:10:38.779183
\.


--
-- Data for Name: categoria; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categoria (id_categoria, nombre) FROM stdin;
1	Camisetas
2	Hoodies
3	Tazas personalizadas
4	Accesorios
5	Vinilos
6	Stickers
7	Bolsos
8	Termos
11	Camisas
\.


--
-- Data for Name: cliente; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cliente (id_cliente, nombres, apellidos, telefono, direccion, identificacion, tipo_cliente, fecha_registro) FROM stdin;
1	Cliente	Fugaz	\N	\N	FUGAZ	Detallista	2026-05-03
2	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista	2026-01-10
3	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista	2026-01-15
4	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista	2025-12-20
5	Karla	González	8855-1144	León	003-090599-0099L	Detallista	2026-02-01
6	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista	2026-02-05
7	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista	2025-11-18
8	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista	2026-03-01
9	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista	2026-03-05
10	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista	2025-10-10
11	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista	2026-03-10
12	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista	2026-03-15
13	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista	2025-09-25
14	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista	2026-03-20
15	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista	2026-03-22
16	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista	2025-08-15
17	Roberto	Morales	88100001	Managua	AUTO-0001	Detallista	2025-11-19
18	Gabriela	Navarro	88100002	Masaya	AUTO-0002	Detallista	2025-11-23
19	Luis	Gutiérrez	88100003	León	AUTO-0003	Detallista	2025-11-27
20	Daniela	Rivas	88100004	Granada	AUTO-0004	Detallista	2025-12-01
21	Fernando	Aguilar	88100005	Carazo	AUTO-0005	Mayorista	2025-12-05
22	Valeria	Vargas	88100006	Estelí	AUTO-0006	Detallista	2025-12-09
23	Miguel	Pineda	88100007	Chinandega	AUTO-0007	Detallista	2025-12-13
24	Andrea	Reyes	88100008	Rivas	AUTO-0008	Detallista	2025-12-17
25	Sergio	Campos	88100009	Matagalpa	AUTO-0009	Detallista	2025-12-21
26	Camila	Duarte	88100010	Jinotepe	AUTO-0010	Mayorista	2025-12-25
27	Eduardo	Flores	88100011	Managua	AUTO-0011	Detallista	2025-12-29
28	Natalia	Castro	88100012	Masaya	AUTO-0012	Detallista	2026-01-02
29	Bryan	Obando	88100013	León	AUTO-0013	Detallista	2026-01-06
30	Fernanda	Rivera	88100014	Granada	AUTO-0014	Detallista	2026-01-10
31	Héctor	Molina	88100015	Carazo	AUTO-0015	Mayorista	2026-01-14
32	Adriana	Chávez	88100016	Estelí	AUTO-0016	Detallista	2026-01-18
33	Samuel	Cruz	88100017	Chinandega	AUTO-0017	Detallista	2026-01-22
34	Isabel	López	88100018	Rivas	AUTO-0018	Detallista	2026-01-26
35	Ángel	Zamora	88100019	Matagalpa	AUTO-0019	Detallista	2026-01-30
36	Renata	Salinas	88100020	Jinotepe	AUTO-0020	Mayorista	2026-02-03
37	Roberto	Morales	88100021	Managua	AUTO-0021	Detallista	2026-02-07
38	Gabriela	Navarro	88100022	Masaya	AUTO-0022	Detallista	2026-02-11
39	Luis	Gutiérrez	88100023	León	AUTO-0023	Detallista	2026-02-15
40	Daniela	Rivas	88100024	Granada	AUTO-0024	Detallista	2026-02-19
41	Fernando	Aguilar	88100025	Carazo	AUTO-0025	Mayorista	2026-02-23
42	Valeria	Vargas	88100026	Estelí	AUTO-0026	Detallista	2026-02-27
43	Miguel	Pineda	88100027	Chinandega	AUTO-0027	Detallista	2026-03-03
44	Andrea	Reyes	88100028	Rivas	AUTO-0028	Detallista	2026-03-07
45	Sergio	Campos	88100029	Matagalpa	AUTO-0029	Detallista	2026-03-11
46	Camila	Duarte	88100030	Jinotepe	AUTO-0030	Mayorista	2026-03-15
47	Eduardo	Flores	88100031	Managua	AUTO-0031	Detallista	2026-03-19
48	Natalia	Castro	88100032	Masaya	AUTO-0032	Detallista	2026-03-23
49	Bryan	Obando	88100033	León	AUTO-0033	Detallista	2026-03-27
50	Fernanda	Rivera	88100034	Granada	AUTO-0034	Detallista	2026-03-31
51	Héctor	Molina	88100035	Carazo	AUTO-0035	Mayorista	2026-04-04
52	Cliente	Fugaz	\N	\N	FUGAZ	Detallista	2026-05-03
53	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista	2026-01-10
54	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista	2026-01-15
55	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista	2025-12-20
56	Karla	González	8855-1144	León	003-090599-0099L	Detallista	2026-02-01
57	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista	2026-02-05
58	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista	2025-11-18
59	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista	2026-03-01
60	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista	2026-03-05
61	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista	2025-10-10
62	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista	2026-03-10
63	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista	2026-03-15
64	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista	2025-09-25
65	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista	2026-03-20
66	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista	2026-03-22
67	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista	2025-08-15
68	Roberto	Morales	88100001	Managua	AUTO-0001	Detallista	2025-11-19
69	Gabriela	Navarro	88100002	Masaya	AUTO-0002	Detallista	2025-11-23
70	Luis	Gutiérrez	88100003	León	AUTO-0003	Detallista	2025-11-27
71	Daniela	Rivas	88100004	Granada	AUTO-0004	Detallista	2025-12-01
72	Fernando	Aguilar	88100005	Carazo	AUTO-0005	Mayorista	2025-12-05
73	Valeria	Vargas	88100006	Estelí	AUTO-0006	Detallista	2025-12-09
74	Miguel	Pineda	88100007	Chinandega	AUTO-0007	Detallista	2025-12-13
75	Andrea	Reyes	88100008	Rivas	AUTO-0008	Detallista	2025-12-17
76	Sergio	Campos	88100009	Matagalpa	AUTO-0009	Detallista	2025-12-21
77	Camila	Duarte	88100010	Jinotepe	AUTO-0010	Mayorista	2025-12-25
78	Eduardo	Flores	88100011	Managua	AUTO-0011	Detallista	2025-12-29
79	Natalia	Castro	88100012	Masaya	AUTO-0012	Detallista	2026-01-02
80	Bryan	Obando	88100013	León	AUTO-0013	Detallista	2026-01-06
81	Fernanda	Rivera	88100014	Granada	AUTO-0014	Detallista	2026-01-10
82	Héctor	Molina	88100015	Carazo	AUTO-0015	Mayorista	2026-01-14
83	Adriana	Chávez	88100016	Estelí	AUTO-0016	Detallista	2026-01-18
84	Samuel	Cruz	88100017	Chinandega	AUTO-0017	Detallista	2026-01-22
85	Isabel	López	88100018	Rivas	AUTO-0018	Detallista	2026-01-26
86	Ángel	Zamora	88100019	Matagalpa	AUTO-0019	Detallista	2026-01-30
87	Renata	Salinas	88100020	Jinotepe	AUTO-0020	Mayorista	2026-02-03
88	Roberto	Morales	88100021	Managua	AUTO-0021	Detallista	2026-02-07
89	Gabriela	Navarro	88100022	Masaya	AUTO-0022	Detallista	2026-02-11
90	Luis	Gutiérrez	88100023	León	AUTO-0023	Detallista	2026-02-15
91	Daniela	Rivas	88100024	Granada	AUTO-0024	Detallista	2026-02-19
92	Fernando	Aguilar	88100025	Carazo	AUTO-0025	Mayorista	2026-02-23
93	Valeria	Vargas	88100026	Estelí	AUTO-0026	Detallista	2026-02-27
94	Miguel	Pineda	88100027	Chinandega	AUTO-0027	Detallista	2026-03-03
95	Andrea	Reyes	88100028	Rivas	AUTO-0028	Detallista	2026-03-07
96	Sergio	Campos	88100029	Matagalpa	AUTO-0029	Detallista	2026-03-11
97	Camila	Duarte	88100030	Jinotepe	AUTO-0030	Mayorista	2026-03-15
98	Eduardo	Flores	88100031	Managua	AUTO-0031	Detallista	2026-03-19
99	Natalia	Castro	88100032	Masaya	AUTO-0032	Detallista	2026-03-23
100	Bryan	Obando	88100033	León	AUTO-0033	Detallista	2026-03-27
101	Fernanda	Rivera	88100034	Granada	AUTO-0034	Detallista	2026-03-31
102	Héctor	Molina	88100035	Carazo	AUTO-0035	Mayorista	2026-04-04
103	Cliente	Fugaz	\N	\N	FUGAZ	Detallista	2026-05-03
104	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista	2026-01-10
105	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista	2026-01-15
106	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista	2025-12-20
107	Karla	González	8855-1144	León	003-090599-0099L	Detallista	2026-02-01
108	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista	2026-02-05
109	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista	2025-11-18
110	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista	2026-03-01
111	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista	2026-03-05
112	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista	2025-10-10
113	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista	2026-03-10
114	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista	2026-03-15
115	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista	2025-09-25
116	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista	2026-03-20
117	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista	2026-03-22
118	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista	2025-08-15
119	Roberto	Morales	88100001	Managua	AUTO-0001	Detallista	2025-11-19
120	Gabriela	Navarro	88100002	Masaya	AUTO-0002	Detallista	2025-11-23
121	Luis	Gutiérrez	88100003	León	AUTO-0003	Detallista	2025-11-27
122	Daniela	Rivas	88100004	Granada	AUTO-0004	Detallista	2025-12-01
123	Fernando	Aguilar	88100005	Carazo	AUTO-0005	Mayorista	2025-12-05
124	Valeria	Vargas	88100006	Estelí	AUTO-0006	Detallista	2025-12-09
125	Miguel	Pineda	88100007	Chinandega	AUTO-0007	Detallista	2025-12-13
126	Andrea	Reyes	88100008	Rivas	AUTO-0008	Detallista	2025-12-17
127	Sergio	Campos	88100009	Matagalpa	AUTO-0009	Detallista	2025-12-21
128	Camila	Duarte	88100010	Jinotepe	AUTO-0010	Mayorista	2025-12-25
129	Eduardo	Flores	88100011	Managua	AUTO-0011	Detallista	2025-12-29
130	Natalia	Castro	88100012	Masaya	AUTO-0012	Detallista	2026-01-02
131	Bryan	Obando	88100013	León	AUTO-0013	Detallista	2026-01-06
132	Fernanda	Rivera	88100014	Granada	AUTO-0014	Detallista	2026-01-10
133	Héctor	Molina	88100015	Carazo	AUTO-0015	Mayorista	2026-01-14
134	Adriana	Chávez	88100016	Estelí	AUTO-0016	Detallista	2026-01-18
135	Samuel	Cruz	88100017	Chinandega	AUTO-0017	Detallista	2026-01-22
136	Isabel	López	88100018	Rivas	AUTO-0018	Detallista	2026-01-26
137	Ángel	Zamora	88100019	Matagalpa	AUTO-0019	Detallista	2026-01-30
138	Renata	Salinas	88100020	Jinotepe	AUTO-0020	Mayorista	2026-02-03
139	Roberto	Morales	88100021	Managua	AUTO-0021	Detallista	2026-02-07
140	Gabriela	Navarro	88100022	Masaya	AUTO-0022	Detallista	2026-02-11
141	Luis	Gutiérrez	88100023	León	AUTO-0023	Detallista	2026-02-15
142	Daniela	Rivas	88100024	Granada	AUTO-0024	Detallista	2026-02-19
143	Fernando	Aguilar	88100025	Carazo	AUTO-0025	Mayorista	2026-02-23
144	Valeria	Vargas	88100026	Estelí	AUTO-0026	Detallista	2026-02-27
145	Miguel	Pineda	88100027	Chinandega	AUTO-0027	Detallista	2026-03-03
146	Andrea	Reyes	88100028	Rivas	AUTO-0028	Detallista	2026-03-07
147	Sergio	Campos	88100029	Matagalpa	AUTO-0029	Detallista	2026-03-11
148	Camila	Duarte	88100030	Jinotepe	AUTO-0030	Mayorista	2026-03-15
149	Eduardo	Flores	88100031	Managua	AUTO-0031	Detallista	2026-03-19
150	Natalia	Castro	88100032	Masaya	AUTO-0032	Detallista	2026-03-23
151	Bryan	Obando	88100033	León	AUTO-0033	Detallista	2026-03-27
152	Fernanda	Rivera	88100034	Granada	AUTO-0034	Detallista	2026-03-31
153	Héctor	Molina	88100035	Carazo	AUTO-0035	Mayorista	2026-04-04
154	Cliente	Fugaz	\N	\N	FUGAZ	Detallista	2026-05-03
155	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista	2026-01-10
156	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista	2026-01-15
157	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista	2025-12-20
158	Karla	González	8855-1144	León	003-090599-0099L	Detallista	2026-02-01
159	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista	2026-02-05
160	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista	2025-11-18
161	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista	2026-03-01
162	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista	2026-03-05
163	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista	2025-10-10
164	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista	2026-03-10
165	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista	2026-03-15
166	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista	2025-09-25
167	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista	2026-03-20
168	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista	2026-03-22
169	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista	2025-08-15
170	Roberto	Morales	88100001	Managua	AUTO-0001	Detallista	2025-11-19
171	Gabriela	Navarro	88100002	Masaya	AUTO-0002	Detallista	2025-11-23
172	Luis	Gutiérrez	88100003	León	AUTO-0003	Detallista	2025-11-27
173	Daniela	Rivas	88100004	Granada	AUTO-0004	Detallista	2025-12-01
174	Fernando	Aguilar	88100005	Carazo	AUTO-0005	Mayorista	2025-12-05
175	Valeria	Vargas	88100006	Estelí	AUTO-0006	Detallista	2025-12-09
176	Miguel	Pineda	88100007	Chinandega	AUTO-0007	Detallista	2025-12-13
177	Andrea	Reyes	88100008	Rivas	AUTO-0008	Detallista	2025-12-17
178	Sergio	Campos	88100009	Matagalpa	AUTO-0009	Detallista	2025-12-21
179	Camila	Duarte	88100010	Jinotepe	AUTO-0010	Mayorista	2025-12-25
180	Eduardo	Flores	88100011	Managua	AUTO-0011	Detallista	2025-12-29
181	Natalia	Castro	88100012	Masaya	AUTO-0012	Detallista	2026-01-02
182	Bryan	Obando	88100013	León	AUTO-0013	Detallista	2026-01-06
183	Fernanda	Rivera	88100014	Granada	AUTO-0014	Detallista	2026-01-10
184	Héctor	Molina	88100015	Carazo	AUTO-0015	Mayorista	2026-01-14
185	Adriana	Chávez	88100016	Estelí	AUTO-0016	Detallista	2026-01-18
186	Samuel	Cruz	88100017	Chinandega	AUTO-0017	Detallista	2026-01-22
187	Isabel	López	88100018	Rivas	AUTO-0018	Detallista	2026-01-26
188	Ángel	Zamora	88100019	Matagalpa	AUTO-0019	Detallista	2026-01-30
189	Renata	Salinas	88100020	Jinotepe	AUTO-0020	Mayorista	2026-02-03
190	Roberto	Morales	88100021	Managua	AUTO-0021	Detallista	2026-02-07
191	Gabriela	Navarro	88100022	Masaya	AUTO-0022	Detallista	2026-02-11
192	Luis	Gutiérrez	88100023	León	AUTO-0023	Detallista	2026-02-15
193	Daniela	Rivas	88100024	Granada	AUTO-0024	Detallista	2026-02-19
194	Fernando	Aguilar	88100025	Carazo	AUTO-0025	Mayorista	2026-02-23
195	Valeria	Vargas	88100026	Estelí	AUTO-0026	Detallista	2026-02-27
196	Miguel	Pineda	88100027	Chinandega	AUTO-0027	Detallista	2026-03-03
197	Andrea	Reyes	88100028	Rivas	AUTO-0028	Detallista	2026-03-07
198	Sergio	Campos	88100029	Matagalpa	AUTO-0029	Detallista	2026-03-11
199	Camila	Duarte	88100030	Jinotepe	AUTO-0030	Mayorista	2026-03-15
200	Eduardo	Flores	88100031	Managua	AUTO-0031	Detallista	2026-03-19
201	Natalia	Castro	88100032	Masaya	AUTO-0032	Detallista	2026-03-23
202	Bryan	Obando	88100033	León	AUTO-0033	Detallista	2026-03-27
203	Fernanda	Rivera	88100034	Granada	AUTO-0034	Detallista	2026-03-31
204	Héctor	Molina	88100035	Carazo	AUTO-0035	Mayorista	2026-04-04
205	Cliente	Fugaz	\N	\N	FUGAZ	Detallista	2026-05-03
206	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista	2026-01-10
207	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista	2026-01-15
208	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista	2025-12-20
209	Karla	González	8855-1144	León	003-090599-0099L	Detallista	2026-02-01
210	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista	2026-02-05
211	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista	2025-11-18
212	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista	2026-03-01
213	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista	2026-03-05
214	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista	2025-10-10
215	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista	2026-03-10
216	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista	2026-03-15
217	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista	2025-09-25
218	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista	2026-03-20
219	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista	2026-03-22
220	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista	2025-08-15
221	Roberto	Morales	88100001	Managua	AUTO-0001	Detallista	2025-11-19
222	Gabriela	Navarro	88100002	Masaya	AUTO-0002	Detallista	2025-11-23
223	Luis	Gutiérrez	88100003	León	AUTO-0003	Detallista	2025-11-27
224	Daniela	Rivas	88100004	Granada	AUTO-0004	Detallista	2025-12-01
225	Fernando	Aguilar	88100005	Carazo	AUTO-0005	Mayorista	2025-12-05
226	Valeria	Vargas	88100006	Estelí	AUTO-0006	Detallista	2025-12-09
227	Miguel	Pineda	88100007	Chinandega	AUTO-0007	Detallista	2025-12-13
228	Andrea	Reyes	88100008	Rivas	AUTO-0008	Detallista	2025-12-17
229	Sergio	Campos	88100009	Matagalpa	AUTO-0009	Detallista	2025-12-21
230	Camila	Duarte	88100010	Jinotepe	AUTO-0010	Mayorista	2025-12-25
231	Eduardo	Flores	88100011	Managua	AUTO-0011	Detallista	2025-12-29
232	Natalia	Castro	88100012	Masaya	AUTO-0012	Detallista	2026-01-02
233	Bryan	Obando	88100013	León	AUTO-0013	Detallista	2026-01-06
234	Fernanda	Rivera	88100014	Granada	AUTO-0014	Detallista	2026-01-10
235	Héctor	Molina	88100015	Carazo	AUTO-0015	Mayorista	2026-01-14
236	Adriana	Chávez	88100016	Estelí	AUTO-0016	Detallista	2026-01-18
237	Samuel	Cruz	88100017	Chinandega	AUTO-0017	Detallista	2026-01-22
238	Isabel	López	88100018	Rivas	AUTO-0018	Detallista	2026-01-26
239	Ángel	Zamora	88100019	Matagalpa	AUTO-0019	Detallista	2026-01-30
240	Renata	Salinas	88100020	Jinotepe	AUTO-0020	Mayorista	2026-02-03
241	Roberto	Morales	88100021	Managua	AUTO-0021	Detallista	2026-02-07
242	Gabriela	Navarro	88100022	Masaya	AUTO-0022	Detallista	2026-02-11
243	Luis	Gutiérrez	88100023	León	AUTO-0023	Detallista	2026-02-15
244	Daniela	Rivas	88100024	Granada	AUTO-0024	Detallista	2026-02-19
245	Fernando	Aguilar	88100025	Carazo	AUTO-0025	Mayorista	2026-02-23
246	Valeria	Vargas	88100026	Estelí	AUTO-0026	Detallista	2026-02-27
247	Miguel	Pineda	88100027	Chinandega	AUTO-0027	Detallista	2026-03-03
248	Andrea	Reyes	88100028	Rivas	AUTO-0028	Detallista	2026-03-07
249	Sergio	Campos	88100029	Matagalpa	AUTO-0029	Detallista	2026-03-11
250	Camila	Duarte	88100030	Jinotepe	AUTO-0030	Mayorista	2026-03-15
251	Eduardo	Flores	88100031	Managua	AUTO-0031	Detallista	2026-03-19
252	Natalia	Castro	88100032	Masaya	AUTO-0032	Detallista	2026-03-23
253	Bryan	Obando	88100033	León	AUTO-0033	Detallista	2026-03-27
254	Fernanda	Rivera	88100034	Granada	AUTO-0034	Detallista	2026-03-31
255	Héctor	Molina	88100035	Carazo	AUTO-0035	Mayorista	2026-04-04
256	Cliente	Fugaz	\N	\N	FUGAZ	Detallista	2026-05-03
257	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista	2026-01-10
258	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista	2026-01-15
259	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista	2025-12-20
260	Karla	González	8855-1144	León	003-090599-0099L	Detallista	2026-02-01
261	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista	2026-02-05
262	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista	2025-11-18
263	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista	2026-03-01
264	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista	2026-03-05
265	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista	2025-10-10
266	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista	2026-03-10
267	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista	2026-03-15
268	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista	2025-09-25
269	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista	2026-03-20
270	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista	2026-03-22
271	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista	2025-08-15
272	Roberto	Morales	88100001	Managua	AUTO-0001	Detallista	2025-11-19
273	Gabriela	Navarro	88100002	Masaya	AUTO-0002	Detallista	2025-11-23
274	Luis	Gutiérrez	88100003	León	AUTO-0003	Detallista	2025-11-27
275	Daniela	Rivas	88100004	Granada	AUTO-0004	Detallista	2025-12-01
276	Fernando	Aguilar	88100005	Carazo	AUTO-0005	Mayorista	2025-12-05
277	Valeria	Vargas	88100006	Estelí	AUTO-0006	Detallista	2025-12-09
278	Miguel	Pineda	88100007	Chinandega	AUTO-0007	Detallista	2025-12-13
279	Andrea	Reyes	88100008	Rivas	AUTO-0008	Detallista	2025-12-17
280	Sergio	Campos	88100009	Matagalpa	AUTO-0009	Detallista	2025-12-21
281	Camila	Duarte	88100010	Jinotepe	AUTO-0010	Mayorista	2025-12-25
282	Eduardo	Flores	88100011	Managua	AUTO-0011	Detallista	2025-12-29
283	Natalia	Castro	88100012	Masaya	AUTO-0012	Detallista	2026-01-02
284	Bryan	Obando	88100013	León	AUTO-0013	Detallista	2026-01-06
285	Fernanda	Rivera	88100014	Granada	AUTO-0014	Detallista	2026-01-10
286	Héctor	Molina	88100015	Carazo	AUTO-0015	Mayorista	2026-01-14
287	Adriana	Chávez	88100016	Estelí	AUTO-0016	Detallista	2026-01-18
288	Samuel	Cruz	88100017	Chinandega	AUTO-0017	Detallista	2026-01-22
289	Isabel	López	88100018	Rivas	AUTO-0018	Detallista	2026-01-26
290	Ángel	Zamora	88100019	Matagalpa	AUTO-0019	Detallista	2026-01-30
291	Renata	Salinas	88100020	Jinotepe	AUTO-0020	Mayorista	2026-02-03
292	Roberto	Morales	88100021	Managua	AUTO-0021	Detallista	2026-02-07
293	Gabriela	Navarro	88100022	Masaya	AUTO-0022	Detallista	2026-02-11
294	Luis	Gutiérrez	88100023	León	AUTO-0023	Detallista	2026-02-15
295	Daniela	Rivas	88100024	Granada	AUTO-0024	Detallista	2026-02-19
296	Fernando	Aguilar	88100025	Carazo	AUTO-0025	Mayorista	2026-02-23
297	Valeria	Vargas	88100026	Estelí	AUTO-0026	Detallista	2026-02-27
298	Miguel	Pineda	88100027	Chinandega	AUTO-0027	Detallista	2026-03-03
299	Andrea	Reyes	88100028	Rivas	AUTO-0028	Detallista	2026-03-07
300	Sergio	Campos	88100029	Matagalpa	AUTO-0029	Detallista	2026-03-11
301	Camila	Duarte	88100030	Jinotepe	AUTO-0030	Mayorista	2026-03-15
302	Eduardo	Flores	88100031	Managua	AUTO-0031	Detallista	2026-03-19
303	Natalia	Castro	88100032	Masaya	AUTO-0032	Detallista	2026-03-23
304	Bryan	Obando	88100033	León	AUTO-0033	Detallista	2026-03-27
305	Fernanda	Rivera	88100034	Granada	AUTO-0034	Detallista	2026-03-31
306	Héctor	Molina	88100035	Carazo	AUTO-0035	Mayorista	2026-04-04
307	Cliente	Fugaz	\N	\N	FUGAZ	Detallista	2026-05-03
308	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista	2026-01-10
309	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista	2026-01-15
310	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista	2025-12-20
311	Karla	González	8855-1144	León	003-090599-0099L	Detallista	2026-02-01
312	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista	2026-02-05
313	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista	2025-11-18
314	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista	2026-03-01
315	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista	2026-03-05
316	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista	2025-10-10
317	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista	2026-03-10
318	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista	2026-03-15
319	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista	2025-09-25
320	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista	2026-03-20
321	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista	2026-03-22
322	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista	2025-08-15
323	Roberto	Morales	88100001	Managua	AUTO-0001	Detallista	2025-11-19
324	Gabriela	Navarro	88100002	Masaya	AUTO-0002	Detallista	2025-11-23
325	Luis	Gutiérrez	88100003	León	AUTO-0003	Detallista	2025-11-27
326	Daniela	Rivas	88100004	Granada	AUTO-0004	Detallista	2025-12-01
327	Fernando	Aguilar	88100005	Carazo	AUTO-0005	Mayorista	2025-12-05
328	Valeria	Vargas	88100006	Estelí	AUTO-0006	Detallista	2025-12-09
329	Miguel	Pineda	88100007	Chinandega	AUTO-0007	Detallista	2025-12-13
330	Andrea	Reyes	88100008	Rivas	AUTO-0008	Detallista	2025-12-17
331	Sergio	Campos	88100009	Matagalpa	AUTO-0009	Detallista	2025-12-21
332	Camila	Duarte	88100010	Jinotepe	AUTO-0010	Mayorista	2025-12-25
333	Eduardo	Flores	88100011	Managua	AUTO-0011	Detallista	2025-12-29
334	Natalia	Castro	88100012	Masaya	AUTO-0012	Detallista	2026-01-02
335	Bryan	Obando	88100013	León	AUTO-0013	Detallista	2026-01-06
336	Fernanda	Rivera	88100014	Granada	AUTO-0014	Detallista	2026-01-10
337	Héctor	Molina	88100015	Carazo	AUTO-0015	Mayorista	2026-01-14
338	Adriana	Chávez	88100016	Estelí	AUTO-0016	Detallista	2026-01-18
339	Samuel	Cruz	88100017	Chinandega	AUTO-0017	Detallista	2026-01-22
340	Isabel	López	88100018	Rivas	AUTO-0018	Detallista	2026-01-26
341	Ángel	Zamora	88100019	Matagalpa	AUTO-0019	Detallista	2026-01-30
342	Renata	Salinas	88100020	Jinotepe	AUTO-0020	Mayorista	2026-02-03
343	Roberto	Morales	88100021	Managua	AUTO-0021	Detallista	2026-02-07
344	Gabriela	Navarro	88100022	Masaya	AUTO-0022	Detallista	2026-02-11
345	Luis	Gutiérrez	88100023	León	AUTO-0023	Detallista	2026-02-15
346	Daniela	Rivas	88100024	Granada	AUTO-0024	Detallista	2026-02-19
347	Fernando	Aguilar	88100025	Carazo	AUTO-0025	Mayorista	2026-02-23
348	Valeria	Vargas	88100026	Estelí	AUTO-0026	Detallista	2026-02-27
349	Miguel	Pineda	88100027	Chinandega	AUTO-0027	Detallista	2026-03-03
350	Andrea	Reyes	88100028	Rivas	AUTO-0028	Detallista	2026-03-07
351	Sergio	Campos	88100029	Matagalpa	AUTO-0029	Detallista	2026-03-11
352	Camila	Duarte	88100030	Jinotepe	AUTO-0030	Mayorista	2026-03-15
353	Eduardo	Flores	88100031	Managua	AUTO-0031	Detallista	2026-03-19
354	Natalia	Castro	88100032	Masaya	AUTO-0032	Detallista	2026-03-23
355	Bryan	Obando	88100033	León	AUTO-0033	Detallista	2026-03-27
356	Fernanda	Rivera	88100034	Granada	AUTO-0034	Detallista	2026-03-31
357	Héctor	Molina	88100035	Carazo	AUTO-0035	Mayorista	2026-04-04
\.


--
-- Data for Name: compra; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.compra (id_compra, fecha, id_proveedor, id_usuario, total) FROM stdin;
1	2025-11-26 08:12:35	1	1	4000.00
2	2025-11-26 13:48:10	2	26	1800.00
3	2025-11-26 18:27:54	3	27	2500.00
4	2025-11-27 09:15:22	1	1	5200.00
5	2025-11-27 14:05:10	5	26	3100.00
6	2025-11-28 10:45:55	6	27	2750.00
7	2025-12-04 10:00:00	1	4	515.00
8	2025-12-07 11:00:00	2	5	1450.00
9	2025-12-10 12:00:00	3	6	2140.00
10	2025-12-13 13:00:00	4	7	1383.00
11	2025-12-16 14:00:00	5	8	6160.00
12	2025-12-19 09:00:00	6	9	2345.00
13	2025-12-22 10:00:00	1	10	2962.00
14	2025-12-25 11:00:00	2	11	6380.00
15	2025-12-28 12:00:00	3	12	5485.00
16	2025-12-31 13:00:00	4	13	4625.00
17	2026-01-03 14:00:00	5	14	3655.00
18	2026-01-06 09:00:00	6	15	4584.00
19	2026-01-09 10:00:00	1	16	7080.00
20	2026-01-12 11:00:00	2	17	6884.00
21	2026-01-15 12:00:00	3	18	1497.00
22	2026-01-18 13:00:00	4	19	3940.00
23	2026-01-21 14:00:00	5	20	5040.00
24	2026-01-24 09:00:00	6	21	1188.00
25	2026-01-27 10:00:00	1	22	1280.00
26	2026-01-30 11:00:00	2	23	351.00
27	2026-02-02 12:00:00	3	24	1590.00
28	2026-02-05 13:00:00	4	25	1795.00
29	2026-02-08 14:00:00	5	26	5120.00
30	2026-02-11 09:00:00	6	27	3090.00
31	2026-02-14 10:00:00	1	28	735.00
32	2026-02-17 11:00:00	2	29	1505.00
33	2026-02-20 12:00:00	3	30	2615.00
34	2026-02-23 13:00:00	4	1	7300.00
35	2026-02-26 14:00:00	5	2	3510.00
36	2026-03-01 09:00:00	6	3	5760.00
37	2026-03-04 10:00:00	1	4	1165.00
38	2026-03-07 11:00:00	2	5	3150.00
39	2026-03-10 12:00:00	3	6	4440.00
40	2026-03-13 13:00:00	4	7	2763.00
41	2026-03-16 14:00:00	5	8	11560.00
42	2025-11-26 08:12:35	1	1	4000.00
43	2025-11-26 13:48:10	2	26	1800.00
44	2025-11-26 18:27:54	3	27	2500.00
45	2025-11-27 09:15:22	1	1	5200.00
46	2025-11-27 14:05:10	5	26	3100.00
47	2025-11-28 10:45:55	6	27	2750.00
48	2025-12-04 10:00:00	1	4	515.00
49	2025-12-07 11:00:00	2	5	1450.00
50	2025-12-10 12:00:00	3	6	2140.00
51	2025-12-13 13:00:00	4	7	1383.00
52	2025-12-16 14:00:00	5	8	6160.00
53	2025-12-19 09:00:00	6	9	2345.00
54	2025-12-22 10:00:00	7	10	2962.00
55	2025-12-25 11:00:00	8	11	6380.00
56	2025-12-28 12:00:00	9	12	5485.00
57	2025-12-31 13:00:00	10	13	4625.00
58	2026-01-03 14:00:00	11	14	3655.00
59	2026-01-06 09:00:00	12	15	4584.00
60	2026-01-09 10:00:00	1	16	7080.00
61	2026-01-12 11:00:00	2	17	6884.00
62	2026-01-15 12:00:00	3	18	1497.00
63	2026-01-18 13:00:00	4	19	3940.00
64	2026-01-21 14:00:00	5	20	5040.00
65	2026-01-24 09:00:00	6	21	1188.00
66	2026-01-27 10:00:00	7	22	1280.00
67	2026-01-30 11:00:00	8	23	351.00
68	2026-02-02 12:00:00	9	24	1590.00
69	2026-02-05 13:00:00	10	25	1795.00
70	2026-02-08 14:00:00	11	26	5120.00
71	2026-02-11 09:00:00	12	27	3090.00
72	2026-02-14 10:00:00	1	28	735.00
73	2026-02-17 11:00:00	2	29	1505.00
74	2026-02-20 12:00:00	3	30	2615.00
75	2026-02-23 13:00:00	4	1	7300.00
76	2026-02-26 14:00:00	5	2	3510.00
77	2026-03-01 09:00:00	6	3	5760.00
78	2026-03-04 10:00:00	7	4	1165.00
79	2026-03-07 11:00:00	8	5	3150.00
80	2026-03-10 12:00:00	9	6	4440.00
81	2026-03-13 13:00:00	10	7	2763.00
82	2026-03-16 14:00:00	11	8	11560.00
83	2025-11-26 08:12:35	1	1	4000.00
84	2025-11-26 13:48:10	2	26	1800.00
85	2025-11-26 18:27:54	3	27	2500.00
86	2025-11-27 09:15:22	1	1	5200.00
87	2025-11-27 14:05:10	5	26	3100.00
88	2025-11-28 10:45:55	6	27	2750.00
89	2025-12-04 10:00:00	1	4	515.00
90	2025-12-07 11:00:00	2	5	1450.00
91	2025-12-10 12:00:00	3	6	2140.00
92	2025-12-13 13:00:00	4	7	1383.00
93	2025-12-16 14:00:00	5	8	6160.00
94	2025-12-19 09:00:00	6	9	2345.00
95	2025-12-22 10:00:00	7	10	2962.00
96	2025-12-25 11:00:00	8	11	6380.00
97	2025-12-28 12:00:00	9	12	5485.00
98	2025-12-31 13:00:00	10	13	4625.00
99	2026-01-03 14:00:00	11	14	3655.00
100	2026-01-06 09:00:00	12	15	4584.00
101	2026-01-09 10:00:00	13	16	7080.00
102	2026-01-12 11:00:00	14	17	6884.00
103	2026-01-15 12:00:00	15	18	1497.00
104	2026-01-18 13:00:00	16	19	3940.00
105	2026-01-21 14:00:00	17	20	5040.00
106	2026-01-24 09:00:00	18	21	1188.00
107	2026-01-27 10:00:00	1	22	1280.00
108	2026-01-30 11:00:00	2	23	351.00
109	2026-02-02 12:00:00	3	24	1590.00
110	2026-02-05 13:00:00	4	25	1795.00
111	2026-02-08 14:00:00	5	26	5120.00
112	2026-02-11 09:00:00	6	27	3090.00
113	2026-02-14 10:00:00	7	28	735.00
114	2026-02-17 11:00:00	8	29	1505.00
115	2026-02-20 12:00:00	9	30	2615.00
116	2026-02-23 13:00:00	10	1	7300.00
117	2026-02-26 14:00:00	11	2	3510.00
118	2026-03-01 09:00:00	12	3	5760.00
119	2026-03-04 10:00:00	13	4	1165.00
120	2026-03-07 11:00:00	14	5	3150.00
121	2026-03-10 12:00:00	15	6	4440.00
122	2026-03-13 13:00:00	16	7	2763.00
123	2026-03-16 14:00:00	17	8	11560.00
124	2025-11-26 08:12:35	1	1	4000.00
125	2025-11-26 13:48:10	2	26	1800.00
126	2025-11-26 18:27:54	3	27	2500.00
127	2025-11-27 09:15:22	1	1	5200.00
128	2025-11-27 14:05:10	5	26	3100.00
129	2025-11-28 10:45:55	6	27	2750.00
130	2025-12-04 10:00:00	1	4	515.00
131	2025-12-07 11:00:00	2	5	1450.00
132	2025-12-10 12:00:00	3	6	2140.00
133	2025-12-13 13:00:00	4	7	1383.00
134	2025-12-16 14:00:00	5	8	6160.00
135	2025-12-19 09:00:00	6	9	2345.00
136	2025-12-22 10:00:00	7	10	2962.00
137	2025-12-25 11:00:00	8	11	6380.00
138	2025-12-28 12:00:00	9	12	5485.00
139	2025-12-31 13:00:00	10	13	4625.00
140	2026-01-03 14:00:00	11	14	3655.00
141	2026-01-06 09:00:00	12	15	4584.00
142	2026-01-09 10:00:00	13	16	7080.00
143	2026-01-12 11:00:00	14	17	6884.00
144	2026-01-15 12:00:00	15	18	1497.00
145	2026-01-18 13:00:00	16	19	3940.00
146	2026-01-21 14:00:00	17	20	5040.00
147	2026-01-24 09:00:00	18	21	1188.00
148	2026-01-27 10:00:00	19	22	1280.00
149	2026-01-30 11:00:00	20	23	351.00
150	2026-02-02 12:00:00	21	24	1590.00
151	2026-02-05 13:00:00	22	25	1795.00
152	2026-02-08 14:00:00	23	26	5120.00
153	2026-02-11 09:00:00	24	27	3090.00
154	2026-02-14 10:00:00	25	28	735.00
155	2026-02-17 11:00:00	1	29	1505.00
156	2026-02-20 12:00:00	2	30	2615.00
157	2026-02-23 13:00:00	3	1	7300.00
158	2026-02-26 14:00:00	4	2	3510.00
159	2026-03-01 09:00:00	5	3	5760.00
160	2026-03-04 10:00:00	6	4	1165.00
161	2026-03-07 11:00:00	7	5	3150.00
162	2026-03-10 12:00:00	8	6	4440.00
163	2026-03-13 13:00:00	9	7	2763.00
164	2026-03-16 14:00:00	10	8	11560.00
165	2025-11-26 08:12:35	1	1	4000.00
166	2025-11-26 13:48:10	2	26	1800.00
167	2025-11-26 18:27:54	3	27	2500.00
168	2025-11-27 09:15:22	1	1	5200.00
169	2025-11-27 14:05:10	5	26	3100.00
170	2025-11-28 10:45:55	6	27	2750.00
171	2025-12-04 10:00:00	1	4	515.00
172	2025-12-07 11:00:00	2	5	1450.00
173	2025-12-10 12:00:00	3	6	2140.00
174	2025-12-13 13:00:00	4	7	1383.00
175	2025-12-16 14:00:00	5	8	6160.00
176	2025-12-19 09:00:00	6	9	2345.00
177	2025-12-22 10:00:00	7	10	2962.00
178	2025-12-25 11:00:00	8	11	6380.00
179	2025-12-28 12:00:00	9	12	5485.00
180	2025-12-31 13:00:00	10	13	4625.00
181	2026-01-03 14:00:00	11	14	3655.00
182	2026-01-06 09:00:00	12	15	4584.00
183	2026-01-09 10:00:00	13	16	7080.00
184	2026-01-12 11:00:00	14	17	6884.00
185	2026-01-15 12:00:00	15	18	1497.00
186	2026-01-18 13:00:00	16	19	3940.00
187	2026-01-21 14:00:00	17	20	5040.00
188	2026-01-24 09:00:00	18	21	1188.00
189	2026-01-27 10:00:00	19	22	1280.00
190	2026-01-30 11:00:00	20	23	351.00
191	2026-02-02 12:00:00	21	24	1590.00
192	2026-02-05 13:00:00	22	25	1795.00
193	2026-02-08 14:00:00	23	26	5120.00
194	2026-02-11 09:00:00	24	27	3090.00
195	2026-02-14 10:00:00	25	28	735.00
196	2026-02-17 11:00:00	26	29	1505.00
197	2026-02-20 12:00:00	27	30	2615.00
198	2026-02-23 13:00:00	28	1	7300.00
199	2026-02-26 14:00:00	29	2	3510.00
200	2026-03-01 09:00:00	30	3	5760.00
201	2026-03-04 10:00:00	31	4	1165.00
202	2026-03-07 11:00:00	1	5	3150.00
203	2026-03-10 12:00:00	2	6	4440.00
204	2026-03-13 13:00:00	3	7	2763.00
205	2026-03-16 14:00:00	4	8	11560.00
206	2025-11-26 08:12:35	1	1	4000.00
207	2025-11-26 13:48:10	2	26	1800.00
208	2025-11-26 18:27:54	3	27	2500.00
209	2025-11-27 09:15:22	1	1	5200.00
210	2025-11-27 14:05:10	5	26	3100.00
211	2025-11-28 10:45:55	6	27	2750.00
212	2025-12-04 10:00:00	1	4	515.00
213	2025-12-07 11:00:00	2	5	1450.00
214	2025-12-10 12:00:00	3	6	2140.00
215	2025-12-13 13:00:00	4	7	1383.00
216	2025-12-16 14:00:00	5	8	6160.00
217	2025-12-19 09:00:00	6	9	2345.00
218	2025-12-22 10:00:00	7	10	2962.00
219	2025-12-25 11:00:00	8	11	6380.00
220	2025-12-28 12:00:00	9	12	5485.00
221	2025-12-31 13:00:00	10	13	4625.00
222	2026-01-03 14:00:00	11	14	3655.00
223	2026-01-06 09:00:00	12	15	4584.00
224	2026-01-09 10:00:00	13	16	7080.00
225	2026-01-12 11:00:00	14	17	6884.00
226	2026-01-15 12:00:00	15	18	1497.00
227	2026-01-18 13:00:00	16	19	3940.00
228	2026-01-21 14:00:00	17	20	5040.00
229	2026-01-24 09:00:00	18	21	1188.00
230	2026-01-27 10:00:00	19	22	1280.00
231	2026-01-30 11:00:00	20	23	351.00
232	2026-02-02 12:00:00	21	24	1590.00
233	2026-02-05 13:00:00	22	25	1795.00
234	2026-02-08 14:00:00	23	26	5120.00
235	2026-02-11 09:00:00	24	27	3090.00
236	2026-02-14 10:00:00	25	28	735.00
237	2026-02-17 11:00:00	26	29	1505.00
238	2026-02-20 12:00:00	27	30	2615.00
239	2026-02-23 13:00:00	28	1	7300.00
240	2026-02-26 14:00:00	29	2	3510.00
241	2026-03-01 09:00:00	30	3	5760.00
242	2026-03-04 10:00:00	31	4	1165.00
243	2026-03-07 11:00:00	32	5	3150.00
244	2026-03-10 12:00:00	33	6	4440.00
245	2026-03-13 13:00:00	34	7	2763.00
246	2026-03-16 14:00:00	35	8	11560.00
247	2025-11-26 08:12:35	1	1	4000.00
248	2025-11-26 13:48:10	2	26	1800.00
249	2025-11-26 18:27:54	3	27	2500.00
250	2025-11-27 09:15:22	1	1	5200.00
251	2025-11-27 14:05:10	5	26	3100.00
252	2025-11-28 10:45:55	6	27	2750.00
253	2025-12-04 10:00:00	1	4	515.00
254	2025-12-07 11:00:00	2	5	1450.00
255	2025-12-10 12:00:00	3	6	2140.00
256	2025-12-13 13:00:00	4	7	1383.00
257	2025-12-16 14:00:00	5	8	6160.00
258	2025-12-19 09:00:00	6	9	2345.00
259	2025-12-22 10:00:00	7	10	2962.00
260	2025-12-25 11:00:00	8	11	6380.00
261	2025-12-28 12:00:00	9	12	5485.00
262	2025-12-31 13:00:00	10	13	4625.00
263	2026-01-03 14:00:00	11	14	3655.00
264	2026-01-06 09:00:00	12	15	4584.00
265	2026-01-09 10:00:00	13	16	7080.00
266	2026-01-12 11:00:00	14	17	6884.00
267	2026-01-15 12:00:00	15	18	1497.00
268	2026-01-18 13:00:00	16	19	3940.00
269	2026-01-21 14:00:00	17	20	5040.00
270	2026-01-24 09:00:00	18	21	1188.00
271	2026-01-27 10:00:00	19	22	1280.00
272	2026-01-30 11:00:00	20	23	351.00
273	2026-02-02 12:00:00	21	24	1590.00
274	2026-02-05 13:00:00	22	25	1795.00
275	2026-02-08 14:00:00	23	26	5120.00
276	2026-02-11 09:00:00	24	27	3090.00
277	2026-02-14 10:00:00	25	28	735.00
278	2026-02-17 11:00:00	26	29	1505.00
279	2026-02-20 12:00:00	27	30	2615.00
280	2026-02-23 13:00:00	28	1	7300.00
281	2026-02-26 14:00:00	29	2	3510.00
282	2026-03-01 09:00:00	30	3	5760.00
283	2026-03-04 10:00:00	31	4	1165.00
284	2026-03-07 11:00:00	32	5	3150.00
285	2026-03-10 12:00:00	33	6	4440.00
286	2026-03-13 13:00:00	34	7	2763.00
287	2026-03-16 14:00:00	35	8	11560.00
\.


--
-- Data for Name: detallecompra; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.detallecompra (id_detalle, id_compra, id_producto, cantidad, costo_unitario, total_linea) FROM stdin;
1	1	1	30	120.00	3600.00
2	1	2	20	115.00	2300.00
3	2	4	20	90.00	1800.00
4	3	5	80	20.00	1600.00
5	3	6	80	5.00	400.00
6	4	9	40	135.00	5400.00
7	4	10	30	320.00	9600.00
8	5	11	60	60.00	3600.00
9	5	14	300	3.00	900.00
10	6	13	40	70.00	2800.00
11	6	15	25	220.00	5500.00
12	7	6	7	5.00	35.00
13	7	11	8	60.00	480.00
14	8	7	8	80.00	640.00
15	8	12	9	90.00	810.00
16	9	8	9	160.00	1440.00
17	9	13	10	70.00	700.00
18	10	9	10	135.00	1350.00
19	10	14	11	3.00	33.00
20	11	10	11	320.00	3520.00
21	11	15	12	220.00	2640.00
22	12	11	12	60.00	720.00
23	12	16	13	125.00	1625.00
24	13	12	13	90.00	1170.00
25	13	17	14	128.00	1792.00
26	14	13	14	70.00	980.00
27	14	18	15	360.00	5400.00
28	15	14	15	3.00	45.00
29	15	19	16	340.00	5440.00
30	16	15	16	220.00	3520.00
31	16	20	17	65.00	1105.00
32	17	16	17	125.00	2125.00
33	17	21	18	85.00	1530.00
34	18	17	18	128.00	2304.00
35	18	22	19	120.00	2280.00
36	19	18	19	360.00	6840.00
37	19	23	20	12.00	240.00
38	20	19	20	340.00	6800.00
39	20	24	21	4.00	84.00
40	21	20	21	65.00	1365.00
41	21	25	22	6.00	132.00
42	22	21	22	85.00	1870.00
43	22	26	23	90.00	2070.00
44	23	22	23	120.00	2760.00
45	23	27	24	95.00	2280.00
46	24	23	24	12.00	288.00
47	24	28	5	180.00	900.00
48	25	24	5	4.00	20.00
49	25	29	6	210.00	1260.00
50	26	25	6	6.00	36.00
51	26	30	7	45.00	315.00
52	27	26	7	90.00	630.00
53	27	1	8	120.00	960.00
54	28	27	8	95.00	760.00
55	28	2	9	115.00	1035.00
56	29	28	9	180.00	1620.00
57	29	3	10	350.00	3500.00
58	30	29	10	210.00	2100.00
59	30	4	11	90.00	990.00
60	31	30	11	45.00	495.00
61	31	5	12	20.00	240.00
62	32	1	12	120.00	1440.00
63	32	6	13	5.00	65.00
64	33	2	13	115.00	1495.00
65	33	7	14	80.00	1120.00
66	34	3	14	350.00	4900.00
67	34	8	15	160.00	2400.00
68	35	4	15	90.00	1350.00
69	35	9	16	135.00	2160.00
70	36	5	16	20.00	320.00
71	36	10	17	320.00	5440.00
72	37	6	17	5.00	85.00
73	37	11	18	60.00	1080.00
74	38	7	18	80.00	1440.00
75	38	12	19	90.00	1710.00
76	39	8	19	160.00	3040.00
77	39	13	20	70.00	1400.00
78	40	9	20	135.00	2700.00
79	40	14	21	3.00	63.00
80	41	10	21	320.00	6720.00
81	41	15	22	220.00	4840.00
82	1	1	30	120.00	3600.00
83	1	2	20	115.00	2300.00
84	2	4	20	90.00	1800.00
85	3	5	80	20.00	1600.00
86	3	6	80	5.00	400.00
87	4	9	40	135.00	5400.00
88	4	10	30	320.00	9600.00
89	5	11	60	60.00	3600.00
90	5	14	300	3.00	900.00
91	6	13	40	70.00	2800.00
92	6	15	25	220.00	5500.00
93	48	6	7	5.00	35.00
94	48	11	8	60.00	480.00
95	49	7	8	80.00	640.00
96	49	12	9	90.00	810.00
97	50	8	9	160.00	1440.00
98	50	13	10	70.00	700.00
99	51	9	10	135.00	1350.00
100	51	14	11	3.00	33.00
101	52	10	11	320.00	3520.00
102	52	15	12	220.00	2640.00
103	53	11	12	60.00	720.00
104	53	16	13	125.00	1625.00
105	54	12	13	90.00	1170.00
106	54	17	14	128.00	1792.00
107	55	13	14	70.00	980.00
108	55	18	15	360.00	5400.00
109	56	14	15	3.00	45.00
110	56	19	16	340.00	5440.00
111	57	15	16	220.00	3520.00
112	57	20	17	65.00	1105.00
113	58	16	17	125.00	2125.00
114	58	21	18	85.00	1530.00
115	59	17	18	128.00	2304.00
116	59	22	19	120.00	2280.00
117	60	18	19	360.00	6840.00
118	60	23	20	12.00	240.00
119	61	19	20	340.00	6800.00
120	61	24	21	4.00	84.00
121	62	20	21	65.00	1365.00
122	62	25	22	6.00	132.00
123	63	21	22	85.00	1870.00
124	63	26	23	90.00	2070.00
125	64	22	23	120.00	2760.00
126	64	27	24	95.00	2280.00
127	65	23	24	12.00	288.00
128	65	28	5	180.00	900.00
129	66	24	5	4.00	20.00
130	66	29	6	210.00	1260.00
131	67	25	6	6.00	36.00
132	67	30	7	45.00	315.00
133	68	26	7	90.00	630.00
134	68	1	8	120.00	960.00
135	69	27	8	95.00	760.00
136	69	2	9	115.00	1035.00
137	70	28	9	180.00	1620.00
138	70	3	10	350.00	3500.00
139	71	29	10	210.00	2100.00
140	71	4	11	90.00	990.00
141	72	30	11	45.00	495.00
142	72	5	12	20.00	240.00
143	73	1	12	120.00	1440.00
144	73	6	13	5.00	65.00
145	74	2	13	115.00	1495.00
146	74	7	14	80.00	1120.00
147	75	3	14	350.00	4900.00
148	75	8	15	160.00	2400.00
149	76	4	15	90.00	1350.00
150	76	9	16	135.00	2160.00
151	77	5	16	20.00	320.00
152	77	10	17	320.00	5440.00
153	78	6	17	5.00	85.00
154	78	11	18	60.00	1080.00
155	79	7	18	80.00	1440.00
156	79	12	19	90.00	1710.00
157	80	8	19	160.00	3040.00
158	80	13	20	70.00	1400.00
159	81	9	20	135.00	2700.00
160	81	14	21	3.00	63.00
161	82	10	21	320.00	6720.00
162	82	15	22	220.00	4840.00
163	1	1	30	120.00	3600.00
164	1	2	20	115.00	2300.00
165	2	4	20	90.00	1800.00
166	3	5	80	20.00	1600.00
167	3	6	80	5.00	400.00
168	4	9	40	135.00	5400.00
169	4	10	30	320.00	9600.00
170	5	11	60	60.00	3600.00
171	5	14	300	3.00	900.00
172	6	13	40	70.00	2800.00
173	6	15	25	220.00	5500.00
174	89	6	7	5.00	35.00
175	89	11	8	60.00	480.00
176	90	7	8	80.00	640.00
177	90	12	9	90.00	810.00
178	91	8	9	160.00	1440.00
179	91	13	10	70.00	700.00
180	92	9	10	135.00	1350.00
181	92	14	11	3.00	33.00
182	93	10	11	320.00	3520.00
183	93	15	12	220.00	2640.00
184	94	11	12	60.00	720.00
185	94	16	13	125.00	1625.00
186	95	12	13	90.00	1170.00
187	95	17	14	128.00	1792.00
188	96	13	14	70.00	980.00
189	96	18	15	360.00	5400.00
190	97	14	15	3.00	45.00
191	97	19	16	340.00	5440.00
192	98	15	16	220.00	3520.00
193	98	20	17	65.00	1105.00
194	99	16	17	125.00	2125.00
195	99	21	18	85.00	1530.00
196	100	17	18	128.00	2304.00
197	100	22	19	120.00	2280.00
198	101	18	19	360.00	6840.00
199	101	23	20	12.00	240.00
200	102	19	20	340.00	6800.00
201	102	24	21	4.00	84.00
202	103	20	21	65.00	1365.00
203	103	25	22	6.00	132.00
204	104	21	22	85.00	1870.00
205	104	26	23	90.00	2070.00
206	105	22	23	120.00	2760.00
207	105	27	24	95.00	2280.00
208	106	23	24	12.00	288.00
209	106	28	5	180.00	900.00
210	107	24	5	4.00	20.00
211	107	29	6	210.00	1260.00
212	108	25	6	6.00	36.00
213	108	30	7	45.00	315.00
214	109	26	7	90.00	630.00
215	109	1	8	120.00	960.00
216	110	27	8	95.00	760.00
217	110	2	9	115.00	1035.00
218	111	28	9	180.00	1620.00
219	111	3	10	350.00	3500.00
220	112	29	10	210.00	2100.00
221	112	4	11	90.00	990.00
222	113	30	11	45.00	495.00
223	113	5	12	20.00	240.00
224	114	1	12	120.00	1440.00
225	114	6	13	5.00	65.00
226	115	2	13	115.00	1495.00
227	115	7	14	80.00	1120.00
228	116	3	14	350.00	4900.00
229	116	8	15	160.00	2400.00
230	117	4	15	90.00	1350.00
231	117	9	16	135.00	2160.00
232	118	5	16	20.00	320.00
233	118	10	17	320.00	5440.00
234	119	6	17	5.00	85.00
235	119	11	18	60.00	1080.00
236	120	7	18	80.00	1440.00
237	120	12	19	90.00	1710.00
238	121	8	19	160.00	3040.00
239	121	13	20	70.00	1400.00
240	122	9	20	135.00	2700.00
241	122	14	21	3.00	63.00
242	123	10	21	320.00	6720.00
243	123	15	22	220.00	4840.00
244	1	1	30	120.00	3600.00
245	1	2	20	115.00	2300.00
246	2	4	20	90.00	1800.00
247	3	5	80	20.00	1600.00
248	3	6	80	5.00	400.00
249	4	9	40	135.00	5400.00
250	4	10	30	320.00	9600.00
251	5	11	60	60.00	3600.00
252	5	14	300	3.00	900.00
253	6	13	40	70.00	2800.00
254	6	15	25	220.00	5500.00
255	130	6	7	5.00	35.00
256	130	11	8	60.00	480.00
257	131	7	8	80.00	640.00
258	131	12	9	90.00	810.00
259	132	8	9	160.00	1440.00
260	132	13	10	70.00	700.00
261	133	9	10	135.00	1350.00
262	133	14	11	3.00	33.00
263	134	10	11	320.00	3520.00
264	134	15	12	220.00	2640.00
265	135	11	12	60.00	720.00
266	135	16	13	125.00	1625.00
267	136	12	13	90.00	1170.00
268	136	17	14	128.00	1792.00
269	137	13	14	70.00	980.00
270	137	18	15	360.00	5400.00
271	138	14	15	3.00	45.00
272	138	19	16	340.00	5440.00
273	139	15	16	220.00	3520.00
274	139	20	17	65.00	1105.00
275	140	16	17	125.00	2125.00
276	140	21	18	85.00	1530.00
277	141	17	18	128.00	2304.00
278	141	22	19	120.00	2280.00
279	142	18	19	360.00	6840.00
280	142	23	20	12.00	240.00
281	143	19	20	340.00	6800.00
282	143	24	21	4.00	84.00
283	144	20	21	65.00	1365.00
284	144	25	22	6.00	132.00
285	145	21	22	85.00	1870.00
286	145	26	23	90.00	2070.00
287	146	22	23	120.00	2760.00
288	146	27	24	95.00	2280.00
289	147	23	24	12.00	288.00
290	147	28	5	180.00	900.00
291	148	24	5	4.00	20.00
292	148	29	6	210.00	1260.00
293	149	25	6	6.00	36.00
294	149	30	7	45.00	315.00
295	150	26	7	90.00	630.00
296	150	1	8	120.00	960.00
297	151	27	8	95.00	760.00
298	151	2	9	115.00	1035.00
299	152	28	9	180.00	1620.00
300	152	3	10	350.00	3500.00
301	153	29	10	210.00	2100.00
302	153	4	11	90.00	990.00
303	154	30	11	45.00	495.00
304	154	5	12	20.00	240.00
305	155	1	12	120.00	1440.00
306	155	6	13	5.00	65.00
307	156	2	13	115.00	1495.00
308	156	7	14	80.00	1120.00
309	157	3	14	350.00	4900.00
310	157	8	15	160.00	2400.00
311	158	4	15	90.00	1350.00
312	158	9	16	135.00	2160.00
313	159	5	16	20.00	320.00
314	159	10	17	320.00	5440.00
315	160	6	17	5.00	85.00
316	160	11	18	60.00	1080.00
317	161	7	18	80.00	1440.00
318	161	12	19	90.00	1710.00
319	162	8	19	160.00	3040.00
320	162	13	20	70.00	1400.00
321	163	9	20	135.00	2700.00
322	163	14	21	3.00	63.00
323	164	10	21	320.00	6720.00
324	164	15	22	220.00	4840.00
325	1	1	30	120.00	3600.00
326	1	2	20	115.00	2300.00
327	2	4	20	90.00	1800.00
328	3	5	80	20.00	1600.00
329	3	6	80	5.00	400.00
330	4	9	40	135.00	5400.00
331	4	10	30	320.00	9600.00
332	5	11	60	60.00	3600.00
333	5	14	300	3.00	900.00
334	6	13	40	70.00	2800.00
335	6	15	25	220.00	5500.00
336	171	6	7	5.00	35.00
337	171	11	8	60.00	480.00
338	172	7	8	80.00	640.00
339	172	12	9	90.00	810.00
340	173	8	9	160.00	1440.00
341	173	13	10	70.00	700.00
342	174	9	10	135.00	1350.00
343	174	14	11	3.00	33.00
344	175	10	11	320.00	3520.00
345	175	15	12	220.00	2640.00
346	176	11	12	60.00	720.00
347	176	16	13	125.00	1625.00
348	177	12	13	90.00	1170.00
349	177	17	14	128.00	1792.00
350	178	13	14	70.00	980.00
351	178	18	15	360.00	5400.00
352	179	14	15	3.00	45.00
353	179	19	16	340.00	5440.00
354	180	15	16	220.00	3520.00
355	180	20	17	65.00	1105.00
356	181	16	17	125.00	2125.00
357	181	21	18	85.00	1530.00
358	182	17	18	128.00	2304.00
359	182	22	19	120.00	2280.00
360	183	18	19	360.00	6840.00
361	183	23	20	12.00	240.00
362	184	19	20	340.00	6800.00
363	184	24	21	4.00	84.00
364	185	20	21	65.00	1365.00
365	185	25	22	6.00	132.00
366	186	21	22	85.00	1870.00
367	186	26	23	90.00	2070.00
368	187	22	23	120.00	2760.00
369	187	27	24	95.00	2280.00
370	188	23	24	12.00	288.00
371	188	28	5	180.00	900.00
372	189	24	5	4.00	20.00
373	189	29	6	210.00	1260.00
374	190	25	6	6.00	36.00
375	190	30	7	45.00	315.00
376	191	26	7	90.00	630.00
377	191	1	8	120.00	960.00
378	192	27	8	95.00	760.00
379	192	2	9	115.00	1035.00
380	193	28	9	180.00	1620.00
381	193	3	10	350.00	3500.00
382	194	29	10	210.00	2100.00
383	194	4	11	90.00	990.00
384	195	30	11	45.00	495.00
385	195	5	12	20.00	240.00
386	196	1	12	120.00	1440.00
387	196	6	13	5.00	65.00
388	197	2	13	115.00	1495.00
389	197	7	14	80.00	1120.00
390	198	3	14	350.00	4900.00
391	198	8	15	160.00	2400.00
392	199	4	15	90.00	1350.00
393	199	9	16	135.00	2160.00
394	200	5	16	20.00	320.00
395	200	10	17	320.00	5440.00
396	201	6	17	5.00	85.00
397	201	11	18	60.00	1080.00
398	202	7	18	80.00	1440.00
399	202	12	19	90.00	1710.00
400	203	8	19	160.00	3040.00
401	203	13	20	70.00	1400.00
402	204	9	20	135.00	2700.00
403	204	14	21	3.00	63.00
404	205	10	21	320.00	6720.00
405	205	15	22	220.00	4840.00
406	1	1	30	120.00	3600.00
407	1	2	20	115.00	2300.00
408	2	4	20	90.00	1800.00
409	3	5	80	20.00	1600.00
410	3	6	80	5.00	400.00
411	4	9	40	135.00	5400.00
412	4	10	30	320.00	9600.00
413	5	11	60	60.00	3600.00
414	5	14	300	3.00	900.00
415	6	13	40	70.00	2800.00
416	6	15	25	220.00	5500.00
417	212	6	7	5.00	35.00
418	212	11	8	60.00	480.00
419	213	7	8	80.00	640.00
420	213	12	9	90.00	810.00
421	214	8	9	160.00	1440.00
422	214	13	10	70.00	700.00
423	215	9	10	135.00	1350.00
424	215	14	11	3.00	33.00
425	216	10	11	320.00	3520.00
426	216	15	12	220.00	2640.00
427	217	11	12	60.00	720.00
428	217	16	13	125.00	1625.00
429	218	12	13	90.00	1170.00
430	218	17	14	128.00	1792.00
431	219	13	14	70.00	980.00
432	219	18	15	360.00	5400.00
433	220	14	15	3.00	45.00
434	220	19	16	340.00	5440.00
435	221	15	16	220.00	3520.00
436	221	20	17	65.00	1105.00
437	222	16	17	125.00	2125.00
438	222	21	18	85.00	1530.00
439	223	17	18	128.00	2304.00
440	223	22	19	120.00	2280.00
441	224	18	19	360.00	6840.00
442	224	23	20	12.00	240.00
443	225	19	20	340.00	6800.00
444	225	24	21	4.00	84.00
445	226	20	21	65.00	1365.00
446	226	25	22	6.00	132.00
447	227	21	22	85.00	1870.00
448	227	26	23	90.00	2070.00
449	228	22	23	120.00	2760.00
450	228	27	24	95.00	2280.00
451	229	23	24	12.00	288.00
452	229	28	5	180.00	900.00
453	230	24	5	4.00	20.00
454	230	29	6	210.00	1260.00
455	231	25	6	6.00	36.00
456	231	30	7	45.00	315.00
457	232	26	7	90.00	630.00
458	232	1	8	120.00	960.00
459	233	27	8	95.00	760.00
460	233	2	9	115.00	1035.00
461	234	28	9	180.00	1620.00
462	234	3	10	350.00	3500.00
463	235	29	10	210.00	2100.00
464	235	4	11	90.00	990.00
465	236	30	11	45.00	495.00
466	236	5	12	20.00	240.00
467	237	1	12	120.00	1440.00
468	237	6	13	5.00	65.00
469	238	2	13	115.00	1495.00
470	238	7	14	80.00	1120.00
471	239	3	14	350.00	4900.00
472	239	8	15	160.00	2400.00
473	240	4	15	90.00	1350.00
474	240	9	16	135.00	2160.00
475	241	5	16	20.00	320.00
476	241	10	17	320.00	5440.00
477	242	6	17	5.00	85.00
478	242	11	18	60.00	1080.00
479	243	7	18	80.00	1440.00
480	243	12	19	90.00	1710.00
481	244	8	19	160.00	3040.00
482	244	13	20	70.00	1400.00
483	245	9	20	135.00	2700.00
484	245	14	21	3.00	63.00
485	246	10	21	320.00	6720.00
486	246	15	22	220.00	4840.00
487	1	1	30	120.00	3600.00
488	1	2	20	115.00	2300.00
489	2	4	20	90.00	1800.00
490	3	5	80	20.00	1600.00
491	3	6	80	5.00	400.00
492	4	9	40	135.00	5400.00
493	4	10	30	320.00	9600.00
494	5	11	60	60.00	3600.00
495	5	14	300	3.00	900.00
496	6	13	40	70.00	2800.00
497	6	15	25	220.00	5500.00
498	253	6	7	5.00	35.00
499	253	11	8	60.00	480.00
500	254	7	8	80.00	640.00
501	254	12	9	90.00	810.00
502	255	8	9	160.00	1440.00
503	255	13	10	70.00	700.00
504	256	9	10	135.00	1350.00
505	256	14	11	3.00	33.00
506	257	10	11	320.00	3520.00
507	257	15	12	220.00	2640.00
508	258	11	12	60.00	720.00
509	258	16	13	125.00	1625.00
510	259	12	13	90.00	1170.00
511	259	17	14	128.00	1792.00
512	260	13	14	70.00	980.00
513	260	18	15	360.00	5400.00
514	261	14	15	3.00	45.00
515	261	19	16	340.00	5440.00
516	262	15	16	220.00	3520.00
517	262	20	17	65.00	1105.00
518	263	16	17	125.00	2125.00
519	263	21	18	85.00	1530.00
520	264	17	18	128.00	2304.00
521	264	22	19	120.00	2280.00
522	265	18	19	360.00	6840.00
523	265	23	20	12.00	240.00
524	266	19	20	340.00	6800.00
525	266	24	21	4.00	84.00
526	267	20	21	65.00	1365.00
527	267	25	22	6.00	132.00
528	268	21	22	85.00	1870.00
529	268	26	23	90.00	2070.00
530	269	22	23	120.00	2760.00
531	269	27	24	95.00	2280.00
532	270	23	24	12.00	288.00
533	270	28	5	180.00	900.00
534	271	24	5	4.00	20.00
535	271	29	6	210.00	1260.00
536	272	25	6	6.00	36.00
537	272	30	7	45.00	315.00
538	273	26	7	90.00	630.00
539	273	1	8	120.00	960.00
540	274	27	8	95.00	760.00
541	274	2	9	115.00	1035.00
542	275	28	9	180.00	1620.00
543	275	3	10	350.00	3500.00
544	276	29	10	210.00	2100.00
545	276	4	11	90.00	990.00
546	277	30	11	45.00	495.00
547	277	5	12	20.00	240.00
548	278	1	12	120.00	1440.00
549	278	6	13	5.00	65.00
550	279	2	13	115.00	1495.00
551	279	7	14	80.00	1120.00
552	280	3	14	350.00	4900.00
553	280	8	15	160.00	2400.00
554	281	4	15	90.00	1350.00
555	281	9	16	135.00	2160.00
556	282	5	16	20.00	320.00
557	282	10	17	320.00	5440.00
558	283	6	17	5.00	85.00
559	283	11	18	60.00	1080.00
560	284	7	18	80.00	1440.00
561	284	12	19	90.00	1710.00
562	285	8	19	160.00	3040.00
563	285	13	20	70.00	1400.00
564	286	9	20	135.00	2700.00
565	286	14	21	3.00	63.00
566	287	10	21	320.00	6720.00
567	287	15	22	220.00	4840.00
\.


--
-- Data for Name: detallefactura; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.detallefactura (id_detalle, id_factura, id_producto, cantidad, precio_unitario, descuento_linea, total_linea) FROM stdin;
1	1	1	2	250.00	0.00	500.00
2	2	6	20	25.00	40.00	460.00
3	2	4	2	180.00	0.00	360.00
4	3	3	2	550.00	100.00	1000.00
5	3	8	2	300.00	50.00	550.00
6	4	5	5	60.00	0.00	300.00
7	5	1	3	250.00	30.00	720.00
8	5	6	4	25.00	10.00	90.00
9	6	3	4	550.00	200.00	2000.00
10	6	7	10	160.00	160.00	1440.00
11	7	2	2	240.00	0.00	480.00
12	7	14	10	15.00	0.00	150.00
13	8	9	2	270.00	40.00	500.00
14	8	11	2	130.00	0.00	260.00
15	9	10	3	520.00	60.00	1500.00
16	9	15	2	380.00	40.00	720.00
17	10	5	6	60.00	0.00	360.00
18	10	6	16	25.00	50.00	350.00
19	11	12	4	180.00	40.00	680.00
20	11	1	1	250.00	0.00	250.00
21	12	13	5	190.00	50.00	900.00
22	12	8	1	300.00	0.00	300.00
23	13	4	3	180.00	0.00	540.00
24	13	7	4	160.00	0.00	640.00
25	13	10	5	520.00	130.00	2470.00
26	14	5	4	60.00	0.00	240.00
27	14	8	5	300.00	75.00	1425.00
28	14	11	1	130.00	0.00	130.00
29	14	14	2	15.00	0.00	30.00
30	15	6	5	25.00	6.25	118.75
31	15	9	1	270.00	0.00	270.00
32	16	7	1	160.00	0.00	160.00
33	16	10	2	520.00	0.00	1040.00
34	16	13	3	190.00	0.00	570.00
35	17	8	2	300.00	0.00	600.00
36	17	11	3	130.00	0.00	390.00
37	17	14	4	15.00	3.00	57.00
38	17	17	5	260.00	0.00	1300.00
39	18	9	3	270.00	0.00	810.00
40	18	12	4	180.00	36.00	684.00
41	19	10	4	520.00	104.00	1976.00
42	19	13	5	190.00	0.00	950.00
43	19	16	1	255.00	0.00	255.00
44	20	11	5	130.00	0.00	650.00
45	20	14	1	15.00	0.00	15.00
46	20	17	2	260.00	0.00	520.00
47	20	20	3	145.00	21.75	413.25
48	21	12	1	180.00	0.00	180.00
49	21	15	2	380.00	0.00	760.00
50	22	13	2	190.00	0.00	380.00
51	22	16	3	255.00	38.25	726.75
52	22	19	4	560.00	0.00	2240.00
53	23	14	1	15.00	0.75	14.25
54	24	15	4	380.00	0.00	1520.00
55	24	18	5	590.00	0.00	2950.00
56	25	16	5	255.00	0.00	1275.00
57	25	19	1	560.00	0.00	560.00
58	25	22	2	280.00	28.00	532.00
59	26	17	1	260.00	0.00	260.00
60	26	20	2	145.00	14.50	275.50
61	26	23	3	45.00	0.00	135.00
62	26	26	4	185.00	0.00	740.00
63	27	18	2	590.00	59.00	1121.00
64	27	21	3	190.00	0.00	570.00
65	28	19	3	560.00	0.00	1680.00
66	28	22	4	280.00	0.00	1120.00
67	28	25	5	28.00	0.00	140.00
68	29	20	4	145.00	0.00	580.00
69	29	23	5	45.00	0.00	225.00
70	29	26	1	185.00	9.25	175.75
71	29	29	2	390.00	0.00	780.00
72	30	21	5	190.00	0.00	950.00
73	30	24	1	18.00	0.90	17.10
74	31	22	1	280.00	14.00	266.00
75	31	25	2	28.00	0.00	56.00
76	31	28	3	340.00	0.00	1020.00
77	32	23	2	45.00	0.00	90.00
78	32	26	3	185.00	0.00	555.00
79	32	29	4	390.00	0.00	1560.00
80	32	2	5	240.00	60.00	1140.00
81	33	24	3	18.00	0.00	54.00
82	33	27	4	195.00	0.00	780.00
83	34	25	1	28.00	0.00	28.00
84	35	26	5	185.00	46.25	878.75
85	35	29	1	390.00	0.00	390.00
86	35	2	2	240.00	0.00	480.00
87	35	5	3	60.00	0.00	180.00
88	36	27	1	195.00	0.00	195.00
89	36	30	2	120.00	0.00	240.00
90	37	28	2	340.00	0.00	680.00
91	37	1	3	250.00	0.00	750.00
92	37	4	4	180.00	36.00	684.00
93	38	29	3	390.00	0.00	1170.00
94	38	2	4	240.00	48.00	912.00
95	38	5	5	60.00	0.00	300.00
96	38	8	1	300.00	0.00	300.00
97	39	30	4	120.00	24.00	456.00
98	39	3	5	550.00	0.00	2750.00
99	40	1	5	250.00	0.00	1250.00
100	40	4	1	180.00	0.00	180.00
101	40	7	2	160.00	0.00	320.00
102	41	2	1	240.00	0.00	240.00
103	41	5	2	60.00	0.00	120.00
104	41	8	3	300.00	45.00	855.00
105	41	11	4	130.00	0.00	520.00
106	42	3	2	550.00	0.00	1100.00
107	42	6	3	25.00	3.75	71.25
108	43	4	3	180.00	27.00	513.00
109	43	7	4	160.00	0.00	640.00
110	43	10	5	520.00	0.00	2600.00
111	44	5	4	60.00	0.00	240.00
112	44	8	5	300.00	0.00	1500.00
113	44	11	1	130.00	0.00	130.00
114	44	14	2	15.00	1.50	28.50
115	45	6	1	25.00	0.00	25.00
116	46	7	1	160.00	0.00	160.00
117	46	10	2	520.00	52.00	988.00
118	46	13	3	190.00	0.00	570.00
119	47	8	2	300.00	30.00	570.00
120	47	11	3	130.00	0.00	390.00
121	47	14	4	15.00	0.00	60.00
122	47	17	5	260.00	0.00	1300.00
123	48	9	3	270.00	0.00	810.00
124	48	12	4	180.00	0.00	720.00
125	49	10	4	520.00	0.00	2080.00
126	49	13	5	190.00	0.00	950.00
127	49	16	1	255.00	12.75	242.25
128	50	11	5	130.00	0.00	650.00
129	50	14	1	15.00	0.75	14.25
130	50	17	2	260.00	0.00	520.00
131	50	20	3	145.00	0.00	435.00
132	51	12	1	180.00	9.00	171.00
133	51	15	2	380.00	0.00	760.00
134	52	13	2	190.00	0.00	380.00
135	52	16	3	255.00	0.00	765.00
136	52	19	4	560.00	0.00	2240.00
137	53	14	3	15.00	0.00	45.00
138	53	17	4	260.00	0.00	1040.00
139	53	20	5	145.00	36.25	688.75
140	53	23	1	45.00	0.00	45.00
141	54	15	4	380.00	0.00	1520.00
142	54	18	5	590.00	147.50	2802.50
143	55	16	5	255.00	63.75	1211.25
144	55	19	1	560.00	0.00	560.00
145	55	22	2	280.00	0.00	560.00
146	56	17	1	260.00	0.00	260.00
147	57	18	2	590.00	0.00	1180.00
148	57	21	3	190.00	0.00	570.00
149	58	19	3	560.00	0.00	1680.00
150	58	22	4	280.00	56.00	1064.00
151	58	25	5	28.00	0.00	140.00
152	59	20	4	145.00	29.00	551.00
153	59	23	5	45.00	0.00	225.00
154	59	26	1	185.00	0.00	185.00
155	59	29	2	390.00	0.00	780.00
156	60	21	5	190.00	0.00	950.00
157	60	24	1	18.00	0.00	18.00
158	61	22	1	280.00	0.00	280.00
159	61	25	2	28.00	0.00	56.00
160	61	28	3	340.00	51.00	969.00
161	62	23	2	45.00	0.00	90.00
162	62	26	3	185.00	27.75	527.25
163	62	29	4	390.00	0.00	1560.00
164	62	2	5	240.00	0.00	1200.00
165	63	24	3	18.00	2.70	51.30
166	63	27	4	195.00	0.00	780.00
167	64	25	4	28.00	0.00	112.00
168	64	28	5	340.00	0.00	1700.00
169	64	1	1	250.00	0.00	250.00
170	65	26	5	185.00	0.00	925.00
171	65	29	1	390.00	0.00	390.00
172	65	2	2	240.00	24.00	456.00
173	65	5	3	60.00	0.00	180.00
174	66	27	1	195.00	0.00	195.00
175	66	30	2	120.00	12.00	228.00
176	67	28	1	340.00	17.00	323.00
177	68	29	3	390.00	0.00	1170.00
178	68	2	4	240.00	0.00	960.00
179	68	5	5	60.00	0.00	300.00
180	68	8	1	300.00	15.00	285.00
181	69	30	4	120.00	0.00	480.00
182	69	3	5	550.00	0.00	2750.00
183	70	1	5	250.00	0.00	1250.00
184	70	4	1	180.00	9.00	171.00
185	70	7	2	160.00	0.00	320.00
186	71	2	1	240.00	12.00	228.00
187	71	5	2	60.00	0.00	120.00
188	71	8	3	300.00	0.00	900.00
189	71	11	4	130.00	0.00	520.00
190	72	3	2	550.00	0.00	1100.00
191	72	6	3	25.00	0.00	75.00
192	73	4	3	180.00	0.00	540.00
193	73	7	4	160.00	0.00	640.00
194	73	10	5	520.00	130.00	2470.00
195	74	5	4	60.00	0.00	240.00
196	74	8	5	300.00	75.00	1425.00
197	74	11	1	130.00	0.00	130.00
198	74	14	2	15.00	0.00	30.00
199	75	6	5	25.00	6.25	118.75
200	75	9	1	270.00	0.00	270.00
201	76	7	1	160.00	0.00	160.00
202	76	10	2	520.00	0.00	1040.00
203	76	13	3	190.00	0.00	570.00
204	77	8	2	300.00	0.00	600.00
205	77	11	3	130.00	0.00	390.00
206	77	14	4	15.00	3.00	57.00
207	77	17	5	260.00	0.00	1300.00
208	78	9	1	270.00	0.00	270.00
209	79	10	4	520.00	104.00	1976.00
210	79	13	5	190.00	0.00	950.00
211	79	16	1	255.00	0.00	255.00
212	80	11	5	130.00	0.00	650.00
213	80	14	1	15.00	0.00	15.00
214	80	17	2	260.00	0.00	520.00
215	80	20	3	145.00	21.75	413.25
216	81	12	1	180.00	0.00	180.00
217	81	15	2	380.00	0.00	760.00
218	82	13	2	190.00	0.00	380.00
219	82	16	3	255.00	38.25	726.75
220	82	19	4	560.00	0.00	2240.00
221	83	14	3	15.00	2.25	42.75
222	83	17	4	260.00	0.00	1040.00
223	83	20	5	145.00	0.00	725.00
224	83	23	1	45.00	0.00	45.00
225	84	15	4	380.00	0.00	1520.00
226	84	18	5	590.00	0.00	2950.00
227	85	16	5	255.00	0.00	1275.00
228	85	19	1	560.00	0.00	560.00
229	85	22	2	280.00	28.00	532.00
230	86	17	1	260.00	0.00	260.00
231	86	20	2	145.00	14.50	275.50
232	86	23	3	45.00	0.00	135.00
233	86	26	4	185.00	0.00	740.00
234	87	18	2	590.00	59.00	1121.00
235	87	21	3	190.00	0.00	570.00
236	88	19	3	560.00	0.00	1680.00
237	88	22	4	280.00	0.00	1120.00
238	88	25	5	28.00	0.00	140.00
239	89	20	1	145.00	0.00	145.00
240	90	21	5	190.00	0.00	950.00
241	90	24	1	18.00	0.90	17.10
242	91	22	1	280.00	14.00	266.00
243	91	25	2	28.00	0.00	56.00
244	91	28	3	340.00	0.00	1020.00
245	92	23	2	45.00	0.00	90.00
246	92	26	3	185.00	0.00	555.00
247	92	29	4	390.00	0.00	1560.00
248	92	2	5	240.00	60.00	1140.00
249	93	24	3	18.00	0.00	54.00
250	93	27	4	195.00	0.00	780.00
251	94	25	4	28.00	0.00	112.00
252	94	28	5	340.00	85.00	1615.00
253	94	1	1	250.00	0.00	250.00
254	95	26	5	185.00	46.25	878.75
255	95	29	1	390.00	0.00	390.00
256	95	2	2	240.00	0.00	480.00
257	95	5	3	60.00	0.00	180.00
258	96	27	1	195.00	0.00	195.00
259	96	30	2	120.00	0.00	240.00
260	97	28	2	340.00	0.00	680.00
261	97	1	3	250.00	0.00	750.00
262	97	4	4	180.00	36.00	684.00
263	98	29	3	390.00	0.00	1170.00
264	98	2	4	240.00	48.00	912.00
265	98	5	5	60.00	0.00	300.00
266	98	8	1	300.00	0.00	300.00
267	99	30	4	120.00	24.00	456.00
268	99	3	5	550.00	0.00	2750.00
269	100	1	1	250.00	0.00	250.00
270	101	2	1	240.00	0.00	240.00
271	101	5	2	60.00	0.00	120.00
272	101	8	3	300.00	45.00	855.00
273	101	11	4	130.00	0.00	520.00
274	102	3	2	550.00	0.00	1100.00
275	102	6	3	25.00	3.75	71.25
276	1	1	2	250.00	0.00	500.00
277	2	6	20	25.00	40.00	460.00
278	2	4	2	180.00	0.00	360.00
279	3	3	2	550.00	100.00	1000.00
280	3	8	2	300.00	50.00	550.00
281	4	5	5	60.00	0.00	300.00
282	5	1	3	250.00	30.00	720.00
283	5	6	4	25.00	10.00	90.00
284	6	3	4	550.00	200.00	2000.00
285	6	7	10	160.00	160.00	1440.00
286	7	2	2	240.00	0.00	480.00
287	7	14	10	15.00	0.00	150.00
288	8	9	2	270.00	40.00	500.00
289	8	11	2	130.00	0.00	260.00
290	9	10	3	520.00	60.00	1500.00
291	9	15	2	380.00	40.00	720.00
292	10	5	6	60.00	0.00	360.00
293	10	6	16	25.00	50.00	350.00
294	11	12	4	180.00	40.00	680.00
295	11	1	1	250.00	0.00	250.00
296	12	13	5	190.00	50.00	900.00
297	12	8	1	300.00	0.00	300.00
298	115	4	3	180.00	0.00	540.00
299	115	7	4	160.00	0.00	640.00
300	115	10	5	520.00	130.00	2470.00
301	116	5	4	60.00	0.00	240.00
302	116	8	5	300.00	75.00	1425.00
303	116	11	1	130.00	0.00	130.00
304	116	14	2	15.00	0.00	30.00
305	117	6	5	25.00	6.25	118.75
306	117	9	1	270.00	0.00	270.00
307	118	7	1	160.00	0.00	160.00
308	118	10	2	520.00	0.00	1040.00
309	118	13	3	190.00	0.00	570.00
310	119	8	2	300.00	0.00	600.00
311	119	11	3	130.00	0.00	390.00
312	119	14	4	15.00	3.00	57.00
313	119	17	5	260.00	0.00	1300.00
314	120	9	3	270.00	0.00	810.00
315	120	12	4	180.00	36.00	684.00
316	121	10	4	520.00	104.00	1976.00
317	121	13	5	190.00	0.00	950.00
318	121	16	1	255.00	0.00	255.00
319	122	11	5	130.00	0.00	650.00
320	122	14	1	15.00	0.00	15.00
321	122	17	2	260.00	0.00	520.00
322	122	20	3	145.00	21.75	413.25
323	123	12	1	180.00	0.00	180.00
324	123	15	2	380.00	0.00	760.00
325	124	13	2	190.00	0.00	380.00
326	124	16	3	255.00	38.25	726.75
327	124	19	4	560.00	0.00	2240.00
328	125	14	1	15.00	0.75	14.25
329	126	15	4	380.00	0.00	1520.00
330	126	18	5	590.00	0.00	2950.00
331	127	16	5	255.00	0.00	1275.00
332	127	19	1	560.00	0.00	560.00
333	127	22	2	280.00	28.00	532.00
334	128	17	1	260.00	0.00	260.00
335	128	20	2	145.00	14.50	275.50
336	128	23	3	45.00	0.00	135.00
337	128	26	4	185.00	0.00	740.00
338	129	18	2	590.00	59.00	1121.00
339	129	21	3	190.00	0.00	570.00
340	130	19	3	560.00	0.00	1680.00
341	130	22	4	280.00	0.00	1120.00
342	130	25	5	28.00	0.00	140.00
343	131	20	4	145.00	0.00	580.00
344	131	23	5	45.00	0.00	225.00
345	131	26	1	185.00	9.25	175.75
346	131	29	2	390.00	0.00	780.00
347	132	21	5	190.00	0.00	950.00
348	132	24	1	18.00	0.90	17.10
349	133	22	1	280.00	14.00	266.00
350	133	25	2	28.00	0.00	56.00
351	133	28	3	340.00	0.00	1020.00
352	134	23	2	45.00	0.00	90.00
353	134	26	3	185.00	0.00	555.00
354	134	29	4	390.00	0.00	1560.00
355	134	2	5	240.00	60.00	1140.00
356	135	24	3	18.00	0.00	54.00
357	135	27	4	195.00	0.00	780.00
358	136	25	1	28.00	0.00	28.00
359	137	26	5	185.00	46.25	878.75
360	137	29	1	390.00	0.00	390.00
361	137	2	2	240.00	0.00	480.00
362	137	5	3	60.00	0.00	180.00
363	138	27	1	195.00	0.00	195.00
364	138	30	2	120.00	0.00	240.00
365	139	28	2	340.00	0.00	680.00
366	139	1	3	250.00	0.00	750.00
367	139	4	4	180.00	36.00	684.00
368	140	29	3	390.00	0.00	1170.00
369	140	2	4	240.00	48.00	912.00
370	140	5	5	60.00	0.00	300.00
371	140	8	1	300.00	0.00	300.00
372	141	30	4	120.00	24.00	456.00
373	141	3	5	550.00	0.00	2750.00
374	142	1	5	250.00	0.00	1250.00
375	142	4	1	180.00	0.00	180.00
376	142	7	2	160.00	0.00	320.00
377	143	2	1	240.00	0.00	240.00
378	143	5	2	60.00	0.00	120.00
379	143	8	3	300.00	45.00	855.00
380	143	11	4	130.00	0.00	520.00
381	144	3	2	550.00	0.00	1100.00
382	144	6	3	25.00	3.75	71.25
383	145	4	3	180.00	27.00	513.00
384	145	7	4	160.00	0.00	640.00
385	145	10	5	520.00	0.00	2600.00
386	146	5	4	60.00	0.00	240.00
387	146	8	5	300.00	0.00	1500.00
388	146	11	1	130.00	0.00	130.00
389	146	14	2	15.00	1.50	28.50
390	147	6	1	25.00	0.00	25.00
391	148	7	1	160.00	0.00	160.00
392	148	10	2	520.00	52.00	988.00
393	148	13	3	190.00	0.00	570.00
394	149	8	2	300.00	30.00	570.00
395	149	11	3	130.00	0.00	390.00
396	149	14	4	15.00	0.00	60.00
397	149	17	5	260.00	0.00	1300.00
398	150	9	3	270.00	0.00	810.00
399	150	12	4	180.00	0.00	720.00
400	151	10	4	520.00	0.00	2080.00
401	151	13	5	190.00	0.00	950.00
402	151	16	1	255.00	12.75	242.25
403	152	11	5	130.00	0.00	650.00
404	152	14	1	15.00	0.75	14.25
405	152	17	2	260.00	0.00	520.00
406	152	20	3	145.00	0.00	435.00
407	153	12	1	180.00	9.00	171.00
408	153	15	2	380.00	0.00	760.00
409	154	13	2	190.00	0.00	380.00
410	154	16	3	255.00	0.00	765.00
411	154	19	4	560.00	0.00	2240.00
412	155	14	3	15.00	0.00	45.00
413	155	17	4	260.00	0.00	1040.00
414	155	20	5	145.00	36.25	688.75
415	155	23	1	45.00	0.00	45.00
416	156	15	4	380.00	0.00	1520.00
417	156	18	5	590.00	147.50	2802.50
418	157	16	5	255.00	63.75	1211.25
419	157	19	1	560.00	0.00	560.00
420	157	22	2	280.00	0.00	560.00
421	158	17	1	260.00	0.00	260.00
422	159	18	2	590.00	0.00	1180.00
423	159	21	3	190.00	0.00	570.00
424	160	19	3	560.00	0.00	1680.00
425	160	22	4	280.00	56.00	1064.00
426	160	25	5	28.00	0.00	140.00
427	161	20	4	145.00	29.00	551.00
428	161	23	5	45.00	0.00	225.00
429	161	26	1	185.00	0.00	185.00
430	161	29	2	390.00	0.00	780.00
431	162	21	5	190.00	0.00	950.00
432	162	24	1	18.00	0.00	18.00
433	163	22	1	280.00	0.00	280.00
434	163	25	2	28.00	0.00	56.00
435	163	28	3	340.00	51.00	969.00
436	164	23	2	45.00	0.00	90.00
437	164	26	3	185.00	27.75	527.25
438	164	29	4	390.00	0.00	1560.00
439	164	2	5	240.00	0.00	1200.00
440	165	24	3	18.00	2.70	51.30
441	165	27	4	195.00	0.00	780.00
442	166	25	4	28.00	0.00	112.00
443	166	28	5	340.00	0.00	1700.00
444	166	1	1	250.00	0.00	250.00
445	167	26	5	185.00	0.00	925.00
446	167	29	1	390.00	0.00	390.00
447	167	2	2	240.00	24.00	456.00
448	167	5	3	60.00	0.00	180.00
449	168	27	1	195.00	0.00	195.00
450	168	30	2	120.00	12.00	228.00
451	169	28	1	340.00	17.00	323.00
452	170	29	3	390.00	0.00	1170.00
453	170	2	4	240.00	0.00	960.00
454	170	5	5	60.00	0.00	300.00
455	170	8	1	300.00	15.00	285.00
456	171	30	4	120.00	0.00	480.00
457	171	3	5	550.00	0.00	2750.00
458	172	1	5	250.00	0.00	1250.00
459	172	4	1	180.00	9.00	171.00
460	172	7	2	160.00	0.00	320.00
461	173	2	1	240.00	12.00	228.00
462	173	5	2	60.00	0.00	120.00
463	173	8	3	300.00	0.00	900.00
464	173	11	4	130.00	0.00	520.00
465	174	3	2	550.00	0.00	1100.00
466	174	6	3	25.00	0.00	75.00
467	175	4	3	180.00	0.00	540.00
468	175	7	4	160.00	0.00	640.00
469	175	10	5	520.00	130.00	2470.00
470	176	5	4	60.00	0.00	240.00
471	176	8	5	300.00	75.00	1425.00
472	176	11	1	130.00	0.00	130.00
473	176	14	2	15.00	0.00	30.00
474	177	6	5	25.00	6.25	118.75
475	177	9	1	270.00	0.00	270.00
476	178	7	1	160.00	0.00	160.00
477	178	10	2	520.00	0.00	1040.00
478	178	13	3	190.00	0.00	570.00
479	179	8	2	300.00	0.00	600.00
480	179	11	3	130.00	0.00	390.00
481	179	14	4	15.00	3.00	57.00
482	179	17	5	260.00	0.00	1300.00
483	180	9	1	270.00	0.00	270.00
484	181	10	4	520.00	104.00	1976.00
485	181	13	5	190.00	0.00	950.00
486	181	16	1	255.00	0.00	255.00
487	182	11	5	130.00	0.00	650.00
488	182	14	1	15.00	0.00	15.00
489	182	17	2	260.00	0.00	520.00
490	182	20	3	145.00	21.75	413.25
491	183	12	1	180.00	0.00	180.00
492	183	15	2	380.00	0.00	760.00
493	184	13	2	190.00	0.00	380.00
494	184	16	3	255.00	38.25	726.75
495	184	19	4	560.00	0.00	2240.00
496	185	14	3	15.00	2.25	42.75
497	185	17	4	260.00	0.00	1040.00
498	185	20	5	145.00	0.00	725.00
499	185	23	1	45.00	0.00	45.00
500	186	15	4	380.00	0.00	1520.00
501	186	18	5	590.00	0.00	2950.00
502	187	16	5	255.00	0.00	1275.00
503	187	19	1	560.00	0.00	560.00
504	187	22	2	280.00	28.00	532.00
505	188	17	1	260.00	0.00	260.00
506	188	20	2	145.00	14.50	275.50
507	188	23	3	45.00	0.00	135.00
508	188	26	4	185.00	0.00	740.00
509	189	18	2	590.00	59.00	1121.00
510	189	21	3	190.00	0.00	570.00
511	190	19	3	560.00	0.00	1680.00
512	190	22	4	280.00	0.00	1120.00
513	190	25	5	28.00	0.00	140.00
514	191	20	1	145.00	0.00	145.00
515	192	21	5	190.00	0.00	950.00
516	192	24	1	18.00	0.90	17.10
517	193	22	1	280.00	14.00	266.00
518	193	25	2	28.00	0.00	56.00
519	193	28	3	340.00	0.00	1020.00
520	194	23	2	45.00	0.00	90.00
521	194	26	3	185.00	0.00	555.00
522	194	29	4	390.00	0.00	1560.00
523	194	2	5	240.00	60.00	1140.00
524	195	24	3	18.00	0.00	54.00
525	195	27	4	195.00	0.00	780.00
526	196	25	4	28.00	0.00	112.00
527	196	28	5	340.00	85.00	1615.00
528	196	1	1	250.00	0.00	250.00
529	197	26	5	185.00	46.25	878.75
530	197	29	1	390.00	0.00	390.00
531	197	2	2	240.00	0.00	480.00
532	197	5	3	60.00	0.00	180.00
533	198	27	1	195.00	0.00	195.00
534	198	30	2	120.00	0.00	240.00
535	199	28	2	340.00	0.00	680.00
536	199	1	3	250.00	0.00	750.00
537	199	4	4	180.00	36.00	684.00
538	200	29	3	390.00	0.00	1170.00
539	200	2	4	240.00	48.00	912.00
540	200	5	5	60.00	0.00	300.00
541	200	8	1	300.00	0.00	300.00
542	201	30	4	120.00	24.00	456.00
543	201	3	5	550.00	0.00	2750.00
544	202	1	1	250.00	0.00	250.00
545	203	2	1	240.00	0.00	240.00
546	203	5	2	60.00	0.00	120.00
547	203	8	3	300.00	45.00	855.00
548	203	11	4	130.00	0.00	520.00
549	204	3	2	550.00	0.00	1100.00
550	204	6	3	25.00	3.75	71.25
551	1	1	2	250.00	0.00	500.00
552	2	6	20	25.00	40.00	460.00
553	2	4	2	180.00	0.00	360.00
554	3	3	2	550.00	100.00	1000.00
555	3	8	2	300.00	50.00	550.00
556	4	5	5	60.00	0.00	300.00
557	5	1	3	250.00	30.00	720.00
558	5	6	4	25.00	10.00	90.00
559	6	3	4	550.00	200.00	2000.00
560	6	7	10	160.00	160.00	1440.00
561	7	2	2	240.00	0.00	480.00
562	7	14	10	15.00	0.00	150.00
563	8	9	2	270.00	40.00	500.00
564	8	11	2	130.00	0.00	260.00
565	9	10	3	520.00	60.00	1500.00
566	9	15	2	380.00	40.00	720.00
567	10	5	6	60.00	0.00	360.00
568	10	6	16	25.00	50.00	350.00
569	11	12	4	180.00	40.00	680.00
570	11	1	1	250.00	0.00	250.00
571	12	13	5	190.00	50.00	900.00
572	12	8	1	300.00	0.00	300.00
573	217	4	3	180.00	0.00	540.00
574	217	7	4	160.00	0.00	640.00
575	217	10	5	520.00	130.00	2470.00
576	218	5	4	60.00	0.00	240.00
577	218	8	5	300.00	75.00	1425.00
578	218	11	1	130.00	0.00	130.00
579	218	14	2	15.00	0.00	30.00
580	219	6	5	25.00	6.25	118.75
581	219	9	1	270.00	0.00	270.00
582	220	7	1	160.00	0.00	160.00
583	220	10	2	520.00	0.00	1040.00
584	220	13	3	190.00	0.00	570.00
585	221	8	2	300.00	0.00	600.00
586	221	11	3	130.00	0.00	390.00
587	221	14	4	15.00	3.00	57.00
588	221	17	5	260.00	0.00	1300.00
589	222	9	3	270.00	0.00	810.00
590	222	12	4	180.00	36.00	684.00
591	223	10	4	520.00	104.00	1976.00
592	223	13	5	190.00	0.00	950.00
593	223	16	1	255.00	0.00	255.00
594	224	11	5	130.00	0.00	650.00
595	224	14	1	15.00	0.00	15.00
596	224	17	2	260.00	0.00	520.00
597	224	20	3	145.00	21.75	413.25
598	225	12	1	180.00	0.00	180.00
599	225	15	2	380.00	0.00	760.00
600	226	13	2	190.00	0.00	380.00
601	226	16	3	255.00	38.25	726.75
602	226	19	4	560.00	0.00	2240.00
603	227	14	1	15.00	0.75	14.25
604	228	15	4	380.00	0.00	1520.00
605	228	18	5	590.00	0.00	2950.00
606	229	16	5	255.00	0.00	1275.00
607	229	19	1	560.00	0.00	560.00
608	229	22	2	280.00	28.00	532.00
609	230	17	1	260.00	0.00	260.00
610	230	20	2	145.00	14.50	275.50
611	230	23	3	45.00	0.00	135.00
612	230	26	4	185.00	0.00	740.00
613	231	18	2	590.00	59.00	1121.00
614	231	21	3	190.00	0.00	570.00
615	232	19	3	560.00	0.00	1680.00
616	232	22	4	280.00	0.00	1120.00
617	232	25	5	28.00	0.00	140.00
618	233	20	4	145.00	0.00	580.00
619	233	23	5	45.00	0.00	225.00
620	233	26	1	185.00	9.25	175.75
621	233	29	2	390.00	0.00	780.00
622	234	21	5	190.00	0.00	950.00
623	234	24	1	18.00	0.90	17.10
624	235	22	1	280.00	14.00	266.00
625	235	25	2	28.00	0.00	56.00
626	235	28	3	340.00	0.00	1020.00
627	236	23	2	45.00	0.00	90.00
628	236	26	3	185.00	0.00	555.00
629	236	29	4	390.00	0.00	1560.00
630	236	2	5	240.00	60.00	1140.00
631	237	24	3	18.00	0.00	54.00
632	237	27	4	195.00	0.00	780.00
633	238	25	1	28.00	0.00	28.00
634	239	26	5	185.00	46.25	878.75
635	239	29	1	390.00	0.00	390.00
636	239	2	2	240.00	0.00	480.00
637	239	5	3	60.00	0.00	180.00
638	240	27	1	195.00	0.00	195.00
639	240	30	2	120.00	0.00	240.00
640	241	28	2	340.00	0.00	680.00
641	241	1	3	250.00	0.00	750.00
642	241	4	4	180.00	36.00	684.00
643	242	29	3	390.00	0.00	1170.00
644	242	2	4	240.00	48.00	912.00
645	242	5	5	60.00	0.00	300.00
646	242	8	1	300.00	0.00	300.00
647	243	30	4	120.00	24.00	456.00
648	243	3	5	550.00	0.00	2750.00
649	244	1	5	250.00	0.00	1250.00
650	244	4	1	180.00	0.00	180.00
651	244	7	2	160.00	0.00	320.00
652	245	2	1	240.00	0.00	240.00
653	245	5	2	60.00	0.00	120.00
654	245	8	3	300.00	45.00	855.00
655	245	11	4	130.00	0.00	520.00
656	246	3	2	550.00	0.00	1100.00
657	246	6	3	25.00	3.75	71.25
658	247	4	3	180.00	27.00	513.00
659	247	7	4	160.00	0.00	640.00
660	247	10	5	520.00	0.00	2600.00
661	248	5	4	60.00	0.00	240.00
662	248	8	5	300.00	0.00	1500.00
663	248	11	1	130.00	0.00	130.00
664	248	14	2	15.00	1.50	28.50
665	249	6	1	25.00	0.00	25.00
666	250	7	1	160.00	0.00	160.00
667	250	10	2	520.00	52.00	988.00
668	250	13	3	190.00	0.00	570.00
669	251	8	2	300.00	30.00	570.00
670	251	11	3	130.00	0.00	390.00
671	251	14	4	15.00	0.00	60.00
672	251	17	5	260.00	0.00	1300.00
673	252	9	3	270.00	0.00	810.00
674	252	12	4	180.00	0.00	720.00
675	253	10	4	520.00	0.00	2080.00
676	253	13	5	190.00	0.00	950.00
677	253	16	1	255.00	12.75	242.25
678	254	11	5	130.00	0.00	650.00
679	254	14	1	15.00	0.75	14.25
680	254	17	2	260.00	0.00	520.00
681	254	20	3	145.00	0.00	435.00
682	255	12	1	180.00	9.00	171.00
683	255	15	2	380.00	0.00	760.00
684	256	13	2	190.00	0.00	380.00
685	256	16	3	255.00	0.00	765.00
686	256	19	4	560.00	0.00	2240.00
687	257	14	3	15.00	0.00	45.00
688	257	17	4	260.00	0.00	1040.00
689	257	20	5	145.00	36.25	688.75
690	257	23	1	45.00	0.00	45.00
691	258	15	4	380.00	0.00	1520.00
692	258	18	5	590.00	147.50	2802.50
693	259	16	5	255.00	63.75	1211.25
694	259	19	1	560.00	0.00	560.00
695	259	22	2	280.00	0.00	560.00
696	260	17	1	260.00	0.00	260.00
697	261	18	2	590.00	0.00	1180.00
698	261	21	3	190.00	0.00	570.00
699	262	19	3	560.00	0.00	1680.00
700	262	22	4	280.00	56.00	1064.00
701	262	25	5	28.00	0.00	140.00
702	263	20	4	145.00	29.00	551.00
703	263	23	5	45.00	0.00	225.00
704	263	26	1	185.00	0.00	185.00
705	263	29	2	390.00	0.00	780.00
706	264	21	5	190.00	0.00	950.00
707	264	24	1	18.00	0.00	18.00
708	265	22	1	280.00	0.00	280.00
709	265	25	2	28.00	0.00	56.00
710	265	28	3	340.00	51.00	969.00
711	266	23	2	45.00	0.00	90.00
712	266	26	3	185.00	27.75	527.25
713	266	29	4	390.00	0.00	1560.00
714	266	2	5	240.00	0.00	1200.00
715	267	24	3	18.00	2.70	51.30
716	267	27	4	195.00	0.00	780.00
717	268	25	4	28.00	0.00	112.00
718	268	28	5	340.00	0.00	1700.00
719	268	1	1	250.00	0.00	250.00
720	269	26	5	185.00	0.00	925.00
721	269	29	1	390.00	0.00	390.00
722	269	2	2	240.00	24.00	456.00
723	269	5	3	60.00	0.00	180.00
724	270	27	1	195.00	0.00	195.00
725	270	30	2	120.00	12.00	228.00
726	271	28	1	340.00	17.00	323.00
727	272	29	3	390.00	0.00	1170.00
728	272	2	4	240.00	0.00	960.00
729	272	5	5	60.00	0.00	300.00
730	272	8	1	300.00	15.00	285.00
731	273	30	4	120.00	0.00	480.00
732	273	3	5	550.00	0.00	2750.00
733	274	1	5	250.00	0.00	1250.00
734	274	4	1	180.00	9.00	171.00
735	274	7	2	160.00	0.00	320.00
736	275	2	1	240.00	12.00	228.00
737	275	5	2	60.00	0.00	120.00
738	275	8	3	300.00	0.00	900.00
739	275	11	4	130.00	0.00	520.00
740	276	3	2	550.00	0.00	1100.00
741	276	6	3	25.00	0.00	75.00
742	277	4	3	180.00	0.00	540.00
743	277	7	4	160.00	0.00	640.00
744	277	10	5	520.00	130.00	2470.00
745	278	5	4	60.00	0.00	240.00
746	278	8	5	300.00	75.00	1425.00
747	278	11	1	130.00	0.00	130.00
748	278	14	2	15.00	0.00	30.00
749	279	6	5	25.00	6.25	118.75
750	279	9	1	270.00	0.00	270.00
751	280	7	1	160.00	0.00	160.00
752	280	10	2	520.00	0.00	1040.00
753	280	13	3	190.00	0.00	570.00
754	281	8	2	300.00	0.00	600.00
755	281	11	3	130.00	0.00	390.00
756	281	14	4	15.00	3.00	57.00
757	281	17	5	260.00	0.00	1300.00
758	282	9	1	270.00	0.00	270.00
759	283	10	4	520.00	104.00	1976.00
760	283	13	5	190.00	0.00	950.00
761	283	16	1	255.00	0.00	255.00
762	284	11	5	130.00	0.00	650.00
763	284	14	1	15.00	0.00	15.00
764	284	17	2	260.00	0.00	520.00
765	284	20	3	145.00	21.75	413.25
766	285	12	1	180.00	0.00	180.00
767	285	15	2	380.00	0.00	760.00
768	286	13	2	190.00	0.00	380.00
769	286	16	3	255.00	38.25	726.75
770	286	19	4	560.00	0.00	2240.00
771	287	14	3	15.00	2.25	42.75
772	287	17	4	260.00	0.00	1040.00
773	287	20	5	145.00	0.00	725.00
774	287	23	1	45.00	0.00	45.00
775	288	15	4	380.00	0.00	1520.00
776	288	18	5	590.00	0.00	2950.00
777	289	16	5	255.00	0.00	1275.00
778	289	19	1	560.00	0.00	560.00
779	289	22	2	280.00	28.00	532.00
780	290	17	1	260.00	0.00	260.00
781	290	20	2	145.00	14.50	275.50
782	290	23	3	45.00	0.00	135.00
783	290	26	4	185.00	0.00	740.00
784	291	18	2	590.00	59.00	1121.00
785	291	21	3	190.00	0.00	570.00
786	292	19	3	560.00	0.00	1680.00
787	292	22	4	280.00	0.00	1120.00
788	292	25	5	28.00	0.00	140.00
789	293	20	1	145.00	0.00	145.00
790	294	21	5	190.00	0.00	950.00
791	294	24	1	18.00	0.90	17.10
792	295	22	1	280.00	14.00	266.00
793	295	25	2	28.00	0.00	56.00
794	295	28	3	340.00	0.00	1020.00
795	296	23	2	45.00	0.00	90.00
796	296	26	3	185.00	0.00	555.00
797	296	29	4	390.00	0.00	1560.00
798	296	2	5	240.00	60.00	1140.00
799	297	24	3	18.00	0.00	54.00
800	297	27	4	195.00	0.00	780.00
801	298	25	4	28.00	0.00	112.00
802	298	28	5	340.00	85.00	1615.00
803	298	1	1	250.00	0.00	250.00
804	299	26	5	185.00	46.25	878.75
805	299	29	1	390.00	0.00	390.00
806	299	2	2	240.00	0.00	480.00
807	299	5	3	60.00	0.00	180.00
808	300	27	1	195.00	0.00	195.00
809	300	30	2	120.00	0.00	240.00
810	301	28	2	340.00	0.00	680.00
811	301	1	3	250.00	0.00	750.00
812	301	4	4	180.00	36.00	684.00
813	302	29	3	390.00	0.00	1170.00
814	302	2	4	240.00	48.00	912.00
815	302	5	5	60.00	0.00	300.00
816	302	8	1	300.00	0.00	300.00
817	303	30	4	120.00	24.00	456.00
818	303	3	5	550.00	0.00	2750.00
819	304	1	1	250.00	0.00	250.00
820	305	2	1	240.00	0.00	240.00
821	305	5	2	60.00	0.00	120.00
822	305	8	3	300.00	45.00	855.00
823	305	11	4	130.00	0.00	520.00
824	306	3	2	550.00	0.00	1100.00
825	306	6	3	25.00	3.75	71.25
826	1	1	2	250.00	0.00	500.00
827	2	6	20	25.00	40.00	460.00
828	2	4	2	180.00	0.00	360.00
829	3	3	2	550.00	100.00	1000.00
830	3	8	2	300.00	50.00	550.00
831	4	5	5	60.00	0.00	300.00
832	5	1	3	250.00	30.00	720.00
833	5	6	4	25.00	10.00	90.00
834	6	3	4	550.00	200.00	2000.00
835	6	7	10	160.00	160.00	1440.00
836	7	2	2	240.00	0.00	480.00
837	7	14	10	15.00	0.00	150.00
838	8	9	2	270.00	40.00	500.00
839	8	11	2	130.00	0.00	260.00
840	9	10	3	520.00	60.00	1500.00
841	9	15	2	380.00	40.00	720.00
842	10	5	6	60.00	0.00	360.00
843	10	6	16	25.00	50.00	350.00
844	11	12	4	180.00	40.00	680.00
845	11	1	1	250.00	0.00	250.00
846	12	13	5	190.00	50.00	900.00
847	12	8	1	300.00	0.00	300.00
848	319	4	3	180.00	0.00	540.00
849	319	7	4	160.00	0.00	640.00
850	319	10	5	520.00	130.00	2470.00
851	320	5	4	60.00	0.00	240.00
852	320	8	5	300.00	75.00	1425.00
853	320	11	1	130.00	0.00	130.00
854	320	14	2	15.00	0.00	30.00
855	321	6	5	25.00	6.25	118.75
856	321	9	1	270.00	0.00	270.00
857	322	7	1	160.00	0.00	160.00
858	322	10	2	520.00	0.00	1040.00
859	322	13	3	190.00	0.00	570.00
860	323	8	2	300.00	0.00	600.00
861	323	11	3	130.00	0.00	390.00
862	323	14	4	15.00	3.00	57.00
863	323	17	5	260.00	0.00	1300.00
864	324	9	3	270.00	0.00	810.00
865	324	12	4	180.00	36.00	684.00
866	325	10	4	520.00	104.00	1976.00
867	325	13	5	190.00	0.00	950.00
868	325	16	1	255.00	0.00	255.00
869	326	11	5	130.00	0.00	650.00
870	326	14	1	15.00	0.00	15.00
871	326	17	2	260.00	0.00	520.00
872	326	20	3	145.00	21.75	413.25
873	327	12	1	180.00	0.00	180.00
874	327	15	2	380.00	0.00	760.00
875	328	13	2	190.00	0.00	380.00
876	328	16	3	255.00	38.25	726.75
877	328	19	4	560.00	0.00	2240.00
878	329	14	1	15.00	0.75	14.25
879	330	15	4	380.00	0.00	1520.00
880	330	18	5	590.00	0.00	2950.00
881	331	16	5	255.00	0.00	1275.00
882	331	19	1	560.00	0.00	560.00
883	331	22	2	280.00	28.00	532.00
884	332	17	1	260.00	0.00	260.00
885	332	20	2	145.00	14.50	275.50
886	332	23	3	45.00	0.00	135.00
887	332	26	4	185.00	0.00	740.00
888	333	18	2	590.00	59.00	1121.00
889	333	21	3	190.00	0.00	570.00
890	334	19	3	560.00	0.00	1680.00
891	334	22	4	280.00	0.00	1120.00
892	334	25	5	28.00	0.00	140.00
893	335	20	4	145.00	0.00	580.00
894	335	23	5	45.00	0.00	225.00
895	335	26	1	185.00	9.25	175.75
896	335	29	2	390.00	0.00	780.00
897	336	21	5	190.00	0.00	950.00
898	336	24	1	18.00	0.90	17.10
899	337	22	1	280.00	14.00	266.00
900	337	25	2	28.00	0.00	56.00
901	337	28	3	340.00	0.00	1020.00
902	338	23	2	45.00	0.00	90.00
903	338	26	3	185.00	0.00	555.00
904	338	29	4	390.00	0.00	1560.00
905	338	2	5	240.00	60.00	1140.00
906	339	24	3	18.00	0.00	54.00
907	339	27	4	195.00	0.00	780.00
908	340	25	1	28.00	0.00	28.00
909	341	26	5	185.00	46.25	878.75
910	341	29	1	390.00	0.00	390.00
911	341	2	2	240.00	0.00	480.00
912	341	5	3	60.00	0.00	180.00
913	342	27	1	195.00	0.00	195.00
914	342	30	2	120.00	0.00	240.00
915	343	28	2	340.00	0.00	680.00
916	343	1	3	250.00	0.00	750.00
917	343	4	4	180.00	36.00	684.00
918	344	29	3	390.00	0.00	1170.00
919	344	2	4	240.00	48.00	912.00
920	344	5	5	60.00	0.00	300.00
921	344	8	1	300.00	0.00	300.00
922	345	30	4	120.00	24.00	456.00
923	345	3	5	550.00	0.00	2750.00
924	346	1	5	250.00	0.00	1250.00
925	346	4	1	180.00	0.00	180.00
926	346	7	2	160.00	0.00	320.00
927	347	2	1	240.00	0.00	240.00
928	347	5	2	60.00	0.00	120.00
929	347	8	3	300.00	45.00	855.00
930	347	11	4	130.00	0.00	520.00
931	348	3	2	550.00	0.00	1100.00
932	348	6	3	25.00	3.75	71.25
933	349	4	3	180.00	27.00	513.00
934	349	7	4	160.00	0.00	640.00
935	349	10	5	520.00	0.00	2600.00
936	350	5	4	60.00	0.00	240.00
937	350	8	5	300.00	0.00	1500.00
938	350	11	1	130.00	0.00	130.00
939	350	14	2	15.00	1.50	28.50
940	351	6	1	25.00	0.00	25.00
941	352	7	1	160.00	0.00	160.00
942	352	10	2	520.00	52.00	988.00
943	352	13	3	190.00	0.00	570.00
944	353	8	2	300.00	30.00	570.00
945	353	11	3	130.00	0.00	390.00
946	353	14	4	15.00	0.00	60.00
947	353	17	5	260.00	0.00	1300.00
948	354	9	3	270.00	0.00	810.00
949	354	12	4	180.00	0.00	720.00
950	355	10	4	520.00	0.00	2080.00
951	355	13	5	190.00	0.00	950.00
952	355	16	1	255.00	12.75	242.25
953	356	11	5	130.00	0.00	650.00
954	356	14	1	15.00	0.75	14.25
955	356	17	2	260.00	0.00	520.00
956	356	20	3	145.00	0.00	435.00
957	357	12	1	180.00	9.00	171.00
958	357	15	2	380.00	0.00	760.00
959	358	13	2	190.00	0.00	380.00
960	358	16	3	255.00	0.00	765.00
961	358	19	4	560.00	0.00	2240.00
962	359	14	3	15.00	0.00	45.00
963	359	17	4	260.00	0.00	1040.00
964	359	20	5	145.00	36.25	688.75
965	359	23	1	45.00	0.00	45.00
966	360	15	4	380.00	0.00	1520.00
967	360	18	5	590.00	147.50	2802.50
968	361	16	5	255.00	63.75	1211.25
969	361	19	1	560.00	0.00	560.00
970	361	22	2	280.00	0.00	560.00
971	362	17	1	260.00	0.00	260.00
972	363	18	2	590.00	0.00	1180.00
973	363	21	3	190.00	0.00	570.00
974	364	19	3	560.00	0.00	1680.00
975	364	22	4	280.00	56.00	1064.00
976	364	25	5	28.00	0.00	140.00
977	365	20	4	145.00	29.00	551.00
978	365	23	5	45.00	0.00	225.00
979	365	26	1	185.00	0.00	185.00
980	365	29	2	390.00	0.00	780.00
981	366	21	5	190.00	0.00	950.00
982	366	24	1	18.00	0.00	18.00
983	367	22	1	280.00	0.00	280.00
984	367	25	2	28.00	0.00	56.00
985	367	28	3	340.00	51.00	969.00
986	368	23	2	45.00	0.00	90.00
987	368	26	3	185.00	27.75	527.25
988	368	29	4	390.00	0.00	1560.00
989	368	2	5	240.00	0.00	1200.00
990	369	24	3	18.00	2.70	51.30
991	369	27	4	195.00	0.00	780.00
992	370	25	4	28.00	0.00	112.00
993	370	28	5	340.00	0.00	1700.00
994	370	1	1	250.00	0.00	250.00
995	371	26	5	185.00	0.00	925.00
996	371	29	1	390.00	0.00	390.00
997	371	2	2	240.00	24.00	456.00
998	371	5	3	60.00	0.00	180.00
999	372	27	1	195.00	0.00	195.00
1000	372	30	2	120.00	12.00	228.00
1001	373	28	1	340.00	17.00	323.00
1002	374	29	3	390.00	0.00	1170.00
1003	374	2	4	240.00	0.00	960.00
1004	374	5	5	60.00	0.00	300.00
1005	374	8	1	300.00	15.00	285.00
1006	375	30	4	120.00	0.00	480.00
1007	375	3	5	550.00	0.00	2750.00
1008	376	1	5	250.00	0.00	1250.00
1009	376	4	1	180.00	9.00	171.00
1010	376	7	2	160.00	0.00	320.00
1011	377	2	1	240.00	12.00	228.00
1012	377	5	2	60.00	0.00	120.00
1013	377	8	3	300.00	0.00	900.00
1014	377	11	4	130.00	0.00	520.00
1015	378	3	2	550.00	0.00	1100.00
1016	378	6	3	25.00	0.00	75.00
1017	379	4	3	180.00	0.00	540.00
1018	379	7	4	160.00	0.00	640.00
1019	379	10	5	520.00	130.00	2470.00
1020	380	5	4	60.00	0.00	240.00
1021	380	8	5	300.00	75.00	1425.00
1022	380	11	1	130.00	0.00	130.00
1023	380	14	2	15.00	0.00	30.00
1024	381	6	5	25.00	6.25	118.75
1025	381	9	1	270.00	0.00	270.00
1026	382	7	1	160.00	0.00	160.00
1027	382	10	2	520.00	0.00	1040.00
1028	382	13	3	190.00	0.00	570.00
1029	383	8	2	300.00	0.00	600.00
1030	383	11	3	130.00	0.00	390.00
1031	383	14	4	15.00	3.00	57.00
1032	383	17	5	260.00	0.00	1300.00
1033	384	9	1	270.00	0.00	270.00
1034	385	10	4	520.00	104.00	1976.00
1035	385	13	5	190.00	0.00	950.00
1036	385	16	1	255.00	0.00	255.00
1037	386	11	5	130.00	0.00	650.00
1038	386	14	1	15.00	0.00	15.00
1039	386	17	2	260.00	0.00	520.00
1040	386	20	3	145.00	21.75	413.25
1041	387	12	1	180.00	0.00	180.00
1042	387	15	2	380.00	0.00	760.00
1043	388	13	2	190.00	0.00	380.00
1044	388	16	3	255.00	38.25	726.75
1045	388	19	4	560.00	0.00	2240.00
1046	389	14	3	15.00	2.25	42.75
1047	389	17	4	260.00	0.00	1040.00
1048	389	20	5	145.00	0.00	725.00
1049	389	23	1	45.00	0.00	45.00
1050	390	15	4	380.00	0.00	1520.00
1051	390	18	5	590.00	0.00	2950.00
1052	391	16	5	255.00	0.00	1275.00
1053	391	19	1	560.00	0.00	560.00
1054	391	22	2	280.00	28.00	532.00
1055	392	17	1	260.00	0.00	260.00
1056	392	20	2	145.00	14.50	275.50
1057	392	23	3	45.00	0.00	135.00
1058	392	26	4	185.00	0.00	740.00
1059	393	18	2	590.00	59.00	1121.00
1060	393	21	3	190.00	0.00	570.00
1061	394	19	3	560.00	0.00	1680.00
1062	394	22	4	280.00	0.00	1120.00
1063	394	25	5	28.00	0.00	140.00
1064	395	20	1	145.00	0.00	145.00
1065	396	21	5	190.00	0.00	950.00
1066	396	24	1	18.00	0.90	17.10
1067	397	22	1	280.00	14.00	266.00
1068	397	25	2	28.00	0.00	56.00
1069	397	28	3	340.00	0.00	1020.00
1070	398	23	2	45.00	0.00	90.00
1071	398	26	3	185.00	0.00	555.00
1072	398	29	4	390.00	0.00	1560.00
1073	398	2	5	240.00	60.00	1140.00
1074	399	24	3	18.00	0.00	54.00
1075	399	27	4	195.00	0.00	780.00
1076	400	25	4	28.00	0.00	112.00
1077	400	28	5	340.00	85.00	1615.00
1078	400	1	1	250.00	0.00	250.00
1079	401	26	5	185.00	46.25	878.75
1080	401	29	1	390.00	0.00	390.00
1081	401	2	2	240.00	0.00	480.00
1082	401	5	3	60.00	0.00	180.00
1083	402	27	1	195.00	0.00	195.00
1084	402	30	2	120.00	0.00	240.00
1085	403	28	2	340.00	0.00	680.00
1086	403	1	3	250.00	0.00	750.00
1087	403	4	4	180.00	36.00	684.00
1088	404	29	3	390.00	0.00	1170.00
1089	404	2	4	240.00	48.00	912.00
1090	404	5	5	60.00	0.00	300.00
1091	404	8	1	300.00	0.00	300.00
1092	405	30	4	120.00	24.00	456.00
1093	405	3	5	550.00	0.00	2750.00
1094	406	1	1	250.00	0.00	250.00
1095	407	2	1	240.00	0.00	240.00
1096	407	5	2	60.00	0.00	120.00
1097	407	8	3	300.00	45.00	855.00
1098	407	11	4	130.00	0.00	520.00
1099	408	3	2	550.00	0.00	1100.00
1100	408	6	3	25.00	3.75	71.25
1101	1	1	2	250.00	0.00	500.00
1102	2	6	20	25.00	40.00	460.00
1103	2	4	2	180.00	0.00	360.00
1104	3	3	2	550.00	100.00	1000.00
1105	3	8	2	300.00	50.00	550.00
1106	4	5	5	60.00	0.00	300.00
1107	5	1	3	250.00	30.00	720.00
1108	5	6	4	25.00	10.00	90.00
1109	6	3	4	550.00	200.00	2000.00
1110	6	7	10	160.00	160.00	1440.00
1111	7	2	2	240.00	0.00	480.00
1112	7	14	10	15.00	0.00	150.00
1113	8	9	2	270.00	40.00	500.00
1114	8	11	2	130.00	0.00	260.00
1115	9	10	3	520.00	60.00	1500.00
1116	9	15	2	380.00	40.00	720.00
1117	10	5	6	60.00	0.00	360.00
1118	10	6	16	25.00	50.00	350.00
1119	11	12	4	180.00	40.00	680.00
1120	11	1	1	250.00	0.00	250.00
1121	12	13	5	190.00	50.00	900.00
1122	12	8	1	300.00	0.00	300.00
1123	421	4	3	180.00	0.00	540.00
1124	421	7	4	160.00	0.00	640.00
1125	421	10	5	520.00	130.00	2470.00
1126	422	5	4	60.00	0.00	240.00
1127	422	8	5	300.00	75.00	1425.00
1128	422	11	1	130.00	0.00	130.00
1129	422	14	2	15.00	0.00	30.00
1130	423	6	5	25.00	6.25	118.75
1131	423	9	1	270.00	0.00	270.00
1132	424	7	1	160.00	0.00	160.00
1133	424	10	2	520.00	0.00	1040.00
1134	424	13	3	190.00	0.00	570.00
1135	425	8	2	300.00	0.00	600.00
1136	425	11	3	130.00	0.00	390.00
1137	425	14	4	15.00	3.00	57.00
1138	425	17	5	260.00	0.00	1300.00
1139	426	9	3	270.00	0.00	810.00
1140	426	12	4	180.00	36.00	684.00
1141	427	10	4	520.00	104.00	1976.00
1142	427	13	5	190.00	0.00	950.00
1143	427	16	1	255.00	0.00	255.00
1144	428	11	5	130.00	0.00	650.00
1145	428	14	1	15.00	0.00	15.00
1146	428	17	2	260.00	0.00	520.00
1147	428	20	3	145.00	21.75	413.25
1148	429	12	1	180.00	0.00	180.00
1149	429	15	2	380.00	0.00	760.00
1150	430	13	2	190.00	0.00	380.00
1151	430	16	3	255.00	38.25	726.75
1152	430	19	4	560.00	0.00	2240.00
1153	431	14	1	15.00	0.75	14.25
1154	432	15	4	380.00	0.00	1520.00
1155	432	18	5	590.00	0.00	2950.00
1156	433	16	5	255.00	0.00	1275.00
1157	433	19	1	560.00	0.00	560.00
1158	433	22	2	280.00	28.00	532.00
1159	434	17	1	260.00	0.00	260.00
1160	434	20	2	145.00	14.50	275.50
1161	434	23	3	45.00	0.00	135.00
1162	434	26	4	185.00	0.00	740.00
1163	435	18	2	590.00	59.00	1121.00
1164	435	21	3	190.00	0.00	570.00
1165	436	19	3	560.00	0.00	1680.00
1166	436	22	4	280.00	0.00	1120.00
1167	436	25	5	28.00	0.00	140.00
1168	437	20	4	145.00	0.00	580.00
1169	437	23	5	45.00	0.00	225.00
1170	437	26	1	185.00	9.25	175.75
1171	437	29	2	390.00	0.00	780.00
1172	438	21	5	190.00	0.00	950.00
1173	438	24	1	18.00	0.90	17.10
1174	439	22	1	280.00	14.00	266.00
1175	439	25	2	28.00	0.00	56.00
1176	439	28	3	340.00	0.00	1020.00
1177	440	23	2	45.00	0.00	90.00
1178	440	26	3	185.00	0.00	555.00
1179	440	29	4	390.00	0.00	1560.00
1180	440	2	5	240.00	60.00	1140.00
1181	441	24	3	18.00	0.00	54.00
1182	441	27	4	195.00	0.00	780.00
1183	442	25	1	28.00	0.00	28.00
1184	443	26	5	185.00	46.25	878.75
1185	443	29	1	390.00	0.00	390.00
1186	443	2	2	240.00	0.00	480.00
1187	443	5	3	60.00	0.00	180.00
1188	444	27	1	195.00	0.00	195.00
1189	444	30	2	120.00	0.00	240.00
1190	445	28	2	340.00	0.00	680.00
1191	445	1	3	250.00	0.00	750.00
1192	445	4	4	180.00	36.00	684.00
1193	446	29	3	390.00	0.00	1170.00
1194	446	2	4	240.00	48.00	912.00
1195	446	5	5	60.00	0.00	300.00
1196	446	8	1	300.00	0.00	300.00
1197	447	30	4	120.00	24.00	456.00
1198	447	3	5	550.00	0.00	2750.00
1199	448	1	5	250.00	0.00	1250.00
1200	448	4	1	180.00	0.00	180.00
1201	448	7	2	160.00	0.00	320.00
1202	449	2	1	240.00	0.00	240.00
1203	449	5	2	60.00	0.00	120.00
1204	449	8	3	300.00	45.00	855.00
1205	449	11	4	130.00	0.00	520.00
1206	450	3	2	550.00	0.00	1100.00
1207	450	6	3	25.00	3.75	71.25
1208	451	4	3	180.00	27.00	513.00
1209	451	7	4	160.00	0.00	640.00
1210	451	10	5	520.00	0.00	2600.00
1211	452	5	4	60.00	0.00	240.00
1212	452	8	5	300.00	0.00	1500.00
1213	452	11	1	130.00	0.00	130.00
1214	452	14	2	15.00	1.50	28.50
1215	453	6	1	25.00	0.00	25.00
1216	454	7	1	160.00	0.00	160.00
1217	454	10	2	520.00	52.00	988.00
1218	454	13	3	190.00	0.00	570.00
1219	455	8	2	300.00	30.00	570.00
1220	455	11	3	130.00	0.00	390.00
1221	455	14	4	15.00	0.00	60.00
1222	455	17	5	260.00	0.00	1300.00
1223	456	9	3	270.00	0.00	810.00
1224	456	12	4	180.00	0.00	720.00
1225	457	10	4	520.00	0.00	2080.00
1226	457	13	5	190.00	0.00	950.00
1227	457	16	1	255.00	12.75	242.25
1228	458	11	5	130.00	0.00	650.00
1229	458	14	1	15.00	0.75	14.25
1230	458	17	2	260.00	0.00	520.00
1231	458	20	3	145.00	0.00	435.00
1232	459	12	1	180.00	9.00	171.00
1233	459	15	2	380.00	0.00	760.00
1234	460	13	2	190.00	0.00	380.00
1235	460	16	3	255.00	0.00	765.00
1236	460	19	4	560.00	0.00	2240.00
1237	461	14	3	15.00	0.00	45.00
1238	461	17	4	260.00	0.00	1040.00
1239	461	20	5	145.00	36.25	688.75
1240	461	23	1	45.00	0.00	45.00
1241	462	15	4	380.00	0.00	1520.00
1242	462	18	5	590.00	147.50	2802.50
1243	463	16	5	255.00	63.75	1211.25
1244	463	19	1	560.00	0.00	560.00
1245	463	22	2	280.00	0.00	560.00
1246	464	17	1	260.00	0.00	260.00
1247	465	18	2	590.00	0.00	1180.00
1248	465	21	3	190.00	0.00	570.00
1249	466	19	3	560.00	0.00	1680.00
1250	466	22	4	280.00	56.00	1064.00
1251	466	25	5	28.00	0.00	140.00
1252	467	20	4	145.00	29.00	551.00
1253	467	23	5	45.00	0.00	225.00
1254	467	26	1	185.00	0.00	185.00
1255	467	29	2	390.00	0.00	780.00
1256	468	21	5	190.00	0.00	950.00
1257	468	24	1	18.00	0.00	18.00
1258	469	22	1	280.00	0.00	280.00
1259	469	25	2	28.00	0.00	56.00
1260	469	28	3	340.00	51.00	969.00
1261	470	23	2	45.00	0.00	90.00
1262	470	26	3	185.00	27.75	527.25
1263	470	29	4	390.00	0.00	1560.00
1264	470	2	5	240.00	0.00	1200.00
1265	471	24	3	18.00	2.70	51.30
1266	471	27	4	195.00	0.00	780.00
1267	472	25	4	28.00	0.00	112.00
1268	472	28	5	340.00	0.00	1700.00
1269	472	1	1	250.00	0.00	250.00
1270	473	26	5	185.00	0.00	925.00
1271	473	29	1	390.00	0.00	390.00
1272	473	2	2	240.00	24.00	456.00
1273	473	5	3	60.00	0.00	180.00
1274	474	27	1	195.00	0.00	195.00
1275	474	30	2	120.00	12.00	228.00
1276	475	28	1	340.00	17.00	323.00
1277	476	29	3	390.00	0.00	1170.00
1278	476	2	4	240.00	0.00	960.00
1279	476	5	5	60.00	0.00	300.00
1280	476	8	1	300.00	15.00	285.00
1281	477	30	4	120.00	0.00	480.00
1282	477	3	5	550.00	0.00	2750.00
1283	478	1	5	250.00	0.00	1250.00
1284	478	4	1	180.00	9.00	171.00
1285	478	7	2	160.00	0.00	320.00
1286	479	2	1	240.00	12.00	228.00
1287	479	5	2	60.00	0.00	120.00
1288	479	8	3	300.00	0.00	900.00
1289	479	11	4	130.00	0.00	520.00
1290	480	3	2	550.00	0.00	1100.00
1291	480	6	3	25.00	0.00	75.00
1292	481	4	3	180.00	0.00	540.00
1293	481	7	4	160.00	0.00	640.00
1294	481	10	5	520.00	130.00	2470.00
1295	482	5	4	60.00	0.00	240.00
1296	482	8	5	300.00	75.00	1425.00
1297	482	11	1	130.00	0.00	130.00
1298	482	14	2	15.00	0.00	30.00
1299	483	6	5	25.00	6.25	118.75
1300	483	9	1	270.00	0.00	270.00
1301	484	7	1	160.00	0.00	160.00
1302	484	10	2	520.00	0.00	1040.00
1303	484	13	3	190.00	0.00	570.00
1304	485	8	2	300.00	0.00	600.00
1305	485	11	3	130.00	0.00	390.00
1306	485	14	4	15.00	3.00	57.00
1307	485	17	5	260.00	0.00	1300.00
1308	486	9	1	270.00	0.00	270.00
1309	487	10	4	520.00	104.00	1976.00
1310	487	13	5	190.00	0.00	950.00
1311	487	16	1	255.00	0.00	255.00
1312	488	11	5	130.00	0.00	650.00
1313	488	14	1	15.00	0.00	15.00
1314	488	17	2	260.00	0.00	520.00
1315	488	20	3	145.00	21.75	413.25
1316	489	12	1	180.00	0.00	180.00
1317	489	15	2	380.00	0.00	760.00
1318	490	13	2	190.00	0.00	380.00
1319	490	16	3	255.00	38.25	726.75
1320	490	19	4	560.00	0.00	2240.00
1321	491	14	3	15.00	2.25	42.75
1322	491	17	4	260.00	0.00	1040.00
1323	491	20	5	145.00	0.00	725.00
1324	491	23	1	45.00	0.00	45.00
1325	492	15	4	380.00	0.00	1520.00
1326	492	18	5	590.00	0.00	2950.00
1327	493	16	5	255.00	0.00	1275.00
1328	493	19	1	560.00	0.00	560.00
1329	493	22	2	280.00	28.00	532.00
1330	494	17	1	260.00	0.00	260.00
1331	494	20	2	145.00	14.50	275.50
1332	494	23	3	45.00	0.00	135.00
1333	494	26	4	185.00	0.00	740.00
1334	495	18	2	590.00	59.00	1121.00
1335	495	21	3	190.00	0.00	570.00
1336	496	19	3	560.00	0.00	1680.00
1337	496	22	4	280.00	0.00	1120.00
1338	496	25	5	28.00	0.00	140.00
1339	497	20	1	145.00	0.00	145.00
1340	498	21	5	190.00	0.00	950.00
1341	498	24	1	18.00	0.90	17.10
1342	499	22	1	280.00	14.00	266.00
1343	499	25	2	28.00	0.00	56.00
1344	499	28	3	340.00	0.00	1020.00
1345	500	23	2	45.00	0.00	90.00
1346	500	26	3	185.00	0.00	555.00
1347	500	29	4	390.00	0.00	1560.00
1348	500	2	5	240.00	60.00	1140.00
1349	501	24	3	18.00	0.00	54.00
1350	501	27	4	195.00	0.00	780.00
1351	502	25	4	28.00	0.00	112.00
1352	502	28	5	340.00	85.00	1615.00
1353	502	1	1	250.00	0.00	250.00
1354	503	26	5	185.00	46.25	878.75
1355	503	29	1	390.00	0.00	390.00
1356	503	2	2	240.00	0.00	480.00
1357	503	5	3	60.00	0.00	180.00
1358	504	27	1	195.00	0.00	195.00
1359	504	30	2	120.00	0.00	240.00
1360	505	28	2	340.00	0.00	680.00
1361	505	1	3	250.00	0.00	750.00
1362	505	4	4	180.00	36.00	684.00
1363	506	29	3	390.00	0.00	1170.00
1364	506	2	4	240.00	48.00	912.00
1365	506	5	5	60.00	0.00	300.00
1366	506	8	1	300.00	0.00	300.00
1367	507	30	4	120.00	24.00	456.00
1368	507	3	5	550.00	0.00	2750.00
1369	508	1	1	250.00	0.00	250.00
1370	509	2	1	240.00	0.00	240.00
1371	509	5	2	60.00	0.00	120.00
1372	509	8	3	300.00	45.00	855.00
1373	509	11	4	130.00	0.00	520.00
1374	510	3	2	550.00	0.00	1100.00
1375	510	6	3	25.00	3.75	71.25
1376	1	1	2	250.00	0.00	500.00
1377	2	6	20	25.00	40.00	460.00
1378	2	4	2	180.00	0.00	360.00
1379	3	3	2	550.00	100.00	1000.00
1380	3	8	2	300.00	50.00	550.00
1381	4	5	5	60.00	0.00	300.00
1382	5	1	3	250.00	30.00	720.00
1383	5	6	4	25.00	10.00	90.00
1384	6	3	4	550.00	200.00	2000.00
1385	6	7	10	160.00	160.00	1440.00
1386	7	2	2	240.00	0.00	480.00
1387	7	14	10	15.00	0.00	150.00
1388	8	9	2	270.00	40.00	500.00
1389	8	11	2	130.00	0.00	260.00
1390	9	10	3	520.00	60.00	1500.00
1391	9	15	2	380.00	40.00	720.00
1392	10	5	6	60.00	0.00	360.00
1393	10	6	16	25.00	50.00	350.00
1394	11	12	4	180.00	40.00	680.00
1395	11	1	1	250.00	0.00	250.00
1396	12	13	5	190.00	50.00	900.00
1397	12	8	1	300.00	0.00	300.00
1398	523	4	3	180.00	0.00	540.00
1399	523	7	4	160.00	0.00	640.00
1400	523	10	5	520.00	130.00	2470.00
1401	524	5	4	60.00	0.00	240.00
1402	524	8	5	300.00	75.00	1425.00
1403	524	11	1	130.00	0.00	130.00
1404	524	14	2	15.00	0.00	30.00
1405	525	6	5	25.00	6.25	118.75
1406	525	9	1	270.00	0.00	270.00
1407	526	7	1	160.00	0.00	160.00
1408	526	10	2	520.00	0.00	1040.00
1409	526	13	3	190.00	0.00	570.00
1410	527	8	2	300.00	0.00	600.00
1411	527	11	3	130.00	0.00	390.00
1412	527	14	4	15.00	3.00	57.00
1413	527	17	5	260.00	0.00	1300.00
1414	528	9	3	270.00	0.00	810.00
1415	528	12	4	180.00	36.00	684.00
1416	529	10	4	520.00	104.00	1976.00
1417	529	13	5	190.00	0.00	950.00
1418	529	16	1	255.00	0.00	255.00
1419	530	11	5	130.00	0.00	650.00
1420	530	14	1	15.00	0.00	15.00
1421	530	17	2	260.00	0.00	520.00
1422	530	20	3	145.00	21.75	413.25
1423	531	12	1	180.00	0.00	180.00
1424	531	15	2	380.00	0.00	760.00
1425	532	13	2	190.00	0.00	380.00
1426	532	16	3	255.00	38.25	726.75
1427	532	19	4	560.00	0.00	2240.00
1428	533	14	1	15.00	0.75	14.25
1429	534	15	4	380.00	0.00	1520.00
1430	534	18	5	590.00	0.00	2950.00
1431	535	16	5	255.00	0.00	1275.00
1432	535	19	1	560.00	0.00	560.00
1433	535	22	2	280.00	28.00	532.00
1434	536	17	1	260.00	0.00	260.00
1435	536	20	2	145.00	14.50	275.50
1436	536	23	3	45.00	0.00	135.00
1437	536	26	4	185.00	0.00	740.00
1438	537	18	2	590.00	59.00	1121.00
1439	537	21	3	190.00	0.00	570.00
1440	538	19	3	560.00	0.00	1680.00
1441	538	22	4	280.00	0.00	1120.00
1442	538	25	5	28.00	0.00	140.00
1443	539	20	4	145.00	0.00	580.00
1444	539	23	5	45.00	0.00	225.00
1445	539	26	1	185.00	9.25	175.75
1446	539	29	2	390.00	0.00	780.00
1447	540	21	5	190.00	0.00	950.00
1448	540	24	1	18.00	0.90	17.10
1449	541	22	1	280.00	14.00	266.00
1450	541	25	2	28.00	0.00	56.00
1451	541	28	3	340.00	0.00	1020.00
1452	542	23	2	45.00	0.00	90.00
1453	542	26	3	185.00	0.00	555.00
1454	542	29	4	390.00	0.00	1560.00
1455	542	2	5	240.00	60.00	1140.00
1456	543	24	3	18.00	0.00	54.00
1457	543	27	4	195.00	0.00	780.00
1458	544	25	1	28.00	0.00	28.00
1459	545	26	5	185.00	46.25	878.75
1460	545	29	1	390.00	0.00	390.00
1461	545	2	2	240.00	0.00	480.00
1462	545	5	3	60.00	0.00	180.00
1463	546	27	1	195.00	0.00	195.00
1464	546	30	2	120.00	0.00	240.00
1465	547	28	2	340.00	0.00	680.00
1466	547	1	3	250.00	0.00	750.00
1467	547	4	4	180.00	36.00	684.00
1468	548	29	3	390.00	0.00	1170.00
1469	548	2	4	240.00	48.00	912.00
1470	548	5	5	60.00	0.00	300.00
1471	548	8	1	300.00	0.00	300.00
1472	549	30	4	120.00	24.00	456.00
1473	549	3	5	550.00	0.00	2750.00
1474	550	1	5	250.00	0.00	1250.00
1475	550	4	1	180.00	0.00	180.00
1476	550	7	2	160.00	0.00	320.00
1477	551	2	1	240.00	0.00	240.00
1478	551	5	2	60.00	0.00	120.00
1479	551	8	3	300.00	45.00	855.00
1480	551	11	4	130.00	0.00	520.00
1481	552	3	2	550.00	0.00	1100.00
1482	552	6	3	25.00	3.75	71.25
1483	553	4	3	180.00	27.00	513.00
1484	553	7	4	160.00	0.00	640.00
1485	553	10	5	520.00	0.00	2600.00
1486	554	5	4	60.00	0.00	240.00
1487	554	8	5	300.00	0.00	1500.00
1488	554	11	1	130.00	0.00	130.00
1489	554	14	2	15.00	1.50	28.50
1490	555	6	1	25.00	0.00	25.00
1491	556	7	1	160.00	0.00	160.00
1492	556	10	2	520.00	52.00	988.00
1493	556	13	3	190.00	0.00	570.00
1494	557	8	2	300.00	30.00	570.00
1495	557	11	3	130.00	0.00	390.00
1496	557	14	4	15.00	0.00	60.00
1497	557	17	5	260.00	0.00	1300.00
1498	558	9	3	270.00	0.00	810.00
1499	558	12	4	180.00	0.00	720.00
1500	559	10	4	520.00	0.00	2080.00
1501	559	13	5	190.00	0.00	950.00
1502	559	16	1	255.00	12.75	242.25
1503	560	11	5	130.00	0.00	650.00
1504	560	14	1	15.00	0.75	14.25
1505	560	17	2	260.00	0.00	520.00
1506	560	20	3	145.00	0.00	435.00
1507	561	12	1	180.00	9.00	171.00
1508	561	15	2	380.00	0.00	760.00
1509	562	13	2	190.00	0.00	380.00
1510	562	16	3	255.00	0.00	765.00
1511	562	19	4	560.00	0.00	2240.00
1512	563	14	3	15.00	0.00	45.00
1513	563	17	4	260.00	0.00	1040.00
1514	563	20	5	145.00	36.25	688.75
1515	563	23	1	45.00	0.00	45.00
1516	564	15	4	380.00	0.00	1520.00
1517	564	18	5	590.00	147.50	2802.50
1518	565	16	5	255.00	63.75	1211.25
1519	565	19	1	560.00	0.00	560.00
1520	565	22	2	280.00	0.00	560.00
1521	566	17	1	260.00	0.00	260.00
1522	567	18	2	590.00	0.00	1180.00
1523	567	21	3	190.00	0.00	570.00
1524	568	19	3	560.00	0.00	1680.00
1525	568	22	4	280.00	56.00	1064.00
1526	568	25	5	28.00	0.00	140.00
1527	569	20	4	145.00	29.00	551.00
1528	569	23	5	45.00	0.00	225.00
1529	569	26	1	185.00	0.00	185.00
1530	569	29	2	390.00	0.00	780.00
1531	570	21	5	190.00	0.00	950.00
1532	570	24	1	18.00	0.00	18.00
1533	571	22	1	280.00	0.00	280.00
1534	571	25	2	28.00	0.00	56.00
1535	571	28	3	340.00	51.00	969.00
1536	572	23	2	45.00	0.00	90.00
1537	572	26	3	185.00	27.75	527.25
1538	572	29	4	390.00	0.00	1560.00
1539	572	2	5	240.00	0.00	1200.00
1540	573	24	3	18.00	2.70	51.30
1541	573	27	4	195.00	0.00	780.00
1542	574	25	4	28.00	0.00	112.00
1543	574	28	5	340.00	0.00	1700.00
1544	574	1	1	250.00	0.00	250.00
1545	575	26	5	185.00	0.00	925.00
1546	575	29	1	390.00	0.00	390.00
1547	575	2	2	240.00	24.00	456.00
1548	575	5	3	60.00	0.00	180.00
1549	576	27	1	195.00	0.00	195.00
1550	576	30	2	120.00	12.00	228.00
1551	577	28	1	340.00	17.00	323.00
1552	578	29	3	390.00	0.00	1170.00
1553	578	2	4	240.00	0.00	960.00
1554	578	5	5	60.00	0.00	300.00
1555	578	8	1	300.00	15.00	285.00
1556	579	30	4	120.00	0.00	480.00
1557	579	3	5	550.00	0.00	2750.00
1558	580	1	5	250.00	0.00	1250.00
1559	580	4	1	180.00	9.00	171.00
1560	580	7	2	160.00	0.00	320.00
1561	581	2	1	240.00	12.00	228.00
1562	581	5	2	60.00	0.00	120.00
1563	581	8	3	300.00	0.00	900.00
1564	581	11	4	130.00	0.00	520.00
1565	582	3	2	550.00	0.00	1100.00
1566	582	6	3	25.00	0.00	75.00
1567	583	4	3	180.00	0.00	540.00
1568	583	7	4	160.00	0.00	640.00
1569	583	10	5	520.00	130.00	2470.00
1570	584	5	4	60.00	0.00	240.00
1571	584	8	5	300.00	75.00	1425.00
1572	584	11	1	130.00	0.00	130.00
1573	584	14	2	15.00	0.00	30.00
1574	585	6	5	25.00	6.25	118.75
1575	585	9	1	270.00	0.00	270.00
1576	586	7	1	160.00	0.00	160.00
1577	586	10	2	520.00	0.00	1040.00
1578	586	13	3	190.00	0.00	570.00
1579	587	8	2	300.00	0.00	600.00
1580	587	11	3	130.00	0.00	390.00
1581	587	14	4	15.00	3.00	57.00
1582	587	17	5	260.00	0.00	1300.00
1583	588	9	1	270.00	0.00	270.00
1584	589	10	4	520.00	104.00	1976.00
1585	589	13	5	190.00	0.00	950.00
1586	589	16	1	255.00	0.00	255.00
1587	590	11	5	130.00	0.00	650.00
1588	590	14	1	15.00	0.00	15.00
1589	590	17	2	260.00	0.00	520.00
1590	590	20	3	145.00	21.75	413.25
1591	591	12	1	180.00	0.00	180.00
1592	591	15	2	380.00	0.00	760.00
1593	592	13	2	190.00	0.00	380.00
1594	592	16	3	255.00	38.25	726.75
1595	592	19	4	560.00	0.00	2240.00
1596	593	14	3	15.00	2.25	42.75
1597	593	17	4	260.00	0.00	1040.00
1598	593	20	5	145.00	0.00	725.00
1599	593	23	1	45.00	0.00	45.00
1600	594	15	4	380.00	0.00	1520.00
1601	594	18	5	590.00	0.00	2950.00
1602	595	16	5	255.00	0.00	1275.00
1603	595	19	1	560.00	0.00	560.00
1604	595	22	2	280.00	28.00	532.00
1605	596	17	1	260.00	0.00	260.00
1606	596	20	2	145.00	14.50	275.50
1607	596	23	3	45.00	0.00	135.00
1608	596	26	4	185.00	0.00	740.00
1609	597	18	2	590.00	59.00	1121.00
1610	597	21	3	190.00	0.00	570.00
1611	598	19	3	560.00	0.00	1680.00
1612	598	22	4	280.00	0.00	1120.00
1613	598	25	5	28.00	0.00	140.00
1614	599	20	1	145.00	0.00	145.00
1615	600	21	5	190.00	0.00	950.00
1616	600	24	1	18.00	0.90	17.10
1617	601	22	1	280.00	14.00	266.00
1618	601	25	2	28.00	0.00	56.00
1619	601	28	3	340.00	0.00	1020.00
1620	602	23	2	45.00	0.00	90.00
1621	602	26	3	185.00	0.00	555.00
1622	602	29	4	390.00	0.00	1560.00
1623	602	2	5	240.00	60.00	1140.00
1624	603	24	3	18.00	0.00	54.00
1625	603	27	4	195.00	0.00	780.00
1626	604	25	4	28.00	0.00	112.00
1627	604	28	5	340.00	85.00	1615.00
1628	604	1	1	250.00	0.00	250.00
1629	605	26	5	185.00	46.25	878.75
1630	605	29	1	390.00	0.00	390.00
1631	605	2	2	240.00	0.00	480.00
1632	605	5	3	60.00	0.00	180.00
1633	606	27	1	195.00	0.00	195.00
1634	606	30	2	120.00	0.00	240.00
1635	607	28	2	340.00	0.00	680.00
1636	607	1	3	250.00	0.00	750.00
1637	607	4	4	180.00	36.00	684.00
1638	608	29	3	390.00	0.00	1170.00
1639	608	2	4	240.00	48.00	912.00
1640	608	5	5	60.00	0.00	300.00
1641	608	8	1	300.00	0.00	300.00
1642	609	30	4	120.00	24.00	456.00
1643	609	3	5	550.00	0.00	2750.00
1644	610	1	1	250.00	0.00	250.00
1645	611	2	1	240.00	0.00	240.00
1646	611	5	2	60.00	0.00	120.00
1647	611	8	3	300.00	45.00	855.00
1648	611	11	4	130.00	0.00	520.00
1649	612	3	2	550.00	0.00	1100.00
1650	612	6	3	25.00	3.75	71.25
1651	1	1	2	250.00	0.00	500.00
1652	2	6	20	25.00	40.00	460.00
1653	2	4	2	180.00	0.00	360.00
1654	3	3	2	550.00	100.00	1000.00
1655	3	8	2	300.00	50.00	550.00
1656	4	5	5	60.00	0.00	300.00
1657	5	1	3	250.00	30.00	720.00
1658	5	6	4	25.00	10.00	90.00
1659	6	3	4	550.00	200.00	2000.00
1660	6	7	10	160.00	160.00	1440.00
1661	7	2	2	240.00	0.00	480.00
1662	7	14	10	15.00	0.00	150.00
1663	8	9	2	270.00	40.00	500.00
1664	8	11	2	130.00	0.00	260.00
1665	9	10	3	520.00	60.00	1500.00
1666	9	15	2	380.00	40.00	720.00
1667	10	5	6	60.00	0.00	360.00
1668	10	6	16	25.00	50.00	350.00
1669	11	12	4	180.00	40.00	680.00
1670	11	1	1	250.00	0.00	250.00
1671	12	13	5	190.00	50.00	900.00
1672	12	8	1	300.00	0.00	300.00
1673	625	4	3	180.00	0.00	540.00
1674	625	7	4	160.00	0.00	640.00
1675	625	10	5	520.00	130.00	2470.00
1676	626	5	4	60.00	0.00	240.00
1677	626	8	5	300.00	75.00	1425.00
1678	626	11	1	130.00	0.00	130.00
1679	626	14	2	15.00	0.00	30.00
1680	627	6	5	25.00	6.25	118.75
1681	627	9	1	270.00	0.00	270.00
1682	628	7	1	160.00	0.00	160.00
1683	628	10	2	520.00	0.00	1040.00
1684	628	13	3	190.00	0.00	570.00
1685	629	8	2	300.00	0.00	600.00
1686	629	11	3	130.00	0.00	390.00
1687	629	14	4	15.00	3.00	57.00
1688	629	17	5	260.00	0.00	1300.00
1689	630	9	3	270.00	0.00	810.00
1690	630	12	4	180.00	36.00	684.00
1691	631	10	4	520.00	104.00	1976.00
1692	631	13	5	190.00	0.00	950.00
1693	631	16	1	255.00	0.00	255.00
1694	632	11	5	130.00	0.00	650.00
1695	632	14	1	15.00	0.00	15.00
1696	632	17	2	260.00	0.00	520.00
1697	632	20	3	145.00	21.75	413.25
1698	633	12	1	180.00	0.00	180.00
1699	633	15	2	380.00	0.00	760.00
1700	634	13	2	190.00	0.00	380.00
1701	634	16	3	255.00	38.25	726.75
1702	634	19	4	560.00	0.00	2240.00
1703	635	14	1	15.00	0.75	14.25
1704	636	15	4	380.00	0.00	1520.00
1705	636	18	5	590.00	0.00	2950.00
1706	637	16	5	255.00	0.00	1275.00
1707	637	19	1	560.00	0.00	560.00
1708	637	22	2	280.00	28.00	532.00
1709	638	17	1	260.00	0.00	260.00
1710	638	20	2	145.00	14.50	275.50
1711	638	23	3	45.00	0.00	135.00
1712	638	26	4	185.00	0.00	740.00
1713	639	18	2	590.00	59.00	1121.00
1714	639	21	3	190.00	0.00	570.00
1715	640	19	3	560.00	0.00	1680.00
1716	640	22	4	280.00	0.00	1120.00
1717	640	25	5	28.00	0.00	140.00
1718	641	20	4	145.00	0.00	580.00
1719	641	23	5	45.00	0.00	225.00
1720	641	26	1	185.00	9.25	175.75
1721	641	29	2	390.00	0.00	780.00
1722	642	21	5	190.00	0.00	950.00
1723	642	24	1	18.00	0.90	17.10
1724	643	22	1	280.00	14.00	266.00
1725	643	25	2	28.00	0.00	56.00
1726	643	28	3	340.00	0.00	1020.00
1727	644	23	2	45.00	0.00	90.00
1728	644	26	3	185.00	0.00	555.00
1729	644	29	4	390.00	0.00	1560.00
1730	644	2	5	240.00	60.00	1140.00
1731	645	24	3	18.00	0.00	54.00
1732	645	27	4	195.00	0.00	780.00
1733	646	25	1	28.00	0.00	28.00
1734	647	26	5	185.00	46.25	878.75
1735	647	29	1	390.00	0.00	390.00
1736	647	2	2	240.00	0.00	480.00
1737	647	5	3	60.00	0.00	180.00
1738	648	27	1	195.00	0.00	195.00
1739	648	30	2	120.00	0.00	240.00
1740	649	28	2	340.00	0.00	680.00
1741	649	1	3	250.00	0.00	750.00
1742	649	4	4	180.00	36.00	684.00
1743	650	29	3	390.00	0.00	1170.00
1744	650	2	4	240.00	48.00	912.00
1745	650	5	5	60.00	0.00	300.00
1746	650	8	1	300.00	0.00	300.00
1747	651	30	4	120.00	24.00	456.00
1748	651	3	5	550.00	0.00	2750.00
1749	652	1	5	250.00	0.00	1250.00
1750	652	4	1	180.00	0.00	180.00
1751	652	7	2	160.00	0.00	320.00
1752	653	2	1	240.00	0.00	240.00
1753	653	5	2	60.00	0.00	120.00
1754	653	8	3	300.00	45.00	855.00
1755	653	11	4	130.00	0.00	520.00
1756	654	3	2	550.00	0.00	1100.00
1757	654	6	3	25.00	3.75	71.25
1758	655	4	3	180.00	27.00	513.00
1759	655	7	4	160.00	0.00	640.00
1760	655	10	5	520.00	0.00	2600.00
1761	656	5	4	60.00	0.00	240.00
1762	656	8	5	300.00	0.00	1500.00
1763	656	11	1	130.00	0.00	130.00
1764	656	14	2	15.00	1.50	28.50
1765	657	6	1	25.00	0.00	25.00
1766	658	7	1	160.00	0.00	160.00
1767	658	10	2	520.00	52.00	988.00
1768	658	13	3	190.00	0.00	570.00
1769	659	8	2	300.00	30.00	570.00
1770	659	11	3	130.00	0.00	390.00
1771	659	14	4	15.00	0.00	60.00
1772	659	17	5	260.00	0.00	1300.00
1773	660	9	3	270.00	0.00	810.00
1774	660	12	4	180.00	0.00	720.00
1775	661	10	4	520.00	0.00	2080.00
1776	661	13	5	190.00	0.00	950.00
1777	661	16	1	255.00	12.75	242.25
1778	662	11	5	130.00	0.00	650.00
1779	662	14	1	15.00	0.75	14.25
1780	662	17	2	260.00	0.00	520.00
1781	662	20	3	145.00	0.00	435.00
1782	663	12	1	180.00	9.00	171.00
1783	663	15	2	380.00	0.00	760.00
1784	664	13	2	190.00	0.00	380.00
1785	664	16	3	255.00	0.00	765.00
1786	664	19	4	560.00	0.00	2240.00
1787	665	14	3	15.00	0.00	45.00
1788	665	17	4	260.00	0.00	1040.00
1789	665	20	5	145.00	36.25	688.75
1790	665	23	1	45.00	0.00	45.00
1791	666	15	4	380.00	0.00	1520.00
1792	666	18	5	590.00	147.50	2802.50
1793	667	16	5	255.00	63.75	1211.25
1794	667	19	1	560.00	0.00	560.00
1795	667	22	2	280.00	0.00	560.00
1796	668	17	1	260.00	0.00	260.00
1797	669	18	2	590.00	0.00	1180.00
1798	669	21	3	190.00	0.00	570.00
1799	670	19	3	560.00	0.00	1680.00
1800	670	22	4	280.00	56.00	1064.00
1801	670	25	5	28.00	0.00	140.00
1802	671	20	4	145.00	29.00	551.00
1803	671	23	5	45.00	0.00	225.00
1804	671	26	1	185.00	0.00	185.00
1805	671	29	2	390.00	0.00	780.00
1806	672	21	5	190.00	0.00	950.00
1807	672	24	1	18.00	0.00	18.00
1808	673	22	1	280.00	0.00	280.00
1809	673	25	2	28.00	0.00	56.00
1810	673	28	3	340.00	51.00	969.00
1811	674	23	2	45.00	0.00	90.00
1812	674	26	3	185.00	27.75	527.25
1813	674	29	4	390.00	0.00	1560.00
1814	674	2	5	240.00	0.00	1200.00
1815	675	24	3	18.00	2.70	51.30
1816	675	27	4	195.00	0.00	780.00
1817	676	25	4	28.00	0.00	112.00
1818	676	28	5	340.00	0.00	1700.00
1819	676	1	1	250.00	0.00	250.00
1820	677	26	5	185.00	0.00	925.00
1821	677	29	1	390.00	0.00	390.00
1822	677	2	2	240.00	24.00	456.00
1823	677	5	3	60.00	0.00	180.00
1824	678	27	1	195.00	0.00	195.00
1825	678	30	2	120.00	12.00	228.00
1826	679	28	1	340.00	17.00	323.00
1827	680	29	3	390.00	0.00	1170.00
1828	680	2	4	240.00	0.00	960.00
1829	680	5	5	60.00	0.00	300.00
1830	680	8	1	300.00	15.00	285.00
1831	681	30	4	120.00	0.00	480.00
1832	681	3	5	550.00	0.00	2750.00
1833	682	1	5	250.00	0.00	1250.00
1834	682	4	1	180.00	9.00	171.00
1835	682	7	2	160.00	0.00	320.00
1836	683	2	1	240.00	12.00	228.00
1837	683	5	2	60.00	0.00	120.00
1838	683	8	3	300.00	0.00	900.00
1839	683	11	4	130.00	0.00	520.00
1840	684	3	2	550.00	0.00	1100.00
1841	684	6	3	25.00	0.00	75.00
1842	685	4	3	180.00	0.00	540.00
1843	685	7	4	160.00	0.00	640.00
1844	685	10	5	520.00	130.00	2470.00
1845	686	5	4	60.00	0.00	240.00
1846	686	8	5	300.00	75.00	1425.00
1847	686	11	1	130.00	0.00	130.00
1848	686	14	2	15.00	0.00	30.00
1849	687	6	5	25.00	6.25	118.75
1850	687	9	1	270.00	0.00	270.00
1851	688	7	1	160.00	0.00	160.00
1852	688	10	2	520.00	0.00	1040.00
1853	688	13	3	190.00	0.00	570.00
1854	689	8	2	300.00	0.00	600.00
1855	689	11	3	130.00	0.00	390.00
1856	689	14	4	15.00	3.00	57.00
1857	689	17	5	260.00	0.00	1300.00
1858	690	9	1	270.00	0.00	270.00
1859	691	10	4	520.00	104.00	1976.00
1860	691	13	5	190.00	0.00	950.00
1861	691	16	1	255.00	0.00	255.00
1862	692	11	5	130.00	0.00	650.00
1863	692	14	1	15.00	0.00	15.00
1864	692	17	2	260.00	0.00	520.00
1865	692	20	3	145.00	21.75	413.25
1866	693	12	1	180.00	0.00	180.00
1867	693	15	2	380.00	0.00	760.00
1868	694	13	2	190.00	0.00	380.00
1869	694	16	3	255.00	38.25	726.75
1870	694	19	4	560.00	0.00	2240.00
1871	695	14	3	15.00	2.25	42.75
1872	695	17	4	260.00	0.00	1040.00
1873	695	20	5	145.00	0.00	725.00
1874	695	23	1	45.00	0.00	45.00
1875	696	15	4	380.00	0.00	1520.00
1876	696	18	5	590.00	0.00	2950.00
1877	697	16	5	255.00	0.00	1275.00
1878	697	19	1	560.00	0.00	560.00
1879	697	22	2	280.00	28.00	532.00
1880	698	17	1	260.00	0.00	260.00
1881	698	20	2	145.00	14.50	275.50
1882	698	23	3	45.00	0.00	135.00
1883	698	26	4	185.00	0.00	740.00
1884	699	18	2	590.00	59.00	1121.00
1885	699	21	3	190.00	0.00	570.00
1886	700	19	3	560.00	0.00	1680.00
1887	700	22	4	280.00	0.00	1120.00
1888	700	25	5	28.00	0.00	140.00
1889	701	20	1	145.00	0.00	145.00
1890	702	21	5	190.00	0.00	950.00
1891	702	24	1	18.00	0.90	17.10
1892	703	22	1	280.00	14.00	266.00
1893	703	25	2	28.00	0.00	56.00
1894	703	28	3	340.00	0.00	1020.00
1895	704	23	2	45.00	0.00	90.00
1896	704	26	3	185.00	0.00	555.00
1897	704	29	4	390.00	0.00	1560.00
1898	704	2	5	240.00	60.00	1140.00
1899	705	24	3	18.00	0.00	54.00
1900	705	27	4	195.00	0.00	780.00
1901	706	25	4	28.00	0.00	112.00
1902	706	28	5	340.00	85.00	1615.00
1903	706	1	1	250.00	0.00	250.00
1904	707	26	5	185.00	46.25	878.75
1905	707	29	1	390.00	0.00	390.00
1906	707	2	2	240.00	0.00	480.00
1907	707	5	3	60.00	0.00	180.00
1908	708	27	1	195.00	0.00	195.00
1909	708	30	2	120.00	0.00	240.00
1910	709	28	2	340.00	0.00	680.00
1911	709	1	3	250.00	0.00	750.00
1912	709	4	4	180.00	36.00	684.00
1913	710	29	3	390.00	0.00	1170.00
1914	710	2	4	240.00	48.00	912.00
1915	710	5	5	60.00	0.00	300.00
1916	710	8	1	300.00	0.00	300.00
1917	711	30	4	120.00	24.00	456.00
1918	711	3	5	550.00	0.00	2750.00
1919	712	1	1	250.00	0.00	250.00
1920	713	2	1	240.00	0.00	240.00
1921	713	5	2	60.00	0.00	120.00
1922	713	8	3	300.00	45.00	855.00
1923	713	11	4	130.00	0.00	520.00
1924	714	3	2	550.00	0.00	1100.00
1925	714	6	3	25.00	3.75	71.25
\.


--
-- Data for Name: factura; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.factura (id_factura, fecha, id_cliente, id_usuario, id_seccion, subtotal, descuento, impuesto, total, tipo_cliente_venta, nombre_cliente_fugaz) FROM stdin;
1	2026-01-12 09:15:00	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
2	2026-01-18 14:30:00	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
3	2026-02-03 10:45:00	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
4	2026-02-11 16:20:00	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
5	2026-02-22 11:10:00	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
6	2026-03-02 13:25:00	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
7	2026-03-09 15:40:00	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
8	2026-03-16 09:50:00	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
9	2026-03-23 17:05:00	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
10	2026-04-05 10:30:00	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
11	2026-04-18 12:15:00	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
12	2026-04-30 18:45:00	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
13	2026-01-06 09:12:00	2	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
14	2026-01-07 10:24:00	3	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
15	2026-01-08 11:36:00	4	3	1	395.00	6.25	58.31	447.06	Habitual	\N
16	2026-01-09 12:00:00	5	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
17	2026-01-10 13:12:00	6	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
18	2026-01-11 14:24:00	7	6	2	1530.00	36.00	224.10	1718.10	Habitual	\N
19	2026-01-12 15:36:00	8	7	1	3285.00	154.00	469.65	3600.65	Habitual	\N
20	2026-01-13 08:00:00	9	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
21	2026-01-14 09:12:00	10	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
22	2026-01-15 10:24:00	11	10	2	3385.00	38.25	502.01	3848.76	Habitual	\N
23	2026-01-16 11:36:00	1	11	1	15.00	0.75	2.14	16.39	Fugaz	Cliente rápido #11
24	2026-01-17 12:00:00	13	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
25	2026-01-18 13:12:00	14	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
26	2026-01-19 14:24:00	15	14	2	1425.00	64.50	204.08	1564.58	Habitual	\N
27	2026-01-20 15:36:00	16	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
28	2026-01-21 08:00:00	17	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
29	2026-01-22 09:12:00	18	17	1	1770.00	9.25	264.11	2024.86	Habitual	\N
30	2026-01-23 10:24:00	19	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
31	2026-01-24 11:36:00	20	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
32	2026-01-25 12:00:00	21	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
33	2026-01-26 13:12:00	22	21	1	834.00	50.00	117.60	901.60	Habitual	\N
34	2026-01-27 14:24:00	1	22	2	28.00	0.00	4.20	32.20	Fugaz	Cliente rápido #22
35	2026-01-28 15:36:00	24	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
36	2026-01-29 08:00:00	25	24	2	435.00	0.00	65.25	500.25	Habitual	\N
37	2026-01-30 09:12:00	26	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
38	2026-01-31 10:24:00	27	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
39	2026-02-01 11:36:00	28	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
40	2026-02-02 12:00:00	29	28	2	1750.00	50.00	255.00	1955.00	Habitual	\N
41	2026-02-03 13:12:00	30	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
42	2026-02-04 14:24:00	31	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
43	2026-02-05 15:36:00	32	1	1	3780.00	27.00	562.95	4315.95	Habitual	\N
44	2026-02-06 08:00:00	33	2	2	1900.00	1.50	284.78	2183.28	Habitual	\N
45	2026-02-07 09:12:00	1	3	1	25.00	0.00	3.75	28.75	Fugaz	Cliente rápido #33
46	2026-02-08 10:24:00	35	4	2	1770.00	52.00	257.70	1975.70	Habitual	\N
47	2026-02-09 11:36:00	36	5	1	2350.00	80.00	340.50	2610.50	Habitual	\N
48	2026-02-10 12:00:00	37	6	2	1530.00	0.00	229.50	1759.50	Habitual	\N
49	2026-02-11 13:12:00	38	7	1	3285.00	12.75	490.84	3763.09	Habitual	\N
50	2026-02-12 14:24:00	39	8	2	1620.00	0.75	242.89	1862.14	Habitual	\N
51	2026-02-13 15:36:00	40	9	1	940.00	9.00	139.65	1070.65	Habitual	\N
52	2026-02-14 08:00:00	41	10	2	3385.00	0.00	507.75	3892.75	Habitual	\N
53	2026-02-15 09:12:00	42	11	1	1855.00	36.25	272.81	2091.56	Habitual	\N
54	2026-02-16 10:24:00	43	12	2	4470.00	197.50	640.88	4913.38	Habitual	\N
55	2026-02-17 11:36:00	44	13	1	2395.00	63.75	349.69	2680.94	Habitual	\N
56	2026-02-18 12:00:00	1	14	2	260.00	0.00	39.00	299.00	Fugaz	Cliente rápido #44
57	2026-02-19 13:12:00	46	15	1	1750.00	0.00	262.50	2012.50	Habitual	\N
58	2026-02-20 14:24:00	47	16	2	2940.00	56.00	432.60	3316.60	Habitual	\N
59	2026-02-21 15:36:00	48	17	1	1770.00	29.00	261.15	2002.15	Habitual	\N
60	2026-02-22 08:00:00	49	18	2	968.00	0.00	145.20	1113.20	Habitual	\N
61	2026-02-23 09:12:00	50	19	1	1356.00	101.00	188.25	1443.25	Habitual	\N
62	2026-02-24 10:24:00	51	20	2	3405.00	27.75	506.59	3883.84	Habitual	\N
63	2026-02-25 11:36:00	2	21	1	834.00	2.70	124.70	956.00	Habitual	\N
64	2026-02-26 12:00:00	3	22	2	2062.00	0.00	309.30	2371.30	Habitual	\N
65	2026-02-27 13:12:00	4	23	1	1975.00	24.00	292.65	2243.65	Habitual	\N
66	2026-02-28 14:24:00	5	24	2	435.00	12.00	63.45	486.45	Habitual	\N
67	2026-03-01 15:36:00	1	25	1	340.00	17.00	48.45	371.45	Fugaz	Cliente rápido #55
68	2026-03-02 08:00:00	7	26	2	2730.00	65.00	399.75	3064.75	Habitual	\N
69	2026-03-03 09:12:00	8	27	1	3230.00	0.00	484.50	3714.50	Habitual	\N
70	2026-03-04 10:24:00	9	28	2	1750.00	9.00	261.15	2002.15	Habitual	\N
71	2026-03-05 11:36:00	10	29	1	1780.00	12.00	265.20	2033.20	Habitual	\N
72	2026-03-06 12:00:00	11	30	2	1175.00	0.00	176.25	1351.25	Habitual	\N
73	2026-03-07 13:12:00	12	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
74	2026-03-08 14:24:00	13	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
75	2026-03-09 15:36:00	14	3	1	395.00	56.25	50.81	389.56	Habitual	\N
76	2026-03-10 08:00:00	15	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
77	2026-03-11 09:12:00	16	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
78	2026-03-12 10:24:00	1	6	2	270.00	0.00	40.50	310.50	Fugaz	Cliente rápido #66
79	2026-03-13 11:36:00	18	7	1	3285.00	104.00	477.15	3658.15	Habitual	\N
80	2026-03-14 12:00:00	19	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
81	2026-03-15 13:12:00	20	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
82	2026-03-16 14:24:00	21	10	2	3385.00	88.25	494.51	3791.26	Habitual	\N
83	2026-03-17 15:36:00	22	11	1	1855.00	2.25	277.91	2130.66	Habitual	\N
84	2026-03-18 08:00:00	23	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
85	2026-03-19 09:12:00	24	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
86	2026-03-20 10:24:00	25	14	2	1425.00	14.50	211.58	1622.08	Habitual	\N
87	2026-03-21 11:36:00	26	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
88	2026-03-22 12:00:00	27	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
89	2026-03-23 13:12:00	1	17	1	145.00	50.00	14.25	109.25	Fugaz	Cliente rápido #77
90	2026-03-24 14:24:00	29	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
91	2026-03-25 15:36:00	30	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
92	2026-03-26 08:00:00	31	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
93	2026-03-27 09:12:00	32	21	1	834.00	0.00	125.10	959.10	Habitual	\N
94	2026-03-28 10:24:00	33	22	2	2062.00	85.00	296.55	2273.55	Habitual	\N
95	2026-03-29 11:36:00	34	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
96	2026-03-30 12:00:00	35	24	2	435.00	50.00	57.75	442.75	Habitual	\N
97	2026-03-31 13:12:00	36	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
98	2026-04-01 14:24:00	37	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
99	2026-04-02 15:36:00	38	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
100	2026-04-03 08:00:00	1	28	2	250.00	0.00	37.50	287.50	Fugaz	Cliente rápido #88
101	2026-04-04 09:12:00	40	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
102	2026-04-05 10:24:00	41	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
103	2026-01-12 09:15:00	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
104	2026-01-18 14:30:00	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
105	2026-02-03 10:45:00	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
106	2026-02-11 16:20:00	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
107	2026-02-22 11:10:00	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
108	2026-03-02 13:25:00	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
109	2026-03-09 15:40:00	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
110	2026-03-16 09:50:00	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
111	2026-03-23 17:05:00	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
112	2026-04-05 10:30:00	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
113	2026-04-18 12:15:00	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
114	2026-04-30 18:45:00	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
115	2026-01-06 09:12:00	2	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
116	2026-01-07 10:24:00	3	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
117	2026-01-08 11:36:00	4	3	1	395.00	6.25	58.31	447.06	Habitual	\N
118	2026-01-09 12:00:00	5	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
119	2026-01-10 13:12:00	6	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
120	2026-01-11 14:24:00	7	6	2	1530.00	36.00	224.10	1718.10	Habitual	\N
121	2026-01-12 15:36:00	8	7	1	3285.00	154.00	469.65	3600.65	Habitual	\N
122	2026-01-13 08:00:00	9	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
123	2026-01-14 09:12:00	10	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
124	2026-01-15 10:24:00	11	10	2	3385.00	38.25	502.01	3848.76	Habitual	\N
125	2026-01-16 11:36:00	1	11	1	15.00	0.75	2.14	16.39	Fugaz	Cliente rápido #11
126	2026-01-17 12:00:00	13	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
127	2026-01-18 13:12:00	14	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
128	2026-01-19 14:24:00	15	14	2	1425.00	64.50	204.08	1564.58	Habitual	\N
129	2026-01-20 15:36:00	16	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
130	2026-01-21 08:00:00	17	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
131	2026-01-22 09:12:00	18	17	1	1770.00	9.25	264.11	2024.86	Habitual	\N
132	2026-01-23 10:24:00	19	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
133	2026-01-24 11:36:00	20	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
134	2026-01-25 12:00:00	21	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
135	2026-01-26 13:12:00	22	21	1	834.00	50.00	117.60	901.60	Habitual	\N
136	2026-01-27 14:24:00	1	22	2	28.00	0.00	4.20	32.20	Fugaz	Cliente rápido #22
137	2026-01-28 15:36:00	24	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
138	2026-01-29 08:00:00	25	24	2	435.00	0.00	65.25	500.25	Habitual	\N
139	2026-01-30 09:12:00	26	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
140	2026-01-31 10:24:00	27	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
141	2026-02-01 11:36:00	28	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
142	2026-02-02 12:00:00	29	28	2	1750.00	50.00	255.00	1955.00	Habitual	\N
143	2026-02-03 13:12:00	30	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
144	2026-02-04 14:24:00	31	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
145	2026-02-05 15:36:00	32	1	1	3780.00	27.00	562.95	4315.95	Habitual	\N
146	2026-02-06 08:00:00	33	2	2	1900.00	1.50	284.78	2183.28	Habitual	\N
147	2026-02-07 09:12:00	1	3	1	25.00	0.00	3.75	28.75	Fugaz	Cliente rápido #33
148	2026-02-08 10:24:00	35	4	2	1770.00	52.00	257.70	1975.70	Habitual	\N
149	2026-02-09 11:36:00	36	5	1	2350.00	80.00	340.50	2610.50	Habitual	\N
150	2026-02-10 12:00:00	37	6	2	1530.00	0.00	229.50	1759.50	Habitual	\N
151	2026-02-11 13:12:00	38	7	1	3285.00	12.75	490.84	3763.09	Habitual	\N
152	2026-02-12 14:24:00	39	8	2	1620.00	0.75	242.89	1862.14	Habitual	\N
153	2026-02-13 15:36:00	40	9	1	940.00	9.00	139.65	1070.65	Habitual	\N
154	2026-02-14 08:00:00	41	10	2	3385.00	0.00	507.75	3892.75	Habitual	\N
155	2026-02-15 09:12:00	42	11	1	1855.00	36.25	272.81	2091.56	Habitual	\N
156	2026-02-16 10:24:00	43	12	2	4470.00	197.50	640.88	4913.38	Habitual	\N
157	2026-02-17 11:36:00	44	13	1	2395.00	63.75	349.69	2680.94	Habitual	\N
158	2026-02-18 12:00:00	1	14	2	260.00	0.00	39.00	299.00	Fugaz	Cliente rápido #44
159	2026-02-19 13:12:00	46	15	1	1750.00	0.00	262.50	2012.50	Habitual	\N
160	2026-02-20 14:24:00	47	16	2	2940.00	56.00	432.60	3316.60	Habitual	\N
161	2026-02-21 15:36:00	48	17	1	1770.00	29.00	261.15	2002.15	Habitual	\N
162	2026-02-22 08:00:00	49	18	2	968.00	0.00	145.20	1113.20	Habitual	\N
163	2026-02-23 09:12:00	50	19	1	1356.00	101.00	188.25	1443.25	Habitual	\N
164	2026-02-24 10:24:00	51	20	2	3405.00	27.75	506.59	3883.84	Habitual	\N
165	2026-02-25 11:36:00	53	21	1	834.00	2.70	124.70	956.00	Habitual	\N
166	2026-02-26 12:00:00	54	22	2	2062.00	0.00	309.30	2371.30	Habitual	\N
167	2026-02-27 13:12:00	55	23	1	1975.00	24.00	292.65	2243.65	Habitual	\N
168	2026-02-28 14:24:00	56	24	2	435.00	12.00	63.45	486.45	Habitual	\N
169	2026-03-01 15:36:00	1	25	1	340.00	17.00	48.45	371.45	Fugaz	Cliente rápido #55
170	2026-03-02 08:00:00	58	26	2	2730.00	65.00	399.75	3064.75	Habitual	\N
171	2026-03-03 09:12:00	59	27	1	3230.00	0.00	484.50	3714.50	Habitual	\N
172	2026-03-04 10:24:00	60	28	2	1750.00	9.00	261.15	2002.15	Habitual	\N
173	2026-03-05 11:36:00	61	29	1	1780.00	12.00	265.20	2033.20	Habitual	\N
174	2026-03-06 12:00:00	62	30	2	1175.00	0.00	176.25	1351.25	Habitual	\N
175	2026-03-07 13:12:00	63	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
176	2026-03-08 14:24:00	64	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
177	2026-03-09 15:36:00	65	3	1	395.00	56.25	50.81	389.56	Habitual	\N
178	2026-03-10 08:00:00	66	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
179	2026-03-11 09:12:00	67	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
180	2026-03-12 10:24:00	1	6	2	270.00	0.00	40.50	310.50	Fugaz	Cliente rápido #66
181	2026-03-13 11:36:00	69	7	1	3285.00	104.00	477.15	3658.15	Habitual	\N
182	2026-03-14 12:00:00	70	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
183	2026-03-15 13:12:00	71	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
184	2026-03-16 14:24:00	72	10	2	3385.00	88.25	494.51	3791.26	Habitual	\N
185	2026-03-17 15:36:00	73	11	1	1855.00	2.25	277.91	2130.66	Habitual	\N
186	2026-03-18 08:00:00	74	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
187	2026-03-19 09:12:00	75	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
188	2026-03-20 10:24:00	76	14	2	1425.00	14.50	211.58	1622.08	Habitual	\N
189	2026-03-21 11:36:00	77	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
190	2026-03-22 12:00:00	78	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
191	2026-03-23 13:12:00	1	17	1	145.00	50.00	14.25	109.25	Fugaz	Cliente rápido #77
192	2026-03-24 14:24:00	80	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
193	2026-03-25 15:36:00	81	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
194	2026-03-26 08:00:00	82	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
195	2026-03-27 09:12:00	83	21	1	834.00	0.00	125.10	959.10	Habitual	\N
196	2026-03-28 10:24:00	84	22	2	2062.00	85.00	296.55	2273.55	Habitual	\N
197	2026-03-29 11:36:00	85	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
198	2026-03-30 12:00:00	86	24	2	435.00	50.00	57.75	442.75	Habitual	\N
199	2026-03-31 13:12:00	87	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
200	2026-04-01 14:24:00	88	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
201	2026-04-02 15:36:00	89	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
202	2026-04-03 08:00:00	1	28	2	250.00	0.00	37.50	287.50	Fugaz	Cliente rápido #88
203	2026-04-04 09:12:00	91	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
204	2026-04-05 10:24:00	92	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
205	2026-01-12 09:15:00	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
206	2026-01-18 14:30:00	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
207	2026-02-03 10:45:00	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
208	2026-02-11 16:20:00	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
209	2026-02-22 11:10:00	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
210	2026-03-02 13:25:00	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
211	2026-03-09 15:40:00	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
212	2026-03-16 09:50:00	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
213	2026-03-23 17:05:00	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
214	2026-04-05 10:30:00	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
215	2026-04-18 12:15:00	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
216	2026-04-30 18:45:00	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
217	2026-01-06 09:12:00	2	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
218	2026-01-07 10:24:00	3	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
219	2026-01-08 11:36:00	4	3	1	395.00	6.25	58.31	447.06	Habitual	\N
220	2026-01-09 12:00:00	5	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
221	2026-01-10 13:12:00	6	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
222	2026-01-11 14:24:00	7	6	2	1530.00	36.00	224.10	1718.10	Habitual	\N
223	2026-01-12 15:36:00	8	7	1	3285.00	154.00	469.65	3600.65	Habitual	\N
224	2026-01-13 08:00:00	9	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
225	2026-01-14 09:12:00	10	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
226	2026-01-15 10:24:00	11	10	2	3385.00	38.25	502.01	3848.76	Habitual	\N
227	2026-01-16 11:36:00	1	11	1	15.00	0.75	2.14	16.39	Fugaz	Cliente rápido #11
228	2026-01-17 12:00:00	13	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
229	2026-01-18 13:12:00	14	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
230	2026-01-19 14:24:00	15	14	2	1425.00	64.50	204.08	1564.58	Habitual	\N
231	2026-01-20 15:36:00	16	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
232	2026-01-21 08:00:00	17	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
233	2026-01-22 09:12:00	18	17	1	1770.00	9.25	264.11	2024.86	Habitual	\N
234	2026-01-23 10:24:00	19	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
235	2026-01-24 11:36:00	20	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
236	2026-01-25 12:00:00	21	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
237	2026-01-26 13:12:00	22	21	1	834.00	50.00	117.60	901.60	Habitual	\N
238	2026-01-27 14:24:00	1	22	2	28.00	0.00	4.20	32.20	Fugaz	Cliente rápido #22
239	2026-01-28 15:36:00	24	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
240	2026-01-29 08:00:00	25	24	2	435.00	0.00	65.25	500.25	Habitual	\N
241	2026-01-30 09:12:00	26	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
242	2026-01-31 10:24:00	27	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
243	2026-02-01 11:36:00	28	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
244	2026-02-02 12:00:00	29	28	2	1750.00	50.00	255.00	1955.00	Habitual	\N
245	2026-02-03 13:12:00	30	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
246	2026-02-04 14:24:00	31	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
247	2026-02-05 15:36:00	32	1	1	3780.00	27.00	562.95	4315.95	Habitual	\N
248	2026-02-06 08:00:00	33	2	2	1900.00	1.50	284.78	2183.28	Habitual	\N
249	2026-02-07 09:12:00	1	3	1	25.00	0.00	3.75	28.75	Fugaz	Cliente rápido #33
250	2026-02-08 10:24:00	35	4	2	1770.00	52.00	257.70	1975.70	Habitual	\N
251	2026-02-09 11:36:00	36	5	1	2350.00	80.00	340.50	2610.50	Habitual	\N
252	2026-02-10 12:00:00	37	6	2	1530.00	0.00	229.50	1759.50	Habitual	\N
253	2026-02-11 13:12:00	38	7	1	3285.00	12.75	490.84	3763.09	Habitual	\N
254	2026-02-12 14:24:00	39	8	2	1620.00	0.75	242.89	1862.14	Habitual	\N
255	2026-02-13 15:36:00	40	9	1	940.00	9.00	139.65	1070.65	Habitual	\N
256	2026-02-14 08:00:00	41	10	2	3385.00	0.00	507.75	3892.75	Habitual	\N
257	2026-02-15 09:12:00	42	11	1	1855.00	36.25	272.81	2091.56	Habitual	\N
258	2026-02-16 10:24:00	43	12	2	4470.00	197.50	640.88	4913.38	Habitual	\N
259	2026-02-17 11:36:00	44	13	1	2395.00	63.75	349.69	2680.94	Habitual	\N
260	2026-02-18 12:00:00	1	14	2	260.00	0.00	39.00	299.00	Fugaz	Cliente rápido #44
261	2026-02-19 13:12:00	46	15	1	1750.00	0.00	262.50	2012.50	Habitual	\N
262	2026-02-20 14:24:00	47	16	2	2940.00	56.00	432.60	3316.60	Habitual	\N
263	2026-02-21 15:36:00	48	17	1	1770.00	29.00	261.15	2002.15	Habitual	\N
264	2026-02-22 08:00:00	49	18	2	968.00	0.00	145.20	1113.20	Habitual	\N
265	2026-02-23 09:12:00	50	19	1	1356.00	101.00	188.25	1443.25	Habitual	\N
266	2026-02-24 10:24:00	51	20	2	3405.00	27.75	506.59	3883.84	Habitual	\N
267	2026-02-25 11:36:00	53	21	1	834.00	2.70	124.70	956.00	Habitual	\N
268	2026-02-26 12:00:00	54	22	2	2062.00	0.00	309.30	2371.30	Habitual	\N
269	2026-02-27 13:12:00	55	23	1	1975.00	24.00	292.65	2243.65	Habitual	\N
270	2026-02-28 14:24:00	56	24	2	435.00	12.00	63.45	486.45	Habitual	\N
271	2026-03-01 15:36:00	1	25	1	340.00	17.00	48.45	371.45	Fugaz	Cliente rápido #55
272	2026-03-02 08:00:00	58	26	2	2730.00	65.00	399.75	3064.75	Habitual	\N
273	2026-03-03 09:12:00	59	27	1	3230.00	0.00	484.50	3714.50	Habitual	\N
274	2026-03-04 10:24:00	60	28	2	1750.00	9.00	261.15	2002.15	Habitual	\N
275	2026-03-05 11:36:00	61	29	1	1780.00	12.00	265.20	2033.20	Habitual	\N
276	2026-03-06 12:00:00	62	30	2	1175.00	0.00	176.25	1351.25	Habitual	\N
277	2026-03-07 13:12:00	63	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
278	2026-03-08 14:24:00	64	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
279	2026-03-09 15:36:00	65	3	1	395.00	56.25	50.81	389.56	Habitual	\N
280	2026-03-10 08:00:00	66	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
281	2026-03-11 09:12:00	67	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
282	2026-03-12 10:24:00	1	6	2	270.00	0.00	40.50	310.50	Fugaz	Cliente rápido #66
283	2026-03-13 11:36:00	69	7	1	3285.00	104.00	477.15	3658.15	Habitual	\N
284	2026-03-14 12:00:00	70	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
285	2026-03-15 13:12:00	71	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
286	2026-03-16 14:24:00	72	10	2	3385.00	88.25	494.51	3791.26	Habitual	\N
287	2026-03-17 15:36:00	73	11	1	1855.00	2.25	277.91	2130.66	Habitual	\N
288	2026-03-18 08:00:00	74	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
289	2026-03-19 09:12:00	75	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
290	2026-03-20 10:24:00	76	14	2	1425.00	14.50	211.58	1622.08	Habitual	\N
291	2026-03-21 11:36:00	77	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
292	2026-03-22 12:00:00	78	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
293	2026-03-23 13:12:00	1	17	1	145.00	50.00	14.25	109.25	Fugaz	Cliente rápido #77
294	2026-03-24 14:24:00	80	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
295	2026-03-25 15:36:00	81	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
296	2026-03-26 08:00:00	82	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
297	2026-03-27 09:12:00	83	21	1	834.00	0.00	125.10	959.10	Habitual	\N
298	2026-03-28 10:24:00	84	22	2	2062.00	85.00	296.55	2273.55	Habitual	\N
299	2026-03-29 11:36:00	85	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
300	2026-03-30 12:00:00	86	24	2	435.00	50.00	57.75	442.75	Habitual	\N
301	2026-03-31 13:12:00	87	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
302	2026-04-01 14:24:00	88	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
303	2026-04-02 15:36:00	89	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
304	2026-04-03 08:00:00	1	28	2	250.00	0.00	37.50	287.50	Fugaz	Cliente rápido #88
305	2026-04-04 09:12:00	91	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
306	2026-04-05 10:24:00	92	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
307	2026-01-12 09:15:00	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
308	2026-01-18 14:30:00	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
309	2026-02-03 10:45:00	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
310	2026-02-11 16:20:00	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
311	2026-02-22 11:10:00	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
312	2026-03-02 13:25:00	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
313	2026-03-09 15:40:00	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
314	2026-03-16 09:50:00	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
315	2026-03-23 17:05:00	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
316	2026-04-05 10:30:00	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
317	2026-04-18 12:15:00	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
318	2026-04-30 18:45:00	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
319	2026-01-06 09:12:00	2	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
320	2026-01-07 10:24:00	3	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
321	2026-01-08 11:36:00	4	3	1	395.00	6.25	58.31	447.06	Habitual	\N
322	2026-01-09 12:00:00	5	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
323	2026-01-10 13:12:00	6	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
324	2026-01-11 14:24:00	7	6	2	1530.00	36.00	224.10	1718.10	Habitual	\N
325	2026-01-12 15:36:00	8	7	1	3285.00	154.00	469.65	3600.65	Habitual	\N
326	2026-01-13 08:00:00	9	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
327	2026-01-14 09:12:00	10	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
328	2026-01-15 10:24:00	11	10	2	3385.00	38.25	502.01	3848.76	Habitual	\N
329	2026-01-16 11:36:00	1	11	1	15.00	0.75	2.14	16.39	Fugaz	Cliente rápido #11
330	2026-01-17 12:00:00	13	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
331	2026-01-18 13:12:00	14	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
332	2026-01-19 14:24:00	15	14	2	1425.00	64.50	204.08	1564.58	Habitual	\N
333	2026-01-20 15:36:00	16	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
334	2026-01-21 08:00:00	17	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
335	2026-01-22 09:12:00	18	17	1	1770.00	9.25	264.11	2024.86	Habitual	\N
336	2026-01-23 10:24:00	19	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
337	2026-01-24 11:36:00	20	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
338	2026-01-25 12:00:00	21	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
339	2026-01-26 13:12:00	22	21	1	834.00	50.00	117.60	901.60	Habitual	\N
340	2026-01-27 14:24:00	1	22	2	28.00	0.00	4.20	32.20	Fugaz	Cliente rápido #22
341	2026-01-28 15:36:00	24	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
342	2026-01-29 08:00:00	25	24	2	435.00	0.00	65.25	500.25	Habitual	\N
343	2026-01-30 09:12:00	26	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
344	2026-01-31 10:24:00	27	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
345	2026-02-01 11:36:00	28	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
346	2026-02-02 12:00:00	29	28	2	1750.00	50.00	255.00	1955.00	Habitual	\N
347	2026-02-03 13:12:00	30	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
348	2026-02-04 14:24:00	31	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
349	2026-02-05 15:36:00	32	1	1	3780.00	27.00	562.95	4315.95	Habitual	\N
350	2026-02-06 08:00:00	33	2	2	1900.00	1.50	284.78	2183.28	Habitual	\N
351	2026-02-07 09:12:00	1	3	1	25.00	0.00	3.75	28.75	Fugaz	Cliente rápido #33
352	2026-02-08 10:24:00	35	4	2	1770.00	52.00	257.70	1975.70	Habitual	\N
353	2026-02-09 11:36:00	36	5	1	2350.00	80.00	340.50	2610.50	Habitual	\N
354	2026-02-10 12:00:00	37	6	2	1530.00	0.00	229.50	1759.50	Habitual	\N
355	2026-02-11 13:12:00	38	7	1	3285.00	12.75	490.84	3763.09	Habitual	\N
356	2026-02-12 14:24:00	39	8	2	1620.00	0.75	242.89	1862.14	Habitual	\N
357	2026-02-13 15:36:00	40	9	1	940.00	9.00	139.65	1070.65	Habitual	\N
358	2026-02-14 08:00:00	41	10	2	3385.00	0.00	507.75	3892.75	Habitual	\N
359	2026-02-15 09:12:00	42	11	1	1855.00	36.25	272.81	2091.56	Habitual	\N
360	2026-02-16 10:24:00	43	12	2	4470.00	197.50	640.88	4913.38	Habitual	\N
361	2026-02-17 11:36:00	44	13	1	2395.00	63.75	349.69	2680.94	Habitual	\N
362	2026-02-18 12:00:00	1	14	2	260.00	0.00	39.00	299.00	Fugaz	Cliente rápido #44
363	2026-02-19 13:12:00	46	15	1	1750.00	0.00	262.50	2012.50	Habitual	\N
364	2026-02-20 14:24:00	47	16	2	2940.00	56.00	432.60	3316.60	Habitual	\N
365	2026-02-21 15:36:00	48	17	1	1770.00	29.00	261.15	2002.15	Habitual	\N
366	2026-02-22 08:00:00	49	18	2	968.00	0.00	145.20	1113.20	Habitual	\N
367	2026-02-23 09:12:00	50	19	1	1356.00	101.00	188.25	1443.25	Habitual	\N
368	2026-02-24 10:24:00	51	20	2	3405.00	27.75	506.59	3883.84	Habitual	\N
369	2026-02-25 11:36:00	53	21	1	834.00	2.70	124.70	956.00	Habitual	\N
370	2026-02-26 12:00:00	54	22	2	2062.00	0.00	309.30	2371.30	Habitual	\N
371	2026-02-27 13:12:00	55	23	1	1975.00	24.00	292.65	2243.65	Habitual	\N
372	2026-02-28 14:24:00	56	24	2	435.00	12.00	63.45	486.45	Habitual	\N
373	2026-03-01 15:36:00	1	25	1	340.00	17.00	48.45	371.45	Fugaz	Cliente rápido #55
374	2026-03-02 08:00:00	58	26	2	2730.00	65.00	399.75	3064.75	Habitual	\N
375	2026-03-03 09:12:00	59	27	1	3230.00	0.00	484.50	3714.50	Habitual	\N
376	2026-03-04 10:24:00	60	28	2	1750.00	9.00	261.15	2002.15	Habitual	\N
377	2026-03-05 11:36:00	61	29	1	1780.00	12.00	265.20	2033.20	Habitual	\N
378	2026-03-06 12:00:00	62	30	2	1175.00	0.00	176.25	1351.25	Habitual	\N
379	2026-03-07 13:12:00	63	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
380	2026-03-08 14:24:00	64	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
381	2026-03-09 15:36:00	65	3	1	395.00	56.25	50.81	389.56	Habitual	\N
382	2026-03-10 08:00:00	66	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
383	2026-03-11 09:12:00	67	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
384	2026-03-12 10:24:00	1	6	2	270.00	0.00	40.50	310.50	Fugaz	Cliente rápido #66
385	2026-03-13 11:36:00	69	7	1	3285.00	104.00	477.15	3658.15	Habitual	\N
386	2026-03-14 12:00:00	70	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
387	2026-03-15 13:12:00	71	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
388	2026-03-16 14:24:00	72	10	2	3385.00	88.25	494.51	3791.26	Habitual	\N
389	2026-03-17 15:36:00	73	11	1	1855.00	2.25	277.91	2130.66	Habitual	\N
390	2026-03-18 08:00:00	74	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
391	2026-03-19 09:12:00	75	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
392	2026-03-20 10:24:00	76	14	2	1425.00	14.50	211.58	1622.08	Habitual	\N
393	2026-03-21 11:36:00	77	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
394	2026-03-22 12:00:00	78	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
395	2026-03-23 13:12:00	1	17	1	145.00	50.00	14.25	109.25	Fugaz	Cliente rápido #77
396	2026-03-24 14:24:00	80	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
397	2026-03-25 15:36:00	81	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
398	2026-03-26 08:00:00	82	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
399	2026-03-27 09:12:00	83	21	1	834.00	0.00	125.10	959.10	Habitual	\N
400	2026-03-28 10:24:00	84	22	2	2062.00	85.00	296.55	2273.55	Habitual	\N
401	2026-03-29 11:36:00	85	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
402	2026-03-30 12:00:00	86	24	2	435.00	50.00	57.75	442.75	Habitual	\N
403	2026-03-31 13:12:00	87	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
404	2026-04-01 14:24:00	88	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
405	2026-04-02 15:36:00	89	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
406	2026-04-03 08:00:00	1	28	2	250.00	0.00	37.50	287.50	Fugaz	Cliente rápido #88
407	2026-04-04 09:12:00	91	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
408	2026-04-05 10:24:00	92	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
409	2026-01-12 09:15:00	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
410	2026-01-18 14:30:00	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
411	2026-02-03 10:45:00	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
412	2026-02-11 16:20:00	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
413	2026-02-22 11:10:00	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
414	2026-03-02 13:25:00	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
415	2026-03-09 15:40:00	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
416	2026-03-16 09:50:00	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
417	2026-03-23 17:05:00	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
418	2026-04-05 10:30:00	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
419	2026-04-18 12:15:00	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
420	2026-04-30 18:45:00	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
421	2026-01-06 09:12:00	2	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
422	2026-01-07 10:24:00	3	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
423	2026-01-08 11:36:00	4	3	1	395.00	6.25	58.31	447.06	Habitual	\N
424	2026-01-09 12:00:00	5	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
425	2026-01-10 13:12:00	6	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
426	2026-01-11 14:24:00	7	6	2	1530.00	36.00	224.10	1718.10	Habitual	\N
427	2026-01-12 15:36:00	8	7	1	3285.00	154.00	469.65	3600.65	Habitual	\N
428	2026-01-13 08:00:00	9	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
429	2026-01-14 09:12:00	10	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
430	2026-01-15 10:24:00	11	10	2	3385.00	38.25	502.01	3848.76	Habitual	\N
431	2026-01-16 11:36:00	1	11	1	15.00	0.75	2.14	16.39	Fugaz	Cliente rápido #11
432	2026-01-17 12:00:00	13	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
433	2026-01-18 13:12:00	14	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
434	2026-01-19 14:24:00	15	14	2	1425.00	64.50	204.08	1564.58	Habitual	\N
435	2026-01-20 15:36:00	16	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
436	2026-01-21 08:00:00	17	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
437	2026-01-22 09:12:00	18	17	1	1770.00	9.25	264.11	2024.86	Habitual	\N
438	2026-01-23 10:24:00	19	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
439	2026-01-24 11:36:00	20	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
440	2026-01-25 12:00:00	21	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
441	2026-01-26 13:12:00	22	21	1	834.00	50.00	117.60	901.60	Habitual	\N
442	2026-01-27 14:24:00	1	22	2	28.00	0.00	4.20	32.20	Fugaz	Cliente rápido #22
443	2026-01-28 15:36:00	24	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
444	2026-01-29 08:00:00	25	24	2	435.00	0.00	65.25	500.25	Habitual	\N
445	2026-01-30 09:12:00	26	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
446	2026-01-31 10:24:00	27	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
447	2026-02-01 11:36:00	28	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
448	2026-02-02 12:00:00	29	28	2	1750.00	50.00	255.00	1955.00	Habitual	\N
449	2026-02-03 13:12:00	30	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
450	2026-02-04 14:24:00	31	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
451	2026-02-05 15:36:00	32	1	1	3780.00	27.00	562.95	4315.95	Habitual	\N
452	2026-02-06 08:00:00	33	2	2	1900.00	1.50	284.78	2183.28	Habitual	\N
453	2026-02-07 09:12:00	1	3	1	25.00	0.00	3.75	28.75	Fugaz	Cliente rápido #33
454	2026-02-08 10:24:00	35	4	2	1770.00	52.00	257.70	1975.70	Habitual	\N
455	2026-02-09 11:36:00	36	5	1	2350.00	80.00	340.50	2610.50	Habitual	\N
456	2026-02-10 12:00:00	37	6	2	1530.00	0.00	229.50	1759.50	Habitual	\N
457	2026-02-11 13:12:00	38	7	1	3285.00	12.75	490.84	3763.09	Habitual	\N
458	2026-02-12 14:24:00	39	8	2	1620.00	0.75	242.89	1862.14	Habitual	\N
459	2026-02-13 15:36:00	40	9	1	940.00	9.00	139.65	1070.65	Habitual	\N
460	2026-02-14 08:00:00	41	10	2	3385.00	0.00	507.75	3892.75	Habitual	\N
461	2026-02-15 09:12:00	42	11	1	1855.00	36.25	272.81	2091.56	Habitual	\N
462	2026-02-16 10:24:00	43	12	2	4470.00	197.50	640.88	4913.38	Habitual	\N
463	2026-02-17 11:36:00	44	13	1	2395.00	63.75	349.69	2680.94	Habitual	\N
464	2026-02-18 12:00:00	1	14	2	260.00	0.00	39.00	299.00	Fugaz	Cliente rápido #44
465	2026-02-19 13:12:00	46	15	1	1750.00	0.00	262.50	2012.50	Habitual	\N
466	2026-02-20 14:24:00	47	16	2	2940.00	56.00	432.60	3316.60	Habitual	\N
467	2026-02-21 15:36:00	48	17	1	1770.00	29.00	261.15	2002.15	Habitual	\N
468	2026-02-22 08:00:00	49	18	2	968.00	0.00	145.20	1113.20	Habitual	\N
469	2026-02-23 09:12:00	50	19	1	1356.00	101.00	188.25	1443.25	Habitual	\N
470	2026-02-24 10:24:00	51	20	2	3405.00	27.75	506.59	3883.84	Habitual	\N
471	2026-02-25 11:36:00	53	21	1	834.00	2.70	124.70	956.00	Habitual	\N
472	2026-02-26 12:00:00	54	22	2	2062.00	0.00	309.30	2371.30	Habitual	\N
473	2026-02-27 13:12:00	55	23	1	1975.00	24.00	292.65	2243.65	Habitual	\N
474	2026-02-28 14:24:00	56	24	2	435.00	12.00	63.45	486.45	Habitual	\N
475	2026-03-01 15:36:00	1	25	1	340.00	17.00	48.45	371.45	Fugaz	Cliente rápido #55
476	2026-03-02 08:00:00	58	26	2	2730.00	65.00	399.75	3064.75	Habitual	\N
477	2026-03-03 09:12:00	59	27	1	3230.00	0.00	484.50	3714.50	Habitual	\N
478	2026-03-04 10:24:00	60	28	2	1750.00	9.00	261.15	2002.15	Habitual	\N
479	2026-03-05 11:36:00	61	29	1	1780.00	12.00	265.20	2033.20	Habitual	\N
480	2026-03-06 12:00:00	62	30	2	1175.00	0.00	176.25	1351.25	Habitual	\N
481	2026-03-07 13:12:00	63	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
482	2026-03-08 14:24:00	64	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
483	2026-03-09 15:36:00	65	3	1	395.00	56.25	50.81	389.56	Habitual	\N
484	2026-03-10 08:00:00	66	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
485	2026-03-11 09:12:00	67	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
486	2026-03-12 10:24:00	1	6	2	270.00	0.00	40.50	310.50	Fugaz	Cliente rápido #66
487	2026-03-13 11:36:00	69	7	1	3285.00	104.00	477.15	3658.15	Habitual	\N
488	2026-03-14 12:00:00	70	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
489	2026-03-15 13:12:00	71	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
490	2026-03-16 14:24:00	72	10	2	3385.00	88.25	494.51	3791.26	Habitual	\N
491	2026-03-17 15:36:00	73	11	1	1855.00	2.25	277.91	2130.66	Habitual	\N
492	2026-03-18 08:00:00	74	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
493	2026-03-19 09:12:00	75	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
494	2026-03-20 10:24:00	76	14	2	1425.00	14.50	211.58	1622.08	Habitual	\N
495	2026-03-21 11:36:00	77	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
496	2026-03-22 12:00:00	78	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
497	2026-03-23 13:12:00	1	17	1	145.00	50.00	14.25	109.25	Fugaz	Cliente rápido #77
498	2026-03-24 14:24:00	80	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
499	2026-03-25 15:36:00	81	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
500	2026-03-26 08:00:00	82	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
501	2026-03-27 09:12:00	83	21	1	834.00	0.00	125.10	959.10	Habitual	\N
502	2026-03-28 10:24:00	84	22	2	2062.00	85.00	296.55	2273.55	Habitual	\N
503	2026-03-29 11:36:00	85	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
504	2026-03-30 12:00:00	86	24	2	435.00	50.00	57.75	442.75	Habitual	\N
505	2026-03-31 13:12:00	87	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
506	2026-04-01 14:24:00	88	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
507	2026-04-02 15:36:00	89	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
508	2026-04-03 08:00:00	1	28	2	250.00	0.00	37.50	287.50	Fugaz	Cliente rápido #88
509	2026-04-04 09:12:00	91	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
510	2026-04-05 10:24:00	92	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
511	2026-01-12 09:15:00	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
512	2026-01-18 14:30:00	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
513	2026-02-03 10:45:00	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
514	2026-02-11 16:20:00	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
515	2026-02-22 11:10:00	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
516	2026-03-02 13:25:00	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
517	2026-03-09 15:40:00	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
518	2026-03-16 09:50:00	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
519	2026-03-23 17:05:00	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
520	2026-04-05 10:30:00	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
521	2026-04-18 12:15:00	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
522	2026-04-30 18:45:00	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
523	2026-01-06 09:12:00	2	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
524	2026-01-07 10:24:00	3	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
525	2026-01-08 11:36:00	4	3	1	395.00	6.25	58.31	447.06	Habitual	\N
526	2026-01-09 12:00:00	5	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
527	2026-01-10 13:12:00	6	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
528	2026-01-11 14:24:00	7	6	2	1530.00	36.00	224.10	1718.10	Habitual	\N
529	2026-01-12 15:36:00	8	7	1	3285.00	154.00	469.65	3600.65	Habitual	\N
530	2026-01-13 08:00:00	9	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
531	2026-01-14 09:12:00	10	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
532	2026-01-15 10:24:00	11	10	2	3385.00	38.25	502.01	3848.76	Habitual	\N
533	2026-01-16 11:36:00	1	11	1	15.00	0.75	2.14	16.39	Fugaz	Cliente rápido #11
534	2026-01-17 12:00:00	13	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
535	2026-01-18 13:12:00	14	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
536	2026-01-19 14:24:00	15	14	2	1425.00	64.50	204.08	1564.58	Habitual	\N
537	2026-01-20 15:36:00	16	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
538	2026-01-21 08:00:00	17	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
539	2026-01-22 09:12:00	18	17	1	1770.00	9.25	264.11	2024.86	Habitual	\N
540	2026-01-23 10:24:00	19	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
541	2026-01-24 11:36:00	20	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
542	2026-01-25 12:00:00	21	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
543	2026-01-26 13:12:00	22	21	1	834.00	50.00	117.60	901.60	Habitual	\N
544	2026-01-27 14:24:00	1	22	2	28.00	0.00	4.20	32.20	Fugaz	Cliente rápido #22
545	2026-01-28 15:36:00	24	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
546	2026-01-29 08:00:00	25	24	2	435.00	0.00	65.25	500.25	Habitual	\N
547	2026-01-30 09:12:00	26	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
548	2026-01-31 10:24:00	27	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
549	2026-02-01 11:36:00	28	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
550	2026-02-02 12:00:00	29	28	2	1750.00	50.00	255.00	1955.00	Habitual	\N
551	2026-02-03 13:12:00	30	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
552	2026-02-04 14:24:00	31	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
553	2026-02-05 15:36:00	32	1	1	3780.00	27.00	562.95	4315.95	Habitual	\N
554	2026-02-06 08:00:00	33	2	2	1900.00	1.50	284.78	2183.28	Habitual	\N
555	2026-02-07 09:12:00	1	3	1	25.00	0.00	3.75	28.75	Fugaz	Cliente rápido #33
556	2026-02-08 10:24:00	35	4	2	1770.00	52.00	257.70	1975.70	Habitual	\N
557	2026-02-09 11:36:00	36	5	1	2350.00	80.00	340.50	2610.50	Habitual	\N
558	2026-02-10 12:00:00	37	6	2	1530.00	0.00	229.50	1759.50	Habitual	\N
559	2026-02-11 13:12:00	38	7	1	3285.00	12.75	490.84	3763.09	Habitual	\N
560	2026-02-12 14:24:00	39	8	2	1620.00	0.75	242.89	1862.14	Habitual	\N
561	2026-02-13 15:36:00	40	9	1	940.00	9.00	139.65	1070.65	Habitual	\N
562	2026-02-14 08:00:00	41	10	2	3385.00	0.00	507.75	3892.75	Habitual	\N
563	2026-02-15 09:12:00	42	11	1	1855.00	36.25	272.81	2091.56	Habitual	\N
564	2026-02-16 10:24:00	43	12	2	4470.00	197.50	640.88	4913.38	Habitual	\N
565	2026-02-17 11:36:00	44	13	1	2395.00	63.75	349.69	2680.94	Habitual	\N
566	2026-02-18 12:00:00	1	14	2	260.00	0.00	39.00	299.00	Fugaz	Cliente rápido #44
567	2026-02-19 13:12:00	46	15	1	1750.00	0.00	262.50	2012.50	Habitual	\N
568	2026-02-20 14:24:00	47	16	2	2940.00	56.00	432.60	3316.60	Habitual	\N
569	2026-02-21 15:36:00	48	17	1	1770.00	29.00	261.15	2002.15	Habitual	\N
570	2026-02-22 08:00:00	49	18	2	968.00	0.00	145.20	1113.20	Habitual	\N
571	2026-02-23 09:12:00	50	19	1	1356.00	101.00	188.25	1443.25	Habitual	\N
572	2026-02-24 10:24:00	51	20	2	3405.00	27.75	506.59	3883.84	Habitual	\N
573	2026-02-25 11:36:00	53	21	1	834.00	2.70	124.70	956.00	Habitual	\N
574	2026-02-26 12:00:00	54	22	2	2062.00	0.00	309.30	2371.30	Habitual	\N
575	2026-02-27 13:12:00	55	23	1	1975.00	24.00	292.65	2243.65	Habitual	\N
576	2026-02-28 14:24:00	56	24	2	435.00	12.00	63.45	486.45	Habitual	\N
577	2026-03-01 15:36:00	1	25	1	340.00	17.00	48.45	371.45	Fugaz	Cliente rápido #55
578	2026-03-02 08:00:00	58	26	2	2730.00	65.00	399.75	3064.75	Habitual	\N
579	2026-03-03 09:12:00	59	27	1	3230.00	0.00	484.50	3714.50	Habitual	\N
580	2026-03-04 10:24:00	60	28	2	1750.00	9.00	261.15	2002.15	Habitual	\N
581	2026-03-05 11:36:00	61	29	1	1780.00	12.00	265.20	2033.20	Habitual	\N
582	2026-03-06 12:00:00	62	30	2	1175.00	0.00	176.25	1351.25	Habitual	\N
583	2026-03-07 13:12:00	63	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
584	2026-03-08 14:24:00	64	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
585	2026-03-09 15:36:00	65	3	1	395.00	56.25	50.81	389.56	Habitual	\N
586	2026-03-10 08:00:00	66	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
587	2026-03-11 09:12:00	67	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
588	2026-03-12 10:24:00	1	6	2	270.00	0.00	40.50	310.50	Fugaz	Cliente rápido #66
589	2026-03-13 11:36:00	69	7	1	3285.00	104.00	477.15	3658.15	Habitual	\N
590	2026-03-14 12:00:00	70	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
591	2026-03-15 13:12:00	71	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
592	2026-03-16 14:24:00	72	10	2	3385.00	88.25	494.51	3791.26	Habitual	\N
593	2026-03-17 15:36:00	73	11	1	1855.00	2.25	277.91	2130.66	Habitual	\N
594	2026-03-18 08:00:00	74	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
595	2026-03-19 09:12:00	75	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
596	2026-03-20 10:24:00	76	14	2	1425.00	14.50	211.58	1622.08	Habitual	\N
597	2026-03-21 11:36:00	77	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
598	2026-03-22 12:00:00	78	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
599	2026-03-23 13:12:00	1	17	1	145.00	50.00	14.25	109.25	Fugaz	Cliente rápido #77
600	2026-03-24 14:24:00	80	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
601	2026-03-25 15:36:00	81	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
602	2026-03-26 08:00:00	82	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
603	2026-03-27 09:12:00	83	21	1	834.00	0.00	125.10	959.10	Habitual	\N
604	2026-03-28 10:24:00	84	22	2	2062.00	85.00	296.55	2273.55	Habitual	\N
605	2026-03-29 11:36:00	85	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
606	2026-03-30 12:00:00	86	24	2	435.00	50.00	57.75	442.75	Habitual	\N
607	2026-03-31 13:12:00	87	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
608	2026-04-01 14:24:00	88	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
609	2026-04-02 15:36:00	89	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
610	2026-04-03 08:00:00	1	28	2	250.00	0.00	37.50	287.50	Fugaz	Cliente rápido #88
611	2026-04-04 09:12:00	91	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
612	2026-04-05 10:24:00	92	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
613	2026-01-12 09:15:00	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
614	2026-01-18 14:30:00	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
615	2026-02-03 10:45:00	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
616	2026-02-11 16:20:00	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
617	2026-02-22 11:10:00	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
618	2026-03-02 13:25:00	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
619	2026-03-09 15:40:00	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
620	2026-03-16 09:50:00	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
621	2026-03-23 17:05:00	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
622	2026-04-05 10:30:00	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
623	2026-04-18 12:15:00	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
624	2026-04-30 18:45:00	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
625	2026-01-06 09:12:00	2	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
626	2026-01-07 10:24:00	3	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
627	2026-01-08 11:36:00	4	3	1	395.00	6.25	58.31	447.06	Habitual	\N
628	2026-01-09 12:00:00	5	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
629	2026-01-10 13:12:00	6	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
630	2026-01-11 14:24:00	7	6	2	1530.00	36.00	224.10	1718.10	Habitual	\N
631	2026-01-12 15:36:00	8	7	1	3285.00	154.00	469.65	3600.65	Habitual	\N
632	2026-01-13 08:00:00	9	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
633	2026-01-14 09:12:00	10	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
634	2026-01-15 10:24:00	11	10	2	3385.00	38.25	502.01	3848.76	Habitual	\N
635	2026-01-16 11:36:00	1	11	1	15.00	0.75	2.14	16.39	Fugaz	Cliente rápido #11
636	2026-01-17 12:00:00	13	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
637	2026-01-18 13:12:00	14	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
638	2026-01-19 14:24:00	15	14	2	1425.00	64.50	204.08	1564.58	Habitual	\N
639	2026-01-20 15:36:00	16	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
640	2026-01-21 08:00:00	17	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
641	2026-01-22 09:12:00	18	17	1	1770.00	9.25	264.11	2024.86	Habitual	\N
642	2026-01-23 10:24:00	19	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
643	2026-01-24 11:36:00	20	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
644	2026-01-25 12:00:00	21	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
645	2026-01-26 13:12:00	22	21	1	834.00	50.00	117.60	901.60	Habitual	\N
646	2026-01-27 14:24:00	1	22	2	28.00	0.00	4.20	32.20	Fugaz	Cliente rápido #22
647	2026-01-28 15:36:00	24	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
648	2026-01-29 08:00:00	25	24	2	435.00	0.00	65.25	500.25	Habitual	\N
649	2026-01-30 09:12:00	26	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
650	2026-01-31 10:24:00	27	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
651	2026-02-01 11:36:00	28	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
652	2026-02-02 12:00:00	29	28	2	1750.00	50.00	255.00	1955.00	Habitual	\N
653	2026-02-03 13:12:00	30	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
654	2026-02-04 14:24:00	31	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
655	2026-02-05 15:36:00	32	1	1	3780.00	27.00	562.95	4315.95	Habitual	\N
656	2026-02-06 08:00:00	33	2	2	1900.00	1.50	284.78	2183.28	Habitual	\N
657	2026-02-07 09:12:00	1	3	1	25.00	0.00	3.75	28.75	Fugaz	Cliente rápido #33
658	2026-02-08 10:24:00	35	4	2	1770.00	52.00	257.70	1975.70	Habitual	\N
659	2026-02-09 11:36:00	36	5	1	2350.00	80.00	340.50	2610.50	Habitual	\N
660	2026-02-10 12:00:00	37	6	2	1530.00	0.00	229.50	1759.50	Habitual	\N
661	2026-02-11 13:12:00	38	7	1	3285.00	12.75	490.84	3763.09	Habitual	\N
662	2026-02-12 14:24:00	39	8	2	1620.00	0.75	242.89	1862.14	Habitual	\N
663	2026-02-13 15:36:00	40	9	1	940.00	9.00	139.65	1070.65	Habitual	\N
664	2026-02-14 08:00:00	41	10	2	3385.00	0.00	507.75	3892.75	Habitual	\N
665	2026-02-15 09:12:00	42	11	1	1855.00	36.25	272.81	2091.56	Habitual	\N
666	2026-02-16 10:24:00	43	12	2	4470.00	197.50	640.88	4913.38	Habitual	\N
667	2026-02-17 11:36:00	44	13	1	2395.00	63.75	349.69	2680.94	Habitual	\N
668	2026-02-18 12:00:00	1	14	2	260.00	0.00	39.00	299.00	Fugaz	Cliente rápido #44
669	2026-02-19 13:12:00	46	15	1	1750.00	0.00	262.50	2012.50	Habitual	\N
670	2026-02-20 14:24:00	47	16	2	2940.00	56.00	432.60	3316.60	Habitual	\N
671	2026-02-21 15:36:00	48	17	1	1770.00	29.00	261.15	2002.15	Habitual	\N
672	2026-02-22 08:00:00	49	18	2	968.00	0.00	145.20	1113.20	Habitual	\N
673	2026-02-23 09:12:00	50	19	1	1356.00	101.00	188.25	1443.25	Habitual	\N
674	2026-02-24 10:24:00	51	20	2	3405.00	27.75	506.59	3883.84	Habitual	\N
675	2026-02-25 11:36:00	53	21	1	834.00	2.70	124.70	956.00	Habitual	\N
676	2026-02-26 12:00:00	54	22	2	2062.00	0.00	309.30	2371.30	Habitual	\N
677	2026-02-27 13:12:00	55	23	1	1975.00	24.00	292.65	2243.65	Habitual	\N
678	2026-02-28 14:24:00	56	24	2	435.00	12.00	63.45	486.45	Habitual	\N
679	2026-03-01 15:36:00	1	25	1	340.00	17.00	48.45	371.45	Fugaz	Cliente rápido #55
680	2026-03-02 08:00:00	58	26	2	2730.00	65.00	399.75	3064.75	Habitual	\N
681	2026-03-03 09:12:00	59	27	1	3230.00	0.00	484.50	3714.50	Habitual	\N
682	2026-03-04 10:24:00	60	28	2	1750.00	9.00	261.15	2002.15	Habitual	\N
683	2026-03-05 11:36:00	61	29	1	1780.00	12.00	265.20	2033.20	Habitual	\N
684	2026-03-06 12:00:00	62	30	2	1175.00	0.00	176.25	1351.25	Habitual	\N
685	2026-03-07 13:12:00	63	1	1	3780.00	130.00	547.50	4197.50	Habitual	\N
686	2026-03-08 14:24:00	64	2	2	1900.00	75.00	273.75	2098.75	Habitual	\N
687	2026-03-09 15:36:00	65	3	1	395.00	56.25	50.81	389.56	Habitual	\N
688	2026-03-10 08:00:00	66	4	2	1770.00	0.00	265.50	2035.50	Habitual	\N
689	2026-03-11 09:12:00	67	5	1	2350.00	3.00	352.05	2699.05	Habitual	\N
690	2026-03-12 10:24:00	1	6	2	270.00	0.00	40.50	310.50	Fugaz	Cliente rápido #66
691	2026-03-13 11:36:00	69	7	1	3285.00	104.00	477.15	3658.15	Habitual	\N
692	2026-03-14 12:00:00	70	8	2	1620.00	21.75	239.74	1837.99	Habitual	\N
693	2026-03-15 13:12:00	71	9	1	940.00	0.00	141.00	1081.00	Habitual	\N
694	2026-03-16 14:24:00	72	10	2	3385.00	88.25	494.51	3791.26	Habitual	\N
695	2026-03-17 15:36:00	73	11	1	1855.00	2.25	277.91	2130.66	Habitual	\N
696	2026-03-18 08:00:00	74	12	2	4470.00	0.00	670.50	5140.50	Habitual	\N
697	2026-03-19 09:12:00	75	13	1	2395.00	28.00	355.05	2722.05	Habitual	\N
698	2026-03-20 10:24:00	76	14	2	1425.00	14.50	211.58	1622.08	Habitual	\N
699	2026-03-21 11:36:00	77	15	1	1750.00	59.00	253.65	1944.65	Habitual	\N
700	2026-03-22 12:00:00	78	16	2	2940.00	0.00	441.00	3381.00	Habitual	\N
701	2026-03-23 13:12:00	1	17	1	145.00	50.00	14.25	109.25	Fugaz	Cliente rápido #77
702	2026-03-24 14:24:00	80	18	2	968.00	0.90	145.07	1112.17	Habitual	\N
703	2026-03-25 15:36:00	81	19	1	1356.00	14.00	201.30	1543.30	Habitual	\N
704	2026-03-26 08:00:00	82	20	2	3405.00	60.00	501.75	3846.75	Habitual	\N
705	2026-03-27 09:12:00	83	21	1	834.00	0.00	125.10	959.10	Habitual	\N
706	2026-03-28 10:24:00	84	22	2	2062.00	85.00	296.55	2273.55	Habitual	\N
707	2026-03-29 11:36:00	85	23	1	1975.00	46.25	289.31	2218.06	Habitual	\N
708	2026-03-30 12:00:00	86	24	2	435.00	50.00	57.75	442.75	Habitual	\N
709	2026-03-31 13:12:00	87	25	1	2150.00	36.00	317.10	2431.10	Habitual	\N
710	2026-04-01 14:24:00	88	26	2	2730.00	48.00	402.30	3084.30	Habitual	\N
711	2026-04-02 15:36:00	89	27	1	3230.00	24.00	480.90	3686.90	Habitual	\N
712	2026-04-03 08:00:00	1	28	2	250.00	0.00	37.50	287.50	Fugaz	Cliente rápido #88
713	2026-04-04 09:12:00	91	29	1	1780.00	45.00	260.25	1995.25	Habitual	\N
714	2026-04-05 10:24:00	92	30	2	1175.00	3.75	175.69	1346.94	Habitual	\N
\.


--
-- Data for Name: producto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.producto (id_producto, codigo, nombre, descripcion, imagen, id_categoria, id_proveedor, precio_compra, precio_venta, stock) FROM stdin;
1	P001	Camiseta Negra Premium	Camiseta de algodón negro premium, ideal para estampados full color y uso diario.	prod_d67e3785273607d89e61a401.png	1	1	120.00	250.00	55
2	P002	Camiseta Blanca Clásica	Camiseta blanca clásica, perfecta para sublimación y estampados personalizados.	prod_69ab914e50de9c2ab70f17e2.jpg	1	1	115.00	240.00	70
4	P004	Taza Mágica Full Color	Taza mágica que revela el diseño al contacto con agua caliente, impresión full color.	prod_77799003da6531f02abb08fb.jpg	3	2	90.00	180.00	40
5	P005	Llavero Acrílico Panda	Llavero acrílico con forma de panda, resistente y ligero para uso diario.	prod_67817b473371f51443a44144.jpg	4	3	20.00	60.00	150
6	P006	Sticker Holográfico Kitsune	Sticker holográfico con diseño de kitsune, acabado brillante y resistente al agua.	prod_5b3fca06c807b9fed7369b0e.jpg	6	4	5.00	25.00	300
7	P007	Bolso Tote Reforzado	Bolso tote de lona reforzada, ideal para compras, estudio o uso promocional.	prod_5864c2b76949a1ffd7fb6bbb.jpg	7	1	80.00	160.00	35
8	P008	Termo de Acero Panda	Termo de acero inoxidable con diseño de panda, conserva la temperatura por horas.	prod_f5368854e9de4fe5d5ef70c1.jpg	8	2	160.00	300.00	25
9	P009	Camiseta Roja Edición Limitada	Camiseta roja de edición limitada, tela suave y corte unisex para colecciones especiales.	prod_977042d6b3d3bdc28500a0b5.jpg	1	1	135.00	270.00	40
11	P011	Taza Blanca Clásica 11oz	Taza blanca de 11oz lista para sublimación, perfecta para pedidos al por mayor.	prod_ee0704d79c323b08c7071c3e.jpg	3	2	60.00	130.00	80
12	P012	Gorra Snapback Panda	Gorra estilo snapback con logo de panda, ajustable y lista para personalización.	prod_3bc17b8d1377a4826a2d8912.jpg	4	3	90.00	180.00	25
13	P013	Vinilo Decorativo Pared	Vinilo decorativo para pared, fácil de colocar y remover, ideal para habitaciones y oficinas.	prod_e155b0fa66e83067205236e4.jpg	5	4	70.00	190.00	60
14	P014	Pack Stickers Surtidos	Pack de stickers surtidos con diferentes diseños de Panda y Kitsune, acabado mate.	prod_bbf90800dd3fd1f24deb27b4.webp	6	4	3.00	15.00	500
28	P028	Termo Blanco Sublimable	Termo blanco preparado para sublimación full color.	\N	8	2	180.00	340.00	18
3	P003	Hoodie Oversize Negro	Hoodie oversize en color negro, tela gruesa y suave, ideal para colección urbana.	prod_3358337933ce52ca1d0d187f.jpg	2	1	350.00	550.00	4
10	P010	Hoodie Gris con Cierre	Hoodie gris con cierre frontal y capucha, ideal para bordado o impresión frontal.	prod_8b600c2980bacbe62c44cbfd.jpg	2	1	320.00	520.00	3
15	P015	Termo Kitsune con Luz LED	Termo Kitsune con tapa iluminada LED, ideal para regalos y colecciones especiales.	prod_bd9ae845c714f7f64699fb75.jpg	8	2	220.00	380.00	2
17	P017	Camiseta Verde Oliva	Camiseta verde oliva para colecciones urbanas y estilo casual.	prod_69f6c653e32401.87569221.jpg	1	1	128.00	260.00	38
18	P018	Hoodie Blanco Premium	Hoodie blanco premium para impresión frontal y posterior.	prod_69f6c65c9eb2e5.53695549.png	2	1	360.00	590.00	5
29	P029	Termo Negro Premium	Termo negro premium con acabado elegante para regalos corporativos.	prod_69f6c66b983df3.09022978.jpg	8	2	210.00	390.00	4
19	P019	Hoodie Rojo Urbano	Hoodie rojo con tela gruesa para diseños de temporada.	prod_69f6c6751074b5.45935088.jpg	2	1	340.00	560.00	1
27	P027	Bolso Negro Panda	Bolso negro con impresión Panda Estampados.	prod_69f6c62f0065b5.42642135.jpg	7	1	95.00	195.00	20
26	P026	Bolso Tote Anime	Bolso tote con diseño anime, resistente y reutilizable.	prod_69f6c6396faa95.38461052.jpg	7	1	90.00	185.00	28
16	P016	Camiseta Azul Marino	Camiseta azul marino para diseños minimalistas y estampados sobrios.	prod_69f6c64107cbe3.98815758.jpg	1	1	125.00	255.00	45
30	P030	Vinilo Laptop Kitsune	Vinilo adhesivo para laptop con diseño Kitsune personalizado.	prod_69f6c64c6e4da6.21583923.jpg	5	4	45.00	120.00	70
22	P022	Mousepad Gamer XL	Mousepad extendido para escritorio gamer con impresión personalizada.	prod_69f6c67e810a30.66453496.jpg	4	4	120.00	280.00	22
23	P023	Pin Acrílico Kitsune	Pin acrílico pequeño con diseño Kitsune coleccionable.	prod_69f6c6861839d2.16895557.png	4	3	12.00	45.00	180
24	P024	Sticker Mate Panda	Sticker acabado mate resistente al agua.	prod_69f6c6937a3a33.43371185.jpg	6	4	4.00	18.00	420
21	P021	Taza Anime Full Print	Taza con área completa para diseños de anime y videojuegos.	prod_69f6c69d6f3638.89424185.png	3	2	85.00	190.00	35
20	P020	Taza Negra 11oz	Taza negra clásica para sublimación y regalos personalizados.	prod_69f6c6ad97f050.78349385.png	3	2	65.00	145.00	55
25	P025	Sticker Vinil Anime	Sticker vinilado para laptops, botellas y libretas.	prod_69f6c6bb44b063.56128007.jpg	6	4	6.00	28.00	260
\.


--
-- Data for Name: proveedor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.proveedor (id_proveedor, nombre, telefono, email, direccion) FROM stdin;
1	Textiles Premium S.A	8888-1111	ventas@textilespremium.com	Managua
2	Cerámica Creativa	7788-5511	info@ceramicacreativa.com	León
3	Hilos & Más	7654-3321	contacto@hilosexport.com	Masaya
4	EstampadosXYZ	9988-7766	ventas@xyz.com	Granada
5	Sublimaciones Centro	2255-7788	info@sublimcentro.com	Managua
6	Impresiones Delta	2299-4455	ventas@impresionesdelta.com	Carazo
7	Textiles Premium S.A	8888-1111	ventas@textilespremium.com	Managua
8	Cerámica Creativa	7788-5511	info@ceramicacreativa.com	León
9	Hilos & Más	7654-3321	contacto@hilosexport.com	Masaya
10	EstampadosXYZ	9988-7766	ventas@xyz.com	Granada
11	Sublimaciones Centro	2255-7788	info@sublimcentro.com	Managua
12	Impresiones Delta	2299-4455	ventas@impresionesdelta.com	Carazo
13	Textiles Premium S.A	8888-1111	ventas@textilespremium.com	Managua
14	Cerámica Creativa	7788-5511	info@ceramicacreativa.com	León
15	Hilos & Más	7654-3321	contacto@hilosexport.com	Masaya
16	EstampadosXYZ	9988-7766	ventas@xyz.com	Granada
17	Sublimaciones Centro	2255-7788	info@sublimcentro.com	Managua
18	Impresiones Delta	2299-4455	ventas@impresionesdelta.com	Carazo
19	Proveedor General	2222-3333	proveedor@example.com	Managua
20	Textiles Premium S.A	8888-1111	ventas@textilespremium.com	Managua
21	Cerámica Creativa	7788-5511	info@ceramicacreativa.com	León
22	Hilos & Más	7654-3321	contacto@hilosexport.com	Masaya
23	EstampadosXYZ	9988-7766	ventas@xyz.com	Granada
24	Sublimaciones Centro	2255-7788	info@sublimcentro.com	Managua
25	Impresiones Delta	2299-4455	ventas@impresionesdelta.com	Carazo
26	Textiles Premium S.A	8888-1111	ventas@textilespremium.com	Managua
27	Cerámica Creativa	7788-5511	info@ceramicacreativa.com	León
28	Hilos & Más	7654-3321	contacto@hilosexport.com	Masaya
29	EstampadosXYZ	9988-7766	ventas@xyz.com	Granada
30	Sublimaciones Centro	2255-7788	info@sublimcentro.com	Managua
31	Impresiones Delta	2299-4455	ventas@impresionesdelta.com	Carazo
32	Textiles Premium S.A	8888-1111	ventas@textilespremium.com	Managua
33	Cerámica Creativa	7788-5511	info@ceramicacreativa.com	León
34	Hilos & Más	7654-3321	contacto@hilosexport.com	Masaya
35	EstampadosXYZ	9988-7766	ventas@xyz.com	Granada
36	Sublimaciones Centro	2255-7788	info@sublimcentro.com	Managua
37	Impresiones Delta	2299-4455	ventas@impresionesdelta.com	Carazo
38	Textiles Premium S.A	8888-1111	ventas@textilespremium.com	Managua
39	Cerámica Creativa	7788-5511	info@ceramicacreativa.com	León
40	Hilos & Más	7654-3321	contacto@hilosexport.com	Masaya
41	EstampadosXYZ	9988-7766	ventas@xyz.com	Granada
42	Sublimaciones Centro	2255-7788	info@sublimcentro.com	Managua
43	Impresiones Delta	2299-4455	ventas@impresionesdelta.com	Carazo
\.


--
-- Data for Name: rol; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rol (id_rol, nombre) FROM stdin;
1	Administrador
2	Supervisor
3	Facturador
\.


--
-- Data for Name: seccion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.seccion (id_seccion, nombre) FROM stdin;
1	Panda Estampados
2	Kitsune
\.


--
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuario (id_usuario, nombre, email, password, id_rol, id_seccion) FROM stdin;
2	Daniel Pérez	daniel.perez@kitsune.com	$2y$12$q/FLcSnscgAJx5KLw6ZfkeKmqVNjVgsid.NzF1vIA7bFvwqZvrtEm	2	2
3	Jeremy Pérez	jeremy.perez@kitsune.com	$2y$12$9cCSd.m5eccghVDHnx1hZOM1KYPHUzQZsJP3Wx4rVYA6L.zbRLuiK	2	2
4	Jhossep Ramos	jhossep.ramos@kitsune.com	$2y$12$6iu4FfS3vWAN3g0dCpaJH./pq4u2fsOQmujzvPxtcoxX1/VhgmV8W	2	2
5	Diego Torres	diego.torres@kitsune.com	$2y$12$8rYAJPYHfmBYfUsvSQ6JQeYzXY/dfhv5minqtioSOT/OYNpYhKfx2	2	2
6	Carlos Núñez	carlos.nunez@kitsune.com	$2y$12$Wktc.2kJD1t1addvJTy6h.HZsofpoPdinZfkNuQpOzT6x1PdxjcbW	2	2
7	Mónica Larios	monica.larios@kitsune.com	$2y$12$VfV8bO4qFkH5AW42IEEeBebUH2OG8E24/adYxBHpkgNxxbpwpDlKe	2	2
8	Esteban Rodríguez	esteban.rodriguez@kitsune.com	$2y$12$6xrE3MOnkmTmYvQDepNuB.dxG7I2BFg9enlTc2Qu8s0JzmDYVm1ze	2	2
9	Eduardo Molina	eduardo.molina@kitsune.com	$2y$12$NHgiv5ky8RBEP.vlKcDZUeiV2ZjiRENZmcmnS9lWm7NsFl3N8Gqom	2	2
10	Andy Sánchez	andy.sanchez@panda.com	$2y$12$h/WagOY4zymqWqcyccvi7.ikKYZxbFknFzm6bqlEfySGINOBxM5US	3	2
11	Sofía Gómez	sofia.gomez@kitsune.com	$2y$12$hVV/KUpCbUX9EOKMueVA3Ozsdgx4na8c9K.HN7Y2LDB9YpoOuX72a	3	2
12	Luis Torres	luis.torres@panda.com	$2y$12$NMsgDw0o0RgTCGtdX9EY8eyhderZcaV4/VPHZOPYR52NJIrde/8q.	3	2
13	Carla Bermúdez	carla.bermudez@kitsune.com	$2y$12$ZuE7Qa26mz2IlL0hl7CW1u/1KL5Bw.Sx8syeol1a7Xm2LXssw7oC6	3	2
14	Karla Medina	karla.medina@panda.com	$2y$12$D5j0CPjHq4paoSBtFFBCDeFHcAd4Lm9onOQYPM9zBRRDJ3OWN/ml6	3	2
15	Wilmer Ruiz	wilmer.ruiz@kitsune.com	$2y$12$br0QmLjZnqcHE.UzWSpQieJYav6W4Fxqi1s8vrBwQuC0g2q0OSnHi	3	2
16	Miguel Hernández	miguel.hernandez@panda.com	$2y$12$H.Ui0n/1U4Ae32AI.C9bbOfSISzETo2MHFKkZWFot8UrObFuesTQ2	3	2
17	Paola López	paola.lopez@panda.com	$2y$12$FDyaXbpcOqlWn0dIk/XHTumo42ZM1aX1U.XZ5wMHtQ7lQo7LgQMOa	3	2
18	Kevin Castillo	kevin.castillo@panda.com	$2y$12$2kA9hBSf/QPIJuDf90I2luKFTeh95mGZqISMqBIdtIe8oFnbk45H2	3	2
19	María Fernández	maria.fernandez@kitsune.com	$2y$12$c1izNLMr5EpE1qhi4qB67evlAxuc8EDdQK09lFcoJzUeNdt0RoWTm	3	2
20	Josefina Rivas	josefina.rivas@kitsune.com	$2y$12$UBJcyEORA9eFtgMVbFqu3u327RnKP1PkiA2Fd.XmA.rKom1FrDISG	3	2
21	Roberto Gutiérrez	roberto.gutierrez@kitsune.com	$2y$12$.PixiEX1MN9hves7DgCE1eUm3vsLz04Mqdd35rzbPX8yFCLEqOpzG	3	2
22	Lucía Herrera	lucia.herrera@kitsune.com	$2y$12$zmQ8NBBVOrw.aO2Umt.hNumWlPWAJ2aA1PxMlz/A0I.jT6o6tl2Rq	3	2
23	Brandon Morales	brandon.morales@kitsune.com	$2y$12$xuhd3.8.x3E4ZKo9nh0yZev8bIXCxd1aDhzlhfpD8YcxTfDTBdUf2	3	2
24	Andrea Vega	andrea.vega@panda.com	$2y$12$CBUWv03lSPKoAbh6gVnbyO4xlbmSAJW0Tkl.QQWbpPHbj7iumaYoG	3	2
25	Sergio Mairena	sergio.mairena@panda.com	$2y$12$2i1oGVK1n6q9D0Iluy3mnuE.lCulA0tfM1pc09Xc8zHn/zAKLZIdW	3	2
26	Julia Campos	julia.campos@panda.com	$2y$12$F7zrVU53XF1dSiV3F8xcjuJrcXNAXgieRNXAhFXMOyBOeRdZPswoy	3	2
27	Laura Castillo	laura.castillo@admin.pandakitsune.com	$2y$12$.inALdr.Qy5DIq3dSxKjVuPSYFTL20B3VL3orm2BwxSSqwKsM3UkC	1	\N
28	Óscar Mejía	oscar.mejia@admin.pandakitsune.com	$2y$12$n1ZhPkXE/LMCwggOgwtHkus4Lt/XPkpgBDkz.lCdIGTU9.Kt1CV2y	1	\N
29	Carmen Rojas	carmen.rojas@panda.com	$2y$12$PsrYOcMFI9eBZlOJzV9RQOMx7uI9cm.3qqPk1tQYz6jxr2fIqhIjO	3	2
30	Nidia Solís	nidia.solis@kitsune.com	$2y$12$a9PzdMLz8ZNtGEDl6tPxnuMRq2qoYHCh55RlpcCDgC7nGvRxl2D7C	3	2
1	Leonel Messi	leonel.messi@admin.pandakitsune.com	$2y$10$oCDDt/YuxYESRT8888zim.7Mn1AsfYVBXbVOgesp.1CLQuhuBxo2m	1	\N
\.


--
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.auditoria_id_auditoria_seq', 1, true);


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categoria_id_categoria_seq', 15, true);


--
-- Name: cliente_id_cliente_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cliente_id_cliente_seq', 357, true);


--
-- Name: compra_id_compra_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.compra_id_compra_seq', 287, true);


--
-- Name: detallecompra_id_detalle_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detallecompra_id_detalle_seq', 567, true);


--
-- Name: detallefactura_id_detalle_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detallefactura_id_detalle_seq', 1925, true);


--
-- Name: factura_id_factura_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.factura_id_factura_seq', 714, true);


--
-- Name: producto_id_producto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.producto_id_producto_seq', 126, true);


--
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.proveedor_id_proveedor_seq', 43, true);


--
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rol_id_rol_seq', 9, true);


--
-- Name: seccion_id_seccion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.seccion_id_seccion_seq', 8, true);


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuario_id_usuario_seq', 36, true);


--
-- Name: auditoria auditoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria
    ADD CONSTRAINT auditoria_pkey PRIMARY KEY (id_auditoria);


--
-- Name: categoria categoria_nombre_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_nombre_key UNIQUE (nombre);


--
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (id_categoria);


--
-- Name: cliente cliente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (id_cliente);


--
-- Name: compra compra_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT compra_pkey PRIMARY KEY (id_compra);


--
-- Name: detallecompra detallecompra_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallecompra
    ADD CONSTRAINT detallecompra_pkey PRIMARY KEY (id_detalle);


--
-- Name: detallefactura detallefactura_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallefactura
    ADD CONSTRAINT detallefactura_pkey PRIMARY KEY (id_detalle);


--
-- Name: factura factura_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT factura_pkey PRIMARY KEY (id_factura);


--
-- Name: producto producto_codigo_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_codigo_key UNIQUE (codigo);


--
-- Name: producto producto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id_producto);


--
-- Name: proveedor proveedor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proveedor
    ADD CONSTRAINT proveedor_pkey PRIMARY KEY (id_proveedor);


--
-- Name: rol rol_nombre_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rol
    ADD CONSTRAINT rol_nombre_key UNIQUE (nombre);


--
-- Name: rol rol_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rol
    ADD CONSTRAINT rol_pkey PRIMARY KEY (id_rol);


--
-- Name: seccion seccion_nombre_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seccion
    ADD CONSTRAINT seccion_nombre_key UNIQUE (nombre);


--
-- Name: seccion seccion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seccion
    ADD CONSTRAINT seccion_pkey PRIMARY KEY (id_seccion);


--
-- Name: usuario usuario_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_email_key UNIQUE (email);


--
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usuario);


--
-- Name: compra fk_compra_proveedor; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT fk_compra_proveedor FOREIGN KEY (id_proveedor) REFERENCES public.proveedor(id_proveedor);


--
-- Name: compra fk_compra_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT fk_compra_usuario FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: detallecompra fk_detcom_compra; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallecompra
    ADD CONSTRAINT fk_detcom_compra FOREIGN KEY (id_compra) REFERENCES public.compra(id_compra);


--
-- Name: detallecompra fk_detcom_producto; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallecompra
    ADD CONSTRAINT fk_detcom_producto FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: detallefactura fk_detfac_factura; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallefactura
    ADD CONSTRAINT fk_detfac_factura FOREIGN KEY (id_factura) REFERENCES public.factura(id_factura);


--
-- Name: detallefactura fk_detfac_producto; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallefactura
    ADD CONSTRAINT fk_detfac_producto FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: factura fk_factura_cliente; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT fk_factura_cliente FOREIGN KEY (id_cliente) REFERENCES public.cliente(id_cliente);


--
-- Name: factura fk_factura_seccion; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT fk_factura_seccion FOREIGN KEY (id_seccion) REFERENCES public.seccion(id_seccion);


--
-- Name: factura fk_factura_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT fk_factura_usuario FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: producto fk_producto_categoria; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria) REFERENCES public.categoria(id_categoria);


--
-- Name: producto fk_producto_proveedor; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT fk_producto_proveedor FOREIGN KEY (id_proveedor) REFERENCES public.proveedor(id_proveedor);


--
-- Name: usuario fk_usuario_rol; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol);


--
-- Name: usuario fk_usuario_seccion; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT fk_usuario_seccion FOREIGN KEY (id_seccion) REFERENCES public.seccion(id_seccion);


--
-- PostgreSQL database dump complete
--

\unrestrict axiMUggbOKzce8AyZv953D8NfZ77f7m8iiLlPGu3mJzpRthKudvmliRAg0pggI1

