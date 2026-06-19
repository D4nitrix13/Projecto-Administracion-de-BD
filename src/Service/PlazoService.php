<?php

declare(strict_types=1);

namespace App\Service;

class PlazoService
{
    private string $archivo;

    public function __construct()
    {
        $this->archivo = __DIR__ . "/../../storage/system/plazos.json";
        $this->asegurarArchivo();
    }

    public function crearPlan(int $idFactura, float $total, string $fechaLimite, array $cuotas): array
    {
        $plan = $this->obtenerPorFactura($idFactura);

        if ($plan !== null) {
            return [
                "success" => false,
                "message" => "Ya existe un plan de pagos para esta factura"
            ];
        }

        $porcentajeTotal = array_reduce($cuotas, fn($sum, $c) => $sum + ($c["porcentaje"] ?? 0), 0);

        if (abs($porcentajeTotal - 100) > 0.01) {
            return [
                "success" => false,
                "message" => "El porcentaje total debe ser 100% (actual: {$porcentajeTotal}%)"
            ];
        }

        $fechaInicio = new \DateTime();
        $fechaLimiteDt = new \DateTime($fechaLimite);

        if ($fechaLimiteDt <= $fechaInicio) {
            return [
                "success" => false,
                "message" => "La fecha límite debe ser futura"
            ];
        }

        $diasTotal = (int)$fechaInicio->diff($fechaLimiteDt)->days;
        $diasPorCuota = $diasTotal / count($cuotas);

        $cuotasProcesadas = [];
        $montoAcumulado = 0;

        foreach ($cuotas as $index => $cuota) {
            $porcentaje = (float)($cuota["porcentaje"] ?? 0);
            $monto = round($total * $porcentaje / 100, 2);
            $montoAcumulado += $monto;

            $fechaCuota = clone $fechaInicio;
            $fechaCuota->modify("+{$diasPorCuota} days");

            $cuotasProcesadas[] = [
                "numero" => $index + 1,
                "porcentaje" => $porcentaje,
                "monto" => $monto,
                "fecha_pago" => $fechaCuota->format("Y-m-d"),
                "estado" => "Pendiente",
                "fecha_pago_real" => null,
                "monto_pagado_cuota" => 0,
                "observaciones" => $cuota["observaciones"] ?? ""
            ];
        }

        $plan = [
            "id_factura" => $idFactura,
            "total_original" => $total,
            "fecha_creacion" => $fechaInicio->format("Y-m-d\TH:i:s"),
            "fecha_limite" => $fechaLimite,
            "monto_total_pagado" => 0,
            "porcentaje_pagado" => 0,
            "estado" => "Activo",
            "cuotas" => $cuotasProcesadas
        ];

        $this->guardarPlan($plan);

        return [
            "success" => true,
            "message" => "Plan de pagos creado exitosamente",
            "plan" => $plan
        ];
    }

    public function obtenerPorFactura(int $idFactura): ?array
    {
        $planes = $this->leer();

        foreach ($planes as $plan) {
            if ((int)$plan["id_factura"] === $idFactura) {
                return $plan;
            }
        }

        return null;
    }

    public function obtenerTodos(): array
    {
        return $this->leer();
    }

    public function marcarCuotaPagada(int $idFactura, int $numeroCuota, float $montoPagado, ?string $observaciones = null): array
    {
        $plan = $this->obtenerPorFactura($idFactura);

        if ($plan === null) {
            return [
                "success" => false,
                "message" => "No existe plan de pagos para esta factura"
            ];
        }

        $cuotaEncontrada = false;

        foreach ($plan["cuotas"] as &$cuota) {
            if ((int)$cuota["numero"] === $numeroCuota) {
                if ($cuota["estado"] === "Pagado") {
                    return [
                        "success" => false,
                        "message" => "Esta cuota ya fue pagada"
                    ];
                }

                $cuota["estado"] = "Pagado";
                $cuota["fecha_pago_real"] = (new \DateTime())->format("Y-m-d\TH:i:s");
                $cuota["monto_pagado_cuota"] = $montoPagado;

                if ($observaciones !== null) {
                    $cuota["observaciones"] = $observaciones;
                }

                $cuotaEncontrada = true;
                break;
            }
        }

        unset($cuota);

        if (!$cuotaEncontrada) {
            return [
                "success" => false,
                "message" => "Cuota #{$numeroCuota} no encontrada"
            ];
        }

        $plan["monto_total_pagado"] = array_reduce(
            $plan["cuotas"],
            fn($sum, $c) => $sum + ($c["estado"] === "Pagado" ? $c["monto_pagado_cuota"] : 0),
            0
        );

        $plan["porcentaje_pagado"] = round(($plan["monto_total_pagado"] / $plan["total_original"]) * 100, 2);

        $todasPagadas = array_reduce($plan["cuotas"], fn($carry, $c) => $carry && $c["estado"] === "Pagado", true);
        $plan["estado"] = $todasPagadas ? "Completado" : "Activo";

        $this->guardarPlan($plan);

        return [
            "success" => true,
            "message" => "Cuota #{$numeroCuota} marcada como pagada",
            "plan" => $plan
        ];
    }

    public function editarCuota(int $idFactura, int $numeroCuota, ?float $nuevoPorcentaje = null, ?string $nuevaFecha = null): array
    {
        $plan = $this->obtenerPorFactura($idFactura);

        if ($plan === null) {
            return [
                "success" => false,
                "message" => "No existe plan de pagos para esta factura"
            ];
        }

        if ($plan["estado"] === "Completado") {
            return [
                "success" => false,
                "message" => "No se puede editar un plan completado"
            ];
        }

        $cuotaEncontrada = false;

        foreach ($plan["cuotas"] as &$cuota) {
            if ((int)$cuota["numero"] === $numeroCuota) {
                if ($cuota["estado"] === "Pagado") {
                    return [
                        "success" => false,
                        "message" => "No se puede editar una cuota ya pagada"
                    ];
                }

                if ($nuevoPorcentaje !== null) {
                    $cuota["porcentaje"] = $nuevoPorcentaje;
                    $cuota["monto"] = round($plan["total_original"] * $nuevoPorcentaje / 100, 2);
                }

                if ($nuevaFecha !== null) {
                    $cuota["fecha_pago"] = $nuevaFecha;
                }

                $cuotaEncontrada = true;
                break;
            }
        }

        unset($cuota);

        if (!$cuotaEncontrada) {
            return [
                "success" => false,
                "message" => "Cuota #{$numeroCuota} no encontrada"
            ];
        }

        $this->guardarPlan($plan);

        return [
            "success" => true,
            "message" => "Cuota #{$numeroCuota} actualizada",
            "plan" => $plan
        ];
    }

    public function eliminarPlan(int $idFactura): array
    {
        $planes = $this->leer();
        $nuevosPlanes = [];
        $eliminado = false;

        foreach ($planes as $plan) {
            if ((int)$plan["id_factura"] === $idFactura) {
                $eliminado = true;
                continue;
            }
            $nuevosPlanes[] = $plan;
        }

        if (!$eliminado) {
            return [
                "success" => false,
                "message" => "No existe plan de pagos para esta factura"
            ];
        }

        $this->escribir($nuevosPlanes);

        return [
            "success" => true,
            "message" => "Plan de pagos eliminado"
        ];
    }

    public function obtenerCuotasVencidas(): array
    {
        $planes = $this->leer();
        $hoy = (new \DateTime())->format("Y-m-d");
        $vencidas = [];

        foreach ($planes as $plan) {
            if ($plan["estado"] !== "Activo") {
                continue;
            }

            foreach ($plan["cuotas"] as $cuota) {
                if ($cuota["estado"] === "Pendiente" && $cuota["fecha_pago"] < $hoy) {
                    $vencidas[] = [
                        "id_factura" => $plan["id_factura"],
                        "cuota" => $cuota,
                        "dias_vencido" => (int)(new \DateTime($cuota["fecha_pago"]))->diff(new \DateTime($hoy))->days
                    ];
                }
            }
        }

        return $vencidas;
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
                "porcentaje" => $porcentaje,
                "observaciones" => ""
            ];
        }

        $fechaLimite = (new \DateTime())->modify("+{$diasVentana} days")->format("Y-m-d");

        return $this->crearPlan($idFactura, $total, $fechaLimite, $cuotas);
    }

    private function leer(): array
    {
        $contenido = file_get_contents($this->archivo);

        if ($contenido === false || trim($contenido) === "") {
            return [];
        }

        $data = json_decode($contenido, true);

        return is_array($data) ? $data : [];
    }

    private function escribir(array $planes): void
    {
        file_put_contents(
            $this->archivo,
            json_encode($planes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    private function guardarPlan(array $plan): void
    {
        $planes = $this->leer();
        $encontrado = false;

        foreach ($planes as &$p) {
            if ((int)$p["id_factura"] === (int)$plan["id_factura"]) {
                $p = $plan;
                $encontrado = true;
                break;
            }
        }

        unset($p);

        if (!$encontrado) {
            $planes[] = $plan;
        }

        $this->escribir($planes);
    }

    private function asegurarArchivo(): void
    {
        if (!is_file($this->archivo)) {
            file_put_contents($this->archivo, json_encode([], JSON_PRETTY_PRINT));
        }
    }
}