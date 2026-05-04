#!/bin/bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

POSTGRES_LOG_DIR="$PROJECT_ROOT/database/logs"
BACKUP_LOG_DIR="$PROJECT_ROOT/backups/logs"

DATE="$(date +%Y-%m-%d_%H-%M-%S)"
BACKUP_FILE="$BACKUP_LOG_DIR/postgres_logs_$DATE.tar.gz"
LOG_FILE="$BACKUP_LOG_DIR/postgres_logs_copy_$DATE.log"

mkdir -p "$BACKUP_LOG_DIR"

{
    echo "Iniciando copia de logs de PostgreSQL..."
    echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
    echo "Origen: $POSTGRES_LOG_DIR"
    echo "Destino: $BACKUP_FILE"

    if [ ! -d "$POSTGRES_LOG_DIR" ]; then
        echo "Error: no existe el directorio de logs de PostgreSQL."
        exit 1
    fi

    if ! find "$POSTGRES_LOG_DIR" -type f | grep -q .; then
        echo "Error: no hay archivos de log para copiar."
        exit 1
    fi

    tar -czf "$BACKUP_FILE" -C "$POSTGRES_LOG_DIR" .

    if [ ! -s "$BACKUP_FILE" ]; then
        echo "Error: el archivo comprimido de logs quedó vacío."
        rm -f "$BACKUP_FILE"
        exit 1
    fi

    echo "Logs copiados correctamente."
    echo "Archivo: $BACKUP_FILE"
    echo "Tamaño: $(du -h "$BACKUP_FILE" | awk '{print $1}')"
} > "$LOG_FILE" 2>&1