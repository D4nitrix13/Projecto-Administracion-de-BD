<?php
// lib/backup.php

function hacerRespaldo(
    PDO $connection,
    string $tipo = 'manual',
    ?int $idUsuario = null,
    ?string $nombrePersonalizado = null,
    ?string $mensajePersonalizado = null
): array {
    date_default_timezone_set('America/Managua');

    $DB_HOST    = '172.17.0.3';
    $DB_PORT    = '5432';
    $DB_USER    = 'postgres';
    $DB_PASS    = "root";
    $DB_NAME    = 'pandas_estampados_y_kitsune';
    $BACKUP_DIR = __DIR__ . '/../backups';

    // Días a conservar
    $DAYS_TO_KEEP = 7;

    if (!is_dir($BACKUP_DIR)) {
        mkdir($BACKUP_DIR, 0775, true);
    }

    // ============================
    // 1) Construir nombre archivo
    // ============================
    $timestamp = date('Y-m-d_H-i-s');

    if ($nombrePersonalizado !== null && trim($nombrePersonalizado) !== '') {
        $slug = preg_replace('/[^A-Za-z0-9_\-]/', '_', trim($nombrePersonalizado));
        if ($slug === '') {
            $slug = $DB_NAME;
        }
        $baseName = $slug;
    } else {
        $baseName = $DB_NAME;
    }

    $filename = sprintf(
        'backup_%s_%s_%s.sql',
        $tipo,
        $baseName,
        $timestamp
    );

    $filepath = $BACKUP_DIR . '/' . $filename;

    // Archivo de log
    $logFile = $BACKUP_DIR . '/backup.log';

    // ============================
    // 2) Ejecutar pg_dump
    // ============================
    $cmd = sprintf(
        'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s > %s 2>&1',
        escapeshellarg($DB_PASS),
        escapeshellarg($DB_HOST),
        escapeshellarg($DB_PORT),
        escapeshellarg($DB_USER),
        escapeshellarg($DB_NAME),
        escapeshellarg($filepath)
    );

    $output   = [];
    $exitCode = 0;
    exec($cmd, $output, $exitCode);

    // ============================
    // 3) Log en archivo
    // ============================
    $logLine = sprintf(
        "[%s] Backup %s %s -> %s (exitCode=%d)\n",
        date('Y-m-d H:i:s'),
        ($tipo === 'auto' ? 'Automatico' : 'Manual'),
        $DB_NAME,
        $filename,
        $exitCode
    );
    file_put_contents($logFile, $logLine, FILE_APPEND);

    // ============================
    // 4) Manejo de error
    // ============================
    if ($exitCode !== 0) {
        if (file_exists($filepath) && filesize($filepath) === 0) {
            @unlink($filepath);
        }

        $mensaje = "Error al ejecutar pg_dump: " . implode("\n", $output);

        return [false, $mensaje];
    }

    // ============================
    // 5) Mensaje de éxito
    // ============================
    if ($mensajePersonalizado !== null && trim($mensajePersonalizado) !== '') {
        $mensaje = trim($mensajePersonalizado);
    } else {
        $mensaje = "Respaldo " . ($tipo === 'auto' ? "automático" : "manual") .
            " generado: {$filename}";
    }

    // ============================
    // 6) Limpieza de backups viejos
    // ============================
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
