# TASKS.md — Tareas Pendientes

## Alta Prioridad

### 1. Implementar controllers vacios

Los siguientes controllers estan vacios (0 lineas) y necesitan implementacion:

| Controller                         | Modulo                | Estado |
| ---------------------------------- | --------------------- | ------ |
| `archivos_wal_controller.php`      | Sistema/WAL           | Vacio  |
| `backups_manuales_controller.php`  | Sistema/Backups       | Vacio  |
| `logs_sistema_controller.php`      | Sistema/Logs          | Vacio  |
| `mantenimiento_bd_controller.php`  | Sistema/Mantenimiento | Vacio  |
| `programar_backups_controller.php` | Sistema/Programacion  | Vacio  |
| `restaurar_bd_controller.php`      | Sistema/Restaurar     | Vacio  |

### 2. Implementar services vacios

Los siguientes services estan vacios (0 lineas):

| Service                      | Modulo        | Estado |
| ---------------------------- | ------------- | ------ |
| `BackupFileService.php`      | Backups       | Vacio  |
| `BackupScheduleService.php`  | Backups       | Vacio  |
| `LogFileService.php`         | Logs          | Vacio  |
| `MantenimientoBdService.php` | Mantenimiento | Vacio  |
| `WalFileService.php`         | WAL           | Vacio  |

### 3. Unificar acceso a BD

`reportes_controller.php` y `configurar_cuenta_controller.php` llaman stored functions directamente via PDO en vez de usar repositorios. Migrar a:

- `src/Repository/ReporteRepository.php`
- `src/Repository/ConfiguracionRepository.php`

## Media Prioridad

### 4. Convertir eliminaciones via GET a POST

Los siguientes archivos usan GET para eliminar (vulnerabilidad CSRF):

| Archivo                  | Accion              |
| ------------------------ | ------------------- |
| `eliminar_cliente.php`   | Eliminar cliente    |
| `eliminar_producto.php`  | Eliminar producto   |
| `eliminar_categoria.php` | Eliminar categoria  |
| `eliminar_proveedor.php` | Eliminar proveedor  |
| `eliminar_usuario.php`   | Eliminar trabajador |
| `eliminar_factura.php`   | Eliminar factura    |

Solucion: Agregar forms con `method="POST"` y `csrfField()` en las vistas de listado.

### 5. Agregar tests unitarios

- Configurar PHPUnit en `composer.json`
- Tests para `FacturaValidationService`
- Tests para `FacturaCalculationService`
- Tests para `ProductoImageService`
- Tests para repositorios (con PostgreSQL de prueba)

### 6. Eliminar wrappers legacy

Despues de migrar todos los controllers a usar namespaces directamente:

- Eliminar `repositories/*.php` (wrappers)
- Eliminar `services/*.php` (wrappers)
- Actualizar `composer.json` para que el autoload apunte directamente a `src/`

### 7. Refactorizar controllers a clases

Convertir los 25 controllers procedurales a clases con inyeccion de dependencias:

```php
// Antes (procedural)
function obtenerDatosClientes(): array { ... }

// Despues (OOP)
class ClientesController {
    public function __construct(
        private ClienteRepository $clienteRepository
    ) {}
    public function index(): array { ... }
}
```

## Baja Prioridad

### 8. Agregar rate limiting

Proteger el endpoint de login contra fuerza bruta. Implementar con archivos o Redis.

### 9. Logging estructurado

Reemplazar `error_log()` con Monolog o similar para logging con niveles.

### 10. Cache de queries

Implementar cache para funciones que no cambian frecuentemente (listar roles, secciones, categorias).

### 11. Optimizar partials de estilos

Los archivos CSS en `partials/` son muy grandes (algunos > 500 lineas). Considerar:

- Unificar en un archivo CSS principal
- O usar un preprocessor (Sass/SCSS)

### 12. Mejorar validacion de imagenes

`ProductoImageService` valida extension pero no valida magic bytes. Agregar validacion de tipo MIME real.

## Bugs Detectados

### Bug 1: Facturacion de cliente fugaz sin registro

Cuando se crea una factura para un cliente fugaz, el sistema busca un cliente con `identificacion='FUGAZ'`. Si no existe, la factura falla. Verificar que `obtener_id_cliente_fugaz()` funcione correctamente.

### Bug 2: Eliminacion de factura sin restaurar stock correctamente

El procedimiento `eliminar_factura_sistema()` restaura stock. Verificar que el trigger de auditoria no interfiera con la restauracion.

### Bug 3: Edicion de factura con stock insuficiente

Al editar una factura, si se aumenta la cantidad de un producto, el stock podria quedar negativo. `editar_factura_sistema()` deberia validar esto.

### Bug 4: Permisos de archivos en Docker

Los archivos subidos (`uploads/productos/`) pueden tener permisos incorrectos despues de `setup.sh`. Verificar que `sudo chown -R 33:33 uploads` funcione correctamente.

## Proximos Pasos Recomendados

1. **Completar los 5 controllers vacios** — Son los modulos de sistema mas criticos
2. **Unificar acceso a BD** — Crear repositorios para reportes y configuracion
3. **Convertir eliminaciones a POST** — Seguridad CSRF
4. **Agregar tests** — Al menos para los servicios de facturacion
5. **Documentar stored functions** — Crear documentacion para las 62 funciones en PostgreSQL
