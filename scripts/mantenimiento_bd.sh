#!/bin/bash

echo "Ejecutando plan de mantenimiento de base de datos..."

./scripts/backup_full.sh
./scripts/backup_diff.sh
./scripts/backup_logs.sh

echo "Plan de mantenimiento finalizado."