<?php

declare(strict_types=1);

namespace App\Repository;

class NotificacionRepository
{
    private string $archivo;

    public function __construct()
    {
        $this->archivo = __DIR__ . "/../../storage/system/notificaciones.json";
        $this->asegurarArchivo();
    }

    public function obtenerTodas(): array
    {
        return $this->leer();
    }

    public function obtenerPorUsuario(int $idUsuario, ?int $idSeccion = null): array
    {
        $notificaciones = $this->leer();

        return array_values(array_filter(
            $notificaciones,
            function (array $n) use ($idUsuario, $idSeccion): bool {
                $esAdmin = ($n["rol_origen"] ?? "") === "Administrador";
                $esDestino = (int)($n["id_usuario_destino"] ?? 0) === $idUsuario;
                $mismaSeccion = $idSeccion === null
                    || (int)($n["id_seccion"] ?? 0) === $idSeccion
                    || $esAdmin;

                return $esDestino || $mismaSeccion;
            }
        ));
    }

    public function obtenerPorId(string $id): ?array
    {
        $notificaciones = $this->leer();

        foreach ($notificaciones as $n) {
            if ($n["id"] === $id) {
                return $n;
            }
        }

        return null;
    }

    public function contarSinLeer(int $idUsuario): int
    {
        $notificaciones = $this->leer();
        $count = 0;

        foreach ($notificaciones as $n) {
            if ((int)($n["id_usuario_destino"] ?? 0) === $idUsuario && empty($n["leida"])) {
                $count++;
            }
        }

        return $count;
    }

    public function guardar(array $notificacion): void
    {
        $notificaciones = $this->leer();
        $notificaciones[] = $notificacion;
        $this->escribir($notificaciones);
    }

    public function marcarLeida(string $id, int $idUsuario): bool
    {
        $notificaciones = $this->leer();

        foreach ($notificaciones as &$n) {
            if ($n["id"] === $id && (int)($n["id_usuario_destino"] ?? 0) === $idUsuario) {
                $n["leida"] = true;
                $n["fecha_lectura"] = date("Y-m-d\TH:i:s");
                $this->escribir($notificaciones);
                return true;
            }
        }

        return false;
    }

    public function marcarTodasLeidas(int $idUsuario): int
    {
        $notificaciones = $this->leer();
        $marcadas = 0;

        foreach ($notificaciones as &$n) {
            if ((int)($n["id_usuario_destino"] ?? 0) === $idUsuario && empty($n["leida"])) {
                $n["leida"] = true;
                $n["fecha_lectura"] = date("Y-m-d\TH:i:s");
                $marcadas++;
            }
        }

        if ($marcadas > 0) {
            $this->escribir($notificaciones);
        }

        return $marcadas;
    }

    public function eliminar(string $id, int $idUsuario, bool $esAdmin): bool
    {
        $notificaciones = $this->leer();
        $nuevo = [];

        foreach ($notificaciones as $n) {
            if ($n["id"] === $id) {
                if (!$esAdmin && (int)($n["id_usuario_destino"] ?? 0) !== $idUsuario) {
                    $nuevo[] = $n;
                    continue;
                }
                continue;
            }
            $nuevo[] = $n;
        }

        if (count($nuevo) === count($notificaciones)) {
            return false;
        }

        $this->escribir($nuevo);
        return true;
    }

    public function limpiarHistorial(): bool
    {
        $this->escribir([]);
        return true;
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

    private function escribir(array $notificaciones): void
    {
        file_put_contents(
            $this->archivo,
            json_encode($notificaciones, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    private function asegurarArchivo(): void
    {
        if (!is_file($this->archivo)) {
            file_put_contents($this->archivo, json_encode([], JSON_PRETTY_PRINT));
        }
    }
}
