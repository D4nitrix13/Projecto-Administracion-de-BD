<?php

declare(strict_types=1);

namespace App\Repository;

class PlazoRepository
{
    public function __construct(private \PDO $connection) {}

    public function crearPlan(int $idFactura, float $total, string $fechaLimite, array $cuotas): int
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare("
                INSERT INTO plazo (id_factura, total_original, fecha_limite, estado)
                VALUES (:id_factura, :total, :fecha_limite, 'Activo')
                RETURNING id_plazo
            ");

            $stmt->execute([
                ":id_factura"   => $idFactura,
                ":total"        => $total,
                ":fecha_limite" => $fechaLimite,
            ]);

            $idPlazo = (int)$stmt->fetchColumn();

            $stmtCuota = $this->connection->prepare("
                INSERT INTO plazo_cuota (id_plazo, numero, porcentaje, monto, fecha_pago, estado, observaciones)
                VALUES (:id_plazo, :numero, :porcentaje, :monto, :fecha_pago, 'Pendiente', :observaciones)
            ");

            foreach ($cuotas as $cuota) {
                $stmtCuota->execute([
                    ":id_plazo"      => $idPlazo,
                    ":numero"        => $cuota["numero"],
                    ":porcentaje"    => $cuota["porcentaje"],
                    ":monto"         => $cuota["monto"],
                    ":fecha_pago"    => $cuota["fecha_pago"],
                    ":observaciones" => $cuota["observaciones"] ?? "",
                ]);
            }

            $this->connection->commit();

            return $idPlazo;
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function obtenerPlanPorFactura(int $idFactura): ?array
    {
        $stmt = $this->connection->prepare("
            SELECT id_plazo, id_factura, total_original, fecha_creacion, fecha_limite, estado
            FROM plazo
            WHERE id_factura = :id_factura
        ");

        $stmt->execute([":id_factura" => $idFactura]);
        $plan = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$plan) {
            return null;
        }

        $plan["cuotas"] = $this->obtenerCuotasPorPlan((int)$plan["id_plazo"]);

        return $plan;
    }

    public function obtenerCuotasPorPlan(int $idPlazo): array
    {
        $stmt = $this->connection->prepare("
            SELECT id_cuota, numero, porcentaje, monto, fecha_pago, estado, fecha_pago_real, monto_pagado, observaciones
            FROM plazo_cuota
            WHERE id_plazo = :id_plazo
            ORDER BY numero
        ");

        $stmt->execute([":id_plazo" => $idPlazo]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function marcarCuotaPagada(int $idCuota, float $montoPagado, ?string $observaciones = null): void
    {
        $this->connection->prepare("
            UPDATE plazo_cuota
            SET estado = 'Pagado',
                fecha_pago_real = NOW(),
                monto_pagado = :monto,
                observaciones = COALESCE(:observaciones, observaciones)
            WHERE id_cuota = :id_cuota
        ")->execute([
            ":id_cuota"      => $idCuota,
            ":monto"         => $montoPagado,
            ":observaciones" => $observaciones,
        ]);
    }

    public function verificarYCompletarPlan(int $idPlazo): void
    {
        $pendientes = $this->connection->prepare("
            SELECT COUNT(*) FROM plazo_cuota
            WHERE id_plazo = :id_plazo AND estado != 'Pagado'
        ");

        $pendientes->execute([":id_plazo" => $idPlazo]);

        if ((int)$pendientes->fetchColumn() === 0) {
            $this->connection->prepare("
                UPDATE plazo SET estado = 'Completado' WHERE id_plazo = :id_plazo
            ")->execute([":id_plazo" => $idPlazo]);
        }
    }

    public function eliminarPlan(int $idPlazo): void
    {
        $this->connection->prepare("DELETE FROM plazo WHERE id_plazo = :id_plazo")
            ->execute([":id_plazo" => $idPlazo]);
    }

    public function eliminarPlanPorFactura(int $idFactura): void
    {
        $this->connection->prepare("DELETE FROM plazo WHERE id_factura = :id_factura")
            ->execute([":id_factura" => $idFactura]);
    }

    public function existePlan(int $idFactura): bool
    {
        $stmt = $this->connection->prepare("SELECT 1 FROM plazo WHERE id_factura = :id_factura LIMIT 1");
        $stmt->execute([":id_factura" => $idFactura]);

        return (bool)$stmt->fetchColumn();
    }

    public function obtenerCuotasVencidas(): array
    {
        $stmt = $this->connection->prepare("
            SELECT
                pc.id_cuota,
                pc.numero,
                pc.monto,
                pc.fecha_pago,
                pc.estado,
                p.id_factura,
                p.total_original,
                EXTRACT(DAY FROM NOW()::timestamp - pc.fecha_pago::timestamp)::int AS dias_vencido
            FROM plazo_cuota pc
            JOIN plazo p ON p.id_plazo = pc.id_plazo
            WHERE pc.estado = 'Pendiente'
              AND pc.fecha_pago < CURRENT_DATE
              AND p.estado = 'Activo'
            ORDER BY pc.fecha_pago
        ");

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
