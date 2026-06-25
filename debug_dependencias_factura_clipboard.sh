#!/usr/bin/env bash
set -uo pipefail

ID_FACTURA="${1:-22}"
OUT="$(mktemp)"

have() {
  command -v "$1" >/dev/null 2>&1
}

write_header() {
  {
    echo
    echo "============================================================"
    echo "$1"
    echo "============================================================"
  } >> "$OUT"
}

copy_file() {
  local file="$1"

  write_header "ARCHIVO: $file"

  {
    echo "RUTA ABSOLUTA: $(realpath "$file" 2>/dev/null || echo 'NO EXISTE')"
    echo

    if [ -f "$file" ]; then
      echo "----- CONTENIDO CON NÚMEROS DE LÍNEA -----"
      nl -ba "$file"
    else
      echo "NO EXISTE"
    fi
  } >> "$OUT"
}

run_cmd() {
  local title="$1"
  shift

  write_header "$title"

  {
    echo "+ $*"
    "$@" 2>&1 || true
  } >> "$OUT"
}

{
  echo "============================================================"
  echo "DEBUG DEPENDENCIAS FACTURACIÓN / ABONO"
  echo "Factura: #${ID_FACTURA}"
  echo "URL: http://localhost:8080/detalle_factura.php?id=${ID_FACTURA}"
  echo "PWD: $(pwd)"
  echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
  echo "============================================================"
} > "$OUT"

run_cmd "GIT STATUS" git status --short

run_cmd "PHP VERSION" php -v

run_cmd "PHP MODULES" php -m

if [ -f composer.json ]; then
  copy_file "composer.json"
fi

if [ -f composer.lock ]; then
  run_cmd "COMPOSER PACKAGES" composer show
fi

write_header "ÁRBOL RELEVANTE DEL PROYECTO"
{
  find . \
    -path "./.git" -prune -o \
    -path "./vendor" -prune -o \
    -path "./node_modules" -prune -o \
    -path "./database/postgresql" -prune -o \
    -path "./database/pgadmin" -prune -o \
    -path "./storage/system" -prune -o \
    -type f \
    \( \
      -path "./*.php" -o \
      -path "./controllers/*.php" -o \
      -path "./repositories/*.php" -o \
      -path "./services/*.php" -o \
      -path "./helpers/*.php" -o \
      -path "./includes/*.php" -o \
      -path "./sql/*.php" -o \
      -path "./partials/facturacion/facturas/detalle/*.php" -o \
      -path "./partials/facturacion/facturas/*.php" -o \
      -path "./src/Repository/*.php" -o \
      -path "./src/Service/*.php" \
    \) \
    -print | sort
} >> "$OUT" 2>&1

write_header "REQUIRE / INCLUDE DETECTADOS"
{
  grep -RIn --exclude-dir=.git --exclude-dir=vendor --exclude-dir=node_modules \
    "require_once\|require \|include_once\|include " \
    registrar_abono.php \
    transicionar_estado_produccion.php \
    detalle_factura.php \
    controllers \
    repositories \
    services \
    helpers \
    includes \
    partials/facturacion/facturas/detalle \
    src 2>/dev/null || true
} >> "$OUT"

write_header "BÚSQUEDA DE ABONO / PAGO / PRODUCCIÓN"
{
  grep -RIn --exclude-dir=.git --exclude-dir=vendor --exclude-dir=node_modules \
    "registrarAbono\|monto_abono\|abono\|monto_pagado\|saldo_pendiente\|porcentaje_pagado\|csrfRequire\|csrfField\|csrfToken\|transicionar\|estado_produccion\|En producción\|Lista para entregar\|Entregada" \
    registrar_abono.php \
    transicionar_estado_produccion.php \
    detalle_factura.php \
    controllers \
    repositories \
    services \
    helpers \
    includes \
    partials/facturacion/facturas/detalle \
    src 2>/dev/null || true
} >> "$OUT"

write_header "LINT PHP DE ARCHIVOS RELEVANTES"
{
  while IFS= read -r file; do
    echo
    echo "--- php -l $file ---"
    php -l "$file" 2>&1 || true
  done < <(
    find . \
      -path "./.git" -prune -o \
      -path "./vendor" -prune -o \
      -path "./node_modules" -prune -o \
      -path "./database/postgresql" -prune -o \
      -path "./database/pgadmin" -prune -o \
      -type f \
      \( \
        -name "registrar_abono.php" -o \
        -name "transicionar_estado_produccion.php" -o \
        -name "detalle_factura.php" -o \
        -path "./controllers/detalle_factura_controller.php" -o \
        -path "./repositories/*.php" -o \
        -path "./services/*.php" -o \
        -path "./helpers/*.php" -o \
        -path "./includes/*.php" -o \
        -path "./partials/facturacion/facturas/detalle/*.php" \
      \) \
      -print | sort
  )
} >> "$OUT"

# Archivos principales
copy_file "detalle_factura.php"
copy_file "registrar_abono.php"
copy_file "transicionar_estado_produccion.php"

# Controladores
copy_file "controllers/detalle_factura_controller.php"
copy_file "controllers/facturas_controller.php"

# Includes / helpers / conexión
for file in includes/*.php helpers/*.php sql/db.php; do
  [ -f "$file" ] && copy_file "$file"
done

# Repositorios y servicios usados por el flujo viejo
for file in repositories/*.php services/*.php; do
  [ -f "$file" ] && copy_file "$file"
done

# Repositorios y servicios nuevos, por si hay duplicación de arquitectura
for file in src/Repository/*.php src/Service/*.php; do
  [ -f "$file" ] && copy_file "$file"
done

# Parciales del detalle de factura
for file in partials/facturacion/facturas/detalle/*.php; do
  [ -f "$file" ] && copy_file "$file"
done

# Parcial de alerts, importante para saber si se muestran errores
copy_file "partials/facturacion/facturas/alerts.php"
copy_file "partials/shared/toast.php"

write_header "LOGS POSIBLES"
{
  echo "Buscando logs locales..."
  find . \
    -path "./.git" -prune -o \
    -path "./vendor" -prune -o \
    -path "./node_modules" -prune -o \
    -path "./database/postgresql" -prune -o \
    -path "./database/pgadmin" -prune -o \
    -type f \
    \( -iname "*.log" -o -iname "error_log" \) \
    -print 2>/dev/null | sort
} >> "$OUT"

while IFS= read -r log_file; do
  write_header "ÚLTIMAS 200 LÍNEAS LOG: $log_file"
  tail -n 200 "$log_file" >> "$OUT" 2>&1 || true
done < <(
  find . \
    -path "./.git" -prune -o \
    -path "./vendor" -prune -o \
    -path "./node_modules" -prune -o \
    -path "./database/postgresql" -prune -o \
    -path "./database/pgadmin" -prune -o \
    -type f \
    \( -iname "*.log" -o -iname "error_log" \) \
    -print 2>/dev/null | sort
)

write_header "PRUEBA HTTP LOCAL DEL FORMULARIO"
{
  if have curl; then
    echo "Probando si detalle_factura responde:"
    curl -I "http://localhost:8080/detalle_factura.php?id=${ID_FACTURA}" 2>&1 || true

    echo
    echo "Buscando formulario de abono en HTML renderizado:"
    curl -s "http://localhost:8080/detalle_factura.php?id=${ID_FACTURA}" 2>/dev/null \
      | grep -n "abono-form\|monto_abono\|registrar_abono\|flash_error\|flash_success" || true
  else
    echo "curl no disponible."
  fi
} >> "$OUT"

write_header "COMANDOS SQL PARA REVISAR MANUALMENTE"
cat >> "$OUT" <<SQL
Ejecuta esto en psql si hace falta:

SELECT
    id_factura,
    total,
    monto_pagado,
    saldo_pendiente,
    porcentaje_pagado,
    estado_pago,
    estado_produccion,
    fecha_entrega_estimada,
    fecha_entrega_real
FROM factura
WHERE id_factura = ${ID_FACTURA};

SELECT *
FROM factura_estado_historial
WHERE id_factura = ${ID_FACTURA}
ORDER BY fecha_evento DESC, id_historial DESC;

SELECT *
FROM plazo
WHERE id_factura = ${ID_FACTURA};

SELECT pc.*
FROM plazo_cuota pc
JOIN plazo p ON p.id_plazo = pc.id_plazo
WHERE p.id_factura = ${ID_FACTURA}
ORDER BY pc.numero;
SQL

if have wl-copy; then
  wl-copy < "$OUT"
  echo "Copiado al portapapeles con wl-copy."
else
  echo "wl-copy no está instalado."
fi

echo "Archivo generado: $OUT"
