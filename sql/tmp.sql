CREATE TABLE IF NOT EXISTS factura_estado_historial (
    id_historial SERIAL PRIMARY KEY,
    id_factura INTEGER NOT NULL REFERENCES factura(id_factura) ON DELETE CASCADE,

    tipo_evento VARCHAR(80) NOT NULL,

    estado_pago_anterior VARCHAR(50),
    estado_pago_nuevo VARCHAR(50),

    estado_produccion_anterior VARCHAR(80),
    estado_produccion_nuevo VARCHAR(80),

    monto_pagado_anterior NUMERIC(12, 2),
    monto_pagado_nuevo NUMERIC(12, 2),
    monto_abonado NUMERIC(12, 2) DEFAULT 0,

    saldo_anterior NUMERIC(12, 2),
    saldo_nuevo NUMERIC(12, 2),

    fecha_entrega_estimada_anterior DATE,
    fecha_entrega_estimada_nueva DATE,

    comentario TEXT,

    fecha_evento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_factura_estado_historial_factura
ON factura_estado_historial(id_factura);

CREATE OR REPLACE FUNCTION registrar_historial_estado_factura()
RETURNS TRIGGER AS $$
DECLARE
    v_tipo_evento VARCHAR(80);
    v_monto_abonado NUMERIC(12, 2);
    v_comentario TEXT;
BEGIN
    IF TG_OP = 'INSERT' THEN
        INSERT INTO factura_estado_historial (
            id_factura,
            tipo_evento,
            estado_pago_nuevo,
            estado_produccion_nuevo,
            monto_pagado_nuevo,
            saldo_nuevo,
            fecha_entrega_estimada_nueva,
            comentario
        ) VALUES (
            NEW.id_factura,
            'Factura creada',
            NEW.estado_pago,
            NEW.estado_produccion,
            NEW.monto_pagado,
            NEW.saldo_pendiente,
            NEW.fecha_entrega_estimada,
            'La factura fue registrada en el sistema.'
        );

        RETURN NEW;
    END IF;

    IF TG_OP = 'UPDATE' THEN
        v_tipo_evento := 'Factura actualizada';
        v_monto_abonado := GREATEST(COALESCE(NEW.monto_pagado, 0) - COALESCE(OLD.monto_pagado, 0), 0);

        IF COALESCE(NEW.monto_pagado, 0) <> COALESCE(OLD.monto_pagado, 0) THEN
            v_tipo_evento := 'Pago actualizado';
        END IF;

        IF COALESCE(NEW.estado_produccion, '') <> COALESCE(OLD.estado_produccion, '') THEN
            v_tipo_evento := 'Estado de producción actualizado';
        END IF;

        IF NEW.estado_produccion = 'Cancelada' AND OLD.estado_produccion IS DISTINCT FROM NEW.estado_produccion THEN
            v_tipo_evento := 'Factura cancelada';

            IF NEW.fecha_entrega_estimada IS NOT NULL AND CURRENT_DATE <= NEW.fecha_entrega_estimada THEN
                v_comentario := 'El cliente canceló antes o en la fecha estimada de entrega.';
            ELSE
                v_comentario := 'El cliente canceló después de la fecha estimada de entrega.';
            END IF;
        ELSE
            v_comentario := 'Cambio registrado automáticamente por el sistema.';
        END IF;

        IF
            COALESCE(NEW.estado_pago, '') <> COALESCE(OLD.estado_pago, '')
            OR COALESCE(NEW.estado_produccion, '') <> COALESCE(OLD.estado_produccion, '')
            OR COALESCE(NEW.monto_pagado, 0) <> COALESCE(OLD.monto_pagado, 0)
            OR COALESCE(NEW.saldo_pendiente, 0) <> COALESCE(OLD.saldo_pendiente, 0)
            OR NEW.fecha_entrega_estimada IS DISTINCT FROM OLD.fecha_entrega_estimada
        THEN
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
                comentario
            ) VALUES (
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
                v_comentario
            );
        END IF;

        RETURN NEW;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_factura_estado_historial ON factura;

CREATE TRIGGER trg_factura_estado_historial
AFTER INSERT OR UPDATE ON factura
FOR EACH ROW
EXECUTE FUNCTION registrar_historial_estado_factura();