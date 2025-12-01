<?php

/**
 * utils/cron_backup.php
 *
 * Script para generar un respaldo de la base de datos PostgreSQL.
 * El archivo se guarda en /app/backups con nombre:
 *
 *   backup_NOMBREBD_YYYY-MM-DD_HH-MM-SS.sql
 *
 * Ejemplo:
 *   backup_pandas_estampados_y_kitsune_2025-02-14_13-50-20.sql
 */

date_default_timezone_set('America/Managua');

/* == CONFIGURACIÓN BÁSICA ========================= */

$dbHost = '172.17.0.3';
$dbPort = '5432';
$dbName = 'pandas_estampados_y_kitsune';
$dbUser = 'postgres';
$dbPass = 'root';

// Carpeta donde se guardarán los respaldos
$backupDir = __DIR__ . '/../backups';

// Opcional: días a conservar (para limpiar respaldos viejos)
$DAYS_TO_KEEP = 7;

/* == CREAR DIRECTORIO SI NO EXISTE ================= */

if (!is_dir($backupDir)) {
    if (!mkdir($backupDir, 0775, true) && !is_dir($backupDir)) {
        fwrite(STDERR, "[ERROR] No se pudo crear el directorio de backups: $backupDir\n");
        exit(1);
    }
}

/* == GENERAR NOMBRE DE ARCHIVO ===================== */

// Formato: backup_NOMBREBD_YYYY-MM-DD_HH-MM-SS.sql
$timestamp = date('Y-m-d_H-i-s');
$filename  = "backup_automatico_{$dbName}_{$timestamp}.sql";
$filepath  = $backupDir . '/' . $filename;

/* == EJECUTAR pg_dump =================================
 * Nota: necesitas tener pg_dump disponible en el sistema.
 * Ejemplo para Debian/Ubuntu: apt-get update && apt-get install -y postgresql-client
 */

$cmd = sprintf(
    'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s > %s 2>&1',
    escapeshellarg($dbPass),
    escapeshellarg($dbHost),
    escapeshellarg($dbPort),
    escapeshellarg($dbUser),
    escapeshellarg($dbName),
    escapeshellarg($filepath)
);

// Ejecutar comando
$output   = [];
$exitCode = 0;
exec($cmd, $output, $exitCode);

/* == LOG Y SALIDA =================================== */

$logLine = sprintf(
    "[%s] Backup Automatico %s -> %s (exitCode=%d)\n",
    date('Y-m-d H:i:s'),
    $dbName,
    $filename,
    $exitCode
);

file_put_contents($backupDir . '/backup.log', $logLine, FILE_APPEND);

if ($exitCode !== 0) {
    fwrite(STDERR, "[ERROR] Falló el respaldo. Revisa backup.log y el archivo temporal.\n");
    // Borrar el archivo fallido:
    if (file_exists($filepath) && filesize($filepath) === 0) {
        unlink($filepath);
    }
    exit(1);
}

echo "[OK] Respaldo creado: $filename\n";

/* == LIMPIAR RESPALDOS MUY VIEJOS (OPCIONAL) ======== */

$now = time();
$maxAge = $DAYS_TO_KEEP * 24 * 60 * 60;

foreach (glob($backupDir . '/backup_automaticos_' . $dbName . '_*.sql') as $file) {
    if (!is_file($file)) continue;

    $fileMTime = filemtime($file);
    if ($fileMTime !== false && ($now - $fileMTime) > $maxAge) {
        @unlink($file);
    }
}
