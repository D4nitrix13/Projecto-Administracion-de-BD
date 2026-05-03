-- ============================================================
-- FUNCIÓN: Obtener información de imagen de producto
-- Sistema: Panda Estampados / Kitsune
-- Uso: partials/ver-imagen-productos/queries.php
-- ============================================================

CREATE OR REPLACE FUNCTION obtener_producto_imagen(
    p_id_producto INT
)
RETURNS TABLE (
    id_producto INT,
    codigo VARCHAR,
    nombre VARCHAR,
    descripcion TEXT,
    imagen VARCHAR,
    precio_venta NUMERIC,
    stock INT
)
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