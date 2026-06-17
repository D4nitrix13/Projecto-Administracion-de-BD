#!/usr/bin/env bash

# ============================================================================
# Panda Estampados / Kitsune — CRUD Test Script
# ============================================================================
# Testea todas las operaciones CRUD de la aplicacion via HTTP.
# Requiere: curl, docker (contenedor pandas_app corriendo), php (dentro del container)
#
# Uso:
#   ./scripts/test_crud.sh                    # Test completo
#   ./scripts/test_crud.sh --module clientes  # Solo un modulo
#   ./scripts/test_crud.sh --list             # Listar modulos disponibles
# ============================================================================

set -euo pipefail

BASE_URL="http://localhost:8080"
COOKIE_FILE=$(mktemp /tmp/cookies.XXXXXX)
TMP_DIR=$(mktemp -d /tmp/test_crud.XXXXXX)
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
    curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" "$url" 2>/dev/null \
        | grep -o 'name="_token" value="[^"]*"' \
        | sed 's/.*value="\([^"]*\)".*/\1/' \
        | head -1
}

# Wrapper para peticiones GET
do_get() {
    local url="$1"
    local expect_code="${2:-200}"
    curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" "$url" 2>/dev/null
}

# Wrapper para peticiones POST (sin archivos)
do_post() {
    local url="$1"
    local data="$2"
    local expect_code="${3:-302}"
    local extra_args=("${4:-}")

    local args=(-s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" -c "$COOKIE_FILE")
    args+=(-d "$data")

    curl "${args[@]}" "$url" 2>/dev/null
}

# Wrapper para POST con multipart (archivos)
do_post_multipart() {
    local url="$1"
    shift
    curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" -c "$COOKIE_FILE" "$@" "$url" 2>/dev/null
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

    log_subsection "Verificando acceso al dashboard"
    local dash_code
    dash_code=$(do_get "$BASE_URL/dashboard.php" 200)
    if [ "$dash_code" = "200" ]; then
        log_pass "Dashboard accesible (200)"
    else
        log_fail "Dashboard no accesible (HTTP $dash_code)"
    fi
}

# ============================================================================
# MODULO: CATEGORIAS
# ============================================================================

test_categorias() {
    log_section "MODULO: CATEGORIAS"

    # ── READ (list) ──
    log_subsection "READ — Listar categorias"
    local code
    code=$(do_get "$BASE_URL/categorias.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado de categorias OK (200)"
    else
        log_fail "Listado de categorias fallo (HTTP $code)"
    fi

    # ── READ (search) ──
    log_subsection "READ — Buscar categorias"
    code=$(do_get "$BASE_URL/categorias.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda de categorias OK (200)"
    else
        log_fail "Busqueda de categorias fallo (HTTP $code)"
    fi

    # ── CREATE ──
    log_subsection "CREATE — Registrar categoria"
    local token
    token=$(get_csrf_token "$BASE_URL/categorias.php")
    code=$(do_post "$BASE_URL/categorias.php" "nombre=Test+CRUD+Categoria&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Creacion de categoria OK (HTTP $code)"
    else
        log_fail "Creacion de categoria fallo (HTTP $code)"
    fi

    # ── Verify CREATE via DB ──
    log_subsection "CREATE — Verificar en BD"
    local cat_count
    cat_count=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT COUNT(*) FROM buscar_categorias('Test CRUD Categoria');" 2>/dev/null | tr -d ' ')
    if [ "$cat_count" -ge 1 ] 2>/dev/null; then
        log_pass "Categoria encontrada en BD (count: $cat_count)"
    else
        log_fail "Categoria no encontrada en BD"
    fi

    # ── UPDATE (via DB direct since no web form for category edit with CSRF) ──
    log_subsection "UPDATE — Actualizar categoria (DB direct)"
    local cat_id
    cat_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT id_categoria FROM buscar_categorias('Test CRUD Categoria') LIMIT 1;" 2>/dev/null | tr -d ' ')

    if [ -n "$cat_id" ] && [ "$cat_id" != "" ]; then
        code=$(do_get "$BASE_URL/editar_categoria.php?id=$cat_id")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Formulario editar categoria OK (HTTP $code)"
        else
            log_fail "Formulario editar categoria fallo (HTTP $code)"
        fi

        # POST update
        local token2
        token2=$(get_csrf_token "$BASE_URL/editar_categoria.php?id=$cat_id")
        code=$(do_post "$BASE_URL/editar_categoria.php?id=$cat_id" "nombre=Test+CRUD+Categoria+UPDATED&_token=$token2")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Update de categoria OK (HTTP $code)"
        else
            log_fail "Update de categoria fallo (HTTP $code)"
        fi
    else
        log_skip "No se pudo obtener ID de categoria para update"
    fi

    # ── DELETE ──
    log_subsection "DELETE — Eliminar categoria"
    if [ -n "${cat_id:-}" ] && [ "$cat_id" != "" ]; then
        code=$(do_get "$BASE_URL/eliminar_categoria.php?id=$cat_id")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Eliminacion de categoria OK (HTTP $code)"
        else
            log_fail "Eliminacion de categoria fallo (HTTP $code)"
        fi
    else
        log_skip "No hay categoria para eliminar"
    fi
}

# ============================================================================
# MODULO: PROVEEDORES
# ============================================================================

test_proveedores() {
    log_section "MODULO: PROVEEDORES"

    # ── READ (list) ──
    log_subsection "READ — Listar proveedores"
    local code
    code=$(do_get "$BASE_URL/proveedores.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado de proveedores OK (200)"
    else
        log_fail "Listado de proveedores fallo (HTTP $code)"
    fi

    # ── READ (search) ──
    log_subsection "READ — Buscar proveedores"
    code=$(do_get "$BASE_URL/proveedores.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda de proveedores OK (200)"
    else
        log_fail "Busqueda de proveedores fallo (HTTP $code)"
    fi

    # ── CREATE ──
    log_subsection "CREATE — Registrar proveedor"
    local token
    token=$(get_csrf_token "$BASE_URL/proveedores.php")
    code=$(do_post "$BASE_URL/proveedores.php" "nombre=Proveedor+CRUD+Test&telefono=8888-0000&email=proveedor.crud@test.com&direccion=Managua&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Creacion de proveedor OK (HTTP $code)"
    else
        log_fail "Creacion de proveedor fallo (HTTP $code)"
    fi

    # ── Verify CREATE ──
    log_subsection "CREATE — Verificar en BD"
    local prov_count
    prov_count=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT COUNT(*) FROM buscar_proveedores_filtrados('Proveedor CRUD Test');" 2>/dev/null | tr -d ' ')
    if [ "$prov_count" -ge 1 ] 2>/dev/null; then
        log_pass "Proveedor encontrado en BD (count: $prov_count)"
    else
        log_fail "Proveedor no encontrado en BD"
    fi

    # ── UPDATE ──
    log_subsection "UPDATE — Editar proveedor"
    local prov_id
    prov_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT id_proveedor FROM buscar_proveedores_filtrados('Proveedor CRUD Test') LIMIT 1;" 2>/dev/null | tr -d ' ')

    if [ -n "${prov_id:-}" ] && [ "$prov_id" != "" ]; then
        code=$(do_get "$BASE_URL/editar_proveedor.php?id=$prov_id")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Formulario editar proveedor OK (HTTP $code)"
        else
            log_fail "Formulario editar proveedor fallo (HTTP $code)"
        fi

        local token2
        token2=$(get_csrf_token "$BASE_URL/editar_proveedor.php?id=$prov_id")
        code=$(do_post "$BASE_URL/editar_proveedor.php?id=$prov_id" "nombre=Proveedor+CRUD+UPDATED&telefono=7777-0000&email=prov.updated@test.com&_token=$token2")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Update de proveedor OK (HTTP $code)"
        else
            log_fail "Update de proveedor fallo (HTTP $code)"
        fi
    else
        log_skip "No se pudo obtener ID de proveedor para update"
    fi

    # ── DELETE ──
    log_subsection "DELETE — Eliminar proveedor"
    if [ -n "${prov_id:-}" ] && [ "$prov_id" != "" ]; then
        code=$(do_get "$BASE_URL/eliminar_proveedor.php?id=$prov_id")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Eliminacion de proveedor OK (HTTP $code)"
        else
            log_fail "Eliminacion de proveedor fallo (HTTP $code)"
        fi
    else
        log_skip "No hay proveedor para eliminar"
    fi
}

# ============================================================================
# MODULO: CLIENTES
# ============================================================================

test_clientes() {
    log_section "MODULO: CLIENTES"

    # ── READ (list) ──
    log_subsection "READ — Listar clientes"
    local code
    code=$(do_get "$BASE_URL/clientes.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado de clientes OK (200)"
    else
        log_fail "Listado de clientes fallo (HTTP $code)"
    fi

    # ── READ (search) ──
    log_subsection "READ — Buscar clientes"
    code=$(do_get "$BASE_URL/clientes.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda de clientes OK (200)"
    else
        log_fail "Busqueda de clientes fallo (HTTP $code)"
    fi

    code=$(do_get "$BASE_URL/clientes.php?tipo=Mayorista")
    if [ "$code" = "200" ]; then
        log_pass "Filtro por tipo Mayorista OK (200)"
    else
        log_fail "Filtro por tipo Mayorista fallo (HTTP $code)"
    fi

    # ── CREATE ──
    log_subsection "CREATE — Registrar cliente"
    local token
    token=$(get_csrf_token "$BASE_URL/nuevo_cliente.php")
    code=$(do_post "$BASE_URL/nuevo_cliente.php" "nombres=Cliente+CRUD&apellidos=Test+User&telefono=8888-1111&direccion=Managua&identificacion=001-010101-0001X&tipo_cliente=Detallista&_token=$token")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        log_pass "Creacion de cliente OK (HTTP $code)"
    else
        log_fail "Creacion de cliente fallo (HTTP $code)"
    fi

    # ── Verify CREATE ──
    log_subsection "CREATE — Verificar en BD"
    local cli_count
    cli_count=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT COUNT(*) FROM buscar_clientes_filtrados('Cliente CRUD', NULL);" 2>/dev/null | tr -d ' ')
    if [ "$cli_count" -ge 1 ] 2>/dev/null; then
        log_pass "Cliente encontrado en BD (count: $cli_count)"
    else
        log_fail "Cliente no encontrado en BD"
    fi

    # ── DETAIL ──
    log_subsection "READ — Detalle cliente"
    local cli_id
    cli_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT id_cliente FROM buscar_clientes_filtrados('Cliente CRUD', NULL) LIMIT 1;" 2>/dev/null | tr -d ' ')

    if [ -n "${cli_id:-}" ] && [ "$cli_id" != "" ]; then
        code=$(do_get "$BASE_URL/detalle_cliente.php?id=$cli_id")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Detalle de cliente OK (HTTP $code)"
        else
            log_fail "Detalle de cliente fallo (HTTP $code)"
        fi

        # ── UPDATE ──
        log_subsection "UPDATE — Editar cliente"
        code=$(do_get "$BASE_URL/editar_cliente.php?id=$cli_id")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Formulario editar cliente OK (HTTP $code)"
        else
            log_fail "Formulario editar cliente fallo (HTTP $code)"
        fi

        local token2
        token2=$(get_csrf_token "$BASE_URL/editar_cliente.php?id=$cli_id")
        code=$(do_post "$BASE_URL/editar_cliente.php?id=$cli_id" "nombres=Cliente+CRUD+UPDATED&apellidos=Test+Updated&telefono=7777-2222&direccion=Managua+Updated&identificacion=001-010101-0001X&tipo_cliente=Detallista&_token=$token2")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Update de cliente OK (HTTP $code)"
        else
            log_fail "Update de cliente fallo (HTTP $code)"
        fi
    else
        log_skip "No se pudo obtener ID de cliente para tests de detalle/update"
    fi

    # ── DELETE ──
    log_subsection "DELETE — Eliminar cliente"
    if [ -n "${cli_id:-}" ] && [ "$cli_id" != "" ]; then
        code=$(do_get "$BASE_URL/eliminar_cliente.php?id=$cli_id")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Eliminacion de cliente OK (HTTP $code)"
        else
            log_fail "Eliminacion de cliente fallo (HTTP $code)"
        fi
    else
        log_skip "No hay cliente para eliminar"
    fi
}

# ============================================================================
# MODULO: PRODUCTOS
# ============================================================================

test_productos() {
    log_section "MODULO: PRODUCTOS"

    # Create a tiny 1x1 PNG for file upload tests
    local test_image="$TMP_DIR/test_product.png"
    printf '\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x02\x00\x00\x00\x90wS\xde\x00\x00\x00\x0cIDATx\x9cc\xf8\x0f\x00\x00\x01\x01\x00\x05\x18\xd8N\x00\x00\x00\x00IEND\xaeB`\x82' > "$test_image"

    # ── READ (list) ──
    log_subsection "READ — Listar productos"
    local code
    code=$(do_get "$BASE_URL/productos.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado de productos OK (200)"
    else
        log_fail "Listado de productos fallo (HTTP $code)"
    fi

    # ── READ (search) ──
    log_subsection "READ — Buscar productos"
    code=$(do_get "$BASE_URL/productos.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda de productos OK (200)"
    else
        log_fail "Busqueda de productos fallo (HTTP $code)"
    fi

    code=$(do_get "$BASE_URL/productos.php?stock=bajo")
    if [ "$code" = "200" ]; then
        log_pass "Filtro stock bajo OK (200)"
    else
        log_fail "Filtro stock bajo fallo (HTTP $code)"
    fi

    # ── READ (form) ──
    log_subsection "READ — Formulario nuevo producto"
    code=$(do_get "$BASE_URL/nuevo_producto.php")
    if [ "$code" = "200" ]; then
        log_pass "Formulario nuevo producto OK (200)"
    else
        log_fail "Formulario nuevo producto fallo (HTTP $code)"
    fi

    # ── CREATE (with image) ──
    log_subsection "CREATE — Registrar producto con imagen"
    local token
    token=$(get_csrf_token "$BASE_URL/nuevo_producto.php")
    code=$(do_post_multipart "$BASE_URL/nuevo_producto.php" \
        -F "codigo=TEST-001" \
        -F "nombre=Producto CRUD Test" \
        -F "descripcion=Producto para testing CRUD" \
        -F "id_categoria=" \
        -F "id_proveedor=" \
        -F "precio_compra=50.00" \
        -F "precio_venta=100.00" \
        -F "stock=10" \
        -F "imagen=@$test_image" \
        -F "_token=$token")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        log_pass "Creacion de producto OK (HTTP $code)"
    else
        log_fail "Creacion de producto fallo (HTTP $code)"
    fi

    # ── Verify CREATE ──
    log_subsection "CREATE — Verificar en BD"
    local prod_count
    prod_count=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT COUNT(*) FROM buscar_productos_inventario('Producto CRUD Test', NULL, NULL, NULL, FALSE);" 2>/dev/null | tr -d ' ')
    if [ "$prod_count" -ge 1 ] 2>/dev/null; then
        log_pass "Producto encontrado en BD (count: $prod_count)"
    else
        log_fail "Producto no encontrado en BD"
    fi

    # ── UPDATE ──
    log_subsection "UPDATE — Editar producto"
    local prod_id
    prod_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT id_producto FROM buscar_productos_inventario('Producto CRUD', NULL, NULL, NULL, FALSE) LIMIT 1;" 2>/dev/null | tr -d ' ')

    if [ -n "${prod_id:-}" ] && [ "$prod_id" != "" ]; then
        code=$(do_get "$BASE_URL/editar_producto.php?id=$prod_id")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Formulario editar producto OK (HTTP $code)"
        else
            log_fail "Formulario editar producto fallo (HTTP $code)"
        fi

        local token2
        token2=$(get_csrf_token "$BASE_URL/editar_producto.php?id=$prod_id")
        code=$(do_post_multipart "$BASE_URL/editar_producto.php" \
            -F "id_producto=$prod_id" \
            -F "codigo=TEST-001" \
            -F "nombre=Producto CRUD UPDATED" \
            -F "descripcion=Producto actualizado" \
            -F "id_categoria=" \
            -F "id_proveedor=" \
            -F "precio_compra=60.00" \
            -F "precio_venta=120.00" \
            -F "stock=15" \
            -F "_token=$token2")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Update de producto OK (HTTP $code)"
        else
            log_fail "Update de producto fallo (HTTP $code)"
        fi
    else
        log_skip "No se pudo obtener ID de producto para update"
    fi

    # ── DELETE ──
    log_subsection "DELETE — Eliminar producto"
    if [ -n "${prod_id:-}" ] && [ "$prod_id" != "" ]; then
        code=$(do_get "$BASE_URL/eliminar_producto.php?id=$prod_id")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Eliminacion de producto OK (HTTP $code)"
        else
            log_fail "Eliminacion de producto fallo (HTTP $code)"
        fi
    else
        log_skip "No hay producto para eliminar"
    fi

    # ── CATALOG ──
    log_subsection "READ — Catalogo publico"
    code=$(do_get "$BASE_URL/catalogo.php")
    if [ "$code" = "200" ]; then
        log_pass "Catalogo publico OK (200)"
    else
        log_fail "Catalogo publico fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: TRABAJADORES (USUARIOS)
# ============================================================================

test_trabajadores() {
    log_section "MODULO: TRABAJADORES (USUARIOS)"

    # ── READ (list) ──
    log_subsection "READ — Listar trabajadores"
    local code
    code=$(do_get "$BASE_URL/trabajadores.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado de trabajadores OK (200)"
    else
        log_fail "Listado de trabajadores fallo (HTTP $code)"
    fi

    # ── READ (search) ──
    log_subsection "READ — Buscar trabajadores"
    code=$(do_get "$BASE_URL/trabajadores.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda de trabajadores OK (200)"
    else
        log_fail "Busqueda de trabajadores fallo (HTTP $code)"
    fi

    code=$(do_get "$BASE_URL/trabajadores.php?rol=3")
    if [ "$code" = "200" ]; then
        log_pass "Filtro por rol OK (200)"
    else
        log_fail "Filtro por rol fallo (HTTP $code)"
    fi

    # ── CREATE ──
    log_subsection "CREATE — Registrar trabajador"
    local token
    token=$(get_csrf_token "$BASE_URL/trabajadores.php")
    code=$(do_post "$BASE_URL/trabajadores.php" "nombre=Trabajador+CRUD+Test&email=trabajador.crud@test.com&password=testpass123&id_rol=3&_token=$token")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        log_pass "Creacion de trabajador OK (HTTP $code)"
    else
        log_fail "Creacion de trabajador fallo (HTTP $code)"
    fi

    # ── Verify CREATE ──
    log_subsection "CREATE — Verificar en BD"
    local user_count
    user_count=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT COUNT(*) FROM buscar_usuarios_filtrados('Trabajador CRUD Test', NULL, NULL);" 2>/dev/null | tr -d ' ')
    if [ "$user_count" -ge 1 ] 2>/dev/null; then
        log_pass "Trabajador encontrado en BD (count: $user_count)"
    else
        log_fail "Trabajador no encontrado en BD"
    fi

    # ── UPDATE ──
    log_subsection "UPDATE — Editar trabajador"
    local user_id
    user_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -t -c \
        "SELECT id_usuario FROM buscar_usuarios_filtrados('Trabajador CRUD Test', NULL, NULL) LIMIT 1;" 2>/dev/null | tr -d ' ')

    if [ -n "${user_id:-}" ] && [ "$user_id" != "" ]; then
        code=$(do_get "$BASE_URL/editar_trabajador.php?id=$user_id")
        if [ "$code" = "200" ] || [ "$code" = "302" ]; then
            log_pass "Formulario editar trabajador OK (HTTP $code)"
        else
            log_fail "Formulario editar trabajador fallo (HTTP $code)"
        fi

        local token2
        token2=$(get_csrf_token "$BASE_URL/editar_trabajador.php?id=$user_id")
        code=$(do_post "$BASE_URL/editar_trabajador.php?id=$user_id" "nombre=Trabajador+CRUD+UPDATED&email=trabajador.crud.updated@test.com&id_rol=3&_token=$token2")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Update de trabajador OK (HTTP $code)"
        else
            log_fail "Update de trabajador fallo (HTTP $code)"
        fi
    else
        log_skip "No se pudo obtener ID de trabajador para update"
    fi

    # ── DELETE ──
    log_subsection "DELETE — Eliminar trabajador"
    if [ -n "${user_id:-}" ] && [ "$user_id" != "" ]; then
        code=$(do_get "$BASE_URL/eliminar_usuario.php?id=$user_id")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Eliminacion de trabajador OK (HTTP $code)"
        else
            log_fail "Eliminacion de trabajador fallo (HTTP $code)"
        fi
    else
        log_skip "No hay trabajador para eliminar"
    fi
}

# ============================================================================
# MODULO: FACTURAS
# ============================================================================

test_facturas() {
    log_section "MODULO: FACTURAS"

    # ── READ (list) ──
    log_subsection "READ — Listar facturas"
    local code
    code=$(do_get "$BASE_URL/facturas.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado de facturas OK (200)"
    else
        log_fail "Listado de facturas fallo (HTTP $code)"
    fi

    # ── READ (search) ──
    log_subsection "READ — Buscar facturas"
    code=$(do_get "$BASE_URL/facturas.php?q=1")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda de facturas OK (200)"
    else
        log_fail "Busqueda de facturas fallo (HTTP $code)"
    fi

    code=$(do_get "$BASE_URL/facturas.php?estado_pago=Pagado")
    if [ "$code" = "200" ]; then
        log_pass "Filtro estado de pago OK (200)"
    else
        log_fail "Filtro estado de pago fallo (HTTP $code)"
    fi

    code=$(do_get "$BASE_URL/facturas.php?estado_produccion=Entregada")
    if [ "$code" = "200" ]; then
        log_pass "Filtro estado de produccion OK (200)"
    else
        log_fail "Filtro estado de produccion fallo (HTTP $code)"
    fi

    # ── CREATE (form) ──
    log_subsection "READ — Formulario nueva factura"
    code=$(do_get "$BASE_URL/nueva_factura.php")
    if [ "$code" = "200" ]; then
        log_pass "Formulario nueva factura OK (200)"
    else
        log_fail "Formulario nueva factura fallo (HTTP $code)"
    fi

    # ── CREATE (with valid data) ──
    log_subsection "CREATE — Registrar factura"

    # Find an existing product with stock and an existing client
    local product_data
    product_data=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -A -t -c \
        "SELECT id_producto, stock FROM producto WHERE stock > 0 LIMIT 1;" 2>/dev/null)
    local prod_id_for_factura=$(echo "$product_data" | cut -d'|' -f1)
    local prod_stock=$(echo "$product_data" | cut -d'|' -f2)

    local client_id
    client_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -A -t -c \
        "SELECT id_cliente FROM cliente LIMIT 1;" 2>/dev/null | head -1)

    local section_id
    section_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -A -t -c \
        "SELECT id_seccion FROM seccion LIMIT 1;" 2>/dev/null | head -1)

    if [ -n "${prod_id_for_factura:-}" ] && [ -n "${client_id:-}" ] && [ "$prod_id_for_factura" != "" ] && [ "$client_id" != "" ]; then
        local qty=1
        local price="100.00"
        local monto_pagado="50.00"
        local fecha_entrega
        fecha_entrega=$(date -d "+7 days" +%Y-%m-%d 2>/dev/null || date -v+7d +%Y-%m-%d 2>/dev/null || echo "2026-12-31")

        local token
        token=$(get_csrf_token "$BASE_URL/nueva_factura.php")
        code=$(do_post "$BASE_URL/nueva_factura.php" \
            "id_cliente=$client_id&id_seccion=$section_id&tipo_cliente_venta=Habitual&descuento_global=0&monto_pagado=$monto_pagado&fecha_entrega_estimada=$fecha_entrega&id_producto[]=$prod_id_for_factura&cantidad[]=$qty&descuento_linea[]=0&_token=$token")

        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Creacion de factura OK (HTTP $code)"
        else
            log_fail "Creacion de factura fallo (HTTP $code)"
        fi
    else
        log_skip "No hay datos suficientes para crear factura (productos: ${prod_id_for_factura:-none}, clientes: ${client_id:-none})"
    fi

    # ── DETAIL ──
    log_subsection "READ — Detalle factura"
    local fact_id
    fact_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -A -t -c \
        "SELECT id_factura FROM factura ORDER BY id_factura DESC LIMIT 1;" 2>/dev/null | head -1)

    if [ -n "${fact_id:-}" ] && [ "$fact_id" != "" ]; then
        code=$(do_get "$BASE_URL/detalle_factura.php?id=$fact_id")
        if [ "$code" = "200" ]; then
            log_pass "Detalle de factura OK (200)"
        else
            log_fail "Detalle de factura fallo (HTTP $code)"
        fi

        # ── PRINT ──
        log_subsection "READ — Imprimir factura"
        code=$(do_get "$BASE_URL/imprimir_factura.php?id=$fact_id")
        if [ "$code" = "200" ]; then
            log_pass "Vista impresion factura OK (200)"
        else
            log_fail "Vista impresion factura fallo (HTTP $code)"
        fi
    else
        log_skip "No hay facturas para test de detalle"
    fi

    # ── HISTORY ──
    log_subsection "READ — Historial de estados"
    code=$(do_get "$BASE_URL/historial_estados_facturas.php")
    if [ "$code" = "200" ]; then
        log_pass "Historial de estados OK (200)"
    else
        log_fail "Historial de estados fallo (HTTP $code)"
    fi

    # ── DELETE ──
    log_subsection "DELETE — Eliminar factura"
    if [ -n "${fact_id:-}" ] && [ "$fact_id" != "" ]; then
        code=$(do_get "$BASE_URL/eliminar_factura.php?id=$fact_id")
        if [ "$code" = "302" ] || [ "$code" = "200" ]; then
            log_pass "Eliminacion de factura OK (HTTP $code)"
        else
            log_fail "Eliminacion de factura fallo (HTTP $code)"
        fi
    else
        log_skip "No hay factura para eliminar"
    fi
}

# ============================================================================
# MODULO: DASHBOARD
# ============================================================================

test_dashboard() {
    log_section "MODULO: DASHBOARD"

    local code
    log_subsection "READ — Dashboard principal"
    code=$(do_get "$BASE_URL/dashboard.php")
    if [ "$code" = "200" ]; then
        log_pass "Dashboard OK (200)"
    else
        log_fail "Dashboard fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: REPORTES
# ============================================================================

test_reportes() {
    log_section "MODULO: REPORTES"

    local code

    log_subsection "READ — Reporte de ventas"
    code=$(do_get "$BASE_URL/reportes.php?tipo=ventas")
    if [ "$code" = "200" ]; then
        log_pass "Reporte de ventas OK (200)"
    else
        log_fail "Reporte de ventas fallo (HTTP $code)"
    fi

    log_subsection "READ — Reporte de productos"
    code=$(do_get "$BASE_URL/reportes.php?tipo=productos")
    if [ "$code" = "200" ]; then
        log_pass "Reporte de productos OK (200)"
    else
        log_fail "Reporte de productos fallo (HTTP $code)"
    fi

    log_subsection "READ — Reporte de clientes"
    code=$(do_get "$BASE_URL/reportes.php?tipo=clientes")
    if [ "$code" = "200" ]; then
        log_pass "Reporte de clientes OK (200)"
    else
        log_fail "Reporte de clientes fallo (HTTP $code)"
    fi

    log_subsection "READ — Reporte con rango de fechas"
    code=$(do_get "$BASE_URL/reportes.php?tipo=ventas&desde=2025-01-01&hasta=2026-12-31")
    if [ "$code" = "200" ]; then
        log_pass "Reporte con fechas OK (200)"
    else
        log_fail "Reporte con fechas fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: COMPRA (solo lectura — no hay UI de creacion)
# ============================================================================

test_compras() {
    log_section "MODULO: COMPRA (solo lectura)"

    local code

    log_subsection "READ — Listar compras"
    code=$(do_get "$BASE_URL/compras.php")
    if [ "$code" = "200" ]; then
        log_pass "Listado de compras OK (200)"
    else
        log_fail "Listado de compras fallo (HTTP $code)"
    fi

    log_subsection "READ — Buscar compras"
    code=$(do_get "$BASE_URL/compras.php?q=1")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda de compras OK (200)"
    else
        log_fail "Busqueda de compras fallo (HTTP $code)"
    fi

    # ── DETAIL ──
    log_subsection "READ — Detalle compra"
    local comp_id
    comp_id=$(docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -A -t -c \
        "SELECT id_compra FROM compra ORDER BY id_compra DESC LIMIT 1;" 2>/dev/null | head -1)

    if [ -n "${comp_id:-}" ] && [ "$comp_id" != "" ]; then
        code=$(do_get "$BASE_URL/detalle_compra.php?id=$comp_id")
        if [ "$code" = "200" ]; then
            log_pass "Detalle de compra OK (200)"
        else
            log_fail "Detalle de compra fallo (HTTP $code)"
        fi
    else
        log_skip "No hay compras en la BD"
    fi
}

# ============================================================================
# MODULO: CUENTA / CONFIGURACION
# ============================================================================

test_cuenta() {
    log_section "MODULO: CUENTA / CONFIGURACION"

    local code

    log_subsection "READ — Formulario configurar cuenta"
    code=$(do_get "$BASE_URL/configurar_cuenta.php")
    if [ "$code" = "200" ]; then
        log_pass "Formulario configurar cuenta OK (200)"
    else
        log_fail "Formulario configurar cuenta fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: AUDITORIA
# ============================================================================

test_auditoria() {
    log_section "MODULO: AUDITORIA"

    local code

    log_subsection "READ — Registros eliminados"
    code=$(do_get "$BASE_URL/auditoria_eliminados.php")
    if [ "$code" = "200" ]; then
        log_pass "Auditoria de eliminados OK (200)"
    else
        log_fail "Auditoria de eliminados fallo (HTTP $code)"
    fi

    log_subsection "READ — Buscar en auditoria"
    code=$(do_get "$BASE_URL/auditoria_eliminados.php?q=test")
    if [ "$code" = "200" ]; then
        log_pass "Busqueda en auditoria OK (200)"
    else
        log_fail "Busqueda en auditoria fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: STORED FUNCTIONS (verificacion directa via BD)
# ============================================================================

test_stored_functions() {
    log_section "STORED FUNCTIONS — Verificacion de integridad"

    local psql="docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -A -t -c"

    log_subsection "Verificar que todas las funciones existen"
    local funciones=(
        "registrar_cliente_sistema"
        "buscar_clientes_filtrados"
        "obtener_cliente_por_id"
        "actualizar_cliente_sistema"
        "eliminar_cliente_sistema"
        "registrar_producto_formulario"
        "buscar_productos_inventario"
        "obtener_producto_edicion_por_id"
        "actualizar_producto_edicion"
        "crear_proveedor_sistema"
        "buscar_proveedores_filtrados"
        "obtener_proveedor_por_id"
        "actualizar_proveedor_sistema"
        "eliminar_proveedor_sistema"
        "registrar_categoria"
        "buscar_categorias"
        "obtener_categoria_por_id"
        "actualizar_categoria"
        "eliminar_categoria_sistema"
        "crear_usuario_sistema"
        "buscar_usuarios_filtrados"
        "obtener_usuario_edicion_por_id"
        "actualizar_usuario_sistema"
        "eliminar_usuario_sistema"
        "registrar_factura_sistema"
        "buscar_facturas_filtradas"
        "obtener_factura_detalle_por_id"
        "eliminar_factura_sistema"
        "obtener_metricas_dashboard"
        "obtener_ventas_dashboard"
        "obtener_usuario_login"
    )

    local all_ok=true
    for func in "${funciones[@]}"; do
        local exists
        exists=$($psql "SELECT 1 FROM pg_proc WHERE proname = '$func';" 2>/dev/null)
        if [ "$exists" = "1" ]; then
            log_pass "Funcion '$func' existe"
        else
            log_fail "Funcion '$func' NO existe"
            all_ok=false
        fi
    done

    log_subsection "Verificar que todos los triggers existen"
    local triggers=(
        "trg_auditar_delete_categoria"
        "trg_auditar_delete_cliente"
        "trg_auditar_delete_producto"
        "trg_auditar_delete_proveedor"
        "trg_factura_estado_historial"
    )

    for trig in "${triggers[@]}"; do
        local exists
        exists=$($psql "SELECT 1 FROM pg_trigger WHERE tgname = '$trig';" 2>/dev/null)
        if [ "$exists" = "1" ]; then
            log_pass "Trigger '$trig' existe"
        else
            log_fail "Trigger '$trig' NO existe"
        fi
    done
}

# ============================================================================
# MODULO: STORED PROCEDURES (verificacion via DB)
# ============================================================================

test_stored_procedures() {
    log_section "STORED PROCEDURES — Verificacion"
    local psql="docker exec -e PGPASSWORD=root pandas_app psql -h pandas_bd -U postgres -d pandas_estampados_y_kitsune -A -t -c"

    local procedures=(
        "registrar_producto"
        "registrar_cliente"
        "registrar_auditoria"
        "agregar_detalle_compra"
        "agregar_detalle_factura"
        "aumentar_stock_producto"
        "disminuir_stock_producto"
        "editar_factura_sistema"
        "actualizar_totales_factura"
        "actualizar_total_compra"
    )

    for proc in "${procedures[@]}"; do
        local exists
        exists=$($psql "SELECT 1 FROM pg_proc WHERE proname = '$proc';" 2>/dev/null)
        if [ "$exists" = "1" ]; then
            log_pass "Procedimiento '$proc' existe"
        else
            log_fail "Procedimiento '$proc' NO existe"
        fi
    done
}

# ============================================================================
# MODULO: COMPORTAMIENTO DE ERRORES (validacion de formularios)
# ============================================================================

test_validation() {
    log_section "VALIDACION — Pruebas de error handling"

    local code

    # ── Login con credenciales incorrectas ──
    log_subsection "Login con password incorrecta"
    local token
    token=$(get_csrf_token "$BASE_URL/login.php")
    code=$(curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "email=leonel.messi@admin.pandakitsune.com&password=wrongpassword&_token=$token" \
        "$BASE_URL/auth.php" 2>/dev/null)
    if [ "$code" = "302" ]; then
        log_pass "Login con password incorrecta redirige a login (302)"
    else
        log_fail "Login con password incorrecta: HTTP $code (esperaba 302)"
    fi

    # ── Crear cliente sin campos obligatorios ──
    log_subsection "Crear cliente sin nombres (validacion)"
    token=$(get_csrf_token "$BASE_URL/nuevo_cliente.php")
    code=$(do_post "$BASE_URL/nuevo_cliente.php" "nombres=&apellidos=&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Crear cliente vacio retorna HTTP $code (no 500)"
    else
        log_fail "Crear cliente vacio fallo con HTTP $code"
    fi

    # ── Crear producto sin campos obligatorios ──
    log_subsection "Crear producto sin codigo (validacion)"
    token=$(get_csrf_token "$BASE_URL/nuevo_producto.php")
    code=$(do_post "$BASE_URL/nuevo_producto.php" "codigo=&nombre=&precio_compra=&precio_venta=&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Crear producto vacio retorna HTTP $code (no 500)"
    else
        log_fail "Crear producto vacio fallo con HTTP $code"
    fi

    # ── Crear trabajador sin campos ──
    log_subsection "Crear trabajador sin email (validacion)"
    token=$(get_csrf_token "$BASE_URL/trabajadores.php")
    code=$(do_post "$BASE_URL/trabajadores.php" "nombre=&email=&password=&id_rol=0&_token=$token")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Crear trabajador vacio retorna HTTP $code (no 500)"
    else
        log_fail "Crear trabajador vacio fallo con HTTP $code"
    fi

    # ── Acceso sin login ──
    log_subsection "Acceso a pagina protegida sin login"
    local temp_cookies
    temp_cookies=$(mktemp /tmp/cookies_nosess.XXXXXX)
    code=$(curl -s -o /dev/null -w "%{http_code}" -b "$temp_cookies" "$BASE_URL/dashboard.php" 2>/dev/null)
    rm -f "$temp_cookies"
    if [ "$code" = "302" ]; then
        log_pass "Dashboard sin login redirige a login (302)"
    else
        log_fail "Dashboard sin login: HTTP $code (esperaba 302)"
    fi
}

# ============================================================================
# MODULO: ARCHIVOS ESTÁTICOS Y ASSETS
# ============================================================================

test_assets() {
    log_section "ASSETS — Verificacion de archivos estaticos"

    local code

    log_subsection "Favicon"
    code=$(do_get "$BASE_URL/favicon.ico")
    if [ "$code" = "200" ] || [ "$code" = "304" ]; then
        log_pass "Favicon accesible (HTTP $code)"
    else
        log_fail "Favicon no accesible (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: WAL / LOGS / BACKUPS (solo accesibilidad HTTP)
# ============================================================================

test_system_pages() {
    log_section "PAGINAS DE SISTEMA — Accesibilidad HTTP"

    local code

    log_subsection "Archivos WAL"
    code=$(do_get "$BASE_URL/archivos_wal.php")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Pagina archivos WAL accesible (HTTP $code)"
    else
        log_fail "Pagina archivos WAL inaccesible (HTTP $code)"
    fi

    log_subsection "Logs del sistema"
    code=$(do_get "$BASE_URL/logs_sistema.php")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Pagina logs sistema accesible (HTTP $code)"
    else
        log_fail "Pagina logs sistema inaccesible (HTTP $code)"
    fi

    log_subsection "Mantenimiento BD"
    code=$(do_get "$BASE_URL/mantenimiento_bd.php")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Pagina mantenimiento accesible (HTTP $code)"
    else
        log_fail "Pagina mantenimiento inaccesible (HTTP $code)"
    fi

    log_subsection "Backups manuales"
    code=$(do_get "$BASE_URL/backups_manuales.php")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Pagina backups manuales accesible (HTTP $code)"
    else
        log_fail "Pagina backups manuales inaccesible (HTTP $code)"
    fi

    log_subsection "Programar backups"
    code=$(do_get "$BASE_URL/programar_backups.php")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Pagina programar backups accesible (HTTP $code)"
    else
        log_fail "Pagina programar backups inaccesible (HTTP $code)"
    fi

    log_subsection "Restaurar BD"
    code=$(do_get "$BASE_URL/restaurar_bd.php")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Pagina restaurar BD accesible (HTTP $code)"
    else
        log_fail "Pagina restaurar BD inaccesible (HTTP $code)"
    fi

    log_subsection "Limite venta fugaz"
    code=$(do_get "$BASE_URL/limite_de_venta_cliente_fugaz.php")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        log_pass "Pagina limite venta fugaz accesible (HTTP $code)"
    else
        log_fail "Pagina limite venta fugaz inaccesible (HTTP $code)"
    fi
}

# ============================================================================
# RESUMEN FINAL
# ============================================================================

print_summary() {
    local total=$((PASS + FAIL + SKIP))

    echo ""
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}  RESUMEN DE PRUEBAS CRUD${NC}"
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "  Total:    ${BOLD}$total${NC}"
    echo -e "  ${GREEN}PASS:${NC}    $PASS"
    echo -e "  ${RED}FAIL:${NC}    $FAIL"
    echo -e "  ${YELLOW}SKIP:${NC}    $SKIP"
    echo ""

    if [ $FAIL -gt 0 ]; then
        echo -e "${RED}${BOLD}  ERRORES ENCONTRADOS:${NC}"
        for err in "${ERRORS[@]}"; do
            echo -e "    ${RED}• $err${NC}"
        done
        echo ""
    fi

    if [ $FAIL -eq 0 ]; then
        echo -e "  ${GREEN}${BOLD}TODAS LAS PRUEBAS PASARON${NC}"
    else
        echo -e "  ${RED}${BOLD}HAY FALLOS — revisar arriba${NC}"
    fi
    echo ""
}

# ============================================================================
# MAIN
# ============================================================================

main() {
    local module="${1:-}"

    echo ""
    echo -e "${BOLD}${CYAN}╔═══════════════════════════════════════════════════════╗${NC}"
    echo -e "${BOLD}${CYAN}║   Panda Estampados / Kitsune — CRUD Test Suite        ║${NC}"
    echo -e "${BOLD}${CYAN}╚═══════════════════════════════════════════════════════╝${NC}"

    # Check Docker
    if ! docker ps | grep -q pandas_app; then
        echo -e "${RED}Error: Contenedor pandas_app no esta corriendo.${NC}"
        echo "Ejecuta: docker compose -f docker/docker-compose.yml up -d"
        exit 1
    fi

    login || { print_summary; exit 1; }

    case "$module" in
        --module)
            local mod="${2:-}"
            case "$mod" in
                categorias)     test_categorias ;;
                proveedores)    test_proveedores ;;
                clientes)       test_clientes ;;
                productos)      test_productos ;;
                trabajadores)   test_trabajadores ;;
                facturas)       test_facturas ;;
                dashboard)      test_dashboard ;;
                reportes)       test_reportes ;;
                compras)        test_compras ;;
                cuenta)         test_cuenta ;;
                auditoria)      test_auditoria ;;
                stored)         test_stored_functions; test_stored_procedures ;;
                validation)     test_validation ;;
                assets)         test_assets ;;
                system)         test_system_pages ;;
                *)
                    echo "Modulo no reconocido: $mod"
                    echo "Uso: $0 --module <modulo>"
                    echo "Modulos: categorias proveedores clientes productos trabajadores facturas"
                    echo "         dashboard reportes compras cuenta auditoria stored validation assets system"
                    exit 1
                    ;;
            esac
            ;;
        --list)
            echo "Modulos disponibles:"
            echo "  categorias     CRUD categorias"
            echo "  proveedores    CRUD proveedores"
            echo "  clientes       CRUD clientes"
            echo "  productos      CRUD productos + catalogo"
            echo "  trabajadores   CRUD usuarios/trabajadores"
            echo "  facturas       CRUD facturas + historial + impresion"
            echo "  dashboard      Dashboard principal"
            echo "  reportes       Reportes (ventas, productos, clientes)"
            echo "  compras        Solo lectura (sin UI de creacion)"
            echo "  cuenta         Configuracion de cuenta"
            echo "  auditoria      Auditoria de eliminados"
            echo "  stored         Verificacion de stored functions y procedures"
            echo "  validation     Pruebas de validacion de formularios"
            echo "  assets         Archivos estaticos"
            echo "  system         Paginas de sistema (WAL, logs, backups)"
            exit 0
            ;;
        "")
            test_categorias
            test_proveedores
            test_clientes
            test_productos
            test_trabajadores
            test_facturas
            test_dashboard
            test_reportes
            test_compras
            test_cuenta
            test_auditoria
            test_stored_functions
            test_stored_procedures
            test_validation
            test_assets
            test_system_pages
            ;;
        *)
            echo "Uso: $0 [--module <modulo>] [--list]"
            exit 1
            ;;
    esac

    print_summary

    if [ $FAIL -gt 0 ]; then
        exit 1
    fi
}

main "$@"
