<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";

function hacerRespaldo(
    PDO $connection,
    string $tipo = 'manual',
    ?int $idUsuario = null,
    ?string $nombrePersonalizado = null,
    ?string $mensajePersonalizado = null
): array {
    date_default_timezone_set('America/Managua');

    $config = getDbConfig();
    $BACKUP_DIR = __DIR__ . '/../backups';
    $DAYS_TO_KEEP = 7;

    if (!is_dir($BACKUP_DIR)) {
        mkdir($BACKUP_DIR, 0775, true);
    }

    $timestamp = date('Y-m-d_H-i-s');

    if ($nombrePersonalizado !== null && trim($nombrePersonalizado) !== '') {
        $slug = preg_replace('/[^A-Za-z0-9_\-]/', '_', trim($nombrePersonalizado));
        $baseName = $slug !== '' ? $slug : $config["database"];
    } else {
        $baseName = $config["database"];
    }

    $filename = sprintf('backup_%s_%s_%s.sql', $tipo, $baseName, $timestamp);
    $filepath = $BACKUP_DIR . '/' . $filename;
    $logFile = $BACKUP_DIR . '/backup.log';

    $cmd = sprintf(
        'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s > %s 2>&1',
        escapeshellarg($config["password"]),
        escapeshellarg($config["host"]),
        escapeshellarg($config["port"]),
        escapeshellarg($config["username"]),
        escapeshellarg($config["database"]),
        escapeshellarg($filepath)
    );

    $output   = [];
    $exitCode = 0;
    exec($cmd, $output, $exitCode);

    $logLine = sprintf(
        "[%s] Backup %s %s -> %s (exitCode=%d)\n",
        date('Y-m-d H:i:s'),
        ($tipo === 'auto' ? 'Automatico' : 'Manual'),
        $config["database"],
        $filename,
        $exitCode
    );
    file_put_contents($logFile, $logLine, FILE_APPEND);

    if ($exitCode !== 0) {
        if (file_exists($filepath) && filesize($filepath) === 0) {
            @unlink($filepath);
        }

        return [false, "Error al ejecutar pg_dump: " . implode("\n", $output)];
    }

    if ($mensajePersonalizado !== null && trim($mensajePersonalizado) !== '') {
        $mensaje = trim($mensajePersonalizado);
    } else {
        $mensaje = "Respaldo " . ($tipo === 'auto' ? "automático" : "manual") . " generado: {$filename}";
    }

    $now    = time();
    $maxAge = $DAYS_TO_KEEP * 24 * 60 * 60;

    foreach (glob($BACKUP_DIR . '/backup_*_' . $baseName . '_*.sql') as $file) {
        if (!is_file($file)) continue;

        $fileMTime = filemtime($file);
        if ($fileMTime !== false && ($now - $fileMTime) > $maxAge) {
            @unlink($file);
        }
    }

    return [true, $mensaje];
}
