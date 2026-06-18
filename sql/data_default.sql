-- =========================================================
-- PREPARAR AUDITORÍA
-- =========================================================
ALTER TABLE auditoria
ADD COLUMN IF NOT EXISTS fecha TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now();

ALTER TABLE auditoria
ADD COLUMN IF NOT EXISTS id_usuario INTEGER;

ALTER TABLE auditoria
ALTER COLUMN usuario SET DEFAULT 'Sistema';

-- =========================================================
-- LIMPIAR TABLAS Y REINICIAR IDS
-- NO TOCA usuario NI cliente
-- =========================================================
BEGIN;

TRUNCATE TABLE
    detallefactura,
    detallecompra,
    factura,
    compra,
    producto,
    proveedor,
    categoria,
    auditoria
RESTART IDENTITY CASCADE;

COMMIT;

-- =========================================================
-- INSERTAR DATOS DE PRUEBA
-- =========================================================
BEGIN;

-- =========================================================
-- 1. CATEGORÍAS
-- =========================================================
INSERT INTO categoria (nombre) VALUES
('Camisetas'),
('Hoodies'),
('Stickers'),
('Tazas'),
('Gorras'),
('Llaveros'),
('Posters'),
('Bolsos'),
('Mousepads'),
('Accesorios personalizados');

-- =========================================================
-- 2. PROVEEDORES
-- =========================================================
INSERT INTO proveedor (nombre, telefono, email, direccion) VALUES
('Textiles Managua S.A.', '2255-1101', '<ventas@textilesmanagua.com>', 'Carretera Norte, Managua'),
('Serigrafía Central', '2266-2304', '<contacto@serigrafiacentral.com>', 'Altamira, Managua'),
('Distribuidora Kitsune', '2277-4512', '<proveedores@kitsunedist.com>', 'Linda Vista, Managua'),
('Panda Print Supplies', '2288-7821', '<ventas@pandaprintsupplies.com>', 'Ciudad Jardín, Managua'),
('Importadora El Sol', '2299-3344', '<info@importadoraelsol.com>', 'Mercado Oriental, Managua'),
('Creativa Nicaragua', '2233-9021', '<creativa@ni.com>', 'Los Robles, Managua'),
('Sublimados León', '2311-4400', '<ventas@sublimadosleon.com>', 'Centro, León'),
('Artes Gráficas Granada', '2552-8820', '<contacto@artesgranada.com>', 'Calle La Calzada, Granada'),
('Materiales Omega', '2244-9910', '<omega@materiales.com>', 'Bolonia, Managua'),
('Print House Nicaragua', '2270-6543', '<ventas@printhouseni.com>', 'Villa Fontana, Managua');

-- =========================================================
-- 3. PRODUCTOS - 120 REGISTROS
-- =========================================================
INSERT INTO producto (
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
SELECT
    'P' || LPAD(gs::TEXT, 3, '0'),
    CASE
        WHEN gs % 10 = 1 THEN 'Camiseta Oversize Negra'
        WHEN gs % 10 = 2 THEN 'Camiseta Blanca Personalizada'
        WHEN gs % 10 = 3 THEN 'Hoodie Oversize Negro'
        WHEN gs % 10 = 4 THEN 'Sticker Holográfico Kitsune'
        WHEN gs % 10 = 5 THEN 'Taza Sublimada Panda'
        WHEN gs % 10 = 6 THEN 'Gorra Bordada Negra'
        WHEN gs % 10 = 7 THEN 'Llavero Acrílico Anime'
        WHEN gs % 10 = 8 THEN 'Poster Ilustrado A3'
        WHEN gs % 10 = 9 THEN 'Bolso Tote Personalizado'
        ELSE 'Mousepad Gamer Estampado'
    END || ' #' || gs,
    'Producto generado para inventario académico de Panda Estampados y Kitsune.',
    NULL,
    ((gs - 1) % 10) + 1,
    ((gs - 1) % 10) + 1,
    ROUND((80 + (gs *3.75))::NUMERIC, 2),
    ROUND((130 + (gs* 5.25))::NUMERIC, 2),
    20 + (gs % 85)
FROM generate_series(1, 120) AS gs;

-- =========================================================
-- 4. COMPRAS - 40 REGISTROS
-- =========================================================
INSERT INTO compra (
    fecha,
    id_proveedor,
    id_usuario,
    total
)
SELECT
    TIMESTAMP '2026-01-05 08:30:00'
        + ((gs *2) || ' days')::INTERVAL
        + ((gs % 8) || ' hours')::INTERVAL,
    ((gs - 1) % 10) + 1,
    (
        SELECT id_usuario
        FROM usuario
        ORDER BY id_usuario
        LIMIT 1 OFFSET ((gs - 1) % (SELECT COUNT(*) FROM usuario))
    ),
    0
FROM generate_series(1, 40) AS gs;

-- =========================================================
-- 5. DETALLE DE COMPRAS - 120 REGISTROS
-- =========================================================
INSERT INTO detallecompra (
    id_compra,
    id_producto,
    cantidad,
    costo_unitario,
    total_linea
)
SELECT
    c.id_compra,
    ((c.id_compra *3 + d.n - 1) % 120) + 1,
    5 + ((c.id_compra + d.n) % 12),
    p.precio_compra,
    (5 + ((c.id_compra + d.n) % 12))* p.precio_compra
FROM compra c
CROSS JOIN generate_series(1, 3) AS d(n)
JOIN producto p
    ON p.id_producto = ((c.id_compra * 3 + d.n - 1) % 120) + 1;

UPDATE compra c
SET total = COALESCE((
    SELECT SUM(dc.total_linea)
    FROM detallecompra dc
    WHERE dc.id_compra = c.id_compra
), 0);

-- =========================================================
-- 6. FACTURAS - 80 REGISTROS
-- =========================================================
INSERT INTO factura (
    fecha,
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
SELECT
    TIMESTAMP '2026-05-18 08:00:00'
        + ((gs % 30) || ' days')::INTERVAL
        + ((gs % 12) || ' hours')::INTERVAL,
    (
        SELECT id_cliente
        FROM cliente
        ORDER BY id_cliente
        LIMIT 1 OFFSET ((gs - 1) % (SELECT COUNT(*) FROM cliente))
    ),
    (
        SELECT id_usuario
        FROM usuario
        ORDER BY id_usuario
        LIMIT 1 OFFSET ((gs - 1) % (SELECT COUNT(*) FROM usuario))
    ),
    (
        SELECT id_seccion
        FROM seccion
        ORDER BY id_seccion
        LIMIT 1 OFFSET ((gs - 1) % (SELECT COUNT(*) FROM seccion))
    ),
    0,
    CASE WHEN gs % 5 = 0 THEN 25.00 ELSE 0.00 END,
    0,
    0,
    CASE WHEN gs % 6 = 0 THEN 'Fugaz' ELSE 'Habitual' END,
    CASE
        WHEN gs % 6 = 0 THEN
            CASE
                WHEN gs % 4 = 0 THEN 'Carlos Mendoza'
                WHEN gs % 4 = 1 THEN 'María González'
                WHEN gs % 4 = 2 THEN 'José Ramírez'
                ELSE 'Lucía Pérez'
            END
        ELSE NULL
    END
FROM generate_series(1, 80) AS gs;

-- =========================================================
-- 7. DETALLE DE FACTURAS - 160 REGISTROS
-- =========================================================
INSERT INTO detallefactura (
    id_factura,
    id_producto,
    cantidad,
    precio_unitario,
    descuento_linea,
    total_linea
)
SELECT
    f.id_factura,
    ((f.id_factura *2 + d.n - 1) % 120) + 1,
    1 + ((f.id_factura + d.n) % 4),
    p.precio_venta,
    CASE WHEN (f.id_factura + d.n) % 7 = 0 THEN 10.00 ELSE 0.00 END,
    ((1 + ((f.id_factura + d.n) % 4))* p.precio_venta)
        - CASE WHEN (f.id_factura + d.n) % 7 = 0 THEN 10.00 ELSE 0.00 END
FROM factura f
CROSS JOIN generate_series(1, 2) AS d(n)
JOIN producto p
    ON p.id_producto = ((f.id_factura * 2 + d.n - 1) % 120) + 1;

UPDATE factura f
SET
    subtotal = COALESCE((
        SELECT SUM(df.precio_unitario *df.cantidad)
        FROM detallefactura df
        WHERE df.id_factura = f.id_factura
    ), 0),
    impuesto = ROUND((
        GREATEST(
            COALESCE((
                SELECT SUM(df.total_linea)
                FROM detallefactura df
                WHERE df.id_factura = f.id_factura
            ), 0) - f.descuento,
            0
        )* 0.15
    )::NUMERIC, 2),
    total = ROUND((
        GREATEST(
            COALESCE((
                SELECT SUM(df.total_linea)
                FROM detallefactura df
                WHERE df.id_factura = f.id_factura
            ), 0) - f.descuento,
            0
        ) * 1.15
    )::NUMERIC, 2);

-- =========================================================
-- 8. AUDITORÍA - 40 REGISTROS
-- =========================================================
INSERT INTO auditoria (
    usuario,
    accion,
    tabla_afectada,
    descripcion,
    fecha,
    id_usuario
)
SELECT
    COALESCE((
        SELECT u.nombre
        FROM usuario u
        ORDER BY u.id_usuario
        LIMIT 1 OFFSET ((gs - 1) % (SELECT COUNT(*) FROM usuario))
    ), 'Sistema'),
    CASE
        WHEN gs % 3 = 0 THEN 'INSERT'
        WHEN gs % 3 = 1 THEN 'UPDATE'
        ELSE 'CONSULTA'
    END,
    CASE
        WHEN gs % 4 = 0 THEN 'factura'
        WHEN gs % 4 = 1 THEN 'producto'
        WHEN gs % 4 = 2 THEN 'compra'
        ELSE 'cliente'
    END,
    'Registro de auditoría generado para pruebas académicas #' || gs,
    TIMESTAMP '2026-02-01 08:00:00'
        + (gs || ' days')::INTERVAL,
    (
        SELECT id_usuario
        FROM usuario
        ORDER BY id_usuario
        LIMIT 1 OFFSET ((gs - 1) % (SELECT COUNT(*) FROM usuario))
    )
FROM generate_series(1, 40) AS gs;

COMMIT;

WITH imagenes AS (
    SELECT *
    FROM (
        VALUES
            (1, 'uploads/productos/prod_3358337933ce52ca1d0d187f.jpg'),
            (2, 'uploads/productos/prod_69f56f7c0e2da0.19573275.jpg'),
            (3, 'uploads/productos/prod_69f5712157ed25.57458575.jpg'),
            (4, 'uploads/productos/prod_3bc17b8d1377a4826a2d8912.jpg'),
            (5, 'uploads/productos/prod_69f56f88ee4f13.38325589.jpg'),
            (6, 'uploads/productos/prod_69f5712a0bc1f0.76146228.jpg'),
            (7, 'uploads/productos/prod_5864c2b76949a1ffd7fb6bbb.jpg'),
            (8, 'uploads/productos/prod_69f56f9aa6b7b4.37645804.jpg'),
            (9, 'uploads/productos/prod_77799003da6531f02abb08fb.jpg'),
            (10, 'uploads/productos/prod_5b3fca06c807b9fed7369b0e.jpg'),
            (11, 'uploads/productos/prod_69f56fb5b95a43.81430094.jpg'),
            (12, 'uploads/productos/prod_8b600c2980bacbe62c44cbfd.jpg'),
            (13, 'uploads/productos/prod_67817b473371f51443a44144.jpg'),
            (14, 'uploads/productos/prod_69f56fc4a61b44.25791177.jpg'),
            (15, 'uploads/productos/prod_977042d6b3d3bdc28500a0b5.jpg'),
            (16, 'uploads/productos/prod_692cba3e06cd65.71101317.png'),
            (17, 'uploads/productos/prod_69f56fd3dbc463.93497112.png'),
            (18, 'uploads/productos/prod_bbf90800dd3fd1f24deb27b4.webp'),
            (19, 'uploads/productos/prod_692e2130300f13.79885065.jpg'),
            (20, 'uploads/productos/prod_69f56fdd0d4ab5.98973422.jpg'),
            (21, 'uploads/productos/prod_bd9ae845c714f7f64699fb75.jpg'),
            (22, 'uploads/productos/prod_692eb95bbb2083.00268187.gif'),
            (23, 'uploads/productos/prod_69f56fe7efb784.38860716.jpg'),
            (24, 'uploads/productos/prod_d67e3785273607d89e61a401.png'),
            (25, 'uploads/productos/prod_69ab914e50de9c2ab70f17e2.jpg'),
            (26, 'uploads/productos/prod_69f570ce0de300.86936150.jpg'),
            (27, 'uploads/productos/prod_e155b0fa66e83067205236e4.jpg'),
            (28, 'uploads/productos/prod_69f56f68b99380.15585436.jpg'),
            (29, 'uploads/productos/prod_69f57104731e04.45231154.jpg'),
            (30, 'uploads/productos/prod_ee0704d79c323b08c7071c3e.jpg'),
            (31, 'uploads/productos/prod_69f56f74936e35.57858233.jpg'),
            (32, 'uploads/productos/prod_69f57111237e59.08360337.jpg'),
            (33, 'uploads/productos/prod_f5368854e9de4fe5d5ef70c1.jpg')
    ) AS t(posicion, ruta)
),
productos_ordenados AS (
    SELECT
        id_producto,
        ROW_NUMBER() OVER (ORDER BY id_producto) AS fila
    FROM producto
)
UPDATE producto p
SET imagen = i.ruta
FROM productos_ordenados po
JOIN imagenes i
    ON i.posicion = ((po.fila - 1) % 33) + 1
WHERE p.id_producto = po.id_producto;

UPDATE producto
SET imagen = regexp_replace(imagen, '^uploads/productos/', '');

UPDATE producto
SET stock = CASE
    WHEN id_producto IN (3, 8, 15, 22, 31, 44, 57, 68, 79, 91) THEN 1
    WHEN id_producto IN (5, 12, 27, 36, 48, 63, 74, 86, 99, 110) THEN 2
    WHEN id_producto IN (9, 19, 33, 41, 52, 66, 72, 88, 104, 118) THEN 3
    WHEN id_producto IN (14, 24, 39, 55, 70, 84, 96, 108, 115, 120) THEN 4
    ELSE stock
END
WHERE id_producto BETWEEN 1 AND 120;
