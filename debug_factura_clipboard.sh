#!/usr/bin/env bash
set -Eeuo pipefail

if ! command -v wl-copy >/dev/null 2>&1; then
  echo "Error: wl-copy no está instalado."
  echo "En Arch/CachyOS instálalo con:"
  echo "sudo pacman -S wl-clipboard"
  exit 1
fi

if ! command -v realpath >/dev/null 2>&1; then
  echo "Error: realpath no está disponible."
  exit 1
fi

# Si no pasas archivos, usa los relacionados con nueva_factura.php
if [ "$#" -eq 0 ]; then
  files=(
    "nueva_factura.php"
    "controllers/nueva_factura_controller.php"
    "partials/facturacion/facturas/nueva/scripts.php"
    "partials/facturacion/facturas/nueva/form.php"
    "partials/facturacion/facturas/nueva/totals-section.php"
  )
else
  files=("$@")
fi

tmp_file="$(mktemp)"

{
  echo "============================================================"
  echo "DEBUG FACTURACIÓN - ARCHIVOS PARA REVISIÓN"
  echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
  echo "Directorio actual: $(pwd)"
  echo "============================================================"
  echo

  echo "============================================================"
  echo "GIT STATUS"
  echo "============================================================"
  git status --short 2>/dev/null || echo "No parece ser un repositorio Git o git no está disponible."
  echo

  echo "============================================================"
  echo "BÚSQUEDA RÁPIDA DEL ERROR: trim("
  echo "============================================================"
  grep -RIn --exclude-dir=.git --exclude-dir=node_modules --exclude-dir=vendor "trim(" \
    nueva_factura.php controllers partials 2>/dev/null || true
  echo

  for file in "${files[@]}"; do
    echo
    echo "============================================================"
    echo "ARCHIVO: $file"
    echo "RUTA ABSOLUTA: $(realpath "$file" 2>/dev/null || echo 'NO EXISTE')"
    echo "============================================================"

    if [ ! -f "$file" ]; then
      echo "ERROR: archivo no encontrado"
      continue
    fi

    echo
    echo "----- CONTENIDO CON NÚMEROS DE LÍNEA -----"
    nl -ba "$file"
    echo
  done
} | tee "$tmp_file"

wl-copy < "$tmp_file"

echo
echo "Contenido copiado al portapapeles con wl-copy."
echo "Ahora puedes pegarlo aquí con Ctrl+V."
echo "Archivo temporal generado: $tmp_file"
