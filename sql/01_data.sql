--
-- PostgreSQL database dump
--

\restrict y787Pzyhd6oy6rDEjal4xbAgXzG2p5Hl4D1QUvYHFfS53ea1ET66FcmCDcFJb15

-- Dumped from database version 18.3 (Debian 18.3-1.pgdg13+1)
-- Dumped by pg_dump version 18.3 (Debian 18.3-1.pgdg12+1)

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

ALTER TABLE IF EXISTS ONLY public.usuario DROP CONSTRAINT IF EXISTS fk_usuario_seccion;
ALTER TABLE IF EXISTS ONLY public.usuario DROP CONSTRAINT IF EXISTS fk_usuario_rol;
ALTER TABLE IF EXISTS ONLY public.producto DROP CONSTRAINT IF EXISTS fk_producto_proveedor;
ALTER TABLE IF EXISTS ONLY public.producto DROP CONSTRAINT IF EXISTS fk_producto_categoria;
ALTER TABLE IF EXISTS ONLY public.factura DROP CONSTRAINT IF EXISTS fk_factura_usuario;
ALTER TABLE IF EXISTS ONLY public.factura DROP CONSTRAINT IF EXISTS fk_factura_seccion;
ALTER TABLE IF EXISTS ONLY public.factura DROP CONSTRAINT IF EXISTS fk_factura_cliente;
ALTER TABLE IF EXISTS ONLY public.detallefactura DROP CONSTRAINT IF EXISTS fk_detfac_producto;
ALTER TABLE IF EXISTS ONLY public.detallefactura DROP CONSTRAINT IF EXISTS fk_detfac_factura;
ALTER TABLE IF EXISTS ONLY public.detallecompra DROP CONSTRAINT IF EXISTS fk_detcom_producto;
ALTER TABLE IF EXISTS ONLY public.detallecompra DROP CONSTRAINT IF EXISTS fk_detcom_compra;
ALTER TABLE IF EXISTS ONLY public.compra DROP CONSTRAINT IF EXISTS fk_compra_usuario;
ALTER TABLE IF EXISTS ONLY public.compra DROP CONSTRAINT IF EXISTS fk_compra_proveedor;
ALTER TABLE IF EXISTS ONLY public.auditoria DROP CONSTRAINT IF EXISTS fk_auditoria_usuario;
DROP TRIGGER IF EXISTS trg_auditar_delete_proveedor ON public.proveedor;
DROP TRIGGER IF EXISTS trg_auditar_delete_producto ON public.producto;
DROP TRIGGER IF EXISTS trg_auditar_delete_cliente ON public.cliente;
DROP TRIGGER IF EXISTS trg_auditar_delete_categoria ON public.categoria;
ALTER TABLE IF EXISTS ONLY public.usuario DROP CONSTRAINT IF EXISTS usuario_pkey;
ALTER TABLE IF EXISTS ONLY public.usuario DROP CONSTRAINT IF EXISTS usuario_email_key;
ALTER TABLE IF EXISTS ONLY public.seccion DROP CONSTRAINT IF EXISTS seccion_pkey;
ALTER TABLE IF EXISTS ONLY public.seccion DROP CONSTRAINT IF EXISTS seccion_nombre_key;
ALTER TABLE IF EXISTS ONLY public.rol DROP CONSTRAINT IF EXISTS rol_pkey;
ALTER TABLE IF EXISTS ONLY public.rol DROP CONSTRAINT IF EXISTS rol_nombre_key;
ALTER TABLE IF EXISTS ONLY public.proveedor DROP CONSTRAINT IF EXISTS proveedor_pkey;
ALTER TABLE IF EXISTS ONLY public.producto DROP CONSTRAINT IF EXISTS producto_pkey;
ALTER TABLE IF EXISTS ONLY public.producto DROP CONSTRAINT IF EXISTS producto_codigo_key;
ALTER TABLE IF EXISTS ONLY public.factura DROP CONSTRAINT IF EXISTS factura_pkey;
ALTER TABLE IF EXISTS ONLY public.detallefactura DROP CONSTRAINT IF EXISTS detallefactura_pkey;
ALTER TABLE IF EXISTS ONLY public.detallecompra DROP CONSTRAINT IF EXISTS detallecompra_pkey;
ALTER TABLE IF EXISTS ONLY public.compra DROP CONSTRAINT IF EXISTS compra_pkey;
ALTER TABLE IF EXISTS ONLY public.cliente DROP CONSTRAINT IF EXISTS cliente_pkey;
ALTER TABLE IF EXISTS ONLY public.categoria DROP CONSTRAINT IF EXISTS categoria_pkey;
ALTER TABLE IF EXISTS ONLY public.categoria DROP CONSTRAINT IF EXISTS categoria_nombre_key;
ALTER TABLE IF EXISTS ONLY public.auditoria DROP CONSTRAINT IF EXISTS auditoria_pkey;
ALTER TABLE IF EXISTS public.usuario ALTER COLUMN id_usuario DROP DEFAULT;
ALTER TABLE IF EXISTS public.seccion ALTER COLUMN id_seccion DROP DEFAULT;
ALTER TABLE IF EXISTS public.rol ALTER COLUMN id_rol DROP DEFAULT;
ALTER TABLE IF EXISTS public.proveedor ALTER COLUMN id_proveedor DROP DEFAULT;
ALTER TABLE IF EXISTS public.producto ALTER COLUMN id_producto DROP DEFAULT;
ALTER TABLE IF EXISTS public.factura ALTER COLUMN id_factura DROP DEFAULT;
ALTER TABLE IF EXISTS public.detallefactura ALTER COLUMN id_detalle DROP DEFAULT;
ALTER TABLE IF EXISTS public.detallecompra ALTER COLUMN id_detalle DROP DEFAULT;
ALTER TABLE IF EXISTS public.compra ALTER COLUMN id_compra DROP DEFAULT;
ALTER TABLE IF EXISTS public.cliente ALTER COLUMN id_cliente DROP DEFAULT;
ALTER TABLE IF EXISTS public.categoria ALTER COLUMN id_categoria DROP DEFAULT;
ALTER TABLE IF EXISTS public.auditoria ALTER COLUMN id_auditoria DROP DEFAULT;
DROP SEQUENCE IF EXISTS public.usuario_id_usuario_seq;
DROP TABLE IF EXISTS public.usuario;
DROP SEQUENCE IF EXISTS public.seccion_id_seccion_seq;
DROP TABLE IF EXISTS public.seccion;
DROP SEQUENCE IF EXISTS public.rol_id_rol_seq;
DROP TABLE IF EXISTS public.rol;
DROP SEQUENCE IF EXISTS public.proveedor_id_proveedor_seq;
DROP TABLE IF EXISTS public.proveedor;
DROP SEQUENCE IF EXISTS public.producto_id_producto_seq;
DROP TABLE IF EXISTS public.producto;
DROP SEQUENCE IF EXISTS public.factura_id_factura_seq;
DROP TABLE IF EXISTS public.factura;
DROP SEQUENCE IF EXISTS public.detallefactura_id_detalle_seq;
DROP TABLE IF EXISTS public.detallefactura;
DROP SEQUENCE IF EXISTS public.detallecompra_id_detalle_seq;
DROP TABLE IF EXISTS public.detallecompra;
DROP SEQUENCE IF EXISTS public.compra_id_compra_seq;
DROP TABLE IF EXISTS public.compra;
DROP SEQUENCE IF EXISTS public.cliente_id_cliente_seq;
DROP TABLE IF EXISTS public.cliente;
DROP SEQUENCE IF EXISTS public.categoria_id_categoria_seq;
DROP TABLE IF EXISTS public.categoria;
DROP SEQUENCE IF EXISTS public.auditoria_id_auditoria_seq;
DROP TABLE IF EXISTS public.auditoria;
DROP FUNCTION IF EXISTS public.validar_factura_existe(p_id_factura integer);
DROP FUNCTION IF EXISTS public.registrar_producto_formulario(p_codigo character varying, p_nombre character varying, p_descripcion text, p_imagen character varying, p_id_categoria integer, p_id_proveedor integer, p_precio_compra numeric, p_precio_venta numeric, p_stock integer);
DROP PROCEDURE IF EXISTS public.registrar_producto(IN p_codigo character varying, IN p_nombre character varying, IN p_descripcion text, IN p_imagen character varying, IN p_id_categoria integer, IN p_id_proveedor integer, IN p_precio_compra numeric, IN p_precio_venta numeric, IN p_stock integer);
DROP FUNCTION IF EXISTS public.registrar_factura_sistema(p_id_cliente integer, p_id_usuario integer, p_id_seccion integer, p_subtotal numeric, p_descuento numeric, p_impuesto numeric, p_total numeric, p_tipo_cliente_venta character varying, p_nombre_cliente_fugaz character varying, p_items jsonb);
DROP FUNCTION IF EXISTS public.registrar_cliente_sistema(p_nombres character varying, p_apellidos character varying, p_telefono character varying, p_direccion character varying, p_identificacion character varying, p_tipo_cliente character varying);
DROP PROCEDURE IF EXISTS public.registrar_cliente(IN p_nombres character varying, IN p_apellidos character varying, IN p_telefono character varying, IN p_direccion character varying, IN p_identificacion character varying, IN p_tipo_cliente character varying);
DROP FUNCTION IF EXISTS public.registrar_categoria(p_nombre character varying);
DROP PROCEDURE IF EXISTS public.registrar_auditoria(IN p_usuario character varying, IN p_accion character varying, IN p_tabla_afectada character varying, IN p_descripcion text);
DROP FUNCTION IF EXISTS public.obtener_ventas_por_dia_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.obtener_ventas_detalladas_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.obtener_ventas_dashboard(p_dias integer);
DROP FUNCTION IF EXISTS public.obtener_usuario_login(p_email character varying);
DROP FUNCTION IF EXISTS public.obtener_usuario_edicion_por_id(p_id_usuario integer);
DROP FUNCTION IF EXISTS public.obtener_usuario_configurar_cuenta(p_id_usuario integer);
DROP FUNCTION IF EXISTS public.obtener_ultimos_productos_vendidos_dashboard(p_limite integer);
DROP FUNCTION IF EXISTS public.obtener_ultimas_facturas_cliente(p_id_cliente integer, p_limite integer);
DROP FUNCTION IF EXISTS public.obtener_total_ventas_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.obtener_total_facturas_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.obtener_total_clientes_reportes();
DROP FUNCTION IF EXISTS public.obtener_stock_bajo_reportes();
DROP FUNCTION IF EXISTS public.obtener_seccion_por_nombre(p_nombre character varying);
DROP FUNCTION IF EXISTS public.obtener_seccion_por_id(p_id_seccion integer);
DROP FUNCTION IF EXISTS public.obtener_resumen_cliente(p_id_cliente integer);
DROP FUNCTION IF EXISTS public.obtener_proveedor_por_id(p_id_proveedor integer);
DROP FUNCTION IF EXISTS public.obtener_productos_reporte(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.obtener_productos_mas_vendidos_reportes(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.obtener_productos_mas_vendidos_dashboard(p_limite integer);
DROP FUNCTION IF EXISTS public.obtener_productos_factura_por_ids(p_ids_productos integer[]);
DROP FUNCTION IF EXISTS public.obtener_producto_imagen(p_id_producto integer);
DROP FUNCTION IF EXISTS public.obtener_producto_edicion_por_id(p_id_producto integer);
DROP FUNCTION IF EXISTS public.obtener_metricas_dashboard();
DROP FUNCTION IF EXISTS public.obtener_lineas_factura_para_impresion(p_id_factura integer);
DROP FUNCTION IF EXISTS public.obtener_lineas_detalle_factura(p_id_factura integer);
DROP FUNCTION IF EXISTS public.obtener_id_cliente_fugaz();
DROP FUNCTION IF EXISTS public.obtener_facturas_recientes_dashboard(p_limite integer);
DROP FUNCTION IF EXISTS public.obtener_factura_para_impresion(p_id_factura integer);
DROP FUNCTION IF EXISTS public.obtener_factura_detalle_por_id(p_id_factura integer);
DROP FUNCTION IF EXISTS public.obtener_detalles_factura_edicion(p_id_factura integer);
DROP FUNCTION IF EXISTS public.obtener_detalles_compra(p_id_compra integer);
DROP FUNCTION IF EXISTS public.obtener_compra_por_id(p_id_compra integer);
DROP FUNCTION IF EXISTS public.obtener_clientes_reporte(p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.obtener_clientes_recientes_dashboard(p_limite integer);
DROP FUNCTION IF EXISTS public.obtener_cliente_por_id(p_id_cliente integer);
DROP FUNCTION IF EXISTS public.obtener_cliente_factura_edicion(p_id_cliente integer);
DROP FUNCTION IF EXISTS public.obtener_cliente_edicion_por_id(p_id_cliente integer);
DROP FUNCTION IF EXISTS public.obtener_categoria_por_id(p_id_categoria integer);
DROP FUNCTION IF EXISTS public.listar_usuarios_para_compras();
DROP FUNCTION IF EXISTS public.listar_usuarios_ordenados();
DROP FUNCTION IF EXISTS public.listar_secciones_ordenadas();
DROP FUNCTION IF EXISTS public.listar_roles_ordenados();
DROP FUNCTION IF EXISTS public.listar_proveedores_producto();
DROP FUNCTION IF EXISTS public.listar_proveedores_para_compras();
DROP FUNCTION IF EXISTS public.listar_proveedores_ordenados();
DROP FUNCTION IF EXISTS public.listar_proveedores_form_producto();
DROP FUNCTION IF EXISTS public.listar_productos_para_factura();
DROP FUNCTION IF EXISTS public.listar_clientes_habituales();
DROP FUNCTION IF EXISTS public.listar_categorias_producto();
DROP FUNCTION IF EXISTS public.listar_categorias_ordenadas();
DROP FUNCTION IF EXISTS public.listar_categorias_form_producto();
DROP FUNCTION IF EXISTS public.listar_categorias_catalogo();
DROP FUNCTION IF EXISTS public.fn_auditar_delete_generico();
DROP FUNCTION IF EXISTS public.eliminar_usuario_sistema(p_id_usuario integer, p_id_usuario_actual integer);
DROP FUNCTION IF EXISTS public.eliminar_proveedor_sistema(p_id_proveedor integer);
DROP FUNCTION IF EXISTS public.eliminar_producto_sistema(p_id_producto integer);
DROP FUNCTION IF EXISTS public.eliminar_factura_sistema(p_id_factura integer);
DROP FUNCTION IF EXISTS public.eliminar_cliente_sistema(p_id_cliente integer);
DROP FUNCTION IF EXISTS public.eliminar_categoria_sistema(p_id_categoria integer);
DROP PROCEDURE IF EXISTS public.editar_factura_sistema(IN p_id_factura integer, IN p_fecha timestamp without time zone, IN p_id_cliente integer, IN p_id_usuario integer, IN p_id_seccion integer, IN p_tipo_cliente_venta character varying, IN p_nombre_cliente_fugaz character varying, IN p_descuento_global numeric, IN p_iva numeric, IN p_items jsonb);
DROP PROCEDURE IF EXISTS public.disminuir_stock_producto(IN p_id_producto integer, IN p_cantidad integer);
DROP FUNCTION IF EXISTS public.crear_usuario_sistema(p_nombre character varying, p_email character varying, p_password text, p_id_rol integer, p_id_seccion integer);
DROP FUNCTION IF EXISTS public.crear_proveedor_sistema(p_nombre character varying, p_telefono character varying, p_email character varying, p_direccion character varying);
DROP FUNCTION IF EXISTS public.calcular_total_compra(p_id_compra integer);
DROP FUNCTION IF EXISTS public.calcular_subtotal_factura(p_id_factura integer);
DROP FUNCTION IF EXISTS public.buscar_usuarios_filtrados(p_busqueda text, p_id_rol integer, p_seccion_filtro text);
DROP FUNCTION IF EXISTS public.buscar_proveedores_filtrados(p_busqueda text);
DROP FUNCTION IF EXISTS public.buscar_productos_inventario(p_busqueda text, p_id_categoria integer, p_id_proveedor integer, p_id_producto integer, p_stock_bajo boolean);
DROP FUNCTION IF EXISTS public.buscar_productos_catalogo(p_busqueda text, p_id_categoria integer, p_disponibilidad character varying);
DROP FUNCTION IF EXISTS public.buscar_facturas_filtradas(p_id_rol integer, p_busqueda text, p_id_seccion integer, p_id_usuario integer, p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.buscar_compras_filtradas(p_busqueda text, p_id_proveedor integer, p_id_usuario integer, p_fecha_desde timestamp without time zone, p_fecha_hasta timestamp without time zone);
DROP FUNCTION IF EXISTS public.buscar_clientes_filtrados(p_busqueda text, p_tipo_cliente character varying);
DROP FUNCTION IF EXISTS public.buscar_categorias(p_busqueda text);
DROP PROCEDURE IF EXISTS public.aumentar_stock_producto(IN p_id_producto integer, IN p_cantidad integer);
DROP PROCEDURE IF EXISTS public.agregar_detalle_factura(IN p_id_factura integer, IN p_id_producto integer, IN p_cantidad integer, IN p_descuento_linea numeric);
DROP PROCEDURE IF EXISTS public.agregar_detalle_compra(IN p_id_compra integer, IN p_id_producto integer, IN p_cantidad integer, IN p_costo_unitario numeric);
DROP FUNCTION IF EXISTS public.actualizar_usuario_sistema(p_id_usuario integer, p_nombre character varying, p_email character varying, p_id_rol integer, p_id_seccion integer, p_password text);
DROP FUNCTION IF EXISTS public.actualizar_usuario_configurar_cuenta(p_id_usuario integer, p_nombre character varying, p_email character varying, p_password text);
DROP PROCEDURE IF EXISTS public.actualizar_totales_factura(IN p_id_factura integer, IN p_descuento numeric, IN p_porcentaje_impuesto numeric);
DROP PROCEDURE IF EXISTS public.actualizar_total_compra(IN p_id_compra integer);
DROP FUNCTION IF EXISTS public.actualizar_proveedor_sistema(p_id_proveedor integer, p_nombre character varying, p_telefono character varying, p_email character varying, p_direccion character varying);
DROP FUNCTION IF EXISTS public.actualizar_producto_edicion(p_id_producto integer, p_codigo character varying, p_nombre character varying, p_descripcion text, p_imagen character varying, p_id_categoria integer, p_id_proveedor integer, p_precio_compra numeric, p_precio_venta numeric, p_stock integer);
DROP FUNCTION IF EXISTS public.actualizar_password_usuario_login(p_id_usuario integer, p_password_hash text);
DROP FUNCTION IF EXISTS public.actualizar_cliente_sistema(p_id_cliente integer, p_nombres character varying, p_apellidos character varying, p_telefono character varying, p_direccion character varying, p_identificacion character varying, p_tipo_cliente character varying);
DROP FUNCTION IF EXISTS public.actualizar_categoria(p_id_categoria integer, p_nombre character varying);
-- *not* dropping schema, since initdb creates it
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

-- *not* creating schema, since initdb creates it


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS '';


--
-- Name: actualizar_categoria(integer, character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: actualizar_cliente_sistema(integer, character varying, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: actualizar_password_usuario_login(integer, text); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: actualizar_producto_edicion(integer, character varying, character varying, text, character varying, integer, integer, numeric, numeric, integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: actualizar_proveedor_sistema(integer, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: actualizar_total_compra(integer); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: actualizar_totales_factura(integer, numeric, numeric); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: actualizar_usuario_configurar_cuenta(integer, character varying, character varying, text); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: actualizar_usuario_sistema(integer, character varying, character varying, integer, integer, text); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: agregar_detalle_compra(integer, integer, integer, numeric); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: agregar_detalle_factura(integer, integer, integer, numeric); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: aumentar_stock_producto(integer, integer); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: buscar_categorias(text); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: buscar_clientes_filtrados(text, character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: buscar_compras_filtradas(text, integer, integer, timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: buscar_facturas_filtradas(integer, text, integer, integer, timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: buscar_productos_catalogo(text, integer, character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: buscar_productos_inventario(text, integer, integer, integer, boolean); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: buscar_proveedores_filtrados(text); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: buscar_usuarios_filtrados(text, integer, text); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: calcular_subtotal_factura(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: calcular_total_compra(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: crear_proveedor_sistema(character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: crear_usuario_sistema(character varying, character varying, text, integer, integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: disminuir_stock_producto(integer, integer); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: editar_factura_sistema(integer, timestamp without time zone, integer, integer, integer, character varying, character varying, numeric, numeric, jsonb); Type: PROCEDURE; Schema: public; Owner: -
--

CREATE PROCEDURE public.editar_factura_sistema(IN p_id_factura integer, IN p_fecha timestamp without time zone, IN p_id_cliente integer, IN p_id_usuario integer, IN p_id_seccion integer, IN p_tipo_cliente_venta character varying, IN p_nombre_cliente_fugaz character varying, IN p_descuento_global numeric, IN p_iva numeric, IN p_items jsonb)
    LANGUAGE plpgsql
    AS $$
DECLARE
    item JSONB;
    v_id_producto INT;
    v_cantidad INT;
    v_descuento_linea NUMERIC;
    v_precio NUMERIC;
    v_stock INT;
    v_subtotal_linea NUMERIC;
    v_total_linea NUMERIC;
BEGIN
    IF NOT EXISTS (SELECT 1 FROM Factura WHERE id_factura = p_id_factura) THEN
        RAISE EXCEPTION 'La factura no existe.';
    END IF;

    FOR item IN
        SELECT jsonb_array_elements(p_items)
    LOOP
        v_id_producto := (item->>'id_producto')::INT;
        v_cantidad := (item->>'cantidad')::INT;

        UPDATE Producto
        SET stock = stock + df.cantidad
        FROM DetalleFactura df
        WHERE df.id_factura = p_id_factura
          AND df.id_producto = Producto.id_producto;
    END LOOP;

    DELETE FROM DetalleFactura
    WHERE id_factura = p_id_factura;

    UPDATE Factura
    SET
        fecha = p_fecha,
        id_cliente = p_id_cliente,
        id_usuario = p_id_usuario,
        id_seccion = p_id_seccion,
        tipo_cliente_venta = p_tipo_cliente_venta,
        nombre_cliente_fugaz = p_nombre_cliente_fugaz,
        descuento = p_descuento_global
    WHERE id_factura = p_id_factura;

    FOR item IN
        SELECT jsonb_array_elements(p_items)
    LOOP
        v_id_producto := (item->>'id_producto')::INT;
        v_cantidad := (item->>'cantidad')::INT;
        v_descuento_linea := COALESCE((item->>'descuento_linea')::NUMERIC, 0);

        SELECT precio_venta, stock
        INTO v_precio, v_stock
        FROM Producto
        WHERE id_producto = v_id_producto
        FOR UPDATE;

        IF v_precio IS NULL THEN
            RAISE EXCEPTION 'Uno de los productos seleccionados no existe.';
        END IF;

        IF v_stock < v_cantidad THEN
            RAISE EXCEPTION 'Stock insuficiente para el producto %.', v_id_producto;
        END IF;

        v_subtotal_linea := v_precio * v_cantidad;

        IF v_descuento_linea < 0 THEN
            RAISE EXCEPTION 'El descuento por línea no puede ser negativo.';
        END IF;

        IF v_descuento_linea > v_subtotal_linea THEN
            RAISE EXCEPTION 'El descuento por línea no puede superar el subtotal.';
        END IF;

        v_total_linea := v_subtotal_linea - v_descuento_linea;

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
            v_id_producto,
            v_cantidad,
            v_precio,
            v_descuento_linea,
            v_total_linea
        );

        UPDATE Producto
        SET stock = stock - v_cantidad
        WHERE id_producto = v_id_producto;
    END LOOP;

    CALL actualizar_totales_factura(p_id_factura, p_descuento_global, p_iva);
END;
$$;


--
-- Name: eliminar_categoria_sistema(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: eliminar_cliente_sistema(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: eliminar_factura_sistema(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: eliminar_producto_sistema(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: eliminar_proveedor_sistema(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: eliminar_usuario_sistema(integer, integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: fn_auditar_delete_generico(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.fn_auditar_delete_generico() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    registro_json JSONB;
    registro_id TEXT;
BEGIN
    registro_json := row_to_json(OLD)::jsonb;

    registro_id := COALESCE(
        registro_json ->> 'id_cliente',
        registro_json ->> 'id_producto',
        registro_json ->> 'id_proveedor',
        registro_json ->> 'id_factura',
        registro_json ->> 'id_compra',
        registro_json ->> 'id_usuario',
        registro_json ->> 'id_categoria'
    );

    INSERT INTO auditoria (
        tabla_afectada,
        accion,
        registro_id,
        datos_anteriores,
        fecha
    )
    VALUES (
        TG_TABLE_NAME,
        'DELETE',
        registro_id,
        registro_json,
        NOW()
    );

    RETURN OLD;
END;
$$;


--
-- Name: listar_categorias_catalogo(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_categorias_form_producto(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_categorias_ordenadas(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_categorias_producto(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_clientes_habituales(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.listar_clientes_habituales() RETURNS TABLE(id_cliente integer, nombre text, telefono text, identificacion text, tipo_cliente text)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT
        c.id_cliente,
        TRIM(c.nombres || ' ' || c.apellidos)::TEXT AS nombre,
        COALESCE(c.telefono, '')::TEXT AS telefono,
        COALESCE(c.identificacion, '')::TEXT AS identificacion,
        c.tipo_cliente::TEXT AS tipo_cliente
    FROM cliente c
    ORDER BY c.nombres ASC, c.apellidos ASC;
END;
$$;


--
-- Name: listar_productos_para_factura(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_proveedores_form_producto(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_proveedores_ordenados(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_proveedores_para_compras(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_proveedores_producto(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_roles_ordenados(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_secciones_ordenadas(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_usuarios_ordenados(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: listar_usuarios_para_compras(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_categoria_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_cliente_edicion_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_cliente_factura_edicion(integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.obtener_cliente_factura_edicion(p_id_cliente integer) RETURNS TABLE(id_cliente integer, nombre text, telefono text, identificacion text, tipo_cliente text)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT
        c.id_cliente,
        TRIM(c.nombres || ' ' || c.apellidos)::TEXT AS nombre,
        COALESCE(c.telefono, '')::TEXT AS telefono,
        COALESCE(c.identificacion, '')::TEXT AS identificacion,
        c.tipo_cliente::TEXT AS tipo_cliente
    FROM cliente c
    WHERE c.id_cliente = p_id_cliente
    LIMIT 1;
END;
$$;


--
-- Name: obtener_cliente_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_clientes_recientes_dashboard(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_clientes_reporte(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_compra_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_detalles_compra(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_detalles_factura_edicion(integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.obtener_detalles_factura_edicion(p_id_factura integer) RETURNS TABLE(id_detalle integer, id_producto integer, codigo character varying, nombre character varying, cantidad integer, precio_unitario numeric, descuento_linea numeric, total_linea numeric)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT
        df.id_detalle,
        df.id_producto,
        p.codigo,
        p.nombre,
        df.cantidad,
        df.precio_unitario,
        df.descuento_linea,
        df.total_linea
    FROM DetalleFactura df
    INNER JOIN Producto p ON p.id_producto = df.id_producto
    WHERE df.id_factura = p_id_factura
    ORDER BY df.id_detalle ASC;
END;
$$;


--
-- Name: obtener_factura_detalle_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_factura_para_impresion(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_facturas_recientes_dashboard(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_id_cliente_fugaz(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_lineas_detalle_factura(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_lineas_factura_para_impresion(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_metricas_dashboard(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_producto_edicion_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_producto_imagen(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_productos_factura_por_ids(integer[]); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_productos_mas_vendidos_dashboard(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_productos_mas_vendidos_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_productos_reporte(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_proveedor_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_resumen_cliente(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_seccion_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_seccion_por_nombre(character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_stock_bajo_reportes(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_total_clientes_reportes(); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_total_facturas_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_total_ventas_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_ultimas_facturas_cliente(integer, integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_ultimos_productos_vendidos_dashboard(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_usuario_configurar_cuenta(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_usuario_edicion_por_id(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_usuario_login(character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_ventas_dashboard(integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_ventas_detalladas_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: obtener_ventas_por_dia_reportes(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: registrar_auditoria(character varying, character varying, character varying, text); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: registrar_categoria(character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: registrar_cliente(character varying, character varying, character varying, character varying, character varying, character varying); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: registrar_cliente_sistema(character varying, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: registrar_factura_sistema(integer, integer, integer, numeric, numeric, numeric, numeric, character varying, character varying, jsonb); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: registrar_producto(character varying, character varying, text, character varying, integer, integer, numeric, numeric, integer); Type: PROCEDURE; Schema: public; Owner: -
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


--
-- Name: registrar_producto_formulario(character varying, character varying, text, character varying, integer, integer, numeric, numeric, integer); Type: FUNCTION; Schema: public; Owner: -
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


--
-- Name: validar_factura_existe(integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.validar_factura_existe(p_id_factura integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_existe BOOLEAN;
BEGIN
    SELECT EXISTS (
        SELECT 1
        FROM Factura
        WHERE id_factura = p_id_factura
    )
    INTO v_existe;

    RETURN v_existe;
END;
$$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: auditoria; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.auditoria (
    id_auditoria integer NOT NULL,
    usuario character varying(100) DEFAULT 'Sistema'::character varying,
    accion character varying(50) NOT NULL,
    tabla_afectada character varying(100) NOT NULL,
    descripcion text,
    fecha_registro timestamp without time zone DEFAULT now() NOT NULL,
    fecha timestamp without time zone DEFAULT now() NOT NULL,
    id_usuario integer
);


--
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.auditoria_id_auditoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.auditoria_id_auditoria_seq OWNED BY public.auditoria.id_auditoria;


--
-- Name: categoria; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categoria (
    id_categoria integer NOT NULL,
    nombre character varying(80) NOT NULL
);


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categoria_id_categoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categoria_id_categoria_seq OWNED BY public.categoria.id_categoria;


--
-- Name: cliente; Type: TABLE; Schema: public; Owner: -
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
    CONSTRAINT ck_cliente_tipo CHECK (((tipo_cliente)::text = ANY (ARRAY[('Mayorista'::character varying)::text, ('Detallista'::character varying)::text])))
);


--
-- Name: cliente_id_cliente_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cliente_id_cliente_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cliente_id_cliente_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cliente_id_cliente_seq OWNED BY public.cliente.id_cliente;


--
-- Name: compra; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.compra (
    id_compra integer NOT NULL,
    fecha timestamp without time zone DEFAULT now() NOT NULL,
    id_proveedor integer NOT NULL,
    id_usuario integer NOT NULL,
    total numeric(10,2) DEFAULT 0 NOT NULL
);


--
-- Name: compra_id_compra_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.compra_id_compra_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: compra_id_compra_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.compra_id_compra_seq OWNED BY public.compra.id_compra;


--
-- Name: detallecompra; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: detallecompra_id_detalle_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.detallecompra_id_detalle_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: detallecompra_id_detalle_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.detallecompra_id_detalle_seq OWNED BY public.detallecompra.id_detalle;


--
-- Name: detallefactura; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: detallefactura_id_detalle_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.detallefactura_id_detalle_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: detallefactura_id_detalle_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.detallefactura_id_detalle_seq OWNED BY public.detallefactura.id_detalle;


--
-- Name: factura; Type: TABLE; Schema: public; Owner: -
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
    CONSTRAINT factura_tipo_cliente_venta_check CHECK (((tipo_cliente_venta)::text = ANY (ARRAY[('Habitual'::character varying)::text, ('Fugaz'::character varying)::text])))
);


--
-- Name: factura_id_factura_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.factura_id_factura_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: factura_id_factura_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.factura_id_factura_seq OWNED BY public.factura.id_factura;


--
-- Name: producto; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: producto_id_producto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.producto_id_producto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: producto_id_producto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.producto_id_producto_seq OWNED BY public.producto.id_producto;


--
-- Name: proveedor; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.proveedor (
    id_proveedor integer NOT NULL,
    nombre character varying(120) NOT NULL,
    telefono character varying(30),
    email character varying(120),
    direccion character varying(200)
);


--
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.proveedor_id_proveedor_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.proveedor_id_proveedor_seq OWNED BY public.proveedor.id_proveedor;


--
-- Name: rol; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rol (
    id_rol integer NOT NULL,
    nombre character varying(30) NOT NULL
);


--
-- Name: rol_id_rol_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rol_id_rol_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rol_id_rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rol_id_rol_seq OWNED BY public.rol.id_rol;


--
-- Name: seccion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.seccion (
    id_seccion integer NOT NULL,
    nombre character varying(30) NOT NULL
);


--
-- Name: seccion_id_seccion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seccion_id_seccion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: seccion_id_seccion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.seccion_id_seccion_seq OWNED BY public.seccion.id_seccion;


--
-- Name: usuario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuario (
    id_usuario integer NOT NULL,
    nombre character varying(100) NOT NULL,
    email character varying(120) NOT NULL,
    password text NOT NULL,
    id_rol integer NOT NULL,
    id_seccion integer
);


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuario_id_usuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuario_id_usuario_seq OWNED BY public.usuario.id_usuario;


--
-- Name: auditoria id_auditoria; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.auditoria ALTER COLUMN id_auditoria SET DEFAULT nextval('public.auditoria_id_auditoria_seq'::regclass);


--
-- Name: categoria id_categoria; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categoria ALTER COLUMN id_categoria SET DEFAULT nextval('public.categoria_id_categoria_seq'::regclass);


--
-- Name: cliente id_cliente; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cliente ALTER COLUMN id_cliente SET DEFAULT nextval('public.cliente_id_cliente_seq'::regclass);


--
-- Name: compra id_compra; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra ALTER COLUMN id_compra SET DEFAULT nextval('public.compra_id_compra_seq'::regclass);


--
-- Name: detallecompra id_detalle; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallecompra ALTER COLUMN id_detalle SET DEFAULT nextval('public.detallecompra_id_detalle_seq'::regclass);


--
-- Name: detallefactura id_detalle; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallefactura ALTER COLUMN id_detalle SET DEFAULT nextval('public.detallefactura_id_detalle_seq'::regclass);


--
-- Name: factura id_factura; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura ALTER COLUMN id_factura SET DEFAULT nextval('public.factura_id_factura_seq'::regclass);


--
-- Name: producto id_producto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto ALTER COLUMN id_producto SET DEFAULT nextval('public.producto_id_producto_seq'::regclass);


--
-- Name: proveedor id_proveedor; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedor ALTER COLUMN id_proveedor SET DEFAULT nextval('public.proveedor_id_proveedor_seq'::regclass);


--
-- Name: rol id_rol; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol ALTER COLUMN id_rol SET DEFAULT nextval('public.rol_id_rol_seq'::regclass);


--
-- Name: seccion id_seccion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.seccion ALTER COLUMN id_seccion SET DEFAULT nextval('public.seccion_id_seccion_seq'::regclass);


--
-- Name: usuario id_usuario; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario ALTER COLUMN id_usuario SET DEFAULT nextval('public.usuario_id_usuario_seq'::regclass);


--
-- Data for Name: auditoria; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.auditoria (id_auditoria, usuario, accion, tabla_afectada, descripcion, fecha_registro, fecha, id_usuario) FROM stdin;
1	Leonel Messi	UPDATE	producto	Registro de auditoría generado para pruebas académicas #1	2026-05-06 09:24:52.990096	2026-02-02 08:00:00	1
2	Daniel Pérez	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #2	2026-05-06 09:24:52.990096	2026-02-03 08:00:00	2
3	Jeremy Pérez	INSERT	cliente	Registro de auditoría generado para pruebas académicas #3	2026-05-06 09:24:52.990096	2026-02-04 08:00:00	3
4	Jhossep Ramos	UPDATE	factura	Registro de auditoría generado para pruebas académicas #4	2026-05-06 09:24:52.990096	2026-02-05 08:00:00	4
5	Diego Torres	CONSULTA	producto	Registro de auditoría generado para pruebas académicas #5	2026-05-06 09:24:52.990096	2026-02-06 08:00:00	5
6	Carlos Núñez	INSERT	compra	Registro de auditoría generado para pruebas académicas #6	2026-05-06 09:24:52.990096	2026-02-07 08:00:00	6
7	Mónica Larios	UPDATE	cliente	Registro de auditoría generado para pruebas académicas #7	2026-05-06 09:24:52.990096	2026-02-08 08:00:00	7
8	Esteban Rodríguez	CONSULTA	factura	Registro de auditoría generado para pruebas académicas #8	2026-05-06 09:24:52.990096	2026-02-09 08:00:00	8
9	Eduardo Molina	INSERT	producto	Registro de auditoría generado para pruebas académicas #9	2026-05-06 09:24:52.990096	2026-02-10 08:00:00	9
10	Andy Sánchez	UPDATE	compra	Registro de auditoría generado para pruebas académicas #10	2026-05-06 09:24:52.990096	2026-02-11 08:00:00	10
11	Sofía Gómez	CONSULTA	cliente	Registro de auditoría generado para pruebas académicas #11	2026-05-06 09:24:52.990096	2026-02-12 08:00:00	11
12	Luis Torres	INSERT	factura	Registro de auditoría generado para pruebas académicas #12	2026-05-06 09:24:52.990096	2026-02-13 08:00:00	12
13	Carla Bermúdez	UPDATE	producto	Registro de auditoría generado para pruebas académicas #13	2026-05-06 09:24:52.990096	2026-02-14 08:00:00	13
14	Karla Medina	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #14	2026-05-06 09:24:52.990096	2026-02-15 08:00:00	14
15	Wilmer Ruiz	INSERT	cliente	Registro de auditoría generado para pruebas académicas #15	2026-05-06 09:24:52.990096	2026-02-16 08:00:00	15
16	Miguel Hernández	UPDATE	factura	Registro de auditoría generado para pruebas académicas #16	2026-05-06 09:24:52.990096	2026-02-17 08:00:00	16
17	Paola López	CONSULTA	producto	Registro de auditoría generado para pruebas académicas #17	2026-05-06 09:24:52.990096	2026-02-18 08:00:00	17
18	Kevin Castillo	INSERT	compra	Registro de auditoría generado para pruebas académicas #18	2026-05-06 09:24:52.990096	2026-02-19 08:00:00	18
19	María Fernández	UPDATE	cliente	Registro de auditoría generado para pruebas académicas #19	2026-05-06 09:24:52.990096	2026-02-20 08:00:00	19
20	Josefina Rivas	CONSULTA	factura	Registro de auditoría generado para pruebas académicas #20	2026-05-06 09:24:52.990096	2026-02-21 08:00:00	20
21	Roberto Gutiérrez	INSERT	producto	Registro de auditoría generado para pruebas académicas #21	2026-05-06 09:24:52.990096	2026-02-22 08:00:00	21
22	Lucía Herrera	UPDATE	compra	Registro de auditoría generado para pruebas académicas #22	2026-05-06 09:24:52.990096	2026-02-23 08:00:00	22
23	Brandon Morales	CONSULTA	cliente	Registro de auditoría generado para pruebas académicas #23	2026-05-06 09:24:52.990096	2026-02-24 08:00:00	23
24	Andrea Vega	INSERT	factura	Registro de auditoría generado para pruebas académicas #24	2026-05-06 09:24:52.990096	2026-02-25 08:00:00	24
25	Sergio Mairena	UPDATE	producto	Registro de auditoría generado para pruebas académicas #25	2026-05-06 09:24:52.990096	2026-02-26 08:00:00	25
26	Julia Campos	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #26	2026-05-06 09:24:52.990096	2026-02-27 08:00:00	26
27	Laura Castillo	INSERT	cliente	Registro de auditoría generado para pruebas académicas #27	2026-05-06 09:24:52.990096	2026-02-28 08:00:00	27
28	Óscar Mejía	UPDATE	factura	Registro de auditoría generado para pruebas académicas #28	2026-05-06 09:24:52.990096	2026-03-01 08:00:00	28
29	Carmen Rojas	CONSULTA	producto	Registro de auditoría generado para pruebas académicas #29	2026-05-06 09:24:52.990096	2026-03-02 08:00:00	29
30	Nidia Solís	INSERT	compra	Registro de auditoría generado para pruebas académicas #30	2026-05-06 09:24:52.990096	2026-03-03 08:00:00	30
31	Leonel Messi	UPDATE	cliente	Registro de auditoría generado para pruebas académicas #31	2026-05-06 09:24:52.990096	2026-03-04 08:00:00	1
32	Daniel Pérez	CONSULTA	factura	Registro de auditoría generado para pruebas académicas #32	2026-05-06 09:24:52.990096	2026-03-05 08:00:00	2
33	Jeremy Pérez	INSERT	producto	Registro de auditoría generado para pruebas académicas #33	2026-05-06 09:24:52.990096	2026-03-06 08:00:00	3
34	Jhossep Ramos	UPDATE	compra	Registro de auditoría generado para pruebas académicas #34	2026-05-06 09:24:52.990096	2026-03-07 08:00:00	4
35	Diego Torres	CONSULTA	cliente	Registro de auditoría generado para pruebas académicas #35	2026-05-06 09:24:52.990096	2026-03-08 08:00:00	5
36	Carlos Núñez	INSERT	factura	Registro de auditoría generado para pruebas académicas #36	2026-05-06 09:24:52.990096	2026-03-09 08:00:00	6
37	Mónica Larios	UPDATE	producto	Registro de auditoría generado para pruebas académicas #37	2026-05-06 09:24:52.990096	2026-03-10 08:00:00	7
38	Esteban Rodríguez	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #38	2026-05-06 09:24:52.990096	2026-03-11 08:00:00	8
39	Eduardo Molina	INSERT	cliente	Registro de auditoría generado para pruebas académicas #39	2026-05-06 09:24:52.990096	2026-03-12 08:00:00	9
40	Andy Sánchez	UPDATE	factura	Registro de auditoría generado para pruebas académicas #40	2026-05-06 09:24:52.990096	2026-03-13 08:00:00	10
\.


--
-- Data for Name: categoria; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categoria (id_categoria, nombre) FROM stdin;
1	Camisetas
2	Hoodies
3	Stickers
4	Tazas
5	Gorras
6	Llaveros
7	Posters
8	Bolsos
9	Mousepads
10	Accesorios personalizados
\.


--
-- Data for Name: cliente; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cliente (id_cliente, nombres, apellidos, telefono, direccion, identificacion, tipo_cliente, fecha_registro) FROM stdin;
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
1	Lucía	Pérez Mendoza	8881-2045	Residencial Las Colinas, Managua	001-120401-1001A	Detallista	2026-01-08
2	Fernando	Aguilar Rivas	8765-1120	Villa Fontana, Managua	001-230799-1002B	Mayorista	2026-01-10
3	María José	González López	8854-7732	Altamira, Managua	001-050302-1003C	Detallista	2026-01-12
4	Carlos Andrés	Mendoza Castillo	8992-4108	Ciudad Jardín, Managua	001-180695-1004D	Mayorista	2026-01-15
5	Sofía	Ramírez Duarte	8720-9981	Linda Vista, Managua	001-280604-1005E	Detallista	2026-01-18
6	José Miguel	Torres Blandón	8677-3409	Reparto San Juan, Managua	001-070598-1006F	Detallista	2026-01-20
7	Ana Gabriela	Martínez Flores	8841-5520	Monseñor Lezcano, Managua	001-141199-1007G	Mayorista	2026-01-22
8	Diego Alejandro	Herrera Navarro	8234-7811	Carretera a Masaya km 10, Managua	001-030701-1008H	Detallista	2026-01-25
9	Valeria	Castillo Morales	8755-3321	Bolonia, Managua	001-250803-1009I	Detallista	2026-01-27
10	Luis Enrique	Morales Chavarría	8999-2876	Los Robles, Managua	001-091096-1010J	Mayorista	2026-01-29
11	Paola	Reyes Gutiérrez	8780-6142	Barrio Altagracia, Managua	001-110500-1011K	Detallista	2026-02-01
12	Kevin José	López Salinas	8655-4470	Mercado Oriental, Managua	001-210497-1012L	Mayorista	2026-02-03
13	Andrea	Vargas Calderón	8812-7634	Sutiaba, León	081-030900-1013M	Detallista	2026-02-05
14	Roberto Carlos	Sánchez Cruz	8773-9201	Centro Histórico, León	081-190694-1014N	Mayorista	2026-02-07
15	Daniela	Obando Espinoza	8699-1055	Reparto San Antonio, Masaya	401-080602-1015P	Detallista	2026-02-09
16	Oscar David	García Rojas	8821-3345	Monimbó, Masaya	401-270395-1016Q	Detallista	2026-02-11
17	Gabriela	Flores Téllez	8744-5098	Calle La Calzada, Granada	201-150899-1017R	Mayorista	2026-02-13
18	Javier Antonio	Navarro Mejía	8660-7789	Reparto Adelita, Granada	201-020296-1018S	Detallista	2026-02-15
19	Camila	Chávez Ruiz	8890-6612	Barrio Guadalupe, Chinandega	301-220704-1019T	Detallista	2026-02-17
20	Miguel Ángel	Bermúdez Pineda	8711-2457	El Viejo, Chinandega	301-100493-1020U	Mayorista	2026-02-19
21	Isabella	Rivas Montenegro	8245-9033	Jinotepe centro, Carazo	501-160105-1021V	Detallista	2026-02-21
22	Harold	Gómez Acevedo	8833-4720	Diriamba, Carazo	501-300891-1022W	Mayorista	2026-02-23
23	Natalia	Campos Urbina	8768-3094	Estelí centro, Estelí	161-090800-1023X	Detallista	2026-02-25
24	Bryan Alexander	Sequeira Alaniz	8622-9187	Condega, Estelí	161-120596-1024Y	Detallista	2026-02-27
25	Fátima	Lacayo Zamora	8901-5366	Matagalpa centro, Matagalpa	441-040999-1025Z	Mayorista	2026-03-01
26	Emilio	Cordero Solís	8750-2208	Sébaco, Matagalpa	441-260394-1026A	Detallista	2026-03-03
27	Alejandra	Bolaños Mairena	8819-7741	Jinotega centro, Jinotega	101-170601-1027B	Detallista	2026-03-05
28	Cristian	Espinoza Valle	8670-3562	San Rafael del Norte, Jinotega	101-011092-1028C	Mayorista	2026-03-07
29	Renata	Salazar Méndez	8791-0084	Juigalpa centro, Chontales	561-240803-1029D	Detallista	2026-03-09
30	Edwin José	Potosme Aráuz	8888-1190	Acoyapa, Chontales	561-130690-1030E	Mayorista	2026-03-11
\.


--
-- Data for Name: compra; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.compra (id_compra, fecha, id_proveedor, id_usuario, total) FROM stdin;
1	2026-01-07 09:30:00	1	1	2377.50
2	2026-01-09 10:30:00	2	2	2977.50
3	2026-01-11 11:30:00	3	3	3645.00
4	2026-01-13 12:30:00	4	4	4380.00
5	2026-01-15 13:30:00	5	5	5182.50
6	2026-01-17 14:30:00	6	6	6052.50
7	2026-01-19 15:30:00	7	7	6990.00
8	2026-01-21 08:30:00	8	8	7995.00
9	2026-01-23 09:30:00	9	9	6757.50
10	2026-01-25 10:30:00	10	10	5362.50
11	2026-01-27 11:30:00	1	11	3810.00
12	2026-01-29 12:30:00	2	12	4680.00
13	2026-01-31 13:30:00	3	13	5617.50
14	2026-02-02 14:30:00	4	14	6622.50
15	2026-02-04 15:30:00	5	15	7695.00
16	2026-02-06 08:30:00	6	16	8835.00
17	2026-02-08 09:30:00	7	17	10042.50
18	2026-02-10 10:30:00	8	18	11317.50
19	2026-02-12 11:30:00	9	19	12660.00
20	2026-02-14 12:30:00	10	20	14070.00
21	2026-02-16 13:30:00	1	21	11617.50
22	2026-02-18 14:30:00	2	22	9007.50
23	2026-02-20 15:30:00	3	23	6240.00
24	2026-02-22 08:30:00	4	24	7515.00
25	2026-02-24 09:30:00	5	25	8857.50
26	2026-02-26 10:30:00	6	26	10267.50
27	2026-02-28 11:30:00	7	27	11745.00
28	2026-03-02 12:30:00	8	28	13290.00
29	2026-03-04 13:30:00	9	29	14902.50
30	2026-03-06 14:30:00	10	30	16582.50
31	2026-03-08 15:30:00	1	1	18330.00
32	2026-03-10 08:30:00	2	2	20145.00
33	2026-03-12 09:30:00	3	3	16477.50
34	2026-03-14 10:30:00	4	4	12652.50
35	2026-03-16 11:30:00	5	5	8670.00
36	2026-03-18 12:30:00	6	6	10350.00
37	2026-03-20 13:30:00	7	7	12097.50
38	2026-03-22 14:30:00	8	8	13912.50
39	2026-03-24 15:30:00	9	9	15795.00
40	2026-03-26 08:30:00	10	10	2895.00
\.


--
-- Data for Name: detallecompra; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.detallecompra (id_detalle, id_compra, id_producto, cantidad, costo_unitario, total_linea) FROM stdin;
1	1	4	7	95.00	665.00
2	2	7	8	106.25	850.00
3	3	10	9	117.50	1057.50
4	4	13	10	128.75	1287.50
5	5	16	11	140.00	1540.00
6	6	19	12	151.25	1815.00
7	7	22	13	162.50	2112.50
8	8	25	14	173.75	2432.50
9	9	28	15	185.00	2775.00
10	10	31	16	196.25	3140.00
11	11	34	5	207.50	1037.50
12	12	37	6	218.75	1312.50
13	13	40	7	230.00	1610.00
14	14	43	8	241.25	1930.00
15	15	46	9	252.50	2272.50
16	16	49	10	263.75	2637.50
17	17	52	11	275.00	3025.00
18	18	55	12	286.25	3435.00
19	19	58	13	297.50	3867.50
20	20	61	14	308.75	4322.50
21	21	64	15	320.00	4800.00
22	22	67	16	331.25	5300.00
23	23	70	5	342.50	1712.50
24	24	73	6	353.75	2122.50
25	25	76	7	365.00	2555.00
26	26	79	8	376.25	3010.00
27	27	82	9	387.50	3487.50
28	28	85	10	398.75	3987.50
29	29	88	11	410.00	4510.00
30	30	91	12	421.25	5055.00
31	31	94	13	432.50	5622.50
32	32	97	14	443.75	6212.50
33	33	100	15	455.00	6825.00
34	34	103	16	466.25	7460.00
35	35	106	5	477.50	2387.50
36	36	109	6	488.75	2932.50
37	37	112	7	500.00	3500.00
38	38	115	8	511.25	4090.00
39	39	118	9	522.50	4702.50
40	40	1	10	83.75	837.50
41	1	5	8	98.75	790.00
42	2	8	9	110.00	990.00
43	3	11	10	121.25	1212.50
44	4	14	11	132.50	1457.50
45	5	17	12	143.75	1725.00
46	6	20	13	155.00	2015.00
47	7	23	14	166.25	2327.50
48	8	26	15	177.50	2662.50
49	9	29	16	188.75	3020.00
50	10	32	5	200.00	1000.00
51	11	35	6	211.25	1267.50
52	12	38	7	222.50	1557.50
53	13	41	8	233.75	1870.00
54	14	44	9	245.00	2205.00
55	15	47	10	256.25	2562.50
56	16	50	11	267.50	2942.50
57	17	53	12	278.75	3345.00
58	18	56	13	290.00	3770.00
59	19	59	14	301.25	4217.50
60	20	62	15	312.50	4687.50
61	21	65	16	323.75	5180.00
62	22	68	5	335.00	1675.00
63	23	71	6	346.25	2077.50
64	24	74	7	357.50	2502.50
65	25	77	8	368.75	2950.00
66	26	80	9	380.00	3420.00
67	27	83	10	391.25	3912.50
68	28	86	11	402.50	4427.50
69	29	89	12	413.75	4965.00
70	30	92	13	425.00	5525.00
71	31	95	14	436.25	6107.50
72	32	98	15	447.50	6712.50
73	33	101	16	458.75	7340.00
74	34	104	5	470.00	2350.00
75	35	107	6	481.25	2887.50
76	36	110	7	492.50	3447.50
77	37	113	8	503.75	4030.00
78	38	116	9	515.00	4635.00
79	39	119	10	526.25	5262.50
80	40	2	11	87.50	962.50
81	1	6	9	102.50	922.50
82	2	9	10	113.75	1137.50
83	3	12	11	125.00	1375.00
84	4	15	12	136.25	1635.00
85	5	18	13	147.50	1917.50
86	6	21	14	158.75	2222.50
87	7	24	15	170.00	2550.00
88	8	27	16	181.25	2900.00
89	9	30	5	192.50	962.50
90	10	33	6	203.75	1222.50
91	11	36	7	215.00	1505.00
92	12	39	8	226.25	1810.00
93	13	42	9	237.50	2137.50
94	14	45	10	248.75	2487.50
95	15	48	11	260.00	2860.00
96	16	51	12	271.25	3255.00
97	17	54	13	282.50	3672.50
98	18	57	14	293.75	4112.50
99	19	60	15	305.00	4575.00
100	20	63	16	316.25	5060.00
101	21	66	5	327.50	1637.50
102	22	69	6	338.75	2032.50
103	23	72	7	350.00	2450.00
104	24	75	8	361.25	2890.00
105	25	78	9	372.50	3352.50
106	26	81	10	383.75	3837.50
107	27	84	11	395.00	4345.00
108	28	87	12	406.25	4875.00
109	29	90	13	417.50	5427.50
110	30	93	14	428.75	6002.50
111	31	96	15	440.00	6600.00
112	32	99	16	451.25	7220.00
113	33	102	5	462.50	2312.50
114	34	105	6	473.75	2842.50
115	35	108	7	485.00	3395.00
116	36	111	8	496.25	3970.00
117	37	114	9	507.50	4567.50
118	38	117	10	518.75	5187.50
119	39	120	11	530.00	5830.00
120	40	3	12	91.25	1095.00
\.


--
-- Data for Name: detallefactura; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.detallefactura (id_detalle, id_factura, id_producto, cantidad, precio_unitario, descuento_linea, total_linea) FROM stdin;
1	1	3	3	145.75	0.00	437.25
2	2	5	4	156.25	0.00	625.00
3	3	7	1	166.75	0.00	166.75
4	4	9	2	177.25	0.00	354.50
5	5	11	3	187.75	0.00	563.25
6	6	13	4	198.25	10.00	783.00
7	7	15	1	208.75	0.00	208.75
8	8	17	2	219.25	0.00	438.50
9	9	19	3	229.75	0.00	689.25
10	10	21	4	240.25	0.00	961.00
11	11	23	1	250.75	0.00	250.75
12	12	25	2	261.25	0.00	522.50
13	13	27	3	271.75	10.00	805.25
14	14	29	4	282.25	0.00	1129.00
15	15	31	1	292.75	0.00	292.75
16	16	33	2	303.25	0.00	606.50
17	17	35	3	313.75	0.00	941.25
18	18	37	4	324.25	0.00	1297.00
19	19	39	1	334.75	0.00	334.75
20	20	41	2	345.25	10.00	680.50
21	21	43	3	355.75	0.00	1067.25
22	22	45	4	366.25	0.00	1465.00
23	23	47	1	376.75	0.00	376.75
24	24	49	2	387.25	0.00	774.50
25	25	51	3	397.75	0.00	1193.25
26	26	53	4	408.25	0.00	1633.00
27	27	55	1	418.75	10.00	408.75
28	28	57	2	429.25	0.00	858.50
29	29	59	3	439.75	0.00	1319.25
30	30	61	4	450.25	0.00	1801.00
31	31	63	1	460.75	0.00	460.75
32	32	65	2	471.25	0.00	942.50
33	33	67	3	481.75	0.00	1445.25
34	34	69	4	492.25	10.00	1959.00
35	35	71	1	502.75	0.00	502.75
36	36	73	2	513.25	0.00	1026.50
37	37	75	3	523.75	0.00	1571.25
38	38	77	4	534.25	0.00	2137.00
39	39	79	1	544.75	0.00	544.75
40	40	81	2	555.25	0.00	1110.50
41	41	83	3	565.75	10.00	1687.25
42	42	85	4	576.25	0.00	2305.00
43	43	87	1	586.75	0.00	586.75
44	44	89	2	597.25	0.00	1194.50
45	45	91	3	607.75	0.00	1823.25
46	46	93	4	618.25	0.00	2473.00
47	47	95	1	628.75	0.00	628.75
48	48	97	2	639.25	10.00	1268.50
49	49	99	3	649.75	0.00	1949.25
50	50	101	4	660.25	0.00	2641.00
51	51	103	1	670.75	0.00	670.75
52	52	105	2	681.25	0.00	1362.50
53	53	107	3	691.75	0.00	2075.25
54	54	109	4	702.25	0.00	2809.00
55	55	111	1	712.75	10.00	702.75
56	56	113	2	723.25	0.00	1446.50
57	57	115	3	733.75	0.00	2201.25
58	58	117	4	744.25	0.00	2977.00
59	59	119	1	754.75	0.00	754.75
60	60	1	2	135.25	0.00	270.50
61	61	3	3	145.75	0.00	437.25
62	62	5	4	156.25	10.00	615.00
63	63	7	1	166.75	0.00	166.75
64	64	9	2	177.25	0.00	354.50
65	65	11	3	187.75	0.00	563.25
66	66	13	4	198.25	0.00	793.00
67	67	15	1	208.75	0.00	208.75
68	68	17	2	219.25	0.00	438.50
69	69	19	3	229.75	10.00	679.25
70	70	21	4	240.25	0.00	961.00
71	71	23	1	250.75	0.00	250.75
72	72	25	2	261.25	0.00	522.50
73	73	27	3	271.75	0.00	815.25
74	74	29	4	282.25	0.00	1129.00
75	75	31	1	292.75	0.00	292.75
76	76	33	2	303.25	10.00	596.50
77	77	35	3	313.75	0.00	941.25
78	78	37	4	324.25	0.00	1297.00
79	79	39	1	334.75	0.00	334.75
80	80	41	2	345.25	0.00	690.50
81	1	4	4	151.00	0.00	604.00
82	2	6	1	161.50	0.00	161.50
83	3	8	2	172.00	0.00	344.00
84	4	10	3	182.50	0.00	547.50
85	5	12	4	193.00	10.00	762.00
86	6	14	1	203.50	0.00	203.50
87	7	16	2	214.00	0.00	428.00
88	8	18	3	224.50	0.00	673.50
89	9	20	4	235.00	0.00	940.00
90	10	22	1	245.50	0.00	245.50
91	11	24	2	256.00	0.00	512.00
92	12	26	3	266.50	10.00	789.50
93	13	28	4	277.00	0.00	1108.00
94	14	30	1	287.50	0.00	287.50
95	15	32	2	298.00	0.00	596.00
96	16	34	3	308.50	0.00	925.50
97	17	36	4	319.00	0.00	1276.00
98	18	38	1	329.50	0.00	329.50
99	19	40	2	340.00	10.00	670.00
100	20	42	3	350.50	0.00	1051.50
101	21	44	4	361.00	0.00	1444.00
102	22	46	1	371.50	0.00	371.50
103	23	48	2	382.00	0.00	764.00
104	24	50	3	392.50	0.00	1177.50
105	25	52	4	403.00	0.00	1612.00
106	26	54	1	413.50	10.00	403.50
107	27	56	2	424.00	0.00	848.00
108	28	58	3	434.50	0.00	1303.50
109	29	60	4	445.00	0.00	1780.00
110	30	62	1	455.50	0.00	455.50
111	31	64	2	466.00	0.00	932.00
112	32	66	3	476.50	0.00	1429.50
113	33	68	4	487.00	10.00	1938.00
114	34	70	1	497.50	0.00	497.50
115	35	72	2	508.00	0.00	1016.00
116	36	74	3	518.50	0.00	1555.50
117	37	76	4	529.00	0.00	2116.00
118	38	78	1	539.50	0.00	539.50
119	39	80	2	550.00	0.00	1100.00
120	40	82	3	560.50	10.00	1671.50
121	41	84	4	571.00	0.00	2284.00
122	42	86	1	581.50	0.00	581.50
123	43	88	2	592.00	0.00	1184.00
124	44	90	3	602.50	0.00	1807.50
125	45	92	4	613.00	0.00	2452.00
126	46	94	1	623.50	0.00	623.50
127	47	96	2	634.00	10.00	1258.00
128	48	98	3	644.50	0.00	1933.50
129	49	100	4	655.00	0.00	2620.00
130	50	102	1	665.50	0.00	665.50
131	51	104	2	676.00	0.00	1352.00
132	52	106	3	686.50	0.00	2059.50
133	53	108	4	697.00	0.00	2788.00
134	54	110	1	707.50	10.00	697.50
135	55	112	2	718.00	0.00	1436.00
136	56	114	3	728.50	0.00	2185.50
137	57	116	4	739.00	0.00	2956.00
138	58	118	1	749.50	0.00	749.50
139	59	120	2	760.00	0.00	1520.00
140	60	2	3	140.50	0.00	421.50
141	61	4	4	151.00	10.00	594.00
142	62	6	1	161.50	0.00	161.50
143	63	8	2	172.00	0.00	344.00
144	64	10	3	182.50	0.00	547.50
145	65	12	4	193.00	0.00	772.00
146	66	14	1	203.50	0.00	203.50
147	67	16	2	214.00	0.00	428.00
148	68	18	3	224.50	10.00	663.50
149	69	20	4	235.00	0.00	940.00
150	70	22	1	245.50	0.00	245.50
151	71	24	2	256.00	0.00	512.00
152	72	26	3	266.50	0.00	799.50
153	73	28	4	277.00	0.00	1108.00
154	74	30	1	287.50	0.00	287.50
155	75	32	2	298.00	10.00	586.00
156	76	34	3	308.50	0.00	925.50
157	77	36	4	319.00	0.00	1276.00
158	78	38	1	329.50	0.00	329.50
159	79	40	2	340.00	0.00	680.00
160	80	42	3	350.50	0.00	1051.50
\.


--
-- Data for Name: factura; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.factura (id_factura, fecha, id_cliente, id_usuario, id_seccion, subtotal, descuento, impuesto, total, tipo_cliente_venta, nombre_cliente_fugaz) FROM stdin;
1	2026-02-02 10:15:00	1	1	1	1041.25	0.00	156.19	1197.44	Habitual	\N
2	2026-02-03 11:15:00	2	2	2	786.50	0.00	117.98	904.48	Habitual	\N
3	2026-02-04 12:15:00	3	3	1	510.75	0.00	76.61	587.36	Habitual	\N
4	2026-02-05 13:15:00	4	4	2	902.00	0.00	135.30	1037.30	Habitual	\N
5	2026-02-06 14:15:00	5	5	1	1335.25	25.00	195.04	1495.29	Habitual	\N
6	2026-02-07 15:15:00	6	6	2	996.50	0.00	147.98	1134.48	Fugaz	José Ramírez
7	2026-02-08 09:15:00	7	7	1	636.75	0.00	95.51	732.26	Habitual	\N
8	2026-02-09 10:15:00	8	8	2	1112.00	0.00	166.80	1278.80	Habitual	\N
9	2026-02-10 11:15:00	9	9	1	1629.25	0.00	244.39	1873.64	Habitual	\N
10	2026-02-11 12:15:00	10	10	2	1206.50	25.00	177.23	1358.73	Habitual	\N
11	2026-02-12 13:15:00	11	11	1	762.75	0.00	114.41	877.16	Habitual	\N
12	2026-02-13 14:15:00	12	12	2	1322.00	0.00	196.80	1508.80	Fugaz	Carlos Mendoza
13	2026-02-14 15:15:00	13	13	1	1923.25	0.00	286.99	2200.24	Habitual	\N
14	2026-02-15 09:15:00	14	14	2	1416.50	0.00	212.48	1628.98	Habitual	\N
15	2026-02-16 10:15:00	15	15	1	888.75	25.00	129.56	993.31	Habitual	\N
16	2026-02-17 11:15:00	16	16	2	1532.00	0.00	229.80	1761.80	Habitual	\N
17	2026-02-18 12:15:00	17	17	1	2217.25	0.00	332.59	2549.84	Habitual	\N
18	2026-02-19 13:15:00	18	18	2	1626.50	0.00	243.98	1870.48	Fugaz	José Ramírez
19	2026-02-20 14:15:00	19	19	1	1014.75	0.00	150.71	1155.46	Habitual	\N
20	2026-02-21 15:15:00	20	20	2	1742.00	25.00	256.05	1963.05	Habitual	\N
21	2026-02-22 09:15:00	21	21	1	2511.25	0.00	376.69	2887.94	Habitual	\N
22	2026-02-23 10:15:00	22	22	2	1836.50	0.00	275.48	2111.98	Habitual	\N
23	2026-02-24 11:15:00	23	23	1	1140.75	0.00	171.11	1311.86	Habitual	\N
24	2026-02-25 12:15:00	24	24	2	1952.00	0.00	292.80	2244.80	Fugaz	Carlos Mendoza
25	2026-02-26 13:15:00	25	25	1	2805.25	25.00	417.04	3197.29	Habitual	\N
26	2026-02-27 14:15:00	26	26	2	2046.50	0.00	305.48	2341.98	Habitual	\N
27	2026-02-28 15:15:00	27	27	1	1266.75	0.00	188.51	1445.26	Habitual	\N
28	2026-03-01 09:15:00	28	28	2	2162.00	0.00	324.30	2486.30	Habitual	\N
29	2026-03-02 10:15:00	29	29	1	3099.25	0.00	464.89	3564.14	Habitual	\N
30	2026-03-03 11:15:00	30	30	2	2256.50	25.00	334.73	2566.23	Fugaz	José Ramírez
31	2026-03-04 12:15:00	31	1	1	1392.75	0.00	208.91	1601.66	Habitual	\N
32	2026-03-05 13:15:00	32	2	2	2372.00	0.00	355.80	2727.80	Habitual	\N
33	2026-03-06 14:15:00	33	3	1	3393.25	0.00	507.49	3890.74	Habitual	\N
34	2026-03-07 15:15:00	34	4	2	2466.50	0.00	368.48	2824.98	Habitual	\N
35	2026-03-08 09:15:00	35	5	1	1518.75	25.00	224.06	1717.81	Habitual	\N
36	2026-03-09 10:15:00	36	6	2	2582.00	0.00	387.30	2969.30	Fugaz	Carlos Mendoza
37	2026-03-10 11:15:00	37	7	1	3687.25	0.00	553.09	4240.34	Habitual	\N
38	2026-03-11 12:15:00	38	8	2	2676.50	0.00	401.48	3077.98	Habitual	\N
39	2026-03-12 13:15:00	39	9	1	1644.75	0.00	246.71	1891.46	Habitual	\N
40	2026-03-13 14:15:00	40	10	2	2792.00	25.00	413.55	3170.55	Habitual	\N
41	2026-03-14 15:15:00	41	11	1	3981.25	0.00	595.69	4566.94	Habitual	\N
42	2026-03-15 09:15:00	42	12	2	2886.50	0.00	432.98	3319.48	Fugaz	José Ramírez
43	2026-03-16 10:15:00	43	13	1	1770.75	0.00	265.61	2036.36	Habitual	\N
44	2026-03-17 11:15:00	44	14	2	3002.00	0.00	450.30	3452.30	Habitual	\N
45	2026-03-18 12:15:00	45	15	1	4275.25	25.00	637.54	4887.79	Habitual	\N
46	2026-03-19 13:15:00	46	16	2	3096.50	0.00	464.48	3560.98	Habitual	\N
47	2026-03-20 14:15:00	47	17	1	1896.75	0.00	283.01	2169.76	Habitual	\N
48	2026-03-21 15:15:00	48	18	2	3212.00	0.00	480.30	3682.30	Fugaz	Carlos Mendoza
49	2026-03-22 09:15:00	49	19	1	4569.25	0.00	685.39	5254.64	Habitual	\N
50	2026-03-23 10:15:00	50	20	2	3306.50	25.00	492.23	3773.73	Habitual	\N
51	2026-03-24 11:15:00	51	21	1	2022.75	0.00	303.41	2326.16	Habitual	\N
52	2026-03-25 12:15:00	52	22	2	3422.00	0.00	513.30	3935.30	Habitual	\N
53	2026-03-26 13:15:00	53	23	1	4863.25	0.00	729.49	5592.74	Habitual	\N
54	2026-03-27 14:15:00	54	24	2	3516.50	0.00	525.98	4032.48	Fugaz	José Ramírez
55	2026-03-28 15:15:00	55	25	1	2148.75	25.00	317.06	2430.81	Habitual	\N
56	2026-03-29 09:15:00	56	26	2	3632.00	0.00	544.80	4176.80	Habitual	\N
57	2026-03-30 10:15:00	57	27	1	5157.25	0.00	773.59	5930.84	Habitual	\N
58	2026-03-31 11:15:00	58	28	2	3726.50	0.00	558.98	4285.48	Habitual	\N
59	2026-04-01 12:15:00	59	29	1	2274.75	0.00	341.21	2615.96	Habitual	\N
60	2026-04-02 13:15:00	60	30	2	692.00	25.00	100.05	767.05	Fugaz	Carlos Mendoza
61	2026-04-03 14:15:00	61	1	1	1041.25	0.00	154.69	1185.94	Habitual	\N
62	2026-04-04 15:15:00	62	2	2	786.50	0.00	116.48	892.98	Habitual	\N
63	2026-04-05 09:15:00	63	3	1	510.75	0.00	76.61	587.36	Habitual	\N
64	2026-04-06 10:15:00	64	4	2	902.00	0.00	135.30	1037.30	Habitual	\N
65	2026-04-07 11:15:00	65	5	1	1335.25	25.00	196.54	1506.79	Habitual	\N
66	2026-04-08 12:15:00	66	6	2	996.50	0.00	149.48	1145.98	Fugaz	José Ramírez
67	2026-04-09 13:15:00	67	7	1	636.75	0.00	95.51	732.26	Habitual	\N
68	2026-04-10 14:15:00	68	8	2	1112.00	0.00	165.30	1267.30	Habitual	\N
69	2026-04-11 15:15:00	69	9	1	1629.25	0.00	242.89	1862.14	Habitual	\N
70	2026-04-12 09:15:00	70	10	2	1206.50	25.00	177.23	1358.73	Habitual	\N
71	2026-04-13 10:15:00	71	11	1	762.75	0.00	114.41	877.16	Habitual	\N
72	2026-04-14 11:15:00	72	12	2	1322.00	0.00	198.30	1520.30	Fugaz	Carlos Mendoza
73	2026-04-15 12:15:00	73	13	1	1923.25	0.00	288.49	2211.74	Habitual	\N
74	2026-04-16 13:15:00	74	14	2	1416.50	0.00	212.48	1628.98	Habitual	\N
75	2026-04-17 14:15:00	75	15	1	888.75	25.00	128.06	981.81	Habitual	\N
76	2026-04-18 15:15:00	76	16	2	1532.00	0.00	228.30	1750.30	Habitual	\N
77	2026-04-19 09:15:00	77	17	1	2217.25	0.00	332.59	2549.84	Habitual	\N
78	2026-04-20 10:15:00	78	18	2	1626.50	0.00	243.98	1870.48	Fugaz	José Ramírez
79	2026-04-21 11:15:00	79	19	1	1014.75	0.00	152.21	1166.96	Habitual	\N
80	2026-04-22 12:15:00	80	20	2	1742.00	25.00	257.55	1974.55	Habitual	\N
\.


--
-- Data for Name: producto; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.producto (id_producto, codigo, nombre, descripcion, imagen, id_categoria, id_proveedor, precio_compra, precio_venta, stock) FROM stdin;
13	P013	Hoodie Oversize Negro #13	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_67817b473371f51443a44144.jpg	3	3	128.75	198.25	33
14	P014	Sticker Holográfico Kitsune #14	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fc4a61b44.25791177.jpg	4	4	132.50	203.50	4
15	P015	Taza Sublimada Panda #15	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_977042d6b3d3bdc28500a0b5.jpg	5	5	136.25	208.75	1
16	P016	Gorra Bordada Negra #16	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692cba3e06cd65.71101317.png	6	6	140.00	214.00	36
17	P017	Llavero Acrílico Anime #17	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fd3dbc463.93497112.png	7	7	143.75	219.25	37
18	P018	Poster Ilustrado A3 #18	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bbf90800dd3fd1f24deb27b4.webp	8	8	147.50	224.50	38
19	P019	Bolso Tote Personalizado #19	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692e2130300f13.79885065.jpg	9	9	151.25	229.75	3
20	P020	Mousepad Gamer Estampado #20	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fdd0d4ab5.98973422.jpg	10	10	155.00	235.00	40
21	P021	Camiseta Oversize Negra #21	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bd9ae845c714f7f64699fb75.jpg	1	1	158.75	240.25	41
22	P022	Camiseta Blanca Personalizada #22	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692eb95bbb2083.00268187.gif	2	2	162.50	245.50	1
23	P023	Hoodie Oversize Negro #23	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fe7efb784.38860716.jpg	3	3	166.25	250.75	43
24	P024	Sticker Holográfico Kitsune #24	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_d67e3785273607d89e61a401.png	4	4	170.00	256.00	4
25	P025	Taza Sublimada Panda #25	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69ab914e50de9c2ab70f17e2.jpg	5	5	173.75	261.25	45
26	P026	Gorra Bordada Negra #26	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f570ce0de300.86936150.jpg	6	6	177.50	266.50	46
27	P027	Llavero Acrílico Anime #27	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_e155b0fa66e83067205236e4.jpg	7	7	181.25	271.75	2
28	P028	Poster Ilustrado A3 #28	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f68b99380.15585436.jpg	8	8	185.00	277.00	48
29	P029	Bolso Tote Personalizado #29	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f57104731e04.45231154.jpg	9	9	188.75	282.25	49
30	P030	Mousepad Gamer Estampado #30	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_ee0704d79c323b08c7071c3e.jpg	10	10	192.50	287.50	50
31	P031	Camiseta Oversize Negra #31	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f74936e35.57858233.jpg	1	1	196.25	292.75	1
32	P032	Camiseta Blanca Personalizada #32	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f57111237e59.08360337.jpg	2	2	200.00	298.00	52
33	P033	Hoodie Oversize Negro #33	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_f5368854e9de4fe5d5ef70c1.jpg	3	3	203.75	303.25	3
34	P034	Sticker Holográfico Kitsune #34	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3358337933ce52ca1d0d187f.jpg	4	4	207.50	308.50	54
35	P035	Taza Sublimada Panda #35	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f7c0e2da0.19573275.jpg	5	5	211.25	313.75	55
36	P036	Gorra Bordada Negra #36	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712157ed25.57458575.jpg	6	6	215.00	319.00	2
37	P037	Llavero Acrílico Anime #37	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3bc17b8d1377a4826a2d8912.jpg	7	7	218.75	324.25	57
38	P038	Poster Ilustrado A3 #38	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f88ee4f13.38325589.jpg	8	8	222.50	329.50	58
39	P039	Bolso Tote Personalizado #39	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712a0bc1f0.76146228.jpg	9	9	226.25	334.75	4
40	P040	Mousepad Gamer Estampado #40	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5864c2b76949a1ffd7fb6bbb.jpg	10	10	230.00	340.00	60
41	P041	Camiseta Oversize Negra #41	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f9aa6b7b4.37645804.jpg	1	1	233.75	345.25	3
42	P042	Camiseta Blanca Personalizada #42	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_77799003da6531f02abb08fb.jpg	2	2	237.50	350.50	62
43	P043	Hoodie Oversize Negro #43	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5b3fca06c807b9fed7369b0e.jpg	3	3	241.25	355.75	63
44	P044	Sticker Holográfico Kitsune #44	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fb5b95a43.81430094.jpg	4	4	245.00	361.00	1
45	P045	Taza Sublimada Panda #45	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_8b600c2980bacbe62c44cbfd.jpg	5	5	248.75	366.25	65
46	P046	Gorra Bordada Negra #46	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_67817b473371f51443a44144.jpg	6	6	252.50	371.50	66
47	P047	Llavero Acrílico Anime #47	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fc4a61b44.25791177.jpg	7	7	256.25	376.75	67
48	P048	Poster Ilustrado A3 #48	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_977042d6b3d3bdc28500a0b5.jpg	8	8	260.00	382.00	2
49	P049	Bolso Tote Personalizado #49	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692cba3e06cd65.71101317.png	9	9	263.75	387.25	69
50	P050	Mousepad Gamer Estampado #50	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fd3dbc463.93497112.png	10	10	267.50	392.50	70
51	P051	Camiseta Oversize Negra #51	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bbf90800dd3fd1f24deb27b4.webp	1	1	271.25	397.75	71
52	P052	Camiseta Blanca Personalizada #52	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692e2130300f13.79885065.jpg	2	2	275.00	403.00	3
53	P053	Hoodie Oversize Negro #53	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fdd0d4ab5.98973422.jpg	3	3	278.75	408.25	73
54	P054	Sticker Holográfico Kitsune #54	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bd9ae845c714f7f64699fb75.jpg	4	4	282.50	413.50	74
55	P055	Taza Sublimada Panda #55	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692eb95bbb2083.00268187.gif	5	5	286.25	418.75	4
56	P056	Gorra Bordada Negra #56	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fe7efb784.38860716.jpg	6	6	290.00	424.00	76
57	P057	Llavero Acrílico Anime #57	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_d67e3785273607d89e61a401.png	7	7	293.75	429.25	1
58	P058	Poster Ilustrado A3 #58	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69ab914e50de9c2ab70f17e2.jpg	8	8	297.50	434.50	78
59	P059	Bolso Tote Personalizado #59	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f570ce0de300.86936150.jpg	9	9	301.25	439.75	79
60	P060	Mousepad Gamer Estampado #60	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_e155b0fa66e83067205236e4.jpg	10	10	305.00	445.00	80
61	P061	Camiseta Oversize Negra #61	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f68b99380.15585436.jpg	1	1	308.75	450.25	81
62	P062	Camiseta Blanca Personalizada #62	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f57104731e04.45231154.jpg	2	2	312.50	455.50	82
63	P063	Hoodie Oversize Negro #63	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_ee0704d79c323b08c7071c3e.jpg	3	3	316.25	460.75	2
64	P064	Sticker Holográfico Kitsune #64	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f74936e35.57858233.jpg	4	4	320.00	466.00	84
65	P065	Taza Sublimada Panda #65	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f57111237e59.08360337.jpg	5	5	323.75	471.25	85
66	P066	Gorra Bordada Negra #66	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_f5368854e9de4fe5d5ef70c1.jpg	6	6	327.50	476.50	3
67	P067	Llavero Acrílico Anime #67	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3358337933ce52ca1d0d187f.jpg	7	7	331.25	481.75	87
68	P068	Poster Ilustrado A3 #68	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f7c0e2da0.19573275.jpg	8	8	335.00	487.00	1
69	P069	Bolso Tote Personalizado #69	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712157ed25.57458575.jpg	9	9	338.75	492.25	89
70	P070	Mousepad Gamer Estampado #70	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3bc17b8d1377a4826a2d8912.jpg	10	10	342.50	497.50	4
71	P071	Camiseta Oversize Negra #71	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f88ee4f13.38325589.jpg	1	1	346.25	502.75	91
72	P072	Camiseta Blanca Personalizada #72	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712a0bc1f0.76146228.jpg	2	2	350.00	508.00	3
73	P073	Hoodie Oversize Negro #73	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5864c2b76949a1ffd7fb6bbb.jpg	3	3	353.75	513.25	93
74	P074	Sticker Holográfico Kitsune #74	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f9aa6b7b4.37645804.jpg	4	4	357.50	518.50	2
75	P075	Taza Sublimada Panda #75	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_77799003da6531f02abb08fb.jpg	5	5	361.25	523.75	95
76	P076	Gorra Bordada Negra #76	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5b3fca06c807b9fed7369b0e.jpg	6	6	365.00	529.00	96
77	P077	Llavero Acrílico Anime #77	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fb5b95a43.81430094.jpg	7	7	368.75	534.25	97
78	P078	Poster Ilustrado A3 #78	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_8b600c2980bacbe62c44cbfd.jpg	8	8	372.50	539.50	98
79	P079	Bolso Tote Personalizado #79	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_67817b473371f51443a44144.jpg	9	9	376.25	544.75	1
80	P080	Mousepad Gamer Estampado #80	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fc4a61b44.25791177.jpg	10	10	380.00	550.00	100
81	P081	Camiseta Oversize Negra #81	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_977042d6b3d3bdc28500a0b5.jpg	1	1	383.75	555.25	101
82	P082	Camiseta Blanca Personalizada #82	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692cba3e06cd65.71101317.png	2	2	387.50	560.50	102
83	P083	Hoodie Oversize Negro #83	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fd3dbc463.93497112.png	3	3	391.25	565.75	103
84	P084	Sticker Holográfico Kitsune #84	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bbf90800dd3fd1f24deb27b4.webp	4	4	395.00	571.00	4
85	P085	Taza Sublimada Panda #85	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692e2130300f13.79885065.jpg	5	5	398.75	576.25	20
86	P086	Gorra Bordada Negra #86	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fdd0d4ab5.98973422.jpg	6	6	402.50	581.50	2
87	P087	Llavero Acrílico Anime #87	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bd9ae845c714f7f64699fb75.jpg	7	7	406.25	586.75	22
88	P088	Poster Ilustrado A3 #88	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692eb95bbb2083.00268187.gif	8	8	410.00	592.00	3
89	P089	Bolso Tote Personalizado #89	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fe7efb784.38860716.jpg	9	9	413.75	597.25	24
90	P090	Mousepad Gamer Estampado #90	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_d67e3785273607d89e61a401.png	10	10	417.50	602.50	25
91	P091	Camiseta Oversize Negra #91	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69ab914e50de9c2ab70f17e2.jpg	1	1	421.25	607.75	1
92	P092	Camiseta Blanca Personalizada #92	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f570ce0de300.86936150.jpg	2	2	425.00	613.00	27
1	P001	Camiseta Oversize Negra #1	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3358337933ce52ca1d0d187f.jpg	1	1	83.75	135.25	21
2	P002	Camiseta Blanca Personalizada #2	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f7c0e2da0.19573275.jpg	2	2	87.50	140.50	22
3	P003	Hoodie Oversize Negro #3	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712157ed25.57458575.jpg	3	3	91.25	145.75	1
4	P004	Sticker Holográfico Kitsune #4	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3bc17b8d1377a4826a2d8912.jpg	4	4	95.00	151.00	24
5	P005	Taza Sublimada Panda #5	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f88ee4f13.38325589.jpg	5	5	98.75	156.25	2
6	P006	Gorra Bordada Negra #6	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712a0bc1f0.76146228.jpg	6	6	102.50	161.50	26
7	P007	Llavero Acrílico Anime #7	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5864c2b76949a1ffd7fb6bbb.jpg	7	7	106.25	166.75	27
8	P008	Poster Ilustrado A3 #8	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f9aa6b7b4.37645804.jpg	8	8	110.00	172.00	1
9	P009	Bolso Tote Personalizado #9	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_77799003da6531f02abb08fb.jpg	9	9	113.75	177.25	3
10	P010	Mousepad Gamer Estampado #10	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5b3fca06c807b9fed7369b0e.jpg	10	10	117.50	182.50	30
11	P011	Camiseta Oversize Negra #11	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fb5b95a43.81430094.jpg	1	1	121.25	187.75	31
12	P012	Camiseta Blanca Personalizada #12	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_8b600c2980bacbe62c44cbfd.jpg	2	2	125.00	193.00	2
93	P093	Hoodie Oversize Negro #93	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_e155b0fa66e83067205236e4.jpg	3	3	428.75	618.25	28
94	P094	Sticker Holográfico Kitsune #94	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f68b99380.15585436.jpg	4	4	432.50	623.50	29
95	P095	Taza Sublimada Panda #95	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f57104731e04.45231154.jpg	5	5	436.25	628.75	30
96	P096	Gorra Bordada Negra #96	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_ee0704d79c323b08c7071c3e.jpg	6	6	440.00	634.00	4
97	P097	Llavero Acrílico Anime #97	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f74936e35.57858233.jpg	7	7	443.75	639.25	32
98	P098	Poster Ilustrado A3 #98	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f57111237e59.08360337.jpg	8	8	447.50	644.50	33
99	P099	Bolso Tote Personalizado #99	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_f5368854e9de4fe5d5ef70c1.jpg	9	9	451.25	649.75	2
100	P100	Mousepad Gamer Estampado #100	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3358337933ce52ca1d0d187f.jpg	10	10	455.00	655.00	35
101	P101	Camiseta Oversize Negra #101	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f7c0e2da0.19573275.jpg	1	1	458.75	660.25	36
102	P102	Camiseta Blanca Personalizada #102	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712157ed25.57458575.jpg	2	2	462.50	665.50	37
103	P103	Hoodie Oversize Negro #103	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_3bc17b8d1377a4826a2d8912.jpg	3	3	466.25	670.75	38
104	P104	Sticker Holográfico Kitsune #104	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f88ee4f13.38325589.jpg	4	4	470.00	676.00	3
105	P105	Taza Sublimada Panda #105	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f5712a0bc1f0.76146228.jpg	5	5	473.75	681.25	40
106	P106	Gorra Bordada Negra #106	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5864c2b76949a1ffd7fb6bbb.jpg	6	6	477.50	686.50	41
107	P107	Llavero Acrílico Anime #107	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56f9aa6b7b4.37645804.jpg	7	7	481.25	691.75	42
108	P108	Poster Ilustrado A3 #108	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_77799003da6531f02abb08fb.jpg	8	8	485.00	697.00	4
109	P109	Bolso Tote Personalizado #109	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_5b3fca06c807b9fed7369b0e.jpg	9	9	488.75	702.25	44
110	P110	Mousepad Gamer Estampado #110	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fb5b95a43.81430094.jpg	10	10	492.50	707.50	2
111	P111	Camiseta Oversize Negra #111	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_8b600c2980bacbe62c44cbfd.jpg	1	1	496.25	712.75	46
112	P112	Camiseta Blanca Personalizada #112	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_67817b473371f51443a44144.jpg	2	2	500.00	718.00	47
113	P113	Hoodie Oversize Negro #113	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fc4a61b44.25791177.jpg	3	3	503.75	723.25	48
114	P114	Sticker Holográfico Kitsune #114	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_977042d6b3d3bdc28500a0b5.jpg	4	4	507.50	728.50	49
115	P115	Taza Sublimada Panda #115	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692cba3e06cd65.71101317.png	5	5	511.25	733.75	4
116	P116	Gorra Bordada Negra #116	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fd3dbc463.93497112.png	6	6	515.00	739.00	51
117	P117	Llavero Acrílico Anime #117	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bbf90800dd3fd1f24deb27b4.webp	7	7	518.75	744.25	52
118	P118	Poster Ilustrado A3 #118	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_692e2130300f13.79885065.jpg	8	8	522.50	749.50	3
119	P119	Bolso Tote Personalizado #119	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_69f56fdd0d4ab5.98973422.jpg	9	9	526.25	754.75	54
120	P120	Mousepad Gamer Estampado #120	Producto generado para inventario académico de Panda Estampados y Kitsune.	prod_bd9ae845c714f7f64699fb75.jpg	10	10	530.00	760.00	4
\.


--
-- Data for Name: proveedor; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.proveedor (id_proveedor, nombre, telefono, email, direccion) FROM stdin;
1	Textiles Managua S.A.	2255-1101	ventas@textilesmanagua.com	Carretera Norte, Managua
2	Serigrafía Central	2266-2304	contacto@serigrafiacentral.com	Altamira, Managua
3	Distribuidora Kitsune	2277-4512	proveedores@kitsunedist.com	Linda Vista, Managua
4	Panda Print Supplies	2288-7821	ventas@pandaprintsupplies.com	Ciudad Jardín, Managua
5	Importadora El Sol	2299-3344	info@importadoraelsol.com	Mercado Oriental, Managua
6	Creativa Nicaragua	2233-9021	creativa@ni.com	Los Robles, Managua
7	Sublimados León	2311-4400	ventas@sublimadosleon.com	Centro, León
8	Artes Gráficas Granada	2552-8820	contacto@artesgranada.com	Calle La Calzada, Granada
9	Materiales Omega	2244-9910	omega@materiales.com	Bolonia, Managua
10	Print House Nicaragua	2270-6543	ventas@printhouseni.com	Villa Fontana, Managua
\.


--
-- Data for Name: rol; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.rol (id_rol, nombre) FROM stdin;
1	Administrador
2	Supervisor
3	Facturador
\.


--
-- Data for Name: seccion; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.seccion (id_seccion, nombre) FROM stdin;
1	Panda Estampados
2	Kitsune
\.


--
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: -
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
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.auditoria_id_auditoria_seq', 40, true);


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.categoria_id_categoria_seq', 10, true);


--
-- Name: cliente_id_cliente_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cliente_id_cliente_seq', 357, true);


--
-- Name: compra_id_compra_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.compra_id_compra_seq', 40, true);


--
-- Name: detallecompra_id_detalle_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.detallecompra_id_detalle_seq', 120, true);


--
-- Name: detallefactura_id_detalle_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.detallefactura_id_detalle_seq', 160, true);


--
-- Name: factura_id_factura_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.factura_id_factura_seq', 80, true);


--
-- Name: producto_id_producto_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.producto_id_producto_seq', 120, true);


--
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.proveedor_id_proveedor_seq', 10, true);


--
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.rol_id_rol_seq', 9, true);


--
-- Name: seccion_id_seccion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.seccion_id_seccion_seq', 8, true);


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuario_id_usuario_seq', 36, true);


--
-- Name: auditoria auditoria_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.auditoria
    ADD CONSTRAINT auditoria_pkey PRIMARY KEY (id_auditoria);


--
-- Name: categoria categoria_nombre_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_nombre_key UNIQUE (nombre);


--
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (id_categoria);


--
-- Name: cliente cliente_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (id_cliente);


--
-- Name: compra compra_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT compra_pkey PRIMARY KEY (id_compra);


--
-- Name: detallecompra detallecompra_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallecompra
    ADD CONSTRAINT detallecompra_pkey PRIMARY KEY (id_detalle);


--
-- Name: detallefactura detallefactura_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallefactura
    ADD CONSTRAINT detallefactura_pkey PRIMARY KEY (id_detalle);


--
-- Name: factura factura_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT factura_pkey PRIMARY KEY (id_factura);


--
-- Name: producto producto_codigo_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_codigo_key UNIQUE (codigo);


--
-- Name: producto producto_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id_producto);


--
-- Name: proveedor proveedor_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedor
    ADD CONSTRAINT proveedor_pkey PRIMARY KEY (id_proveedor);


--
-- Name: rol rol_nombre_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol
    ADD CONSTRAINT rol_nombre_key UNIQUE (nombre);


--
-- Name: rol rol_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol
    ADD CONSTRAINT rol_pkey PRIMARY KEY (id_rol);


--
-- Name: seccion seccion_nombre_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.seccion
    ADD CONSTRAINT seccion_nombre_key UNIQUE (nombre);


--
-- Name: seccion seccion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.seccion
    ADD CONSTRAINT seccion_pkey PRIMARY KEY (id_seccion);


--
-- Name: usuario usuario_email_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_email_key UNIQUE (email);


--
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usuario);


--
-- Name: categoria trg_auditar_delete_categoria; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_auditar_delete_categoria AFTER DELETE ON public.categoria FOR EACH ROW EXECUTE FUNCTION public.fn_auditar_delete_generico();


--
-- Name: cliente trg_auditar_delete_cliente; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_auditar_delete_cliente AFTER DELETE ON public.cliente FOR EACH ROW EXECUTE FUNCTION public.fn_auditar_delete_generico();


--
-- Name: producto trg_auditar_delete_producto; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_auditar_delete_producto AFTER DELETE ON public.producto FOR EACH ROW EXECUTE FUNCTION public.fn_auditar_delete_generico();


--
-- Name: proveedor trg_auditar_delete_proveedor; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_auditar_delete_proveedor AFTER DELETE ON public.proveedor FOR EACH ROW EXECUTE FUNCTION public.fn_auditar_delete_generico();


--
-- Name: auditoria fk_auditoria_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.auditoria
    ADD CONSTRAINT fk_auditoria_usuario FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: compra fk_compra_proveedor; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT fk_compra_proveedor FOREIGN KEY (id_proveedor) REFERENCES public.proveedor(id_proveedor);


--
-- Name: compra fk_compra_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT fk_compra_usuario FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: detallecompra fk_detcom_compra; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallecompra
    ADD CONSTRAINT fk_detcom_compra FOREIGN KEY (id_compra) REFERENCES public.compra(id_compra);


--
-- Name: detallecompra fk_detcom_producto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallecompra
    ADD CONSTRAINT fk_detcom_producto FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: detallefactura fk_detfac_factura; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallefactura
    ADD CONSTRAINT fk_detfac_factura FOREIGN KEY (id_factura) REFERENCES public.factura(id_factura);


--
-- Name: detallefactura fk_detfac_producto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detallefactura
    ADD CONSTRAINT fk_detfac_producto FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: factura fk_factura_cliente; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT fk_factura_cliente FOREIGN KEY (id_cliente) REFERENCES public.cliente(id_cliente);


--
-- Name: factura fk_factura_seccion; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT fk_factura_seccion FOREIGN KEY (id_seccion) REFERENCES public.seccion(id_seccion);


--
-- Name: factura fk_factura_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura
    ADD CONSTRAINT fk_factura_usuario FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: producto fk_producto_categoria; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria) REFERENCES public.categoria(id_categoria);


--
-- Name: producto fk_producto_proveedor; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT fk_producto_proveedor FOREIGN KEY (id_proveedor) REFERENCES public.proveedor(id_proveedor);


--
-- Name: usuario fk_usuario_rol; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol);


--
-- Name: usuario fk_usuario_seccion; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT fk_usuario_seccion FOREIGN KEY (id_seccion) REFERENCES public.seccion(id_seccion);


--
-- PostgreSQL database dump complete
--

\unrestrict y787Pzyhd6oy6rDEjal4xbAgXzG2p5Hl4D1QUvYHFfS53ea1ET66FcmCDcFJb15

