DROP DATABASE IF EXISTS pandas_estampados_y_kitsune;

CREATE DATABASE pandas_estampados_y_kitsune;

\c pandas_estampados_y_kitsune

/* 1) Seguridad y estructura interna */

------------------------------------------------------------
-- 1) TABLAS BÁSICAS: ROLES, SECCIONES, USUARIOS
------------------------------------------------------------

CREATE TABLE Seccion (
    id_seccion SERIAL PRIMARY KEY,
    nombre     VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE Rol (
    id_rol SERIAL PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE Usuario (
    id_usuario  SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    email       VARCHAR(120) NOT NULL UNIQUE,
    password    TEXT NOT NULL,
    id_rol      INT NOT NULL,
    id_seccion  INT NULL,
    CONSTRAINT FK_Usuario_Rol FOREIGN KEY (id_rol) REFERENCES Rol(id_rol),
    CONSTRAINT FK_Usuario_Seccion FOREIGN KEY (id_seccion) REFERENCES Seccion(id_seccion)
);


------------------------------------------------------------
-- 2) CATÁLOGO: CATEGORÍAS, PROVEEDORES, PRODUCTOS
------------------------------------------------------------

CREATE TABLE Categoria (
    id_categoria SERIAL PRIMARY KEY,
    nombre       VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE Proveedor (
    id_proveedor SERIAL PRIMARY KEY,
    nombre       VARCHAR(120) NOT NULL,
    telefono     VARCHAR(30),
    email        VARCHAR(120),
    direccion    VARCHAR(200)
);

CREATE TABLE Producto (
    id_producto   SERIAL PRIMARY KEY,
    codigo        VARCHAR(50) NOT NULL UNIQUE,
    nombre        VARCHAR(120) NOT NULL,
    descripcion   TEXT DEFAULT 'Sin descripción',
    imagen        VARCHAR(200),
    id_categoria  INT NULL,
    id_proveedor  INT NULL,
    precio_compra NUMERIC(10,2) NOT NULL CHECK (precio_compra >= 0),
    precio_venta  NUMERIC(10,2) NOT NULL CHECK (precio_venta >= 0),
    stock         INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
    CONSTRAINT FK_Producto_Categoria FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria),
    CONSTRAINT FK_Producto_Proveedor FOREIGN KEY (id_proveedor) REFERENCES Proveedor(id_proveedor)
);


------------------------------------------------------------
-- 3) CLIENTES
------------------------------------------------------------

CREATE TABLE Cliente (
    id_cliente     SERIAL PRIMARY KEY,
    nombres        VARCHAR(80) NOT NULL,
    apellidos      VARCHAR(80) NOT NULL,
    telefono       VARCHAR(30),
    direccion      VARCHAR(200),
    identificacion VARCHAR(40),
    tipo_cliente   VARCHAR(12) NOT NULL DEFAULT 'Detallista',
    CONSTRAINT CK_Cliente_Tipo CHECK (tipo_cliente IN ('Mayorista','Detallista'))
);


------------------------------------------------------------
-- 4) FACTURAS Y DETALLE DE FACTURA
------------------------------------------------------------

CREATE TABLE Factura (
    id_factura SERIAL PRIMARY KEY,
    fecha      TIMESTAMP NOT NULL DEFAULT NOW(),
    id_cliente INT NOT NULL,
    id_usuario INT NOT NULL,
    id_seccion INT NOT NULL,
    subtotal   NUMERIC(10,2) NOT NULL DEFAULT 0,
    descuento  NUMERIC(10,2) NOT NULL DEFAULT 0,
    impuesto   NUMERIC(10,2) NOT NULL DEFAULT 0,
    total      NUMERIC(10,2) NOT NULL DEFAULT 0,
    tipo_cliente_venta VARCHAR(10) NOT NULL DEFAULT 'Habitual'
        CHECK (tipo_cliente_venta IN ('Habitual','Fugaz')),
    nombre_cliente_fugaz VARCHAR(150),

    CONSTRAINT FK_Factura_Cliente FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    CONSTRAINT FK_Factura_Usuario FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    CONSTRAINT FK_Factura_Seccion FOREIGN KEY (id_seccion) REFERENCES Seccion(id_seccion)
);


CREATE TABLE DetalleFactura (
    id_detalle       SERIAL PRIMARY KEY,
    id_factura       INT NOT NULL,
    id_producto      INT NOT NULL,
    cantidad         INT NOT NULL CHECK (cantidad > 0),
    precio_unitario  NUMERIC(10,2) NOT NULL CHECK (precio_unitario >= 0),
    descuento_linea  NUMERIC(10,2) NOT NULL DEFAULT 0,
    total_linea      NUMERIC(10,2) NOT NULL,
    CONSTRAINT FK_DetFac_Factura FOREIGN KEY (id_factura) REFERENCES Factura(id_factura),
    CONSTRAINT FK_DetFac_Producto FOREIGN KEY (id_producto) REFERENCES Producto(id_producto)
);


------------------------------------------------------------
-- 5) COMPRAS Y DETALLE DE COMPRAS
------------------------------------------------------------

CREATE TABLE Compra (
    id_compra    SERIAL PRIMARY KEY,
    fecha        TIMESTAMP NOT NULL DEFAULT NOW(),
    id_proveedor INT NOT NULL,
    id_usuario   INT NOT NULL,
    total        NUMERIC(10,2) NOT NULL DEFAULT 0,
    CONSTRAINT FK_Compra_Proveedor FOREIGN KEY (id_proveedor) REFERENCES Proveedor(id_proveedor),
    CONSTRAINT FK_Compra_Usuario   FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

CREATE TABLE DetalleCompra (
    id_detalle     SERIAL PRIMARY KEY,
    id_compra      INT NOT NULL,
    id_producto    INT NOT NULL,
    cantidad       INT NOT NULL CHECK (cantidad > 0),
    costo_unitario NUMERIC(10,2) NOT NULL CHECK (costo_unitario >= 0),
    total_linea    NUMERIC(10,2) NOT NULL,
    CONSTRAINT FK_DetCom_Compra FOREIGN KEY (id_compra) REFERENCES Compra(id_compra),
    CONSTRAINT FK_DetCom_Producto FOREIGN KEY (id_producto) REFERENCES Producto(id_producto)
);
