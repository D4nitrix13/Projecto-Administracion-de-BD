BEGIN;

-- ============================================================
-- BACKUP INTERNO EN ESQUEMA TEMPORAL DE SEGURIDAD
-- ============================================================

DROP SCHEMA IF EXISTS backup_limpieza_demo CASCADE;
CREATE SCHEMA backup_limpieza_demo;

CREATE TABLE backup_limpieza_demo.auditoria AS TABLE auditoria;
CREATE TABLE backup_limpieza_demo.categoria AS TABLE categoria;
CREATE TABLE backup_limpieza_demo.cliente AS TABLE cliente;
CREATE TABLE backup_limpieza_demo.compra AS TABLE compra;
CREATE TABLE backup_limpieza_demo.detallecompra AS TABLE detallecompra;
CREATE TABLE backup_limpieza_demo.detallefactura AS TABLE detallefactura;
CREATE TABLE backup_limpieza_demo.factura AS TABLE factura;
CREATE TABLE backup_limpieza_demo.factura_estado_historial AS TABLE factura_estado_historial;
CREATE TABLE backup_limpieza_demo.plazo AS TABLE plazo;
CREATE TABLE backup_limpieza_demo.plazo_cuota AS TABLE plazo_cuota;
CREATE TABLE backup_limpieza_demo.producto AS TABLE producto;
CREATE TABLE backup_limpieza_demo.proveedor AS TABLE proveedor;
CREATE TABLE backup_limpieza_demo.rol AS TABLE rol;
CREATE TABLE backup_limpieza_demo.seccion AS TABLE seccion;
CREATE TABLE backup_limpieza_demo.usuario AS TABLE usuario;


-- ============================================================
-- 1. CONSERVAR SOLO FACTURAS RECIENTES IMPORTANTES
--    #22 = cancelada con abono parcial
--    #23 = pagada y entregada
--    #24 = pendiente
-- ============================================================

DELETE FROM plazo
WHERE id_factura NOT IN (22, 23, 24);

DELETE FROM factura_estado_historial
WHERE id_factura NOT IN (22, 23, 24);

DELETE FROM detallefactura
WHERE id_factura NOT IN (22, 23, 24);

DELETE FROM factura
WHERE id_factura NOT IN (22, 23, 24);


-- ============================================================
-- 2. LIMPIAR COMPRAS MASIVAS DE PRUEBA
-- ============================================================

DELETE FROM detallecompra;
DELETE FROM compra;


-- ============================================================
-- 3. LIMPIAR Y NORMALIZAR PRODUCTOS IMPORTANTES
-- ============================================================

-- Mantener catálogo pequeño y limpio.
-- Se conservan productos representativos del negocio.
DELETE FROM producto
WHERE id_producto NOT IN (9, 10, 11, 12, 13, 14, 15, 16, 17);

-- Corregir categorías/proveedores para que tengan sentido.
UPDATE producto SET
    codigo = 'CAM-001',
    nombre = 'Camiseta Oversize Negra',
    descripcion = 'Camiseta personalizada de algodón para estampado.',
    id_categoria = 1,
    id_proveedor = 1,
    precio_compra = 158.75,
    precio_venta = 240.25
WHERE id_producto = 9;

UPDATE producto SET
    codigo = 'CAM-002',
    nombre = 'Camiseta Blanca Personalizada',
    descripcion = 'Camiseta blanca lista para sublimación o serigrafía.',
    id_categoria = 1,
    id_proveedor = 1,
    precio_compra = 162.50,
    precio_venta = 245.50
WHERE id_producto = 10;

UPDATE producto SET
    codigo = 'HOD-001',
    nombre = 'Hoodie Oversize Negro',
    descripcion = 'Hoodie oversize para estampados personalizados.',
    id_categoria = 2,
    id_proveedor = 2,
    precio_compra = 166.25,
    precio_venta = 250.75
WHERE id_producto = 11;

UPDATE producto SET
    codigo = 'STK-001',
    nombre = 'Sticker Holográfico Kitsune',
    descripcion = 'Sticker decorativo holográfico con diseño Kitsune.',
    id_categoria = 3,
    id_proveedor = 3,
    precio_compra = 170.00,
    precio_venta = 256.00
WHERE id_producto = 12;

UPDATE producto SET
    codigo = 'TAZ-001',
    nombre = 'Taza Sublimada Panda',
    descripcion = 'Taza personalizada para sublimación.',
    id_categoria = 4,
    id_proveedor = 4,
    precio_compra = 173.75,
    precio_venta = 261.25
WHERE id_producto = 13;

UPDATE producto SET
    codigo = 'GOR-001',
    nombre = 'Gorra Bordada Negra',
    descripcion = 'Gorra negra personalizable con bordado.',
    id_categoria = 5,
    id_proveedor = 5,
    precio_compra = 177.50,
    precio_venta = 266.50
WHERE id_producto = 14;

UPDATE producto SET
    codigo = 'LLA-001',
    nombre = 'Llavero Acrílico Anime',
    descripcion = 'Llavero acrílico personalizado con diseño anime.',
    id_categoria = 6,
    id_proveedor = 6,
    precio_compra = 181.25,
    precio_venta = 271.75
WHERE id_producto = 15;

UPDATE producto SET
    codigo = 'POS-001',
    nombre = 'Poster Ilustrado A3',
    descripcion = 'Poster ilustrado en formato A3.',
    id_categoria = 7,
    id_proveedor = 7,
    precio_compra = 185.00,
    precio_venta = 277.00
WHERE id_producto = 16;

UPDATE producto SET
    codigo = 'BOL-001',
    nombre = 'Bolso Tote Personalizado',
    descripcion = 'Bolso tote personalizado para estampado.',
    id_categoria = 8,
    id_proveedor = 9,
    precio_compra = 188.75,
    precio_venta = 282.25
WHERE id_producto = 17;


-- ============================================================
-- 4. LIMPIAR CATEGORÍAS BASURA
-- ============================================================

DELETE FROM categoria
WHERE id_categoria NOT IN (1, 2, 3, 4, 5, 6, 7, 8, 9);


-- ============================================================
-- 5. LIMPIAR PROVEEDORES BASURA
-- ============================================================

DELETE FROM proveedor
WHERE id_proveedor NOT IN (1, 2, 3, 4, 5, 6, 7, 9, 10);


-- ============================================================
-- 6. DEJAR SOLO CLIENTES USADOS POR FACTURAS CONSERVADAS
-- ============================================================

DELETE FROM cliente
WHERE id_cliente NOT IN (
    SELECT DISTINCT id_cliente
    FROM factura
);


-- ============================================================
-- 7. DEJAR SOLO USUARIOS DEMO IMPORTANTES
--    1  = Administrador
--    2  = Supervisor
--    10 = Facturador
-- ============================================================

DELETE FROM usuario
WHERE id_usuario NOT IN (1, 2, 10);

UPDATE usuario
SET
    nombre = 'Administrador Panda Kitsune',
    email = 'admin@pandakitsune.local',
    id_rol = 1,
    id_seccion = NULL
WHERE id_usuario = 1;

UPDATE usuario
SET
    nombre = 'Supervisor Kitsune',
    email = 'supervisor@pandakitsune.local',
    id_rol = 2,
    id_seccion = 2
WHERE id_usuario = 2;

UPDATE usuario
SET
    nombre = 'Facturador Panda',
    email = 'facturador@pandakitsune.local',
    id_rol = 3,
    id_seccion = 1
WHERE id_usuario = 10;


-- ============================================================
-- 8. NORMALIZAR PLANES DE PAGO RECIENTES
-- ============================================================

UPDATE plazo
SET estado = 'Cancelado'
WHERE id_factura = 22;

UPDATE plazo
SET estado = 'Completado'
WHERE id_factura = 23;

UPDATE plazo
SET estado = 'Activo'
WHERE id_factura = 24;

UPDATE plazo_cuota
SET observaciones = ''
WHERE observaciones IS NULL
   OR trim(observaciones) IN ('asdfasdfsadfsda', 'asdfsfd', 'asdsdafsfd');

UPDATE plazo_cuota pc
SET observaciones = 'Factura cancelada con abono parcial registrado.'
FROM plazo p
WHERE p.id_plazo = pc.id_plazo
  AND p.id_factura = 22;


-- ============================================================
-- 9. MEJORAR COMENTARIOS DEL HISTORIAL RECIENTE
-- ============================================================

UPDATE factura_estado_historial
SET comentario = CASE
    WHEN id_factura = 22 AND tipo_evento = 'Factura creada'
        THEN 'Factura registrada con plan de pago pendiente.'

    WHEN id_factura = 22 AND monto_abonado > 0
        THEN 'Se registró un abono de C$ ' || TO_CHAR(monto_abonado, 'FM999999990.00') ||
             '. Saldo actual: C$ ' || TO_CHAR(saldo_nuevo, 'FM999999990.00') || '.'

    WHEN id_factura = 22 AND estado_produccion_nuevo = 'Cancelada'
        THEN 'Factura cancelada después de registrar un abono parcial.'

    WHEN id_factura = 23 AND tipo_evento = 'Factura creada'
        THEN 'Factura registrada correctamente.'

    WHEN id_factura = 23 AND monto_abonado > 0 AND estado_pago_nuevo = 'Parcial'
        THEN 'Se registró un abono parcial de C$ ' || TO_CHAR(monto_abonado, 'FM999999990.00') ||
             '. Saldo actual: C$ ' || TO_CHAR(saldo_nuevo, 'FM999999990.00') || '.'

    WHEN id_factura = 23 AND estado_pago_nuevo = 'Pagado'
        THEN 'Factura marcada como pagada. Saldo actual: C$ 0.00.'

    WHEN id_factura = 23 AND estado_produccion_nuevo = 'Lista para entregar'
        THEN 'Pedido terminado y marcado como listo para entregar.'

    WHEN id_factura = 23 AND estado_produccion_nuevo = 'Entregada'
        THEN 'Pedido entregado al cliente con pago completo.'

    WHEN id_factura = 24 AND tipo_evento = 'Factura creada'
        THEN 'Factura registrada pendiente de abono inicial.'

    ELSE comentario
END
WHERE id_factura IN (22, 23, 24);


-- ============================================================
-- 10. LIMPIAR AUDITORÍA DE PRUEBAS
-- ============================================================

TRUNCATE TABLE auditoria RESTART IDENTITY;


-- ============================================================
-- 11. AJUSTAR SECUENCIAS
-- ============================================================

SELECT setval(pg_get_serial_sequence('categoria', 'id_categoria'), GREATEST(COALESCE((SELECT MAX(id_categoria) FROM categoria), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('cliente', 'id_cliente'), GREATEST(COALESCE((SELECT MAX(id_cliente) FROM cliente), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('compra', 'id_compra'), GREATEST(COALESCE((SELECT MAX(id_compra) FROM compra), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('detallecompra', 'id_detalle'), GREATEST(COALESCE((SELECT MAX(id_detalle) FROM detallecompra), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('detallefactura', 'id_detalle'), GREATEST(COALESCE((SELECT MAX(id_detalle) FROM detallefactura), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('factura', 'id_factura'), GREATEST(COALESCE((SELECT MAX(id_factura) FROM factura), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('factura_estado_historial', 'id_historial'), GREATEST(COALESCE((SELECT MAX(id_historial) FROM factura_estado_historial), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('plazo', 'id_plazo'), GREATEST(COALESCE((SELECT MAX(id_plazo) FROM plazo), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('plazo_cuota', 'id_cuota'), GREATEST(COALESCE((SELECT MAX(id_cuota) FROM plazo_cuota), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('producto', 'id_producto'), GREATEST(COALESCE((SELECT MAX(id_producto) FROM producto), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('proveedor', 'id_proveedor'), GREATEST(COALESCE((SELECT MAX(id_proveedor) FROM proveedor), 0) + 1, 1), false);
SELECT setval(pg_get_serial_sequence('usuario', 'id_usuario'), GREATEST(COALESCE((SELECT MAX(id_usuario) FROM usuario), 0) + 1, 1), false);


-- ============================================================
-- 12. VERIFICACIÓN FINAL
-- ============================================================

SELECT 'auditoria' AS tabla, COUNT(*) FROM auditoria
UNION ALL SELECT 'categoria', COUNT(*) FROM categoria
UNION ALL SELECT 'cliente', COUNT(*) FROM cliente
UNION ALL SELECT 'compra', COUNT(*) FROM compra
UNION ALL SELECT 'detallecompra', COUNT(*) FROM detallecompra
UNION ALL SELECT 'detallefactura', COUNT(*) FROM detallefactura
UNION ALL SELECT 'factura', COUNT(*) FROM factura
UNION ALL SELECT 'factura_estado_historial', COUNT(*) FROM factura_estado_historial
UNION ALL SELECT 'plazo', COUNT(*) FROM plazo
UNION ALL SELECT 'plazo_cuota', COUNT(*) FROM plazo_cuota
UNION ALL SELECT 'producto', COUNT(*) FROM producto
UNION ALL SELECT 'proveedor', COUNT(*) FROM proveedor
UNION ALL SELECT 'rol', COUNT(*) FROM rol
UNION ALL SELECT 'seccion', COUNT(*) FROM seccion
UNION ALL SELECT 'usuario', COUNT(*) FROM usuario
ORDER BY tabla;

COMMIT;
