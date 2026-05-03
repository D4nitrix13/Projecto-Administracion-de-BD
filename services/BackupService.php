<?php

require_once __DIR__ . "/../lib/backup.php";

class BackupService
{
    private PDO $connection;
    private string $backupDir;
    private string $deleteQueueFile;

    public function __construct(PDO $connection, string $backupDir)
    {
        $this->connection = $connection;
        $this->backupDir = rtrim($backupDir, "/");
        $this->deleteQueueFile = $this->backupDir . "/logs/delete_queue.json";

        $this->asegurarArchivoCola();
    }

    public function generarRespaldoManual(
        ?int $idUsuario,
        ?string $nombrePersonalizado,
        ?string $mensajePersonalizado
    ): array {
        [$ok, $msg] = hacerRespaldo(
            $this->connection,
            "manual",
            $idUsuario,
            $nombrePersonalizado,
            $mensajePersonalizado
        );

        return [
            "success" => $ok,
            "message" => $msg,
        ];
    }

    public function restaurarDesdeArchivo(string $archivo): array
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

        $dbHost = getenv("DB_HOST") ?: "pandas_bd";
        $dbPort = getenv("DB_PORT") ?: "5432";
        $dbUser = getenv("DB_USER") ?: "postgres";
        $dbPass = getenv("DB_PASSWORD") ?: "root";
        $dbName = getenv("DB_NAME") ?: "pandas_estampados_y_kitsune";

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

    public function listarRespaldos(): array
    {
        $this->eliminarRespaldosProgramados();

        $cola = $this->leerColaEliminacion();
        $archivos = [];

        foreach ($this->obtenerDirectoriosBusqueda() as $directorio => $tipo) {
            if (!is_dir($directorio)) {
                continue;
            }

            foreach (glob($directorio . "/*.sql") as $filepath) {
                $nombre = basename($filepath);
                $pendiente = isset($cola[$nombre]);

                $archivos[] = [
                    "nombre" => $nombre,
                    "fecha" => date("Y-m-d H:i:s", filemtime($filepath)),
                    "tamanio" => filesize($filepath),
                    "tipo" => $tipo,
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
            $this->backupDir => "Manual",
            $this->backupDir . "/full" => "Completo",
            $this->backupDir . "/diff" => "Diferencial",
        ];
    }

    private function asegurarArchivoCola(): void
    {
        $directorio = dirname($this->deleteQueueFile);

        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }

        if (!is_file($this->deleteQueueFile)) {
            file_put_contents($this->deleteQueueFile, json_encode([], JSON_PRETTY_PRINT));
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
