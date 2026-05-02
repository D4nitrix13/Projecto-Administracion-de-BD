<?php

require_once __DIR__ . "/../lib/backup.php";

class BackupService
{
    private PDO $connection;
    private string $backupDir;

    public function __construct(PDO $connection, string $backupDir)
    {
        $this->connection = $connection;
        $this->backupDir = $backupDir;
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
        $filepath = $this->backupDir . "/" . $archivo;

        if (!is_file($filepath)) {
            return [
                "success" => false,
                "message" => "Archivo de respaldo no encontrado.",
            ];
        }

        if (!str_ends_with($archivo, ".sql")) {
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
        $archivos = [];

        if (!is_dir($this->backupDir)) {
            return [];
        }

        foreach (glob($this->backupDir . "/*.sql") as $filepath) {
            $archivos[] = [
                "nombre" => basename($filepath),
                "fecha" => date("Y-m-d H:i:s", filemtime($filepath)),
                "tamanio" => filesize($filepath),
            ];
        }

        usort(
            $archivos,
            fn(array $a, array $b): int => strcmp($b["fecha"], $a["fecha"])
        );

        return $archivos;
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
