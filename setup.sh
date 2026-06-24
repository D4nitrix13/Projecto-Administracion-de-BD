#!/usr/bin/env bash
set -e

COMPOSE_FILE="docker/docker-compose.yml"

show_help() {
    echo "Uso: ./setup.sh [comando]"
    echo ""
    echo "Comandos:"
    echo "  up        Levantar todos los contenedores (con caché)"
    echo "  down      Detener y eliminar contenedores + volúmenes"
    echo "  stop      Detener contenedores sin eliminarlos"
    echo "  start     Iniciar contenedores detenidos"
    echo "  restart   Reiniciar contenedores"
    echo "  fresh     Reconstruir TODO desde cero (sin caché, borra datos)"
    echo "  build     Reconstruir imagen app con caché"
    echo "  logs      Ver logs de todos los contenedores"
    echo "  status    Ver estado de contenedores"
    echo ""
}

cmd_up() {
    echo "=== Levantando contenedores ==="
    docker compose -f "$COMPOSE_FILE" up -d
    echo ""
    echo "=========================================="
    echo "  Proyecto listo!"
    echo "  URL: http://localhost:8080"
    echo "  pgAdmin: http://localhost:5050"
    echo "  Mailpit: http://localhost:8025"
    echo "=========================================="
}

cmd_down() {
    echo "=== Deteniendo y eliminando contenedores + volúmenes ==="
    docker compose -f "$COMPOSE_FILE" down --remove-orphans -v
}

cmd_stop() {
    echo "=== Deteniendo contenedores ==="
    docker compose -f "$COMPOSE_FILE" stop
}

cmd_start() {
    echo "=== Iniciando contenedores ==="
    docker compose -f "$COMPOSE_FILE" start
}

cmd_restart() {
    echo "=== Reiniciando contenedores ==="
    docker compose -f "$COMPOSE_FILE" restart
}

cmd_build() {
    echo "=== Reconstruyendo imagen app (con caché) ==="
    docker compose -f "$COMPOSE_FILE" build app
}

cmd_logs() {
    docker compose -f "$COMPOSE_FILE" logs -f
}

cmd_status() {
    docker compose -f "$COMPOSE_FILE" ps
}

cmd_fresh() {
    echo "!!! REINSTALACIÓN COMPLETA - Se borrarán todos los datos !!!"
    echo "Presiona Ctrl+C en los próximos 5 segundos para cancelar..."
    sleep 5

    echo ""
    echo "=== 1) Deteniendo contenedores ==="
    docker compose -f "$COMPOSE_FILE" down --remove-orphans -v

    echo ""
    echo "=== 2) Eliminando datos anteriores ==="
    sudo rm -rf storage/ backups/ database/

    echo ""
    echo "=== 3) Creando estructura del proyecto ==="
    mkdir -p storage/system
    mkdir -p backups/manual backups/full backups/diff backups/logs
    mkdir -p database/postgresql database/wal_archive database/pgadmin database/logs
    mkdir -p uploads/productos

    echo ""
    echo "=== 4) Generando .env ==="
    if [ ! -f .env ]; then
        cp .env.example .env
        echo ".env creado desde .env.example"
    fi

    echo ""
    echo "=== 5) Instalando dependencias PHP ==="
    echo "Levantando PostgreSQL..."
    docker compose -f "$COMPOSE_FILE" up -d postgres

    echo "Esperando que PostgreSQL esté listo..."
    until docker exec pandas_bd pg_isready -U postgres >/dev/null 2>&1; do
        sleep 2
    done

    echo "Instalando dependencias con Composer..."
    docker compose -f "$COMPOSE_FILE" run --rm app composer install --no-interaction --prefer-dist

    echo ""
    echo "=== 6) Generando archivos de configuración ==="
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

    echo '{"limite_cliente_fugaz": 1000.00}' > storage/system/configuracion_sistema.json
    echo "[]" > storage/system/maintenance_history.json
    echo "[]" > storage/system/notificaciones.json
    echo "[]" > storage/system/wal_delete_queue.json
    echo '{}' > storage/system/backup_metadata.json
    echo "[]" > storage/system/plazos.json
    echo "[]" > backups/logs/delete_queue.json

    if [ ! -f numero_de_whatsapp.txt ]; then
        echo "+50500000000" > numero_de_whatsapp.txt
    fi

    echo ""
    echo "=== 7) Configurando permisos ==="
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

    echo ""
    echo "=== 8) Levantando todos los contenedores ==="
    docker compose -f "$COMPOSE_FILE" build app
    docker compose -f "$COMPOSE_FILE" up -d

    echo "Esperando que PostgreSQL esté listo..."
    until docker exec pandas_bd pg_isready -U postgres -d pandas_estampados_y_kitsune >/dev/null 2>&1; do
        sleep 2
    done

    echo ""
    echo "=== 9) Cargando esquema y datos ==="
    docker exec -i pandas_bd psql \
        -U postgres \
        -d pandas_estampados_y_kitsune \
        < sql/01_data.sql

    echo "Cargando procedimientos y funciones base..."
    docker exec -i pandas_bd psql \
        -U postgres \
        -d pandas_estampados_y_kitsune \
        < sql/02_procedures.sql

    echo "Cargando funciones de paginación..."
    docker exec -i pandas_bd psql \
        -U postgres \
        -d pandas_estampados_y_kitsune \
        < sql/03_paginacion.sql

    echo "Cargando funciones de ranking..."
    docker exec -i pandas_bd psql \
        -U postgres \
        -d pandas_estampados_y_kitsune \
        < sql/04_ranking_productos.sql

    echo "Cargando tablas de plazos..."
    docker exec -i pandas_bd psql \
        -U postgres \
        -d pandas_estampados_y_kitsune \
        < sql/05_plazos.sql

    echo "Cargando funciones de reportes..."
    docker exec -i pandas_bd psql \
        -U postgres \
        -d pandas_estampados_y_kitsune \
        < sql/06_reportes_functions.sql

    echo "Esquema y funciones cargados correctamente."
    echo ""
    echo "=========================================="
    echo "  Proyecto listo!"
    echo "  URL: http://localhost:8080"
    echo "  pgAdmin: http://localhost:5050"
    echo "  Mailpit: http://localhost:8025"
    echo "=========================================="
}

# === Main ===
case "${1:-}" in
    up)      cmd_up ;;
    down)    cmd_down ;;
    stop)    cmd_stop ;;
    start)   cmd_start ;;
    restart) cmd_restart ;;
    build)   cmd_build ;;
    logs)    cmd_logs ;;
    status)  cmd_status ;;
    fresh)   cmd_fresh ;;
    *)       show_help; exit 1 ;;
esac
