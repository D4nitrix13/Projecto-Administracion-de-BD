#!/bin/bash

CONTAINER_NAME="pandas_bd"
LOG_DIR="./backups/logs"
DATE=$(date +%Y-%m-%d_%H-%M-%S)

mkdir -p "$LOG_DIR"

LOG_FILE="$LOG_DIR/docker_postgres_$DATE.log"

docker logs "$CONTAINER_NAME" > "$LOG_FILE" 2>&1

echo "Logs guardados en: $LOG_FILE"