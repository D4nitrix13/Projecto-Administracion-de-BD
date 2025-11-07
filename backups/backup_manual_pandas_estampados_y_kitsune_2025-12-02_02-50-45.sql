--
-- PostgreSQL database dump
--

\restrict RHD5aP8xqfbLS69ZKlhPYBg6KjGQjdLgUhPSFYwQNCucRZ5UMbJzt5ufhx1GqR2

-- Dumped from database version 18.1 (Debian 18.1-1.pgdg13+2)
-- Dumped by pg_dump version 18.1 (Ubuntu 18.1-1.pgdg24.04+2)

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

SET default_tablespace = '';

SET default_table_access_method = heap;

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
\.


--
-- Data for Name: cliente; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cliente (id_cliente, nombres, apellidos, telefono, direccion, identificacion, tipo_cliente) FROM stdin;
1	Juan	Lopez	8888-2222	Managua	001-110998-0001M	Detallista
2	Ana	Martínez	7777-3333	Masaya	002-210598-0044K	Detallista
3	Comercial	Ruiz S.A	2266-9877	Granada	J123456789	Mayorista
4	Karla	González	8855-1144	León	003-090599-0099L	Detallista
5	Jhossep	Ramos	8877-6655	Carazo	004-200800-0055P	Detallista
6	Impresiones	Del Norte	2250-7788	Estelí	J987654321	Mayorista
7	María	Sáenz	8666-3344	Managua	005-101001-0003M	Detallista
8	Carlos	Blandón	8744-2233	Masaya	006-020202-0004H	Detallista
9	Studio	Creativo Luna	2225-6633	León	J112233445	Mayorista
10	Mario	Hernández	8787-1212	Granada	007-030303-0005V	Detallista
11	Lucía	Pérez	8833-5599	Managua	008-040404-0006P	Detallista
12	Tienda	Colores S.A	2233-8899	Managua	J556677889	Mayorista
13	Kevin	Castillo	8811-7722	Masaya	009-050505-0007C	Detallista
14	Paola	Mendoza	8822-9933	Carazo	010-060606-0008R	Detallista
15	Diseños	Urbanos	2277-4455	León	J667788990	Mayorista
16	Cliente	Fugaz	\N	\N	FUGAZ	Detallista
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
23	13	4	3	180.00	23.00	517.00
24	13	11	3	130.00	0.00	390.00
\.


--
-- Data for Name: factura; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.factura (id_factura, fecha, id_cliente, id_usuario, id_seccion, subtotal, descuento, impuesto, total, tipo_cliente_venta, nombre_cliente_fugaz) FROM stdin;
1	2025-12-02 06:53:52.251083	1	4	1	500.00	0.00	75.00	575.00	Habitual	\N
2	2025-12-02 06:53:52.251083	2	5	2	840.00	40.00	120.00	920.00	Habitual	\N
3	2025-12-02 06:53:52.251083	3	1	1	1500.00	150.00	225.00	1575.00	Habitual	\N
4	2025-12-02 06:53:52.251083	4	5	2	300.00	0.00	45.00	345.00	Habitual	\N
5	2025-12-02 06:53:52.251083	5	21	2	960.00	60.00	135.00	1035.00	Habitual	\N
6	2025-12-02 06:53:52.251083	6	26	1	3200.00	320.00	432.00	3312.00	Habitual	\N
7	2025-12-02 06:53:52.251083	7	22	2	450.00	0.00	67.50	517.50	Habitual	\N
8	2025-12-02 06:53:52.251083	8	23	1	780.00	30.00	112.50	862.50	Habitual	\N
9	2025-12-02 06:53:52.251083	9	27	1	2100.00	210.00	283.50	2173.50	Habitual	\N
10	2025-12-02 06:53:52.251083	10	28	2	650.00	0.00	97.50	747.50	Habitual	\N
11	2025-12-02 06:53:52.251083	11	29	1	920.00	50.00	130.50	1000.50	Habitual	\N
12	2025-12-02 06:53:52.251083	12	1	2	1450.00	80.00	205.50	1575.50	Habitual	\N
13	2025-12-02 08:14:25.631986	2	1	2	930.00	43.00	133.05	1020.05	Habitual	\N
\.


--
-- Data for Name: producto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.producto (id_producto, codigo, nombre, descripcion, imagen, id_categoria, id_proveedor, precio_compra, precio_venta, stock) FROM stdin;
1	P001	Camiseta Negra Premium	Camiseta de algodón negro premium, ideal para estampados full color y uso diario.	prod_d67e3785273607d89e61a401.png	1	1	120.00	250.00	55
2	P002	Camiseta Blanca Clásica	Camiseta blanca clásica, perfecta para sublimación y estampados personalizados.	prod_69ab914e50de9c2ab70f17e2.jpg	1	1	115.00	240.00	70
3	P003	Hoodie Oversize Negro	Hoodie oversize en color negro, tela gruesa y suave, ideal para colección urbana.	prod_3358337933ce52ca1d0d187f.jpg	2	1	350.00	550.00	20
5	P005	Llavero Acrílico Panda	Llavero acrílico con forma de panda, resistente y ligero para uso diario.	prod_67817b473371f51443a44144.jpg	4	3	20.00	60.00	150
6	P006	Sticker Holográfico Kitsune	Sticker holográfico con diseño de kitsune, acabado brillante y resistente al agua.	prod_5b3fca06c807b9fed7369b0e.jpg	6	4	5.00	25.00	300
7	P007	Bolso Tote Reforzado	Bolso tote de lona reforzada, ideal para compras, estudio o uso promocional.	prod_5864c2b76949a1ffd7fb6bbb.jpg	7	1	80.00	160.00	35
8	P008	Termo de Acero Panda	Termo de acero inoxidable con diseño de panda, conserva la temperatura por horas.	prod_f5368854e9de4fe5d5ef70c1.jpg	8	2	160.00	300.00	25
9	P009	Camiseta Roja Edición Limitada	Camiseta roja de edición limitada, tela suave y corte unisex para colecciones especiales.	prod_977042d6b3d3bdc28500a0b5.jpg	1	1	135.00	270.00	40
10	P010	Hoodie Gris con Cierre	Hoodie gris con cierre frontal y capucha, ideal para bordado o impresión frontal.	prod_8b600c2980bacbe62c44cbfd.jpg	2	1	320.00	520.00	18
12	P012	Gorra Snapback Panda	Gorra estilo snapback con logo de panda, ajustable y lista para personalización.	prod_3bc17b8d1377a4826a2d8912.jpg	4	3	90.00	180.00	25
13	P013	Vinilo Decorativo Pared	Vinilo decorativo para pared, fácil de colocar y remover, ideal para habitaciones y oficinas.	prod_e155b0fa66e83067205236e4.jpg	5	4	70.00	190.00	60
14	P014	Pack Stickers Surtidos	Pack de stickers surtidos con diferentes diseños de Panda y Kitsune, acabado mate.	prod_bbf90800dd3fd1f24deb27b4.webp	6	4	3.00	15.00	500
15	P015	Termo Kitsune con Luz LED	Termo Kitsune con tapa iluminada LED, ideal para regalos y colecciones especiales.	prod_bd9ae845c714f7f64699fb75.jpg	8	2	220.00	380.00	15
4	P004	Taza Mágica Full Color	Taza mágica que revela el diseño al contacto con agua caliente, impresión full color.	prod_77799003da6531f02abb08fb.jpg	3	2	90.00	180.00	37
11	P011	Taza Blanca Clásica 11oz	Taza blanca de 11oz lista para sublimación, perfecta para pedidos al por mayor.	prod_ee0704d79c323b08c7071c3e.jpg	3	2	60.00	130.00	77
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
1	Leonel Messi	leonel.messi@admin.pandakitsune.com	$2y$10$RFdBKmcjh9F9hFGB5PYM5uYRGytk1wcKBz1xBtFfF/bp3p6kfjUbC	1	\N
7	Carla Bermúdez	carla.bermudez@kitsune.com	$2y$12$ms6hACDwIsyxkdfDYlJexefz1ZT2x3ji7pcKeC0HyboUU9J7ifxgi	3	2
11	Wilmer Ruiz	wilmer.ruiz@kitsune.com	$2y$12$UVGcd3Nx5WN6balNnQUTZuKiEIz2c3C3Plj02E/NPFO8uuV6gchda	3	2
15	María Fernández	maria.fernandez@kitsune.com	$2y$12$NmOhXlKgjbQaD873duMCLe5sFBb1vazssqptJk205XStju1rXvtWi	3	2
26	Laura Castillo	laura.castillo@admin.pandakitsune.com	$2y$12$CB/GqGCH8j4gEjorUXsdnOpfkerjI293aYhNnaVkimflikSV5bbT.	1	\N
27	Óscar Mejía	oscar.mejia@admin.pandakitsune.com	$2y$12$YoQt4rtdfKejCojrKTpmheZ8v8MXnfDwXWEWb8IYBZeNUL4iqfNfC	1	\N
2	Daniel Pérez	daniel.perez@kitsune.com	$2y$12$Cuy1umkJSfW.KT663X3yMuHtSToKuy88VwU0XKvht6ue/sZo5NEAG	2	2
3	Jeremy Pérez	jeremy.perez@kitsune.com	$2y$12$FE0wsyguHQsX4JCnhoAPpelNFTZJqMWPI0W62khH8VJIhhVqJekyu	2	2
8	Jhossep Ramos	jhossep.ramos@kitsune.com	$2y$12$0dTVZfdHzCUG09tBtenTQuMOjHPchANMWBOsHQeITOXRvoy5BU8Pq	2	2
9	Diego Torres	diego.torres@kitsune.com	$2y$12$Dn0g5aHIxHqjZro/Cr3xI.za7ggzInqKbkdZoKiM83tctAuweldUC	2	2
18	Carlos Núñez	carlos.nunez@kitsune.com	$2y$12$872fFb6cpYjHaZUPBhYpIOUXoOO6yDShH2Ap77q.BUtj0jtqMnbW2	2	2
19	Mónica Larios	monica.larios@kitsune.com	$2y$12$PGv1HK.Tu5G9udNlZ6qO4eKup/BTB/QwnzTht.eTBYgkOrF4P5fTW	2	2
20	Esteban Rodríguez	esteban.rodriguez@kitsune.com	$2y$12$JGRU/Xqn4bIxiscXkhDD6eTG9sMA9fizdVMZAvJ9N0i4FbDP5/b7C	2	2
30	Eduardo Molina	eduardo.molina@kitsune.com	$2y$12$MXfiIgRFUAgdW/0acIOsaOWHWt4zyng3GW6LJKXFyrxVbyq8K.aWG	2	2
5	Sofía Gómez	sofia.gomez@kitsune.com	$2y$12$HO5syWmHZgmjKV1hSBAXMeZRNz1r8Ce1oMMuug6MdguwCy7/740h6	3	2
16	Josefina Rivas	josefina.rivas@kitsune.com	$2y$12$eQJZJhA/z6bjOD3RT8tAs.wB59SK2D2qNmRMwRmhDqT2ZAjS8IT26	3	2
17	Roberto Gutiérrez	roberto.gutierrez@kitsune.com	$2y$12$lT3MqDxErkRwRcPy3xxPk.bLG9yS6ED/we4I79iv7jnUp4haBQ0wW	3	2
21	Lucía Herrera	lucia.herrera@kitsune.com	$2y$12$nGB5WHJPO.zg/7YpuTs0NuaWV8Q2v72HU2U4s7AJGYyOl8neDGlge	3	2
22	Brandon Morales	brandon.morales@kitsune.com	$2y$12$P9f0ji3ihKWsF//iURO/z.VFPNohw.zPZ6bjcmosnOr3A2w3FkmUG	3	2
29	Nidia Solís	nidia.solis@kitsune.com	$2y$12$UHpqsLXh6UB28jg8TS7MZeSk/uRCBXdl7An7OpWc7otk0ssabQ1HC	3	2
6	Luis Torres	luis.torres@panda.com	$2y$12$gcwTISi4Ssm.HFmYCiCmu.rDWqVhSVsAtI4VNk06PgDhnJ4yMq5i2	3	2
10	Karla Medina	karla.medina@panda.com	$2y$12$L59Re6YPm/NyaesLuT5VDOBzbmBLq5EvPAMKnKZI3Cyjz5.d6Edhq	3	2
12	Miguel Hernández	miguel.hernandez@panda.com	$2y$12$RudYHl9XcS10R9bPSE3XBuII..ckhk77XfJD5dSF9elcgoA5pg3G6	3	2
13	Paola López	paola.lopez@panda.com	$2y$12$ZBhT6UwudDMc/gcGhmf3sOwviyKc4tEtfSgik4cjnoBtrVjaj./Sy	3	2
14	Kevin Castillo	kevin.castillo@panda.com	$2y$12$LvrszuemNKKLdK52y7vsu.yhUV0Uciffu8/iV2LbRyTL6YjqLxOfu	3	2
23	Andrea Vega	andrea.vega@panda.com	$2y$12$lwdBwQdWOA1SKpbsAXIRbeQ6WiifMAkT6iEPeNgaKeXw88pbT.oc2	3	2
24	Sergio Mairena	sergio.mairena@panda.com	$2y$12$EokoIy.vdAUByq803ynWuOZlZvkZEe71PqG/4Hj7ZlxG9wnrwJU.G	3	2
25	Julia Campos	julia.campos@panda.com	$2y$12$Ql6klPEg3UYaNL7Pp5Q0Du//2ZtGm8FQ2S0NxcIBesSjNLifem4LK	3	2
28	Carmen Rojas	carmen.rojas@panda.com	$2y$12$ucPOIgrlvTNr1aAE/HVUCOkEvbXUf1UXkyeNGHoTK4keE09weJBEa	3	2
4	Andy Sánchez	andy.sanchez@panda.com	$2y$10$JuMEFqY7FkfZp6Be8dcovOLcpPPsYJYW5NktS3tu6lcQw1woHj3pi	3	2
\.


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categoria_id_categoria_seq', 8, true);


--
-- Name: cliente_id_cliente_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cliente_id_cliente_seq', 16, true);


--
-- Name: compra_id_compra_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.compra_id_compra_seq', 6, true);


--
-- Name: detallecompra_id_detalle_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detallecompra_id_detalle_seq', 11, true);


--
-- Name: detallefactura_id_detalle_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detallefactura_id_detalle_seq', 24, true);


--
-- Name: factura_id_factura_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.factura_id_factura_seq', 13, true);


--
-- Name: producto_id_producto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.producto_id_producto_seq', 15, true);


--
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.proveedor_id_proveedor_seq', 6, true);


--
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rol_id_rol_seq', 3, true);


--
-- Name: seccion_id_seccion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.seccion_id_seccion_seq', 2, true);


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuario_id_usuario_seq', 30, true);


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

\unrestrict RHD5aP8xqfbLS69ZKlhPYBg6KjGQjdLgUhPSFYwQNCucRZ5UMbJzt5ufhx1GqR2

