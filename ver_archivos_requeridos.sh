#!/usr/bin/env bash

set -euo pipefail

ARCHIVO_BASE="${1:-restaurar_bd.php}"
RAIZ_PROYECTO="$(pwd)"

echo "========================================"
echo "Analizando archivo: $ARCHIVO_BASE"
echo "Ruta base del proyecto: $RAIZ_PROYECTO"
echo "========================================"
echo

if [[ ! -f "$ARCHIVO_BASE" ]]; then
    echo "ERROR: No existe el archivo: $ARCHIVO_BASE"
    exit 1
fi

echo "Archivos requeridos encontrados:"
echo

grep -E 'require|require_once|include|include_once' "$ARCHIVO_BASE" \
    | sed -E 's/.*__DIR__ \. "([^"]+)".*/\1/' \
    | while read -r RUTA_RELATIVA; do

        if [[ "$RUTA_RELATIVA" == require* || "$RUTA_RELATIVA" == include* ]]; then
            continue
        fi

        RUTA_COMPLETA="$RAIZ_PROYECTO$RUTA_RELATIVA"

        echo "========================================"
        echo "Archivo:"
        echo "$RUTA_COMPLETA"
        echo "========================================"

        if [[ -f "$RUTA_COMPLETA" ]]; then
            echo
            echo "Contenido:"
            echo "----------------------------------------"
            cat "$RUTA_COMPLETA"
            echo
        else
            echo
            echo "ERROR: Este archivo requerido no existe."
            echo
        fi

    done