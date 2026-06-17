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

### Modulo 2: Autenticacion

Login con sesiones PHP, hash bcrypt, soporte para contraseñas legacy con auto-rehash.

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
- Eliminacion con restauracion de stock

### Modulo 9: Compras

Registro de compras a proveedores con incremento automatico de stock.

### Modulo 10: Trabajadores

CRUD de usuarios con roles (Admin, Supervisor, Facturador) y secciones (Panda Estampados, Kitsune).

### Modulo 11: Reportes

Dashboard analitico con ventas por dia, top productos, ranking de clientes, filtros por rango de fechas.

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

## Decisiones Tecnicas Tomadas

| Decision               | Eleccion              | Razon                                                |
| ---------------------- | --------------------- | ---------------------------------------------------- |
| Framework              | Ninguno (vanilla PHP) | Proyecto de aprendizaje/control academico            |
| Base de datos          | PostgreSQL 18         | Stored functions/procedures para logica de negocio   |
| Autoloading            | Composer PSR-4        | Namespaces en `App\Repository\*` y `App\Service\*`   |
| Backward compatibility | class_alias wrappers  | Controladores legacy funcionan sin modificar         |
| CSRF                   | Token por sesion      | Proteccion basica en todos los POST                  |
| Contraseñas            | bcrypt (cost 12)      | Seguridad + rehash automatico de legacy              |
| Configuracion          | .env via Dotenv       | Credenciales fuera del codigo                        |
| Contenedores           | Docker Compose        | PHP/Apache + PostgreSQL + pgAdmin + Backup Scheduler |
| Imagenes               | Upload directo        | `uploads/productos/` con nombres unicos              |

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
- Docker multi-servicio

### Incompleto / Pendiente

- 5 controllers vacios (WAL, backups manuales, logs, mantenimiento, programar backups, restaurar)
- 5 services vacios (BackupFile, BackupSchedule, LogFile, MantenimientoBd, WalFile)
- No hay tests unitarios
- No hay tests de integracion
- Eliminaciones via GET (deberian ser POST para CSRF completo)
- reportes_controller.php y configurar_cuenta_controller.php bypass repositorios

### Suposiciones Importantes

- El numero de WhatsApp se almacena en `numero_de_whatsapp.txt` (archivo plano)
- La configuracion del sistema (limite fugaz) se almacena en `storage/system/configuracion_sistema.json`
- El historial de mantenimiento se almacena en `storage/system/maintenance_history.json`
- La cola de eliminacion de respaldos se almacena en `backups/logs/delete_queue.json`
- IVA fijo al 15% (`IVA_RATE = 0.15`)
- Zona horaria: `America/Managua`
- Puerto de la app: 8080
- Puerto de pgAdmin: 5050
