#!/bin/bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

POSTGRES_LOG_DIR="$PROJECT_ROOT/database/logs"
BACKUP_LOG_DIR="$PROJECT_ROOT/backups/logs"

DATE="$(date +%Y-%m-%d_%H-%M-%S)"

BACKUP_FILE="$BACKUP_LOG_DIR/postgres_logs_$DATE.tar.gz"
LOG_FILE="$BACKUP_LOG_DIR/backup_logs_$DATE.log"

mkdir -p "$POSTGRES_LOG_DIR"
mkdir -p "$BACKUP_LOG_DIR"

{
    echo "Iniciando copia de registros..."
    echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
    echo "Origen: $POSTGRES_LOG_DIR"
    echo "Destino: $BACKUP_FILE"

    LOG_COUNT="$(find "$POSTGRES_LOG_DIR" -type f 2>/dev/null | wc -l)"

    if [ "$LOG_COUNT" -eq 0 ]; then
        echo "No se encontraron logs de PostgreSQL para comprimir."
        echo "Se deja este archivo como evidencia de revisión de logs."
        exit 0
    fi

    tar -czf "$BACKUP_FILE" -C "$POSTGRES_LOG_DIR" .

    if [ ! -s "$BACKUP_FILE" ]; then
        echo "Error: el archivo comprimido de logs quedó vacío."
        rm -f "$BACKUP_FILE"
        exit 1
    fi

    echo "Copia de registros generada correctamente."
    echo "Archivo: $BACKUP_FILE"
    echo "Tamaño: $(du -h "$BACKUP_FILE" | awk '{print $1}')"
} > "$LOG_FILE" 2>&1