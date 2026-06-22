<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PlazoRepository;

class PlazoService
{
    public function __construct(
        private PlazoRepository $repo,
    ) {}

    public function crearPlan(int $idFactura, float $total, string $fechaLimite, array $cuotas): array
    {
        if ($this->repo->existePlan($idFactura)) {
            return [
                "success" => false,
                "message" => "Ya existe un plan de pagos para esta factura",
            ];
        }

        $porcentajeTotal = array_reduce($cuotas, fn($sum, $c) => $sum + ($c["porcentaje"] ?? 0), 0);

        if (abs($porcentajeTotal - 100) > 0.01) {
            return [
                "success" => false,
                "message" => "El porcentaje total debe ser 100% (actual: {$porcentajeTotal}%)",
            ];
        }

        $fechaInicio = new \DateTime();
        $fechaLimiteDt = new \DateTime($fechaLimite);

        if ($fechaLimiteDt <= $fechaInicio) {
            return [
                "success" => false,
                "message" => "La fecha límite debe ser futura",
            ];
        }

        $diasTotal = (int)$fechaInicio->diff($fechaLimiteDt)->days;
        $diasPorCuota = $diasTotal / count($cuotas);

        $cuotasProcesadas = [];

        foreach ($cuotas as $index => $cuota) {
            $porcentaje = (float)($cuota["porcentaje"] ?? 0);
            $monto = round($total * $porcentaje / 100, 2);

            $fechaCuota = clone $fechaInicio;
            $fechaCuota->modify("+{$diasPorCuota} days");

            $cuotasProcesadas[] = [
                "numero"        => $index + 1,
                "porcentaje"    => $porcentaje,
                "monto"         => $monto,
                "fecha_pago"    => $fechaCuota->format("Y-m-d"),
                "observaciones" => $cuota["observaciones"] ?? "",
            ];
        }

        $idPlazo = $this->repo->crearPlan($idFactura, $total, $fechaLimite, $cuotasProcesadas);

        return [
            "success" => true,
            "message" => "Plan de pagos creado exitosamente",
            "id_plazo" => $idPlazo,
        ];
    }

    public function obtenerPorFactura(int $idFactura): ?array
    {
        return $this->repo->obtenerPlanPorFactura($idFactura);
    }

    public function marcarCuotaPagada(int $idCuota, float $montoPagado, ?string $observaciones = null): array
    {
        $this->repo->marcarCuotaPagada($idCuota, $montoPagado, $observaciones);

        return [
            "success" => true,
            "message" => "Cuota marcada como pagada",
        ];
    }

    public function eliminarPlanPorFactura(int $idFactura): array
    {
        $this->repo->eliminarPlanPorFactura($idFactura);

        return [
            "success" => true,
            "message" => "Plan de pagos eliminado",
        ];
    }

    public function generarPlanAutomatico(int $idFactura, float $total, int $numCuotas, int $diasVentana = 15): array
    {
        $cuotas = [];
        $porcentajePorCuota = round(100 / $numCuotas, 2);
        $resto = 100 - ($porcentajePorCuota * $numCuotas);

        for ($i = 0; $i < $numCuotas; $i++) {
            $porcentaje = $porcentajePorCuota;

            if ($i === $numCuotas - 1 && abs($resto) > 0.01) {
                $porcentaje = round($porcentaje + $resto, 2);
            }

            $cuotas[] = [
                "porcentaje"    => $porcentaje,
                "observaciones" => "",
            ];
        }

        $fechaLimite = (new \DateTime())->modify("+{$diasVentana} days")->format("Y-m-d");

        return $this->crearPlan($idFactura, $total, $fechaLimite, $cuotas);
    }

    public function obtenerCuotasVencidas(): array
    {
        return $this->repo->obtenerCuotasVencidas();
    }
}
