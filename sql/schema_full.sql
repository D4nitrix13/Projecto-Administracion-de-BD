CREATE TABLE auditoria (
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
CREATE TABLE categoria (
    id_categoria integer NOT NULL,
    nombre character varying(80) NOT NULL
);
CREATE TABLE cliente (
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

CREATE TABLE compra (
    id_compra integer NOT NULL,
    fecha timestamp without time zone DEFAULT now() NOT NULL,
    id_proveedor integer NOT NULL,
    id_usuario integer NOT NULL,
    total numeric(10,2) DEFAULT 0 NOT NULL
);

CREATE TABLE detallecompra (
    id_detalle integer NOT NULL,
    id_compra integer NOT NULL,
    id_producto integer NOT NULL,
    cantidad integer NOT NULL,
    costo_unitario numeric(10,2) NOT NULL,
    total_linea numeric(10,2) NOT NULL,
    CONSTRAINT detallecompra_cantidad_check CHECK ((cantidad > 0)),
    CONSTRAINT detallecompra_costo_unitario_check CHECK ((costo_unitario >= (0)::numeric))
);

CREATE TABLE detallefactura (
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
CREATE TABLE factura (
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
    CONSTRAINT chk_factura_estado_pago_valido CHECK (((estado_pago)::text = ANY (ARRAY[('Pendiente'::character varying)::text, ('Parcial'::character varying)::text, ('Pagado'::character varying)::text]))),
    CONSTRAINT chk_factura_estado_produccion_valido CHECK (((estado_produccion)::text = ANY (ARRAY[('Pendiente'::character varying)::text, ('En producción'::character varying)::text, ('Lista para entregar'::character varying)::text, ('Entregada'::character varying)::text, ('Cancelada'::character varying)::text]))),
    CONSTRAINT chk_factura_monto_pagado_no_negativo CHECK ((monto_pagado >= (0)::numeric)),
    CONSTRAINT chk_factura_porcentaje_pagado_rango CHECK (((porcentaje_pagado >= (0)::numeric) AND (porcentaje_pagado <= (100)::numeric))),
    CONSTRAINT chk_factura_saldo_pendiente_no_negativo CHECK ((saldo_pendiente >= (0)::numeric)),
    CONSTRAINT factura_tipo_cliente_venta_check CHECK (((tipo_cliente_venta)::text = ANY (ARRAY[('Habitual'::character varying)::text, ('Fugaz'::character varying)::text])))
);
CREATE TABLE factura_estado_historial (
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
CREATE TABLE producto (
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
CREATE TABLE proveedor (
    id_proveedor integer NOT NULL,
    nombre character varying(120) NOT NULL,
    telefono character varying(30),
    email character varying(120),
    direccion character varying(200)
);
CREATE TABLE rol (
    id_rol integer NOT NULL,
    nombre character varying(30) NOT NULL
);
CREATE TABLE seccion (
    id_seccion integer NOT NULL,
    nombre character varying(30) NOT NULL
);
CREATE TABLE usuario (
    id_usuario integer NOT NULL,
    nombre character varying(100) NOT NULL,
    email character varying(120) NOT NULL,
    password text NOT NULL,
    id_rol integer NOT NULL,
    id_seccion integer
);
