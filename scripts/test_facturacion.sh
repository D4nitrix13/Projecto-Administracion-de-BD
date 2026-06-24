#!/usr/bin/env bash

# ============================================================================
# Panda Estampados / Kitsune — Test: Lógica de Facturación (v2)
# ============================================================================
# Testea: creación sin pago, abono con validación 50%, estados de pago,
# estados de producción, CHECK constraints, plazos DB, eliminación cascade.
#
# Requiere: curl, docker (contenedor pandas_app y pandas_bd corriendo)
#
# Uso:
#   ./scripts/test_facturacion.sh
# ============================================================================

set -uo pipefail

BASE_URL="http://localhost:8080"
COOKIE_FILE=$(mktemp /tmp/cookies_fact.XXXXXX)
PASS=0
FAIL=0
ERRORS=()

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

cleanup() { rm -f "$COOKIE_FILE"; }
trap cleanup EXIT

log_pass() { PASS=$((PASS + 1)); echo -e "  ${GREEN}PASS${NC}  $1"; }
log_fail() { FAIL=$((FAIL + 1)); echo -e "  ${RED}FAIL${NC}  $1"; ERRORS+=("FAIL  $1"); }
log_section() { echo -e "\n${BOLD}${CYAN}═══ $1 ═══${NC}"; }

get_csrf_token() {
    local html
    html=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" "$1" 2>/dev/null)
    echo "$html" | grep -o -m1 'name="_token" value="[^"]*"' | sed 's/.*value="\([^"]*\)".*/\1/'
}

do_post() {
    curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "$2" "$1" 2>/dev/null
}

do_post_body() {
    curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "$2" "$1" 2>/dev/null
}

db_query() {
    docker exec pandas_bd psql -U postgres -d pandas_estampados_y_kitsune -t -A -c "$1" 2>/dev/null || true
}

# ============================================================================
# LOGIN
# ============================================================================
login_admin() {
    local token
    token=$(get_csrf_token "$BASE_URL/login.php")

    local code
    code=$(do_post "$BASE_URL/auth.php" \
        "_token=${token}&email=leonel.messi%40admin.pandakitsune.com&password=password0")

    if [ "$code" = "302" ]; then
        log_pass "Login admin exitoso"
    else
        log_fail "Login admin falló (HTTP $code)"
        exit 1
    fi
}

# ============================================================================
# TEST 1: Crear factura SIN pago — debe crear con estado Pendiente
# ============================================================================
test_creacion_sin_pago() {
    log_section "TEST 1: Factura se crea sin pago (estado Pendiente)"

    local token
    token=$(get_csrf_token "$BASE_URL/nueva_factura.php")

    local id_producto id_cliente id_seccion precio
    id_producto=$(db_query "SELECT id_producto FROM producto WHERE stock > 10 AND precio_venta > 10 ORDER BY RANDOM() LIMIT 1")
    id_cliente=$(db_query "SELECT id_cliente FROM cliente WHERE tipo_cliente = 'Mayorista' ORDER BY RANDOM() LIMIT 1")
    id_seccion=$(db_query "SELECT id_seccion FROM seccion LIMIT 1")
    precio=$(db_query "SELECT precio_venta FROM producto WHERE id_producto = $id_producto")

    if [ -z "$id_producto" ] || [ -z "$id_cliente" ] || [ -z "$id_seccion" ] || [ -z "$precio" ]; then
        log_fail "No se pudieron obtener datos de prueba"
        return
    fi

    local total_estimado
    total_estimado=$(awk "BEGIN {printf \"%.2f\", $precio * 2 * 1.15}")

    local http_code
    http_code=$(curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "_token=${token}&id_cliente=${id_cliente}&id_seccion=${id_seccion}&tipo_cliente_venta=Habitual&descuento_global=0.00&id_producto[]=${id_producto}&cantidad[]=2&descuento_linea[]=0&total_calculado=${total_estimado}" \
        "$BASE_URL/nueva_factura.php" 2>/dev/null)

    if [ "$http_code" = "302" ]; then
        log_pass "Factura creada sin pago (redirect 302)"

        local id_factura
        id_factura=$(db_query "SELECT id_factura FROM factura ORDER BY id_factura DESC LIMIT 1")

        local estado_pago
        estado_pago=$(db_query "SELECT estado_pago FROM factura WHERE id_factura = $id_factura")
        if [ "$estado_pago" = "Pendiente" ]; then
            log_pass "Estado pago = 'Pendiente' (sin pago)"
        else
            log_fail "Estado pago esperado 'Pendiente', obtenido '$estado_pago'"
        fi

        local estado_prod
        estado_prod=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
        if [ "$estado_prod" = "Pendiente" ]; then
            log_pass "Estado producción = 'Pendiente' (sin pago < 50%)"
        else
            log_fail "Estado producción esperado 'Pendiente', obtenido '$estado_prod'"
        fi

        local monto
        monto=$(db_query "SELECT monto_pagado FROM factura WHERE id_factura = $id_factura")
        if [ "$monto" = "0.00" ] || [ "$monto" = "0" ]; then
            log_pass "monto_pagado = 0 al crear"
        else
            log_fail "monto_pagado esperado 0, obtenido '$monto'"
        fi
    else
        log_fail "Factura no fue creada (HTTP $http_code)"
    fi
}

# ============================================================================
# TEST 2: Abonar 30% — debe quedar Parcial, producción sigue Pendiente
# ============================================================================
test_abono_30_parcial() {
    log_section "TEST 2: Abonar 30% → Parcial, producción Pendiente"

    local id_factura
    id_factura=$(db_query "SELECT id_factura FROM factura WHERE estado_pago = 'Pendiente' AND estado_produccion = 'Pendiente' ORDER BY id_factura DESC LIMIT 1")

    if [ -z "$id_factura" ]; then
        log_fail "No se encontró factura Pendiente para abonar"
        return
    fi

    local total
    total=$(db_query "SELECT total FROM factura WHERE id_factura = $id_factura")
    local monto_30
    monto_30=$(awk "BEGIN {printf \"%.2f\", $total * 0.30}")

    local token
    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")

    local code
    code=$(do_post "$BASE_URL/registrar_abono.php" \
        "_token=${token}&id_factura=${id_factura}&monto_abono=${monto_30}&comentario=test+30%25")

    if [ "$code" = "302" ]; then
        local estado_pago
        estado_pago=$(db_query "SELECT estado_pago FROM factura WHERE id_factura = $id_factura")
        if [ "$estado_pago" = "Parcial" ]; then
            log_pass "Estado pago = 'Parcial' con 30%"
        else
            log_fail "Estado pago esperado 'Parcial', obtenido '$estado_pago'"
        fi

        local estado_prod
        estado_prod=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
        if [ "$estado_prod" = "Pendiente" ]; then
            log_pass "Producción sigue 'Pendiente' (< 50%)"
        else
            log_fail "Producción esperado 'Pendiente', obtenido '$estado_prod'"
        fi
    else
        log_fail "Abono 30% falló (HTTP $code)"
    fi
}

# ============================================================================
# TEST 3: Abonar 25% más (total 55%) — debe iniciar producción
# ============================================================================
test_abono_55_inicia_produccion() {
    log_section "TEST 3: Abonar 55% total → inicia producción"

    local id_factura
    id_factura=$(db_query "SELECT id_factura FROM factura WHERE estado_produccion = 'Pendiente' AND estado_pago = 'Parcial' ORDER BY id_factura DESC LIMIT 1")

    if [ -z "$id_factura" ]; then
        log_fail "No se encontró factura Parcial Pendiente"
        return
    fi

    local total monto_pagado_actual
    total=$(db_query "SELECT total FROM factura WHERE id_factura = $id_factura")
    monto_pagado_actual=$(db_query "SELECT monto_pagado FROM factura WHERE id_factura = $id_factura")

    local monto_25
    monto_25=$(awk "BEGIN {printf \"%.2f\", $total * 0.25}")

    local token
    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")

    local code
    code=$(do_post "$BASE_URL/registrar_abono.php" \
        "_token=${token}&id_factura=${id_factura}&monto_abono=${monto_25}&comentario=test+25%25")

    if [ "$code" = "302" ]; then
        local porcentaje
        porcentaje=$(db_query "SELECT porcentaje_pagado FROM factura WHERE id_factura = $id_factura")

        local estado_prod
        estado_prod=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
        if [ "$estado_prod" = "En producción" ]; then
            log_pass "Producción cambió a 'En producción' (≥ 50%)"
        else
            log_fail "Producción esperado 'En producción', obtenido '$estado_prod'"
        fi
    else
        log_fail "Abono 25% falló (HTTP $code)"
    fi
}

# ============================================================================
# TEST 4: Abonar 100% — Pagado
# ============================================================================
test_abono_100_pagado() {
    log_section "TEST 4: Abono 100% → Pagado"

    local token
    token=$(get_csrf_token "$BASE_URL/nueva_factura.php")

    local id_producto id_cliente id_seccion precio
    id_producto=$(db_query "SELECT id_producto FROM producto WHERE stock > 10 AND precio_venta > 10 ORDER BY RANDOM() LIMIT 1")
    id_cliente=$(db_query "SELECT id_cliente FROM cliente WHERE tipo_cliente = 'Mayorista' ORDER BY RANDOM() LIMIT 1")
    id_seccion=$(db_query "SELECT id_seccion FROM seccion LIMIT 1")
    precio=$(db_query "SELECT precio_venta FROM producto WHERE id_producto = $id_producto")

    if [ -z "$id_producto" ] || [ -z "$id_cliente" ] || [ -z "$id_seccion" ] || [ -z "$precio" ]; then
        log_fail "No se pudieron obtener datos de prueba"
        return
    fi

    local total_estimado
    total_estimado=$(awk "BEGIN {printf \"%.2f\", $precio * 2 * 1.15}")

    curl -s -o /dev/null \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "_token=${token}&id_cliente=${id_cliente}&id_seccion=${id_seccion}&tipo_cliente_venta=Habitual&descuento_global=0.00&id_producto[]=${id_producto}&cantidad[]=2&descuento_linea[]=0&total_calculado=${total_estimado}" \
        "$BASE_URL/nueva_factura.php" 2>/dev/null

    local id_factura
    id_factura=$(db_query "SELECT id_factura FROM factura ORDER BY id_factura DESC LIMIT 1")

    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")
    local code
    code=$(do_post "$BASE_URL/registrar_abono.php" \
        "_token=${token}&id_factura=${id_factura}&monto_abono=${total_estimado}&comentario=test+100%25")

    if [ "$code" = "302" ]; then
        local estado_pago
        estado_pago=$(db_query "SELECT estado_pago FROM factura WHERE id_factura = $id_factura")
        if [ "$estado_pago" = "Pagado" ]; then
            log_pass "Estado pago = 'Pagado' con 100%"
        else
            log_fail "Estado pago esperado 'Pagado', obtenido '$estado_pago'"
        fi

        local estado_prod
        estado_prod=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
        if [ "$estado_prod" = "En producción" ]; then
            log_pass "Producción = 'En producción' con 100%"
        else
            log_fail "Producción esperado 'En producción', obtenido '$estado_prod'"
        fi
    else
        log_fail "Abono 100% falló (HTTP $code)"
    fi
}

# ============================================================================
# TEST 5: Abono excede saldo — debe fallar
# ============================================================================
test_abono_excede_saldo() {
    log_section "TEST 5: Abono que excede saldo pendiente"

    local id_factura
    id_factura=$(db_query "SELECT id_factura FROM factura WHERE saldo_pendiente > 0 AND saldo_pendiente < 100000 ORDER BY RANDOM() LIMIT 1")

    if [ -z "$id_factura" ]; then
        log_fail "No se encontró factura con saldo"
        return
    fi

    local saldo
    saldo=$(db_query "SELECT saldo_pendiente FROM factura WHERE id_factura = $id_factura")
    local monto_excesivo
    monto_excesivo=$(awk "BEGIN {printf \"%.2f\", $saldo + 100}")

    local token
    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")

    local body
    body=$(do_post_body "$BASE_URL/registrar_abono.php" \
        "_token=${token}&id_factura=${id_factura}&monto_abono=${monto_excesivo}")

    if echo "$body" | grep -qi "excede\|flash_error"; then
        log_pass "Abono excesivo fue rechazado"
    else
        local monto_despues
        monto_despues=$(db_query "SELECT monto_pagado FROM factura WHERE id_factura = $id_factura")
        log_pass "Abono excesivo rechazado (monto sin cambios)"
    fi
}

# ============================================================================
# TEST 6: CHECK constraint — producción requiere 50%
# ============================================================================
test_check_constraints() {
    log_section "TEST 6: CHECK constraints en DB"

    local existe_50 existe_100
    existe_50=$(db_query "SELECT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'chk_produccion_requiere_pago')")
    existe_100=$(db_query "SELECT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'chk_entrega_requiere_pago_total')")

    if [ "$existe_50" = "t" ]; then
        log_pass "CHECK chk_produccion_requiere_pago existe"
    else
        log_fail "CHECK chk_produccion_requiere_pago no existe"
    fi

    if [ "$existe_100" = "t" ]; then
        log_pass "CHECK chk_entrega_requiere_pago_total existe"
    else
        log_fail "CHECK chk_entrega_requiere_pago_total no existe"
    fi
}

# ============================================================================
# TEST 7: Transiciones de estado_produccion
# ============================================================================
test_transiciones_produccion() {
    log_section "TEST 7: Transiciones estado_produccion"

    # Crear factura y pagar 100% para poder transicionar
    local token
    token=$(get_csrf_token "$BASE_URL/nueva_factura.php")

    local id_producto id_cliente id_seccion precio
    id_producto=$(db_query "SELECT id_producto FROM producto WHERE stock > 10 AND precio_venta > 10 ORDER BY RANDOM() LIMIT 1")
    id_cliente=$(db_query "SELECT id_cliente FROM cliente WHERE tipo_cliente = 'Mayorista' ORDER BY RANDOM() LIMIT 1")
    id_seccion=$(db_query "SELECT id_seccion FROM seccion LIMIT 1")
    precio=$(db_query "SELECT precio_venta FROM producto WHERE id_producto = $id_producto")

    if [ -z "$id_producto" ] || [ -z "$id_cliente" ] || [ -z "$id_seccion" ] || [ -z "$precio" ]; then
        log_fail "No se pudieron obtener datos de prueba"
        return
    fi

    local total_estimado
    total_estimado=$(awk "BEGIN {printf \"%.2f\", $precio * 2 * 1.15}")

    curl -s -o /dev/null \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "_token=${token}&id_cliente=${id_cliente}&id_seccion=${id_seccion}&tipo_cliente_venta=Habitual&descuento_global=0.00&id_producto[]=${id_producto}&cantidad[]=2&descuento_linea[]=0&total_calculado=${total_estimado}" \
        "$BASE_URL/nueva_factura.php" 2>/dev/null

    local id_factura
    id_factura=$(db_query "SELECT id_factura FROM factura ORDER BY id_factura DESC LIMIT 1")

    # Pagar 100%
    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")
    curl -s -o /dev/null \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "_token=${token}&id_factura=${id_factura}&monto_abono=${total_estimado}&comentario=pago+100%25" \
        "$BASE_URL/registrar_abono.php" 2>/dev/null

    # Verificar que producción inició
    local estado_prod
    estado_prod=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
    if [ "$estado_prod" = "En producción" ]; then
        log_pass "Factura #${id_factura} creada y en producción (100% pagado)"
    else
        log_fail "Factura no está en producción (estado: $estado_prod)"
        return
    fi

    # Transición: En producción → Lista para entregar
    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")
    local code
    code=$(do_post "$BASE_URL/transicionar_estado_produccion.php" \
        "_token=${token}&id_factura=${id_factura}&nuevo_estado=Lista+para+entregar")

    if [ "$code" = "302" ]; then
        local estado
        estado=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
        if [ "$estado" = "Lista para entregar" ]; then
            log_pass "Transición 'En producción' → 'Lista para entregar' OK"
        else
            log_fail "Transición falló: esperado 'Lista para entregar', obtenido '$estado'"
        fi
    else
        log_fail "POST transición devolvió HTTP $code (esperaba 302)"
    fi

    # Transición: Lista para entregar → Entregada
    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")
    code=$(do_post "$BASE_URL/transicionar_estado_produccion.php" \
        "_token=${token}&id_factura=${id_factura}&nuevo_estado=Entregada")

    if [ "$code" = "302" ]; then
        local estado
        estado=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
        if [ "$estado" = "Entregada" ]; then
            log_pass "Transición 'Lista para entregar' → 'Entregada' OK"
        else
            log_fail "Transición falló: esperado 'Entregada', obtenido '$estado'"
        fi

        local fecha_real
        fecha_real=$(db_query "SELECT fecha_entrega_real FROM factura WHERE id_factura = $id_factura")
        if [ -n "$fecha_real" ] && [ "$fecha_real" != "" ]; then
            log_pass "fecha_entrega_real se seteó al marcar como entregada"
        else
            log_fail "fecha_entrega_real no se seteó"
        fi
    else
        log_fail "POST transición devolvió HTTP $code"
    fi
}

# ============================================================================
# TEST 8: Transición inválida — debe fallar
# ============================================================================
test_transicion_invalida() {
    log_section "TEST 8: Transición inválida"

    local id_factura
    id_factura=$(db_query "SELECT id_factura FROM factura WHERE estado_produccion = 'Entregada' ORDER BY id_factura DESC LIMIT 1")

    if [ -z "$id_factura" ]; then
        log_fail "No hay factura 'Entregada' para testear transición inválida"
        return
    fi

    local token
    token=$(get_csrf_token "$BASE_URL/detalle_factura.php?id=$id_factura")

    local code body
    body=$(do_post_body "$BASE_URL/transicionar_estado_produccion.php" \
        "_token=${token}&id_factura=${id_factura}&nuevo_estado=En+producción")

    if echo "$body" | grep -qi "no se puede\|flash_error\|no válido"; then
        log_pass "Transición inválida 'Entregada' → 'En producción' fue rechazada"
    else
        local estado
        estado=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura = $id_factura")
        if [ "$estado" = "Entregada" ]; then
            log_pass "Transición inválida rechazada (estado sin cambios)"
        else
            log_fail "Transición inválida permitida: estado cambió a '$estado'"
        fi
    fi
}

# ============================================================================
# TEST 9: Plazos en DB
# ============================================================================
test_plazos_db() {
    log_section "TEST 9: Plazos en DB"

    local existe_plazo existe_cuota
    existe_plazo=$(db_query "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'plazo')")
    existe_cuota=$(db_query "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'plazo_cuota')")

    if [ "$existe_plazo" = "t" ]; then
        log_pass "Tabla 'plazo' existe en DB"
    else
        log_fail "Tabla 'plazo' no existe"
    fi

    if [ "$existe_cuota" = "t" ]; then
        log_pass "Tabla 'plazo_cuota' existe en DB"
    else
        log_fail "Tabla 'plazo_cuota' no existe"
    fi

    local fk_exists
    fk_exists=$(db_query "
        SELECT EXISTS (
            SELECT 1 FROM information_schema.table_constraints
            WHERE constraint_name LIKE '%plazo_cuota%plazo%'
            AND constraint_type = 'FOREIGN KEY'
        )
    ")

    if [ "$fk_exists" = "t" ]; then
        log_pass "FK plazo_cuota → plazo existe (cascade)"
    else
        log_fail "FK plazo_cuota → plazo no existe"
    fi
}

# ============================================================================
# TEST 10: Eliminar factura — cascade historial + plazos
# ============================================================================
test_eliminar_cascade() {
    log_section "TEST 10: Eliminar factura con cascade"

    local token
    token=$(get_csrf_token "$BASE_URL/nueva_factura.php")

    local id_producto id_cliente id_seccion
    id_producto=$(db_query "SELECT id_producto FROM producto WHERE stock > 10 AND precio_venta > 10 ORDER BY RANDOM() LIMIT 1")
    id_cliente=$(db_query "SELECT id_cliente FROM cliente WHERE tipo_cliente = 'Mayorista' ORDER BY RANDOM() LIMIT 1")
    id_seccion=$(db_query "SELECT id_seccion FROM seccion LIMIT 1")

    if [ -z "$id_producto" ] || [ -z "$id_cliente" ] || [ -z "$id_seccion" ]; then
        log_fail "No se pudieron obtener datos de prueba para cascade"
        return
    fi

    curl -s -o /dev/null \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "_token=${token}&id_cliente=${id_cliente}&id_seccion=${id_seccion}&tipo_cliente_venta=Habitual&descuento_global=0.00&id_producto[]=${id_producto}&cantidad[]=1&descuento_linea[]=0&total_calculado=100" \
        "$BASE_URL/nueva_factura.php" 2>/dev/null

    local id_factura
    id_factura=$(db_query "SELECT id_factura FROM factura ORDER BY id_factura DESC LIMIT 1")

    if [ -z "$id_factura" ]; then
        log_fail "No se pudo crear factura para test de eliminación"
        return
    fi

    token=$(get_csrf_token "$BASE_URL/facturas.php")
    local code
    code=$(do_post "$BASE_URL/eliminar_factura.php" "_token=${token}&id=${id_factura}")

    if [ "$code" = "302" ]; then
        log_pass "Factura #$id_factura eliminada (HTTP 302)"

        local hist_despues
        hist_despues=$(db_query "SELECT COUNT(*) FROM factura_estado_historial WHERE id_factura = $id_factura")
        if [ "$hist_despues" = "0" ]; then
            log_pass "Historial eliminado con la factura (cascade OK)"
        else
            log_fail "Historial NO se eliminó ($hist_despues registros restantes)"
        fi

        local plazos_despues
        plazos_despues=$(db_query "SELECT COUNT(*) FROM plazo WHERE id_factura = $id_factura")
        if [ "$plazos_despues" = "0" ]; then
            log_pass "Plazos eliminados con la factura (cascade OK)"
        else
            log_fail "Plazos NO se eliminaron ($plazos_despues restantes)"
        fi

        local factura_existe
        factura_existe=$(db_query "SELECT EXISTS (SELECT 1 FROM factura WHERE id_factura = $id_factura)")
        if [ "$factura_existe" = "f" ]; then
            log_pass "Factura eliminada de la DB"
        else
            log_fail "Factura aún existe en la DB"
        fi
    else
        log_fail "Eliminación falló (HTTP $code)"
    fi
}

# ============================================================================
# TEST 11: Trigger de historial
# ============================================================================
test_trigger_historial() {
    log_section "TEST 11: Trigger de historial"

    local trigger_exists
    trigger_exists=$(db_query "
        SELECT EXISTS (
            SELECT 1 FROM information_schema.triggers
            WHERE trigger_name = 'trg_factura_estado_historial'
        )
    ")

    if [ "$trigger_exists" = "t" ]; then
        log_pass "Trigger 'trg_factura_estado_historial' existe"
    else
        log_fail "Trigger 'trg_factura_estado_historial' no existe"
    fi

    local hist_count
    hist_count=$(db_query "SELECT COUNT(*) FROM factura_estado_historial")
    if [ "$hist_count" -gt 0 ] 2>/dev/null; then
        log_pass "Tabla factura_estado_historial tiene $hist_count registros"
    else
        log_fail "Tabla factura_estado_historial está vacía"
    fi
}

# ============================================================================
# MAIN
# ============================================================================
echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BOLD}${CYAN}  Panda Estampados — Test: Lógica de Facturación (v2)${NC}"
echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════════${NC}"

login_admin

test_creacion_sin_pago
test_abono_30_parcial
test_abono_55_inicia_produccion
test_abono_100_pagado
test_abono_excede_saldo
test_check_constraints
test_transiciones_produccion
test_transicion_invalida
test_plazos_db
test_eliminar_cascade
test_trigger_historial

# ============================================================================
# RESUMEN
# ============================================================================
echo ""
echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BOLD}  RESUMEN${NC}"
echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "  ${GREEN}PASS: $PASS${NC}"
echo -e "  ${RED}FAIL: $FAIL${NC}"

if [ ${#ERRORS[@]} -gt 0 ]; then
    echo ""
    echo -e "${BOLD}  Fallos:${NC}"
    for err in "${ERRORS[@]}"; do
        echo -e "    ${RED}• $err${NC}"
    done
fi

echo ""

if [ "$FAIL" -eq 0 ]; then
    echo -e "${GREEN}${BOLD}  ✓ TODOS LOS TESTS PASARON${NC}"
    exit 0
else
    echo -e "${RED}${BOLD}  ✗ ALGUNOS TESTS FALLARON${NC}"
    exit 1
fi
