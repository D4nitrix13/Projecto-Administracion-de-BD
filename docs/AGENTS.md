# AGENTS.md — Guia para Agentes de IA

## Descripcion General

Sistema de administracion para un negocio de estampados y venta de productos llamado **Panda Estampados / Kitsune**. Gestiona facturacion, inventario, compras, clientes, reportes, respaldos de base de datos y un catalogo publico con integracion a WhatsApp.

**Stack:** PHP 8.3 + PostgreSQL 18 + Docker + Composer (PSR-4 autoloading)

## Estructura de Carpetas

```bash
.
├── bootstrap.php              # Autoload Composer + .env + csrf helpers
├── config/
│   ├── constants.php          # IVA_RATE, TIPO_CLIENTE_HABITUAL/FUGAZ
│   └── database.php           # getDbConfig() desde $_ENV
├── helpers/
│   ├── csrf.php               # csrfToken, csrfField, csrfRequire
│   └── format.php             # formatearFechaExtendida()
├── includes/
│   └── auth_guard.php         # requireLogin(), requireAdmin()
├── sql/
│   ├── db.php                 # Conexion PDO (require para obtener $connection)
│   ├── 01_data.sql            # Schema completo: tablas + funciones + triggers
│   └── 02_procedures.sql      # Procedimientos adicionales
├── src/
│   ├── Repository/            # 11 repositorios namespaced (App\Repository\*)
│   └── Service/               # 6 servicios namespaced (App\Service\*)
├── repositories/              # Wrappers class_alias() -> src/Repository/
├── services/                  # Wrappers class_alias() -> src/Service/
├── controllers/               # Controllers procedurales (25 archivos, 6 vacios)
├── partials/                  # 133 archivos de UI organizados por modulo
├── uploads/productos/         # Imagenes de productos subidas
├── storage/system/            # JSON de configuracion del sistema
├── backups/                   # Respaldos manuales/automaticos
├── scripts/                   # Shell scripts de backup
├── docker/                    # Dockerfile + docker-compose.yml
├── setup.sh                   # Script de instalacion completa
└── [pagina].php               # Archivos de entrada por pagina (dashboard.php, etc.)
```

## Reglas para Agentes de IA

### ANTES de modificar cualquier archivo

1. **Lee `docs/PROJECT_CONTEXT.md`** para entender el dominio del negocio
2. **Lee `docs/ARCHITECTURE.md`** para entender como se comunican los componentes
3. **Nunca modifiques archivos SQL** (`sql/01_data.sql`, `sql/02_procedures.sql`) sin confirmar primero — contienen el schema de produccion
4. **Nunca elimines `repositories/` o `services/` wrappers** — controladores legacy los usan via class_alias
5. **Nunca modifiques `config/database.php`** directamente — usa `.env` para configuracion
6. **Respeta el patron de naming** — los archivos SQL usan snake_case, PHP usa camelCase

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
[pagina.php] → session_start() → require auth_guard → require controller
    → controller llama repositorio/servicio
        → repositorio/servicio llama funcion almacenada PostgreSQL
    → controller retorna array de datos
[pagina.php] → require partials/ (vista + estilos)
```

### Patron de Repositorio

Los repositorios en `src/Repository/` son clases que reciben PDO en el constructor y llaman funciones almacenadas PostgreSQL. Los wrappers en `repositories/` usan `class_alias()` para backward compatibility.

```php
// repositories/ClienteRepository.php (wrapper)
require_once __DIR__ . "/../bootstrap.php";
class_alias(\App\Repository\ClienteRepository::class, 'ClienteRepository');

// src/Repository/ClienteRepository.php (implementacion real)
namespace App\Repository;
class ClienteRepository {
    public function __construct(private \PDO $pdo) {}
    public function obtenerClientePorId(int $id): ?array { ... }
}
```

### Patron de Controller

Los controllers son archivos procedurales que retornan arrays asociativos:

```php
function obtenerDatosClientes(): array {
    $connection = require __DIR__ . "/../sql/db.php";
    $clienteRepository = new ClienteRepository($connection);
    // ... logica ...
    return ["user" => $user, "clientes" => $clientes, ...];
}
```

## Archivos Importantes

| Archivo                     | Por que es importante                                 |
| --------------------------- | ----------------------------------------------------- |
| `bootstrap.php`             | Punto de entrada de autoloading. No duplicar includes |
| `config/database.php`       | Unica fuente de verdad para conexion DB               |
| `includes/auth_guard.php`   | Control de acceso + CSRF en POST                      |
| `sql/db.php`                | Conexion PDO. `require` retorna la conexion           |
| `sql/01_data.sql`           | Schema completo. NO modificar sin revisar             |
| `composer.json`             | Dependencias PHP y autoload PSR-4                     |
| `docker/docker-compose.yml` | Orquestacion de servicios                             |
| `setup.sh`                  | Instalacion completa del proyecto                     |

## Cosas que NO se deben tocar sin revisar

1. **`sql/01_data.sql`** — Contiene 62 funciones almacenadas, 10 procedimientos, 5 triggers. Cualquier cambio puede romper la aplicacion
2. **`repositories/` y `services/` wrappers** — Si los eliminas, los 25+ controladores que los usan van a fallar
3. **`partials/facturacion/facturas/nueva/scripts.php`** (749 lineas) — Logica compleja de JavaScript para crear facturas
4. **`partials/facturacion/facturas/editar/form.php`** (845 lineas) — Formulario de edicion mas grande del proyecto
5. **`.env`** — Contiene credenciales. Nunca commitear
6. **`storage/system/`** — JSON de configuracion del sistema. Se genera en setup.sh

## Comandos Utiles

```bash
# Instalar y ejecutar todo
bash setup.sh

# Verificar sintaxis PHP de todos los archivos
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# Ver logs de la app
docker logs pandas_app

# Ver logs de PostgreSQL
docker logs pandas_bd

# Entrar al contenedor de la app
docker exec -it pandas_app bash

# Conectar a PostgreSQL
docker exec -it pandas_bd psql -U postgres -d pandas_estampados_y_kitsune

# Reconstruir solo la imagen Docker
docker compose -f docker/docker-compose.yml build app

# Levantar servicios
docker compose -f docker/docker-compose.yml up -d

# Detener servicios
docker compose -f docker/docker-compose.yml down
```

## Problemas Conocidos

1. **`sql/db.php` path** — Usa `__DIR__ . "/../config/database.php"` (no `__DIR__ . "/config/database.php"`)
2. **Docker volumes** — El `..:/var/www/html` mapea el proyecto raiz. Los cambios en el host se reflejan inmediatamente
3. **6 controllers vacios** — `archivos_wal_controller.php`, `backups_manuales_controller.php`, `logs_sistema_controller.php`, `mantenimiento_bd_controller.php`, `programar_backups_controller.php`, `restaurar_bd_controller.php` estan sin implementar
4. **5 services vacios** — `BackupFileService.php`, `BackupScheduleService.php`, `LogFileService.php`, `MantenimientoBdService.php`, `WalFileService.php` estan sin implementar
5. **reportes_controller.php y configurar_cuenta_controller.php** — Llaman stored functions directamente via PDO en vez de usar repositorios
