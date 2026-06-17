CREATE TABLE rol (
    id_rol INT NOT NULL,
    nombre VARCHAR(30) NOT NULL,
    PRIMARY KEY (id_rol)
);

CREATE TABLE seccion (
    id_seccion INT NOT NULL,
    nombre VARCHAR(30) NOT NULL,
    PRIMARY KEY (id_seccion)
);

CREATE TABLE usuario (
    id_usuario INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    password TEXT NOT NULL,
    id_rol INT NOT NULL,
    id_seccion INT,
    PRIMARY KEY (id_usuario),
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol),
    FOREIGN KEY (id_seccion) REFERENCES seccion(id_seccion)
);

CREATE TABLE categoria (
    id_categoria INT NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    PRIMARY KEY (id_categoria)
);

CREATE TABLE proveedor (
    id_proveedor INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(30),
    email VARCHAR(120),
    direccion VARCHAR(200),
    PRIMARY KEY (id_proveedor)
);

CREATE TABLE cliente (
    id_cliente INT NOT NULL,
    nombres VARCHAR(80) NOT NULL,
    apellidos VARCHAR(80) NOT NULL,
    telefono VARCHAR(30),
    direccion VARCHAR(200),
    identificacion VARCHAR(40),
    tipo_cliente VARCHAR(12) NOT NULL DEFAULT 'Detallista',
    fecha_registro DATE NOT NULL,
    PRIMARY KEY (id_cliente)
);

CREATE TABLE producto (
    id_producto INT NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(200),
    id_categoria INT,
    id_proveedor INT,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id_producto),
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria),
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
);

CREATE TABLE compra (
    id_compra INT NOT NULL,
    fecha DATETIME NOT NULL,
    id_proveedor INT NOT NULL,
    id_usuario INT NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (id_compra),
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE detallecompra (
    id_detalle INT NOT NULL,
    id_compra INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL,
    total_linea DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_detalle),
    FOREIGN KEY (id_compra) REFERENCES compra(id_compra),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE factura (
    id_factura INT NOT NULL,
    fecha DATETIME NOT NULL,
    id_cliente INT NOT NULL,
    id_usuario INT NOT NULL,
    id_seccion INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0,
    impuesto DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    tipo_cliente_venta VARCHAR(10) NOT NULL DEFAULT 'Habitual',
    nombre_cliente_fugaz VARCHAR(150),
    monto_pagado DECIMAL(10,2) NOT NULL DEFAULT 0,
    saldo_pendiente DECIMAL(10,2) NOT NULL DEFAULT 0,
    porcentaje_pagado DECIMAL(5,2) NOT NULL DEFAULT 0,
    estado_pago VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
    estado_produccion VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
    fecha_orden_produccion DATETIME,
    fecha_entrega_estimada DATE,
    fecha_entrega_real DATE,
    PRIMARY KEY (id_factura),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_seccion) REFERENCES seccion(id_seccion)
);

CREATE TABLE detallefactura (
    id_detalle INT NOT NULL,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    descuento_linea DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_linea DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_detalle),
    FOREIGN KEY (id_factura) REFERENCES factura(id_factura),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE factura_estado_historial (
    id_historial INT NOT NULL,
    id_factura INT NOT NULL,
    tipo_evento VARCHAR(80) NOT NULL,
    estado_pago_anterior VARCHAR(50),
    estado_pago_nuevo VARCHAR(50),
    estado_produccion_anterior VARCHAR(80),
    estado_produccion_nuevo VARCHAR(80),
    monto_pagado_anterior DECIMAL(12,2),
    monto_pagado_nuevo DECIMAL(12,2),
    monto_abonado DECIMAL(12,2) DEFAULT 0,
    saldo_anterior DECIMAL(12,2),
    saldo_nuevo DECIMAL(12,2),
    fecha_entrega_estimada_anterior DATE,
    fecha_entrega_estimada_nueva DATE,
    comentario TEXT,
    fecha_evento DATETIME NOT NULL,
    PRIMARY KEY (id_historial),
    FOREIGN KEY (id_factura) REFERENCES factura(id_factura)
);

CREATE TABLE auditoria (
    id_auditoria INT NOT NULL,
    usuario VARCHAR(100) DEFAULT 'Sistema',
    accion VARCHAR(50) NOT NULL,
    tabla_afectada VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_registro DATETIME NOT NULL,
    fecha DATETIME NOT NULL,
    id_usuario INT,
    registro_id TEXT,
    datos_anteriores JSON,
    PRIMARY KEY (id_auditoria),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);
