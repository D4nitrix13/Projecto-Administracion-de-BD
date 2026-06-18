#!/bin/bash

# ============================================================
# Test: Recuperación y cambio de contraseña
# Panda Estampados / Kitsune
# ============================================================

set -euo pipefail

BASE_URL="http://localhost:8080"
COOKIE_JAR="/tmp/test_reset_cookies.txt"
PASS=0
FAIL=0
TOTAL=0

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

assert_eq() {
    local desc="$1" expected="$2" actual="$3"
    TOTAL=$((TOTAL + 1))
    if [ "$expected" = "$actual" ]; then
        echo -e "  ${GREEN}✓${NC} $desc"
        PASS=$((PASS + 1))
    else
        echo -e "  ${RED}✗${NC} $desc (expected: $expected, got: $actual)"
        FAIL=$((FAIL + 1))
    fi
}

assert_contains() {
    local desc="$1" needle="$2" haystack="$3"
    TOTAL=$((TOTAL + 1))
    if echo "$haystack" | grep -q "$needle"; then
        echo -e "  ${GREEN}✓${NC} $desc"
        PASS=$((PASS + 1))
    else
        echo -e "  ${RED}✗${NC} $desc (does not contain: $needle)"
        FAIL=$((FAIL + 1))
    fi
}

assert_not_contains() {
    local desc="$1" needle="$2" haystack="$3"
    TOTAL=$((TOTAL + 1))
    if echo "$haystack" | grep -q "$needle"; then
        echo -e "  ${RED}✗${NC} $desc (unexpectedly contains: $needle)"
        FAIL=$((FAIL + 1))
    else
        echo -e "  ${GREEN}✓${NC} $desc"
        PASS=$((PASS + 1))
    fi
}

get_csrf() {
    curl -s --connect-timeout 5 -c "$COOKIE_JAR" "$BASE_URL/$1" | grep -oP 'name="_token" value="\K[^"]+' | head -1
}

rm -f "$COOKIE_JAR"

# ============================================================
echo ""
echo -e "${YELLOW}═══════════════════════════════════════════════════${NC}"
echo -e "${YELLOW}  TEST: Recuperación de Contraseña${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════════════${NC}"
echo ""

# ── 1. Páginas cargan correctamente ──────────────────────
echo -e "${YELLOW}1. Páginas HTTP${NC}"

STATUS=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 "$BASE_URL/forgot_password.php")
assert_eq "forgot_password.php retorna 200" "200" "$STATUS"

STATUS=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 "$BASE_URL/reset_password.php?token=abc")
assert_eq "reset_password.php retorna 200" "200" "$STATUS"

echo ""

# ── 2. Login page shows "Forgot password?" link ───────────
echo -e "${YELLOW}2. Link en login${NC}"

PAGE=$(curl -s --connect-timeout 5 "$BASE_URL/login.php")
assert_contains "Login tiene link 'Olvidaste tu contraseña'" "forgot_password.php" "$PAGE"

echo ""

# ── 3. Forgot password: correo existente ──────────────────
echo -e "${YELLOW}3. Solicitud con correo existente${NC}"

CSRF=$(get_csrf "forgot_password.php")
RESULT=$(curl -s --connect-timeout 5 -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -X POST "$BASE_URL/forgot_password.php" \
    -d "_token=$CSRF" \
    -d "email=leonel.messi@admin.pandakitsune.com")
assert_contains "Muestra mensaje genérico de éxito" "Si el correo existe" "$RESULT"

echo ""

# ── 4. Forgot password: correo inexistente (mismo mensaje) ─
echo -e "${YELLOW}4. Solicitud con correo inexistente${NC}"

rm -f "$COOKIE_JAR"
CSRF=$(get_csrf "forgot_password.php")
RESULT=$(curl -s --connect-timeout 5 -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -X POST "$BASE_URL/forgot_password.php" \
    -d "_token=$CSRF" \
    -d "email=noexiste@dominio.com")
assert_contains "Muestra mensaje genérico (no revela existencia)" "Si el correo existe" "$RESULT"

echo ""

# ── 5. Token manipulado es rechazado ──────────────────────
echo -e "${YELLOW}5. Token inválido/manipulado${NC}"

RESULT=$(curl -s --connect-timeout 5 "$BASE_URL/reset_password.php?token=abc123.manipulated")
assert_contains "Token manipulado muestra error" "no es válido" "$RESULT"

echo ""

# ── 6. Token vacío es rechazado ───────────────────────────
echo -e "${YELLOW}6. Token vacío${NC}"

RESULT=$(curl -s --connect-timeout 5 "$BASE_URL/reset_password.php?token=")
assert_contains "Token vacío muestra error" "enlace de recuperación válido\|no es válido" "$RESULT"

echo ""

# ── 7. Formulario de resetPassword aparece con token válido ──
echo -e "${YELLOW}7. Generar token válido y verificar formulario${NC}"

# Generate a valid token via PHP directly
TOKEN=$(docker exec pandas_app php -r "
    require '/var/www/html/bootstrap.php';
    use App\Service\TokenService;
    \$ts = new TokenService();
    // Get the admin user's password hash
    \$conn = require '/var/www/html/sql/db.php';
    \$stmt = \$conn->prepare('SELECT password FROM usuario WHERE id_usuario = 1');
    \$stmt->execute();
    \$hash = \$stmt->fetchColumn();
    echo \$ts->generate(1, 'leonel.messi@admin.pandakitsune.com', \$hash);
" 2>/dev/null)

echo "  Token generado: ${TOKEN:0:30}..."

RESULT=$(curl -s --connect-timeout 5 "$BASE_URL/reset_password.php?token=$TOKEN")
assert_contains "Token válido muestra formulario" "Nueva contraseña" "$RESULT"
assert_contains "Token válido muestra nombre del usuario" "Leonel" "$RESULT"

echo ""

# ── 8. Cambio de contraseña con token válido ──────────────
echo -e "${YELLOW}8. Cambio de contraseña${NC}"

rm -f "$COOKIE_JAR"
CSRF=$(get_csrf "reset_password.php?token=$TOKEN")
RESULT=$(curl -s --connect-timeout 5 -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -X POST "$BASE_URL/reset_password.php" \
    -d "_token=$CSRF" \
    -d "token=$TOKEN" \
    -d "new_password=NuevaClave123!" \
    -d "confirm_password=NuevaClave123!" \
    -w "\nHTTP_CODE:%{http_code}")

HTTP_CODE=$(echo "$RESULT" | grep -oP 'HTTP_CODE:\K\d+')
assert_eq "POST retorno 200" "200" "$HTTP_CODE"

BODY=$(echo "$RESULT" | sed 's/HTTP_CODE:.*//')
assert_contains "Mensaje de éxito después del cambio" "actualizada correctamente" "$BODY"

echo ""

# ── 9. Token ya no funciona después del cambio ────────────
echo -e "${YELLOW}9. Token inválido después del cambio${NC}"

RESULT=$(curl -s --connect-timeout 5 "$BASE_URL/reset_password.php?token=$TOKEN")
assert_contains "Token viejo ya no funciona" "no es válido\|ha expirado\|No se proporcionó" "$RESULT"

echo ""

# ── 10. Nueva contraseña funciona para login ──────────────
echo -e "${YELLOW}10. Login con nueva contraseña${NC}"

rm -f "$COOKIE_JAR"
CSRF=$(get_csrf "login.php")
LOGIN_RESULT=$(curl -s --connect-timeout 5 -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -X POST "$BASE_URL/auth.php" \
    -d "_token=$CSRF" \
    -d "email=leonel.messi@admin.pandakitsune.com" \
    -d "password=NuevaClave123!" \
    -w "\nHTTP_CODE:%{http_code}" \
    -D -)

HTTP_CODE=$(echo "$LOGIN_RESULT" | grep -oP 'HTTP_CODE:\K\d+')
assert_eq "Login con nueva contraseña redirige (302)" "302" "$HTTP_CODE"

echo ""

# ── 11. Restaurar contraseña original ─────────────────────
echo -e "${YELLOW}11. Restaurar contraseña original${NC}"

docker exec pandas_app php -r "
    require '/var/www/html/bootstrap.php';
    \$conn = require '/var/www/html/sql/db.php';
    \$hash = password_hash('password0', PASSWORD_BCRYPT, ['cost' => 12]);
    \$stmt = \$conn->prepare('SELECT actualizar_password_usuario_login(1, :h) AS ok');
    \$stmt->execute([':h' => \$hash]);
    echo 'restored';
" 2>/dev/null
echo -e "  ${GREEN}✓${NC} Contraseña original restaurada"

echo ""

# ── 12. Login con contraseña original funciona ────────────
echo -e "${YELLOW}12. Login con contraseña original restaurada${NC}"

rm -f "$COOKIE_JAR"
CSRF=$(get_csrf "login.php")
LOGIN_RESULT=$(curl -s --connect-timeout 5 -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -X POST "$BASE_URL/auth.php" \
    -d "_token=$CSRF" \
    -d "email=leonel.messi@admin.pandakitsune.com" \
    -d "password=password0" \
    -w "\nHTTP_CODE:%{http_code}" \
    -D -)

HTTP_CODE=$(echo "$LOGIN_RESULT" | grep -oP 'HTTP_CODE:\K\d+')
assert_eq "Login con contraseña original funciona" "302" "$HTTP_CODE"

echo ""

# ── 13. No se crearon tablas nuevas ───────────────────────
echo -e "${YELLOW}13. Integridad de BD${NC}"

TABLES=$(docker exec pandas_bd psql -U postgres -d pandas_estampados_y_kitsune -t -c \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public'" 2>/dev/null | tr -d ' ')
assert_eq "Total tablas sigue siendo 13" "13" "$TABLES"

echo ""

# ── 14. CSRF protección ───────────────────────────────────
echo -e "${YELLOW}14. CSRF protection${NC}"

STATUS=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 \
    -X POST "$BASE_URL/forgot_password.php" \
    -d "email=test@test.com")
assert_eq "POST sin CSRF retorna 403" "403" "$STATUS"

echo ""

# ── Resumen ───────────────────────────────────────────────
echo -e "${YELLOW}═══════════════════════════════════════════════════${NC}"
echo -e "  ${GREEN}PASSED: $PASS${NC}  ${RED}FAILED: $FAIL${NC}  TOTAL: $TOTAL"
echo -e "${YELLOW}═══════════════════════════════════════════════════${NC}"
echo ""

rm -f "$COOKIE_JAR"

[ $FAIL -eq 0 ] && exit 0 || exit 1
