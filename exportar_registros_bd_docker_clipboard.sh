#!/usr/bin/env bash
set -Eeuo pipefail

CONTAINER="${1:-postgres}"
DB_NAME="${2:-pandas_estampados_y_kitsune}"
DB_USER="${3:-postgres}"
OUT="/tmp/registros_${DB_NAME}_$(date +%Y%m%d_%H%M%S).txt"

run_psql() {
  docker exec -i "$CONTAINER" psql -U "$DB_USER" -d "$DB_NAME" "$@"
}

{
  echo "============================================================"
  echo "EXPORTACIÓN COMPLETA DE REGISTROS"
  echo "Contenedor: $CONTAINER"
  echo "Base de datos: $DB_NAME"
  echo "Usuario: $DB_USER"
  echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
  echo "============================================================"
  echo

  echo "============================================================"
  echo "TABLAS"
  echo "============================================================"
  run_psql -c "\dt"
  echo

  echo "============================================================"
  echo "CONTEO DE REGISTROS POR TABLA"
  echo "============================================================"
  run_psql -P pager=off -c "
SELECT
    schemaname,
    relname AS tabla,
    n_live_tup AS filas_estimadas
FROM pg_stat_user_tables
ORDER BY relname;
"
  echo

  echo "============================================================"
  echo "LLAVES FORÁNEAS / RELACIONES"
  echo "============================================================"
  run_psql -P pager=off -c "
SELECT
    tc.table_name AS tabla_origen,
    kcu.column_name AS columna_origen,
    ccu.table_name AS tabla_referenciada,
    ccu.column_name AS columna_referenciada,
    tc.constraint_name
FROM information_schema.table_constraints AS tc
JOIN information_schema.key_column_usage AS kcu
    ON tc.constraint_name = kcu.constraint_name
JOIN information_schema.constraint_column_usage AS ccu
    ON ccu.constraint_name = tc.constraint_name
WHERE tc.constraint_type = 'FOREIGN KEY'
  AND tc.table_schema = 'public'
ORDER BY tc.table_name, kcu.column_name;
"
  echo

  TABLES=(
    auditoria
    categoria
    cliente
    compra
    detallecompra
    detallefactura
    factura
    factura_estado_historial
    plazo
    plazo_cuota
    producto
    proveedor
    rol
    seccion
    usuario
  )

  for table in "${TABLES[@]}"; do
    echo
    echo "============================================================"
    echo "TABLA: $table"
    echo "============================================================"

    echo
    echo "----- ESTRUCTURA -----"
    run_psql -P pager=off -c "\d+ $table"

    echo
    echo "----- REGISTROS -----"
    run_psql -P pager=off -P null='NULL' -c "SELECT * FROM $table ORDER BY 1;"
  done

} > "$OUT"

if command -v wl-copy >/dev/null 2>&1; then
  wl-copy < "$OUT"
  echo "Copiado al portapapeles con wl-copy."
else
  echo "wl-copy no está instalado."
fi

echo "Archivo generado:"
echo "$OUT"
