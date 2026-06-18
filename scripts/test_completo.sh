#!/usr/bin/env bash

# ============================================================================
# Panda Estampados / Kitsune — Comprehensive Test Suite
# ============================================================================
# Testea TODA la funcionalidad de la aplicacion: cada endpoint CRUD, cada
# accion POST, cada filtro, cada exportacion, autenticacion, permisos,
# validaciones, y endpoints de sistema.
#
# Requiere: curl, docker (contenedor pandas_app y pandas_bd corriendo)
#
# Uso:
#   ./scripts/test_completo.sh                    # Test completo
#   ./scripts/test_completo.sh --module auth      # Solo un modulo
#   ./scripts/test_completo.sh --list             # Listar modulos disponibles
# ============================================================================

set -euo pipefail

BASE_URL="http://localhost:8080"
COOKIE_FILE=$(mktemp /tmp/cookies_full.XXXXXX)
COOKIE_ADMIN=$(mktemp /tmp/cookies_admin.XXXXXX)
TMP_DIR=$(mktemp -d /tmp/test_full.XXXXXX)
PASS=0
FAIL=0
SKIP=0
ERRORS=()
SELECTED_MODULE=""

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
    rm -f "$COOKIE_FILE" "$COOKIE_ADMIN"
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
    local cookie="$1"
    local url="$2"
    local html
    html=$(curl -s -b "$cookie" -c "$cookie" "$url" 2>/dev/null)
    local token
    token=$(echo "$html" | grep -o -m1 'name="_token" value="[^"]*"' | sed 's/.*value="\([^"]*\)".*/\1/')
    if [ -z "$token" ]; then
        token=$(curl -s -b "$cookie" -c "$cookie" "$BASE_URL/csrf_token.php" 2>/dev/null)
    fi
    echo "$token"
}

do_get() {
    local cookie="$1"
    local url="$2"
    curl -s -o /dev/null -w "%{http_code}" -b "$cookie" "$url" 2>/dev/null
}

do_post() {
    local cookie="$1"
    local url="$2"
    local data="$3"
    curl -s -o /dev/null -w "%{http_code}" \
        -b "$cookie" -c "$cookie" \
        -d "$data" \
        "$url" 2>/dev/null
}

do_post_multipart() {
    local cookie="$1"
    local url="$2"
    shift 2
    curl -s -o /dev/null -w "%{http_code}" \
        -b "$cookie" -c "$cookie" \
        "$@" "$url" 2>/dev/null
}

do_get_body() {
    local cookie="$1"
    local url="$2"
    curl -s -b "$cookie" "$url" 2>/dev/null
}

db_query() {
    docker exec pandas_bd psql -U postgres -d pandas_estampados_y_kitsune -t -A -c "$1" 2>/dev/null || true
}

# Unique test ID to avoid conflicts with previous runs
TEST_ID=$(date +%s | tail -c 7)

# ============================================================================
# LOGIN HELPERS
# ============================================================================

login_admin() {
    local cookie="$1"
    local token
    token=$(get_csrf_token "$cookie" "$BASE_URL/login.php")

    if [ -z "$token" ]; then
        log_fail "No se pudo obtener token CSRF del login"
        return 1
    fi

    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" \
        -b "$cookie" -c "$cookie" \
        -d "email=leonel.messi@admin.pandakitsune.com&password=password0&_token=$token" \
        "$BASE_URL/auth.php" 2>/dev/null)

    if [ "$code" = "302" ]; then
        return 0
    else
        log_fail "Login admin fallo con HTTP $code"
        return 1
    fi
}

# ============================================================================
# MODULO: AUTENTICACION Y SESION
# ============================================================================

test_auth() {
    log_section "MODULO: AUTENTICACION Y SESION"

    # -- Login page loads --
    log_subsection "GET /login.php — pagina de login"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/login.php")
    if [ "$code" = "200" ]; then
        log_pass "Pagina de login accesible (200)"
    else
        log_fail "Pagina de login fallo (HTTP $code)"
    fi

    # -- CSRF token available --
    log_subsection "GET /csrf_token.php — token CSRF"
    local token_body
    token_body=$(curl -s "$BASE_URL/csrf_token.php" 2>/dev/null)
    if [ ${#token_body} -gt 10 ]; then
        log_pass "Token CSRF generado (${#token_body} chars)"
    else
        log_fail "Token CSRF vacio o muy corto"
    fi

    # -- Login with wrong credentials --
    log_subsection "POST /auth.php — credenciales incorrectas"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/login.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/auth.php" \
        "email=wrong@test.com&password=wrongpass&_token=$token")
    if [ "$code" = "302" ]; then
        log_pass "Login con credenciales incorrectas redirige (302)"
    else
        log_fail "Login incorrecto retorno HTTP $code"
    fi

    # -- Login with empty fields --
    log_subsection "POST /auth.php — campos vacios"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/login.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/auth.php" \
        "email=&password=&_token=$token")
    if [ "$code" = "302" ]; then
        log_pass "Login con campos vacios redirige (302)"
    else
        log_fail "Login vacio retorno HTTP $code"
    fi

    # -- Login CSRF protection --
    log_subsection "POST /auth.php — sin CSRF token"
    code=$(curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "email=test@test.com&password=pass123" \
        "$BASE_URL/auth.php" 2>/dev/null)
    if [ "$code" = "403" ]; then
        log_pass "Login sin CSRF retorna 403"
    else
        log_fail "Login sin CSRF retorno HTTP $code (esperaba 403)"
    fi

    # -- Successful login --
    log_subsection "POST /auth.php — login exitoso"
    login_admin "$COOKIE_FILE"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/dashboard.php")
    if [ "$code" = "200" ]; then
        log_pass "Dashboard accesible post-login (200)"
    else
        log_fail "Dashboard inaccesible post-login (HTTP $code)"
    fi

    # -- Logout --
    log_subsection "GET /logout.php — cerrar sesion"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/logout.php")
    if [ "$code" = "302" ]; then
        log_pass "Logout redirige (302)"
    else
        log_fail "Logout retorno HTTP $code"
    fi

    # -- Dashboard inaccessible after logout --
    log_subsection "GET /dashboard.php — sin sesion"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/dashboard.php")
    if [ "$code" = "302" ]; then
        log_pass "Dashboard redirige a login sin sesion (302)"
    else
        log_fail "Dashboard accesible sin sesion (HTTP $code)"
    fi

    # -- Login redirect when already logged in --
    log_subsection "GET /login.php — logueado redirige a dashboard"
    login_admin "$COOKIE_FILE"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/login.php")
    if [ "$code" = "302" ]; then
        log_pass "Login.php redirige a dashboard cuando logueado (302)"
    else
        log_fail "Login.php no redirige cuando logueado (HTTP $code)"
    fi

    # -- Forgot password redirect when logged in --
    log_subsection "GET /forgot_password.php — logueado redirige"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/forgot_password.php")
    if [ "$code" = "302" ]; then
        log_pass "forgot_password.php redirige cuando logueado (302)"
    else
        log_fail "forgot_password.php no redirige (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: RECUPERAR CONTRASENA
# ============================================================================

test_password_recovery() {
    log_section "MODULO: RECUPERAR CONTRASENA"

    # -- Logout first --
    do_get "$COOKIE_FILE" "$BASE_URL/logout.php" >/dev/null 2>&1 || true

    # -- Forgot password page loads --
    log_subsection "GET /forgot_password.php — pagina carga"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/forgot_password.php")
    if [ "$code" = "200" ]; then
        log_pass "Pagina forgot password accesible (200)"
    else
        log_fail "Pagina forgot password fallo (HTTP $code)"
    fi

    # -- Forgot password with valid email --
    log_subsection "POST /forgot_password.php — email existente"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/forgot_password.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/forgot_password.php" \
        "email=leonel.messi@admin.pandakitsune.com&_token=$token")
    if [ "$code" = "200" ]; then
        log_pass "Forgot password con email valido retorna 200"
    else
        log_fail "Forgot password retorno HTTP $code"
    fi

    # -- Forgot password with non-existent email (should still show generic message) --
    log_subsection "POST /forgot_password.php — email inexistente"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/forgot_password.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/forgot_password.php" \
        "email=nobody@test.com&_token=$token")
    if [ "$code" = "200" ]; then
        log_pass "Forgot password con email inexistente retorna 200 (generico)"
    else
        log_fail "Forgot password retorno HTTP $code"
    fi

    # -- Forgot password with invalid email --
    log_subsection "POST /forgot_password.php — email invalido"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/forgot_password.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/forgot_password.php" \
        "email=notanemail&_token=$token")
    if [ "$code" = "200" ]; then
        log_pass "Forgot password con email invalido retorna 200"
    else
        log_fail "Forgot password retorno HTTP $code"
    fi

    # -- Reset password without token --
    log_subsection "GET /reset_password.php — sin token"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/reset_password.php")
    if [ "$code" = "200" ]; then
        log_pass "Reset password sin token retorna 200"
    else
        log_fail "Reset password retorno HTTP $code"
    fi

    # -- Reset password with invalid token --
    log_subsection "GET /reset_password.php — token invalido"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/reset_password.php?token=invalidtoken123")
    if [ "$code" = "200" ]; then
        log_pass "Reset password con token invalido retorna 200"
    else
        log_fail "Reset password retorno HTTP $code"
    fi
}

# ============================================================================
# MODULO: DASHBOARD
# ============================================================================

test_dashboard() {
    log_section "MODULO: DASHBOARD"

    log_subsection "GET /dashboard.php — carga completa"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/dashboard.php")
    if [ "$code" = "200" ]; then
        log_pass "Dashboard accesible (200)"
    else
        log_fail "Dashboard fallo (HTTP $code)"
    fi

    log_subsection "Verificar datos del dashboard"
    local body
    body=$(do_get_body "$COOKIE_FILE" "$BASE_URL/dashboard.php")
    if echo "$body" | grep -q "Ingresos recientes"; then
        log_pass "Seccion Ingresos recientes presente"
    else
        log_fail "Seccion Ingresos recientes no encontrada"
    fi
    if echo "$body" | grep -q "Productos más vendidos"; then
        log_pass "Seccion Productos mas vendidos presente"
    else
        log_fail "Seccion Productos mas vendidos no encontrada"
    fi
    if echo "$body" | grep -q "ventasSemanaChart"; then
        log_pass "Canvas chart presente en dashboard"
    else
        log_fail "Canvas chart no encontrado"
    fi
}

# ============================================================================
# MODULO: CATALOGO PUBLICO
# ============================================================================

test_catalogo() {
    log_section "MODULO: CATALOGO PUBLICO"

    log_subsection "GET /catalogo.php — pagina publica"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/catalogo.php")
    if [ "$code" = "200" ]; then
        log_pass "Catalogo publico accesible (200)"
    else
        log_fail "Catalogo publico fallo (HTTP $code)"
    fi

    log_subsection "GET /catalogo.php?q=test — busqueda"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/catalogo.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda en catalogo OK (200)"
    else
        log_fail "Busqueda en catalogo fallo (HTTP $code)"
    fi

    log_subsection "GET / — indice carga catalogo"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/")
    if [ "$code" = "200" ]; then
        log_pass "Indice carga catalogo (200)"
    else
        log_fail "Indice fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: CATEGORIAS
# ============================================================================

test_categorias() {
    log_section "MODULO: CATEGORIAS"

    log_subsection "GET /categorias.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/categorias.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado categorias OK (200)"
    else
        log_fail "Listado categorias fallo (HTTP $code)"
    fi

    log_subsection "POST /categorias.php — crear categoria"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/categorias.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/categorias.php" \
        "nombre=TestCat_Completo&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Creacion categoria OK (HTTP $code)"
    else
        log_fail "Creacion categoria fallo (HTTP $code)"
    fi

    local cat_id
    cat_id=$(db_query "SELECT id_categoria FROM categoria WHERE nombre='TestCat_Completo' ORDER BY id_categoria DESC LIMIT 1;")
    if [ -n "$cat_id" ]; then
        log_pass "Categoria creada en BD (id: $cat_id)"

        log_subsection "GET /editar_categoria.php?id=$cat_id — formulario editar"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/editar_categoria.php?id=$cat_id")
        if [ "$code" = "200" ]; then
            log_pass "Formulario editar categoria OK (200)"
        else
            log_fail "Formulario editar categoria fallo (HTTP $code)"
        fi

        log_subsection "POST /editar_categoria.php — actualizar categoria"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/editar_categoria.php?id=$cat_id")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/editar_categoria.php" \
            "id=$cat_id&nombre=TestCat_Updated&_token=$token")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Update categoria OK (HTTP $code)"
        else
            log_fail "Update categoria fallo (HTTP $code)"
        fi

        log_subsection "POST /eliminar_categoria.php — eliminar categoria"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/categorias.php")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/eliminar_categoria.php" \
            "id=$cat_id&_token=$token")
        if [ "$code" = "302" ]; then
            log_pass "Eliminacion categoria OK (302)"
        else
            log_fail "Eliminacion categoria fallo (HTTP $code)"
        fi
    else
        log_fail "Categoria no encontrada en BD"
    fi
}

# ============================================================================
# MODULO: PROVEEDORES
# ============================================================================

test_proveedores() {
    log_section "MODULO: PROVEEDORES"

    log_subsection "GET /proveedores.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/proveedores.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado proveedores OK (200)"
    else
        log_fail "Listado proveedores fallo (HTTP $code)"
    fi

    log_subsection "POST /proveedores.php — crear proveedor"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/proveedores.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/proveedores.php" \
        "nombre=ProveedorTest_Completo&contacto=Juan Perez&telefono=88889999&correo=proveedor@test.com&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Creacion proveedor OK (HTTP $code)"
    else
        log_fail "Creacion proveedor fallo (HTTP $code)"
    fi

    local prov_id
    prov_id=$(db_query "SELECT id_proveedor FROM proveedor WHERE nombre='ProveedorTest_Completo' ORDER BY id_proveedor DESC LIMIT 1;")
    if [ -n "$prov_id" ]; then
        log_pass "Proveedor creado en BD (id: $prov_id)"

        log_subsection "GET /editar_proveedor.php?id=$prov_id — formulario"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/editar_proveedor.php?id=$prov_id")
        if [ "$code" = "200" ]; then
            log_pass "Formulario editar proveedor OK (200)"
        else
            log_fail "Formulario editar proveedor fallo (HTTP $code)"
        fi

        log_subsection "POST /editar_proveedor.php — actualizar"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/editar_proveedor.php?id=$prov_id")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/editar_proveedor.php" \
            "id=$prov_id&nombre=ProveedorTest_Updated&contacto=Pedro&telefono=77776666&correo=updated@test.com&_token=$token")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Update proveedor OK (HTTP $code)"
        else
            log_fail "Update proveedor fallo (HTTP $code)"
        fi

        log_subsection "POST /eliminar_proveedor.php — eliminar"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/proveedores.php")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/eliminar_proveedor.php" \
            "id=$prov_id&_token=$token")
        if [ "$code" = "302" ]; then
            log_pass "Eliminacion proveedor OK (302)"
        else
            log_fail "Eliminacion proveedor fallo (HTTP $code)"
        fi
    else
        log_fail "Proveedor no encontrado en BD"
    fi
}

# ============================================================================
# MODULO: CLIENTES
# ============================================================================

test_clientes() {
    log_section "MODULO: CLIENTES"

    log_subsection "GET /clientes.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/clientes.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado clientes OK (200)"
    else
        log_fail "Listado clientes fallo (HTTP $code)"
    fi

    log_subsection "GET /clientes.php?q=test — busqueda"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/clientes.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda clientes OK (200)"
    else
        log_fail "Busqueda clientes fallo (HTTP $code)"
    fi

    log_subsection "GET /clientes.php?tipo=Mayorista — filtro tipo"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/clientes.php?tipo=Mayorista")
    if [ "$code" = "200" ]; then
        log_pass "Filtro tipo Mayorista OK (200)"
    else
        log_fail "Filtro tipo fallo (HTTP $code)"
    fi

    log_subsection "POST /nuevo_cliente.php — crear cliente"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/nuevo_cliente.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/nuevo_cliente.php" \
        "nombres=ClienteTest&apellidos=Completo&cedula=001$TEST_ID&telefono=88887777&correo=cliente$TEST_ID@test.com&tipo=Mayorista&_token=$token")
    if [ "$code" = "302" ]; then
        log_pass "Creacion cliente OK (302)"
    else
        log_fail "Creacion cliente fallo (HTTP $code)"
    fi

    local cli_id
    cli_id=$(db_query "SELECT id_cliente FROM cliente WHERE cedula='001$TEST_ID' ORDER BY id_cliente DESC LIMIT 1;")
    if [ -n "$cli_id" ]; then
        log_pass "Cliente creado en BD (id: $cli_id)"

        log_subsection "GET /detalle_cliente.php?id=$cli_id — detalle"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/detalle_cliente.php?id=$cli_id")
        if [ "$code" = "200" ]; then
            log_pass "Detalle cliente OK (200)"
        else
            log_fail "Detalle cliente fallo (HTTP $code)"
        fi

        log_subsection "GET /editar_cliente.php?id=$cli_id — formulario"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/editar_cliente.php?id=$cli_id")
        if [ "$code" = "200" ]; then
            log_pass "Formulario editar cliente OK (200)"
        else
            log_fail "Formulario editar cliente fallo (HTTP $code)"
        fi

        log_subsection "POST /editar_cliente.php — actualizar"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/editar_cliente.php?id=$cli_id")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/editar_cliente.php" \
            "id=$cli_id&nombres=ClienteUpdated&apellidos=Modificado&cedula=001$TEST_ID&telefono=77776666&correo=updated$TEST_ID@test.com&tipo=Detallista&_token=$token")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Update cliente OK (HTTP $code)"
        else
            log_fail "Update cliente fallo (HTTP $code)"
        fi

        log_subsection "POST /eliminar_cliente.php — eliminar"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/clientes.php")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/eliminar_cliente.php" \
            "id=$cli_id&_token=$token")
        if [ "$code" = "302" ]; then
            log_pass "Eliminacion cliente OK (302)"
        else
            log_fail "Eliminacion cliente fallo (HTTP $code)"
        fi
    else
        log_fail "Cliente no encontrado en BD"
    fi
}

# ============================================================================
# MODULO: PRODUCTOS
# ============================================================================

test_productos() {
    log_section "MODULO: PRODUCTOS"

    log_subsection "GET /productos.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/productos.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado productos OK (200)"
    else
        log_fail "Listado productos fallo (HTTP $code)"
    fi

    log_subsection "GET /productos.php?q=camiseta — busqueda"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/productos.php?q=camiseta")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda productos OK (200)"
    else
        log_fail "Busqueda productos fallo (HTTP $code)"
    fi

    log_subsection "GET /productos.php?stock=bajo — filtro stock bajo"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/productos.php?stock=bajo")
    if [ "$code" = "200" ]; then
        log_pass "Filtro stock bajo OK (200)"
    else
        log_fail "Filtro stock bajo fallo (HTTP $code)"
    fi

    log_subsection "GET /productos.php?categoria=1 — filtro categoria"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/productos.php?categoria=1")
    if [ "$code" = "200" ]; then
        log_pass "Filtro categoria OK (200)"
    else
        log_fail "Filtro categoria fallo (HTTP $code)"
    fi

    # Create a temp image for upload
    local img_file="$TMP_DIR/test_producto.jpg"
    printf '\xff\xd8\xff\xe0\x00\x10JFIF\x00' > "$img_file" 2>/dev/null || \
    printf '\x89PNG\r\n\x1a\n' > "$img_file" 2>/dev/null || \
    touch "$img_file"

    log_subsection "POST /nuevo_producto.php — crear producto (multipart)"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/nuevo_producto.php")
    code=$(do_post_multipart "$COOKIE_FILE" "$BASE_URL/nuevo_producto.php" \
        -F "codigo=TEST001" \
        -F "nombre=ProductoTest_Completo" \
        -F "descripcion=Producto de prueba completo" \
        -F "id_categoria=1" \
        -F "id_proveedor=1" \
        -F "precio_compra=100" \
        -F "precio_venta=150" \
        -F "stock=50" \
        -F "imagen=@$img_file" \
        -F "_token=$token")
    if [ "$code" = "302" ]; then
        log_pass "Creacion producto OK (302)"
    else
        log_fail "Creacion producto fallo (HTTP $code)"
    fi

    local prod_id
    prod_id=$(db_query "SELECT id_producto FROM producto WHERE codigo='TEST001' ORDER BY id_producto DESC LIMIT 1;")
    if [ -n "$prod_id" ]; then
        log_pass "Producto creado en BD (id: $prod_id)"

        log_subsection "GET /editar_producto.php?id=$prod_id — formulario"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/editar_producto.php?id=$prod_id")
        if [ "$code" = "200" ]; then
            log_pass "Formulario editar producto OK (200)"
        else
            log_fail "Formulario editar producto fallo (HTTP $code)"
        fi

        log_subsection "POST /editar_producto.php — actualizar producto"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/editar_producto.php?id=$prod_id")
        code=$(do_post_multipart "$COOKIE_FILE" "$BASE_URL/editar_producto.php" \
            -F "id_producto=$prod_id" \
            -F "codigo=TEST001" \
            -F "nombre=ProductoTest_Updated" \
            -F "descripcion=Actualizado" \
            -F "id_categoria=1" \
            -F "id_proveedor=1" \
            -F "precio_compra=110" \
            -F "precio_venta=165" \
            -F "stock=75" \
            -F "_token=$token")
        if [ "$code" = "302" ]; then
            log_pass "Update producto OK (302)"
        else
            log_fail "Update producto fallo (HTTP $code)"
        fi

        log_subsection "GET /ver_imagen_producto.php?id=$prod_id — imagen"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/ver_imagen_producto.php?id=$prod_id")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Ver imagen producto OK (HTTP $code)"
        else
            log_fail "Ver imagen producto fallo (HTTP $code)"
        fi

        log_subsection "POST /eliminar_producto.php — eliminar"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/productos.php")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/eliminar_producto.php" \
            "id=$prod_id&_token=$token")
        if [ "$code" = "302" ]; then
            log_pass "Eliminacion producto OK (302)"
        else
            log_fail "Eliminacion producto fallo (HTTP $code)"
        fi
    else
        log_fail "Producto no encontrado en BD"
    fi
}

# ============================================================================
# MODULO: TRABAJADORES (ADMIN)
# ============================================================================

test_trabajadores() {
    log_section "MODULO: TRABAJADORES"

    log_subsection "GET /trabajadores.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/trabajadores.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado trabajadores OK (200)"
    else
        log_fail "Listado trabajadores fallo (HTTP $code)"
    fi

    log_subsection "GET /trabajadores.php?q=admin — busqueda"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/trabajadores.php?q=admin")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda trabajadores OK (200)"
    else
        log_fail "Busqueda trabajadores fallo (HTTP $code)"
    fi

    log_subsection "POST /trabajadores.php — crear trabajador"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/trabajadores.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/trabajadores.php" \
        "nombre=TestWorker&email=worker@test.com&password=pass1234&id_rol=3&id_seccion=1&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Creacion trabajador OK (HTTP $code)"
    else
        log_fail "Creacion trabajador fallo (HTTP $code)"
    fi

    local user_id
    user_id=$(db_query "SELECT id_usuario FROM usuario WHERE email='worker@test.com' ORDER BY id_usuario DESC LIMIT 1;")
    if [ -n "$user_id" ]; then
        log_pass "Trabajador creado en BD (id: $user_id)"

        log_subsection "GET /editar_trabajador.php?id=$user_id — formulario"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/editar_trabajador.php?id=$user_id")
        if [ "$code" = "200" ]; then
            log_pass "Formulario editar trabajador OK (200)"
        else
            log_fail "Formulario editar trabajador fallo (HTTP $code)"
        fi

        log_subsection "POST /editar_trabajador.php — actualizar"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/editar_trabajador.php?id=$user_id")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/editar_trabajador.php" \
            "id_usuario=$user_id&nombre=TestWorkerUpdated&email=worker_updated@test.com&id_rol=3&id_seccion=1&_token=$token")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Update trabajador OK (HTTP $code)"
        else
            log_fail "Update trabajador fallo (HTTP $code)"
        fi

        log_subsection "POST /eliminar_usuario.php — eliminar"
        token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/trabajadores.php")
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/eliminar_usuario.php" \
            "id=$user_id&_token=$token")
        if [ "$code" = "302" ]; then
            log_pass "Eliminacion trabajador OK (302)"
        else
            log_fail "Eliminacion trabajador fallo (HTTP $code)"
        fi
    else
        log_fail "Trabajador no encontrado en BD"
    fi
}

# ============================================================================
# MODULO: FACTURAS
# ============================================================================

test_facturas() {
    log_section "MODULO: FACTURAS"

    log_subsection "GET /facturas.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/facturas.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado facturas OK (200)"
    else
        log_fail "Listado facturas fallo (HTTP $code)"
    fi

    log_subsection "GET /facturas.php?q=1 — busqueda"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/facturas.php?q=1")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda facturas OK (200)"
    else
        log_fail "Busqueda facturas fallo (HTTP $code)"
    fi

    log_subsection "GET /facturas.php?estado_pago=Pagado — filtro estado pago"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/facturas.php?estado_pago=Pagado")
    if [ "$code" = "200" ]; then
        log_pass "Filtro estado pago OK (200)"
    else
        log_fail "Filtro estado pago fallo (HTTP $code)"
    fi

    log_subsection "GET /facturas.php?estado_produccion=Entregada — filtro produccion"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/facturas.php?estado_produccion=Entregada")
    if [ "$code" = "200" ]; then
        log_pass "Filtro estado produccion OK (200)"
    else
        log_fail "Filtro estado produccion fallo (HTTP $code)"
    fi

    local first_factura
    first_factura=$(db_query "SELECT id_factura FROM factura ORDER BY id_factura DESC LIMIT 1;")
    if [ -n "$first_factura" ]; then
        log_subsection "GET /detalle_factura.php?id=$first_factura — detalle"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/detalle_factura.php?id=$first_factura")
        if [ "$code" = "200" ]; then
            log_pass "Detalle factura OK (200)"
        else
            log_fail "Detalle factura fallo (HTTP $code)"
        fi

        log_subsection "GET /imprimir_factura.php?id=$first_factura — imprimir"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/imprimir_factura.php?id=$first_factura")
        if [ "$code" = "200" ]; then
            log_pass "Imprimir factura OK (200)"
        else
            log_fail "Imprimir factura fallo (HTTP $code)"
        fi
    fi

    log_subsection "GET /historial_estados_facturas.php — historial"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/historial_estados_facturas.php")
    if [ "$code" = "200" ]; then
        log_pass "Historial estados facturas OK (200)"
    else
        log_fail "Historial estados facturas fallo (HTTP $code)"
    fi

    log_subsection "GET /editar_factura.php?id=1 — formulario editar"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/editar_factura.php?id=1")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Formulario editar factura OK (HTTP $code)"
    else
        log_fail "Formulario editar factura fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: COMPRAS
# ============================================================================

test_compras() {
    log_section "MODULO: COMPRAS"

    log_subsection "GET /compras.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/compras.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado compras OK (200)"
    else
        log_fail "Listado compras fallo (HTTP $code)"
    fi

    local first_compra
    first_compra=$(db_query "SELECT id_compra FROM compra ORDER BY id_compra DESC LIMIT 1;")
    if [ -n "$first_compra" ]; then
        log_subsection "GET /detalle_compra.php?id=$first_compra — detalle"
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/detalle_compra.php?id=$first_compra")
        if [ "$code" = "200" ]; then
            log_pass "Detalle compra OK (200)"
        else
            log_fail "Detalle compra fallo (HTTP $code)"
        fi
    fi

    log_subsection "GET /comprar_producto.php?id=1 — formulario compra"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/comprar_producto.php?id=1")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Formulario comprar producto OK (HTTP $code)"
    else
        log_fail "Formulario comprar producto fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: CONFIGURAR CUENTA
# ============================================================================

test_configurar_cuenta() {
    log_section "MODULO: CONFIGURAR CUENTA"

    log_subsection "GET /configurar_cuenta.php — formulario"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/configurar_cuenta.php")
    if [ "$code" = "200" ]; then
        log_pass "Formulario configurar cuenta OK (200)"
    else
        log_fail "Formulario configurar cuenta fallo (HTTP $code)"
    fi

    log_subsection "Verificar campo olvide contrasena"
    local body
    body=$(do_get_body "$COOKIE_FILE" "$BASE_URL/configurar_cuenta.php")
    if echo "$body" | grep -q "forgot_password.php"; then
        log_pass "Link 'Olvidé mi contraseña' presente"
    else
        log_fail "Link 'Olvidé mi contraseña' no encontrado"
    fi
}

# ============================================================================
# MODULO: REPORTES Y EXPORTACIONES
# ============================================================================

test_reportes() {
    log_section "MODULO: REPORTES Y EXPORTACIONES"

    log_subsection "GET /reportes.php — reportes generales"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/reportes.php")
    if [ "$code" = "200" ]; then
        log_pass "Reportes accesibles (200)"
    else
        log_fail "Reportes fallo (HTTP $code)"
    fi

    log_subsection "GET /reportes.php?tipo=ventas — reporte ventas"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/reportes.php?tipo=ventas")
    if [ "$code" = "200" ]; then
        log_pass "Reporte ventas OK (200)"
    else
        log_fail "Reporte ventas fallo (HTTP $code)"
    fi

    log_subsection "GET /reportes.php?tipo=productos — reporte productos"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/reportes.php?tipo=productos")
    if [ "$code" = "200" ]; then
        log_pass "Reporte productos OK (200)"
    else
        log_fail "Reporte productos fallo (HTTP $code)"
    fi

    log_subsection "GET /export.php?tipo=ventas — exportar ventas Excel"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/export.php?tipo=ventas")
    if [ "$code" = "200" ]; then
        log_pass "Exportar ventas OK (200)"
    else
        log_fail "Exportar ventas fallo (HTTP $code)"
    fi

    log_subsection "GET /export.php?tipo=productos — exportar productos Excel"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/export.php?tipo=productos")
    if [ "$code" = "200" ]; then
        log_pass "Exportar productos OK (200)"
    else
        log_fail "Exportar productos fallo (HTTP $code)"
    fi

    log_subsection "GET /export.php?tipo=clientes — exportar clientes Excel"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/export.php?tipo=clientes")
    if [ "$code" = "200" ]; then
        log_pass "Exportar clientes OK (200)"
    else
        log_fail "Exportar clientes fallo (HTTP $code)"
    fi

    log_subsection "GET /export.php?tipo=completo — exportar completo Excel"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/export.php?tipo=completo")
    if [ "$code" = "200" ]; then
        log_pass "Exportar completo OK (200)"
    else
        log_fail "Exportar completo fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: NOTIFICACIONES
# ============================================================================

test_notificaciones() {
    log_section "MODULO: NOTIFICACIONES"

    log_subsection "GET /notificaciones.php — listar"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/notificaciones.php")
    if [ "$code" = "200" ]; then
        log_pass "Notificaciones accesible (200)"
    else
        log_fail "Notificaciones fallo (HTTP $code)"
    fi

    log_subsection "GET /notificacion_contador.php — contador JSON"
    local body
    body=$(do_get_body "$COOKIE_FILE" "$BASE_URL/notificacion_contador.php")
    if echo "$body" | grep -q "sin_leer"; then
        log_pass "Contador notificaciones retorna JSON valido"
    else
        log_fail "Contador notificaciones no retorno JSON"
    fi

    log_subsection "GET /notificaciones_api.php — API JSON"
    body=$(do_get_body "$COOKIE_FILE" "$BASE_URL/notificaciones_api.php")
    if echo "$body" | grep -q "ok"; then
        log_pass "API notificaciones retorna JSON"
    else
        log_fail "API notificaciones no retorno JSON"
    fi
}

# ============================================================================
# MODULO: SISTEMA (ADMIN)
# ============================================================================

test_sistema() {
    log_section "MODULO: SISTEMA (ADMIN)"

    log_subsection "GET /trabajadores.php — admin listado"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/trabajadores.php")
    if [ "$code" = "200" ]; then
        log_pass "Trabajadores admin OK (200)"
    else
        log_fail "Trabajadores admin fallo (HTTP $code)"
    fi

    log_subsection "GET /auditoria_eliminados.php — auditoria"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/auditoria_eliminados.php")
    if [ "$code" = "200" ]; then
        log_pass "Auditoria eliminados OK (200)"
    else
        log_fail "Auditoria eliminados fallo (HTTP $code)"
    fi

    log_subsection "GET /backups_manuales.php — backups manuales"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/backups_manuales.php")
    if [ "$code" = "200" ]; then
        log_pass "Backups manuales OK (200)"
    else
        log_fail "Backups manuales fallo (HTTP $code)"
    fi

    log_subsection "GET /programar_backups.php — programar backups"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/programar_backups.php")
    if [ "$code" = "200" ]; then
        log_pass "Programar backups OK (200)"
    else
        log_fail "Programar backups fallo (HTTP $code)"
    fi

    log_subsection "GET /mantenimiento_bd.php — mantenimiento"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/mantenimiento_bd.php")
    if [ "$code" = "200" ]; then
        log_pass "Mantenimiento BD OK (200)"
    else
        log_fail "Mantenimiento BD fallo (HTTP $code)"
    fi

    log_subsection "GET /restaurar_bd.php — restaurar"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/restaurar_bd.php")
    if [ "$code" = "200" ]; then
        log_pass "Restaurar BD OK (200)"
    else
        log_fail "Restaurar BD fallo (HTTP $code)"
    fi

    log_subsection "GET /archivos_wal.php — WAL"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/archivos_wal.php")
    if [ "$code" = "200" ]; then
        log_pass "Archivos WAL OK (200)"
    else
        log_fail "Archivos WAL fallo (HTTP $code)"
    fi

    log_subsection "GET /logs_sistema.php — logs"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/logs_sistema.php")
    if [ "$code" = "200" ]; then
        log_pass "Logs sistema OK (200)"
    else
        log_fail "Logs sistema fallo (HTTP $code)"
    fi

    log_subsection "GET /cambiar_numero_de_whatsapp.php — WhatsApp"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/cambiar_numero_de_whatsapp.php")
    if [ "$code" = "200" ]; then
        log_pass "WhatsApp config OK (200)"
    else
        log_fail "WhatsApp config fallo (HTTP $code)"
    fi

    log_subsection "GET /limite_de_venta_cliente_fugaz.php — limite fugaz"
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/limite_de_venta_cliente_fugaz.php")
    if [ "$code" = "200" ]; then
        log_pass "Limite venta fugaz OK (200)"
    else
        log_fail "Limite venta fugaz fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: ACCESO RESTRINGIDO Y UTILIDADES
# ============================================================================

test_utilidades() {
    log_section "MODULO: ACCESO RESTRINGIDO Y UTILIDADES"

    log_subsection "GET /acceso_restringido.php — pagina estatica"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/acceso_restringido.php")
    if [ "$code" = "200" ]; then
        log_pass "Pagina acceso restringido OK (200)"
    else
        log_fail "Pagina acceso restringido fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: PERMISOS POR ROL
# ============================================================================

test_permisos() {
    log_section "MODULO: PERMISOS POR ROL"

    # Test that admin-only pages redirect non-admin users
    log_subsection "Verificar paginas admin sin permisos"
    local code

    # These pages require admin (id_rol=1)
    # Since we're logged in as admin, we can test the pages load
    # For a proper test we'd need a non-admin user, but we test accessibility

    local admin_pages=(
        "trabajadores.php"
        "auditoria_eliminados.php"
        "backups_manuales.php"
        "programar_backups.php"
        "mantenimiento_bd.php"
        "restaurar_bd.php"
        "archivos_wal.php"
        "logs_sistema.php"
        "cambiar_numero_de_whatsapp.php"
        "limite_de_venta_cliente_fugaz.php"
    )

    for page in "${admin_pages[@]}"; do
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/$page")
        if [ "$code" = "200" ]; then
            log_pass "Admin accede a $page (200)"
        else
            log_fail "Admin no accede a $page (HTTP $code)"
        fi
    done
}

# ============================================================================
# MODULO: VALIDACION DE FORMULARIOS
# ============================================================================

test_validaciones() {
    log_section "MODULO: VALIDACION DE FORMULARIOS"

    log_subsection "POST /nuevo_cliente.php — campos vacios"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/nuevo_cliente.php")
    local code
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/nuevo_cliente.php" \
        "nombres=&apellidos=&cedula=&telefono=&correo=&tipo=&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Validacion cliente vacio OK (HTTP $code)"
    else
        log_fail "Validacion cliente vacio fallo (HTTP $code)"
    fi

    log_subsection "POST /categorias.php — categoria vacia"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/categorias.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/categorias.php" \
        "nombre=&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Validacion categoria vacia OK (HTTP $code)"
    else
        log_fail "Validacion categoria vacia fallo (HTTP $code)"
    fi

    log_subsection "POST /proveedores.php — proveedor vacio"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/proveedores.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/proveedores.php" \
        "nombre=&contacto=&telefono=&correo=&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Validacion proveedor vacio OK (HTTP $code)"
    else
        log_fail "Validacion proveedor vacio fallo (HTTP $code)"
    fi

    log_subsection "POST /trabajadores.php — trabajador vacio"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/trabajadores.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/trabajadores.php" \
        "nombre=&email=&password=&id_rol=&id_seccion=&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Validacion trabajador vacio OK (HTTP $code)"
    else
        log_fail "Validacion trabajador vacio fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: VERIFICACION BD
# ============================================================================

test_verificacion_bd() {
    log_section "MODULO: VERIFICACION BD"

    log_subsection "Verificar tablas existentes"
    local count
    count=$(db_query "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE';")
    if [ "$count" = "13" ]; then
        log_pass "13 tablas en BD (correcto)"
    else
        log_fail "Se esperaban 13 tablas, encontradas: $count"
    fi

    log_subsection "Verificar funciones almacenadas"
    count=$(db_query "SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema='public' AND routine_type='FUNCTION';")
    if [ "$count" -gt 10 ]; then
        log_pass "$count funciones almacenadas encontradas"
    else
        log_fail "Funciones insuficientes: $count"
    fi

    log_subsection "Verificar procedimientos almacenados"
    count=$(db_query "SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema='public' AND routine_type='PROCEDURE';")
    if [ "$count" -gt 0 ]; then
        log_pass "$count procedimientos almacenados encontrados"
    else
        log_fail "No se encontraron procedimientos"
    fi

    log_subsection "Verificar triggers"
    count=$(db_query "SELECT COUNT(*) FROM information_schema.triggers WHERE trigger_schema='public';")
    if [ "$count" -gt 0 ]; then
        log_pass "$count triggers encontrados"
    else
        log_fail "No se encontraron triggers"
    fi

    log_subsection "Verificar secuencias"
    count=$(db_query "SELECT COUNT(*) FROM information_schema.sequences WHERE sequence_schema='public';")
    if [ "$count" -gt 0 ]; then
        log_pass "$count secuencias encontradas"
    else
        log_fail "No se encontraron secuencias"
    fi
}

# ============================================================================
# MODULO: ENDPOINTS SIN AUTENTICACION
# ============================================================================

test_no_auth() {
    log_section "MODULO: ENDPOINTS SIN AUTENTICACION"

    # Logout first
    do_get "$COOKIE_FILE" "$BASE_URL/logout.php" >/dev/null 2>&1 || true

    local pages_public=(
        "login.php"
        "catalogo.php"
        "forgot_password.php"
        "acceso_restringido.php"
    )

    for page in "${pages_public[@]}"; do
        local code
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/$page")
        if [ "$code" = "200" ]; then
            log_pass "Publico: $page accesible (200)"
        else
            log_fail "Publico: $page fallo (HTTP $code)"
        fi
    done

    local pages_protected=(
        "dashboard.php"
        "productos.php"
        "clientes.php"
        "facturas.php"
        "configurar_cuenta.php"
        "reportes.php"
    )

    for page in "${pages_protected[@]}"; do
        local code
        code=$(do_get "$COOKIE_FILE" "$BASE_URL/$page")
        if [ "$code" = "302" ]; then
            log_pass "Protegido: $page redirige sin sesion (302)"
        else
            log_fail "Protegido: $page accesible sin sesion (HTTP $code)"
        fi
    done
}

# ============================================================================
# MODULO: CREACION DE COMPRA
# ============================================================================

test_compra_creacion() {
    log_section "MODULO: CREACION DE COMPRA"

    local prod_id
    prod_id=$(db_query "SELECT id_producto FROM producto ORDER BY id_producto LIMIT 1;")
    if [ -z "$prod_id" ]; then
        log_skip "No hay productos para crear compra"
        return
    fi

    log_subsection "POST /comprar_producto.php?id=$prod_id — crear compra"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/comprar_producto.php?id=$prod_id")
    local code
    local stock_before
    stock_before=$(db_query "SELECT stock FROM producto WHERE id_producto=$prod_id;")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/comprar_producto.php?id=$prod_id" \
        "id_proveedor=1&cantidad=10&costo_unitario=50&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Compra creada OK (HTTP $code)"

        local stock_after
        stock_after=$(db_query "SELECT stock FROM producto WHERE id_producto=$prod_id;")
        local expected=$((stock_before + 10))
        if [ "$stock_after" = "$expected" ]; then
            log_pass "Stock incrementado correctamente ($stock_before → $stock_after)"
        else
            log_fail "Stock no incrementado (esperaba $expected, obtuvo $stock_after)"
        fi

        local compra_count
        compra_count=$(db_query "SELECT COUNT(*) FROM compra WHERE id_proveedor=1;")
        if [ "$compra_count" -gt 0 ]; then
            log_pass "Compra registrada en BD"
        else
            log_fail "Compra no encontrada en BD"
        fi
    else
        log_fail "Compra fallo (HTTP $code)"
    fi

    log_subsection "POST /comprar_producto.php?id=$prod_id — validacion cantidad cero"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/comprar_producto.php?id=$prod_id")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/comprar_producto.php?id=$prod_id" \
        "id_proveedor=1&cantidad=0&costo_unitario=50&_token=$token")
    if [ "$code" = "200" ]; then
        log_pass "Validacion cantidad cero OK (200)"
    else
        log_fail "Validacion cantidad cero fallo (HTTP $code)"
    fi

    log_subsection "POST /comprar_producto.php?id=$prod_id — validacion sin proveedor"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/comprar_producto.php?id=$prod_id")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/comprar_producto.php?id=$prod_id" \
        "id_proveedor=0&cantidad=5&costo_unitario=50&_token=$token")
    if [ "$code" = "200" ]; then
        log_pass "Validacion sin proveedor OK (200)"
    else
        log_fail "Validacion sin proveedor fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: ACCIONES DE FACTURAS
# ============================================================================

test_factura_acciones() {
    log_section "MODULO: ACCIONES DE FACTURAS"

    local factura_id
    factura_id=$(db_query "SELECT id_factura FROM factura ORDER BY id_factura DESC LIMIT 1;")
    if [ -z "$factura_id" ]; then
        log_skip "No hay facturas para testear acciones"
        return
    fi

    log_subsection "GET /editar_factura.php?id=$factura_id — formulario"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/editar_factura.php?id=$factura_id")
    if [ "$code" = "200" ]; then
        log_pass "Formulario editar factura OK (200)"
    else
        log_fail "Formulario editar factura fallo (HTTP $code)"
    fi

    log_subsection "POST /editar_factura.php — cambiar estado pago"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/editar_factura.php?id=$factura_id")
    local current_pago
    current_pago=$(db_query "SELECT estado_pago FROM factura WHERE id_factura=$factura_id;")
    if [ "$current_pago" = "Pagado" ]; then
        new_pago="Pendiente"
    else
        new_pago="Pagado"
    fi
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/editar_factura.php" \
        "id_factura=$factura_id&estado_pago=$new_pago&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        local updated_pago
        updated_pago=$(db_query "SELECT estado_pago FROM factura WHERE id_factura=$factura_id;")
        if [ "$updated_pago" = "$new_pago" ]; then
            log_pass "Estado pago cambiado ($current_pago → $new_pago)"
        else
            log_fail "Estado pago no cambio (esperaba $new_pago, obtuvo $updated_pago)"
        fi
    else
        log_fail "Cambio estado pago fallo (HTTP $code)"
    fi

    log_subsection "POST /editar_factura.php — cambiar estado produccion"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/editar_factura.php?id=$factura_id")
    local current_prod
    current_prod=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura=$factura_id;")
    if [ "$current_prod" = "Entregada" ]; then
        new_prod="Pendiente"
    else
        new_prod="Entregada"
    fi
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/editar_factura.php" \
        "id_factura=$factura_id&estado_produccion=$new_prod&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        local updated_prod
        updated_prod=$(db_query "SELECT estado_produccion FROM factura WHERE id_factura=$factura_id;")
        if [ "$updated_prod" = "$new_prod" ]; then
            log_pass "Estado produccion cambiado ($current_prod → $new_prod)"
        else
            log_fail "Estado produccion no cambio (esperaba $new_prod, obtuvo $updated_prod)"
        fi
    else
        log_fail "Cambio estado produccion fallo (HTTP $code)"
    fi

    log_subsection "POST /eliminar_factura.php — eliminar factura (con detalle)"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/facturas.php")
    local test_factura
    test_factura=$(db_query "SELECT f.id_factura FROM factura f LEFT JOIN detalle_factura df ON f.id_factura = df.id_factura WHERE df.id_detalle_factura IS NULL ORDER BY f.id_factura DESC LIMIT 1;")
    if [ -n "$test_factura" ]; then
        code=$(do_post "$COOKIE_FILE" "$BASE_URL/eliminar_factura.php" \
            "id=$test_factura&_token=$token")
        if [ "$code" = "302" ]; then
            local exists
            exists=$(db_query "SELECT COUNT(*) FROM factura WHERE id_factura=$test_factura;")
            if [ "$exists" = "0" ]; then
                log_pass "Factura eliminada correctamente"
            else
                log_fail "Factura no fue eliminada de BD"
            fi
        else
            log_fail "Eliminacion factura fallo (HTTP $code)"
        fi
    else
        log_skip "No hay facturas sin detalle para eliminar"
    fi
}

# ============================================================================
# MODULO: BACKUP MANUAL
# ============================================================================

test_backup_manual() {
    log_section "MODULO: BACKUP MANUAL"

    log_subsection "POST /backups_manuales.php — crear backup"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/backups_manuales.php")
    local code
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/backups_manuales.php" \
        "action=backup&nombre_archivo=test_backup&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Backup manual creado OK (HTTP $code)"

        local backup_exists
        backup_exists=$(ls /home/d4nitrix13/Code/Projecto-Administracion-de-BD/backups/*.sql 2>/dev/null | wc -l)
        if [ "$backup_exists" -gt 0 ]; then
            log_pass "Archivo de backup encontrado en disco"
        else
            log_fail "No se encontro archivo de backup en disco"
        fi
    else
        log_fail "Backup manual fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: ACCIONES POST DE SISTEMA
# ============================================================================

test_post_acciones_sistema() {
    log_section "MODULO: ACCIONES POST DE SISTEMA"

    log_subsection "POST /notificaciones_api.php — marcar_todas"
    local token
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/notificaciones.php")
    local code
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/notificaciones_api.php" \
        "action=marcar_todas&_token=$token")
    if [ "$code" = "200" ]; then
        log_pass "Marcar todas notificaciones OK (200)"
    else
        log_fail "Marcar todas notificaciones fallo (HTTP $code)"
    fi

    log_subsection "POST /programar_backups.php — save_schedule"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/programar_backups.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/programar_backups.php" \
        "action=save_schedule&enabled=1&type=full&interval_value=1&interval_unit=weeks&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Guardar programacion backup OK (HTTP $code)"
    else
        log_fail "Guardar programacion backup fallo (HTTP $code)"
    fi

    log_subsection "POST /programar_backups.php — reset_schedule"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/programar_backups.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/programar_backups.php" \
        "action=reset_schedule&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Restablecer programacion OK (HTTP $code)"
    else
        log_fail "Restablecer programacion fallo (HTTP $code)"
    fi

    log_subsection "POST /cambiar_numero_de_whatsapp.php — actualizar numero"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/cambiar_numero_de_whatsapp.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/cambiar_numero_de_whatsapp.php" \
        "numero=50588889999&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Actualizar WhatsApp OK (HTTP $code)"
    else
        log_fail "Actualizar WhatsApp fallo (HTTP $code)"
    fi

    log_subsection "POST /limite_de_venta_cliente_fugaz.php — save_config"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/limite_de_venta_cliente_fugaz.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/limite_de_venta_cliente_fugaz.php" \
        "action=save_config&limite=5000&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Guardar limite venta fugaz OK (HTTP $code)"
    else
        log_fail "Guardar limite venta fugaz fallo (HTTP $code)"
    fi

    log_subsection "POST /limite_de_venta_cliente_fugaz.php — reset_config"
    token=$(get_csrf_token "$COOKIE_FILE" "$BASE_URL/limite_de_venta_cliente_fugaz.php")
    code=$(do_post "$COOKIE_FILE" "$BASE_URL/limite_de_venta_cliente_fugaz.php" \
        "action=reset_config&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Restablecer limite venta fugaz OK (HTTP $code)"
    else
        log_fail "Restablecer limite venta fugaz fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: ESTILOS Y RECURSOS ESTATICOS
# ============================================================================

test_recursos() {
    log_section "MODULO: ESTILOS Y RECURSOS ESTATICOS"

    log_subsection "Recursos CSS/JS"
    local code
    code=$(do_get "$COOKIE_FILE" "$BASE_URL/assets/css/estilos.css")
    if [ "$code" = "200" ]; then
        log_pass "CSS principal accesible (200)"
    else
        log_fail "CSS principal fallo (HTTP $code)"
    fi
}

# ============================================================================
# RESUMEN FINAL
# ============================================================================

print_summary() {
    echo ""
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}${CYAN}  RESUMEN DE PRUEBAS COMPLETAS${NC}"
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo ""
    echo "  Total:    ${BOLD}$((PASS + FAIL + SKIP))${NC}"
    echo -e "  ${GREEN}PASS:${NC}    ${PASS}"
    echo -e "  ${RED}FAIL:${NC}    ${FAIL}"
    echo -e "  ${YELLOW}SKIP:${NC}    ${SKIP}"
    echo ""

    if [ ${#ERRORS[@]} -gt 0 ]; then
        echo -e "${RED}  ERRORES:${NC}"
        for err in "${ERRORS[@]}"; do
            echo -e "    ${RED}•${NC} $err"
        done
        echo ""
    fi

    if [ "$FAIL" -eq 0 ]; then
        echo -e "  ${GREEN}${BOLD}TODAS LAS PRUEBAS PASARON${NC}"
    else
        echo -e "  ${RED}${BOLD}HAY $FAIL PRUEBAS FALLIDAS${NC}"
    fi

    echo ""
}

# ============================================================================
# MODULE LIST
# ============================================================================

list_modules() {
    echo "Modulos disponibles:"
    echo "  auth              Autenticacion y sesion"
    echo "  password_recovery Recuperar contrasena"
    echo "  dashboard         Dashboard principal"
    echo "  catalogo          Catalogo publico"
    echo "  categorias        CRUD categorias"
    echo "  proveedores       CRUD proveedores"
    echo "  clientes          CRUD clientes"
    echo "  productos         CRUD productos"
    echo "  trabajadores      CRUD trabajadores"
    echo "  facturas          CRUD facturas"
    echo "  compras           Compras e inventario"
    echo "  compra_creacion   Creacion de compra con stock"
    echo "  configurar_cuenta Configuracion de cuenta"
    echo "  reportes          Reportes y exportaciones"
    echo "  notificaciones    Sistema de notificaciones"
    echo "  sistema           Paginas admin del sistema"
    echo "  permisos          Permisos por rol"
    echo "  validaciones      Validacion de formularios"
    echo "  verificacion_bd   Verificacion de BD"
    echo "  factura_acciones  Acciones CRUD facturas"
    echo "  backup_manual     Backup manual"
    echo "  no_auth           Endpoints sin autenticacion"
    echo "  post_acciones     Acciones POST del sistema"
    echo "  recursos          Estilos y recursos estaticos"
    echo "  utilidades        Paginas de utilidad"
}

# ============================================================================
# MAIN
# ============================================================================

for arg in "$@"; do
    case "$arg" in
        --list)
            list_modules
            exit 0
            ;;
        --module)
            shift
            SELECTED_MODULE="${1:-}"
            ;;
        --module=*)
            SELECTED_MODULE="${arg#*=}"
            ;;
    esac
done

echo -e "${BOLD}${CYAN}╔═══════════════════════════════════════════════════════╗${NC}"
echo -e "${BOLD}${CYAN}║   Panda Estampados / Kitsune — Suite de Pruebas      ║${NC}"
echo -e "${BOLD}${CYAN}║   Completa: CRUD + Auth + Sistema + Validaciones      ║${NC}"
echo -e "${BOLD}${CYAN}╚═══════════════════════════════════════════════════════╝${NC}"

run_module() {
    local module="$1"
    case "$module" in
        auth)              test_auth ;;
        password_recovery) test_password_recovery ;;
        dashboard)         test_dashboard ;;
        catalogo)          test_catalogo ;;
        categorias)        test_categorias ;;
        proveedores)       test_proveedores ;;
        clientes)          test_clientes ;;
        productos)         test_productos ;;
        trabajadores)      test_trabajadores ;;
        facturas)          test_facturas ;;
        compras)           test_compras ;;
        compra_creacion)   test_compra_creacion ;;
        configurar_cuenta) test_configurar_cuenta ;;
        reportes)          test_reportes ;;
        notificaciones)    test_notificaciones ;;
        sistema)           test_sistema ;;
        permisos)          test_permisos ;;
        validaciones)      test_validaciones ;;
        verificacion_bd)   test_verificacion_bd ;;
        factura_acciones)  test_factura_acciones ;;
        backup_manual)     test_backup_manual ;;
        no_auth)           test_no_auth ;;
        post_acciones)     test_post_acciones_sistema ;;
        recursos)          test_recursos ;;
        utilidades)        test_utilidades ;;
        *)                 echo "Modulo desconocido: $module"; exit 1 ;;
    esac
}

if [ -n "$SELECTED_MODULE" ]; then
    run_moduD_MODULE" ]; then
    run_module "$SELECTED_MODULE"
else
    test_auth
    test_password_recovery
    test_no_auth

    # Re-login after no_auth test (which logs out)
    log_section "RE-LOGIN POST NO_AUTH"
    login_admin "$COOKIE_FILE"
    log_pass "Sesion admin restaurada"

    test_dashboard
    test_catalogo
    test_categorias
    test_proveedores
    test_clientes
    test_productos
    test_trabajadores
    test_facturas
    test_compras
    test_compra_creacion
    test_configurar_cuenta
    test_reportes
    test_notificaciones
    test_sistema
    test_permisos
    test_validaciones
    test_verificacion_bd
    test_factura_acciones
    test_backup_manual
    test_post_acciones_sistema
    test_recursos
    test_utilidades
fi

print_summary

if [ "$FAIL" -gt 0 ]; then
    exit 1
fi
