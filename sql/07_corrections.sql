BEGIN;

-- ============================================================
-- 1. RESPALDO DE SEGURIDAD
-- ============================================================

DO $$
DECLARE
    sufijo TEXT := to_char(clock_timestamp(), 'YYYYMMDD_HH24MISS');
BEGIN
    EXECUTE format(
        'CREATE TABLE %I AS TABLE factura',
        'backup_factura_' || sufijo
    );

    EXECUTE format(
        'CREATE TABLE %I AS TABLE factura_estado_historial',
        'backup_factura_estado_historial_' || sufijo
    );
END $$;


-- ============================================================
-- 2. VER INCONSISTENCIAS ANTES DE CORREGIR
-- ============================================================

SELECT
    id_factura,
    total,
    monto_pagado,
    saldo_pendiente,
    porcentaje_pagado,
    estado_pago,
    CASE
        WHEN COALESCE(total, 0) <= 0 THEN 'Pagado'
        WHEN COALESCE(monto_pagado, 0) <= 0 THEN 'Pendiente'
        WHEN COALESCE(monto_pagado, 0) < COALESCE(total, 0) THEN 'Parcial'
        ELSE 'Pagado'
    END AS estado_pago_correcto,
    CASE
        WHEN COALESCE(total, 0) <= 0 THEN 0.00
        ELSE ROUND(GREATEST(COALESCE(total, 0) - COALESCE(monto_pagado, 0), 0), 2)
    END AS saldo_correcto
FROM factura
ORDER BY id_factura;


-- ============================================================
-- 3. CORREGIR FACTURAS
-- ============================================================
-- Reglas:
-- total <= 0            => Pagado, saldo 0
-- monto_pagado <= 0     => Pendiente
-- monto_pagado < total  => Parcial
-- monto_pagado >= total => Pagado
-- saldo nunca negativo
-- porcentaje máximo 100

WITH datos_corregidos AS (
    SELECT
        id_factura,

        ROUND(GREATEST(COALESCE(total, 0), 0), 2) AS total_corregido,

        CASE
            WHEN COALESCE(total, 0) <= 0 THEN 0.00
            ELSE ROUND(
                LEAST(
                    GREATEST(COALESCE(monto_pagado, 0), 0),
                    GREATEST(COALESCE(total, 0), 0)
                ),
                2
            )
        END AS monto_pagado_corregido
    FROM factura
),
calculos AS (
    SELECT
        id_factura,
        total_corregido,
        monto_pagado_corregido,

        CASE
            WHEN total_corregido <= 0 THEN 0.00
            ELSE ROUND(GREATEST(total_corregido - monto_pagado_corregido, 0), 2)
        END AS saldo_corregido,

        CASE
            WHEN total_corregido <= 0 THEN 100.00
            ELSE ROUND(
                LEAST((monto_pagado_corregido / NULLIF(total_corregido, 0)) * 100, 100),
                2
            )
        END AS porcentaje_corregido,

        CASE
            WHEN total_corregido <= 0 THEN 'Pagado'
            WHEN monto_pagado_corregido <= 0 THEN 'Pendiente'
            WHEN monto_pagado_corregido < total_corregido THEN 'Parcial'
            ELSE 'Pagado'
        END AS estado_pago_corregido
    FROM datos_corregidos
)
UPDATE factura f
SET
    total = c.total_corregido,
    monto_pagado = c.monto_pagado_corregido,
    saldo_pendiente = c.saldo_corregido,
    porcentaje_pagado = c.porcentaje_corregido,
    estado_pago = c.estado_pago_corregido
FROM calculos c
WHERE f.id_factura = c.id_factura;


-- ============================================================
-- 4. CORREGIR HISTORIAL EXISTENTE
-- ============================================================
-- Esto actualiza los registros viejos del historial para que
-- muestren abono, saldo y estado coherente con la factura actual.

UPDATE factura_estado_historial h
SET
    estado_pago_nuevo = f.estado_pago,
    estado_produccion_nuevo = f.estado_produccion,
    monto_pagado_nuevo = f.monto_pagado,
    saldo_nuevo = f.saldo_pendiente,

    -- Para los registros actuales de prueba, usamos el pagado acumulado
    -- como abono mostrado, porque monto_abonado estaba en 0.00 para todo.
    monto_abonado = CASE
        WHEN f.monto_pagado > 0 THEN f.monto_pagado
        ELSE 0.00
    END,

    comentario = CASE
        WHEN f.total <= 0 THEN
            'Factura sin saldo pendiente. Saldo actual: C$ 0.00.'

        WHEN f.estado_pago = 'Pendiente' THEN
            'Factura pendiente de pago. Saldo actual: C$ ' ||
            to_char(f.saldo_pendiente, 'FM9999999990.00') || '.'

        WHEN f.estado_pago = 'Parcial' THEN
            'Factura parcialmente pagada. Monto pagado acumulado: C$ ' ||
            to_char(f.monto_pagado, 'FM9999999990.00') ||
            '. Saldo actual: C$ ' ||
            to_char(f.saldo_pendiente, 'FM9999999990.00') || '.'

        WHEN f.estado_pago = 'Pagado' THEN
            'Factura marcada como pagada. Saldo actual: C$ 0.00.'

        ELSE
            'Factura actualizada.'
    END
FROM factura f
WHERE h.id_factura = f.id_factura;


-- ============================================================
-- 5. AGREGAR VALIDACIONES A LA TABLA FACTURA
-- ============================================================

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'chk_factura_estado_pago_valido'
    ) THEN
        ALTER TABLE factura
        ADD CONSTRAINT chk_factura_estado_pago_valido
        CHECK (estado_pago IN ('Pendiente', 'Parcial', 'Pagado'));
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'chk_factura_estado_produccion_valido'
    ) THEN
        ALTER TABLE factura
        ADD CONSTRAINT chk_factura_estado_produccion_valido
        CHECK (
            estado_produccion IN (
                'Pendiente',
                'En producción',
                'Lista para entregar',
                'Entregada'
            )
        );
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'chk_factura_montos_no_negativos'
    ) THEN
        ALTER TABLE factura
        ADD CONSTRAINT chk_factura_montos_no_negativos
        CHECK (
            subtotal >= 0
            AND descuento >= 0
            AND impuesto >= 0
            AND total >= 0
            AND monto_pagado >= 0
            AND saldo_pendiente >= 0
            AND porcentaje_pagado >= 0
        );
    END IF;
END $$;


-- ============================================================
-- 6. FUNCIÓN PARA RECALCULAR ESTADO DE PAGO AUTOMÁTICAMENTE
-- ============================================================

CREATE OR REPLACE FUNCTION fn_factura_recalcular_pago()
RETURNS TRIGGER AS $$
DECLARE
    v_total NUMERIC(12,2);
    v_monto_pagado NUMERIC(12,2);
BEGIN
    v_total := ROUND(GREATEST(COALESCE(NEW.total, 0), 0), 2);

    IF v_total <= 0 THEN
        NEW.total := 0.00;
        NEW.monto_pagado := 0.00;
        NEW.saldo_pendiente := 0.00;
        NEW.porcentaje_pagado := 100.00;
        NEW.estado_pago := 'Pagado';
        RETURN NEW;
    END IF;

    v_monto_pagado := ROUND(
        LEAST(
            GREATEST(COALESCE(NEW.monto_pagado, 0), 0),
            v_total
        ),
        2
    );

    NEW.total := v_total;
    NEW.monto_pagado := v_monto_pagado;
    NEW.saldo_pendiente := ROUND(GREATEST(v_total - v_monto_pagado, 0), 2);
    NEW.porcentaje_pagado := ROUND(LEAST((v_monto_pagado / v_total) * 100, 100), 2);

    IF v_monto_pagado <= 0 THEN
        NEW.estado_pago := 'Pendiente';
    ELSIF v_monto_pagado < v_total THEN
        NEW.estado_pago := 'Parcial';
    ELSE
        NEW.estado_pago := 'Pagado';
        NEW.saldo_pendiente := 0.00;
        NEW.porcentaje_pagado := 100.00;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


DROP TRIGGER IF EXISTS trg_factura_recalcular_pago ON factura;

CREATE TRIGGER trg_factura_recalcular_pago
BEFORE INSERT OR UPDATE OF total, monto_pagado
ON factura
FOR EACH ROW
EXECUTE FUNCTION fn_factura_recalcular_pago();


-- ============================================================
-- 7. FUNCIÓN PARA REGISTRAR HISTORIAL AUTOMÁTICAMENTE
-- ============================================================
-- IMPORTANTE:
-- Si tu PHP ya inserta historial manualmente, este trigger puede duplicar
-- registros. Si eso pasa, elimina este trigger y deja solo la lógica en PHP.

CREATE OR REPLACE FUNCTION fn_factura_registrar_historial()
RETURNS TRIGGER AS $$
DECLARE
    v_monto_abonado NUMERIC(12,2);
    v_comentario TEXT;
    v_tipo_evento TEXT;
BEGIN
    IF
        OLD.estado_pago IS NOT DISTINCT FROM NEW.estado_pago
        AND OLD.estado_produccion IS NOT DISTINCT FROM NEW.estado_produccion
        AND OLD.monto_pagado IS NOT DISTINCT FROM NEW.monto_pagado
        AND OLD.saldo_pendiente IS NOT DISTINCT FROM NEW.saldo_pendiente
        AND OLD.fecha_entrega_estimada IS NOT DISTINCT FROM NEW.fecha_entrega_estimada
    THEN
        RETURN NEW;
    END IF;

    v_monto_abonado := ROUND(
        GREATEST(
            COALESCE(NEW.monto_pagado, 0) - COALESCE(OLD.monto_pagado, 0),
            0
        ),
        2
    );

    IF v_monto_abonado > 0 THEN
        v_tipo_evento := 'Abono registrado';

        v_comentario :=
            'Se registró un abono de C$ ' ||
            to_char(v_monto_abonado, 'FM9999999990.00') ||
            '. Saldo actual: C$ ' ||
            to_char(NEW.saldo_pendiente, 'FM9999999990.00') || '.';

    ELSIF OLD.estado_pago IS DISTINCT FROM NEW.estado_pago THEN
        v_tipo_evento := 'Estado de pago actualizado';

        IF NEW.estado_pago = 'Pagado' THEN
            v_comentario :=
                'Factura marcada como pagada. Saldo actual: C$ 0.00.';
        ELSE
            v_comentario :=
                'Estado de pago actualizado de ' ||
                COALESCE(OLD.estado_pago, 'Sin estado') ||
                ' a ' ||
                COALESCE(NEW.estado_pago, 'Sin estado') ||
                '. Saldo actual: C$ ' ||
                to_char(NEW.saldo_pendiente, 'FM9999999990.00') || '.';
        END IF;

    ELSIF OLD.estado_produccion IS DISTINCT FROM NEW.estado_produccion THEN
        v_tipo_evento := 'Estado de producción actualizado';

        v_comentario :=
            'Estado de producción cambiado de ' ||
            COALESCE(OLD.estado_produccion, 'Sin estado') ||
            ' a ' ||
            COALESCE(NEW.estado_produccion, 'Sin estado') || '.';

    ELSIF OLD.fecha_entrega_estimada IS DISTINCT FROM NEW.fecha_entrega_estimada THEN
        v_tipo_evento := 'Fecha de entrega actualizada';

        v_comentario :=
            'Fecha estimada de entrega actualizada de ' ||
            COALESCE(OLD.fecha_entrega_estimada::TEXT, 'Sin fecha') ||
            ' a ' ||
            COALESCE(NEW.fecha_entrega_estimada::TEXT, 'Sin fecha') || '.';

    ELSE
        v_tipo_evento := 'Factura actualizada';

        v_comentario :=
            'Factura actualizada. Saldo actual: C$ ' ||
            to_char(NEW.saldo_pendiente, 'FM9999999990.00') || '.';
    END IF;

    INSERT INTO factura_estado_historial (
        id_factura,
        tipo_evento,
        estado_pago_anterior,
        estado_pago_nuevo,
        estado_produccion_anterior,
        estado_produccion_nuevo,
        monto_pagado_anterior,
        monto_pagado_nuevo,
        monto_abonado,
        saldo_anterior,
        saldo_nuevo,
        fecha_entrega_estimada_anterior,
        fecha_entrega_estimada_nueva,
        comentario,
        fecha_evento
    )
    VALUES (
        NEW.id_factura,
        v_tipo_evento,
        OLD.estado_pago,
        NEW.estado_pago,
        OLD.estado_produccion,
        NEW.estado_produccion,
        OLD.monto_pagado,
        NEW.monto_pagado,
        v_monto_abonado,
        OLD.saldo_pendiente,
        NEW.saldo_pendiente,
        OLD.fecha_entrega_estimada,
        NEW.fecha_entrega_estimada,
        v_comentario,
        NOW()
    );

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


DROP TRIGGER IF EXISTS trg_factura_registrar_historial ON factura;

CREATE TRIGGER trg_factura_registrar_historial
AFTER UPDATE OF estado_pago, estado_produccion, monto_pagado, saldo_pendiente, fecha_entrega_estimada
ON factura
FOR EACH ROW
EXECUTE FUNCTION fn_factura_registrar_historial();


-- ============================================================
-- 8. VERIFICACIÓN FINAL
-- ============================================================

SELECT
    id_factura,
    total,
    monto_pagado,
    saldo_pendiente,
    porcentaje_pagado,
    estado_pago,
    estado_produccion
FROM factura
ORDER BY id_factura;


SELECT
    id_historial,
    id_factura,
    tipo_evento,
    estado_pago_anterior,
    estado_pago_nuevo,
    estado_produccion_anterior,
    estado_produccion_nuevo,
    monto_pagado_anterior,
    monto_pagado_nuevo,
    monto_abonado,
    saldo_anterior,
    saldo_nuevo,
    comentario,
    fecha_evento
FROM factura_estado_historial
ORDER BY id_historial;


-- Si todo se ve bien:
-- COMMIT;

-- Si algo se ve mal:
-- ROLLBACK;

BEGIN;

-- Corregir solo las facturas inconsistentes detectadas
UPDATE factura
SET
    monto_pagado = CASE
        WHEN total <= 0 THEN 0.00
        WHEN monto_pagado < 0 THEN 0.00
        WHEN monto_pagado > total THEN total
        ELSE ROUND(monto_pagado, 2)
    END,
    saldo_pendiente = CASE
        WHEN total <= 0 THEN 0.00
        ELSE ROUND(
            GREATEST(
                total - CASE
                    WHEN monto_pagado < 0 THEN 0.00
                    WHEN monto_pagado > total THEN total
                    ELSE monto_pagado
                END,
                0
            ),
            2
        )
    END,
    porcentaje_pagado = CASE
        WHEN total <= 0 THEN 100.00
        ELSE ROUND(
            LEAST(
                (
                    CASE
                        WHEN monto_pagado < 0 THEN 0.00
                        WHEN monto_pagado > total THEN total
                        ELSE monto_pagado
                    END / total
                ) * 100,
                100
            ),
            2
        )
    END,
    estado_pago = CASE
        WHEN total <= 0 THEN 'Pagado'
        WHEN monto_pagado <= 0 THEN 'Pendiente'
        WHEN monto_pagado < total THEN 'Parcial'
        ELSE 'Pagado'
    END
WHERE id_factura IN (4, 10, 14);

-- Verificar después de corregir
SELECT
    id_factura,
    total,
    monto_pagado,
    saldo_pendiente,
    porcentaje_pagado,
    estado_pago,
    CASE
        WHEN total <= 0 THEN 'Pagado'
        WHEN monto_pagado <= 0 THEN 'Pendiente'
        WHEN monto_pagado < total THEN 'Parcial'
        ELSE 'Pagado'
    END AS estado_correcto,
    ROUND(GREATEST(total - monto_pagado, 0), 2) AS saldo_correcto
FROM factura
WHERE id_factura IN (4, 10, 14)
ORDER BY id_factura;

-- Si todo se ve bien:
COMMIT;

-- Si algo sale raro, en vez de COMMIT usa:
-- ROLLBACK;