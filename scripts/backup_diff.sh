#!/bin/bash

CONTAINER_NAME="pandas_bd"
DB_USER="postgres"
DB_NAME="pandas_estampados_y_kitsune"
BACKUP_DIR="./backups/diff"
LOG_DIR="./backups/logs"
DATE=$(date +%Y-%m-%d_%H-%M-%S)

mkdir -p "$BACKUP_DIR"
mkdir -p "$LOG_DIR"

BACKUP_FILE="$BACKUP_DIR/backup_diff_$DATE.sql"
LOG_FILE="$LOG_DIR/backup_diff_$DATE.log"

echo "Iniciando backup diferencial académico..." | tee -a "$LOG_FILE"

docker exec "$CONTAINER_NAME" pg_dump \
  -U "$DB_USER" \
  -d "$DB_NAME" \
  -t productos \
  -t clientes \
  -t facturas \
  -t detalle_factura \
  -t usuarios \
  > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
  echo "Backup diferencial académico generado correctamente: $BACKUP_FILE" | tee -a "$LOG_FILE"
else
  echo "Error al generar el backup diferencial académico" | tee -a "$LOG_FILE"
  exit 1
fi