--
-- PostgreSQL database dump
--

\restrict Sri9v3gsI5fHli5XqYDmv8FUYf4tdYBuUK0FCyTpXs9Y1d2KlWQeaA0uofDIuyx

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
ALTER TABLE IF EXISTS ONLY public.factura_estado_historial DROP CONSTRAINT IF EXISTS factura_estado_historial_id_factura_fkey;
DROP TRIGGER IF EXISTS trg_factura_estado_historial ON public.factura;
DROP TRIGGER IF EXISTS trg_auditar_delete_proveedor ON public.proveedor;
DROP TRIGGER IF EXISTS trg_auditar_delete_producto ON public.producto;
DROP TRIGGER IF EXISTS trg_auditar_delete_cliente ON public.cliente;
DROP TRIGGER IF EXISTS trg_auditar_delete_categoria ON public.categoria;
DROP INDEX IF EXISTS public.idx_factura_estado_historial_factura;
DROP INDEX IF EXISTS public.idx_auditoria_tabla_afectada;
DROP INDEX IF EXISTS public.idx_auditoria_registro_id;
DROP INDEX IF EXISTS public.idx_auditoria_datos_anteriores_gin;
DROP INDEX IF EXISTS public.idx_auditoria_accion_fecha;
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
ALTER TABLE IF EXISTS ONLY public.factura_estado_historial DROP CONSTRAINT IF EXISTS factura_estado_historial_pkey;
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
ALTER TABLE IF EXISTS public.factura_estado_historial ALTER COLUMN id_historial DROP DEFAULT;
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
DROP SEQUENCE IF EXISTS public.factura_estado_historial_id_historial_seq;
DROP TABLE IF EXISTS public.factura_estado_historial;
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
DROP FUNCTION IF EXISTS public.registrar_historial_estado_factura();
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
-- Name: registrar_historial_estado_factura(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.registrar_historial_estado_factura() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_tipo_evento VARCHAR(80);
    v_monto_abonado NUMERIC(12, 2);
    v_comentario TEXT;
BEGIN
    IF TG_OP = 'INSERT' THEN
        INSERT INTO factura_estado_historial (
            id_factura,
            tipo_evento,
            estado_pago_nuevo,
            estado_produccion_nuevo,
            monto_pagado_nuevo,
            saldo_nuevo,
            fecha_entrega_estimada_nueva,
            comentario
        ) VALUES (
            NEW.id_factura,
            'Factura creada',
            NEW.estado_pago,
            NEW.estado_produccion,
            NEW.monto_pagado,
            NEW.saldo_pendiente,
            NEW.fecha_entrega_estimada,
            'La factura fue registrada en el sistema.'
        );

        RETURN NEW;
    END IF;

    IF TG_OP = 'UPDATE' THEN
        v_tipo_evento := 'Factura actualizada';
        v_monto_abonado := GREATEST(COALESCE(NEW.monto_pagado, 0) - COALESCE(OLD.monto_pagado, 0), 0);

        IF COALESCE(NEW.monto_pagado, 0) <> COALESCE(OLD.monto_pagado, 0) THEN
            v_tipo_evento := 'Pago actualizado';
        END IF;

        IF COALESCE(NEW.estado_produccion, '') <> COALESCE(OLD.estado_produccion, '') THEN
            v_tipo_evento := 'Estado de producción actualizado';
        END IF;

        IF NEW.estado_produccion = 'Cancelada' AND OLD.estado_produccion IS DISTINCT FROM NEW.estado_produccion THEN
            v_tipo_evento := 'Factura cancelada';

            IF NEW.fecha_entrega_estimada IS NOT NULL AND CURRENT_DATE <= NEW.fecha_entrega_estimada THEN
                v_comentario := 'El cliente canceló antes o en la fecha estimada de entrega.';
            ELSE
                v_comentario := 'El cliente canceló después de la fecha estimada de entrega.';
            END IF;
        ELSE
            v_comentario := 'Cambio registrado automáticamente por el sistema.';
        END IF;

        IF
            COALESCE(NEW.estado_pago, '') <> COALESCE(OLD.estado_pago, '')
            OR COALESCE(NEW.estado_produccion, '') <> COALESCE(OLD.estado_produccion, '')
            OR COALESCE(NEW.monto_pagado, 0) <> COALESCE(OLD.monto_pagado, 0)
            OR COALESCE(NEW.saldo_pendiente, 0) <> COALESCE(OLD.saldo_pendiente, 0)
            OR NEW.fecha_entrega_estimada IS DISTINCT FROM OLD.fecha_entrega_estimada
        THEN
            INSERT INTO factura_estado_historial (
                id_factura,
                tipo_evento,
                estado_pago_anterior,
                estado_pago_nuevo,
                estado_produccion_anterior,
                estado_produccion_nuevo,
                monto_pagado_anterior,
                monto_pagado_nuevo,
                monto_abonado,
                saldo_anterior,
                saldo_nuevo,
                fecha_entrega_estimada_anterior,
                fecha_entrega_estimada_nueva,
                comentario
            ) VALUES (
                NEW.id_factura,
                v_tipo_evento,
                OLD.estado_pago,
                NEW.estado_pago,
                OLD.estado_produccion,
                NEW.estado_produccion,
                OLD.monto_pagado,
                NEW.monto_pagado,
                v_monto_abonado,
                OLD.saldo_pendiente,
                NEW.saldo_pendiente,
                OLD.fecha_entrega_estimada,
                NEW.fecha_entrega_estimada,
                v_comentario
            );
        END IF;

        RETURN NEW;
    END IF;

    RETURN NEW;
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
    id_usuario integer,
    registro_id text,
    datos_anteriores jsonb
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
    monto_pagado numeric(10,2) DEFAULT 0 NOT NULL,
    saldo_pendiente numeric(10,2) DEFAULT 0 NOT NULL,
    porcentaje_pagado numeric(5,2) DEFAULT 0 NOT NULL,
    estado_pago character varying(30) DEFAULT 'Pendiente'::character varying NOT NULL,
    estado_produccion character varying(30) DEFAULT 'Pendiente'::character varying NOT NULL,
    fecha_orden_produccion timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega_estimada date,
    fecha_entrega_real date,
    CONSTRAINT chk_factura_estado_pago_valido CHECK (((estado_pago)::text = ANY ((ARRAY['Pendiente'::character varying, 'Parcial'::character varying, 'Pagado'::character varying])::text[]))),
    CONSTRAINT chk_factura_estado_produccion_valido CHECK (((estado_produccion)::text = ANY ((ARRAY['Pendiente'::character varying, 'En producción'::character varying, 'Lista para entregar'::character varying, 'Entregada'::character varying, 'Cancelada'::character varying])::text[]))),
    CONSTRAINT chk_factura_monto_pagado_no_negativo CHECK ((monto_pagado >= (0)::numeric)),
    CONSTRAINT chk_factura_porcentaje_pagado_rango CHECK (((porcentaje_pagado >= (0)::numeric) AND (porcentaje_pagado <= (100)::numeric))),
    CONSTRAINT chk_factura_saldo_pendiente_no_negativo CHECK ((saldo_pendiente >= (0)::numeric)),
    CONSTRAINT factura_tipo_cliente_venta_check CHECK (((tipo_cliente_venta)::text = ANY (ARRAY[('Habitual'::character varying)::text, ('Fugaz'::character varying)::text])))
);


--
-- Name: factura_estado_historial; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.factura_estado_historial (
    id_historial integer NOT NULL,
    id_factura integer NOT NULL,
    tipo_evento character varying(80) NOT NULL,
    estado_pago_anterior character varying(50),
    estado_pago_nuevo character varying(50),
    estado_produccion_anterior character varying(80),
    estado_produccion_nuevo character varying(80),
    monto_pagado_anterior numeric(12,2),
    monto_pagado_nuevo numeric(12,2),
    monto_abonado numeric(12,2) DEFAULT 0,
    saldo_anterior numeric(12,2),
    saldo_nuevo numeric(12,2),
    fecha_entrega_estimada_anterior date,
    fecha_entrega_estimada_nueva date,
    comentario text,
    fecha_evento timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: factura_estado_historial_id_historial_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.factura_estado_historial_id_historial_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: factura_estado_historial_id_historial_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.factura_estado_historial_id_historial_seq OWNED BY public.factura_estado_historial.id_historial;


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
-- Name: factura_estado_historial id_historial; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura_estado_historial ALTER COLUMN id_historial SET DEFAULT nextval('public.factura_estado_historial_id_historial_seq'::regclass);


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

COPY public.auditoria (id_auditoria, usuario, accion, tabla_afectada, descripcion, fecha_registro, fecha, id_usuario, registro_id, datos_anteriores) FROM stdin;
1	Leonel Messi	UPDATE	producto	Registro de auditoría generado para pruebas académicas #1	2026-05-06 09:24:52.990096	2026-02-02 08:00:00	1	\N	\N
2	Daniel Pérez	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #2	2026-05-06 09:24:52.990096	2026-02-03 08:00:00	2	\N	\N
3	Jeremy Pérez	INSERT	cliente	Registro de auditoría generado para pruebas académicas #3	2026-05-06 09:24:52.990096	2026-02-04 08:00:00	3	\N	\N
4	Jhossep Ramos	UPDATE	factura	Registro de auditoría generado para pruebas académicas #4	2026-05-06 09:24:52.990096	2026-02-05 08:00:00	4	\N	\N
5	Diego Torres	CONSULTA	producto	Registro de auditoría generado para pruebas académicas #5	2026-05-06 09:24:52.990096	2026-02-06 08:00:00	5	\N	\N
6	Carlos Núñez	INSERT	compra	Registro de auditoría generado para pruebas académicas #6	2026-05-06 09:24:52.990096	2026-02-07 08:00:00	6	\N	\N
7	Mónica Larios	UPDATE	cliente	Registro de auditoría generado para pruebas académicas #7	2026-05-06 09:24:52.990096	2026-02-08 08:00:00	7	\N	\N
8	Esteban Rodríguez	CONSULTA	factura	Registro de auditoría generado para pruebas académicas #8	2026-05-06 09:24:52.990096	2026-02-09 08:00:00	8	\N	\N
9	Eduardo Molina	INSERT	producto	Registro de auditoría generado para pruebas académicas #9	2026-05-06 09:24:52.990096	2026-02-10 08:00:00	9	\N	\N
10	Andy Sánchez	UPDATE	compra	Registro de auditoría generado para pruebas académicas #10	2026-05-06 09:24:52.990096	2026-02-11 08:00:00	10	\N	\N
11	Sofía Gómez	CONSULTA	cliente	Registro de auditoría generado para pruebas académicas #11	2026-05-06 09:24:52.990096	2026-02-12 08:00:00	11	\N	\N
12	Luis Torres	INSERT	factura	Registro de auditoría generado para pruebas académicas #12	2026-05-06 09:24:52.990096	2026-02-13 08:00:00	12	\N	\N
13	Carla Bermúdez	UPDATE	producto	Registro de auditoría generado para pruebas académicas #13	2026-05-06 09:24:52.990096	2026-02-14 08:00:00	13	\N	\N
14	Karla Medina	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #14	2026-05-06 09:24:52.990096	2026-02-15 08:00:00	14	\N	\N
15	Wilmer Ruiz	INSERT	cliente	Registro de auditoría generado para pruebas académicas #15	2026-05-06 09:24:52.990096	2026-02-16 08:00:00	15	\N	\N
16	Miguel Hernández	UPDATE	factura	Registro de auditoría generado para pruebas académicas #16	2026-05-06 09:24:52.990096	2026-02-17 08:00:00	16	\N	\N
17	Paola López	CONSULTA	producto	Registro de auditoría generado para pruebas académicas #17	2026-05-06 09:24:52.990096	2026-02-18 08:00:00	17	\N	\N
18	Kevin Castillo	INSERT	compra	Registro de auditoría generado para pruebas académicas #18	2026-05-06 09:24:52.990096	2026-02-19 08:00:00	18	\N	\N
19	María Fernández	UPDATE	cliente	Registro de auditoría generado para pruebas académicas #19	2026-05-06 09:24:52.990096	2026-02-20 08:00:00	19	\N	\N
20	Josefina Rivas	CONSULTA	factura	Registro de auditoría generado para pruebas académicas #20	2026-05-06 09:24:52.990096	2026-02-21 08:00:00	20	\N	\N
21	Roberto Gutiérrez	INSERT	producto	Registro de auditoría generado para pruebas académicas #21	2026-05-06 09:24:52.990096	2026-02-22 08:00:00	21	\N	\N
22	Lucía Herrera	UPDATE	compra	Registro de auditoría generado para pruebas académicas #22	2026-05-06 09:24:52.990096	2026-02-23 08:00:00	22	\N	\N
23	Brandon Morales	CONSULTA	cliente	Registro de auditoría generado para pruebas académicas #23	2026-05-06 09:24:52.990096	2026-02-24 08:00:00	23	\N	\N
24	Andrea Vega	INSERT	factura	Registro de auditoría generado para pruebas académicas #24	2026-05-06 09:24:52.990096	2026-02-25 08:00:00	24	\N	\N
25	Sergio Mairena	UPDATE	producto	Registro de auditoría generado para pruebas académicas #25	2026-05-06 09:24:52.990096	2026-02-26 08:00:00	25	\N	\N
26	Julia Campos	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #26	2026-05-06 09:24:52.990096	2026-02-27 08:00:00	26	\N	\N
27	Laura Castillo	INSERT	cliente	Registro de auditoría generado para pruebas académicas #27	2026-05-06 09:24:52.990096	2026-02-28 08:00:00	27	\N	\N
28	Óscar Mejía	UPDATE	factura	Registro de auditoría generado para pruebas académicas #28	2026-05-06 09:24:52.990096	2026-03-01 08:00:00	28	\N	\N
29	Carmen Rojas	CONSULTA	producto	Registro de auditoría generado para pruebas académicas #29	2026-05-06 09:24:52.990096	2026-03-02 08:00:00	29	\N	\N
30	Nidia Solís	INSERT	compra	Registro de auditoría generado para pruebas académicas #30	2026-05-06 09:24:52.990096	2026-03-03 08:00:00	30	\N	\N
31	Leonel Messi	UPDATE	cliente	Registro de auditoría generado para pruebas académicas #31	2026-05-06 09:24:52.990096	2026-03-04 08:00:00	1	\N	\N
32	Daniel Pérez	CONSULTA	factura	Registro de auditoría generado para pruebas académicas #32	2026-05-06 09:24:52.990096	2026-03-05 08:00:00	2	\N	\N
33	Jeremy Pérez	INSERT	producto	Registro de auditoría generado para pruebas académicas #33	2026-05-06 09:24:52.990096	2026-03-06 08:00:00	3	\N	\N
34	Jhossep Ramos	UPDATE	compra	Registro de auditoría generado para pruebas académicas #34	2026-05-06 09:24:52.990096	2026-03-07 08:00:00	4	\N	\N
35	Diego Torres	CONSULTA	cliente	Registro de auditoría generado para pruebas académicas #35	2026-05-06 09:24:52.990096	2026-03-08 08:00:00	5	\N	\N
36	Carlos Núñez	INSERT	factura	Registro de auditoría generado para pruebas académicas #36	2026-05-06 09:24:52.990096	2026-03-09 08:00:00	6	\N	\N
37	Mónica Larios	UPDATE	producto	Registro de auditoría generado para pruebas académicas #37	2026-05-06 09:24:52.990096	2026-03-10 08:00:00	7	\N	\N
38	Esteban Rodríguez	CONSULTA	compra	Registro de auditoría generado para pruebas académicas #38	2026-05-06 09:24:52.990096	2026-03-11 08:00:00	8	\N	\N
39	Eduardo Molina	INSERT	cliente	Registro de auditoría generado para pruebas académicas #39	2026-05-06 09:24:52.990096	2026-03-12 08:00:00	9	\N	\N
40	Andy Sánchez	UPDATE	factura	Registro de auditoría generado para pruebas académicas #40	2026-05-06 09:24:52.990096	2026-03-13 08:00:00	10	\N	\N
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
3	3	7	1	166.75	0.00	166.75
4	4	9	2	177.25	0.00	354.50
5	5	11	3	187.75	0.00	563.25
7	7	15	1	208.75	0.00	208.75
8	8	17	2	219.25	0.00	438.50
9	9	19	3	229.75	0.00	689.25
11	11	23	1	250.75	0.00	250.75
12	12	25	2	261.25	0.00	522.50
13	13	27	3	271.75	10.00	805.25
15	15	31	1	292.75	0.00	292.75
16	16	33	2	303.25	0.00	606.50
17	17	35	3	313.75	0.00	941.25
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
63	63	7	1	166.75	0.00	166.75
64	64	9	2	177.25	0.00	354.50
65	65	11	3	187.75	0.00	563.25
67	67	15	1	208.75	0.00	208.75
68	68	17	2	219.25	0.00	438.50
69	69	19	3	229.75	10.00	679.25
71	71	23	1	250.75	0.00	250.75
72	72	25	2	261.25	0.00	522.50
73	73	27	3	271.75	0.00	815.25
75	75	31	1	292.75	0.00	292.75
76	76	33	2	303.25	10.00	596.50
77	77	35	3	313.75	0.00	941.25
79	79	39	1	334.75	0.00	334.75
80	80	41	2	345.25	0.00	690.50
82	2	6	1	161.50	0.00	161.50
83	3	8	2	172.00	0.00	344.00
84	4	10	3	182.50	0.00	547.50
86	6	14	1	203.50	0.00	203.50
87	7	16	2	214.00	0.00	428.00
88	8	18	3	224.50	0.00	673.50
90	10	22	1	245.50	0.00	245.50
91	11	24	2	256.00	0.00	512.00
92	12	26	3	266.50	10.00	789.50
94	14	30	1	287.50	0.00	287.50
95	15	32	2	298.00	0.00	596.00
96	16	34	3	308.50	0.00	925.50
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
142	62	6	1	161.50	0.00	161.50
143	63	8	2	172.00	0.00	344.00
144	64	10	3	182.50	0.00	547.50
146	66	14	1	203.50	0.00	203.50
147	67	16	2	214.00	0.00	428.00
148	68	18	3	224.50	10.00	663.50
150	70	22	1	245.50	0.00	245.50
151	71	24	2	256.00	0.00	512.00
152	72	26	3	266.50	0.00	799.50
154	74	30	1	287.50	0.00	287.50
155	75	32	2	298.00	10.00	586.00
156	76	34	3	308.50	0.00	925.50
158	78	38	1	329.50	0.00	329.50
159	79	40	2	340.00	0.00	680.00
160	80	42	3	350.50	0.00	1051.50
2	2	5	5	156.25	0.00	781.25
6	6	13	9	198.25	10.00	1774.25
10	10	21	13	240.25	0.00	3123.25
14	14	29	19	282.25	0.00	5362.75
18	18	37	25	324.25	0.00	8106.25
62	62	5	5	156.25	10.00	771.25
66	66	13	9	198.25	0.00	1784.25
70	70	21	13	240.25	0.00	3123.25
74	74	29	19	282.25	0.00	5362.75
78	78	37	25	324.25	0.00	8106.25
81	1	4	3	151.00	0.00	453.00
85	5	12	7	193.00	10.00	1341.00
89	9	20	11	235.00	0.00	2585.00
93	13	28	16	277.00	0.00	4432.00
97	17	36	22	319.00	0.00	7018.00
141	61	4	3	151.00	10.00	443.00
145	65	12	7	193.00	0.00	1351.00
149	69	20	11	235.00	0.00	2585.00
153	73	28	16	277.00	0.00	4432.00
157	77	36	22	319.00	0.00	7018.00
\.


--
-- Data for Name: factura; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.factura (id_factura, fecha, id_cliente, id_usuario, id_seccion, subtotal, descuento, impuesto, total, tipo_cliente_venta, nombre_cliente_fugaz, monto_pagado, saldo_pendiente, porcentaje_pagado, estado_pago, estado_produccion, fecha_orden_produccion, fecha_entrega_estimada, fecha_entrega_real) FROM stdin;
26	2026-02-27 14:15:00	26	26	2	2046.50	0.00	305.48	2341.98	Habitual	\N	1756.49	585.49	75.00	Parcial	En producción	2026-02-27 14:45:00	2026-03-02	\N
27	2026-02-28 15:15:00	27	27	1	1266.75	0.00	188.51	1445.26	Habitual	\N	1083.95	361.31	75.00	Parcial	En producción	2026-02-28 15:45:00	2026-03-04	\N
28	2026-03-01 09:15:00	28	28	2	2162.00	0.00	324.30	2486.30	Habitual	\N	1864.73	621.57	75.00	Parcial	En producción	2026-03-01 09:45:00	2026-03-06	\N
29	2026-03-02 10:15:00	29	29	1	3099.25	0.00	464.89	3564.14	Habitual	\N	1782.07	1782.07	50.00	Parcial	Lista para entregar	2026-03-02 10:45:00	2026-03-08	\N
30	2026-03-03 11:15:00	30	30	2	2256.50	25.00	334.73	2566.23	Fugaz	José Ramírez	1283.12	1283.11	50.00	Parcial	Lista para entregar	2026-03-03 11:45:00	2026-03-05	\N
31	2026-03-04 12:15:00	31	1	1	1392.75	0.00	208.91	1601.66	Habitual	\N	800.83	800.83	50.00	Parcial	Lista para entregar	2026-03-04 12:45:00	2026-03-07	\N
32	2026-03-05 13:15:00	32	2	2	2372.00	0.00	355.80	2727.80	Habitual	\N	1363.90	1363.90	50.00	Parcial	Lista para entregar	2026-03-05 13:45:00	2026-03-09	\N
33	2026-03-06 14:15:00	33	3	1	3393.25	0.00	507.49	3890.74	Habitual	\N	1945.37	1945.37	50.00	Parcial	Lista para entregar	2026-03-06 14:45:00	2026-03-11	\N
34	2026-03-07 15:15:00	34	4	2	2466.50	0.00	368.48	2824.98	Habitual	\N	1412.49	1412.49	50.00	Parcial	Lista para entregar	2026-03-07 15:45:00	2026-03-13	\N
35	2026-03-08 09:15:00	35	5	1	1518.75	25.00	224.06	1717.81	Habitual	\N	858.91	858.90	50.00	Parcial	Lista para entregar	2026-03-08 09:45:00	2026-03-10	\N
36	2026-03-09 10:15:00	36	6	2	2582.00	0.00	387.30	2969.30	Fugaz	Carlos Mendoza	1484.65	1484.65	50.00	Parcial	Lista para entregar	2026-03-09 10:45:00	2026-03-12	\N
37	2026-03-10 11:15:00	37	7	1	3687.25	0.00	553.09	4240.34	Habitual	\N	2120.17	2120.17	50.00	Parcial	Lista para entregar	2026-03-10 11:45:00	2026-03-14	\N
38	2026-03-11 12:15:00	38	8	2	2676.50	0.00	401.48	3077.98	Habitual	\N	1538.99	1538.99	50.00	Parcial	Lista para entregar	2026-03-11 12:45:00	2026-03-16	\N
39	2026-03-12 13:15:00	39	9	1	1644.75	0.00	246.71	1891.46	Habitual	\N	0.00	1891.46	0.00	Pendiente	En producción	2026-03-12 13:45:00	2026-03-18	\N
40	2026-03-13 14:15:00	40	10	2	2792.00	25.00	413.55	3170.55	Habitual	\N	0.00	3170.55	0.00	Pendiente	En producción	2026-03-13 14:45:00	2026-03-15	\N
41	2026-03-14 15:15:00	41	11	1	3981.25	0.00	595.69	4566.94	Habitual	\N	0.00	4566.94	0.00	Pendiente	En producción	2026-03-14 15:45:00	2026-03-17	\N
42	2026-03-15 09:15:00	42	12	2	2886.50	0.00	432.98	3319.48	Fugaz	José Ramírez	0.00	3319.48	0.00	Pendiente	En producción	2026-03-15 09:45:00	2026-03-19	\N
43	2026-03-16 10:15:00	43	13	1	1770.75	0.00	265.61	2036.36	Habitual	\N	0.00	2036.36	0.00	Pendiente	En producción	2026-03-16 10:45:00	2026-03-21	\N
44	2026-03-17 11:15:00	44	14	2	3002.00	0.00	450.30	3452.30	Habitual	\N	0.00	3452.30	0.00	Pendiente	En producción	2026-03-17 11:45:00	2026-03-23	\N
45	2026-03-18 12:15:00	45	15	1	4275.25	25.00	637.54	4887.79	Habitual	\N	0.00	4887.79	0.00	Pendiente	En producción	2026-03-18 12:45:00	2026-03-20	\N
46	2026-03-19 13:15:00	46	16	2	3096.50	0.00	464.48	3560.98	Habitual	\N	0.00	3560.98	0.00	Pendiente	En producción	2026-03-19 13:45:00	2026-03-22	\N
47	2026-03-20 14:15:00	47	17	1	1896.75	0.00	283.01	2169.76	Habitual	\N	0.00	2169.76	0.00	Pendiente	En producción	2026-03-20 14:45:00	2026-03-24	\N
48	2026-03-21 15:15:00	48	18	2	3212.00	0.00	480.30	3682.30	Fugaz	Carlos Mendoza	0.00	3682.30	0.00	Pendiente	En producción	2026-03-21 15:45:00	2026-03-26	\N
49	2026-03-22 09:15:00	49	19	1	4569.25	0.00	685.39	5254.64	Habitual	\N	0.00	5254.64	0.00	Pendiente	Pendiente	2026-03-22 09:45:00	2026-03-28	\N
50	2026-03-23 10:15:00	50	20	2	3306.50	25.00	492.23	3773.73	Habitual	\N	0.00	3773.73	0.00	Pendiente	Pendiente	2026-03-23 10:45:00	2026-03-25	\N
51	2026-03-24 11:15:00	51	21	1	2022.75	0.00	303.41	2326.16	Habitual	\N	0.00	2326.16	0.00	Pendiente	Pendiente	2026-03-24 11:45:00	2026-03-27	\N
52	2026-03-25 12:15:00	52	22	2	3422.00	0.00	513.30	3935.30	Habitual	\N	0.00	3935.30	0.00	Pendiente	Pendiente	2026-03-25 12:45:00	2026-03-29	\N
53	2026-03-26 13:15:00	53	23	1	4863.25	0.00	729.49	5592.74	Habitual	\N	0.00	5592.74	0.00	Pendiente	Pendiente	2026-03-26 13:45:00	2026-03-31	\N
54	2026-03-27 14:15:00	54	24	2	3516.50	0.00	525.98	4032.48	Fugaz	José Ramírez	0.00	4032.48	0.00	Pendiente	Pendiente	2026-03-27 14:45:00	2026-04-02	\N
55	2026-03-28 15:15:00	55	25	1	2148.75	25.00	317.06	2430.81	Habitual	\N	0.00	2430.81	0.00	Pendiente	Pendiente	2026-03-28 15:45:00	2026-03-30	\N
56	2026-03-29 09:15:00	56	26	2	3632.00	0.00	544.80	4176.80	Habitual	\N	0.00	4176.80	0.00	Pendiente	Pendiente	2026-03-29 09:45:00	2026-04-01	\N
57	2026-03-30 10:15:00	57	27	1	5157.25	0.00	773.59	5930.84	Habitual	\N	2075.79	3855.05	35.00	Parcial	Cancelada	2026-03-30 10:45:00	2026-04-03	\N
58	2026-03-31 11:15:00	58	28	2	3726.50	0.00	558.98	4285.48	Habitual	\N	1499.92	2785.56	35.00	Parcial	Cancelada	2026-03-31 11:45:00	2026-04-05	\N
59	2026-04-01 12:15:00	59	29	1	2274.75	0.00	341.21	2615.96	Habitual	\N	915.59	1700.37	35.00	Parcial	Cancelada	2026-04-01 12:45:00	2026-04-07	\N
60	2026-04-02 13:15:00	60	30	2	692.00	25.00	100.05	767.05	Fugaz	Carlos Mendoza	268.47	498.58	35.00	Parcial	Cancelada	2026-04-02 13:45:00	2026-04-04	\N
61	2026-04-03 14:15:00	61	1	1	1041.25	0.00	154.69	1185.94	Habitual	\N	415.08	770.86	35.00	Parcial	Cancelada	2026-04-03 14:45:00	2026-04-06	\N
62	2026-04-04 15:15:00	62	2	2	786.50	0.00	116.48	892.98	Habitual	\N	312.54	580.44	35.00	Parcial	Cancelada	2026-04-04 15:45:00	2026-04-08	\N
63	2026-04-05 09:15:00	63	3	1	510.75	0.00	76.61	587.36	Habitual	\N	0.00	587.36	0.00	Pendiente	Cancelada	2026-04-05 09:45:00	2026-04-10	\N
64	2026-04-06 10:15:00	64	4	2	902.00	0.00	135.30	1037.30	Habitual	\N	0.00	1037.30	0.00	Pendiente	Cancelada	2026-04-06 10:45:00	2026-04-12	\N
65	2026-04-07 11:15:00	65	5	1	1335.25	25.00	196.54	1506.79	Habitual	\N	0.00	1506.79	0.00	Pendiente	Cancelada	2026-04-07 11:45:00	2026-04-09	\N
66	2026-04-08 12:15:00	66	6	2	996.50	0.00	149.48	1145.98	Fugaz	José Ramírez	0.00	1145.98	0.00	Pendiente	Cancelada	2026-04-08 12:45:00	2026-04-11	\N
67	2026-04-09 13:15:00	67	7	1	636.75	0.00	95.51	732.26	Habitual	\N	732.26	0.00	100.00	Pagado	En producción	2026-04-09 13:45:00	2026-04-13	\N
68	2026-04-10 14:15:00	68	8	2	1112.00	0.00	165.30	1267.30	Habitual	\N	1267.30	0.00	100.00	Pagado	En producción	2026-04-10 14:45:00	2026-04-15	\N
69	2026-04-11 15:15:00	69	9	1	1629.25	0.00	242.89	1862.14	Habitual	\N	1862.14	0.00	100.00	Pagado	En producción	2026-04-11 15:45:00	2026-04-17	\N
70	2026-04-12 09:15:00	70	10	2	1206.50	25.00	177.23	1358.73	Habitual	\N	1358.73	0.00	100.00	Pagado	En producción	2026-04-12 09:45:00	2026-04-14	\N
71	2026-04-13 10:15:00	71	11	1	762.75	0.00	114.41	877.16	Habitual	\N	789.44	87.72	90.00	Parcial	Entregada	2026-04-13 10:45:00	2026-04-16	2026-04-17
72	2026-04-14 11:15:00	72	12	2	1322.00	0.00	198.30	1520.30	Fugaz	Carlos Mendoza	1368.27	152.03	90.00	Parcial	Entregada	2026-04-14 11:45:00	2026-04-18	2026-04-19
73	2026-04-15 12:15:00	73	13	1	1923.25	0.00	288.49	2211.74	Habitual	\N	1990.57	221.17	90.00	Parcial	Entregada	2026-04-15 12:45:00	2026-04-20	2026-04-21
74	2026-04-16 13:15:00	74	14	2	1416.50	0.00	212.48	1628.98	Habitual	\N	1466.08	162.90	90.00	Parcial	Entregada	2026-04-16 13:45:00	2026-04-22	2026-04-23
75	2026-04-17 14:15:00	75	15	1	888.75	25.00	128.06	981.81	Habitual	\N	883.63	98.18	90.00	Parcial	Entregada	2026-04-17 14:45:00	2026-04-19	2026-04-20
76	2026-04-18 15:15:00	76	16	2	1532.00	0.00	228.30	1750.30	Habitual	\N	1750.30	0.00	100.00	Pagado	Cancelada	2026-04-18 15:45:00	2026-04-21	\N
77	2026-04-19 09:15:00	77	17	1	2217.25	0.00	332.59	2549.84	Habitual	\N	2549.84	0.00	100.00	Pagado	Cancelada	2026-04-19 09:45:00	2026-04-23	\N
78	2026-04-20 10:15:00	78	18	2	1626.50	0.00	243.98	1870.48	Fugaz	José Ramírez	1870.48	0.00	100.00	Pagado	Cancelada	2026-04-20 10:45:00	2026-04-25	\N
79	2026-04-21 11:15:00	79	19	1	1014.75	0.00	152.21	1166.96	Habitual	\N	1166.96	0.00	100.00	Pagado	Cancelada	2026-04-21 11:45:00	2026-04-27	\N
1	2026-02-02 10:15:00	1	1	1	1041.25	0.00	156.19	1197.44	Habitual	\N	1197.44	0.00	100.00	Pagado	Entregada	2026-02-02 10:45:00	2026-02-05	2026-02-05
2	2026-02-03 11:15:00	2	2	2	786.50	0.00	117.98	904.48	Habitual	\N	904.48	0.00	100.00	Pagado	Entregada	2026-02-03 11:45:00	2026-02-07	2026-02-07
3	2026-02-04 12:15:00	3	3	1	510.75	0.00	76.61	587.36	Habitual	\N	587.36	0.00	100.00	Pagado	Entregada	2026-02-04 12:45:00	2026-02-09	2026-02-09
4	2026-02-05 13:15:00	4	4	2	902.00	0.00	135.30	1037.30	Habitual	\N	1037.30	0.00	100.00	Pagado	Entregada	2026-02-05 13:45:00	2026-02-11	2026-02-11
5	2026-02-06 14:15:00	5	5	1	1335.25	25.00	195.04	1495.29	Habitual	\N	1495.29	0.00	100.00	Pagado	Entregada	2026-02-06 14:45:00	2026-02-08	2026-02-08
6	2026-02-07 15:15:00	6	6	2	996.50	0.00	147.98	1134.48	Fugaz	José Ramírez	1134.48	0.00	100.00	Pagado	Entregada	2026-02-07 15:45:00	2026-02-10	2026-02-10
7	2026-02-08 09:15:00	7	7	1	636.75	0.00	95.51	732.26	Habitual	\N	732.26	0.00	100.00	Pagado	Entregada	2026-02-08 09:45:00	2026-02-12	2026-02-12
8	2026-02-09 10:15:00	8	8	2	1112.00	0.00	166.80	1278.80	Habitual	\N	1278.80	0.00	100.00	Pagado	Entregada	2026-02-09 10:45:00	2026-02-14	2026-02-14
9	2026-02-10 11:15:00	9	9	1	1629.25	0.00	244.39	1873.64	Habitual	\N	1873.64	0.00	100.00	Pagado	Entregada	2026-02-10 11:45:00	2026-02-16	2026-02-16
10	2026-02-11 12:15:00	10	10	2	1206.50	25.00	177.23	1358.73	Habitual	\N	1358.73	0.00	100.00	Pagado	Entregada	2026-02-11 12:45:00	2026-02-13	2026-02-13
11	2026-02-12 13:15:00	11	11	1	762.75	0.00	114.41	877.16	Habitual	\N	877.16	0.00	100.00	Pagado	Lista para entregar	2026-02-12 13:45:00	2026-02-15	\N
12	2026-02-13 14:15:00	12	12	2	1322.00	0.00	196.80	1508.80	Fugaz	Carlos Mendoza	1508.80	0.00	100.00	Pagado	Lista para entregar	2026-02-13 14:45:00	2026-02-17	\N
13	2026-02-14 15:15:00	13	13	1	1923.25	0.00	286.99	2200.24	Habitual	\N	2200.24	0.00	100.00	Pagado	Lista para entregar	2026-02-14 15:45:00	2026-02-19	\N
14	2026-02-15 09:15:00	14	14	2	1416.50	0.00	212.48	1628.98	Habitual	\N	1628.98	0.00	100.00	Pagado	Lista para entregar	2026-02-15 09:45:00	2026-02-21	\N
15	2026-02-16 10:15:00	15	15	1	888.75	25.00	129.56	993.31	Habitual	\N	993.31	0.00	100.00	Pagado	Lista para entregar	2026-02-16 10:45:00	2026-02-18	\N
16	2026-02-17 11:15:00	16	16	2	1532.00	0.00	229.80	1761.80	Habitual	\N	1761.80	0.00	100.00	Pagado	Lista para entregar	2026-02-17 11:45:00	2026-02-20	\N
17	2026-02-18 12:15:00	17	17	1	2217.25	0.00	332.59	2549.84	Habitual	\N	2549.84	0.00	100.00	Pagado	Lista para entregar	2026-02-18 12:45:00	2026-02-22	\N
18	2026-02-19 13:15:00	18	18	2	1626.50	0.00	243.98	1870.48	Fugaz	José Ramírez	1870.48	0.00	100.00	Pagado	Lista para entregar	2026-02-19 13:45:00	2026-02-24	\N
19	2026-02-20 14:15:00	19	19	1	1014.75	0.00	150.71	1155.46	Habitual	\N	866.60	288.86	75.00	Parcial	En producción	2026-02-20 14:45:00	2026-02-26	\N
20	2026-02-21 15:15:00	20	20	2	1742.00	25.00	256.05	1963.05	Habitual	\N	1472.29	490.76	75.00	Parcial	En producción	2026-02-21 15:45:00	2026-02-23	\N
21	2026-02-22 09:15:00	21	21	1	2511.25	0.00	376.69	2887.94	Habitual	\N	2165.96	721.98	75.00	Parcial	En producción	2026-02-22 09:45:00	2026-02-25	\N
22	2026-02-23 10:15:00	22	22	2	1836.50	0.00	275.48	2111.98	Habitual	\N	1583.99	527.99	75.00	Parcial	En producción	2026-02-23 10:45:00	2026-02-27	\N
23	2026-02-24 11:15:00	23	23	1	1140.75	0.00	171.11	1311.86	Habitual	\N	983.90	327.96	75.00	Parcial	En producción	2026-02-24 11:45:00	2026-03-01	\N
24	2026-02-25 12:15:00	24	24	2	1952.00	0.00	292.80	2244.80	Fugaz	Carlos Mendoza	1683.60	561.20	75.00	Parcial	En producción	2026-02-25 12:45:00	2026-03-03	\N
25	2026-02-26 13:15:00	25	25	1	2805.25	25.00	417.04	3197.29	Habitual	\N	2397.97	799.32	75.00	Parcial	En producción	2026-02-26 13:45:00	2026-02-28	\N
80	2026-04-22 12:15:00	80	20	2	1742.00	25.00	257.55	1974.55	Habitual	\N	1974.55	0.00	100.00	Pagado	Cancelada	2026-04-22 12:45:00	2026-04-24	\N
\.


--
-- Data for Name: factura_estado_historial; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.factura_estado_historial (id_historial, id_factura, tipo_evento, estado_pago_anterior, estado_pago_nuevo, estado_produccion_anterior, estado_produccion_nuevo, monto_pagado_anterior, monto_pagado_nuevo, monto_abonado, saldo_anterior, saldo_nuevo, fecha_entrega_estimada_anterior, fecha_entrega_estimada_nueva, comentario, fecha_evento) FROM stdin;
1	20	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1963.05	\N	2026-02-23	La factura fue registrada en el sistema.	2026-02-21 15:15:00
2	25	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3197.29	\N	2026-02-28	La factura fue registrada en el sistema.	2026-02-26 13:15:00
3	26	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2341.98	\N	2026-03-02	La factura fue registrada en el sistema.	2026-02-27 14:15:00
4	27	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1445.26	\N	2026-03-04	La factura fue registrada en el sistema.	2026-02-28 15:15:00
5	11	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	877.16	\N	2026-02-15	La factura fue registrada en el sistema.	2026-02-12 13:15:00
6	39	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1891.46	\N	2026-03-18	La factura fue registrada en el sistema.	2026-03-12 13:15:00
7	17	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2549.84	\N	2026-02-22	La factura fue registrada en el sistema.	2026-02-18 12:15:00
8	66	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1145.98	\N	2026-04-11	La factura fue registrada en el sistema.	2026-04-08 12:15:00
9	33	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3890.74	\N	2026-03-11	La factura fue registrada en el sistema.	2026-03-06 14:15:00
10	57	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	5930.84	\N	2026-04-03	La factura fue registrada en el sistema.	2026-03-30 10:15:00
11	31	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1601.66	\N	2026-03-07	La factura fue registrada en el sistema.	2026-03-04 12:15:00
12	34	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2824.98	\N	2026-03-13	La factura fue registrada en el sistema.	2026-03-07 15:15:00
13	12	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1508.80	\N	2026-02-17	La factura fue registrada en el sistema.	2026-02-13 14:15:00
14	10	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1358.73	\N	2026-02-13	La factura fue registrada en el sistema.	2026-02-11 12:15:00
15	18	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1870.48	\N	2026-02-24	La factura fue registrada en el sistema.	2026-02-19 13:15:00
16	64	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1037.30	\N	2026-04-12	La factura fue registrada en el sistema.	2026-04-06 10:15:00
17	71	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	877.16	\N	2026-04-16	La factura fue registrada en el sistema.	2026-04-13 10:15:00
18	2	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	904.48	\N	2026-02-07	La factura fue registrada en el sistema.	2026-02-03 11:15:00
19	72	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1520.30	\N	2026-04-18	La factura fue registrada en el sistema.	2026-04-14 11:15:00
20	47	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2169.76	\N	2026-03-24	La factura fue registrada en el sistema.	2026-03-20 14:15:00
21	46	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3560.98	\N	2026-03-22	La factura fue registrada en el sistema.	2026-03-19 13:15:00
22	15	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	993.31	\N	2026-02-18	La factura fue registrada en el sistema.	2026-02-16 10:15:00
23	77	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2549.84	\N	2026-04-23	La factura fue registrada en el sistema.	2026-04-19 09:15:00
24	73	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2211.74	\N	2026-04-20	La factura fue registrada en el sistema.	2026-04-15 12:15:00
25	56	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	4176.80	\N	2026-04-01	La factura fue registrada en el sistema.	2026-03-29 09:15:00
26	40	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3170.55	\N	2026-03-15	La factura fue registrada en el sistema.	2026-03-13 14:15:00
27	13	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2200.24	\N	2026-02-19	La factura fue registrada en el sistema.	2026-02-14 15:15:00
28	21	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2887.94	\N	2026-02-25	La factura fue registrada en el sistema.	2026-02-22 09:15:00
29	5	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1495.29	\N	2026-02-08	La factura fue registrada en el sistema.	2026-02-06 14:15:00
30	19	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1155.46	\N	2026-02-26	La factura fue registrada en el sistema.	2026-02-20 14:15:00
31	65	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1506.79	\N	2026-04-09	La factura fue registrada en el sistema.	2026-04-07 11:15:00
32	52	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3935.30	\N	2026-03-29	La factura fue registrada en el sistema.	2026-03-25 12:15:00
33	37	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	4240.34	\N	2026-03-14	La factura fue registrada en el sistema.	2026-03-10 11:15:00
34	32	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2727.80	\N	2026-03-09	La factura fue registrada en el sistema.	2026-03-05 13:15:00
35	78	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1870.48	\N	2026-04-25	La factura fue registrada en el sistema.	2026-04-20 10:15:00
36	24	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2244.80	\N	2026-03-03	La factura fue registrada en el sistema.	2026-02-25 12:15:00
37	55	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2430.81	\N	2026-03-30	La factura fue registrada en el sistema.	2026-03-28 15:15:00
38	68	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1267.30	\N	2026-04-15	La factura fue registrada en el sistema.	2026-04-10 14:15:00
39	38	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3077.98	\N	2026-03-16	La factura fue registrada en el sistema.	2026-03-11 12:15:00
40	8	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1278.80	\N	2026-02-14	La factura fue registrada en el sistema.	2026-02-09 10:15:00
41	80	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1974.55	\N	2026-04-24	La factura fue registrada en el sistema.	2026-04-22 12:15:00
42	48	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3682.30	\N	2026-03-26	La factura fue registrada en el sistema.	2026-03-21 15:15:00
43	28	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2486.30	\N	2026-03-06	La factura fue registrada en el sistema.	2026-03-01 09:15:00
44	30	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2566.23	\N	2026-03-05	La factura fue registrada en el sistema.	2026-03-03 11:15:00
45	62	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	892.98	\N	2026-04-08	La factura fue registrada en el sistema.	2026-04-04 15:15:00
46	67	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	732.26	\N	2026-04-13	La factura fue registrada en el sistema.	2026-04-09 13:15:00
47	50	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3773.73	\N	2026-03-25	La factura fue registrada en el sistema.	2026-03-23 10:15:00
48	51	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2326.16	\N	2026-03-27	La factura fue registrada en el sistema.	2026-03-24 11:15:00
49	76	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1750.30	\N	2026-04-21	La factura fue registrada en el sistema.	2026-04-18 15:15:00
50	69	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1862.14	\N	2026-04-17	La factura fue registrada en el sistema.	2026-04-11 15:15:00
51	79	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1166.96	\N	2026-04-27	La factura fue registrada en el sistema.	2026-04-21 11:15:00
52	42	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3319.48	\N	2026-03-19	La factura fue registrada en el sistema.	2026-03-15 09:15:00
53	59	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2615.96	\N	2026-04-07	La factura fue registrada en el sistema.	2026-04-01 12:15:00
54	74	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1628.98	\N	2026-04-22	La factura fue registrada en el sistema.	2026-04-16 13:15:00
55	6	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1134.48	\N	2026-02-10	La factura fue registrada en el sistema.	2026-02-07 15:15:00
56	29	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3564.14	\N	2026-03-08	La factura fue registrada en el sistema.	2026-03-02 10:15:00
57	41	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	4566.94	\N	2026-03-17	La factura fue registrada en el sistema.	2026-03-14 15:15:00
58	16	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1761.80	\N	2026-02-20	La factura fue registrada en el sistema.	2026-02-17 11:15:00
59	54	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	4032.48	\N	2026-04-02	La factura fue registrada en el sistema.	2026-03-27 14:15:00
60	36	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2969.30	\N	2026-03-12	La factura fue registrada en el sistema.	2026-03-09 10:15:00
61	4	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1037.30	\N	2026-02-11	La factura fue registrada en el sistema.	2026-02-05 13:15:00
62	53	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	5592.74	\N	2026-03-31	La factura fue registrada en el sistema.	2026-03-26 13:15:00
63	23	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1311.86	\N	2026-03-01	La factura fue registrada en el sistema.	2026-02-24 11:15:00
64	44	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	3452.30	\N	2026-03-23	La factura fue registrada en el sistema.	2026-03-17 11:15:00
65	58	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	4285.48	\N	2026-04-05	La factura fue registrada en el sistema.	2026-03-31 11:15:00
66	1	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1197.44	\N	2026-02-05	La factura fue registrada en el sistema.	2026-02-02 10:15:00
67	49	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	5254.64	\N	2026-03-28	La factura fue registrada en el sistema.	2026-03-22 09:15:00
68	22	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2111.98	\N	2026-02-27	La factura fue registrada en el sistema.	2026-02-23 10:15:00
69	70	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1358.73	\N	2026-04-14	La factura fue registrada en el sistema.	2026-04-12 09:15:00
70	45	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	4887.79	\N	2026-03-20	La factura fue registrada en el sistema.	2026-03-18 12:15:00
71	60	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	767.05	\N	2026-04-04	La factura fue registrada en el sistema.	2026-04-02 13:15:00
72	75	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	981.81	\N	2026-04-19	La factura fue registrada en el sistema.	2026-04-17 14:15:00
73	43	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	2036.36	\N	2026-03-21	La factura fue registrada en el sistema.	2026-03-16 10:15:00
74	3	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	587.36	\N	2026-02-09	La factura fue registrada en el sistema.	2026-02-04 12:15:00
75	61	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1185.94	\N	2026-04-06	La factura fue registrada en el sistema.	2026-04-03 14:15:00
76	14	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1628.98	\N	2026-02-21	La factura fue registrada en el sistema.	2026-02-15 09:15:00
77	35	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1717.81	\N	2026-03-10	La factura fue registrada en el sistema.	2026-03-08 09:15:00
78	63	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	587.36	\N	2026-04-10	La factura fue registrada en el sistema.	2026-04-05 09:15:00
79	9	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	1873.64	\N	2026-02-16	La factura fue registrada en el sistema.	2026-02-10 11:15:00
80	7	Factura creada	\N	Pendiente	\N	Pendiente	\N	0.00	0.00	\N	732.26	\N	2026-02-12	La factura fue registrada en el sistema.	2026-02-08 09:15:00
81	1	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	598.72	598.72	1197.44	598.72	\N	2026-02-05	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-02 10:25:00
82	2	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	452.24	452.24	904.48	452.24	\N	2026-02-07	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-03 11:25:00
83	3	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	293.68	293.68	587.36	293.68	\N	2026-02-09	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-04 12:25:00
84	4	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	518.65	518.65	1037.30	518.65	\N	2026-02-11	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-05 13:25:00
85	5	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	747.65	747.65	1495.29	747.65	\N	2026-02-08	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-06 14:25:00
86	6	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	567.24	567.24	1134.48	567.24	\N	2026-02-10	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-07 15:25:00
87	7	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	366.13	366.13	732.26	366.13	\N	2026-02-12	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-08 09:25:00
88	8	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	639.40	639.40	1278.80	639.40	\N	2026-02-14	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-09 10:25:00
89	9	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	936.82	936.82	1873.64	936.82	\N	2026-02-16	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-10 11:25:00
90	10	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	679.37	679.37	1358.73	679.37	\N	2026-02-13	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-11 12:25:00
91	11	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	438.58	438.58	877.16	438.58	\N	2026-02-15	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-12 13:25:00
92	12	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	754.40	754.40	1508.80	754.40	\N	2026-02-17	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-13 14:25:00
93	13	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1100.12	1100.12	2200.24	1100.12	\N	2026-02-19	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-14 15:25:00
94	14	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	814.49	814.49	1628.98	814.49	\N	2026-02-21	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-15 09:25:00
95	15	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	496.66	496.66	993.31	496.66	\N	2026-02-18	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-16 10:25:00
96	16	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	880.90	880.90	1761.80	880.90	\N	2026-02-20	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-17 11:25:00
97	17	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1274.92	1274.92	2549.84	1274.92	\N	2026-02-22	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-18 12:25:00
98	18	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	935.24	935.24	1870.48	935.24	\N	2026-02-24	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-19 13:25:00
168	8	Producción iniciada	Parcial	Parcial	Pendiente	En producción	639.40	639.40	0.00	639.40	639.40	\N	2026-02-14	La orden pasó al área de producción.	2026-02-09 11:15:00
99	19	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	577.73	577.73	1155.46	577.73	\N	2026-02-26	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-20 14:25:00
100	20	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	981.53	981.53	1963.05	981.53	\N	2026-02-23	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-21 15:25:00
101	21	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1443.97	1443.97	2887.94	1443.97	\N	2026-02-25	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-22 09:25:00
102	22	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1055.99	1055.99	2111.98	1055.99	\N	2026-02-27	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-23 10:25:00
103	23	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	655.93	655.93	1311.86	655.93	\N	2026-03-01	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-24 11:25:00
104	24	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1122.40	1122.40	2244.80	1122.40	\N	2026-03-03	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-25 12:25:00
105	25	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1598.65	1598.65	3197.29	1598.65	\N	2026-02-28	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-26 13:25:00
106	26	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1170.99	1170.99	2341.98	1170.99	\N	2026-03-02	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-27 14:25:00
107	27	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	722.63	722.63	1445.26	722.63	\N	2026-03-04	El cliente realizó el abono inicial requerido para iniciar producción.	2026-02-28 15:25:00
108	28	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1243.15	1243.15	2486.30	1243.15	\N	2026-03-06	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-01 09:25:00
109	29	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1782.07	1782.07	3564.14	1782.07	\N	2026-03-08	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-02 10:25:00
110	30	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1283.12	1283.12	2566.23	1283.12	\N	2026-03-05	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-03 11:25:00
111	31	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	800.83	800.83	1601.66	800.83	\N	2026-03-07	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-04 12:25:00
112	32	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1363.90	1363.90	2727.80	1363.90	\N	2026-03-09	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-05 13:25:00
113	33	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1945.37	1945.37	3890.74	1945.37	\N	2026-03-11	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-06 14:25:00
114	34	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1412.49	1412.49	2824.98	1412.49	\N	2026-03-13	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-07 15:25:00
115	35	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	858.91	858.91	1717.81	858.91	\N	2026-03-10	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-08 09:25:00
116	36	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1484.65	1484.65	2969.30	1484.65	\N	2026-03-12	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-09 10:25:00
117	37	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2120.17	2120.17	4240.34	2120.17	\N	2026-03-14	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-10 11:25:00
118	38	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1538.99	1538.99	3077.98	1538.99	\N	2026-03-16	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-11 12:25:00
119	39	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	945.73	945.73	1891.46	945.73	\N	2026-03-18	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-12 13:25:00
120	40	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1585.28	1585.28	3170.55	1585.28	\N	2026-03-15	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-13 14:25:00
121	41	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2283.47	2283.47	4566.94	2283.47	\N	2026-03-17	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-14 15:25:00
122	42	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1659.74	1659.74	3319.48	1659.74	\N	2026-03-19	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-15 09:25:00
123	43	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1018.18	1018.18	2036.36	1018.18	\N	2026-03-21	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-16 10:25:00
124	44	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1726.15	1726.15	3452.30	1726.15	\N	2026-03-23	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-17 11:25:00
125	45	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2443.90	2443.90	4887.79	2443.90	\N	2026-03-20	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-18 12:25:00
126	46	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1780.49	1780.49	3560.98	1780.49	\N	2026-03-22	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-19 13:25:00
127	47	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1084.88	1084.88	2169.76	1084.88	\N	2026-03-24	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-20 14:25:00
128	48	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1841.15	1841.15	3682.30	1841.15	\N	2026-03-26	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-21 15:25:00
129	49	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2627.32	2627.32	5254.64	2627.32	\N	2026-03-28	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-22 09:25:00
130	50	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1886.87	1886.87	3773.73	1886.87	\N	2026-03-25	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-23 10:25:00
131	51	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1163.08	1163.08	2326.16	1163.08	\N	2026-03-27	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-24 11:25:00
132	52	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1967.65	1967.65	3935.30	1967.65	\N	2026-03-29	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-25 12:25:00
133	53	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2796.37	2796.37	5592.74	2796.37	\N	2026-03-31	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-26 13:25:00
134	54	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2016.24	2016.24	4032.48	2016.24	\N	2026-04-02	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-27 14:25:00
135	55	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1215.41	1215.41	2430.81	1215.41	\N	2026-03-30	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-28 15:25:00
136	56	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2088.40	2088.40	4176.80	2088.40	\N	2026-04-01	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-29 09:25:00
137	57	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2965.42	2965.42	5930.84	2965.42	\N	2026-04-03	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-30 10:25:00
138	58	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	2142.74	2142.74	4285.48	2142.74	\N	2026-04-05	El cliente realizó el abono inicial requerido para iniciar producción.	2026-03-31 11:25:00
139	59	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1307.98	1307.98	2615.96	1307.98	\N	2026-04-07	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-01 12:25:00
140	60	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	383.53	383.53	767.05	383.53	\N	2026-04-04	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-02 13:25:00
141	61	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	592.97	592.97	1185.94	592.97	\N	2026-04-06	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-03 14:25:00
142	62	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	446.49	446.49	892.98	446.49	\N	2026-04-08	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-04 15:25:00
143	63	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	293.68	293.68	587.36	293.68	\N	2026-04-10	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-05 09:25:00
144	64	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	518.65	518.65	1037.30	518.65	\N	2026-04-12	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-06 10:25:00
145	65	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	753.40	753.40	1506.79	753.40	\N	2026-04-09	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-07 11:25:00
146	66	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	572.99	572.99	1145.98	572.99	\N	2026-04-11	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-08 12:25:00
147	67	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	366.13	366.13	732.26	366.13	\N	2026-04-13	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-09 13:25:00
148	68	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	633.65	633.65	1267.30	633.65	\N	2026-04-15	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-10 14:25:00
149	69	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	931.07	931.07	1862.14	931.07	\N	2026-04-17	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-11 15:25:00
150	70	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	679.37	679.37	1358.73	679.37	\N	2026-04-14	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-12 09:25:00
151	71	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	438.58	438.58	877.16	438.58	\N	2026-04-16	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-13 10:25:00
152	72	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	760.15	760.15	1520.30	760.15	\N	2026-04-18	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-14 11:25:00
153	73	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1105.87	1105.87	2211.74	1105.87	\N	2026-04-20	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-15 12:25:00
154	74	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	814.49	814.49	1628.98	814.49	\N	2026-04-22	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-16 13:25:00
155	75	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	490.91	490.91	981.81	490.91	\N	2026-04-19	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-17 14:25:00
156	76	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	875.15	875.15	1750.30	875.15	\N	2026-04-21	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-18 15:25:00
157	77	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	1274.92	1274.92	2549.84	1274.92	\N	2026-04-23	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-19 09:25:00
158	78	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	935.24	935.24	1870.48	935.24	\N	2026-04-25	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-20 10:25:00
159	79	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	583.48	583.48	1166.96	583.48	\N	2026-04-27	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-21 11:25:00
160	80	Abono inicial registrado	Pendiente	Parcial	Pendiente	En producción	0.00	987.28	987.28	1974.55	987.28	\N	2026-04-24	El cliente realizó el abono inicial requerido para iniciar producción.	2026-04-22 12:25:00
161	1	Producción iniciada	Parcial	Parcial	Pendiente	En producción	598.72	598.72	0.00	598.72	598.72	\N	2026-02-05	La orden pasó al área de producción.	2026-02-02 11:15:00
162	2	Producción iniciada	Parcial	Parcial	Pendiente	En producción	452.24	452.24	0.00	452.24	452.24	\N	2026-02-07	La orden pasó al área de producción.	2026-02-03 12:15:00
163	3	Producción iniciada	Parcial	Parcial	Pendiente	En producción	293.68	293.68	0.00	293.68	293.68	\N	2026-02-09	La orden pasó al área de producción.	2026-02-04 13:15:00
164	4	Producción iniciada	Parcial	Parcial	Pendiente	En producción	518.65	518.65	0.00	518.65	518.65	\N	2026-02-11	La orden pasó al área de producción.	2026-02-05 14:15:00
165	5	Producción iniciada	Parcial	Parcial	Pendiente	En producción	747.65	747.65	0.00	747.65	747.65	\N	2026-02-08	La orden pasó al área de producción.	2026-02-06 15:15:00
166	6	Producción iniciada	Parcial	Parcial	Pendiente	En producción	567.24	567.24	0.00	567.24	567.24	\N	2026-02-10	La orden pasó al área de producción.	2026-02-07 16:15:00
167	7	Producción iniciada	Parcial	Parcial	Pendiente	En producción	366.13	366.13	0.00	366.13	366.13	\N	2026-02-12	La orden pasó al área de producción.	2026-02-08 10:15:00
169	9	Producción iniciada	Parcial	Parcial	Pendiente	En producción	936.82	936.82	0.00	936.82	936.82	\N	2026-02-16	La orden pasó al área de producción.	2026-02-10 12:15:00
170	10	Producción iniciada	Parcial	Parcial	Pendiente	En producción	679.37	679.37	0.00	679.37	679.37	\N	2026-02-13	La orden pasó al área de producción.	2026-02-11 13:15:00
171	11	Producción iniciada	Parcial	Parcial	Pendiente	En producción	438.58	438.58	0.00	438.58	438.58	\N	2026-02-15	La orden pasó al área de producción.	2026-02-12 14:15:00
172	12	Producción iniciada	Parcial	Parcial	Pendiente	En producción	754.40	754.40	0.00	754.40	754.40	\N	2026-02-17	La orden pasó al área de producción.	2026-02-13 15:15:00
173	13	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1100.12	1100.12	0.00	1100.12	1100.12	\N	2026-02-19	La orden pasó al área de producción.	2026-02-14 16:15:00
174	14	Producción iniciada	Parcial	Parcial	Pendiente	En producción	814.49	814.49	0.00	814.49	814.49	\N	2026-02-21	La orden pasó al área de producción.	2026-02-15 10:15:00
175	15	Producción iniciada	Parcial	Parcial	Pendiente	En producción	496.66	496.66	0.00	496.66	496.66	\N	2026-02-18	La orden pasó al área de producción.	2026-02-16 11:15:00
176	16	Producción iniciada	Parcial	Parcial	Pendiente	En producción	880.90	880.90	0.00	880.90	880.90	\N	2026-02-20	La orden pasó al área de producción.	2026-02-17 12:15:00
177	17	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1274.92	1274.92	0.00	1274.92	1274.92	\N	2026-02-22	La orden pasó al área de producción.	2026-02-18 13:15:00
178	18	Producción iniciada	Parcial	Parcial	Pendiente	En producción	935.24	935.24	0.00	935.24	935.24	\N	2026-02-24	La orden pasó al área de producción.	2026-02-19 14:15:00
179	19	Producción iniciada	Parcial	Parcial	Pendiente	En producción	577.73	577.73	0.00	577.73	577.73	\N	2026-02-26	La orden pasó al área de producción.	2026-02-20 15:15:00
180	20	Producción iniciada	Parcial	Parcial	Pendiente	En producción	981.53	981.53	0.00	981.53	981.53	\N	2026-02-23	La orden pasó al área de producción.	2026-02-21 16:15:00
181	21	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1443.97	1443.97	0.00	1443.97	1443.97	\N	2026-02-25	La orden pasó al área de producción.	2026-02-22 10:15:00
182	22	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1055.99	1055.99	0.00	1055.99	1055.99	\N	2026-02-27	La orden pasó al área de producción.	2026-02-23 11:15:00
183	23	Producción iniciada	Parcial	Parcial	Pendiente	En producción	655.93	655.93	0.00	655.93	655.93	\N	2026-03-01	La orden pasó al área de producción.	2026-02-24 12:15:00
184	24	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1122.40	1122.40	0.00	1122.40	1122.40	\N	2026-03-03	La orden pasó al área de producción.	2026-02-25 13:15:00
185	25	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1598.65	1598.65	0.00	1598.65	1598.65	\N	2026-02-28	La orden pasó al área de producción.	2026-02-26 14:15:00
186	26	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1170.99	1170.99	0.00	1170.99	1170.99	\N	2026-03-02	La orden pasó al área de producción.	2026-02-27 15:15:00
187	27	Producción iniciada	Parcial	Parcial	Pendiente	En producción	722.63	722.63	0.00	722.63	722.63	\N	2026-03-04	La orden pasó al área de producción.	2026-02-28 16:15:00
188	28	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1243.15	1243.15	0.00	1243.15	1243.15	\N	2026-03-06	La orden pasó al área de producción.	2026-03-01 10:15:00
189	29	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1782.07	1782.07	0.00	1782.07	1782.07	\N	2026-03-08	La orden pasó al área de producción.	2026-03-02 11:15:00
190	30	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1283.12	1283.12	0.00	1283.12	1283.12	\N	2026-03-05	La orden pasó al área de producción.	2026-03-03 12:15:00
191	31	Producción iniciada	Parcial	Parcial	Pendiente	En producción	800.83	800.83	0.00	800.83	800.83	\N	2026-03-07	La orden pasó al área de producción.	2026-03-04 13:15:00
192	32	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1363.90	1363.90	0.00	1363.90	1363.90	\N	2026-03-09	La orden pasó al área de producción.	2026-03-05 14:15:00
193	33	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1945.37	1945.37	0.00	1945.37	1945.37	\N	2026-03-11	La orden pasó al área de producción.	2026-03-06 15:15:00
194	34	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1412.49	1412.49	0.00	1412.49	1412.49	\N	2026-03-13	La orden pasó al área de producción.	2026-03-07 16:15:00
195	35	Producción iniciada	Parcial	Parcial	Pendiente	En producción	858.91	858.91	0.00	858.91	858.91	\N	2026-03-10	La orden pasó al área de producción.	2026-03-08 10:15:00
196	36	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1484.65	1484.65	0.00	1484.65	1484.65	\N	2026-03-12	La orden pasó al área de producción.	2026-03-09 11:15:00
197	37	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2120.17	2120.17	0.00	2120.17	2120.17	\N	2026-03-14	La orden pasó al área de producción.	2026-03-10 12:15:00
198	38	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1538.99	1538.99	0.00	1538.99	1538.99	\N	2026-03-16	La orden pasó al área de producción.	2026-03-11 13:15:00
199	39	Producción iniciada	Parcial	Parcial	Pendiente	En producción	945.73	945.73	0.00	945.73	945.73	\N	2026-03-18	La orden pasó al área de producción.	2026-03-12 14:15:00
200	40	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1585.28	1585.28	0.00	1585.28	1585.28	\N	2026-03-15	La orden pasó al área de producción.	2026-03-13 15:15:00
201	41	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2283.47	2283.47	0.00	2283.47	2283.47	\N	2026-03-17	La orden pasó al área de producción.	2026-03-14 16:15:00
202	42	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1659.74	1659.74	0.00	1659.74	1659.74	\N	2026-03-19	La orden pasó al área de producción.	2026-03-15 10:15:00
203	43	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1018.18	1018.18	0.00	1018.18	1018.18	\N	2026-03-21	La orden pasó al área de producción.	2026-03-16 11:15:00
204	44	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1726.15	1726.15	0.00	1726.15	1726.15	\N	2026-03-23	La orden pasó al área de producción.	2026-03-17 12:15:00
205	45	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2443.90	2443.90	0.00	2443.90	2443.90	\N	2026-03-20	La orden pasó al área de producción.	2026-03-18 13:15:00
206	46	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1780.49	1780.49	0.00	1780.49	1780.49	\N	2026-03-22	La orden pasó al área de producción.	2026-03-19 14:15:00
207	47	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1084.88	1084.88	0.00	1084.88	1084.88	\N	2026-03-24	La orden pasó al área de producción.	2026-03-20 15:15:00
208	48	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1841.15	1841.15	0.00	1841.15	1841.15	\N	2026-03-26	La orden pasó al área de producción.	2026-03-21 16:15:00
209	49	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2627.32	2627.32	0.00	2627.32	2627.32	\N	2026-03-28	La orden pasó al área de producción.	2026-03-22 10:15:00
210	50	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1886.87	1886.87	0.00	1886.87	1886.87	\N	2026-03-25	La orden pasó al área de producción.	2026-03-23 11:15:00
211	51	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1163.08	1163.08	0.00	1163.08	1163.08	\N	2026-03-27	La orden pasó al área de producción.	2026-03-24 12:15:00
212	52	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1967.65	1967.65	0.00	1967.65	1967.65	\N	2026-03-29	La orden pasó al área de producción.	2026-03-25 13:15:00
213	53	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2796.37	2796.37	0.00	2796.37	2796.37	\N	2026-03-31	La orden pasó al área de producción.	2026-03-26 14:15:00
214	54	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2016.24	2016.24	0.00	2016.24	2016.24	\N	2026-04-02	La orden pasó al área de producción.	2026-03-27 15:15:00
215	55	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1215.41	1215.41	0.00	1215.41	1215.41	\N	2026-03-30	La orden pasó al área de producción.	2026-03-28 16:15:00
216	56	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2088.40	2088.40	0.00	2088.40	2088.40	\N	2026-04-01	La orden pasó al área de producción.	2026-03-29 10:15:00
217	57	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2965.42	2965.42	0.00	2965.42	2965.42	\N	2026-04-03	La orden pasó al área de producción.	2026-03-30 11:15:00
218	58	Producción iniciada	Parcial	Parcial	Pendiente	En producción	2142.74	2142.74	0.00	2142.74	2142.74	\N	2026-04-05	La orden pasó al área de producción.	2026-03-31 12:15:00
219	59	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1307.98	1307.98	0.00	1307.98	1307.98	\N	2026-04-07	La orden pasó al área de producción.	2026-04-01 13:15:00
220	60	Producción iniciada	Parcial	Parcial	Pendiente	En producción	383.53	383.53	0.00	383.53	383.53	\N	2026-04-04	La orden pasó al área de producción.	2026-04-02 14:15:00
221	61	Producción iniciada	Parcial	Parcial	Pendiente	En producción	592.97	592.97	0.00	592.97	592.97	\N	2026-04-06	La orden pasó al área de producción.	2026-04-03 15:15:00
222	62	Producción iniciada	Parcial	Parcial	Pendiente	En producción	446.49	446.49	0.00	446.49	446.49	\N	2026-04-08	La orden pasó al área de producción.	2026-04-04 16:15:00
223	63	Producción iniciada	Parcial	Parcial	Pendiente	En producción	293.68	293.68	0.00	293.68	293.68	\N	2026-04-10	La orden pasó al área de producción.	2026-04-05 10:15:00
224	64	Producción iniciada	Parcial	Parcial	Pendiente	En producción	518.65	518.65	0.00	518.65	518.65	\N	2026-04-12	La orden pasó al área de producción.	2026-04-06 11:15:00
225	65	Producción iniciada	Parcial	Parcial	Pendiente	En producción	753.40	753.40	0.00	753.40	753.40	\N	2026-04-09	La orden pasó al área de producción.	2026-04-07 12:15:00
226	66	Producción iniciada	Parcial	Parcial	Pendiente	En producción	572.99	572.99	0.00	572.99	572.99	\N	2026-04-11	La orden pasó al área de producción.	2026-04-08 13:15:00
227	67	Producción iniciada	Parcial	Parcial	Pendiente	En producción	366.13	366.13	0.00	366.13	366.13	\N	2026-04-13	La orden pasó al área de producción.	2026-04-09 14:15:00
228	68	Producción iniciada	Parcial	Parcial	Pendiente	En producción	633.65	633.65	0.00	633.65	633.65	\N	2026-04-15	La orden pasó al área de producción.	2026-04-10 15:15:00
229	69	Producción iniciada	Parcial	Parcial	Pendiente	En producción	931.07	931.07	0.00	931.07	931.07	\N	2026-04-17	La orden pasó al área de producción.	2026-04-11 16:15:00
230	70	Producción iniciada	Parcial	Parcial	Pendiente	En producción	679.37	679.37	0.00	679.37	679.37	\N	2026-04-14	La orden pasó al área de producción.	2026-04-12 10:15:00
231	71	Producción iniciada	Parcial	Parcial	Pendiente	En producción	438.58	438.58	0.00	438.58	438.58	\N	2026-04-16	La orden pasó al área de producción.	2026-04-13 11:15:00
232	72	Producción iniciada	Parcial	Parcial	Pendiente	En producción	760.15	760.15	0.00	760.15	760.15	\N	2026-04-18	La orden pasó al área de producción.	2026-04-14 12:15:00
233	73	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1105.87	1105.87	0.00	1105.87	1105.87	\N	2026-04-20	La orden pasó al área de producción.	2026-04-15 13:15:00
234	74	Producción iniciada	Parcial	Parcial	Pendiente	En producción	814.49	814.49	0.00	814.49	814.49	\N	2026-04-22	La orden pasó al área de producción.	2026-04-16 14:15:00
235	75	Producción iniciada	Parcial	Parcial	Pendiente	En producción	490.91	490.91	0.00	490.91	490.91	\N	2026-04-19	La orden pasó al área de producción.	2026-04-17 15:15:00
236	76	Producción iniciada	Parcial	Parcial	Pendiente	En producción	875.15	875.15	0.00	875.15	875.15	\N	2026-04-21	La orden pasó al área de producción.	2026-04-18 16:15:00
237	77	Producción iniciada	Parcial	Parcial	Pendiente	En producción	1274.92	1274.92	0.00	1274.92	1274.92	\N	2026-04-23	La orden pasó al área de producción.	2026-04-19 10:15:00
238	78	Producción iniciada	Parcial	Parcial	Pendiente	En producción	935.24	935.24	0.00	935.24	935.24	\N	2026-04-25	La orden pasó al área de producción.	2026-04-20 11:15:00
239	79	Producción iniciada	Parcial	Parcial	Pendiente	En producción	583.48	583.48	0.00	583.48	583.48	\N	2026-04-27	La orden pasó al área de producción.	2026-04-21 12:15:00
240	80	Producción iniciada	Parcial	Parcial	Pendiente	En producción	987.28	987.28	0.00	987.28	987.28	\N	2026-04-24	La orden pasó al área de producción.	2026-04-22 13:15:00
241	3	Segundo abono registrado	Parcial	Parcial	En producción	En producción	293.68	440.52	146.84	293.68	146.84	\N	2026-02-09	El cliente realizó un segundo abono sobre la factura.	2026-02-05 12:15:00
242	6	Segundo abono registrado	Parcial	Parcial	En producción	En producción	567.24	850.86	283.62	567.24	283.62	\N	2026-02-10	El cliente realizó un segundo abono sobre la factura.	2026-02-08 15:15:00
243	9	Segundo abono registrado	Parcial	Parcial	En producción	En producción	936.82	1405.23	468.41	936.82	468.41	\N	2026-02-16	El cliente realizó un segundo abono sobre la factura.	2026-02-11 11:15:00
244	12	Segundo abono registrado	Parcial	Parcial	En producción	En producción	754.40	1131.60	377.20	754.40	377.20	\N	2026-02-17	El cliente realizó un segundo abono sobre la factura.	2026-02-14 14:15:00
245	15	Segundo abono registrado	Parcial	Parcial	En producción	En producción	496.66	744.98	248.33	496.66	248.33	\N	2026-02-18	El cliente realizó un segundo abono sobre la factura.	2026-02-17 10:15:00
246	18	Segundo abono registrado	Parcial	Parcial	En producción	En producción	935.24	1402.86	467.62	935.24	467.62	\N	2026-02-24	El cliente realizó un segundo abono sobre la factura.	2026-02-20 13:15:00
247	21	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1443.97	2165.96	721.99	1443.97	721.99	\N	2026-02-25	El cliente realizó un segundo abono sobre la factura.	2026-02-23 09:15:00
248	24	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1122.40	1683.60	561.20	1122.40	561.20	\N	2026-03-03	El cliente realizó un segundo abono sobre la factura.	2026-02-26 12:15:00
249	27	Segundo abono registrado	Parcial	Parcial	En producción	En producción	722.63	1083.95	361.32	722.63	361.32	\N	2026-03-04	El cliente realizó un segundo abono sobre la factura.	2026-03-01 15:15:00
250	30	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1283.12	1924.67	641.56	1283.12	641.56	\N	2026-03-05	El cliente realizó un segundo abono sobre la factura.	2026-03-04 11:15:00
251	33	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1945.37	2918.06	972.69	1945.37	972.69	\N	2026-03-11	El cliente realizó un segundo abono sobre la factura.	2026-03-07 14:15:00
252	36	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1484.65	2226.98	742.33	1484.65	742.33	\N	2026-03-12	El cliente realizó un segundo abono sobre la factura.	2026-03-10 10:15:00
253	39	Segundo abono registrado	Parcial	Parcial	En producción	En producción	945.73	1418.60	472.87	945.73	472.87	\N	2026-03-18	El cliente realizó un segundo abono sobre la factura.	2026-03-13 13:15:00
254	42	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1659.74	2489.61	829.87	1659.74	829.87	\N	2026-03-19	El cliente realizó un segundo abono sobre la factura.	2026-03-16 09:15:00
255	45	Segundo abono registrado	Parcial	Parcial	En producción	En producción	2443.90	3665.84	1221.95	2443.90	1221.95	\N	2026-03-20	El cliente realizó un segundo abono sobre la factura.	2026-03-19 12:15:00
256	48	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1841.15	2761.73	920.58	1841.15	920.58	\N	2026-03-26	El cliente realizó un segundo abono sobre la factura.	2026-03-22 15:15:00
257	51	Segundo abono registrado	Parcial	Parcial	En producción	En producción	1163.08	1744.62	581.54	1163.08	581.54	\N	2026-03-27	El cliente realizó un segundo abono sobre la factura.	2026-03-25 11:15:00
258	54	Segundo abono registrado	Parcial	Parcial	En producción	En producción	2016.24	3024.36	1008.12	2016.24	1008.12	\N	2026-04-02	El cliente realizó un segundo abono sobre la factura.	2026-03-28 14:15:00
259	57	Segundo abono registrado	Parcial	Parcial	En producción	En producción	2965.42	4448.13	1482.71	2965.42	1482.71	\N	2026-04-03	El cliente realizó un segundo abono sobre la factura.	2026-03-31 10:15:00
260	60	Segundo abono registrado	Parcial	Parcial	En producción	En producción	383.53	575.29	191.76	383.53	191.76	\N	2026-04-04	El cliente realizó un segundo abono sobre la factura.	2026-04-03 13:15:00
261	63	Segundo abono registrado	Parcial	Parcial	En producción	En producción	293.68	440.52	146.84	293.68	146.84	\N	2026-04-10	El cliente realizó un segundo abono sobre la factura.	2026-04-06 09:15:00
262	66	Segundo abono registrado	Parcial	Parcial	En producción	En producción	572.99	859.49	286.50	572.99	286.50	\N	2026-04-11	El cliente realizó un segundo abono sobre la factura.	2026-04-09 12:15:00
263	69	Segundo abono registrado	Parcial	Parcial	En producción	En producción	931.07	1396.61	465.54	931.07	465.54	\N	2026-04-17	El cliente realizó un segundo abono sobre la factura.	2026-04-12 15:15:00
264	72	Segundo abono registrado	Parcial	Parcial	En producción	En producción	760.15	1140.23	380.08	760.15	380.08	\N	2026-04-18	El cliente realizó un segundo abono sobre la factura.	2026-04-15 11:15:00
265	75	Segundo abono registrado	Parcial	Parcial	En producción	En producción	490.91	736.36	245.45	490.91	245.45	\N	2026-04-19	El cliente realizó un segundo abono sobre la factura.	2026-04-18 14:15:00
266	78	Segundo abono registrado	Parcial	Parcial	En producción	En producción	935.24	1402.86	467.62	935.24	467.62	\N	2026-04-25	El cliente realizó un segundo abono sobre la factura.	2026-04-21 10:15:00
267	3	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	440.52	440.52	0.00	146.84	146.84	\N	2026-02-09	El pedido fue finalizado y quedó listo para entrega.	2026-02-09 00:00:00
268	6	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	850.86	850.86	0.00	283.62	283.62	\N	2026-02-10	El pedido fue finalizado y quedó listo para entrega.	2026-02-10 00:00:00
269	9	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1405.23	1405.23	0.00	468.41	468.41	\N	2026-02-16	El pedido fue finalizado y quedó listo para entrega.	2026-02-16 00:00:00
270	12	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1131.60	1131.60	0.00	377.20	377.20	\N	2026-02-17	El pedido fue finalizado y quedó listo para entrega.	2026-02-17 00:00:00
271	15	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	744.98	744.98	0.00	248.33	248.33	\N	2026-02-18	El pedido fue finalizado y quedó listo para entrega.	2026-02-18 00:00:00
272	18	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1402.86	1402.86	0.00	467.62	467.62	\N	2026-02-24	El pedido fue finalizado y quedó listo para entrega.	2026-02-24 00:00:00
273	21	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	2165.96	2165.96	0.00	721.99	721.99	\N	2026-02-25	El pedido fue finalizado y quedó listo para entrega.	2026-02-25 00:00:00
274	24	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1683.60	1683.60	0.00	561.20	561.20	\N	2026-03-03	El pedido fue finalizado y quedó listo para entrega.	2026-03-03 00:00:00
275	27	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1083.95	1083.95	0.00	361.32	361.32	\N	2026-03-04	El pedido fue finalizado y quedó listo para entrega.	2026-03-04 00:00:00
276	30	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1924.67	1924.67	0.00	641.56	641.56	\N	2026-03-05	El pedido fue finalizado y quedó listo para entrega.	2026-03-05 00:00:00
277	33	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	2918.06	2918.06	0.00	972.69	972.69	\N	2026-03-11	El pedido fue finalizado y quedó listo para entrega.	2026-03-11 00:00:00
278	36	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	2226.98	2226.98	0.00	742.33	742.33	\N	2026-03-12	El pedido fue finalizado y quedó listo para entrega.	2026-03-12 00:00:00
279	39	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1418.60	1418.60	0.00	472.87	472.87	\N	2026-03-18	El pedido fue finalizado y quedó listo para entrega.	2026-03-18 00:00:00
280	42	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	2489.61	2489.61	0.00	829.87	829.87	\N	2026-03-19	El pedido fue finalizado y quedó listo para entrega.	2026-03-19 00:00:00
281	45	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	3665.84	3665.84	0.00	1221.95	1221.95	\N	2026-03-20	El pedido fue finalizado y quedó listo para entrega.	2026-03-20 00:00:00
282	48	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	2761.73	2761.73	0.00	920.58	920.58	\N	2026-03-26	El pedido fue finalizado y quedó listo para entrega.	2026-03-26 00:00:00
283	51	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1744.62	1744.62	0.00	581.54	581.54	\N	2026-03-27	El pedido fue finalizado y quedó listo para entrega.	2026-03-27 00:00:00
284	54	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	3024.36	3024.36	0.00	1008.12	1008.12	\N	2026-04-02	El pedido fue finalizado y quedó listo para entrega.	2026-04-02 00:00:00
285	57	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	4448.13	4448.13	0.00	1482.71	1482.71	\N	2026-04-03	El pedido fue finalizado y quedó listo para entrega.	2026-04-03 00:00:00
286	60	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	575.29	575.29	0.00	191.76	191.76	\N	2026-04-04	El pedido fue finalizado y quedó listo para entrega.	2026-04-04 00:00:00
287	63	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	440.52	440.52	0.00	146.84	146.84	\N	2026-04-10	El pedido fue finalizado y quedó listo para entrega.	2026-04-10 00:00:00
288	66	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	859.49	859.49	0.00	286.50	286.50	\N	2026-04-11	El pedido fue finalizado y quedó listo para entrega.	2026-04-11 00:00:00
289	69	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1396.61	1396.61	0.00	465.54	465.54	\N	2026-04-17	El pedido fue finalizado y quedó listo para entrega.	2026-04-17 00:00:00
290	72	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1140.23	1140.23	0.00	380.08	380.08	\N	2026-04-18	El pedido fue finalizado y quedó listo para entrega.	2026-04-18 00:00:00
291	75	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	736.36	736.36	0.00	245.45	245.45	\N	2026-04-19	El pedido fue finalizado y quedó listo para entrega.	2026-04-19 00:00:00
292	78	Pedido listo para entregar	Parcial	Parcial	En producción	Lista para entregar	1402.86	1402.86	0.00	467.62	467.62	\N	2026-04-25	El pedido fue finalizado y quedó listo para entrega.	2026-04-25 00:00:00
293	5	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	747.65	747.65	0.00	747.65	747.65	\N	2026-02-08	El cliente canceló después de la fecha estimada de entrega.	2026-02-08 14:15:00
294	10	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	679.37	679.37	0.00	679.37	679.37	\N	2026-02-13	El cliente canceló después de la fecha estimada de entrega.	2026-02-13 12:15:00
295	15	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	496.66	496.66	0.00	496.66	496.66	\N	2026-02-18	El cliente canceló después de la fecha estimada de entrega.	2026-02-18 10:15:00
296	20	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	981.53	981.53	0.00	981.53	981.53	\N	2026-02-23	El cliente canceló después de la fecha estimada de entrega.	2026-02-23 15:15:00
297	25	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	1598.65	1598.65	0.00	1598.65	1598.65	\N	2026-02-28	El cliente canceló después de la fecha estimada de entrega.	2026-02-28 13:15:00
298	30	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	1283.12	1283.12	0.00	1283.12	1283.12	\N	2026-03-05	El cliente canceló después de la fecha estimada de entrega.	2026-03-05 11:15:00
299	35	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	858.91	858.91	0.00	858.91	858.91	\N	2026-03-10	El cliente canceló después de la fecha estimada de entrega.	2026-03-10 09:15:00
300	40	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	1585.28	1585.28	0.00	1585.28	1585.28	\N	2026-03-15	El cliente canceló después de la fecha estimada de entrega.	2026-03-15 14:15:00
301	45	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	2443.90	2443.90	0.00	2443.90	2443.90	\N	2026-03-20	El cliente canceló después de la fecha estimada de entrega.	2026-03-20 12:15:00
302	50	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	1886.87	1886.87	0.00	1886.87	1886.87	\N	2026-03-25	El cliente canceló después de la fecha estimada de entrega.	2026-03-25 10:15:00
303	55	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	1215.41	1215.41	0.00	1215.41	1215.41	\N	2026-03-30	El cliente canceló después de la fecha estimada de entrega.	2026-03-30 15:15:00
304	60	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	383.53	383.53	0.00	383.53	383.53	\N	2026-04-04	El cliente canceló después de la fecha estimada de entrega.	2026-04-04 13:15:00
305	65	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	753.40	753.40	0.00	753.40	753.40	\N	2026-04-09	El cliente canceló después de la fecha estimada de entrega.	2026-04-09 11:15:00
306	70	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	679.37	679.37	0.00	679.37	679.37	\N	2026-04-14	El cliente canceló después de la fecha estimada de entrega.	2026-04-14 09:15:00
307	75	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	490.91	490.91	0.00	490.91	490.91	\N	2026-04-19	El cliente canceló después de la fecha estimada de entrega.	2026-04-19 14:15:00
308	80	Factura cancelada por el cliente	Parcial	Parcial	En producción	Cancelada	987.28	987.28	0.00	987.28	987.28	\N	2026-04-24	El cliente canceló después de la fecha estimada de entrega.	2026-04-24 12:15:00
309	3	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	440.52	587.36	146.84	146.84	0.00	\N	2026-02-09	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-02-09 02:00:00
310	6	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	850.86	1134.48	283.62	283.62	0.00	\N	2026-02-10	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-02-10 02:00:00
311	9	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1405.23	1873.64	468.41	468.41	0.00	\N	2026-02-16	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-02-16 02:00:00
312	12	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1131.60	1508.80	377.20	377.20	0.00	\N	2026-02-17	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-02-17 02:00:00
313	18	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1402.86	1870.48	467.62	467.62	0.00	\N	2026-02-24	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-02-24 02:00:00
314	21	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	2165.96	2887.94	721.99	721.99	0.00	\N	2026-02-25	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-02-25 02:00:00
315	24	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1683.60	2244.80	561.20	561.20	0.00	\N	2026-03-03	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-03 02:00:00
316	27	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1083.95	1445.26	361.32	361.32	0.00	\N	2026-03-04	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-04 02:00:00
317	33	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	2918.06	3890.74	972.69	972.69	0.00	\N	2026-03-11	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-11 02:00:00
318	36	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	2226.98	2969.30	742.33	742.33	0.00	\N	2026-03-12	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-12 02:00:00
319	39	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1418.60	1891.46	472.87	472.87	0.00	\N	2026-03-18	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-18 02:00:00
320	42	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	2489.61	3319.48	829.87	829.87	0.00	\N	2026-03-19	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-19 02:00:00
321	48	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	2761.73	3682.30	920.58	920.58	0.00	\N	2026-03-26	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-26 02:00:00
322	51	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1744.62	2326.16	581.54	581.54	0.00	\N	2026-03-27	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-03-27 02:00:00
323	54	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	3024.36	4032.48	1008.12	1008.12	0.00	\N	2026-04-02	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-04-02 02:00:00
324	57	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	4448.13	5930.84	1482.71	1482.71	0.00	\N	2026-04-03	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-04-03 02:00:00
325	63	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	440.52	587.36	146.84	146.84	0.00	\N	2026-04-10	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-04-10 02:00:00
326	66	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	859.49	1145.98	286.50	286.50	0.00	\N	2026-04-11	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-04-11 02:00:00
327	69	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1396.61	1862.14	465.54	465.54	0.00	\N	2026-04-17	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-04-17 02:00:00
328	72	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1140.23	1520.30	380.08	380.08	0.00	\N	2026-04-18	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-04-18 02:00:00
329	78	Pago final y entrega	Parcial	Pagado	Lista para entregar	Entregada	1402.86	1870.48	467.62	467.62	0.00	\N	2026-04-25	El cliente canceló el saldo pendiente y el pedido fue entregado.	2026-04-25 02:00:00
330	11	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	877.16	877.16	0.00	0.00	0.00	2026-02-15	2026-02-15	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
331	12	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	1508.80	1508.80	0.00	0.00	0.00	2026-02-17	2026-02-17	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
332	13	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	2200.24	2200.24	0.00	0.00	0.00	2026-02-19	2026-02-19	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
333	14	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	1628.98	1628.98	0.00	0.00	0.00	2026-02-21	2026-02-21	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
334	15	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	993.31	993.31	0.00	0.00	0.00	2026-02-18	2026-02-18	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
335	16	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	1761.80	1761.80	0.00	0.00	0.00	2026-02-20	2026-02-20	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
336	17	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	2549.84	2549.84	0.00	0.00	0.00	2026-02-22	2026-02-22	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
337	18	Estado de producción actualizado	Pagado	Pagado	Entregada	Lista para entregar	1870.48	1870.48	0.00	0.00	0.00	2026-02-24	2026-02-24	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
338	19	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	1155.46	866.60	0.00	0.00	288.86	2026-02-26	2026-02-26	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
339	20	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	1963.05	1472.29	0.00	0.00	490.76	2026-02-23	2026-02-23	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
340	21	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	2887.94	2165.96	0.00	0.00	721.98	2026-02-25	2026-02-25	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
341	22	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	2111.98	1583.99	0.00	0.00	527.99	2026-02-27	2026-02-27	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
342	23	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	1311.86	983.90	0.00	0.00	327.96	2026-03-01	2026-03-01	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
343	24	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	2244.80	1683.60	0.00	0.00	561.20	2026-03-03	2026-03-03	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
344	25	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	3197.29	2397.97	0.00	0.00	799.32	2026-02-28	2026-02-28	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
345	26	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	2341.98	1756.49	0.00	0.00	585.49	2026-03-02	2026-03-02	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
346	27	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	1445.26	1083.95	0.00	0.00	361.31	2026-03-04	2026-03-04	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
347	28	Estado de producción actualizado	Pagado	Parcial	Entregada	En producción	2486.30	1864.73	0.00	0.00	621.57	2026-03-06	2026-03-06	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
348	29	Estado de producción actualizado	Pagado	Parcial	Entregada	Lista para entregar	3564.14	1782.07	0.00	0.00	1782.07	2026-03-08	2026-03-08	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
349	30	Estado de producción actualizado	Pagado	Parcial	Entregada	Lista para entregar	2566.23	1283.12	0.00	0.00	1283.11	2026-03-05	2026-03-05	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
350	31	Estado de producción actualizado	Pagado	Parcial	Entregada	Lista para entregar	1601.66	800.83	0.00	0.00	800.83	2026-03-07	2026-03-07	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
351	32	Estado de producción actualizado	Pagado	Parcial	Entregada	Lista para entregar	2727.80	1363.90	0.00	0.00	1363.90	2026-03-09	2026-03-09	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
352	33	Estado de producción actualizado	Pagado	Parcial	Entregada	Lista para entregar	3890.74	1945.37	0.00	0.00	1945.37	2026-03-11	2026-03-11	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
353	34	Estado de producción actualizado	Pagado	Parcial	Entregada	Lista para entregar	2824.98	1412.49	0.00	0.00	1412.49	2026-03-13	2026-03-13	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
354	35	Estado de producción actualizado	Pagado	Parcial	Entregada	Lista para entregar	1717.81	858.91	0.00	0.00	858.90	2026-03-10	2026-03-10	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
355	39	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	945.73	0.00	0.00	945.73	1891.46	2026-03-18	2026-03-18	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
356	40	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	1585.28	0.00	0.00	1585.28	3170.55	2026-03-15	2026-03-15	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
357	41	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	2283.47	0.00	0.00	2283.47	4566.94	2026-03-17	2026-03-17	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
358	42	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	1659.74	0.00	0.00	1659.74	3319.48	2026-03-19	2026-03-19	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
359	43	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	1018.18	0.00	0.00	1018.18	2036.36	2026-03-21	2026-03-21	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
360	44	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	1726.15	0.00	0.00	1726.15	3452.30	2026-03-23	2026-03-23	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
361	45	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	2443.90	0.00	0.00	2443.90	4887.79	2026-03-20	2026-03-20	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
362	46	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	1780.49	0.00	0.00	1780.49	3560.98	2026-03-22	2026-03-22	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
363	47	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	1084.88	0.00	0.00	1084.88	2169.76	2026-03-24	2026-03-24	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
364	48	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	En producción	1841.15	0.00	0.00	1841.15	3682.30	2026-03-26	2026-03-26	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
365	49	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	Pendiente	2627.32	0.00	0.00	2627.32	5254.64	2026-03-28	2026-03-28	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
366	50	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	Pendiente	1886.87	0.00	0.00	1886.87	3773.73	2026-03-25	2026-03-25	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
367	51	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	Pendiente	1163.08	0.00	0.00	1163.08	2326.16	2026-03-27	2026-03-27	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
368	52	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	Pendiente	1967.65	0.00	0.00	1967.65	3935.30	2026-03-29	2026-03-29	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
369	53	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	Pendiente	2796.37	0.00	0.00	2796.37	5592.74	2026-03-31	2026-03-31	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
370	54	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	Pendiente	2016.24	0.00	0.00	2016.24	4032.48	2026-04-02	2026-04-02	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
371	55	Estado de producción actualizado	Parcial	Pendiente	Lista para entregar	Pendiente	1215.41	0.00	0.00	1215.41	2430.81	2026-03-30	2026-03-30	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
372	56	Estado de producción actualizado	Parcial	Pendiente	En producción	Pendiente	2088.40	0.00	0.00	2088.40	4176.80	2026-04-01	2026-04-01	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
373	57	Factura cancelada	Parcial	Parcial	En producción	Cancelada	2965.42	2075.79	0.00	2965.42	3855.05	2026-04-03	2026-04-03	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
374	58	Factura cancelada	Parcial	Parcial	En producción	Cancelada	2142.74	1499.92	0.00	2142.74	2785.56	2026-04-05	2026-04-05	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
375	59	Factura cancelada	Parcial	Parcial	En producción	Cancelada	1307.98	915.59	0.00	1307.98	1700.37	2026-04-07	2026-04-07	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
376	60	Factura cancelada	Parcial	Parcial	En producción	Cancelada	383.53	268.47	0.00	383.53	498.58	2026-04-04	2026-04-04	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
377	61	Factura cancelada	Parcial	Parcial	En producción	Cancelada	592.97	415.08	0.00	592.97	770.86	2026-04-06	2026-04-06	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
378	62	Factura cancelada	Parcial	Parcial	En producción	Cancelada	446.49	312.54	0.00	446.49	580.44	2026-04-08	2026-04-08	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
379	63	Factura cancelada	Parcial	Pendiente	En producción	Cancelada	293.68	0.00	0.00	293.68	587.36	2026-04-10	2026-04-10	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
380	64	Factura cancelada	Parcial	Pendiente	En producción	Cancelada	518.65	0.00	0.00	518.65	1037.30	2026-04-12	2026-04-12	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
381	65	Factura cancelada	Parcial	Pendiente	En producción	Cancelada	753.40	0.00	0.00	753.40	1506.79	2026-04-09	2026-04-09	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
382	66	Factura cancelada	Pendiente	Pendiente	En producción	Cancelada	0.00	0.00	0.00	1145.98	1145.98	2026-04-11	2026-04-11	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
383	67	Pago actualizado	Pendiente	Pagado	En producción	En producción	0.00	732.26	732.26	732.26	0.00	2026-04-13	2026-04-13	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
384	68	Pago actualizado	Pendiente	Pagado	En producción	En producción	0.00	1267.30	1267.30	1267.30	0.00	2026-04-15	2026-04-15	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
385	69	Pago actualizado	Pendiente	Pagado	En producción	En producción	0.00	1862.14	1862.14	1862.14	0.00	2026-04-17	2026-04-17	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
386	70	Pago actualizado	Pendiente	Pagado	En producción	En producción	0.00	1358.73	1358.73	1358.73	0.00	2026-04-14	2026-04-14	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
387	71	Estado de producción actualizado	Pendiente	Parcial	Pendiente	Entregada	0.00	789.44	789.44	877.16	87.72	2026-04-16	2026-04-16	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
388	72	Estado de producción actualizado	Pendiente	Parcial	Pendiente	Entregada	0.00	1368.27	1368.27	1520.30	152.03	2026-04-18	2026-04-18	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
389	73	Estado de producción actualizado	Pendiente	Parcial	Pendiente	Entregada	0.00	1990.57	1990.57	2211.74	221.17	2026-04-20	2026-04-20	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
390	74	Estado de producción actualizado	Pendiente	Parcial	Pendiente	Entregada	0.00	1466.08	1466.08	1628.98	162.90	2026-04-22	2026-04-22	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
391	75	Estado de producción actualizado	Pendiente	Parcial	Pendiente	Entregada	0.00	883.63	883.63	981.81	98.18	2026-04-19	2026-04-19	Cambio registrado automáticamente por el sistema.	2026-05-12 20:46:35.442283
392	76	Factura cancelada	Pendiente	Pagado	Pendiente	Cancelada	0.00	1750.30	1750.30	1750.30	0.00	2026-04-21	2026-04-21	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
393	77	Factura cancelada	Pendiente	Pagado	Pendiente	Cancelada	0.00	2549.84	2549.84	2549.84	0.00	2026-04-23	2026-04-23	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
394	78	Factura cancelada	Pendiente	Pagado	Pendiente	Cancelada	0.00	1870.48	1870.48	1870.48	0.00	2026-04-25	2026-04-25	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
395	79	Factura cancelada	Pendiente	Pagado	Pendiente	Cancelada	0.00	1166.96	1166.96	1166.96	0.00	2026-04-27	2026-04-27	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
396	80	Factura cancelada	Pendiente	Pagado	Pendiente	Cancelada	0.00	1974.55	1974.55	1974.55	0.00	2026-04-24	2026-04-24	El cliente canceló después de la fecha estimada de entrega.	2026-05-12 20:46:35.442283
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
-- Name: factura_estado_historial_id_historial_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.factura_estado_historial_id_historial_seq', 396, true);


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
-- Name: factura_estado_historial factura_estado_historial_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura_estado_historial
    ADD CONSTRAINT factura_estado_historial_pkey PRIMARY KEY (id_historial);


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
-- Name: idx_auditoria_accion_fecha; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_auditoria_accion_fecha ON public.auditoria USING btree (accion, fecha DESC);


--
-- Name: idx_auditoria_datos_anteriores_gin; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_auditoria_datos_anteriores_gin ON public.auditoria USING gin (datos_anteriores);


--
-- Name: idx_auditoria_registro_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_auditoria_registro_id ON public.auditoria USING btree (registro_id);


--
-- Name: idx_auditoria_tabla_afectada; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_auditoria_tabla_afectada ON public.auditoria USING btree (tabla_afectada);


--
-- Name: idx_factura_estado_historial_factura; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_factura_estado_historial_factura ON public.factura_estado_historial USING btree (id_factura);


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
-- Name: factura trg_factura_estado_historial; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_factura_estado_historial AFTER INSERT OR UPDATE ON public.factura FOR EACH ROW EXECUTE FUNCTION public.registrar_historial_estado_factura();


--
-- Name: factura_estado_historial factura_estado_historial_id_factura_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.factura_estado_historial
    ADD CONSTRAINT factura_estado_historial_id_factura_fkey FOREIGN KEY (id_factura) REFERENCES public.factura(id_factura) ON DELETE CASCADE;


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

\unrestrict Sri9v3gsI5fHli5XqYDmv8FUYf4tdYBuUK0FCyTpXs9Y1d2KlWQeaA0uofDIuyx

