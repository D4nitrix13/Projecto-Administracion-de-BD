BEGIN;

-- ============================================================
-- Permitir eliminar proveedores aunque tengan productos/compras
-- sin borrar historial comercial.
--
-- Regla:
-- - Producto conserva el registro, pero queda sin proveedor.
-- - Compra conserva el registro, pero queda sin proveedor.
-- - Proveedor se elimina físicamente.
-- ============================================================

-- 1. compra.id_proveedor actualmente es NOT NULL.
--    Se vuelve nullable para conservar compras históricas.
ALTER TABLE compra
ALTER COLUMN id_proveedor DROP NOT NULL;

-- 2. Rehacer FK de compra -> proveedor con ON DELETE SET NULL.
ALTER TABLE compra
DROP CONSTRAINT IF EXISTS fk_compra_proveedor;

ALTER TABLE compra
ADD CONSTRAINT fk_compra_proveedor
FOREIGN KEY (id_proveedor)
REFERENCES proveedor(id_proveedor)
ON DELETE SET NULL;

-- 3. Rehacer FK de producto -> proveedor con ON DELETE SET NULL.
ALTER TABLE producto
DROP CONSTRAINT IF EXISTS fk_producto_proveedor;

ALTER TABLE producto
ADD CONSTRAINT fk_producto_proveedor
FOREIGN KEY (id_proveedor)
REFERENCES proveedor(id_proveedor)
ON DELETE SET NULL;

-- 4. Reemplazar función usada por el sistema.
CREATE OR REPLACE FUNCTION eliminar_proveedor_sistema(p_id_proveedor integer)
RETURNS boolean
LANGUAGE plpgsql
AS $$
DECLARE
    v_existe boolean;
BEGIN
    IF p_id_proveedor IS NULL OR p_id_proveedor <= 0 THEN
        RAISE EXCEPTION 'ID de proveedor no válido';
    END IF;

    SELECT EXISTS (
        SELECT 1
        FROM proveedor
        WHERE id_proveedor = p_id_proveedor
    )
    INTO v_existe;

    IF NOT v_existe THEN
        RETURN false;
    END IF;

    UPDATE producto
    SET id_proveedor = NULL
    WHERE id_proveedor = p_id_proveedor;

    UPDATE compra
    SET id_proveedor = NULL
    WHERE id_proveedor = p_id_proveedor;

    DELETE FROM proveedor
    WHERE id_proveedor = p_id_proveedor;

    RETURN true;
END;
$$;

COMMIT;
