#!/usr/bin/env bash

# ============================================================================
# Panda Estampados / Kitsune — Integration CRUD Test Script
# ============================================================================
# Testea ciclos completos de CRUD con verificacion en Base de Datos.
# Cada modulo: CREATE → verificar DB → UPDATE → verificar DB → DELETE → verificar DB
#
# Uso:
#   ./scripts/test_crud_integration.sh                    # Test completo
#   ./scripts/test_crud_integration.sh --module clientes  # Solo un modulo
#   ./scripts/test_crud_integration.sh --list             # Listar modulos
# ============================================================================

set -uo pipefail

BASE_URL="http://localhost:8080"
COOKIE_FILE=$(mktemp /tmp/cookies_int.XXXXXX)
TMP_DIR=$(mktemp -d /tmp/test_int.XXXXXX)
PASS=0
FAIL=0
SKIP=0
ERRORS=()
SELECTED_MODULE=""

DB_HOST="pandas_bd"
DB_USER="postgres"
DB_PASS="root"
DB_NAME="pandas_estampados_y_kitsune"

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

do_get_redirect() {
    local url="$1"
    curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" "$url" 2>/dev/null
}

do_post() {
    local url="$1"
    local data="$2"
    curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "$data" "$url" 2>/dev/null
}

do_post_multipart() {
    local url="$1"
    shift
    curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        "$@" "$url" 2>/dev/null
}

db_query() {
    local sql="$1"
    docker exec -e PGPASSWORD="$DB_PASS" pandas_app psql \
        -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" \
        -t -A -c "$sql" 2>/dev/null
}

db_count() {
    local sql="$1"
    db_query "$sql" | tr -d '[:space:]'
}

# ============================================================================
# LOGIN
# ============================================================================

login() {
    log_section "LOGIN"
    local token
    token=$(get_csrf_token "$BASE_URL/login.php")
    if [ -z "$token" ]; then
        log_fail "No se pudo obtener token CSRF del login"
        return 1
    fi

    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" \
        -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
        -d "email=leonel.messi@admin.pandakitsune.com&password=password0&_token=$token" \
        "$BASE_URL/auth.php" 2>/dev/null)

    if [ "$code" = "302" ]; then
        log_pass "Login exitoso"
    else
        log_fail "Login fallo (HTTP $code)"
        return 1
    fi
}

# ============================================================================
# MODULO: CATEGORIAS
# ============================================================================

test_categorias() {
    log_section "INTEGRATION: CATEGORIAS"

    local ts
    ts=$(date +%s)
    local cat_name="Cat-INT-${ts}"
    local cat_name_upd="Cat-INT-${ts}-UPD"

    # CREATE
    log_subsection "CREATE — Crear categoria"
    local token
    token=$(get_csrf_token "$BASE_URL/categorias.php")
    local count_before
    count_before=$(db_count "SELECT COUNT(*) FROM Categoria WHERE nombre = '${cat_name}';")

    local code
    code=$(do_post "$BASE_URL/categorias.php" "nombre=${cat_name}&_token=${token}")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        local count_after
        count_after=$(db_count "SELECT COUNT(*) FROM Categoria WHERE nombre = '${cat_name}';")
        if [ "$count_after" -gt "$count_before" ]; then
            log_pass "Categoria creada y verificada en DB"
        else
            log_fail "Categoria no aparece en DB (antes=${count_before} despues=${count_after})"
        fi
    else
        log_fail "Crear categoria fallo (HTTP $code)"
    fi

    local cat_id
    cat_id=$(db_count "SELECT id_categoria FROM Categoria WHERE nombre = '${cat_name}' LIMIT 1;")
    if [ -z "$cat_id" ] || [ "$cat_id" = "0" ]; then
        log_fail "No se pudo obtener ID de categoria creada"
        return 1
    fi

    # UPDATE
    log_subsection "UPDATE — Editar categoria"
    token=$(get_csrf_token "$BASE_URL/editar_categoria.php?id=${cat_id}")
    code=$(do_post "$BASE_URL/editar_categoria.php?id=${cat_id}" \
        "id_categoria=${cat_id}&nombre=${cat_name_upd}&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local verify_name
        verify_name=$(db_count "SELECT nombre FROM Categoria WHERE id_categoria = ${cat_id};")
        if [ "$verify_name" = "$cat_name_upd" ]; then
            log_pass "Categoria actualizada y verificada en DB"
        else
            log_fail "Nombre no se actualizo (esperado='${cat_name_upd}' actual='${verify_name}')"
        fi
    else
        log_fail "Editar categoria fallo (HTTP $code)"
    fi

    # DELETE
    log_subsection "DELETE — Eliminar categoria"
    local count_before_del
    count_before_del=$(db_count "SELECT COUNT(*) FROM Categoria WHERE id_categoria = ${cat_id};")
    token=$(get_csrf_token "$BASE_URL/eliminar_categoria.php")
    code=$(do_post "$BASE_URL/eliminar_categoria.php" "id=${cat_id}&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after_del
        count_after_del=$(db_count "SELECT COUNT(*) FROM Categoria WHERE id_categoria = ${cat_id};")
        if [ "$count_after_del" -lt "$count_before_del" ]; then
            log_pass "Categoria eliminada y verificada en DB"
        else
            log_fail "Categoria no se elimino de DB"
        fi
    else
        log_fail "Eliminar categoria fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: PROVEEDORES
# ============================================================================

test_proveedores() {
    log_section "INTEGRATION: PROVEEDORES"

    local ts
    ts=$(date +%s)
    local prov_name="Prov-INT-${ts}"
    local prov_name_upd="Prov-INT-${ts}-UPD"

    # CREATE
    log_subsection "CREATE — Crear proveedor"
    local token
    token=$(get_csrf_token "$BASE_URL/proveedores.php")
    local count_before
    count_before=$(db_count "SELECT COUNT(*) FROM Proveedor WHERE nombre = '${prov_name}';")

    local code
    code=$(do_post "$BASE_URL/proveedores.php" \
        "nombre=${prov_name}&telefono=8888${ts: -4}&email=prov${ts}@test.com&_token=${token}")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        local count_after
        count_after=$(db_count "SELECT COUNT(*) FROM Proveedor WHERE nombre = '${prov_name}';")
        if [ "$count_after" -gt "$count_before" ]; then
            log_pass "Proveedor creado y verificado en DB"
        else
            log_fail "Proveedor no aparece en DB"
        fi
    else
        log_fail "Crear proveedor fallo (HTTP $code)"
    fi

    local prov_id
    prov_id=$(db_count "SELECT id_proveedor FROM Proveedor WHERE nombre = '${prov_name}' LIMIT 1;")
    if [ -z "$prov_id" ] || [ "$prov_id" = "0" ]; then
        log_fail "No se pudo obtener ID de proveedor creado"
        return 1
    fi

    # UPDATE
    log_subsection "UPDATE — Editar proveedor"
    token=$(get_csrf_token "$BASE_URL/editar_proveedor.php?id=${prov_id}")
    code=$(do_post "$BASE_URL/editar_proveedor.php?id=${prov_id}" \
        "id_proveedor=${prov_id}&nombre=${prov_name_upd}&telefono=7777${ts: -4}&email=provupd${ts}@test.com&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local verify_name
        verify_name=$(db_count "SELECT nombre FROM Proveedor WHERE id_proveedor = ${prov_id};")
        if [ "$verify_name" = "$prov_name_upd" ]; then
            log_pass "Proveedor actualizado y verificado en DB"
        else
            log_fail "Nombre no se actualizo (esperado='${prov_name_upd}' actual='${verify_name}')"
        fi
    else
        log_fail "Editar proveedor fallo (HTTP $code)"
    fi

    # DELETE
    log_subsection "DELETE — Eliminar proveedor"
    local count_before_del
    count_before_del=$(db_count "SELECT COUNT(*) FROM Proveedor WHERE id_proveedor = ${prov_id};")
    token=$(get_csrf_token "$BASE_URL/eliminar_proveedor.php")
    code=$(do_post "$BASE_URL/eliminar_proveedor.php" "id=${prov_id}&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after_del
        count_after_del=$(db_count "SELECT COUNT(*) FROM Proveedor WHERE id_proveedor = ${prov_id};")
        if [ "$count_after_del" -lt "$count_before_del" ]; then
            log_pass "Proveedor eliminado y verificado en DB"
        else
            log_fail "Proveedor no se elimino de DB"
        fi
    else
        log_fail "Eliminar proveedor fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: CLIENTES
# ============================================================================

test_clientes() {
    log_section "INTEGRATION: CLIENTES"

    local ts
    ts=$(date +%s)
    local cli_name="Cliente-INT-${ts}"
    cli_name="${cli_name:0:50}"
    local cli_name_upd="Cliente-INT-${ts}-UPD"
    cli_name_upd="${cli_name_upd:0:50}"

    # CREATE
    log_subsection "CREATE — Crear cliente"
    local token
    token=$(get_csrf_token "$BASE_URL/nuevo_cliente.php")
    local count_before
    count_before=$(db_count "SELECT COUNT(*) FROM Cliente WHERE nombres = '${cli_name}';")

    local code
    code=$(do_post "$BASE_URL/nuevo_cliente.php" \
        "nombres=${cli_name}&apellidos=TestApe&telefono=8888${ts: -4}&tipo_cliente=Detallista&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after
        count_after=$(db_count "SELECT COUNT(*) FROM Cliente WHERE nombres = '${cli_name}';")
        if [ "$count_after" -gt "$count_before" ]; then
            log_pass "Cliente creado y verificado en DB"
        else
            log_fail "Cliente no aparece en DB"
        fi
    else
        log_fail "Crear cliente fallo (HTTP $code)"
    fi

    local cli_id
    cli_id=$(db_count "SELECT id_cliente FROM Cliente WHERE nombres = '${cli_name}' LIMIT 1;")
    if [ -z "$cli_id" ] || [ "$cli_id" = "0" ]; then
        log_fail "No se pudo obtener ID de cliente creado"
        return 1
    fi

    # UPDATE
    log_subsection "UPDATE — Editar cliente"
    token=$(get_csrf_token "$BASE_URL/editar_cliente.php?id=${cli_id}")
    code=$(do_post "$BASE_URL/editar_cliente.php?id=${cli_id}" \
        "id_cliente=${cli_id}&nombres=${cli_name_upd}&apellidos=TestApeUpd&telefono=7777${ts: -4}&tipo_cliente=Detallista&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local verify_name
        verify_name=$(db_count "SELECT nombres FROM Cliente WHERE id_cliente = ${cli_id};")
        if [ "$verify_name" = "$cli_name_upd" ]; then
            log_pass "Cliente actualizado y verificado en DB"
        else
            log_fail "Nombre no se actualizo (esperado='${cli_name_upd}' actual='${verify_name}')"
        fi
    else
        log_fail "Editar cliente fallo (HTTP $code)"
    fi

    # DELETE
    log_subsection "DELETE — Eliminar cliente"
    local count_before_del
    count_before_del=$(db_count "SELECT COUNT(*) FROM Cliente WHERE id_cliente = ${cli_id};")
    token=$(get_csrf_token "$BASE_URL/eliminar_cliente.php")
    code=$(do_post "$BASE_URL/eliminar_cliente.php" "id=${cli_id}&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after_del
        count_after_del=$(db_count "SELECT COUNT(*) FROM Cliente WHERE id_cliente = ${cli_id};")
        if [ "$count_after_del" -lt "$count_before_del" ]; then
            log_pass "Cliente eliminado y verificado en DB"
        else
            log_fail "Cliente no se elimino de DB"
        fi
    else
        log_fail "Eliminar cliente fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: TRABAJADORES
# ============================================================================

test_trabajadores() {
    log_section "INTEGRATION: TRABAJADORES"

    local ts
    ts=$(date +%s)
    local worker_name="Worker-INT-${ts}"
    local worker_email="worker${ts}@test.com"

    # CREATE
    log_subsection "CREATE — Crear trabajador"
    local token
    token=$(get_csrf_token "$BASE_URL/trabajadores.php")
    local count_before
    count_before=$(db_count "SELECT COUNT(*) FROM Usuario WHERE email = '${worker_email}';")

    local code
    code=$(do_post "$BASE_URL/trabajadores.php" \
        "nombre=${worker_name}&email=${worker_email}&password=testpass123&id_rol=3&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after
        count_after=$(db_count "SELECT COUNT(*) FROM Usuario WHERE email = '${worker_email}';")
        if [ "$count_after" -gt "$count_before" ]; then
            log_pass "Trabajador creado y verificado en DB"
        else
            log_fail "Trabajador no aparece en DB"
        fi
    else
        log_fail "Crear trabajador fallo (HTTP $code)"
    fi

    local worker_id
    worker_id=$(db_count "SELECT id_usuario FROM Usuario WHERE email = '${worker_email}' LIMIT 1;")
    if [ -z "$worker_id" ] || [ "$worker_id" = "0" ]; then
        log_fail "No se pudo obtener ID de trabajador creado"
        return 1
    fi

    # UPDATE
    log_subsection "UPDATE — Editar trabajador"
    local worker_name_upd="Worker-INT-${ts}-UPD"
    token=$(get_csrf_token "$BASE_URL/editar_trabajador.php?id=${worker_id}")
    code=$(do_post "$BASE_URL/editar_trabajador.php?id=${worker_id}" \
        "id_usuario=${worker_id}&nombre=${worker_name_upd}&email=${worker_email}&id_rol=3&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local verify_name
        verify_name=$(db_count "SELECT nombre FROM Usuario WHERE id_usuario = ${worker_id};")
        if [ "$verify_name" = "$worker_name_upd" ]; then
            log_pass "Trabajador actualizado y verificado en DB"
        else
            log_fail "Nombre no se actualizo (esperado='${worker_name_upd}' actual='${verify_name}')"
        fi
    else
        log_fail "Editar trabajador fallo (HTTP $code)"
    fi

    # DELETE
    log_subsection "DELETE — Eliminar trabajador"
    local count_before_del
    count_before_del=$(db_count "SELECT COUNT(*) FROM Usuario WHERE id_usuario = ${worker_id};")
    token=$(get_csrf_token "$BASE_URL/eliminar_usuario.php")
    code=$(do_post "$BASE_URL/eliminar_usuario.php" "id=${worker_id}&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after_del
        count_after_del=$(db_count "SELECT COUNT(*) FROM Usuario WHERE id_usuario = ${worker_id};")
        if [ "$count_after_del" -lt "$count_before_del" ]; then
            log_pass "Trabajador eliminado y verificado en DB"
        else
            log_fail "Trabajador no se elimino de DB"
        fi
    else
        log_fail "Eliminar trabajador fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: PRODUCTOS
# ============================================================================

test_productos() {
    log_section "INTEGRATION: PRODUCTOS"

    local ts
    ts=$(date +%s)
    local prod_code="PROD-INT-${ts: -6}"
    local prod_name="Producto-INT-${ts}"
    local test_image="${TMP_DIR}/test_product.png"

    # Create a tiny valid PNG for upload
    printf '\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x02\x00\x00\x00\x90wS\xde\x00\x00\x00\x0cIDATx\x9cc\xf8\x0f\x00\x00\x01\x01\x00\x05\x18\xd8N\x00\x00\x00\x00IEND\xaeB`\x82' > "$test_image"

    # CREATE
    log_subsection "CREATE — Crear producto"
    local token
    token=$(get_csrf_token "$BASE_URL/nuevo_producto.php")
    local count_before
    count_before=$(db_count "SELECT COUNT(*) FROM Producto WHERE codigo = '${prod_code}';")

    local code
    code=$(do_post_multipart "$BASE_URL/nuevo_producto.php" \
        -F "codigo=${prod_code}" \
        -F "nombre=${prod_name}" \
        -F "descripcion=Producto integration test" \
        -F "id_categoria=" \
        -F "id_proveedor=" \
        -F "precio_compra=50.00" \
        -F "precio_venta=100.00" \
        -F "stock=20" \
        -F "imagen=@${test_image}" \
        -F "_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after
        count_after=$(db_count "SELECT COUNT(*) FROM Producto WHERE codigo = '${prod_code}';")
        if [ "$count_after" -gt "$count_before" ]; then
            log_pass "Producto creado y verificado en DB"
        else
            log_fail "Producto no aparece en DB"
        fi
    else
        log_fail "Crear producto fallo (HTTP $code)"
    fi

    local prod_id
    prod_id=$(db_count "SELECT id_producto FROM Producto WHERE codigo = '${prod_code}' LIMIT 1;")
    if [ -z "$prod_id" ] || [ "$prod_id" = "0" ]; then
        log_fail "No se pudo obtener ID de producto creado"
        return 1
    fi

    # Verify stock
    local stock_after_create
    stock_after_create=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")
    if [ "$stock_after_create" = "20" ]; then
        log_pass "Stock inicial correcto (20)"
    else
        log_fail "Stock inicial incorrecto (esperado=20 actual=${stock_after_create})"
    fi

    # UPDATE
    log_subsection "UPDATE — Editar producto"
    local prod_name_upd="Producto-INT-${ts}-UPD"
    token=$(get_csrf_token "$BASE_URL/editar_producto.php?id=${prod_id}")
    code=$(do_post_multipart "$BASE_URL/editar_producto.php?id=${prod_id}" \
        -F "id_producto=${prod_id}" \
        -F "codigo=${prod_code}" \
        -F "nombre=${prod_name_upd}" \
        -F "descripcion=Updated integration test" \
        -F "id_categoria=" \
        -F "id_proveedor=" \
        -F "precio_compra=60.00" \
        -F "precio_venta=120.00" \
        -F "stock=30" \
        -F "_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local verify_name
        verify_name=$(db_count "SELECT nombre FROM Producto WHERE id_producto = ${prod_id};")
        local verify_stock
        verify_stock=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")
        if [ "$verify_name" = "$prod_name_upd" ] && [ "$verify_stock" = "30" ]; then
            log_pass "Producto actualizado (nombre + stock=30) verificado en DB"
        else
            log_fail "Producto no se actualizo correctamente (nombre='${verify_name}' stock='${verify_stock}')"
        fi
    else
        log_fail "Editar producto fallo (HTTP $code)"
    fi

    # DELETE
    log_subsection "DELETE — Eliminar producto"
    local count_before_del
    count_before_del=$(db_count "SELECT COUNT(*) FROM Producto WHERE id_producto = ${prod_id};")
    token=$(get_csrf_token "$BASE_URL/eliminar_producto.php")
    code=$(do_post "$BASE_URL/eliminar_producto.php" "id=${prod_id}&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after_del
        count_after_del=$(db_count "SELECT COUNT(*) FROM Producto WHERE id_producto = ${prod_id};")
        if [ "$count_after_del" -lt "$count_before_del" ]; then
            log_pass "Producto eliminado y verificado en DB"
        else
            log_fail "Producto no se elimino de DB"
        fi
    else
        log_fail "Eliminar producto fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: FACTURAS (con verificacion de stock)
# ============================================================================

test_facturas() {
    log_section "INTEGRATION: FACTURAS"

    local ts
    ts=$(date +%s)

    # Get a product with enough stock for create(2) + edit(3)
    local prod_id
    prod_id=$(db_count "SELECT id_producto FROM Producto WHERE stock >= 10 ORDER BY id_producto LIMIT 1;")
    if [ -z "$prod_id" ] || [ "$prod_id" = "0" ]; then
        # If no product has enough stock, pick the first one and adjust quantities
        prod_id=$(db_count "SELECT id_producto FROM Producto ORDER BY id_producto LIMIT 1;")
        CREATE_QTY=1
        EDIT_QTY=1
    else
        CREATE_QTY=2
        EDIT_QTY=3
    fi
    if [ -z "$prod_id" ] || [ "$prod_id" = "0" ]; then
        log_skip "No hay productos para crear factura"
        return 1
    fi

    local cli_id
    cli_id=$(db_count "SELECT id_cliente FROM Cliente ORDER BY id_cliente LIMIT 1;")
    if [ -z "$cli_id" ] || [ "$cli_id" = "0" ]; then
        log_skip "No hay clientes para crear factura"
        return 1
    fi

    local stock_before
    stock_before=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")

    # CREATE
    log_subsection "CREATE — Crear factura"
    local token
    token=$(get_csrf_token "$BASE_URL/nueva_factura.php")

    local total_estimate
    total_estimate=$(db_count "SELECT ROUND(precio_venta * ${CREATE_QTY} * 1.15, 2) FROM Producto WHERE id_producto = ${prod_id};")
    local monto_pago
    monto_pago=$(echo "$total_estimate" | cut -d. -f1)

    local code
    code=$(do_post "$BASE_URL/nueva_factura.php" \
        "id_cliente=${cli_id}&id_seccion=1&tipo_cliente_venta=Habitual&descuento_global=0&monto_pagado=${total_estimate}&fecha_entrega_estimada=2026-12-31&id_producto[]=${prod_id}&cantidad[]=${CREATE_QTY}&descuento_linea[]=0&_token=${token}")
    if [ "$code" = "302" ]; then
        log_pass "Factura creacion exitosa (302 redirect)"
    else
        log_fail "Crear factura fallo (HTTP $code — se esperaba 302)"
        return 1
    fi

    local fact_id
    fact_id=$(db_count "SELECT id_factura FROM Factura ORDER BY id_factura DESC LIMIT 1;")
    if [ -z "$fact_id" ] || [ "$fact_id" = "0" ]; then
        log_fail "No se pudo obtener ID de factura creada"
        return 1
    fi
    log_pass "Factura creada con ID: ${fact_id}"

    # Verify stock decreased
    local stock_after_create
    stock_after_create=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")
    local expected_stock=$((stock_before - CREATE_QTY))
    if [ "$stock_after_create" = "$expected_stock" ]; then
        log_pass "Stock post-creacion correcto (${stock_before} → ${stock_after_create})"
    else
        log_fail "Stock post-creacion incorrecto (esperado=${expected_stock} actual=${stock_after_create})"
    fi

    # Verify detail lines exist
    local detail_count
    detail_count=$(db_count "SELECT COUNT(*) FROM DetalleFactura WHERE id_factura = ${fact_id};")
    if [ "$detail_count" -gt "0" ]; then
        log_pass "Detalle de factura creado (${detail_count} lineas)"
    else
        log_fail "Detalle de factura vacio"
    fi

    # Verify monto_pagado was set
    local monto_pagado
    monto_pagado=$(db_count "SELECT monto_pagado FROM Factura WHERE id_factura = ${fact_id};")
    if [ -n "$monto_pagado" ] && [ "$monto_pagado" != "0" ] && [ "$monto_pagado" != "" ]; then
        log_pass "Monto pagado registrado (${monto_pagado})"
    else
        log_fail "Monto pagado no se registro (${monto_pagado})"
    fi

    # READ
    log_subsection "READ — Ver detalle de factura"
    code=$(do_get "$BASE_URL/detalle_factura.php?id=${fact_id}")
    if [ "$code" = "200" ]; then
        log_pass "Detalle de factura accesible (200)"
    else
        log_fail "Detalle de factura fallo (HTTP $code)"
    fi

    # UPDATE (edit factura)
    log_subsection "UPDATE — Editar factura (cambiar cantidad)"
    token=$(get_csrf_token "$BASE_URL/editar_factura.php?id=${fact_id}")
    local stock_before_edit
    stock_before_edit=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")

    local edit_total
    edit_total=$(db_count "SELECT ROUND(precio_venta * ${EDIT_QTY} * 1.15, 2) FROM Producto WHERE id_producto = ${prod_id};")

    code=$(do_post "$BASE_URL/editar_factura.php?id=${fact_id}" \
        "id_factura=${fact_id}&fecha=2026-06-15T10:00&id_usuario=1&id_seccion=1&descuento_global=0&tipo_cliente_venta=Habitual&id_cliente=${cli_id}&monto_pagado=${edit_total}&fecha_entrega_estimada=2026-12-31&id_producto[]=${prod_id}&cantidad[]=${EDIT_QTY}&descuento_linea[]=0&_token=${token}")
    if [ "$code" = "200" ] || [ "$code" = "302" ]; then
        # Stock should be: stock_before_edit (which already had -CREATE_QTY from create) restored, then -EDIT_QTY for new qty
        local stock_after_edit
        stock_after_edit=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")
        local expected_after_edit=$((stock_before_edit + CREATE_QTY - EDIT_QTY))
        if [ "$stock_after_edit" = "$expected_after_edit" ]; then
            log_pass "Stock post-edicion correcto (restaurado +${CREATE_QTY}, descontado -${EDIT_QTY} = ${stock_after_edit})"
        else
            log_fail "Stock post-edicion incorrecto (esperado=${expected_after_edit} actual=${stock_after_edit})"
        fi

        # Verify detail was updated
        local detail_total
        detail_total=$(db_count "SELECT cantidad FROM DetalleFactura WHERE id_factura = ${fact_id} AND id_producto = ${prod_id};")
        if [ "$detail_total" = "${EDIT_QTY}" ]; then
            log_pass "Detalle actualizado correctamente (cantidad=${EDIT_QTY})"
        else
            log_fail "Detalle no se actualizo (cantidad=${detail_total})"
        fi
    else
        log_fail "Editar factura fallo (HTTP $code)"
    fi

    # DELETE (with POST + CSRF)
    log_subsection "DELETE — Eliminar factura (POST + CSRF)"
    local count_before_del
    count_before_del=$(db_count "SELECT COUNT(*) FROM Factura WHERE id_factura = ${fact_id};")
    local stock_before_del
    stock_before_del=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")

    token=$(get_csrf_token "$BASE_URL/facturas.php")
    code=$(do_post "$BASE_URL/eliminar_factura.php" "id=${fact_id}&_token=${token}")
    if [ "$code" = "302" ] || [ "$code" = "200" ]; then
        local count_after_del
        count_after_del=$(db_count "SELECT COUNT(*) FROM Factura WHERE id_factura = ${fact_id};")
        if [ "$count_after_del" -lt "$count_before_del" ]; then
            log_pass "Factura eliminada de DB"
        else
            log_fail "Factura no se elimino de DB"
        fi

        # Verify stock restored
        local stock_after_del
        stock_after_del=$(db_count "SELECT stock FROM Producto WHERE id_producto = ${prod_id};")
        local expected_after_del=$((stock_before_del + EDIT_QTY))
        if [ "$stock_after_del" = "$expected_after_del" ]; then
            log_pass "Stock restaurado post-eliminacion (${stock_before_del} → ${stock_after_del})"
        else
            log_fail "Stock no se_restore correctamente (esperado=${expected_after_del} actual=${stock_after_del})"
        fi

        # Verify detail lines deleted
        local detail_after_del
        detail_after_del=$(db_count "SELECT COUNT(*) FROM DetalleFactura WHERE id_factura = ${fact_id};")
        if [ "$detail_after_del" = "0" ]; then
            log_pass "Detalles de factura eliminados"
        else
            log_fail "Detalles de factura NO eliminados (${detail_after_del} restantes)"
        fi
    else
        log_fail "Eliminar factura fallo (HTTP $code)"
    fi
}

# ============================================================================
# MODULO: EXPORT
# ============================================================================

test_export() {
    log_section "INTEGRATION: EXPORT"

    local code

    log_subsection "EXPORT — Ventas a Excel"
    code=$(do_get "$BASE_URL/export.php?tipo=ventas")
    if [ "$code" = "200" ]; then
        log_pass "Exportar ventas OK (200)"
    else
        log_fail "Exportar ventas fallo (HTTP $code)"
    fi

    log_subsection "EXPORT — Productos a Excel"
    code=$(do_get "$BASE_URL/export.php?tipo=productos")
    if [ "$code" = "200" ]; then
        log_pass "Exportar productos OK (200)"
    else
        log_fail "Exportar productos fallo (HTTP $code)"
    fi

    log_subsection "EXPORT — Clientes a Excel"
    code=$(do_get "$BASE_URL/export.php?tipo=clientes")
    if [ "$code" = "200" ]; then
        log_pass "Exportar clientes OK (200)"
    else
        log_fail "Exportar clientes fallo (HTTP $code)"
    fi

    log_subsection "EXPORT — Completo a Excel"
    code=$(do_get "$BASE_URL/export.php?tipo=completo")
    if [ "$code" = "200" ]; then
        log_pass "Exportar completo OK (200)"
    else
        log_fail "Exportar completo fallo (HTTP $code)"
    fi
}

# ============================================================================
# SUMMARY
# ============================================================================

print_summary() {
    echo ""
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}${CYAN}  RESUMEN DE PRUEBAS DE INTEGRACION${NC}"
    echo -e "${BOLD}${CYAN}═══════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "  Total:    ${BOLD}$((PASS + FAIL + SKIP))${NC}"
    echo -e "  ${GREEN}PASS:${NC}    ${PASS}"
    echo -e "  ${RED}FAIL:${NC}    ${FAIL}"
    echo -e "  ${YELLOW}SKIP:${NC}    ${SKIP}"
    echo ""

    if [ $FAIL -gt 0 ]; then
        echo -e "${BOLD}${RED}  FALLAS:${NC}"
        for err in "${ERRORS[@]}"; do
            echo -e "    ${RED}•${NC} $err"
        done
        echo ""
    fi

    if [ $FAIL -eq 0 ]; then
        echo -e "  ${GREEN}${BOLD}TODAS LAS PRUEBAS DE INTEGRACION PASARON${NC}"
    else
        echo -e "  ${RED}${BOLD}HAY PRUEBAS FALLIDAS${NC}"
    fi
}

# ============================================================================
# MAIN
# ============================================================================

usage() {
    echo "Uso: $0 [--module <modulo>] [--list]"
    echo ""
    echo "Modulos disponibles:"
    echo "  categorias    CRUD categorias"
    echo "  proveedores   CRUD proveedores"
    echo "  clientes      CRUD clientes"
    echo "  trabajadores  CRUD trabajadores"
    echo "  productos     CRUD productos (con imagen)"
    echo "  facturas      CRUD facturas (con verificacion stock)"
    echo "  export        Exportacion Excel"
    exit 0
}

main() {
    while [ $# -gt 0 ]; do
        case "$1" in
            --module)
                SELECTED_MODULE="${2:-}"
                shift 2
                ;;
            --list)
                usage
                ;;
            *)
                echo "Uso: $0 [--module <modulo>] [--list]"
                exit 1
                ;;
        esac
    done

    login

    case "$SELECTED_MODULE" in
        categorias)
            test_categorias
            ;;
        proveedores)
            test_proveedores
            ;;
        clientes)
            test_clientes
            ;;
        trabajadores)
            test_trabajadores
            ;;
        productos)
            test_productos
            ;;
        facturas)
            test_facturas
            ;;
        export)
            test_export
            ;;
        "")
            test_categorias
            test_proveedores
            test_clientes
            test_trabajadores
            test_productos
            test_facturas
            test_export
            ;;
        *)
            echo "Modulo desconocido: $SELECTED_MODULE"
            echo "Usa --list para ver modulos disponibles"
            exit 1
            ;;
    esac

    print_summary

    if [ $FAIL -gt 0 ]; then
        exit 1
    fi
}

main "$@"
