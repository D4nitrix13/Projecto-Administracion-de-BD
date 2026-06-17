# ARCHITECTURE.md — Arquitectura del Sistema

## Arquitectura General

El sistema sigue una arquitectura en capas **procedural** con separacion de responsabilidades:

```bash
┌─────────────────────────────────────────────────┐
│                   PRESENTACION                   │
│  [pagina.php] → [partials/ vistas + estilos]     │
├─────────────────────────────────────────────────┤
│                   CONTROLADOR                    │
│  [controllers/*_controller.php]                  │
│  Funciones que retornan arrays de datos          │
├─────────────────────────────────────────────────┤
│                    SERVICIO                       │
│  [src/Service/*]                                 │
│  Logica de negocio, validacion, calculos         │
├─────────────────────────────────────────────────┤
│                   REPOSITORIO                    │
│  [src/Repository/*]                              │
│  Acceso a datos via funciones almacenadas        │
├─────────────────────────────────────────────────┤
│                BASE DE DATOS                      │
│  PostgreSQL 18 + 62 funciones + 10 procedimientos│
│  + 5 triggers + 13 tablas                        │
└─────────────────────────────────────────────────┘
```

## Flujo de Datos

### Request GET (listado)

```bash
1. pagina.php ejecuta session_start()
2. Requiere auth_guard.php (valida sesion)
3. Requiere controller (ej: clientes_controller.php)
4. Controller crea repositorio con $connection
5. Repositorio llama funcion almacenada PostgreSQL
6. Controller retorna array de datos
7. pagina.php require partials/ que renderizan HTML
```

### Request POST (crear/editar)

```bash
1. Mismo flujo que GET hasta paso 3
2. Controller detecta REQUEST_METHOD === "POST"
3. csrfRequire() valida token (via auth_guard)
4. Controller valida datos del POST
5. Si hay errores: retorna array con "error"
6. Si es valido: repositorio/servicio ejecuta funcion almacenada
7. Si es exitoso: redirige con flash message
```

### Facturacion (flujo complejo)

```bash
1. nueva_factura.php → FacturaService
2. FacturaValidationService: valida cliente, items, stock, pago minimo 50%
3. FacturaCalculationService: calcula subtotales, IVA, total, estado
4. FacturaService: llama registrar_factura_sistema() en PostgreSQL
5. PostgreSQL: transaccion atomica (INSERT factura + detalles + DESC stock)
6. Trigger: registrar_historial_estado_factura() registra "Factura creada"
```

## Comunicacion entre Componentes

### PHP → PostgreSQL

Toda la comunicacion es via PDO con funciones almacenadas. Los repositorios NO escriben SQL directamente (excepto AuditoriaRepository y FacturaEstadoHistorialRepository).

```php
// Patron standard
$stmt = $this->pdo->prepare("SELECT * FROM buscar_clientes_filtrados(:busqueda, :tipo)");
$stmt->execute([":busqueda" => $busqueda, ":tipo" => $tipo]);
return $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### Docker Services

```bash
┌──────────────┐     ┌──────────────┐
│  pandas_app  │────>│  pandas_bd   │
│  PHP/Apache  │     │  PostgreSQL  │
│  :8080       │     │  :5432       │
└──────────────┘     └──────────────┘
       │
       │ (pg_dump/psql)
       ▼
┌──────────────┐     ┌──────────────┐
│  backup_     │     │  pandas_     │
│  scheduler   │     │  pgadmin     │
│  (cron loop) │     │  :5050       │
└──────────────┘     └──────────────┘
```

### Env vars (.env)

```bash
DB_HOST=pandas_b
DB_PORT=5432
DB_DATABASE=pandas_estampados_y_kitsune
DB_USERNAME=postgres
DB_PASSWORD=root
APP_TIMEZONE=America/Managua
APP_ENV=development
```

## Modulos Principales

### Autenticacion y Autorizacion

- `includes/auth_guard.php` — `requireLogin()` + `requireAdmin()`
- Roles: Admin(1), Supervisor(2), Facturador(3)
- Secciones: Panda Estampados(1), Kitsune(2)
- CSRF en auth_guard para todos los POST protegidos

### Facturacion

- `src/Service/FacturaService.php` — Orquestacion (287 lineas)
- `src/Service/FacturaValidationService.php` — Validacion (212 lineas)
- `src/Service/FacturaCalculationService.php` — Calculos (112 lineas)
- Stored function `registrar_factura_sistema()` — Transaccion atomica
- Trigger `registrar_historial_estado_factura()` — Historial automatico

### Backups

- `src/Service/BackupService.php` — Vida completa del backup (442 lineas)
- `scripts/backup_*.sh` — Scripts shell para pg_dump
- `utils/cron_backup.php` — Verificador de schedule (cada 60s)
- `storage/system/backup_schedule.json` — Configuracion de schedule

### Inventario

- Repositorios: ProductoRepository, CategoriaRepository, ProveedorRepository
- Stored functions para CRUD y busqueda
- Imagenes via ProductoImageService

## Dependencias PHP

| Paquete          | Version | Uso                             |
| ---------------- | ------- | ------------------------------- |
| php              | >=8.1   | Runtime                         |
| vlucas/phpdotenv | ^5.5    | Variables de entorno desde .env |

## Dependencias del Sistema

| Componente   | Version | Uso                     |
| ------------ | ------- | ----------------------- |
| PostgreSQL   | 18      | Base de datos principal |
| Apache       | 2.4     | Servidor web            |
| PHP          | 8.3     | Runtime                 |
| Docker       | latest  | Contenedores            |
| Composer     | 2       | Gestor de dependencias  |
| pg_dump/psql | 18      | Backups y restauracion  |
| cron         | latest  | Backup scheduler        |

## Mejoras Arquitectonicas Recomendadas

### Alta Prioridad

1. **Mover logica de controller a clases** — Los controllers son procedurales. Convertir a clases con metodos estaticos o instanciacion simple
2. **Unificar acceso a BD** — `reportes_controller.php` y `configurar_cuenta_controller.php` bypass repositorios. Unificar via repositorios
3. **Implementar los 5 controllers/services vacios** — WAL, backups manuales, logs, mantenimiento, programar backups, restaurar

### Media Prioridad

1. **Eliminar wrappers `repositories/` y `services/`** — Despues de migrar todos los controllers a usar namespaces directamente
2. **Agregar tests** — PHPUnit para repositorios y servicios
3. **CSRF en eliminaciones via GET** — Convertir a POST con forms

### Baja Prioridad

1. **Rate limiting** — Proteccion contra fuerza bruta en login
2. **Logging estructurado** — Monolog en vez de error_log
3. **Cache de queries** — Redis o file-based para funciones que no cambian frecuentemente
