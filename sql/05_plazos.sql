-- ============================================================
-- PLAZOS: Tablas para planes de pago (migración desde JSON)
-- ============================================================

CREATE TABLE IF NOT EXISTS plazo (
    id_plazo        SERIAL PRIMARY KEY,
    id_factura      INT NOT NULL,
    total_original  NUMERIC(10,2) NOT NULL,
    fecha_creacion  TIMESTAMP NOT NULL DEFAULT NOW(),
    fecha_limite    DATE NOT NULL,
    estado          VARCHAR(20) DEFAULT 'Activo' NOT NULL,
    CONSTRAINT chk_plazo_estado CHECK (estado IN ('Activo', 'Completado', 'Cancelado'))
);

CREATE TABLE IF NOT EXISTS plazo_cuota (
    id_cuota        SERIAL PRIMARY KEY,
    id_plazo        INT NOT NULL REFERENCES plazo(id_plazo) ON DELETE CASCADE,
    numero          INT NOT NULL,
    porcentaje      NUMERIC(5,2) NOT NULL,
    monto           NUMERIC(10,2) NOT NULL,
    fecha_pago      DATE NOT NULL,
    estado          VARCHAR(20) DEFAULT 'Pendiente' NOT NULL,
    fecha_pago_real TIMESTAMP,
    monto_pagado    NUMERIC(10,2) DEFAULT 0,
    observaciones   TEXT DEFAULT '',
    CONSTRAINT chk_cuota_estado CHECK (estado IN ('Pendiente', 'Pagado', 'Vencido'))
);

CREATE INDEX IF NOT EXISTS idx_plazo_factura ON plazo(id_factura);
CREATE INDEX IF NOT EXISTS idx_plazo_cuota_plazo ON plazo_cuota(id_plazo);
CREATE INDEX IF NOT EXISTS idx_plazo_cuota_estado ON plazo_cuota(estado);
