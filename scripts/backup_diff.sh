#!/bin/bash

set -euo pipefail

DB_HOST="${DB_HOST:-postgres}"
DB_PORT="${DB_PORT:-5432}"
DB_USER="${DB_USERNAME:-postgres}"
DB_PASSWORD="${DB_PASSWORD:-root}"
DB_NAME="${DB_DATABASE:-pandas_estampados_y_kitsune}"

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

BACKUP_DIR="$PROJECT_ROOT/backups/diff"
LOG_DIR="$PROJECT_ROOT/backups/logs"

DATE="$(date +%Y-%m-%d_%H-%M-%S)"

mkdir -p "$BACKUP_DIR"
mkdir -p "$LOG_DIR"

BACKUP_FILE="$BACKUP_DIR/backup_diff_$DATE.sql"
TEMP_FILE="$BACKUP_FILE.tmp"
LOG_FILE="$LOG_DIR/backup_diff_$DATE.log"

export PGPASSWORD="$DB_PASSWORD"

{
    echo "Iniciando respaldo rápido de datos importantes..."
    echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
    echo "Host: $DB_HOST"
    echo "Puerto: $DB_PORT"
    echo "Base de datos: $DB_NAME"
    echo "Archivo temporal: $TEMP_FILE"

    TABLES_QUERY="
        SELECT table_schema || '.' || table_name
        FROM information_schema.tables
        WHERE table_schema = 'public'
          AND table_type = 'BASE TABLE'
          AND lower(table_name) IN (
              'producto',
              'productos',
              'cliente',
              'clientes',
              'factura',
              'facturas',
              'detallefactura',
              'detalle_factura',
              'usuario',
              'usuarios'
          )
        ORDER BY table_name;
    "

    mapfile -t TABLES < <(
        psql \
            -h "$DB_HOST" \
            -p "$DB_PORT" \
            -U "$DB_USER" \
            -d "$DB_NAME" \
            -At \
            -c "$TABLES_QUERY"
    )

    if [ "${#TABLES[@]}" -eq 0 ]; then
        echo "Error: no se encontraron tablas críticas para el respaldo rápido."
        echo "Revise los nombres reales de las tablas con:"
        echo "psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME -c '\\dt'"
        exit 1
    fi

    echo "Tablas encontradas para respaldo rápido:"
    for table in "${TABLES[@]}"; do
        echo "- $table"
    done

    TABLE_ARGS=()

    for table in "${TABLES[@]}"; do
        schema="${table%%.*}"
        table_name="${table#*.}"
        TABLE_ARGS+=(--table="${schema}.\"${table_name}\"")
    done

    if pg_dump \
        -h "$DB_HOST" \
        -p "$DB_PORT" \
        -U "$DB_USER" \
        -d "$DB_NAME" \
        --clean \
        --if-exists \
        --no-owner \
        --no-privileges \
        "${TABLE_ARGS[@]}" \
        > "$TEMP_FILE"; then

        if [ ! -s "$TEMP_FILE" ]; then
            echo "Error: pg_dump terminó, pero el archivo diferencial quedó vacío."
            rm -f "$TEMP_FILE"
            exit 1
        fi

        mv "$TEMP_FILE" "$BACKUP_FILE"

        echo "Respaldo rápido generado correctamente."
        echo "Archivo: $BACKUP_FILE"
        echo "Tamaño: $(du -h "$BACKUP_FILE" | awk '{print $1}')"
    else
        echo "Error: falló la ejecución de pg_dump para el respaldo rápido."
        rm -f "$TEMP_FILE"
        exit 1
    fi
} > "$LOG_FILE" 2>&1