<?php

class FacturaEstadoHistorialRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function obtenerHistorialPorFactura(int $idFactura): array
    {
        $statement = $this->connection->prepare("
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
                fecha_entrega_estimada_anterior,
                fecha_entrega_estimada_nueva,
                comentario,
                fecha_evento
            FROM factura_estado_historial
            WHERE id_factura = :id_factura
            ORDER BY fecha_evento DESC, id_historial DESC
        ");

        $statement->execute([
            ":id_factura" => $idFactura,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialGeneral(): array
    {
        $statement = $this->connection->prepare("
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
                fecha_entrega_estimada_anterior,
                fecha_entrega_estimada_nueva,
                comentario,
                fecha_evento
            FROM factura_estado_historial
            ORDER BY fecha_evento DESC, id_historial DESC
            LIMIT 200
        ");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
