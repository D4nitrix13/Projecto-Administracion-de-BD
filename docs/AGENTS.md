# AGENTS.md — Guia para Agentes de IA

## Descripcion General

Sistema de administracion para un negocio de estampados y venta de productos llamado **Panda Estampados / Kitsune**. Gestiona facturacion, inventario, compras, clientes, reportes, respaldos de base de datos, un catalogo publico con integracion a WhatsApp, recuperacion de contrasena via email, y notificaciones en tiempo real.

**Stack:** PHP 8.3 + PostgreSQL 18 + Docker Compose + Composer (PSR-4 autoload)

## Estructura de Carpetas

```bash
.
├── bootstrap.php              # Autoload Composer + .env + csrf helpers
├── config/
│   ├── constants.php          # IVA_RATE, TIPO_CLIENTE constants
│   └── database.php           # getDbConfig() from $_ENV
├── helpers/
│   ├── csrf.php               # csrfToken, csrfField, csrfRequire
│   ├── format.php             # Number/date formatting helpers
│   └── notificaciones.php     # notificar(), notificarAdmin(), notificarSeccion()
├── includes/
│   ├── auth.php               # Login/logout, password_verify, session creation
│   └── auth_guard.php         # requireLogin(), requireAdmin(), CSRF on POST
├── sql/
│   ├── db.php                 # PDO connection (require returns $connection)
│   ├── 01_data.sql            # 13 tables, 84 functions, 10 procedures, 5 triggers
│   ├── 02_procedures.sql      # Additional procedures
│   └── README.md              # DB documentation with credentials
├── src/
│   ├── Controller/
│   │   ├── ForgotPasswordController.php   # Forgot password form + email sending
│   │   └── ResetPasswordController.php    # Token validation + password update
│   ├── Repository/            # 13 namespaced repos (App\Repository\*)
│   └── Service/
│       ├── TokenService.php              # HMAC SHA-256 token generation/validation
│       ├── EmailService.php              # PHPMailer wrapper (conditional SMTPAuth)
│       └── ...                           # Other services
├── repositories/              # class_alias() wrappers -> src/Repository/
├── services/                  # class_alias() wrappers -> src/Service/
├── controllers/               # Procedural controllers (25 files)
├── partials/                  # UI organized by module
├── uploads/productos/         # Product images
├── storage/system/            # JSON config + notifications
├── backups/                   # Manual/automatic backups
├── scripts/
│   ├── test_crud.sh           # 129 CRUD tests
│   ├── test_crud_integration.sh  # 32 integration tests
│   ├── test_notificaciones.sh    # 32 notification tests
│   └── test_password_reset.sh    # 16 password reset tests
├── docker/
│   ├── Dockerfile             # PHP 8.3 + Apache + extensions
│   └── docker-compose.yml     # 5 services: app, postgres, backup_scheduler, pgadmin, mailpit
├── .env                       # All config (DB, SMTP, password reset secrets)
├── .env.example               # Documented template
├── composer.json              # PSR-4 autoload, dotenv, phpspreadsheet, phpmailer
├── setup.sh                   # Full project setup
└── [page].php                 # Entry files (dashboard.php, login.php, etc.)
```

## Reglas para Agentes de IA

### ANTES de modificar cualquier archivo

1. **Lee `docs/PROJECT_CONTEXT.md`** para entender el dominio del negocio
2. **Lee `docs/ARCHITECTURE.md`** para entender como se comunican los componentes
3. **Nunca modifiques archivos SQL** (`sql/01_data.sql`, `sql/02_procedures.sql`) sin confirmar primero
4. **Nunca elimines `repositories/` o `services/` wrappers** — controladores legacy los usan via class_alias
5. **Nunca modifiques `config/database.php`** directamente — usa `.env` para configuracion
6. **Respeta el patron de naming** — SQL usa snake_case, PHP usa camelCase

### Convenciones de Nombres

| Tipo                 | Convencion | Ejemplo                                      |
| -------------------- | ---------- | -------------------------------------------- |
| Archivos PHP         | snake_case | `editar_cliente_controller.php`              |
| Funciones PHP        | camelCase  | `obtenerDatosClientes()`                     |
| Variables PHP        | camelCase  | `$idRol`, `$flashSuccess`                    |
| Tablas PostgreSQL    | snake_case | `detallefactura`, `factura_estado_historial` |
| Funciones PostgreSQL | snake_case | `registrar_cliente_sistema()`                |
| Clases PHP           | PascalCase | `ClienteRepository`, `FacturaService`        |
| Namespaces           | PascalCase | `App\Repository\ClienteRepository`           |

### Flujo de Request

```bash
[page.php] -> session_start() -> require auth_guard -> require controller
    -> controller calls repository/service
        -> repository/service calls PostgreSQL stored function
    -> controller returns array
[page.php] -> require partials/ (view + styles)
```

### Patron de Repositorio

Repositorios en `src/Repository/` reciben PDO en constructor y llaman funciones almacenadas PostgreSQL. Wrappers en `repositories/` usan `class_alias()` para backward compatibility.

```php
// repositories/ClienteRepository.php (wrapper)
require_once __DIR__ . "/../bootstrap.php";
class_alias(\App\Repository\ClienteRepository::class, 'ClienteRepository');

// src/Repository/ClienteRepository.php (real implementation)
namespace App\Repository;
class ClienteRepository {
    public function __construct(private \PDO $pdo) {}
    public function obtenerClientePorId(int $id): ?array { ... }
}
```

### Patron de Controller

Controllers son archivos procedurales que retornan arrays asociativos:

```php
function obtenerDatosClientes(): array {
    $connection = require __DIR__ . "/../sql/db.php";
    $clienteRepository = new ClienteRepository($connection);
    // ... logic ...
    return ["user" => $user, "clientes" => $clientes, ...];
}
```

### Patron de Servicio

Servicios en `src/Service/` son clases namespaced que encapsulan logica reutilizable. Wrappers en `services/` usan `class_alias()` igual que repositorios.

```php
// src/Service/TokenService.php
namespace App\Service;
class TokenService {
    public static function generateToken(string $userId): string { ... }
    public static function validateToken(string $token): ?array { ... }
}
```

## Archivos Importantes

| Archivo                                      | Por que es importante                                 |
| -------------------------------------------- | ----------------------------------------------------- |
| `bootstrap.php`                              | Punto de entrada de autoloading. No duplicar includes |
| `config/database.php`                        | Unica fuente de verdad para conexion DB               |
| `includes/auth_guard.php`                    | Control de acceso + CSRF en POST                      |
| `sql/db.php`                                 | Conexion PDO. `require` retorna la conexion           |
| `sql/01_data.sql`                            | Schema completo. NO modificar sin revisar             |
| `composer.json`                              | Dependencias PHP y autoload PSR-4                     |
| `docker/docker-compose.yml`                  | Orquestacion de 5 servicios                           |
| `setup.sh`                                   | Instalacion completa del proyecto                     |
| `.env`                                       | Configuracion (DB, SMTP, password reset secrets)      |
| `src/Controller/ResetPasswordController.php` | Password reset — usa getConnection() estatico         |
| `src/Service/TokenService.php`               | HMAC token generation/validation                      |
| `src/Service/EmailService.php`               | PHPMailer wrapper with conditional SMTPAuth           |

## Cosas que NO se deben tocar sin revisar

1. **`sql/01_data.sql`** — 84 functions, 10 procedures, 5 triggers. Cualquier cambio puede romper la aplicacion
2. **`repositories/` y `services/` wrappers** — Si los eliminas, 25+ controladores van a fallar
3. **`partials/facturacion/facturas/nueva/scripts.php`** — Logica compleja JS para crear facturas
4. **`partials/facturacion/facturas/editar/form.php`** — Formulario de edicion mas grande del proyecto
5. **`.env`** — Contiene credenciales y secretos. Nunca commitear
6. **`storage/system/`** — JSON de configuracion y notificaciones

## Comandos Utiles

```bash
# Instalar y ejecutar todo
bash setup.sh

# Verificar sintaxis PHP de todos los archivos
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# Ejecutar tests
./scripts/test_crud.sh           # 129 tests CRUD
./scripts/test_crud_integration.sh  # 32 tests de integracion
./scripts/test_notificaciones.sh    # 32 tests de notificaciones
./scripts/test_password_reset.sh    # 16 tests de recuperacion de contrasena

# Ver logs de la app
docker logs pandas_app

# Ver logs de PostgreSQL
docker logs pandas_bd

# Entrar al contenedor de la app
docker exec -it pandas_app bash

# Conectar a PostgreSQL
docker exec -it pandas_bd psql -U postgres -d pandas_estampados_y_kitsune

# Ver emails capturados por MailPit
# Abrir http://localhost:8025 en el navegador

# Reconstruir solo la imagen Docker
docker compose -f docker/docker-compose.yml build app

# Levantar servicios
docker compose -f docker/docker-compose.yml up -d

# Detener servicios
docker compose -f docker/docker-compose.yml down
```

## Credenciales

| Servicio   | Usuario                               | Contrasena |
| ---------- | ------------------------------------- | ---------- |
| Login app  | leonel.messi@admin.pandakitsune.com   | password0  |
| PostgreSQL | postgres                              | root       |
| pgAdmin    | admin@admin.com                       | admin      |

## Problemas Conocidos

1. **`sql/db.php` path** — Usa `__DIR__ . "/../config/database.php"` (no `__DIR__ . "/config/database.php"`)
2. **Docker volumes** — `..:/var/www/html` mapea el proyecto raiz. Cambios en el host se reflejan inmediatamente
3. **`createConnection()` redeclaration** — Controllers que usan `require sql/db.php` fallan si se llaman dos veces en el mismo request. Usa `getConnection()` estatico en ResetPasswordController/ForgotPasswordController
4. **5 controllers vacios** — `archivos_wal_controller.php`, `backups_manuales_controller.php`, `logs_sistema_controller.php`, `mantenimiento_bd_controller.php`, `programar_backups_controller.php`, `restaurar_bd_controller.php`
5. **5 services vacios** — `BackupFileService.php`, `BackupScheduleService.php`, `LogFileService.php`, `MantenimientoBdService.php`, `WalFileService.php`
6. **Admin user tiene `id_seccion=NULL`** — Al crear facturas, `id_seccion` se necesita setear explicitamente a 1
7. **`phpoffice/phpspreadsheet` instalado localmente** — Los endpoints de exportacion (`export.php`) lo cargan via Composer autoload. Si corre fuera de Docker, Composer install es necesario
8. **`phpmailer/phpmailer` instalado en el contenedor** — Para instalarlo fuera de Docker: `composer require phpmailer/phpmailer`
9. **MailPit no requiere autenticacion** — `SMTPUser` y `SMTPPass` vacios en `.env` para MailPit. Para produccion SMTP real, configurar credenciales en `.env`
10. **RESET_PASSWORD_SECRET generado** — Ya esta en `.env`. Si se pierde, generar con `openssl rand -hex 32`
