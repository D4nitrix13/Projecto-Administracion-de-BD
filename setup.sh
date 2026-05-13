#!/usr/bin/env bash
set -e

docker compose -f docker/docker-compose.yml down --remove-orphans -v

sudo rm -rf storage/ backups/ database/

mkdir -p storage/system
mkdir -p backups/manual backups/full backups/diff backups/logs
mkdir -p database/postgresql database/logs database/wal_archive database/pgadmin

cat > storage/system/backup_schedule.json <<'EOF'
{
    "enabled": true,
    "type": "full",
    "interval_value": 1,
    "interval_unit": "weeks",
    "last_run_at": null,
    "next_run_at": "2026-05-12 22:15:01",
    "updated_at": "2026-05-05 22:15:01"
}
EOF

echo '{
    "limite_cliente_fugaz": 1000.00
}' > storage/system/configuracion_sistema.json

echo "[]" > storage/system/maintenance_history.json
echo "[]" > backups/logs/delete_queue.json

sudo chown -R 33:33 storage backups
sudo chmod -R 775 storage backups
sudo find storage backups -type f -exec chmod 664 {} \;
sudo find storage backups -type d -exec chmod 775 {} \;

sudo chown -R 999:999 database/postgresql database/logs database/wal_archive

sudo chmod -R 700 database/postgresql

sudo chmod -R 755 database/logs
sudo find database/logs -type f -exec chmod 644 {} \;
sudo find database/logs -type d -exec chmod 755 {} \;

sudo chmod -R 775 database/wal_archive
sudo find database/wal_archive -type f -exec chmod 664 {} \;
sudo find database/wal_archive -type d -exec chmod 775 {} \;

sudo chown -R 5050:5050 database/pgadmin
sudo chmod -R 700 database/pgadmin

chmod +x scripts/backup_full.sh
chmod +x scripts/backup_diff.sh
chmod +x scripts/backup_logs.sh
chmod +x scripts/mantenimiento_bd.sh

docker compose -f docker/docker-compose.yml up -d --build

docker compose -f docker/docker-compose.yml up -d --build

echo "Esperando que PostgreSQL esté listo..."
until docker exec pandas_bd pg_isready -U postgres -d pandas_estampados_y_kitsune >/dev/null 2>&1; do
    sleep 2
done

echo "Cargando datos iniciales desde sql/01_data.sql..."
docker exec -i pandas_bd psql \
    -U postgres \
    -d pandas_estampados_y_kitsune \
    < sql/01_data.sql

echo "Datos iniciales cargados correctamente."