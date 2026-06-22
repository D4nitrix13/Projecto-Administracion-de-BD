#!/usr/bin/env bash
set -e

# =============================================
# 1) Detener y limpiar contenedores
# =============================================
docker compose -f docker/docker-compose.yml down --remove-orphans -v

# =============================================
# 2) Eliminar datos anteriores
# =============================================
sudo rm -rf storage/ backups/ database/

# =============================================
# 3) Crear estructura del proyecto
# =============================================
mkdir -p storage/system
mkdir -p backups/manual backups/full backups/diff backups/logs
mkdir -p database/postgresql database/wal_archive database/pgadmin database/logs
mkdir -p uploads/productos

# =============================================
# 4) Generar .env si no existe
# =============================================
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env creado desde .env.example — edítalo con tus credenciales reales si es necesario."
fi

# =============================================
# 5) Instalar dependencias PHP (Composer)
# =============================================
if [ ! -d "vendor" ]; then
    echo "Levantando PostgreSQL..."
    docker compose -f docker/docker-compose.yml up -d postgres

    echo "Esperando que PostgreSQL esté listo..."
    until docker exec pandas_bd pg_isready -U postgres >/dev/null 2>&1; do
        sleep 2
    done

    echo "Instalando dependencias con Composer..."
    docker compose -f docker/docker-compose.yml run --rm app composer install --no-interaction --prefer-dist
fi

# =============================================
# 6) Archivos de configuración iniciales
# =============================================
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
echo "[]" > storage/system/notificaciones.json
echo "[]" > storage/system/wal_delete_queue.json
echo '{}' > storage/system/backup_metadata.json
echo "[]" > storage/system/plazos.json
echo "[]" > backups/logs/delete_queue.json

if [ ! -f numero_de_whatsapp.txt ]; then
    echo "+50500000000" > numero_de_whatsapp.txt
fi

# =============================================
# 7) Permisos
# =============================================
sudo chown -R 33:33 storage backups uploads
sudo chown 33:33 numero_de_whatsapp.txt 2>/dev/null || true
sudo chmod 664 numero_de_whatsapp.txt 2>/dev/null || true
sudo chmod -R 775 storage backups uploads
sudo find storage backups uploads -type f -exec chmod 664 {} \;
sudo find storage backups uploads -type d -exec chmod 775 {} \;

sudo chown -R 999:999 database/postgresql database/wal_archive

sudo chmod -R 700 database/postgresql

sudo chmod -R 775 database/wal_archive
sudo find database/wal_archive -type f -exec chmod 664 {} \;
sudo find database/wal_archive -type d -exec chmod 775 {} \;

sudo chown -R 5050:5050 database/pgadmin
sudo chmod -R 700 database/pgadmin

chmod +x scripts/backup_full.sh
chmod +x scripts/backup_diff.sh
chmod +x scripts/backup_logs.sh
chmod +x scripts/mantenimiento_bd.sh

# Notification cron for inventory alerts (inside Docker container)
docker exec pandas_app sh -c 'command -v crontab >/dev/null 2>&1 && (crontab -l 2>/dev/null; echo "*/30 * * * * cd /var/www/html && php scripts/notificaciones_inventario.php >> storage/system/logs/notificaciones_cron.log 2>&1") | crontab - || echo "  [WARN] crontab not available in container — skipping cron setup"' 2>/dev/null || echo "  [WARN] Could not configure cron in container"

# =============================================
# 8) Levantar Docker
# =============================================
docker compose -f docker/docker-compose.yml build --no-cache app
docker compose -f docker/docker-compose.yml up -d

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
echo ""
echo "=========================================="
echo "  Proyecto listo!"
echo "  URL: http://localhost:8080"
echo "  pgAdmin: http://localhost:5050"
echo "  Mailpit: http://localhost:8025"
echo "=========================================="
