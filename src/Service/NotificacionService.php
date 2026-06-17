<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\NotificacionRepository;

class NotificacionService
{
    private NotificacionRepository $repo;

    public function __construct()
    {
        $this->repo = new NotificacionRepository();
    }

    public function crear(string $tipo, string $titulo, string $mensaje, array $opts = []): void
    {
        $idDestino = $opts["id_usuario_destino"] ?? 0;

        if ($idDestino <= 0 && isset($_SESSION["user"]["id_usuario"])) {
            $idDestino = (int) $_SESSION["user"]["id_usuario"];
        }

        $notif = [
            "id"                => bin2hex(random_bytes(16)),
            "tipo"              => $tipo,
            "titulo"            => $titulo,
            "mensaje"           => $mensaje,
            "leida"             => false,
            "fecha"             => date("Y-m-d\TH:i:s"),
            "fecha_lectura"     => null,
            "id_usuario_origen" => $opts["id_usuario_origen"] ?? null,
            "id_usuario_destino"=> $idDestino,
            "id_seccion"        => $opts["id_seccion"] ?? null,
            "rol_origen"        => $opts["rol_origen"] ?? "",
            "metadata"          => $opts["metadata"] ?? [],
        ];

        $this->repo->guardar($notif);
    }

    public function obtenerParaUsuario(int $idUsuario, ?int $idSeccion = null): array
    {
        $notificaciones = $this->repo->obtenerPorUsuario($idUsuario, $idSeccion);

        usort($notificaciones, fn(array $a, array $b): int => strcmp($b["fecha"], $a["fecha"]));

        return $notificaciones;
    }

    public function contarSinLeer(int $idUsuario): int
    {
        return $this->repo->contarSinLeer($idUsuario);
    }

    public function marcarLeida(string $id, int $idUsuario): bool
    {
        return $this->repo->marcarLeida($id, $idUsuario);
    }

    public function marcarTodasLeidas(int $idUsuario): int
    {
        return $this->repo->marcarTodasLeidas($idUsuario);
    }

    public function eliminar(string $id, int $idUsuario, bool $esAdmin): bool
    {
        return $this->repo->eliminar($id, $idUsuario, $esAdmin);
    }

    public function limpiarHistorial(): bool
    {
        return $this->repo->limpiarHistorial();
    }

    public function tipoLabel(string $tipo): string
    {
        return match ($tipo) {
            "factura_creada"        => "Factura creada",
            "pago_recibido"         => "Pago recibido",
            "factura_pagada"        => "Factura pagada",
            "produccion_cambiada"   => "Producción actualizada",
            "factura_cancelada"     => "Factura cancelada",
            "cliente_creado"        => "Nuevo cliente",
            "cliente_eliminado"     => "Cliente eliminado",
            "producto_creado"       => "Nuevo producto",
            "producto_eliminado"    => "Producto eliminado",
            "stock_bajo"            => "Stock bajo",
            "compra_registrada"     => "Compra registrada",
            "backup_manual"         => "Backup manual",
            "backup_automatico"     => "Backup automático",
            "backup_restaurado"     => "BD restaurada",
            "mantenimiento_bd"      => "Mantenimiento BD",
            "usuario_creado"        => "Nuevo usuario",
            "usuario_eliminado"     => "Usuario eliminado",
            default                 => $tipo,
        };
    }

    public function tipoIcono(string $tipo): string
    {
        return match ($tipo) {
            "factura_creada"        => "receipt",
            "pago_recibido"         => "dollar-sign",
            "factura_pagada"        => "check-circle",
            "produccion_cambiada"   => "truck",
            "factura_cancelada"     => "x-circle",
            "cliente_creado"        => "user-plus",
            "cliente_eliminado"     => "user-minus",
            "producto_creado"       => "package",
            "producto_eliminado"    => "trash-2",
            "stock_bajo"            => "alert-triangle",
            "compra_registrada"     => "shopping-cart",
            "backup_manual"         => "hard-drive",
            "backup_automatico"     => "clock",
            "backup_restaurado"     => "refresh-cw",
            "mantenimiento_bd"      => "settings",
            "usuario_creado"        => "user-plus",
            "usuario_eliminado"     => "user-minus",
            default                 => "bell",
        };
    }

    public function tipoColor(string $tipo): string
    {
        return match ($tipo) {
            "factura_creada"        => "#2563eb",
            "pago_recibido"         => "#16a34a",
            "factura_pagada"        => "#16a34a",
            "produccion_cambiada"   => "#d97706",
            "factura_cancelada"     => "#dc2626",
            "cliente_creado"        => "#7c3aed",
            "cliente_eliminado"     => "#dc2626",
            "producto_creado"       => "#0891b2",
            "producto_eliminado"    => "#dc2626",
            "stock_bajo"            => "#ea580c",
            "compra_registrada"     => "#2563eb",
            "backup_manual"         => "#16a34a",
            "backup_automatico"     => "#0369a1",
            "backup_restaurado"     => "#7c3aed",
            "mantenimiento_bd"      => "#6b7280",
            "usuario_creado"        => "#7c3aed",
            "usuario_eliminado"     => "#dc2626",
            default                 => "#6b7280",
        };
    }
}
