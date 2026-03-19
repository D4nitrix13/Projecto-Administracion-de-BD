------------------------------------------------------------
-- 1) ROLES  (solo 3 roles en el sistema)
------------------------------------------------------------
INSERT INTO Rol (nombre) VALUES
('Administrador'),
('Supervisor'),
('Facturador');

------------------------------------------------------------
-- 2) SECCIONES
------------------------------------------------------------
INSERT INTO Seccion (nombre) VALUES
('Panda Estampados'),
('Kitsune');

------------------------------------------------------------
-- 3) USUARIOS (30 usuarios, con tus 30 hashes)
-- password0  → Admin General
-- password1  → Daniel Pérez
-- ...
-- password29 → Reportes BI
------------------------------------------------------------
INSERT INTO Usuario (nombre, email, password, id_rol, id_seccion) VALUES
('Leonel Messi', 'leonel.messi@admin.pandakitsune.com', '$2y$12$eNT2qKeNt3ra8jeYrXM72.dGnzKPH7Sk1sUsKA/VVgBqNJfEh5p1S', 1, NULL),

('Daniel Pérez',    'daniel.perez@kitsune.com',          '$2y$12$q/FLcSnscgAJx5KLw6ZfkeKmqVNjVgsid.NzF1vIA7bFvwqZvrtEm', 2, 2),
('Jeremy Pérez',    'jeremy.perez@kitsune.com',          '$2y$12$9cCSd.m5eccghVDHnx1hZOM1KYPHUzQZsJP3Wx4rVYA6L.zbRLuiK', 2, 2),
('Jhossep Ramos',   'jhossep.ramos@kitsune.com',         '$2y$12$6iu4FfS3vWAN3g0dCpaJH./pq4u2fsOQmujzvPxtcoxX1/VhgmV8W', 2, 2),
('Diego Torres',    'diego.torres@kitsune.com',          '$2y$12$8rYAJPYHfmBYfUsvSQ6JQeYzXY/dfhv5minqtioSOT/OYNpYhKfx2', 2, 2),
('Carlos Núñez',    'carlos.nunez@kitsune.com',          '$2y$12$Wktc.2kJD1t1addvJTy6h.HZsofpoPdinZfkNuQpOzT6x1PdxjcbW', 2, 2),
('Mónica Larios',   'monica.larios@kitsune.com',         '$2y$12$VfV8bO4qFkH5AW42IEEeBebUH2OG8E24/adYxBHpkgNxxbpwpDlKe', 2, 2),
('Esteban Rodríguez','esteban.rodriguez@kitsune.com',    '$2y$12$6xrE3MOnkmTmYvQDepNuB.dxG7I2BFg9enlTc2Qu8s0JzmDYVm1ze', 2, 2),
('Eduardo Molina',  'eduardo.molina@kitsune.com',        '$2y$12$NHgiv5ky8RBEP.vlKcDZUeiV2ZjiRENZmcmnS9lWm7NsFl3N8Gqom', 2, 2),

-- TODOS LOS FACTURADORES (id_rol = 3) EN KITSUNE (id_seccion = 2)
('Andy Sánchez',    'andy.sanchez@panda.com',            '$2y$12$h/WagOY4zymqWqcyccvi7.ikKYZxbFknFzm6bqlEfySGINOBxM5US', 3, 2),
('Sofía Gómez',     'sofia.gomez@kitsune.com',           '$2y$12$hVV/KUpCbUX9EOKMueVA3Ozsdgx4na8c9K.HN7Y2LDB9YpoOuX72a', 3, 2),
('Luis Torres',     'luis.torres@panda.com',             '$2y$12$NMsgDw0o0RgTCGtdX9EY8eyhderZcaV4/VPHZOPYR52NJIrde/8q.', 3, 2),
('Carla Bermúdez',  'carla.bermudez@kitsune.com',        '$2y$12$ZuE7Qa26mz2IlL0hl7CW1u/1KL5Bw.Sx8syeol1a7Xm2LXssw7oC6', 3, 2),
('Karla Medina',    'karla.medina@panda.com',            '$2y$12$D5j0CPjHq4paoSBtFFBCDeFHcAd4Lm9onOQYPM9zBRRDJ3OWN/ml6', 3, 2),
('Wilmer Ruiz',     'wilmer.ruiz@kitsune.com',           '$2y$12$br0QmLjZnqcHE.UzWSpQieJYav6W4Fxqi1s8vrBwQuC0g2q0OSnHi', 3, 2),
('Miguel Hernández','miguel.hernandez@panda.com',        '$2y$12$H.Ui0n/1U4Ae32AI.C9bbOfSISzETo2MHFKkZWFot8UrObFuesTQ2', 3, 2),
('Paola López',     'paola.lopez@panda.com',             '$2y$12$FDyaXbpcOqlWn0dIk/XHTumo42ZM1aX1U.XZ5wMHtQ7lQo7LgQMOa', 3, 2),
('Kevin Castillo',  'kevin.castillo@panda.com',          '$2y$12$2kA9hBSf/QPIJuDf90I2luKFTeh95mGZqISMqBIdtIe8oFnbk45H2', 3, 2),
('María Fernández', 'maria.fernandez@kitsune.com',       '$2y$12$c1izNLMr5EpE1qhi4qB67evlAxuc8EDdQK09lFcoJzUeNdt0RoWTm', 3, 2),
('Josefina Rivas',  'josefina.rivas@kitsune.com',        '$2y$12$UBJcyEORA9eFtgMVbFqu3u327RnKP1PkiA2Fd.XmA.rKom1FrDISG', 3, 2),
('Roberto Gutiérrez','roberto.gutierrez@kitsune.com',    '$2y$12$.PixiEX1MN9hves7DgCE1eUm3vsLz04Mqdd35rzbPX8yFCLEqOpzG', 3, 2),
('Lucía Herrera',   'lucia.herrera@kitsune.com',         '$2y$12$zmQ8NBBVOrw.aO2Umt.hNumWlPWAJ2aA1PxMlz/A0I.jT6o6tl2Rq', 3, 2),
('Brandon Morales', 'brandon.morales@kitsune.com',       '$2y$12$xuhd3.8.x3E4ZKo9nh0yZev8bIXCxd1aDhzlhfpD8YcxTfDTBdUf2', 3, 2),
('Andrea Vega',     'andrea.vega@panda.com',             '$2y$12$CBUWv03lSPKoAbh6gVnbyO4xlbmSAJW0Tkl.QQWbpPHbj7iumaYoG', 3, 2),
('Sergio Mairena',  'sergio.mairena@panda.com',          '$2y$12$2i1oGVK1n6q9D0Iluy3mnuE.lCulA0tfM1pc09Xc8zHn/zAKLZIdW', 3, 2),
('Julia Campos',    'julia.campos@panda.com',            '$2y$12$F7zrVU53XF1dSiV3F8xcjuJrcXNAXgieRNXAhFXMOyBOeRdZPswoy', 3, 2),

('Laura Castillo',  'laura.castillo@admin.pandakitsune.com', '$2y$12$.inALdr.Qy5DIq3dSxKjVuPSYFTL20B3VL3orm2BwxSSqwKsM3UkC', 1, NULL),
('Óscar Mejía',     'oscar.mejia@admin.pandakitsune.com',    '$2y$12$n1ZhPkXE/LMCwggOgwtHkus4Lt/XPkpgBDkz.lCdIGTU9.Kt1CV2y', 1, NULL),

('Carmen Rojas',    'carmen.rojas@panda.com',            '$2y$12$PsrYOcMFI9eBZlOJzV9RQOMx7uI9cm.3qqPk1tQYz6jxr2fIqhIjO', 3, 2),
('Nidia Solís',     'nidia.solis@kitsune.com',           '$2y$12$a9PzdMLz8ZNtGEDl6tPxnuMRq2qoYHCh55RlpcCDgC7nGvRxl2D7C', 3, 2);



------------------------------------------------------------
-- 4) CATEGORÍAS
------------------------------------------------------------
INSERT INTO Categoria (nombre) VALUES
('Camisetas'),
('Hoodies'),
('Tazas personalizadas'),
('Accesorios'),
('Vinilos'),
('Stickers'),
('Bolsos'),
('Termos');

------------------------------------------------------------
-- 5) PROVEEDORES
------------------------------------------------------------
INSERT INTO Proveedor (nombre, telefono, email, direccion) VALUES
('Textiles Premium S.A', '8888-1111', 'ventas@textilespremium.com', 'Managua'),
('Cerámica Creativa',    '7788-5511', 'info@ceramicacreativa.com',  'León'),
('Hilos & Más',          '7654-3321', 'contacto@hilosexport.com',   'Masaya'),
('EstampadosXYZ',        '9988-7766', 'ventas@xyz.com',             'Granada'),
('Sublimaciones Centro', '2255-7788', 'info@sublimcentro.com',      'Managua'),
('Impresiones Delta',    '2299-4455', 'ventas@impresionesdelta.com','Carazo');

------------------------------------------------------------
-- 6) PRODUCTOS (más variedad para que parezca producción)
------------------------------------------------------------
------------------------------------------------------------
-- 6) PRODUCTOS (con descripción e imagen)
------------------------------------------------------------
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
) VALUES
('P001', 'Camiseta Negra Premium',
 'Camiseta de algodón negro premium, ideal para estampados full color y uso diario.',
 'prod_d67e3785273607d89e61a401.png',
 1, 1, 120.00, 250.00, 55),

('P002', 'Camiseta Blanca Clásica',
 'Camiseta blanca clásica, perfecta para sublimación y estampados personalizados.',
 'prod_69ab914e50de9c2ab70f17e2.jpg',
 1, 1, 115.00, 240.00, 70),

('P003', 'Hoodie Oversize Negro',
 'Hoodie oversize en color negro, tela gruesa y suave, ideal para colección urbana.',
 'prod_3358337933ce52ca1d0d187f.jpg',
 2, 1, 350.00, 550.00, 20),

('P004', 'Taza Mágica Full Color',
 'Taza mágica que revela el diseño al contacto con agua caliente, impresión full color.',
 'prod_77799003da6531f02abb08fb.jpg',
 3, 2, 90.00, 180.00, 40),

('P005', 'Llavero Acrílico Panda',
 'Llavero acrílico con forma de panda, resistente y ligero para uso diario.',
 'prod_67817b473371f51443a44144.jpg',
 4, 3, 20.00, 60.00, 150),

('P006', 'Sticker Holográfico Kitsune',
 'Sticker holográfico con diseño de kitsune, acabado brillante y resistente al agua.',
 'prod_5b3fca06c807b9fed7369b0e.jpg',
 6, 4, 5.00, 25.00, 300),

('P007', 'Bolso Tote Reforzado',
 'Bolso tote de lona reforzada, ideal para compras, estudio o uso promocional.',
 'prod_5864c2b76949a1ffd7fb6bbb.jpg',
 7, 1, 80.00, 160.00, 35),

('P008', 'Termo de Acero Panda',
 'Termo de acero inoxidable con diseño de panda, conserva la temperatura por horas.',
 'prod_f5368854e9de4fe5d5ef70c1.jpg',
 8, 2, 160.00, 300.00, 25),

('P009', 'Camiseta Roja Edición Limitada',
 'Camiseta roja de edición limitada, tela suave y corte unisex para colecciones especiales.',
 'prod_977042d6b3d3bdc28500a0b5.jpg',
 1, 1, 135.00, 270.00, 40),

('P010', 'Hoodie Gris con Cierre',
 'Hoodie gris con cierre frontal y capucha, ideal para bordado o impresión frontal.',
 'prod_8b600c2980bacbe62c44cbfd.jpg',
 2, 1, 320.00, 520.00, 18),

('P011', 'Taza Blanca Clásica 11oz',
 'Taza blanca de 11oz lista para sublimación, perfecta para pedidos al por mayor.',
 'prod_ee0704d79c323b08c7071c3e.jpg',
 3, 2, 60.00, 130.00, 80),

('P012', 'Gorra Snapback Panda',
 'Gorra estilo snapback con logo de panda, ajustable y lista para personalización.',
 'prod_3bc17b8d1377a4826a2d8912.jpg',
 4, 3, 90.00, 180.00, 25),

('P013', 'Vinilo Decorativo Pared',
 'Vinilo decorativo para pared, fácil de colocar y remover, ideal para habitaciones y oficinas.',
 'prod_e155b0fa66e83067205236e4.jpg',
 5, 4, 70.00, 190.00, 60),

('P014', 'Pack Stickers Surtidos',
 'Pack de stickers surtidos con diferentes diseños de Panda y Kitsune, acabado mate.',
 'prod_bbf90800dd3fd1f24deb27b4.webp',
 6, 4, 3.00, 15.00, 500),

('P015', 'Termo Kitsune con Luz LED',
 'Termo Kitsune con tapa iluminada LED, ideal para regalos y colecciones especiales.',
 'prod_bd9ae845c714f7f64699fb75.jpg',
 8, 2, 220.00, 380.00, 15);


------------------------------------------------------------
-- 7) CLIENTES (más clientes mixtos)
------------------------------------------------------------
INSERT INTO Cliente (nombres, apellidos, telefono, direccion, identificacion, tipo_cliente) VALUES
('Cliente', 'Fugaz', NULL, NULL, 'FUGAZ', 'Detallista'),
('Juan',        'Lopez',       '8888-2222', 'Managua', '001-110998-0001M', 'Detallista'),
('Ana',         'Martínez',    '7777-3333', 'Masaya',  '002-210598-0044K', 'Detallista'),
('Comercial',   'Ruiz S.A',    '2266-9877', 'Granada', 'J123456789',       'Mayorista'),
('Karla',       'González',    '8855-1144', 'León',    '003-090599-0099L', 'Detallista'),
('Jhossep',     'Ramos',       '8877-6655', 'Carazo',  '004-200800-0055P', 'Detallista'),
('Impresiones','Del Norte',    '2250-7788', 'Estelí',  'J987654321',       'Mayorista'),
('María',      'Sáenz',        '8666-3344', 'Managua', '005-101001-0003M', 'Detallista'),
('Carlos',     'Blandón',      '8744-2233', 'Masaya',  '006-020202-0004H', 'Detallista'),
('Studio',     'Creativo Luna','2225-6633', 'León',    'J112233445',       'Mayorista'),
('Mario',      'Hernández',    '8787-1212', 'Granada', '007-030303-0005V', 'Detallista'),
('Lucía',      'Pérez',        '8833-5599', 'Managua', '008-040404-0006P', 'Detallista'),
('Tienda',     'Colores S.A',  '2233-8899', 'Managua', 'J556677889',       'Mayorista'),
('Kevin',      'Castillo',     '8811-7722', 'Masaya',  '009-050505-0007C', 'Detallista'),
('Paola',      'Mendoza',      '8822-9933', 'Carazo',  '010-060606-0008R', 'Detallista'),
('Diseños',    'Urbanos',      '2277-4455', 'León',    'J667788990',       'Mayorista');

------------------------------------------------------------
-- 8) FACTURAS (más movimiento de ventas)
------------------------------------------------------------
INSERT INTO Factura (id_cliente, id_usuario, id_seccion, subtotal, descuento, impuesto, total) VALUES
-- Cliente 1 -> Detallista → puede facturar Andy (Facturador)
(1, 4, 1,  500.00,   0.00,  75.00,  575.00),

-- Cliente 2 -> Detallista → puede facturar Sofía (Facturador)
(2, 5, 2,  840.00,  40.00, 120.00,  920.00),

-- Cliente 3 -> MAYORISTA → SOLO ADMIN
-- antes: (3, 4, 1, ...)
(3, 1, 1, 1500.00, 150.00, 225.00, 1575.00),

-- Cliente 4 -> Detallista → puede facturar Sofía
(4, 5, 2,  300.00,   0.00,  45.00,  345.00),

-- Cliente 5 -> Detallista → puede facturar Lucía (Facturador)
(5, 21, 2,  960.00,  60.00, 135.00, 1035.00),

-- Cliente 6 -> MAYORISTA → SOLO ADMIN
-- antes: (6, 24, 1, ...)
(6, 26, 1, 3200.00, 320.00, 432.00, 3312.00),

-- Cliente 7 -> Detallista → puede facturar Brandon (Facturador)
(7, 22, 2,  450.00,   0.00,  67.50,  517.50),

-- Cliente 8 -> Detallista → puede facturar Andrea (Facturador)
(8, 23, 1,  780.00,  30.00, 112.50,  862.50),

-- Cliente 9 -> MAYORISTA → SOLO ADMIN
-- antes: (9, 25, 1, ...)
(9, 27, 1, 2100.00, 210.00, 283.50, 2173.50),

-- Cliente 10 -> Detallista → puede facturar Carmen (Facturador)
(10, 28, 2,  650.00,   0.00,  97.50,  747.50),

-- Cliente 11 -> Detallista → puede facturar Nidia (Facturador)
(11, 29, 1,  920.00,  50.00, 130.50, 1000.50),

-- Cliente 12 -> MAYORISTA → SOLO ADMIN
-- antes: (12, 30, 2, ...)
(12, 1, 2, 1450.00,  80.00, 205.50, 1575.50);


------------------------------------------------------------
-- 9) DETALLE DE FACTURAS
-- Suponiendo que id_factura es identity starting en 1
------------------------------------------------------------
INSERT INTO DetalleFactura (id_factura, id_producto, cantidad, precio_unitario, descuento_linea, total_linea) VALUES
(1, 1,  2, 250.00,   0.00,  500.00),
(2, 6, 20,  25.00,  40.00,  460.00),
(2, 4,  2, 180.00,   0.00,  360.00),
(3, 3,  2, 550.00, 100.00, 1000.00),
(3, 8,  2, 300.00,  50.00,  550.00),
(4, 5,  5,  60.00,   0.00,  300.00),
-- Nuevas facturas
(5, 1,  3, 250.00,  30.00,  720.00),
(5, 6,  4,  25.00,  10.00,   90.00),
(6, 3,  4, 550.00, 200.00, 2000.00),
(6, 7, 10, 160.00, 160.00, 1440.00),
(7, 2,  2, 240.00,   0.00,  480.00),
(7, 14,10,  15.00,   0.00,  150.00),
(8, 9,  2, 270.00,  40.00,  500.00),
(8, 11,2, 130.00,   0.00,  260.00),
(9, 10,3, 520.00,  60.00, 1500.00),
(9, 15,2, 380.00,  40.00,  720.00),
(10,5,  6,  60.00,   0.00,  360.00),
(10,6, 16,  25.00,  50.00,  350.00),
(11,12,4, 180.00,  40.00,  680.00),
(11,1,  1, 250.00,   0.00,  250.00),
(12,13,5, 190.00,  50.00,  900.00),
(12,8,  1, 300.00,   0.00,  300.00);

------------------------------------------------------------
-- 10) COMPRAS (historial de compras a proveedores)
------------------------------------------------------------
INSERT INTO Compra (fecha, id_proveedor, id_usuario, total) VALUES
('2025-11-26 08:12:35', 1,  1, 4000.00),  -- antes usuario 2
('2025-11-26 13:48:10', 2, 26, 1800.00),  -- antes usuario 3
('2025-11-26 18:27:54', 3, 27, 2500.00),  -- antes usuario 4
('2025-11-27 09:15:22', 1,  1, 5200.00),  -- antes usuario 2
('2025-11-27 14:05:10', 5, 26, 3100.00),  -- antes usuario 3
('2025-11-28 10:45:55', 6, 27, 2750.00);  -- antes usuario 24


------------------------------------------------------------
-- 11) DETALLE DE COMPRAS
-- Suponiendo id_compra identity desde 1
------------------------------------------------------------
INSERT INTO DetalleCompra (id_compra, id_producto, cantidad, costo_unitario, total_linea) VALUES
(1, 1,  30, 120.00, 3600.00),
(1, 2,  20, 115.00, 2300.00),
(2, 4,  20,  90.00, 1800.00),
(3, 5,  80,  20.00, 1600.00),
(3, 6,  80,   5.00,  400.00),
-- nuevas compras
(4, 9,  40, 135.00, 5400.00),
(4, 10,30, 320.00, 9600.00),
(5, 11,60,  60.00, 3600.00),
(5, 14,300,  3.00,  900.00),
(6, 13,40,  70.00, 2800.00),
(6, 15,25, 220.00, 5500.00);

