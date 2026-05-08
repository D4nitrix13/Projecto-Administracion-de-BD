<?php

class BackupService
{
    private PDO $connection;
    private string $backupDir;
    private string $manualBackupDir;
    private string $fullBackupDir;
    private string $diffBackupDir;
    private string $logsDir;
    private string $deleteQueueFile;
    private string $metadataFile;

    public function __construct(PDO $connection, string $backupDir)
    {
        $this->connection = $connection;

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
        $nombreBase = $this->normalizarNombreArchivo($nombrePersonalizado);

        if ($nombreBase === "") {
            $nombreBase = "pandas_estampados_y_kitsune";
        }

        $fecha = date("Y-m-d_H-i-s");
        $fechaLegible = date("Y-m-d H:i:s");

        $nombreArchivo = "backup_manual_{$nombreBase}_{$fecha}.sql";
        $rutaTemporal = $this->manualBackupDir . "/" . $nombreArchivo . ".tmp";
        $rutaFinal = $this->manualBackupDir . "/" . $nombreArchivo;
        $logFile = $this->logsDir . "/backup_manual_{$fecha}.log";

        $descripcion = trim((string)$mensajePersonalizado);

        $dbHost = getenv("DB_HOST") ?: "postgres";
        $dbPort = getenv("DB_PORT") ?: "5432";
        $dbUser = getenv("DB_USERNAME") ?: (getenv("DB_USER") ?: "postgres");
        $dbPass = getenv("DB_PASSWORD") ?: "root";
        $dbName = getenv("DB_DATABASE") ?: (getenv("DB_NAME") ?: "pandas_estampados_y_kitsune");

        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -p %s -U %s -d %s --clean --if-exists --no-owner --no-privileges > %s 2> %s',
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbName),
            escapeshellarg($rutaTemporal),
            escapeshellarg($logFile)
        );

        $returnCode = 0;
        system($command, $returnCode);

        if ($returnCode !== 0) {
            @unlink($rutaTemporal);

            $detalle = is_file($logFile)
                ? trim((string)file_get_contents($logFile))
                : "No se pudo obtener el detalle del error.";

            return [
                "success" => false,
                "message" => "Error al generar el respaldo manual:\n" . $detalle,
            ];
        }

        if (!is_file($rutaTemporal) || filesize($rutaTemporal) === 0) {
            @unlink($rutaTemporal);

            return [
                "success" => false,
                "message" => "El respaldo manual no se generó correctamente porque el archivo quedó vacío.",
            ];
        }

        rename($rutaTemporal, $rutaFinal);

        $this->guardarMetadataRespaldo($nombreArchivo, [
            "descripcion" => $descripcion,
            "usuario_id" => $idUsuario,
            "tipo" => "Manual",
            "archivo" => $nombreArchivo,
            "ruta" => $rutaFinal,
            "created_at" => $fechaLegible,
        ]);

        $mensajeLog = [
            "Fecha: " . $fechaLegible,
            "Usuario ID: " . ($idUsuario !== null ? (string)$idUsuario : "No disponible"),
            "Archivo: " . $rutaFinal,
            "Descripción: " . ($descripcion !== "" ? $descripcion : "Sin descripción"),
            "Estado: respaldo manual generado correctamente",
        ];

        file_put_contents($logFile, implode(PHP_EOL, $mensajeLog) . PHP_EOL, FILE_APPEND);

        return [
            "success" => true,
            "message" => "Respaldo manual generado correctamente: {$nombreArchivo}",
        ];
    }

    public function restaurarDesdeArchivo(string $archivo, bool $forzarRestauracion = false): array
    {
        $archivo = basename($archivo);
        $filepath = $this->obtenerRutaRespaldo($archivo);

        if ($filepath === null) {
            return [
                "success" => false,
                "message" => "Archivo de respaldo no encontrado.",
            ];
        }

        if (!str_ends_with(strtolower($archivo), ".sql")) {
            return [
                "success" => false,
                "message" => "Solo se permiten archivos de respaldo con extensión .sql.",
            ];
        }

        $analisisRestauracion = $this->analizarRestauracion($archivo);

        if (!$analisisRestauracion["success"]) {
            return $analisisRestauracion;
        }

        if (!$analisisRestauracion["es_ultimo"] && !$forzarRestauracion) {
            return [
                "success" => false,
                "message" => "Este respaldo no es el más reciente. Para restaurarlo debe escribir exactamente FORZAR en la confirmación avanzada.",
            ];
        }

        $dbHost = getenv("DB_HOST") ?: "postgres";
        $dbPort = getenv("DB_PORT") ?: "5432";
        $dbUser = getenv("DB_USERNAME") ?: (getenv("DB_USER") ?: "postgres");
        $dbPass = getenv("DB_PASSWORD") ?: "root";
        $dbName = getenv("DB_DATABASE") ?: (getenv("DB_NAME") ?: "pandas_estampados_y_kitsune");

        $dropResult = $this->limpiarBaseDatos(
            $dbHost,
            $dbPort,
            $dbUser,
            $dbPass,
            $dbName
        );

        if (!$dropResult["success"]) {
            return $dropResult;
        }

        return $this->ejecutarRestore(
            $filepath,
            $archivo,
            $dbHost,
            $dbPort,
            $dbUser,
            $dbPass,
            $dbName
        );
    }

    public function analizarRestauracion(string $archivo): array
    {
        $archivo = basename($archivo);
        $filepath = $this->obtenerRutaRespaldo($archivo);

        if ($filepath === null) {
            return [
                "success" => false,
                "message" => "Archivo de respaldo no encontrado.",
            ];
        }

        if (!str_ends_with(strtolower($archivo), ".sql")) {
            return [
                "success" => false,
                "message" => "Solo se permiten archivos de respaldo con extensión .sql.",
            ];
        }

        $respaldosDisponibles = array_values(array_filter(
            $this->listarRespaldos(),
            fn(array $respaldo): bool => empty($respaldo["deletion_pending"])
        ));

        if (empty($respaldosDisponibles)) {
            return [
                "success" => false,
                "message" => "No hay respaldos disponibles para comparar.",
            ];
        }

        usort(
            $respaldosDisponibles,
            fn(array $a, array $b): int => strcmp((string)$b["fecha"], (string)$a["fecha"])
        );

        $ultimo = $respaldosDisponibles[0];

        $fechaSeleccionadaTimestamp = filemtime($filepath);
        $rutaUltimo = $this->obtenerRutaRespaldo((string)$ultimo["nombre"]);
        $fechaUltimoTimestamp = $rutaUltimo !== null ? filemtime($rutaUltimo) : null;

        if ($fechaSeleccionadaTimestamp === false || $fechaUltimoTimestamp === null || $fechaUltimoTimestamp === false) {
            return [
                "success" => false,
                "message" => "No se pudo validar la fecha del respaldo seleccionado.",
            ];
        }

        $esUltimo = $archivo === $ultimo["nombre"] || $fechaSeleccionadaTimestamp >= $fechaUltimoTimestamp;

        return [
            "success" => true,
            "message" => null,
            "es_ultimo" => $esUltimo,
            "archivo_seleccionado" => $archivo,
            "fecha_seleccionada" => date("Y-m-d H:i:s", $fechaSeleccionadaTimestamp),
            "archivo_ultimo" => $ultimo["nombre"],
            "fecha_ultimo" => $ultimo["fecha"],
        ];
    }

    public function listarRespaldos(): array
    {
        $this->eliminarRespaldosProgramados();

        $cola = $this->leerColaEliminacion();
        $metadata = $this->leerMetadataRespaldos();
        $archivos = [];

        foreach ($this->obtenerDirectoriosBusqueda() as $directorio => $tipo) {
            if (!is_dir($directorio)) {
                continue;
            }

            foreach (glob($directorio . "/*.sql") ?: [] as $filepath) {
                $nombre = basename($filepath);
                $pendiente = isset($cola[$nombre]);
                $datosMetadata = $metadata[$nombre] ?? [];

                $archivos[] = [
                    "nombre" => $nombre,
                    "fecha" => date("Y-m-d H:i:s", filemtime($filepath)),
                    "tamanio" => filesize($filepath),
                    "tipo" => $tipo,
                    "descripcion" => trim((string)($datosMetadata["descripcion"] ?? "")),
                    "mensaje" => trim((string)($datosMetadata["descripcion"] ?? "")),
                    "usuario_id" => $datosMetadata["usuario_id"] ?? null,
                    "metadata_created_at" => $datosMetadata["created_at"] ?? null,
                    "deletion_pending" => $pendiente,
                    "deletion_at" => $pendiente ? ($cola[$nombre]["delete_at"] ?? null) : null,
                ];
            }
        }

        usort(
            $archivos,
            fn(array $a, array $b): int => strcmp($b["fecha"], $a["fecha"])
        );

        return $archivos;
    }

    public function programarEliminacion(string $archivo, int $horas = 24): array
    {
        $archivo = basename($archivo);
        $filepath = $this->obtenerRutaRespaldo($archivo);

        if ($filepath === null) {
            return [
                "success" => false,
                "message" => "No se encontró el archivo que desea eliminar.",
            ];
        }

        $cola = $this->leerColaEliminacion();

        if (isset($cola[$archivo])) {
            return [
                "success" => false,
                "message" => "Este respaldo ya tiene una eliminación programada.",
            ];
        }

        $deleteAt = (new DateTime("+{$horas} hours"))->format("Y-m-d H:i:s");

        $cola[$archivo] = [
            "delete_at" => $deleteAt,
        ];

        $this->guardarColaEliminacion($cola);

        return [
            "success" => true,
            "message" => "Se programó la eliminación de {$archivo} dentro de {$horas} horas.",
        ];
    }

    public function cancelarEliminacion(string $archivo): array
    {
        $archivo = basename($archivo);
        $cola = $this->leerColaEliminacion();

        if (!isset($cola[$archivo])) {
            return [
                "success" => false,
                "message" => "Ese respaldo no tenía un borrado programado.",
            ];
        }

        unset($cola[$archivo]);
        $this->guardarColaEliminacion($cola);

        return [
            "success" => true,
            "message" => "Se canceló el borrado programado de {$archivo}.",
        ];
    }

    public function eliminarRespaldosProgramados(): void
    {
        $cola = $this->leerColaEliminacion();
        $ahora = time();
        $huboCambios = false;

        foreach ($cola as $archivo => $data) {
            $deleteAt = strtotime((string)($data["delete_at"] ?? ""));

            if ($deleteAt === false) {
                unset($cola[$archivo]);
                $huboCambios = true;
                continue;
            }

            if ($ahora >= $deleteAt) {
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

        foreach (array_keys($this->obtenerDirectoriosBusqueda()) as $directorio) {
            $ruta = $directorio . "/" . $archivo;

            if (is_file($ruta)) {
                return $ruta;
            }
        }

        return null;
    }

    private function obtenerDirectoriosBusqueda(): array
    {
        return [
            $this->manualBackupDir => "Manual",
            $this->fullBackupDir => "Completo",
            $this->diffBackupDir => "Diferencial",
        ];
    }

    private function asegurarDirectorios(): void
    {
        foreach (
            [
                $this->backupDir,
                $this->manualBackupDir,
                $this->fullBackupDir,
                $this->diffBackupDir,
                $this->logsDir,
                dirname($this->metadataFile),
            ] as $directorio
        ) {
            if (!is_dir($directorio)) {
                mkdir($directorio, 0775, true);
            }
        }
    }

    private function asegurarArchivoCola(): void
    {
        $this->asegurarDirectorios();

        if (!is_file($this->deleteQueueFile)) {
            file_put_contents(
                $this->deleteQueueFile,
                json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }
    }

    private function asegurarArchivoMetadata(): void
    {
        $this->asegurarDirectorios();

        if (!is_file($this->metadataFile)) {
            file_put_contents(
                $this->metadataFile,
                json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
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
        $this->asegurarArchivoCola();

        file_put_contents(
            $this->deleteQueueFile,
            json_encode($cola, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
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
        $this->asegurarArchivoMetadata();

        file_put_contents(
            $this->metadataFile,
            json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function guardarMetadataRespaldo(string $archivo, array $datos): void
    {
        $archivo = basename($archivo);

        if ($archivo === "") {
            return;
        }

        $metadata = $this->leerMetadataRespaldos();
        $metadata[$archivo] = $datos;

        $this->guardarMetadataRespaldos($metadata);
    }

    private function eliminarMetadataRespaldo(string $archivo): void
    {
        $archivo = basename($archivo);

        if ($archivo === "") {
            return;
        }

        $metadata = $this->leerMetadataRespaldos();

        if (!isset($metadata[$archivo])) {
            return;
        }

        unset($metadata[$archivo]);

        $this->guardarMetadataRespaldos($metadata);
    }

    private function normalizarNombreArchivo(?string $nombre): string
    {
        $nombre = trim((string)$nombre);

        if ($nombre === "") {
            return "";
        }

        $nombre = mb_strtolower($nombre, "UTF-8");

        $reemplazos = [
            "á" => "a",
            "é" => "e",
            "í" => "i",
            "ó" => "o",
            "ú" => "u",
            "ñ" => "n",
        ];

        $nombre = strtr($nombre, $reemplazos);
        $nombre = preg_replace('/[^a-z0-9]+/', '_', $nombre);
        $nombre = trim((string)$nombre, "_");

        return mb_substr($nombre, 0, 80);
    }

    private function limpiarBaseDatos(
        string $dbHost,
        string $dbPort,
        string $dbUser,
        string $dbPass,
        string $dbName
    ): array {
        $command = sprintf(
            'PGPASSWORD=%s psql -v ON_ERROR_STOP=1 -h %s -p %s -U %s -d %s -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" 2>&1',
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbName)
        );

        $output = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                "success" => false,
                "message" => "Error al limpiar la base de datos antes de restaurar:\n" . implode("\n", $output),
            ];
        }

        return [
            "success" => true,
            "message" => null,
        ];
    }

    private function ejecutarRestore(
        string $filepath,
        string $archivo,
        string $dbHost,
        string $dbPort,
        string $dbUser,
        string $dbPass,
        string $dbName
    ): array {
        $command = sprintf(
            'PGPASSWORD=%s psql -v ON_ERROR_STOP=1 -h %s -p %s -U %s -d %s -f %s 2>&1',
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbName),
            escapeshellarg($filepath)
        );

        $output = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                "success" => false,
                "message" => "Error al restaurar la base de datos:\n" . implode("\n", $output),
            ];
        }

        return [
            "success" => true,
            "message" => "Base de datos restaurada correctamente desde {$archivo}.",
        ];
    }
}
