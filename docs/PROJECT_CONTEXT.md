# PROJECT_CONTEXT.md — Contexto del Proyecto

## Que problema resuelve

El negocio **Panda Estampados / Kitsune** necesitaba un sistema para gestionar sus operaciones diarias: facturacion de ventas a clientes (habituales y fugazes), control de inventario de productos, registro de compras a proveedores, generacion de reportes de ventas, y respaldos automatizados de la base de datos.

Antes del sistema, no existia una plataforma centralizada que integrara todas estas operaciones.

## Para quien esta pensado

- **Admin (rol 1):** Acceso total a todas las funcionalidades
- **Supervisor (rol 2):** Gestion de clientes detallista, facturacion, reportes
- **Facturador (rol 3):** Facturacion y gestion de clientes detallista
- **Clientes externos:** Acceso al catalogo publico via navegador/WhatsApp

## Funcionalidades Principales

### Modulo 1: Catalogo Publico

Pagina publica (sin login) que muestra productos con precios, disponibilidad e integracion a WhatsApp para consultas.

### Modulo 2: Autenticacion y Recuperacion de Contrasena

Login con sesiones PHP, hash bcrypt, soporte para contraseñas legacy con auto-rehash.

**Recuperacion de contrasena via email:**

- Formulario "Olvide mi contrasena" en la pantalla de login
- Email con link de reseteo (HMAC SHA-256 firmado, expira en 30 minutos)
- Cambio de contrasena y actualizacion automatica del hash
- MailPit captura emails en desarrollo (puerto 8025)
- Sin nueva tabla DB — tokens se invalidan automaticamente al cambiar contrasena

### Modulo 3: Dashboard

Panel principal con metricas (clientes, facturas, ventas, stock bajo), graficos de ventas diarias y top productos.

### Modulo 4: Clientes

CRUD completo con dos tipos: Mayorista y Detallista. Roles restringidos solo ven/crean clientes Detallista.

### Modulo 5: Productos

CRUD con imagenes, categorias, proveedores, precios de compra/venta y control de stock.

### Modulo 6: Categorias

CRUD para clasificar productos. Solo Admin puede gestionar.

### Modulo 7: Proveedores

CRUD para proveedores que surten productos. Admin y Supervisor pueden gestionar.

### Modulo 8: Facturacion (MODULO MAS COMPLEJO)

Ciclo de vida completo:

- Crear factura con seleccion de cliente/productos/descuentos
- Estados de pago: Pendiente -> Parcial -> Pagado
- Estados de produccion: Pendiente -> En produccion -> Lista para entregar -> Entregada/Cancelada
- Historial de estados con timeline
- Impresion de facturas (formato media carta)
- Edicion de facturas (solo Admin)
- Eliminacion con restauracion de stock y CSRF

### Modulo 9: Compras

Registro de compras a proveedores con incremento automatico de stock.

### Modulo 10: Trabajadores

CRUD de usuarios con roles (Admin, Supervisor, Facturador) y secciones (Panda Estampados, Kitsune).

### Modulo 11: Reportes

Dashboard analitico con ventas por dia, top productos, ranking de clientes, filtros por rango de fechas.

Exportacion a Excel via PhpSpreadsheet 5.8 con botones por tabla (ventas_diarias, productos_mas_vendidos, clientes_top, resumen_ventas).

### Modulo 12: Backups

Gestion completa: manual, programado, restauracion, mantenimiento, descarga, eliminacion programada.

### Modulo 13: Auditoria

Registro de eliminaciones con capacidad de restauracion desde JSONB.

### Modulo 14: Historial de Estados

Timeline de cambios de estado de facturas con filtros.

### Modulo 15: Logs y WAL

Visor de logs del sistema y archivos Write-Ahead Log de PostgreSQL.

### Modulo 16: Configuracion

Cuenta de usuario, limite de venta fugaz, numero de WhatsApp.

### Modulo 17: Notificaciones

Sistema de notificaciones en tiempo real:

- Almacena en JSON (`storage/system/notificaciones.json`)
- Polling cada 15 segundos via AJAX
- Campanita en el sidebar con badge de no leidas
- Notificaciones por facturacion, stock bajo, eliminaciones
- Endpoints: `notificaciones.php`, `marcar_leida.php`, `marcar_todas_leidas.php`

## Decisiones Tecnicas Tomadas

| Decision               | Eleccion              | Razon                                                |
| ---------------------- | --------------------- | ---------------------------------------------------- |
| Framework              | Ninguno (vanilla PHP) | Proyecto de aprendizaje/control academico            |
| Base de datos          | PostgreSQL 18         | Stored functions/procedures para logica de negocio   |
| Autoloading            | Composer PSR-4        | Namespaces en `App\Repository\*` y `App\Service\*`   |
| Backward compatibility | class_alias wrappers  | Controladores legacy funcionan sin modificar         |
| CSRF                   | Token por sesion      | Proteccion basica en todos los POST                  |
| Contrasenas            | bcrypt (cost 12)      | Seguridad + rehash automatico de legacy              |
| Configuracion          | .env via Dotenv       | Credenciales fuera del codigo                        |
| Contenedores           | Docker Compose        | PHP/Apache + PostgreSQL + pgAdmin + Backup + MailPit |
| Imagenes               | Upload directo        | `uploads/productos/` con nombres unicos              |
| Password reset         | HMAC SHA-256 token    | Sin nueva tabla DB — se invalida automaticamente     |
| Email SMTP             | PHPMailer 7.1         | Envio de emails para password recovery               |
| SMTP testing           | MailPit               | Captura de emails en desarrollo (puerto 8025)        |
| Excel export           | PhpSpreadsheet 5.8    | Exportacion de reportes a Excel                      |

## Estado del Proyecto

### Terminado

- Todos los modulos CRUD (clientes, productos, categorias, proveedores, trabajadores)
- Sistema de facturacion completo con estados y historial
- Dashboard con graficos
- Catalogo publico con WhatsApp
- Sistema de backups manual y programado
- Restauracion de base de datos
- Auditoria de eliminaciones
- Visor de logs y WAL
- Configuracion del sistema
- Autenticacion y control de roles
- Proteccion CSRF en todos los POST
- Docker multi-servicio (app, postgres, pgadmin, backup_scheduler, mailpit)
- Recuperacion de contrasena via email con HMAC tokens
- Sistema de notificaciones en tiempo real
- Exportacion de reportes a Excel
- 177 tests automatizados (129 CRUD + 32 integracion + 32 notificaciones)

### Incompleto / Pendiente

- 5 controllers vacios (WAL, backups manuales, logs, mantenimiento, programar backups, restaurar)
- 5 services vacios (BackupFile, BackupSchedule, LogFile, MantenimientoBd, WalFile)
- No hay tests unitarios
- Convertir remaining `catalogo_controller.php` queries a CatalogoRepository

### Suposiciones Importantes

- El numero de WhatsApp se almacena en `numero_de_whatsapp.txt` (archivo plano)
- La configuracion del sistema (limite fugaz) se almacena en `storage/system/configuracion_sistema.json`
- El historial de mantenimiento se almacena en `storage/system/maintenance_history.json`
- La cola de eliminacion de respaldos se almacena en `backups/logs/delete_queue.json`
- IVA fijo al 15% (`IVA_RATE = 0.15`)
- Zona horaria: `America/Managua`
- Puerto de la app: 8080
- Puerto de pgAdmin: 5050
- Puerto de MailPit web: 8025
- Puerto de MailPit SMTP: 1025

## Como Ejecutar

```bash
# Instalar y ejecutar todo (recomendado)
bash setup.sh

# Verificar que funcione
curl http://localhost:8080/
# Login: leonel.messi@admin.pandakitsune.com / password0
```

## Estructura de Docker

| Servicio         | Contenedor                | Puerto    | Funcion                              |
| ---------------- | ------------------------- | --------- | ------------------------------------ |
| PHP/Apache       | `pandas_app`              | 8080      | Servidor web de la aplicacion        |
| PostgreSQL       | `pandas_bd`               | 5432      | Base de datos                        |
| pgAdmin          | `pandas_pgadmin`          | 5050      | Admin web de PostgreSQL              |
| Backup Scheduler | `pandas_backup_scheduler` | (no)      | Ejecuta backups automaticos cada 60s |
| MailPit          | `pandas_mailpit`          | 8025/1025 | Captura de emails en desarrollo      |
