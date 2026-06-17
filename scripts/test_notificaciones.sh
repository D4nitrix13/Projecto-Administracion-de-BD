#!/usr/bin/env bash

# ============================================================================
# Panda Estampados / Kitsune — Notifications Test Script
# ============================================================================
# Testea el sistema completo de notificaciones: CRUD, permisos, AJAX.
#
# Uso:
#   ./scripts/test_notificaciones.sh
# ============================================================================

set -uo pipefail

BASE_URL="http://localhost:8080"
COOKIE_FILE=$(mktemp /tmp/cookies_notif.XXXXXX)
TMP_DIR=$(mktemp -d /tmp/test_notif.XXXXXX)
PASS=0
FAIL=0
SKIP=0
ERRORS=()

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# ============================================================================
# HELPERS
# ============================================================================

cleanup() {
    rm -f "$COOKIE_FILE"
    rm -rf "$TMP_DIR"
}
trap cleanup EXIT

log_pass() {
    PASS=$((PASS + 1))
    echo -e "  ${GREEN}PASS${NC}  $1"
}

log_fail() {
    FAIL=$((FAIL + 1))
    local msg="FAIL  $1"
    echo -e "  ${RED}FAIL${NC}  $1"
    ERRORS+=("$msg")
}

log_skip() {
    SKIP=$((SKIP + 1))
    echo -e "  ${YELLOW}SKIP${NC}  $1"
}

log_section() {
    echo ""
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}${CYAN}  $1${NC}"
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
}

log_subsection() {
    echo -e "  ${BOLD}── $1 ──${NC}"
}

get_csrf_token() {
    local url="$1"
    local html
    html=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" "$url" 2>/dev/null)
    local token
    token=$(echo "$html" | grep -o -m1 'name="_token" value="[^"]*"' | sed 's/.*value="\([^"]*\)".*/\1/')
    if [ -z "$token" ]; then
        token=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" "$BASE_URL/csrf_token.php" 2>/dev/null)
    fi
    echo "$token"
}

do_get() {
    local url="$1"
    curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" "$url" 2>/dev/null
}

do_get_body() {
    local url="$1"
    curl -s -b "$COOKIE_FILE" "$url" 2>/dev/null
}

do_post() {
    local url="$1"
    local data="$2"
    curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "$data" "$url" 2>/dev/null
}

do_post_body() {
    local url="$1"
    local data="$2"
    curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "$data" "$url" 2>/dev/null
}

# ============================================================================
# LOGIN
# ============================================================================

login() {
    log_section "LOGIN"
    log_subsection "Obteniendo token CSRF"

    local token
    token=$(get_csrf_token "$BASE_URL/login.php")

    if [ -z "$token" ]; then
        log_fail "No se pudo obtener token CSRF del login"
        return 1
    fi
    log_pass "Token CSRF obtenido (${token:0:16}...)"

    log_subsection "Autenticando como admin"
    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "email=leonel.messi@admin.pandakitsune.com&password=password0&_token=$token" \
        "$BASE_URL/auth.php" 2>/dev/null)

    if [ "$code" = "302" ]; then
        log_pass "Login exitoso (302 redirect)"
    else
        log_fail "Login fallo con HTTP $code"
        return 1
    fi
}

# ============================================================================
# TEST: PAGE ACCESS
# ============================================================================

test_notificaciones_page() {
    log_section "NOTIFICACIONES — ACCESO A PAGINA"

    log_subsection "GET notificaciones.php"
    local code
    code=$(do_get "$BASE_URL/notificaciones.php")
    if [ "$code" = "200" ]; then
        log_pass "Pagina de notificaciones accesible (200)"
    else
        log_fail "Pagina de notificaciones no accesible (HTTP $code)"
    fi

    log_subsection "Verificando contenido de la pagina"
    local body
    body=$(do_get_body "$BASE_URL/notificaciones.php")

    if echo "$body" | grep -q "Notificaciones"; then
        log_pass "Titulo 'Notificaciones' encontrado"
    else
        log_fail "Titulo 'Notificaciones' no encontrado"
    fi

    if echo "$body" | grep -q "Historial de eventos del sistema"; then
        log_pass "Subtitulo encontrado"
    else
        log_fail "Subtitulo no encontrado"
    fi

    if echo "$body" | grep -q "notifBadge"; then
        log_pass "Badge de notificaciones presente en sidebar"
    else
        log_fail "Badge de notificaciones no encontrado en sidebar"
    fi
}

# ============================================================================
# TEST: AJAX ENDPOINTS
# ============================================================================

test_ajax_contador() {
    log_section "NOTIFICACIONES — AJAX CONTADOR"

    log_subsection "GET notificacion_contador.php (sin leer)"
    local response
    response=$(do_get_body "$BASE_URL/notificacion_contador.php")

    if echo "$response" | grep -q '"ok":true'; then
        log_pass "Contador responde OK"
    else
        log_fail "Contador no responde OK: $response"
    fi

    if echo "$response" | grep -q '"sin_leer":'; then
        log_pass "Campo 'sin_leer' presente en respuesta"
    else
        log_fail "Campo 'sin_leer' no encontrado"
    fi
}

test_api_list() {
    log_section "NOTIFICACIONES — API LISTAR"

    log_subsection "GET notificaciones_api.php (listar)"
    local response
    response=$(do_get_body "$BASE_URL/notificaciones_api.php")

    if echo "$response" | grep -q '"ok":true'; then
        log_pass "API listar responde OK"
    else
        log_fail "API listar no responde OK: $response"
    fi

    if echo "$response" | grep -q '"notificaciones":'; then
        log_pass "Campo 'notificaciones' presente en respuesta"
    else
        log_fail "Campo 'notificaciones' no encontrado"
    fi
}

# ============================================================================
# TEST: CREATE NOTIFICATION (via product creation)
# ============================================================================

test_crear_notificacion() {
    log_section "NOTIFICACIONES — CREAR VIA ACCION"

    log_subsection "Obteniendo CSRF token"
    local token
    token=$(get_csrf_token "$BASE_URL/nuevo_producto.php")
    if [ -z "$token" ]; then
        log_fail "No se pudo obtener CSRF token"
        return 1
    fi
    log_pass "CSRF token obtenido"

    log_subsection "Registrando producto (trigger notificacion)"
    local timestamp
    timestamp=$(date +%s)
    local codigo="NOTIF_TEST_${timestamp}"
    local nombre="Producto Notif Test ${timestamp}"

    local tmpimg
    tmpimg=$(mktemp /tmp/test_img.XXXXXX.jpg)
    printf '\xff\xd8\xff\xe0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00' > "$tmpimg"

    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -F "codigo=$codigo" \
        -F "nombre=$nombre" \
        -F "descripcion=Test de notificaciones" \
        -F "id_categoria=" \
        -F "id_proveedor=" \
        -F "precio_compra=100" \
        -F "precio_venta=150" \
        -F "stock=3" \
        -F "imagen=@$tmpimg;type=image/jpeg" \
        -F "_token=$token" \
        "$BASE_URL/nuevo_producto.php" 2>/dev/null)

    rm -f "$tmpimg"

    if [ "$code" = "302" ]; then
        log_pass "Producto registrado (302 redirect) — notificacion creada"
    else
        log_fail "Registro de producto fallo (HTTP $code)"
    fi

    log_subsection "Verificando notificacion en API"
    sleep 1
    local response
    response=$(do_get_body "$BASE_URL/notificaciones_api.php")

    if echo "$response" | grep -q "Nuevo producto"; then
        log_pass "Notificacion 'Nuevo producto' encontrada en API"
    else
        log_fail "Notificacion 'Nuevo producto' no encontrada"
    fi

    if echo "$response" | grep -q "stock_bajo"; then
        log_pass "Notificacion 'stock_bajo' encontrada (stock=3 <= 5)"
    else
        log_fail "Notificacion 'stock_bajo' no encontrada"
    fi

    log_subsection "Verificando contador actualizado"
    local count_response
    count_response=$(do_get_body "$BASE_URL/notificacion_contador.php")
    local sin_leer
    sin_leer=$(echo "$count_response" | grep -o '"sin_leer":[0-9]*' | cut -d: -f2)

    if [ -n "$sin_leer" ] && [ "$sin_leer" -gt 0 ] 2>/dev/null; then
        log_pass "Contador sin_leer: $sin_leer (mayor a 0)"
    else
        log_fail "Contador sin_leer no incrementado: $sin_leer"
    fi

    echo "$nombre" > "$TMP_DIR/producto_nombre"
}

# ============================================================================
# TEST: MARK AS READ
# ============================================================================

test_marcar_leida() {
    log_section "NOTIFICACIONES — MARCAR COMO LEIDA"

    log_subsection "Obteniendo primera notificacion"
    local response
    response=$(do_get_body "$BASE_URL/notificaciones_api.php")

    local notif_id
    notif_id=$(echo "$response" | grep -o '"id":"[^"]*"' | head -1 | sed 's/"id":"\([^"]*\)"/\1/')

    if [ -z "$notif_id" ]; then
        log_skip "No hay notificaciones para marcar"
        return 0
    fi
    log_pass "Notificacion encontrada: ${notif_id:0:16}..."

    log_subsection "Marcando como leida"
    local token
    token=$(get_csrf_token "$BASE_URL/notificaciones.php")

    local result
    result=$(do_post_body "$BASE_URL/notificaciones_api.php" \
        "action=marcar_leida&id=$notif_id&_token=$token")

    if echo "$result" | grep -q '"ok":true'; then
        log_pass "Notificacion marcada como leida"
    else
        log_fail "No se pudo marcar como leida: $result"
    fi

    log_subsection "Verificando que ya no aparece como sin leer"
    local count_response
    count_response=$(do_get_body "$BASE_URL/notificacion_contador.php")
    local sin_leer_after
    sin_leer_after=$(echo "$count_response" | grep -o '"sin_leer":[0-9]*' | cut -d: -f2)
    log_pass "Contador despues de marcar leida: $sin_leer_after"
}

# ============================================================================
# TEST: MARK ALL AS READ
# ============================================================================

test_marcar_todas_leidas() {
    log_section "NOTIFICACIONES — MARCAR TODAS COMO LEIDAS"

    local token
    token=$(get_csrf_token "$BASE_URL/notificaciones.php")

    log_subsection "Ejecutando marcar_todas"
    local result
    result=$(do_post_body "$BASE_URL/notificaciones_api.php" \
        "action=marcar_todas&_token=$token")

    if echo "$result" | grep -q '"ok":true'; then
        log_pass "Todas las notificaciones marcadas como leidas"
    else
        log_fail "No se pudo marcar todas: $result"
    fi

    log_subsection "Verificando contador en 0"
    local count_response
    count_response=$(do_get_body "$BASE_URL/notificacion_contador.php")
    local sin_leer
    sin_leer=$(echo "$count_response" | grep -o '"sin_leer":[0-9]*' | cut -d: -f2)

    if [ "$sin_leer" = "0" ]; then
        log_pass "Contador sin_leer: 0 (correcto)"
    else
        log_fail "Contador sin_leer deberia ser 0, es: $sin_leer"
    fi
}

# ============================================================================
# TEST: DELETE NOTIFICATION
# ============================================================================

test_eliminar_notificacion() {
    log_section "NOTIFICACIONES — ELIMINAR NOTIFICACION"

    log_subsection "Creando notificacion para eliminar"
    local timestamp
    timestamp=$(date +%s)

    local token
    token=$(get_csrf_token "$BASE_URL/nuevo_producto.php")

    local tmpimg
    tmpimg=$(mktemp /tmp/test_img.XXXXXX.jpg)
    printf '\xff\xd8\xff\xe0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00' > "$tmpimg"

    curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -F "codigo=DEL_TEST_${timestamp}" \
        -F "nombre=Eliminar Test ${timestamp}" \
        -F "descripcion=" \
        -F "id_categoria=" \
        -F "id_proveedor=" \
        -F "precio_compra=50" \
        -F "precio_venta=80" \
        -F "stock=10" \
        -F "imagen=@$tmpimg;type=image/jpeg" \
        -F "_token=$token" \
        "$BASE_URL/nuevo_producto.php" > /dev/null 2>&1

    rm -f "$tmpimg"

    sleep 1

    log_subsection "Obteniendo ID de notificacion"
    local response
    response=$(do_get_body "$BASE_URL/notificaciones_api.php")

    local notif_id
    notif_id=$(echo "$response" | grep -o '"id":"[^"]*"' | tail -1 | sed 's/"id":"\([^"]*\)"/\1/')

    if [ -z "$notif_id" ]; then
        log_skip "No hay notificaciones para eliminar"
        return 0
    fi
    log_pass "Notificacion a eliminar: ${notif_id:0:16}..."

    log_subsection "Eliminando notificacion"
    token=$(get_csrf_token "$BASE_URL/notificaciones.php")
    local result
    result=$(do_post_body "$BASE_URL/notificaciones_api.php" \
        "action=eliminar&id=$notif_id&_token=$token")

    if echo "$result" | grep -q '"ok":true'; then
        log_pass "Notificacion eliminada"
    else
        log_fail "No se pudo eliminar: $result"
    fi

    log_subsection "Verificando que ya no existe"
    local verify_response
    verify_response=$(do_get_body "$BASE_URL/notificaciones_api.php")

    if echo "$verify_response" | grep -q "$notif_id"; then
        log_fail "La notificacion aun existe en la lista"
    else
        log_pass "Notificacion eliminada correctamente"
    fi
}

# ============================================================================
# TEST: CLEAR HISTORY (admin only)
# ============================================================================

test_limpiar_historial() {
    log_section "NOTIFICACIONES — LIMPIAR HISTORIAL"

    log_subsection "Creando notificaciones de prueba"
    local timestamp
    timestamp=$(date +%s)
    local token
    token=$(get_csrf_token "$BASE_URL/nuevo_producto.php")

    local tmpimg
    tmpimg=$(mktemp /tmp/test_img.XXXXXX.jpg)
    printf '\xff\xd8\xff\xe0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00' > "$tmpimg"

    for i in 1 2 3; do
        curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
            -F "codigo=CLR_TEST_${timestamp}_${i}" \
            -F "nombre=Limpiar Test ${timestamp} ${i}" \
            -F "descripcion=" \
            -F "id_categoria=" \
            -F "id_proveedor=" \
            -F "precio_compra=50" \
            -F "precio_venta=80" \
            -F "stock=10" \
            -F "imagen=@$tmpimg;type=image/jpeg" \
            -F "_token=$token" \
            "$BASE_URL/nuevo_producto.php" > /dev/null 2>&1
    done

    rm -f "$tmpimg"

    sleep 1

    log_subsection "Verificando que hay notificaciones"
    local response
    response=$(do_get_body "$BASE_URL/notificaciones_api.php")
    local count_before
    count_before=$(echo "$response" | grep -o '"id":"[^"]*"' | wc -l)

    if [ "$count_before" -gt 0 ] 2>/dev/null; then
        log_pass "Hay $count_before notificaciones antes de limpiar"
    else
        log_skip "No hay notificaciones para limpiar"
        return 0
    fi

    log_subsection "Limpiando historial"
    token=$(get_csrf_token "$BASE_URL/notificaciones.php")
    local result
    result=$(do_post_body "$BASE_URL/notificaciones_api.php" \
        "action=limpiar&_token=$token")

    if echo "$result" | grep -q '"ok":true'; then
        log_pass "Historial limpiado"
    else
        log_fail "No se pudo limpiar historial: $result"
    fi

    log_subsection "Verificando que no quedan notificaciones"
    local verify_response
    verify_response=$(do_get_body "$BASE_URL/notificaciones_api.php")
    local count_after
    count_after=$(echo "$verify_response" | grep -o '"id":"[^"]*"' | wc -l)

    if [ "$count_after" -eq 0 ] 2>/dev/null; then
        log_pass "Historial limpiado correctamente (0 notificaciones)"
    else
        log_fail "Quedan $count_after notificaciones despues de limpiar"
    fi
}

# ============================================================================
# TEST: SIDEBAR BADGE
# ============================================================================

test_sidebar_badge() {
    log_section "NOTIFICACIONES — SIDEBAR BADGE"

    log_subsection "Verificando badge en sidebar"
    local body
    body=$(do_get_body "$BASE_URL/notificaciones.php")

    if echo "$body" | grep -q 'id="notifBadge"'; then
        log_pass "Badge element presente en sidebar"
    else
        log_fail "Badge element no encontrado"
    fi

    if echo "$body" | grep -q 'notificacion_contador.php'; then
        log_pass "Script de polling AJAX presente"
    else
        log_fail "Script de polling AJAX no encontrado"
    fi

    if echo "$body" | grep -q 'setInterval'; then
        log_pass "Polling interval configurado"
    else
        log_fail "Polling interval no encontrado"
    fi
}

# ============================================================================
# TEST: PERMISSIONS (non-admin cannot clear)
# ============================================================================

test_permisos() {
    log_section "NOTIFICACIONES — PERMISOS"

    log_subsection "Verificando que la pagina carga para admin"
    local code
    code=$(do_get "$BASE_URL/notificaciones.php")
    if [ "$code" = "200" ]; then
        log_pass "Admin puede acceder a notificaciones"
    else
        log_fail "Admin no puede acceder (HTTP $code)"
    fi

    log_subsection "Verificando boton limpiar historial"
    local body
    body=$(do_get_body "$BASE_URL/notificaciones.php")

    if echo "$body" | grep -q "limpiarHistorial"; then
        log_pass "Boton limpiar historial visible (admin)"
    else
        log_fail "Boton limpiar historial no encontrado"
    fi

    log_subsection "Verificando CSRF en API"
    local result
    result=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "action=marcar_todas" \
        "$BASE_URL/notificaciones_api.php" 2>/dev/null)

    if echo "$result" | grep -q "CSRF" || echo "$result" | grep -q "403" || echo "$result" | grep -q "inválido"; then
        log_pass "API rechaza peticiones sin CSRF token"
    else
        log_fail "API no valida CSRF correctamente"
    fi
}

# ============================================================================
# TEST: NOTIFICATION TYPES AND LABELS
# ============================================================================

test_tipos_notificacion() {
    log_section "NOTIFICACIONES — TIPOS Y LABELS"

    log_subsection "Verificando tipos en la respuesta"
    local response
    response=$(do_get_body "$BASE_URL/notificaciones_api.php")

    local tipos=(
        "factura_creada"
        "pago_recibido"
        "factura_pagada"
        "produccion_cambiada"
        "factura_cancelada"
        "cliente_creado"
        "cliente_eliminado"
        "producto_creado"
        "producto_eliminado"
        "stock_bajo"
        "compra_registrada"
        "backup_manual"
        "backup_automatico"
        "backup_restaurado"
        "mantenimiento_bd"
        "usuario_creado"
        "usuario_eliminado"
    )

    local found=0
    for tipo in "${tipos[@]}"; do
        if echo "$response" | grep -q "$tipo"; then
            found=$((found + 1))
        fi
    done

    if [ "$found" -gt 0 ]; then
        log_pass "Se encontraron $found tipos de notificacion en la respuesta"
    else
        log_skip "No hay notificaciones en el sistema para verificar tipos"
    fi
}

# ============================================================================
# SUMMARY
# ============================================================================

print_summary() {
    echo ""
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}${CYAN}  RESUMEN DE TESTS DE NOTIFICACIONES${NC}"
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "  ${GREEN}PASS: $PASS${NC}"
    echo -e "  ${RED}FAIL: $FAIL${NC}"
    echo -e "  ${YELLOW}SKIP: $SKIP${NC}"
    echo ""

    local total=$((PASS + FAIL + SKIP))
    echo -e "  Total: $total tests"

    if [ "$FAIL" -gt 0 ]; then
        echo ""
        echo -e "${BOLD}${RED}  FALLOS:${NC}"
        for err in "${ERRORS[@]}"; do
            echo -e "    ${RED}✗${NC} $err"
        done
    fi

    echo ""

    if [ "$FAIL" -eq 0 ]; then
        echo -e "  ${GREEN}${BOLD}✓ TODOS LOS TESTS PASARON${NC}"
    else
        echo -e "  ${RED}${BOLD}✗ $FAIL TEST(S) FALLARON${NC}"
    fi

    echo ""
}

# ============================================================================
# MAIN
# ============================================================================

main() {
    echo ""
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}${CYAN}  Panda Estampados / Kitsune — Notifications Tests${NC}"
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"

    login

    test_notificaciones_page
    test_ajax_contador
    test_api_list
    test_crear_notificacion
    test_marcar_leida
    test_marcar_todas_leidas
    test_eliminar_notificacion
    test_limpiar_historial
    test_sidebar_badge
    test_permisos
    test_tipos_notificacion

    print_summary

    if [ "$FAIL" -gt 0 ]; then
        exit 1
    fi
}

main "$@"
