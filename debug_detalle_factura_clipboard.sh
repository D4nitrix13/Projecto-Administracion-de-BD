#!/usr/bin/env bash
set -uo pipefail

ID_FACTURA="${1:-22}"
OUT="$(mktemp)"

copy_file() {
  local file="$1"

  {
    echo
    echo "============================================================"
    echo "ARCHIVO: $file"
    echo "RUTA: $(realpath "$file" 2>/dev/null || echo 'NO EXISTE')"
    echo "============================================================"

    if [ -f "$file" ]; then
      nl -ba "$file"
    else
      echo "NO EXISTE"
    fi
  } >> "$OUT"
}

{
  echo "============================================================"
  echo "DEBUG DETALLE FACTURA #${ID_FACTURA}"
  echo "PWD: $(pwd)"
  echo "FECHA: $(date '+%Y-%m-%d %H:%M:%S')"
  echo "============================================================"
  echo

  echo "============================================================"
  echo "GIT STATUS"
  echo "============================================================"
  git status --short 2>/dev/null || true
  echo

  echo "============================================================"
  echo "CONSULTA BD FACTURA #${ID_FACTURA}"
  echo "============================================================"

  if command -v docker >/dev/null 2>&1; then
    docker compose exec -T postgres psql -U postgres -d pandas_estampados_y_kitsune <<SQL 2>&1
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

SELECT
    id_historial,
    id_factura,
    tipo_evento,
    estado_pago_anterior,
    estado_pago_nuevo,
    estado_produccion_anterior,
    estado_produccion_nuevo,
    monto_pagado_anterior,
    monto_pagado_nuevo,
    monto_abonado,
    saldo_anterior,
    saldo_nuevo,
    comentario,
    fecha_evento
FROM factura_estado_historial
WHERE id_factura = ${ID_FACTURA}
ORDER BY fecha_evento DESC, id_historial DESC
LIMIT 20;

SELECT *
FROM plazo
WHERE id_factura = ${ID_FACTURA};

SELECT pc.*
FROM plazo_cuota pc
JOIN plazo p ON p.id_plazo = pc.id_plazo
WHERE p.id_factura = ${ID_FACTURA}
ORDER BY pc.numero;
SQL
  else
    echo "Docker no disponible."
  fi
} > "$OUT"

copy_file "detalle_factura.php"
copy_file "controllers/detalle_factura_controller.php"
copy_file "partials/facturacion/facturas/detalle/abono-form.php"
copy_file "partials/facturacion/facturas/detalle/actions.php"
copy_file "partials/facturacion/facturas/detalle/production-actions.php"
copy_file "partials/facturacion/facturas/detalle/summary.php"
copy_file "partials/facturacion/facturas/detalle/totals.php"
copy_file "partials/facturacion/facturas/detalle/timeline.php"
copy_file "partials/facturacion/facturas/detalle/header.php"
copy_file "src/Repository/FacturaRepository.php"
copy_file "src/Service/FacturaService.php"
copy_file "src/Service/FacturaValidationService.php"
copy_file "src/Service/FacturaCalculationService.php"
copy_file "src/Repository/FacturaEstadoHistorialRepository.php"

if command -v wl-copy >/dev/null 2>&1; then
  wl-copy < "$OUT"
  echo "Copiado al portapapeles con wl-copy."
else
  echo "wl-copy no está instalado. Archivo generado:"
fi

echo "$OUT"
