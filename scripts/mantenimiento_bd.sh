#!/bin/bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

LOG_DIR="$PROJECT_ROOT/backups/logs"
DATE="$(date +%Y-%m-%d_%H-%M-%S)"
LOG_FILE="$LOG_DIR/mantenimiento_bd_$DATE.log"

mkdir -p "$LOG_DIR"

{
    echo "Ejecutando plan de mantenimiento de base de datos..."
    echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"

    echo ""
    echo "1. Ejecutando backup completo..."
    bash "$PROJECT_ROOT/scripts/backup_full.sh"

    echo ""
    echo "2. Ejecutando respaldo rápido de datos importantes..."
    bash "$PROJECT_ROOT/scripts/backup_diff.sh"

    echo ""
    echo "3. Copiando logs de PostgreSQL..."
    bash "$PROJECT_ROOT/scripts/backup_logs.sh"

    echo ""
    echo "Plan de mantenimiento finalizado correctamente."
} > "$LOG_FILE" 2>&1