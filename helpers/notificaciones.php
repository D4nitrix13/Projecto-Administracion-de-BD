<?php

declare(strict_types=1);

require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../services/NotificacionService.php";

function notificar(string $tipo, string $titulo, string $mensaje, array $opts = []): void
{
    try {
        $service = new \App\Service\NotificacionService();
        $service->crear($tipo, $titulo, $mensaje, $opts);
    } catch (\Throwable $e) {
        error_log("Error creando notificación: " . $e->getMessage());
    }
}

function notificarAdmin(string $tipo, string $titulo, string $mensaje, array $opts = []): void
{
    notificar($tipo, $titulo, $mensaje, array_merge($opts, [
        "id_usuario_destino" => 0,
        "rol_origen" => $opts["rol_origen"] ?? "",
    ]));
}

function notificarSeccion(string $tipo, string $titulo, string $mensaje, int $idSeccion, array $opts = []): void
{
    notificar($tipo, $titulo, $mensaje, array_merge($opts, [
        "id_usuario_destino" => 0,
        "id_seccion" => $idSeccion,
    ]));
}
