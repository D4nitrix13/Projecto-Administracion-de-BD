#!/bin/bash

set -euo pipefail

DB_HOST="${DB_HOST:-postgres}"
DB_PORT="${DB_PORT:-5432}"
DB_USER="${DB_USERNAME:-postgres}"
DB_PASSWORD="${DB_PASSWORD:-root}"
DB_NAME="${DB_DATABASE:-pandas_estampados_y_kitsune}"

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

BACKUP_DIR="$PROJECT_ROOT/backups/full"
LOG_DIR="$PROJECT_ROOT/backups/logs"

DATE="$(date +%Y-%m-%d_%H-%M-%S)"

mkdir -p "$BACKUP_DIR"
mkdir -p "$LOG_DIR"

BACKUP_FILE="$BACKUP_DIR/backup_full_$DATE.sql"
TEMP_FILE="$BACKUP_FILE.tmp"
LOG_FILE="$LOG_DIR/backup_full_$DATE.log"

{
    echo "Iniciando backup completo..."
    echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
    echo "Host: $DB_HOST"
    echo "Puerto: $DB_PORT"
    echo "Base de datos: $DB_NAME"
    echo "Archivo temporal: $TEMP_FILE"

    export PGPASSWORD="$DB_PASSWORD"

    if pg_dump \
        -h "$DB_HOST" \
        -p "$DB_PORT" \
        -U "$DB_USER" \
        -d "$DB_NAME" \
        --clean \
        --if-exists \
        --no-owner \
        --no-privileges \
        > "$TEMP_FILE"; then

        if [ ! -s "$TEMP_FILE" ]; then
            echo "Error: pg_dump terminó, pero el archivo quedó vacío."
            rm -f "$TEMP_FILE"
            exit 1
        fi

        mv "$TEMP_FILE" "$BACKUP_FILE"

        echo "Backup completo generado correctamente."
        echo "Archivo: $BACKUP_FILE"
        echo "Tamaño: $(du -h "$BACKUP_FILE" | awk '{print $1}')"
    else
        echo "Error: falló la ejecución de pg_dump."
        rm -f "$TEMP_FILE"
        exit 1
    fi
} > "$LOG_FILE" 2>&1