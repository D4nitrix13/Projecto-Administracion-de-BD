<?php

declare(strict_types=1);

namespace App\Service;

class BackupService
{
    private string $manualBackupDir;
    private string $fullBackupDir;
    private string $diffBackupDir;
    private string $logsDir;
    private string $deleteQueueFile;
    private string $metadataFile;

    public function __construct(
        private \PDO $connection,
        private string $backupDir,
    ) {
        $this->backupDir = rtrim($backupDir, "/");
        $this->manualBackupDir = $this->backupDir . "/manual";
        $this->fullBackupDir = $this->backupDir . "/full";
        $this->diffBackupDir = $this->backupDir . "/diff";
        $this->logsDir = $this->backupDir . "/logs";
        $this->deleteQueueFile = $this->logsDir . "/delete_queue.json";
        $this->metadataFile = dirname($this->backupDir) . "/storage/system/backup_metadata.json";

        $this->asegurarDirectorios();
        $this->asegurarArchivoCola();
        $this->asegurarArchivoMetadata();
    }

    public function generarRespaldoManual(
        ?int $idUsuario,
        ?string $nombrePersonalizado,
        ?string $mensajePersonalizado
    ): array {
        $nombreBase = $this->normalizarNombreArchivo($nombrePersonalizado) ?: "pandas_estampados_y_kitsune";
        $fecha = date("Y-m-d_H-i-s");
        $nombreArchivo = "backup_manual_{$nombreBase}_{$fecha}.sql";
        $rutaTemporal = $this->manualBackupDir . "/" . $nombreArchivo . ".tmp";
        $rutaFinal = $this->manualBackupDir . "/" . $nombreArchivo;
        $logFile = $this->logsDir . "/backup_manual_{$fecha}.log";

        $db = $this->getDbConfig();

        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -p %s -U %s -d %s --clean --if-exists --no-owner --no-privileges > %s 2> %s',
            escapeshellarg($db["password"]),
            escapeshellarg($db["host"]),
            escapeshellarg($db["port"]),
            escapeshellarg($db["username"]),
            escapeshellarg($db["database"]),
            escapeshellarg($rutaTemporal),
            escapeshellarg($logFile)
        );

        $returnCode = 0;
        system($command, $returnCode);

        if ($returnCode !== 0) {
            @unlink($rutaTemporal);
            $detalle = is_file($logFile) ? trim((string) file_get_contents($logFile)) : "Detalle no disponible.";
            return ["success" => false, "message" => "Error al generar respaldo:\n" . $detalle];
        }

        if (!is_file($rutaTemporal) || filesize($rutaTemporal) === 0) {
            @unlink($rutaTemporal);
            return ["success" => false, "message" => "El respaldo quedó vacío."];
        }

        rename($rutaTemporal, $rutaFinal);

        $this->guardarMetadataRespaldo($nombreArchivo, [
            "descripcion" => trim((string) $mensajePersonalizado),
            "usuario_id"  => $idUsuario,
            "tipo"        => "Manual",
            "archivo"     => $nombreArchivo,
            "ruta"        => $rutaFinal,
            "created_at"  => date("Y-m-d H:i:s"),
        ]);

        return ["success" => true, "message" => "Respaldo generado: {$nombreArchivo}"];
    }

    public function restaurarDesdeArchivo(string $archivo, bool $forzar = false): array
    {
        $archivo = basename($archivo);
        $filepath = $this->obtenerRutaRespaldo($archivo);

        if ($filepath === null) {
            return ["success" => false, "message" => "Archivo no encontrado."];
        }

        if (!str_ends_with(strtolower($archivo), ".sql")) {
            return ["success" => false, "message" => "Solo se permiten archivos .sql."];
        }

        $analisis = $this->analizarRestauracion($archivo);

        if (!$analisis["success"]) {
            return $analisis;
        }

        if (!$analisis["es_ultimo"] && !$forzar) {
            return ["success" => false, "message" => "No es el respaldo más reciente. Use FORZAR para restaurarlo."];
        }

        $db = $this->getDbConfig();

        $drop = $this->limpiarBaseDatos($db);

        if (!$drop["success"]) {
            return $drop;
        }

        return $this->ejecutarRestore($filepath, $archivo, $db);
    }

    public function analizarRestauracion(string $archivo): array
    {
        $archivo = basename($archivo);
        $filepath = $this->obtenerRutaRespaldo($archivo);

        if ($filepath === null) {
            return ["success" => false, "message" => "Archivo no encontrado."];
        }

        $respaldos = array_values(array_filter(
            $this->listarRespaldos(),
            fn(array $r): bool => empty($r["deletion_pending"])
        ));

        if (empty($respaldos)) {
            return ["success" => false, "message" => "No hay respaldos disponibles."];
        }

        usort($respaldos, fn(array $a, array $b): int => strcmp((string) $b["fecha"], (string) $a["fecha"]));

        $ultimo = $respaldos[0];
        $fechaSeleccion = filemtime($filepath);
        $rutaUltimo = $this->obtenerRutaRespaldo((string) $ultimo["nombre"]);
        $fechaUltimo = $rutaUltimo !== null ? filemtime($rutaUltimo) : null;

        if ($fechaSeleccion === false || $fechaUltimo === null || $fechaUltimo === false) {
            return ["success" => false, "message" => "No se pudo validar la fecha."];
        }

        return [
            "success"              => true,
            "message"              => null,
            "es_ultimo"            => $archivo === $ultimo["nombre"] || $fechaSeleccion >= $fechaUltimo,
            "archivo_seleccionado" => $archivo,
            "fecha_seleccionada"   => date("Y-m-d H:i:s", $fechaSeleccion),
            "archivo_ultimo"       => $ultimo["nombre"],
            "fecha_ultimo"         => $ultimo["fecha"],
        ];
    }

    public function listarRespaldos(): array
    {
        $this->eliminarRespaldosProgramados();

        $cola = $this->leerColaEliminacion();
        $metadata = $this->leerMetadataRespaldos();
        $archivos = [];

        foreach ($this->obtenerDirectoriosBusqueda() as $directorio => $tipo) {
            if (!is_dir($directorio)) continue;

            foreach (glob($directorio . "/*.sql") ?: [] as $filepath) {
                $nombre = basename($filepath);
                $pendiente = isset($cola[$nombre]);
                $datos = $metadata[$nombre] ?? [];

                $archivos[] = [
                    "nombre"               => $nombre,
                    "fecha"                => date("Y-m-d H:i:s", filemtime($filepath)),
                    "tamanio"              => filesize($filepath),
                    "tipo"                 => $tipo,
                    "descripcion"          => trim((string) ($datos["descripcion"] ?? "")),
                    "mensaje"              => trim((string) ($datos["descripcion"] ?? "")),
                    "usuario_id"           => $datos["usuario_id"] ?? null,
                    "metadata_created_at"  => $datos["created_at"] ?? null,
                    "deletion_pending"     => $pendiente,
                    "deletion_at"          => $pendiente ? ($cola[$nombre]["delete_at"] ?? null) : null,
                ];
            }
        }

        usort($archivos, fn(array $a, array $b): int => strcmp($b["fecha"], $a["fecha"]));

        return $archivos;
    }

    public function programarEliminacion(string $archivo, int $horas = 24): array
    {
        $archivo = basename($archivo);
        $filepath = $this->obtenerRutaRespaldo($archivo);

        if ($filepath === null) {
            return ["success" => false, "message" => "Archivo no encontrado."];
        }

        $cola = $this->leerColaEliminacion();

        if (isset($cola[$archivo])) {
            return ["success" => false, "message" => "Ya tiene eliminación programada."];
        }

        $cola[$archivo] = ["delete_at" => (new \DateTime("+{$horas} hours"))->format("Y-m-d H:i:s")];
        $this->guardarColaEliminacion($cola);

        return ["success" => true, "message" => "Eliminación programada en {$horas} horas."];
    }

    public function cancelarEliminacion(string $archivo): array
    {
        $archivo = basename($archivo);
        $cola = $this->leerColaEliminacion();

        if (!isset($cola[$archivo])) {
            return ["success" => false, "message" => "No tenía borrado programado."];
        }

        unset($cola[$archivo]);
        $this->guardarColaEliminacion($cola);

        return ["success" => true, "message" => "Borrado cancelado."];
    }

    public function eliminarRespaldosProgramados(): void
    {
        $cola = $this->leerColaEliminacion();
        $ahora = time();
        $huboCambios = false;

        foreach ($cola as $archivo => $data) {
            $deleteAt = strtotime((string) ($data["delete_at"] ?? ""));

            if ($deleteAt === false || $ahora >= $deleteAt) {
                $filepath = $this->obtenerRutaRespaldo($archivo);

                if ($filepath !== null && is_file($filepath)) {
                    @unlink($filepath);
                }

                $this->eliminarMetadataRespaldo($archivo);
                unset($cola[$archivo]);
                $huboCambios = true;
            }
        }

        if ($huboCambios) {
            $this->guardarColaEliminacion($cola);
        }
    }

    public function obtenerRutaRespaldo(string $archivo): ?string
    {
        $archivo = basename($archivo);

        if ($archivo === "" || !str_ends_with(strtolower($archivo), ".sql")) {
            return null;
        }

        foreach (array_keys($this->obtenerDirectoriosBusqueda()) as $dir) {
            $ruta = $dir . "/" . $archivo;

            if (is_file($ruta)) {
                return $ruta;
            }
        }

        return null;
    }

    private function getDbConfig(): array
    {
        return [
            "host"     => getenv("DB_HOST") ?: "postgres",
            "port"     => getenv("DB_PORT") ?: "5432",
            "username" => getenv("DB_USERNAME") ?: "postgres",
            "password" => getenv("DB_PASSWORD") ?: "root",
            "database" => getenv("DB_DATABASE") ?: "pandas_estampados_y_kitsune",
        ];
    }

    private function obtenerDirectoriosBusqueda(): array
    {
        return [
            $this->manualBackupDir => "Manual",
            $this->fullBackupDir   => "Completo",
            $this->diffBackupDir   => "Diferencial",
        ];
    }

    private function asegurarDirectorios(): void
    {
        foreach ([$this->backupDir, $this->manualBackupDir, $this->fullBackupDir, $this->diffBackupDir, $this->logsDir, dirname($this->metadataFile)] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }
    }

    private function asegurarArchivoCola(): void
    {
        $this->asegurarDirectorios();

        if (!is_file($this->deleteQueueFile)) {
            file_put_contents($this->deleteQueueFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    private function asegurarArchivoMetadata(): void
    {
        $this->asegurarDirectorios();

        if (!is_file($this->metadataFile)) {
            file_put_contents($this->metadataFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    private function leerColaEliminacion(): array
    {
        $this->asegurarArchivoCola();
        $contenido = file_get_contents($this->deleteQueueFile);

        if ($contenido === false || trim($contenido) === "") {
            return [];
        }

        $data = json_decode($contenido, true);

        return is_array($data) ? $data : [];
    }

    private function guardarColaEliminacion(array $cola): void
    {
        file_put_contents($this->deleteQueueFile, json_encode($cola, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function leerMetadataRespaldos(): array
    {
        $this->asegurarArchivoMetadata();
        $contenido = file_get_contents($this->metadataFile);

        if ($contenido === false || trim($contenido) === "") {
            return [];
        }

        $data = json_decode($contenido, true);

        return is_array($data) ? $data : [];
    }

    private function guardarMetadataRespaldos(array $metadata): void
    {
        file_put_contents($this->metadataFile, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function guardarMetadataRespaldo(string $archivo, array $datos): void
    {
        $archivo = basename($archivo);

        if ($archivo === "") return;

        $metadata = $this->leerMetadataRespaldos();
        $metadata[$archivo] = $datos;
        $this->guardarMetadataRespaldos($metadata);
    }

    private function eliminarMetadataRespaldo(string $archivo): void
    {
        $archivo = basename($archivo);

        if ($archivo === "") return;

        $metadata = $this->leerMetadataRespaldos();
        unset($metadata[$archivo]);
        $this->guardarMetadataRespaldos($metadata);
    }

    private function normalizarNombreArchivo(?string $nombre): string
    {
        $nombre = trim((string) $nombre);

        if ($nombre === "") return "";

        $nombre = mb_strtolower($nombre, "UTF-8");
        $nombre = strtr($nombre, ["á" => "a", "é" => "e", "í" => "i", "ó" => "o", "ú" => "u", "ñ" => "n"]);
        $nombre = preg_replace('/[^a-z0-9]+/', '_', $nombre);

        return mb_substr(trim((string) $nombre, "_"), 0, 80);
    }

    private function limpiarBaseDatos(array $db): array
    {
        $command = sprintf(
            'PGPASSWORD=%s psql -v ON_ERROR_STOP=1 -h %s -p %s -U %s -d %s -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" 2>&1',
            escapeshellarg($db["password"]),
            escapeshellarg($db["host"]),
            escapeshellarg($db["port"]),
            escapeshellarg($db["username"]),
            escapeshellarg($db["database"])
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return ["success" => false, "message" => "Error al limpiar BD:\n" . implode("\n", $output)];
        }

        return ["success" => true, "message" => null];
    }

    private function ejecutarRestore(string $filepath, string $archivo, array $db): array
    {
        $command = sprintf(
            'PGPASSWORD=%s psql -v ON_ERROR_STOP=1 -h %s -p %s -U %s -d %s -f %s 2>&1',
            escapeshellarg($db["password"]),
            escapeshellarg($db["host"]),
            escapeshellarg($db["port"]),
            escapeshellarg($db["username"]),
            escapeshellarg($db["database"]),
            escapeshellarg($filepath)
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return ["success" => false, "message" => "Error al restaurar:\n" . implode("\n", $output)];
        }

        return ["success" => true, "message" => "BD restaurada desde {$archivo}."];
    }
}
