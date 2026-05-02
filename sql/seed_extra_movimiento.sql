BEGIN;

------------------------------------------------------------
-- SEED EXTRA DE MOVIMIENTO
-- Objetivo:
-- - Más productos para reportes de inventario.
-- - Más clientes para filtros y reportes.
-- - Muchas facturas con fechas variadas.
-- - Detalles de factura calculados automáticamente.
-- - Compras a proveedores para historial de compras.
-- - Algunos productos con stock bajo para probar alertas.
------------------------------------------------------------

------------------------------------------------------------
-- 1) PRODUCTOS EXTRA
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
('P016', 'Camiseta Azul Marino', 'Camiseta azul marino para diseños minimalistas y estampados sobrios.', NULL, 1, 1, 125.00, 255.00, 45),
('P017', 'Camiseta Verde Oliva', 'Camiseta verde oliva para colecciones urbanas y estilo casual.', NULL, 1, 1, 128.00, 260.00, 38),
('P018', 'Hoodie Blanco Premium', 'Hoodie blanco premium para impresión frontal y posterior.', NULL, 2, 1, 360.00, 590.00, 16),
('P019', 'Hoodie Rojo Urbano', 'Hoodie rojo con tela gruesa para diseños de temporada.', NULL, 2, 1, 340.00, 560.00, 12),
('P020', 'Taza Negra 11oz', 'Taza negra clásica para sublimación y regalos personalizados.', NULL, 3, 2, 65.00, 145.00, 55),
('P021', 'Taza Anime Full Print', 'Taza con área completa para diseños de anime y videojuegos.', NULL, 3, 2, 85.00, 190.00, 35),
('P022', 'Mousepad Gamer XL', 'Mousepad extendido para escritorio gamer con impresión personalizada.', NULL, 4, 4, 120.00, 280.00, 22),
('P023', 'Pin Acrílico Kitsune', 'Pin acrílico pequeño con diseño Kitsune coleccionable.', NULL, 4, 3, 12.00, 45.00, 180),
('P024', 'Sticker Mate Panda', 'Sticker acabado mate resistente al agua.', NULL, 6, 4, 4.00, 18.00, 420),
('P025', 'Sticker Vinil Anime', 'Sticker vinilado para laptops, botellas y libretas.', NULL, 6, 4, 6.00, 28.00, 260),
('P026', 'Bolso Tote Anime', 'Bolso tote con diseño anime, resistente y reutilizable.', NULL, 7, 1, 90.00, 185.00, 28),
('P027', 'Bolso Negro Panda', 'Bolso negro con impresión Panda Estampados.', NULL, 7, 1, 95.00, 195.00, 20),
('P028', 'Termo Blanco Sublimable', 'Termo blanco preparado para sublimación full color.', NULL, 8, 2, 180.00, 340.00, 18),
('P029', 'Termo Negro Premium', 'Termo negro premium con acabado elegante para regalos corporativos.', NULL, 8, 2, 210.00, 390.00, 14),
('P030', 'Vinilo Laptop Kitsune', 'Vinilo adhesivo para laptop con diseño Kitsune personalizado.', NULL, 5, 4, 45.00, 120.00, 70)
ON CONFLICT (codigo) DO NOTHING;

------------------------------------------------------------
-- 2) CLIENTES EXTRA
------------------------------------------------------------
INSERT INTO Cliente (
    nombres,
    apellidos,
    telefono,
    direccion,
    identificacion,
    tipo_cliente,
    fecha_registro
)
SELECT
    (ARRAY[
        'Roberto','Gabriela','Luis','Daniela','Fernando',
        'Valeria','Miguel','Andrea','Sergio','Camila',
        'Eduardo','Natalia','Bryan','Fernanda','Héctor',
        'Adriana','Samuel','Isabel','Ángel','Renata'
    ])[((g - 1) % 20) + 1] AS nombres,

    (ARRAY[
        'Morales','Navarro','Gutiérrez','Rivas','Aguilar',
        'Vargas','Pineda','Reyes','Campos','Duarte',
        'Flores','Castro','Obando','Rivera','Molina',
        'Chávez','Cruz','López','Zamora','Salinas'
    ])[((g - 1) % 20) + 1] AS apellidos,

    '88' || LPAD((100000 + g)::TEXT, 6, '0') AS telefono,

    (ARRAY[
        'Managua','Masaya','León','Granada','Carazo',
        'Estelí','Chinandega','Rivas','Matagalpa','Jinotepe'
    ])[((g - 1) % 10) + 1] AS direccion,

    'AUTO-' || LPAD(g::TEXT, 4, '0') AS identificacion,

    CASE
        WHEN g % 5 = 0 THEN 'Mayorista'
        ELSE 'Detallista'
    END AS tipo_cliente,

    (DATE '2025-11-15' + (g * INTERVAL '4 days'))::DATE AS fecha_registro
FROM generate_series(1, 35) AS g;

------------------------------------------------------------
-- 3) FACTURAS EXTRA + DETALLES
------------------------------------------------------------
-- Crea 90 facturas distribuidas entre enero y abril de 2026.
-- Cada factura habitual tendrá entre 2 y 4 productos.
-- Algunas facturas serán de cliente fugaz, pero se mantienen por debajo
-- de C$ 1,000 para respetar la regla que implementaste en el sistema.
------------------------------------------------------------

DO $$
DECLARE
    clientes_ids INT[];
    usuarios_ids INT[];
    productos_ids INT[];

    id_cliente_fugaz INT;
    id_factura_nueva INT;

    i INT;
    j INT;

    cliente_id INT;
    usuario_id INT;
    seccion_id INT;
    producto_id INT;

    tipo_venta TEXT;
    nombre_fugaz TEXT;

    fecha_factura TIMESTAMP;

    cantidad INT;
    lineas INT;

    precio NUMERIC(10,2);
    descuento_linea NUMERIC(10,2);
    total_linea NUMERIC(10,2);

    subtotal_factura NUMERIC(10,2);
    descuento_factura NUMERIC(10,2);
    impuesto_factura NUMERIC(10,2);
    total_factura NUMERIC(10,2);
BEGIN
    SELECT ARRAY_AGG(id_cliente ORDER BY id_cliente)
    INTO clientes_ids
    FROM Cliente
    WHERE identificacion IS DISTINCT FROM 'FUGAZ';

    SELECT id_cliente
    INTO id_cliente_fugaz
    FROM Cliente
    WHERE identificacion = 'FUGAZ'
    LIMIT 1;

    SELECT ARRAY_AGG(id_usuario ORDER BY id_usuario)
    INTO usuarios_ids
    FROM Usuario;

    SELECT ARRAY_AGG(id_producto ORDER BY id_producto)
    INTO productos_ids
    FROM Producto;

    IF id_cliente_fugaz IS NULL THEN
        RAISE EXCEPTION 'No existe el cliente FUGAZ. Inserte primero Cliente/Fugaz con identificacion = FUGAZ.';
    END IF;

    IF array_length(clientes_ids, 1) IS NULL THEN
        RAISE EXCEPTION 'No hay clientes habituales disponibles.';
    END IF;

    IF array_length(usuarios_ids, 1) IS NULL THEN
        RAISE EXCEPTION 'No hay usuarios disponibles.';
    END IF;

    IF array_length(productos_ids, 1) IS NULL THEN
        RAISE EXCEPTION 'No hay productos disponibles.';
    END IF;

    FOR i IN 1..90 LOOP
        subtotal_factura := 0;
        descuento_factura := 0;

        fecha_factura :=
            TIMESTAMP '2026-01-05 08:00:00'
            + (i * INTERVAL '1 day')
            + ((i % 8) * INTERVAL '1 hour')
            + ((i % 4) * INTERVAL '12 minutes');

        usuario_id := usuarios_ids[((i - 1) % array_length(usuarios_ids, 1)) + 1];

        seccion_id := CASE
            WHEN i % 2 = 0 THEN 2
            ELSE 1
        END;

        IF i % 11 = 0 THEN
            tipo_venta := 'Fugaz';
            cliente_id := id_cliente_fugaz;
            nombre_fugaz := 'Cliente rápido #' || i;
            lineas := 1;
        ELSE
            tipo_venta := 'Habitual';
            cliente_id := clientes_ids[((i - 1) % array_length(clientes_ids, 1)) + 1];
            nombre_fugaz := NULL;
            lineas := 2 + (i % 3);
        END IF;

        INSERT INTO Factura (
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
        VALUES (
            fecha_factura,
            cliente_id,
            usuario_id,
            seccion_id,
            0,
            0,
            0,
            0,
            tipo_venta,
            nombre_fugaz
        )
        RETURNING id_factura INTO id_factura_nueva;

        FOR j IN 1..lineas LOOP
            producto_id := productos_ids[((i + (j * 3) - 1) % array_length(productos_ids, 1)) + 1];

            SELECT precio_venta
            INTO precio
            FROM Producto
            WHERE id_producto = producto_id;

            cantidad := CASE
                WHEN tipo_venta = 'Fugaz' THEN 1
                ELSE 1 + ((i + j) % 5)
            END;

            descuento_linea := CASE
                WHEN (i + j) % 4 = 0 THEN ROUND((precio * cantidad) * 0.05, 2)
                ELSE 0
            END;

            total_linea := (precio * cantidad) - descuento_linea;

            INSERT INTO DetalleFactura (
                id_factura,
                id_producto,
                cantidad,
                precio_unitario,
                descuento_linea,
                total_linea
            )
            VALUES (
                id_factura_nueva,
                producto_id,
                cantidad,
                precio,
                descuento_linea,
                total_linea
            );

            subtotal_factura := subtotal_factura + (precio * cantidad);
            descuento_factura := descuento_factura + descuento_linea;
        END LOOP;

        -- Descuento global ocasional.
        IF i % 7 = 0 THEN
            descuento_factura := descuento_factura + 50;
        END IF;

        -- Regla de control para facturas fugaces:
        -- se ajusta el descuento para que el total final no supere C$ 1,000.
        IF tipo_venta = 'Fugaz' AND (subtotal_factura - descuento_factura) > 850 THEN
            descuento_factura := subtotal_factura - 850;
        END IF;

        impuesto_factura := ROUND(GREATEST(subtotal_factura - descuento_factura, 0) * 0.15, 2);
        total_factura := ROUND(GREATEST(subtotal_factura - descuento_factura, 0) + impuesto_factura, 2);

        UPDATE Factura
        SET
            subtotal = subtotal_factura,
            descuento = descuento_factura,
            impuesto = impuesto_factura,
            total = total_factura
        WHERE id_factura = id_factura_nueva;
    END LOOP;
END $$;

------------------------------------------------------------
-- 4) COMPRAS EXTRA + DETALLES
------------------------------------------------------------
-- Crea 35 compras a proveedores con detalles calculados.
------------------------------------------------------------

DO $$
DECLARE
    proveedores_ids INT[];
    usuarios_ids INT[];
    productos_ids INT[];

    id_compra_nueva INT;

    i INT;
    j INT;

    proveedor_id INT;
    usuario_id INT;
    producto_id INT;

    fecha_compra TIMESTAMP;

    cantidad INT;
    costo NUMERIC(10,2);
    total_linea NUMERIC(10,2);
    total_compra NUMERIC(10,2);
BEGIN
    SELECT ARRAY_AGG(id_proveedor ORDER BY id_proveedor)
    INTO proveedores_ids
    FROM Proveedor;

    SELECT ARRAY_AGG(id_usuario ORDER BY id_usuario)
    INTO usuarios_ids
    FROM Usuario;

    SELECT ARRAY_AGG(id_producto ORDER BY id_producto)
    INTO productos_ids
    FROM Producto;

    IF array_length(proveedores_ids, 1) IS NULL THEN
        RAISE EXCEPTION 'No hay proveedores disponibles.';
    END IF;

    IF array_length(usuarios_ids, 1) IS NULL THEN
        RAISE EXCEPTION 'No hay usuarios disponibles.';
    END IF;

    IF array_length(productos_ids, 1) IS NULL THEN
        RAISE EXCEPTION 'No hay productos disponibles.';
    END IF;

    FOR i IN 1..35 LOOP
        total_compra := 0;

        proveedor_id := proveedores_ids[((i - 1) % array_length(proveedores_ids, 1)) + 1];
        usuario_id := usuarios_ids[((i + 2) % array_length(usuarios_ids, 1)) + 1];

        fecha_compra :=
            TIMESTAMP '2025-12-01 09:00:00'
            + (i * INTERVAL '3 days')
            + ((i % 6) * INTERVAL '1 hour');

        INSERT INTO Compra (
            fecha,
            id_proveedor,
            id_usuario,
            total
        )
        VALUES (
            fecha_compra,
            proveedor_id,
            usuario_id,
            0
        )
        RETURNING id_compra INTO id_compra_nueva;

        FOR j IN 1..2 LOOP
            producto_id := productos_ids[((i + (j * 5) - 1) % array_length(productos_ids, 1)) + 1];

            SELECT precio_compra
            INTO costo
            FROM Producto
            WHERE id_producto = producto_id;

            cantidad := 5 + ((i + j) % 20);
            total_linea := costo * cantidad;

            INSERT INTO DetalleCompra (
                id_compra,
                id_producto,
                cantidad,
                costo_unitario,
                total_linea
            )
            VALUES (
                id_compra_nueva,
                producto_id,
                cantidad,
                costo,
                total_linea
            );

            total_compra := total_compra + total_linea;
        END LOOP;

        UPDATE Compra
        SET total = total_compra
        WHERE id_compra = id_compra_nueva;
    END LOOP;
END $$;

------------------------------------------------------------
-- 5) AJUSTES PARA PROBAR STOCK BAJO
------------------------------------------------------------
-- Esto permite que dashboard.php?stock=bajo y reportes de inventario
-- tengan productos para mostrar como alerta.
------------------------------------------------------------
UPDATE Producto
SET stock = CASE codigo
    WHEN 'P003' THEN 4
    WHEN 'P010' THEN 3
    WHEN 'P015' THEN 2
    WHEN 'P018' THEN 5
    WHEN 'P019' THEN 1
    WHEN 'P029' THEN 4
    ELSE stock
END
WHERE codigo IN ('P003', 'P010', 'P015', 'P018', 'P019', 'P029');

COMMIT;

------------------------------------------------------------
-- 6) VERIFICACIÓN RÁPIDA
------------------------------------------------------------
SELECT 'Clientes' AS tabla, COUNT(*) AS total FROM Cliente
UNION ALL
SELECT 'Productos', COUNT(*) FROM Producto
UNION ALL
SELECT 'Facturas', COUNT(*) FROM Factura
UNION ALL
SELECT 'Detalles factura', COUNT(*) FROM DetalleFactura
UNION ALL
SELECT 'Compras', COUNT(*) FROM Compra
UNION ALL
SELECT 'Detalles compra', COUNT(*) FROM DetalleCompra;

SELECT
    DATE(fecha) AS fecha,
    COUNT(*) AS facturas,
    SUM(total) AS ventas_dia
FROM Factura
GROUP BY DATE(fecha)
ORDER BY fecha DESC
LIMIT 15;

SELECT
    p.codigo,
    p.nombre,
    SUM(df.cantidad) AS cantidad_vendida,
    SUM(df.total_linea) AS total_vendido
FROM DetalleFactura df
INNER JOIN Producto p ON p.id_producto = df.id_producto
GROUP BY p.codigo, p.nombre
ORDER BY cantidad_vendida DESC
LIMIT 15;