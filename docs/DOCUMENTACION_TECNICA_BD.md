# Documentación Técnica — Administración de Bases de Datos

## Panda Estampados / Kitsune — Sistema de Gestión Empresarial

---

## 1. Stack Tecnológico

| Componente              | Tecnología             | Versión |
| ----------------------- | ---------------------- | ------- |
| **Base de datos**       | PostgreSQL             | 18.3    |
| **Lenguaje servidor**   | PHP                    | 8.3     |
| **Contenedorization**   | Docker Compose         | v2      |
| **Web server**          | Apache (mod_php)       | —       |
| **Gestión DB (GUI)**    | pgAdmin 4              | latest  |
| **Dependencias PHP**    | Composer               | 2.x     |
| **Exportación Excel**   | PhpSpreadsheet         | 5.8     |
| **Hashing contraseñas** | bcrypt (password_hash) | cost 12 |
| **Zona horaria**        | America/Managua        | —       |
| **Impuesto IVA**        | 15%                    | —       |

---

## 2. Arquitectura Docker

```bash
┌───────────────────────────────────────────────────────────────┐
│                    docker-compose.yml                         │
├────────────────┬──────────────┬───────────────┬───────────────┤
│  pandas_app    │  pandas_bd   │ pandas_pgadmin│  pandas_backup│
│  PHP 8.3/Apache│ PostgreSQL 18│  pgAdmin 4    │  _scheduler   │
│  :8080         │  :5432       │  :5050        │  (no puerto)  │
└───────┬────────┴──────────────┴───────────────┴────┬──────────┘
        │                                            │
        │   Volumen compartido: database/postgresql  │
        │   Volumen WAL: database/wal_archive        │
        │   Volumen backups: backups/                │
        └────────────────────────────────────────────┘
```

### 2.1 Servicios

| Servicio           | Contenedor                | Puerto  | Función                                |
| ------------------ | ------------------------- | ------- | -------------------------------------- |
| `app`              | `pandas_app`              | 8080→80 | PHP + Apache, sirve la aplicación web  |
| `postgres`         | `pandas_bd`               | 5432    | PostgreSQL 18 con WAL archiving        |
| `pgadmin`          | `pandas_pgadmin`          | 5050→80 | Interfaz gráfica de administración     |
| `backup_scheduler` | `pandas_backup_scheduler` | —       | Daemon que ejecuta backups automáticos |

### 2.2 Configuración PostgreSQL

```sql
-- Parámetros de arranque en docker-compose.yml
-c timezone=America/Managua
-c wal_level=replica
-c archive_mode=on
-c archive_command='test ! -f /wal_archive/%f && cp %p /wal_archive/%f'
```

**WAL Archiving**: Cada archivo WAL se copia a `/wal_archive/` antes de ser reutilizado, permitiendo backups incrementales y point-in-time recovery.

---

## 3. Modelo de Base de Datos

### 3.1 Esquema General

- **Base de datos**: `pandas_estampados_y_kitsune`
- **Usuario**: `postgres`
- **Contraseña**: `root`
- **Zona horaria**: `America/Managua`
- **Total tablas**: 13
- **Total funciones**: 84
- **Total procedimientos**: 10
- **Total triggers**: 5

### 3.2 Diagrama de Entidades (Resumen)

```bash
                    ┌──────────┐
                    │   rol    │
                    └────┬─────┘
                         │ 1:N
                    ┌────┴─────┐
                    │ usuario  │─────────────┐
                    └────┬─────┘              │
                         │                    │ 1:N
                    ┌────┴─────┐         ┌────┴──────┐
                    │ seccion  │         │  compra   │
                    └──────────┘         └─────┬─────┘
                                               │ 1:N
                    ┌──────────┐         ┌─────┴────────┐
                    │proveedor │─────────│detallecompra │
                    └────┬─────┘         └──────────────┘
                         │ 1:N
                    ┌────┴─────┐
                    │ producto │
                    └────┬─────┘
                         │
            ┌────────────┼────────────┐
            │ 1:N        │ 1:N        │
     ┌──────┴──────┐  ┌──┴────────┐  ┌┴──────────────┐
     │  categoria  │  │detallefac │  │               │
     └─────────────┘  │  tura     │  │               │
                      └─────┬─────┘  └───────────────┘
                            │ 1:N
                       ┌────┴─────┐
                       │ factura  │
                       └────┬─────┘
                            │ 1:N
                       ┌────┴────────────┐
                       │    cliente      │
                       └─────────────────┘
```

### 3.3 Diagrama Chen (Completo)

Ver `sql/ER_CHEN.md` para el diagrama completo con cardinalidades.

### 3.4 Diagrama UML de Clases

Ver `sql/uml_clases.puml` o `sql/UML_CLASES.md` — incluye 13 clases con atributos, métodos y 15 relaciones.

---

## 4. Tablas del Sistema

### 4.1 `usuario`

Almacena los usuarios del sistema con sus credenciales y roles.

| Columna      | Tipo         | Restricción            | Descripción                           |
| ------------ | ------------ | ---------------------- | ------------------------------------- |
| `id_usuario` | INTEGER      | PK, SERIAL             | Identificador único                   |
| `nombre`     | VARCHAR(100) | NOT NULL               | Nombre completo                       |
| `email`      | VARCHAR(120) | NOT NULL, UNIQUE       | Correo electrónico (usado para login) |
| `password`   | TEXT         | NOT NULL               | Hash bcrypt (cost 12)                 |
| `id_rol`     | INTEGER      | FK → rol               | Rol del usuario                       |
| `id_seccion` | INTEGER      | FK → seccion, NULLABLE | Sección (NULL para admin)             |

**Nota**: El usuario admin (id=1) tiene `id_seccion=NULL` y no puede ser eliminado.

### 4.2 `rol`

Roles disponibles en el sistema.

| id_rol | nombre        |
| ------ | ------------- |
| 1      | Administrador |
| 2      | Supervisor    |
| 3      | Facturador    |

### 4.3 `seccion`

Secciones/departamentos del negocio.

| id_seccion | nombre           |
| ---------- | ---------------- |
| 1          | Kitsune          |
| 2          | Panda Estampados |

### 4.4 `cliente`

Clientes mayoristas y detallistas.

| Columna          | Tipo         | Restricción                    | Descripción                |
| ---------------- | ------------ | ------------------------------ | -------------------------- |
| `id_cliente`     | INTEGER      | PK, SERIAL                     | Identificador único        |
| `nombres`        | VARCHAR(80)  | NOT NULL                       | Nombres                    |
| `apellidos`      | VARCHAR(80)  | NOT NULL                       | Apellidos                  |
| `telefono`       | VARCHAR(30)  | NULLABLE                       | Teléfono                   |
| `direccion`      | VARCHAR(200) | NULLABLE                       | Dirección                  |
| `identificacion` | VARCHAR(40)  | NULLABLE                       | Cédula/RUC                 |
| `tipo_cliente`   | VARCHAR(12)  | NOT NULL, DEFAULT 'Detallista' | 'Mayorista' o 'Detallista' |
| `fecha_registro` | DATE         | NOT NULL, DEFAULT CURRENT_DATE | Fecha de alta              |

**Cliente Fugaz**: Existe un cliente especial con `identificacion='FUGAZ'` (id=52) para ventas walk-in sin registro.

### 4.5 `producto`

Productos del catálogo.

| Columna         | Tipo          | Restricción               | Descripción               |
| --------------- | ------------- | ------------------------- | ------------------------- |
| `id_producto`   | INTEGER       | PK, SERIAL                | Identificador único       |
| `codigo`        | VARCHAR(50)   | NOT NULL, UNIQUE          | Código único del producto |
| `nombre`        | VARCHAR(120)  | NOT NULL                  | Nombre                    |
| `descripcion`   | TEXT          | DEFAULT 'Sin descripción' | Descripción               |
| `imagen`        | VARCHAR(200)  | NULLABLE                  | Ruta de imagen            |
| `id_categoria`  | INTEGER       | FK → categoria            | Categoría                 |
| `id_proveedor`  | INTEGER       | FK → proveedor            | Proveedor                 |
| `precio_compra` | NUMERIC(10,2) | NOT NULL, ≥0              | Costo de compra           |
| `precio_venta`  | NUMERIC(10,2) | NOT NULL, ≥0              | Precio de venta           |
| `stock`         | INTEGER       | NOT NULL, DEFAULT 0, ≥0   | Stock actual              |

**Stock Bajo**: Productos con `stock <= 5` se consideran stock bajo.

### 4.6 `categoria`

Categorías de productos.

| Columna        | Tipo        | Restricción      |
| -------------- | ----------- | ---------------- |
| `id_categoria` | INTEGER     | PK, SERIAL       |
| `nombre`       | VARCHAR(80) | NOT NULL, UNIQUE |

### 4.7 `proveedor`

Proveedores de mercancía.

| Columna        | Tipo         | Restricción |
| -------------- | ------------ | ----------- |
| `id_proveedor` | INTEGER      | PK, SERIAL  |
| `nombre`       | VARCHAR(120) | NOT NULL    |
| `telefono`     | VARCHAR(30)  | NULLABLE    |
| `email`        | VARCHAR(120) | NULLABLE    |
| `direccion`    | VARCHAR(200) | NULLABLE    |

### 4.8 `factura`

Facturas de venta — tabla central del sistema.

| Columna                  | Tipo          | Restricción                   | Descripción                      |
| ------------------------ | ------------- | ----------------------------- | -------------------------------- |
| `id_factura`             | INTEGER       | PK, SERIAL                    | Número de factura                |
| `fecha`                  | TIMESTAMP     | NOT NULL, DEFAULT now()       | Fecha de emisión                 |
| `id_cliente`             | INTEGER       | FK → cliente                  | Cliente asociado                 |
| `id_usuario`             | INTEGER       | FK → usuario                  | Vendedor                         |
| `id_seccion`             | INTEGER       | FK → seccion                  | Sección de venta                 |
| `subtotal`               | NUMERIC(10,2) | NOT NULL, DEFAULT 0           | Subtotal antes de impuestos      |
| `descuento`              | NUMERIC(10,2) | NOT NULL, DEFAULT 0           | Descuento global                 |
| `impuesto`               | NUMERIC(10,2) | NOT NULL, DEFAULT 0           | IVA (15%)                        |
| `total`                  | NUMERIC(10,2) | NOT NULL, DEFAULT 0           | Total final                      |
| `tipo_cliente_venta`     | VARCHAR(10)   | NOT NULL, DEFAULT 'Habitual'  | 'Habitual' o 'Fugaz'             |
| `nombre_cliente_fugaz`   | VARCHAR(150)  | NULLABLE                      | Nombre para clientes fugaces     |
| `monto_pagado`           | NUMERIC(10,2) | NOT NULL, DEFAULT 0           | Monto ya pagado                  |
| `saldo_pendiente`        | NUMERIC(10,2) | NOT NULL, DEFAULT 0           | Saldo restante                   |
| `porcentaje_pagado`      | NUMERIC(5,2)  | NOT NULL, DEFAULT 0           | % pagado (0-100)                 |
| `estado_pago`            | VARCHAR(30)   | NOT NULL, DEFAULT 'Pendiente' | 'Pendiente', 'Parcial', 'Pagado' |
| `estado_produccion`      | VARCHAR(30)   | NOT NULL, DEFAULT 'Pendiente' | Ver estados abajo                |
| `fecha_orden_produccion` | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP     | Cuando se inició producción      |
| `fecha_entrega_estimada` | DATE          | NULLABLE                      | Fecha estimada de entrega        |
| `fecha_entrega_real`     | DATE          | NULLABLE                      | Fecha real de entrega            |

**Estados de pago**: `Pendiente` → `Parcial` → `Pagado`

**Estados de producción**: `Pendiente` → `En producción` → `Lista para entregar` → `Entregada` | `Cancelada`

**Restricciones CHECK**:

- `estado_pago` IN ('Pendiente', 'Parcial', 'Pagado')
- `estado_produccion` IN ('Pendiente', 'En producción', 'Lista para entregar', 'Entregada', 'Cancelada')
- `monto_pagado >= 0`
- `saldo_pendiente >= 0`
- `porcentaje_pagado BETWEEN 0 AND 100`
- `tipo_cliente_venta` IN ('Habitual', 'Fugaz')

### 4.9 `detallefactura`

Líneas de detalle de cada factura.

| Columna           | Tipo          | Restricción         | Descripción                |
| ----------------- | ------------- | ------------------- | -------------------------- |
| `id_detalle`      | INTEGER       | PK, SERIAL          | Identificador único        |
| `id_factura`      | INTEGER       | FK → factura        | Factura padre              |
| `id_producto`     | INTEGER       | FK → producto       | Producto vendido           |
| `cantidad`        | INTEGER       | NOT NULL, >0        | Cantidad                   |
| `precio_unitario` | NUMERIC(10,2) | NOT NULL, ≥0        | Precio al momento de venta |
| `descuento_linea` | NUMERIC(10,2) | NOT NULL, DEFAULT 0 | Descuento por línea        |
| `total_linea`     | NUMERIC(10,2) | NOT NULL            | Total de la línea          |

### 4.10 `factura_estado_historial`

Auditoría de cambios de estado de facturas.

| Columna                           | Tipo          | Descripción                 |
| --------------------------------- | ------------- | --------------------------- |
| `id_historial`                    | INTEGER       | PK, SERIAL                  |
| `id_factura`                      | INTEGER       | FK → factura                |
| `tipo_evento`                     | VARCHAR(80)   | Tipo de cambio registrado   |
| `estado_pago_anterior`            | VARCHAR(50)   | Estado de pago previo       |
| `estado_pago_nuevo`               | VARCHAR(50)   | Estado de pago nuevo        |
| `estado_produccion_anterior`      | VARCHAR(80)   | Estado de producción previo |
| `estado_produccion_nuevo`         | VARCHAR(80)   | Estado de producción nuevo  |
| `monto_pagado_anterior`           | NUMERIC(12,2) | Monto pagado previo         |
| `monto_pagado_nuevo`              | NUMERIC(12,2) | Monto pagado nuevo          |
| `monto_abonado`                   | NUMERIC(12,2) | Diferencia abonada          |
| `saldo_anterior`                  | NUMERIC(12,2) | Saldo previo                |
| `saldo_nuevo`                     | NUMERIC(12,2) | Saldo nuevo                 |
| `fecha_entrega_estimada_anterior` | DATE          | Fecha estimada previa       |
| `fecha_entrega_estimada_nueva`    | DATE          | Fecha estimada nueva        |
| `comentario`                      | TEXT          | Descripción del cambio      |
| `fecha_evento`                    | TIMESTAMP     | Fecha del evento            |

### 4.11 `compra`

Compras a proveedores.

| Columna        | Tipo          | Restricción              |
| -------------- | ------------- | ------------------------ |
| `id_compra`    | INTEGER       | PK, SERIAL               |
| `fecha`        | TIMESTAMP     | NOT NULL, DEFAULT now()  |
| `id_proveedor` | INTEGER       | FK → proveedor, NOT NULL |
| `id_usuario`   | INTEGER       | FK → usuario, NOT NULL   |
| `total`        | NUMERIC(10,2) | NOT NULL, DEFAULT 0      |

### 4.12 `detallecompra`

Líneas de detalle de cada compra.

| Columna          | Tipo          | Restricción             |
| ---------------- | ------------- | ----------------------- |
| `id_detalle`     | INTEGER       | PK, SERIAL              |
| `id_compra`      | INTEGER       | FK → compra, NOT NULL   |
| `id_producto`    | INTEGER       | FK → producto, NOT NULL |
| `cantidad`       | INTEGER       | NOT NULL, >0            |
| `costo_unitario` | NUMERIC(10,2) | NOT NULL, ≥0            |
| `total_linea`    | NUMERIC(10,2) | NOT NULL                |

### 4.13 `auditoria`

Registro de auditoría de eliminaciones.

| Columna            | Tipo         | Descripción                              |
| ------------------ | ------------ | ---------------------------------------- |
| `id_auditoria`     | INTEGER      | PK, SERIAL                               |
| `usuario`          | VARCHAR(100) | DEFAULT 'Sistema'                        |
| `accion`           | VARCHAR(50)  | 'DELETE', 'RESTAURADO', etc.             |
| `tabla_afectada`   | VARCHAR(100) | Tabla afectada                           |
| `descripcion`      | TEXT         | Descripción                              |
| `fecha_registro`   | TIMESTAMP    | Fecha del registro                       |
| `fecha`            | TIMESTAMP    | Fecha del evento                         |
| `id_usuario`       | INTEGER      | Usuario que realizó la acción            |
| `registro_id`      | TEXT         | ID del registro eliminado                |
| `datos_anteriores` | JSONB        | Snapshot completo del registro eliminado |

---

## 5. Funciones de Base de Datos

### 5.1 Funciones de Login y Autenticación

| Función                 | Parámetros        | Retorna      | Descripción                        |
| ----------------------- | ----------------- | ------------ | ---------------------------------- |
| `obtener_usuario_login` | `p_email VARCHAR` | `TABLE(...)` | Busca usuario por email para login |

### 5.2 Funciones CRUD — Usuarios

| Función                                | Parámetros                                      | Retorna             | Descripción                                    |
| -------------------------------------- | ----------------------------------------------- | ------------------- | ---------------------------------------------- |
| `crear_usuario_sistema`                | nombre, email, password, id_rol, id_seccion     | `BOOLEAN`           | Crea usuario con validaciones                  |
| `actualizar_usuario_sistema`           | id, nombre, email, id_rol, id_seccion, password | `BOOLEAN`           | Actualiza usuario (password opcional)          |
| `eliminar_usuario_sistema`             | id_usuario, id_usuario_actual                   | `TABLE(filas)`      | Elimina (no permite admin ni auto-eliminación) |
| `obtener_usuario_edicion_por_id`       | id_usuario                                      | `TABLE(...)`        | Datos para formulario de edición               |
| `obtener_usuario_configurar_cuenta`    | id_usuario                                      | `TABLE(...)`        | Datos para configuración de cuenta             |
| `actualizar_usuario_configurar_cuenta` | id, nombre, email, password                     | `BOOLEAN`           | Actualiza nombre/email/password propio         |
| `actualizar_password_usuario_login`    | id, password_hash                               | `BOOLEAN`           | Actualiza solo el hash de contraseña           |
| `buscar_usuarios_filtrados`            | busqueda, id_rol, seccion_filtro                | `TABLE(...)`        | Búsqueda con filtros múltiples                 |
| `listar_usuarios_ordenados`            | —                                               | `TABLE(id, nombre)` | Lista simple ordenada                          |
| `listar_usuarios_para_compras`         | —                                               | `TABLE(id, nombre)` | Para formulario de compras                     |

### 5.3 Funciones CRUD — Clientes

| Función                                | Parámetros                                                                | Retorna      | Descripción                     |
| -------------------------------------- | ------------------------------------------------------------------------- | ------------ | ------------------------------- |
| `registrar_cliente_sistema`            | nombres, apellidos, telefono, direccion, identificacion, tipo_cliente     | `BOOLEAN`    | Crea cliente con validaciones   |
| `actualizar_cliente_sistema`           | id, nombres, apellidos, telefono, direccion, identificacion, tipo_cliente | `BOOLEAN`    | Actualiza cliente               |
| `eliminar_cliente_sistema`             | id_cliente                                                                | `BOOLEAN`    | Elimina cliente                 |
| `obtener_cliente_por_id`               | id_cliente                                                                | `TABLE(...)` | Detalle completo                |
| `obtener_cliente_edicion_por_id`       | id_cliente                                                                | `TABLE(...)` | Para formulario de edición      |
| `obtener_cliente_factura_edicion`      | id_cliente                                                                | `TABLE(...)` | Para edición de factura         |
| `obtener_clientes_recientes_dashboard` | limite                                                                    | `TABLE(...)` | Últimos N clientes              |
| `obtener_clientes_reporte`             | fecha_desde, fecha_hasta                                                  | `TABLE(...)` | Reporte de clientes con totales |
| `obtener_resumen_cliente`              | id_cliente                                                                | `TABLE(...)` | Resumen de compras del cliente  |
| `obtener_ultimas_facturas_cliente`     | id_cliente, limite                                                        | `TABLE(...)` | Últimas facturas de un cliente  |
| `buscar_clientes_filtrados`            | busqueda, tipo_cliente                                                    | `TABLE(...)` | Búsqueda multi-palabra          |
| `listar_clientes_habituales`           | —                                                                         | `TABLE(...)` | Todos los clientes para factura |

### 5.4 Funciones CRUD — Productos

| Función                                    | Parámetros                                                 | Retorna              | Descripción                  |
| ------------------------------------------ | ---------------------------------------------------------- | -------------------- | ---------------------------- |
| `registrar_producto_formulario`            | codigo, nombre, desc, imagen, cat, prov, pc, pv, stock     | `TABLE(id_producto)` | Crea producto, retorna ID    |
| `actualizar_producto_edicion`              | id, codigo, nombre, desc, imagen, cat, prov, pc, pv, stock | `BOOLEAN`            | Actualiza producto           |
| `eliminar_producto_sistema`                | id_producto                                                | `TABLE(filas)`       | Elimina producto             |
| `obtener_producto_edicion_por_id`          | id_producto                                                | `TABLE(...)`         | Para formulario de edición   |
| `obtener_producto_imagen`                  | id_producto                                                | `TABLE(...)`         | Detalle con imagen           |
| `obtener_productos_factura_por_ids`        | ids_productos[]                                            | `TABLE(...)`         | Productos por array de IDs   |
| `obtener_productos_mas_vendidos_dashboard` | limite                                                     | `TABLE(...)`         | Top N más vendidos           |
| `obtener_productos_reporte`                | fecha_desde, fecha_hasta                                   | `TABLE(...)`         | Reporte de productos         |
| `obtener_productos_mas_vendidos_reportes`  | fecha_desde, fecha_hasta                                   | `TABLE(...)`         | Top 10 más vendidos          |
| `buscar_productos_catalogo`                | busqueda, id_categoria, disponibilidad                     | `TABLE(...)`         | Catálogo público con filtros |
| `buscar_productos_inventario`              | busqueda, cat, prov, id_prod, stock_bajo                   | `TABLE(...)`         | Inventario administrativo    |
| `listar_productos_para_factura`            | —                                                          | `TABLE(...)`         | Productos para crear factura |
| `listar_categorias_producto`               | —                                                          | `TABLE(...)`         | Categorías para select       |
| `listar_categorias_form_producto`          | —                                                          | `TABLE(...)`         | Categorías para form         |
| `listar_categorias_ordenadas`              | —                                                          | `TABLE(...)`         | Categorías ordenadas         |
| `listar_categorias_catalogo`               | —                                                          | `TABLE(...)`         | Categorías del catálogo      |
| `listar_proveedores_producto`              | —                                                          | `TABLE(...)`         | Proveedores para select      |
| `listar_proveedores_form_producto`         | —                                                          | `TABLE(...)`         | Proveedores para form        |
| `listar_proveedores_ordenados`             | —                                                          | `TABLE(...)`         | Proveedores ordenados        |

### 5.5 Funciones CRUD — Categorías y Proveedores

| Función                           | Parámetros                             | Retorna             | Descripción                  |
| --------------------------------- | -------------------------------------- | ------------------- | ---------------------------- |
| `registrar_categoria`             | nombre                                 | `TABLE(id, nombre)` | Crea categoría, retorna fila |
| `actualizar_categoria`            | id, nombre                             | `TABLE(filas)`      | Actualiza categoría          |
| `eliminar_categoria_sistema`      | id_categoria                           | `BOOLEAN`           | Elimina categoría            |
| `obtener_categoria_por_id`        | id_categoria                           | `TABLE(...)`        | Detalle de categoría         |
| `buscar_categorias`               | busqueda                               | `TABLE(...)`        | Búsqueda por nombre          |
| `crear_proveedor_sistema`         | nombre, telefono, email, direccion     | `BOOLEAN`           | Crea proveedor               |
| `actualizar_proveedor_sistema`    | id, nombre, telefono, email, direccion | `BOOLEAN`           | Actualiza proveedor          |
| `eliminar_proveedor_sistema`      | id_proveedor                           | `BOOLEAN`           | Elimina proveedor            |
| `obtener_proveedor_por_id`        | id_proveedor                           | `TABLE(...)`        | Detalle de proveedor         |
| `buscar_proveedores_filtrados`    | busqueda                               | `TABLE(...)`        | Búsqueda multi-campo         |
| `listar_proveedores_para_compras` | —                                      | `TABLE(...)`        | Para formulario de compras   |

### 5.6 Funciones de Facturación

| Función                                 | Parámetros                                                                                      | Retorna       | Descripción                                         |
| --------------------------------------- | ----------------------------------------------------------------------------------------------- | ------------- | --------------------------------------------------- |
| `registrar_factura_sistema`             | id_cliente, id_usuario, id_seccion, subtotal, desc, imp, total, tipo, nombre_fugaz, items JSONB | `INTEGER`     | Crea factura + detalles + descuenta stock           |
| `editar_factura_sistema`                | id, fecha, cliente, usuario, seccion, tipo, nombre_fugaz, desc, iva, items JSONB                | — (PROCEDURE) | Restaura stock → reemplaza detalles → recalcula     |
| `eliminar_factura_sistema`              | id_factura                                                                                      | `BOOLEAN`     | Devuelve stock → elimina detalles → elimina factura |
| `obtener_factura_para_impresion`        | id_factura                                                                                      | `TABLE(...)`  | Datos completos para impresión                      |
| `obtener_factura_detalle_por_id`        | id_factura                                                                                      | `TABLE(...)`  | Detalle para vista                                  |
| `obtener_detalles_factura_edicion`      | id_factura                                                                                      | `TABLE(...)`  | Detalles para edición                               |
| `obtener_lineas_detalle_factura`        | id_factura                                                                                      | `TABLE(...)`  | Líneas de detalle                                   |
| `obtener_lineas_factura_para_impresion` | id_factura                                                                                      | `TABLE(...)`  | Líneas para impresión                               |
| `obtener_facturas_recientes_dashboard`  | limite                                                                                          | `TABLE(...)`  | Últimas facturas                                    |
| `calcular_subtotal_factura`             | id_factura                                                                                      | `NUMERIC`     | Suma de detalles                                    |
| `actualizar_totales_factura`            | id, descuento, porcentaje_impuesto                                                              | — (PROCEDURE) | Recalcula subtotal/impuesto/total                   |
| `agregar_detalle_factura`               | id_factura, id_producto, cantidad, descuento                                                    | — (PROCEDURE) | Agrega línea + descuenta stock                      |
| `validar_factura_existe`                | id_factura                                                                                      | `BOOLEAN`     | Verifica existencia                                 |
| `buscar_facturas_filtradas`             | id_rol, busqueda, id_seccion, id_usuario, desde, hasta                                          | `TABLE(...)`  | Búsqueda con filtros                                |
| `obtener_id_cliente_fugaz`              | —                                                                                               | `INTEGER`     | ID del cliente fugaz                                |

### 5.7 Funciones de Compras

| Función                      | Parámetros                                       | Retorna       | Descripción                  |
| ---------------------------- | ------------------------------------------------ | ------------- | ---------------------------- |
| `obtener_compra_por_id`      | id_compra                                        | `TABLE(...)`  | Detalle de compra            |
| `obtener_detalles_compra`    | id_compra                                        | `TABLE(...)`  | Líneas de detalle            |
| `calcular_total_compra`      | id_compra                                        | `NUMERIC`     | Suma de detalles             |
| `actualizar_total_compra`    | id_compra                                        | — (PROCEDURE) | Recalcula total              |
| `agregar_detalle_compra`     | id_compra, id_producto, cantidad, costo_unitario | — (PROCEDURE) | Agrega línea + aumenta stock |
| `buscar_compras_filtradas`   | busqueda, prov, usuario, desde, hasta            | `TABLE(...)`  | Búsqueda con filtros         |
| `listar_secciones_ordenadas` | —                                                | `TABLE(...)`  | Secciones para select        |
| `listar_roles_ordenados`     | —                                                | `TABLE(...)`  | Roles para select            |

### 5.8 Funciones de Reportes y Dashboard

| Función                                        | Parámetros                       | Retorna                 | Descripción                                             |
| ---------------------------------------------- | -------------------------------- | ----------------------- | ------------------------------------------------------- |
| `obtener_metricas_dashboard`                   | —                                | `TABLE(...)`            | KPIs: clientes, productos, facturas, ventas, stock bajo |
| `obtener_ventas_dashboard`                     | dias                             | `TABLE(dia, total_dia)` | Ventas diarias últimos N días                           |
| `obtener_ventas_detalladas_reportes`           | desde, hasta                     | `TABLE(...)`            | Detalle de ventas por factura                           |
| `obtener_ventas_por_dia_reportes`              | desde, hasta                     | `TABLE(...)`            | Ventas agrupadas por día                                |
| `obtener_total_ventas_reportes`                | desde, hasta                     | `NUMERIC`               | Suma total de ventas                                    |
| `obtener_total_facturas_reportes`              | desde, hasta                     | `INTEGER`               | Cantidad de facturas                                    |
| `obtener_total_clientes_reportes`              | —                                | `INTEGER`               | Cantidad total de clientes                              |
| `obtener_stock_bajo_reportes`                  | —                                | `INTEGER`               | Productos con stock ≤ 5                                 |
| `obtener_ultimos_productos_vendidos_dashboard` | limite                           | `TABLE(...)`            | Últimas ventas                                          |
| `buscar_usuarios_filtrados`                    | busqueda, id_rol, seccion_filtro | `TABLE(...)`            | Búsqueda de usuarios                                    |
| `obtener_seccion_por_id`                       | id_seccion                       | `TABLE(...)`            | Sección por ID                                          |
| `obtener_seccion_por_nombre`                   | nombre                           | `TABLE(...)`            | Sección por nombre                                      |

---

## 6. Triggers

### 6.1 `registrar_historial_estado_factura`

- **Tabla**: `factura`
- **Tipo**: AFTER INSERT OR UPDATE
- **Función**: `registrar_historial_estado_factura()`
- **Descripción**: Registra automáticamente en `factura_estado_historial` cada cambio de estado de pago, producción, monto pagado, saldo o fecha de entrega estimada.

**Eventos registrados**:

- INSERT: "Factura creada"
- UPDATE con cambio de monto: "Pago actualizado"
- UPDATE con cambio de producción: "Estado de producción actualizado"
- UPDATE con Cancelada: "Factura cancelada" (con comentario sobre si fue antes/después de fecha estimada)

### 6.2 `trg_auditar_delete_categoria`

- **Tabla**: `categoria`
- **Tipo**: BEFORE DELETE
- **Función**: `fn_auditar_delete_generico()`
- **Descripción**: Registra en `auditoria` el registro eliminado con snapshot JSONB completo.

### 6.3 `trg_auditar_delete_cliente`

- **Tabla**: `cliente`
- **Tipo**: BEFORE DELETE
- **Función**: `fn_auditar_delete_generico()`

### 6.4 `trg_auditar_delete_producto`

- **Tabla**: `producto`
- **Tipo**: BEFORE DELETE
- **Función**: `fn_auditar_delete_generico()`

### 6.5 `trg_auditar_delete_proveedor`

- **Tabla**: `proveedor`
- **Tipo**: BEFORE DELETE
- **Función**: `fn_auditar_delete_generico()`

---

## 7. Procedimientos Almacenados

| Procedimiento                | Parámetros                                                                | Descripción                                              |
| ---------------------------- | ------------------------------------------------------------------------- | -------------------------------------------------------- |
| `registrar_cliente`          | nombres, apellidos, telefono, direccion, identificacion, tipo_cliente     | Inserta cliente                                          |
| `registrar_producto`         | codigo, nombre, desc, imagen, cat, prov, pc, pv, stock                    | Inserta producto                                         |
| `registrar_auditoria`        | usuario, accion, tabla, descripcion                                       | Inserta registro de auditoría                            |
| `aumentar_stock_producto`    | id_producto, cantidad                                                     | Incrementa stock                                         |
| `disminuir_stock_producto`   | id_producto, cantidad                                                     | Decrementa stock (con validación)                        |
| `agregar_detalle_factura`    | id_factura, id_producto, cantidad, descuento                              | Agrega línea + descuenta stock + recalcula               |
| `agregar_detalle_compra`     | id_compra, id_producto, cantidad, costo_unitario                          | Agrega línea + aumenta stock + recalcula                 |
| `actualizar_totales_factura` | id_factura, descuento, porcentaje_impuesto                                | Recalcula subtotal/impuesto/total                        |
| `actualizar_total_compra`    | id_compra                                                                 | Recalcula total de compra                                |
| `editar_factura_sistema`     | id, fecha, cliente, usuario, seccion, tipo, fugaz, desc, iva, items JSONB | Edición completa: restaura stock → reemplaza → recalcula |

---

## 8. Flujo de Facturación

### 8.1 Crear Factura

```bash
1. Seleccionar cliente (Habitual o Fugaz)
2. Seleccionar sección (Kitsune o Panda Estampados)
3. Agregar productos con cantidades
4. Sistema valida stock suficiente
5. Sistema calcula: subtotal, descuento, IVA (15%), total
6. Se guarda la factura con estado_pago='Pendiente', estado_produccion='Pendiente'
7. Trigger registra "Factura creada" en historial
8. Stock se descuenta automáticamente
```

### 8.2 Editar Factura

```bash
1. Restaurar stock de TODOS los detalles viejos (en lote)
2. Eliminar detalles viejos
3. Actualizar datos de la factura
4. Insertar nuevos detalles (con validación de stock)
5. Descuentar stock de nuevos productos
6. Recalcular totales
7. Trigger registra cambios en historial
```

### 8.3 Eliminar Factura

```bash
1. Devolver stock de todos los productos vendidos
2. Eliminar detalles de la factura
3. Eliminar factura principal
4. (Las eliminaciones de categoría/cliente/producto/proveedor quedan registradas en auditoría)
```

### 8.4 Flujo de Pagos

```bash
Monto_pagado se actualiza → Sistema recalcula:
- saldo_pendiente = total - monto_pagado
- porcentaje_pagado = (monto_pagado / total) * 100
- estado_pago:
    monto_pagado = 0        → 'Pendiente'
    monto_pagado < total     → 'Parcial'
    monto_pagado >= total    → 'Pagado'
```

---

## 9. Seguridad

### 9.1 Autenticación

- **Contraseñas**: Bcrypt con cost factor 12
- **Sesiones**: PHP nativas con `session_start()`
- **Login**: `obtener_usuario_login()` busca por email, `password_verify()` en PHP

### 9.2 Autorización

| Rol               | Permisos                                                                                                |
| ----------------- | ------------------------------------------------------------------------------------------------------- |
| Administrador (1) | Acceso total: CRUD de todos los módulos, reportes, configuración, backups                               |
| Supervisor (2)    | Facturación, clientes, productos, reportes. Solo ve facturas de sección Kitsune con clientes Detallista |
| Facturador (3)    | Facturación y clientes. Solo ve facturas de sección Kitsune con clientes Detallista                     |

### 9.3 Protección CSRF

- Tokens CSRF en **todas** las 37 formularios POST
- `csrfRequire()` en `auth_guard.php` para handlers protegidos
- `csrf_token.php` como endpoint helper para extracción de tokens

### 9.4 Protección SQL

- **Todas** las queries usan prepared statements (PDO)
- **Todas** las funciones/procedimientos validan parámetros con `RAISE EXCEPTION`
- Nunca se concatena input del usuario en SQL

### 9.5 Protección de Archivos

- `realpath()` y `basename()` para prevenir path traversal en uploads
- Validación de tipo MIME: solo `image/*`
- Tamaño máximo: 5MB

### 9.6 Protección de Rutas DELETE

- Todas las eliminaciones usan POST (no GET)
- Tokens CSRF obligatorios
- Validación de permisos por rol

---

## 10. Estrategia de Backups

### 10.1 Tipos de Backup

| Script           | Tipo         | Comando                                                                      | Almacén         |
| ---------------- | ------------ | ---------------------------------------------------------------------------- | --------------- |
| `backup_full.sh` | Full         | `pg_dump --clean --if-exists`                                                | `backups/full/` |
| `backup_diff.sh` | Diferencial  | Tablas críticas: factura, detallefactura, cliente, producto, compra, usuario | `backups/diff/` |
| `backup_logs.sh` | Logs         | `tar -czf` de logs PostgreSQL                                                | `backups/logs/` |
| Mantenimiento    | Optimización | `VACUUM FULL + REINDEX + ANALYZE`                                            | —               |

### 10.2 Backup Automático

- **Container**: `pandas_backup_scheduler` ejecuta `crond -f` (loop cada 60 segundos)
- **Configuración**: `storage/system/backup_schedule.json`
- **Metadata**: `storage/system/backup_metadata.json`
- **Backups manuales**: Se guardan en `backups/manual/`

### 10.3 WAL Archiving

```yaml
command: >
  postgres
  -c wal_level=replica
  -c archive_mode=on
  -c archive_command='test ! -f /wal_archive/%f && cp %p /wal_archive/%f'
```

Permite point-in-time recovery combinando backups con archivos WAL.

---

## 11. Notificaciones

### 11.1 Tipos de Notificación

| Tipo                  | Color    | Descripción                    |
| --------------------- | -------- | ------------------------------ |
| `factura_creada`      | azul     | Nueva factura registrada       |
| `pago_recibido`       | verde    | Pago parcial recibido          |
| `factura_pagada`      | verde    | Factura completamente pagada   |
| `produccion_cambiada` | naranja  | Cambio en estado de producción |
| `factura_cancelada`   | rojo     | Factura cancelada              |
| `cliente_creado`      | azul     | Nuevo cliente registrado       |
| `cliente eliminado`   | rojo     | Cliente eliminado              |
| `producto_creado`     | azul     | Nuevo producto registrado      |
| `producto eliminado`  | rojo     | Producto eliminado             |
| `stock_bajo`          | amarillo | Stock de producto ≤ 5          |
| `compra_registrada`   | azul     | Nueva compra a proveedor       |
| `backup_manual`       | verde    | Backup manual completado       |
| `backup_automatico`   | gris     | Backup automático completado   |
| `backup_restaurado`   | naranja  | Base de datos restaurada       |
| `mantenimiento_bd`    | morado   | Mantenimiento (VACUUM/REINDEX) |
| `usuario_creado`      | azul     | Nuevo usuario registrado       |
| `usuario eliminado`   | rojo     | Usuario eliminado              |

### 11.2 Almacenamiento

- **Archivo**: `storage/system/notificaciones.json`
- **CRUD**: `NotificacionRepository` con file locking (`LOCK_EX`)
- **Auto-creación**: `asegurarArchivo()` crea el archivo si no existe

### 11.3 Permisos

| Rol        | Ver historial | Marcar leida | Marcar todas | Eliminar          | Limpiar todo |
| ---------- | ------------- | ------------ | ------------ | ----------------- | ------------ |
| Admin      | Si            | Si           | Si           | Si (cualquiera)   | Si           |
| Supervisor | Si            | Si           | Si           | Si (solo propias) | No           |
| Facturador | Si            | Si           | Si           | Si (solo propias) | No           |

### 11.4 Endpoints

| Endpoint                    | Método | Función                                       |
| --------------------------- | ------ | --------------------------------------------- |
| `notificaciones.php`        | GET    | Página de historial                           |
| `notificaciones_api.php`    | POST   | marcar_leida, marcar_todas, eliminar, limpiar |
| `notificacion_contador.php` | GET    | Contador de no leídas (AJAX)                  |

---

## 12. Exportación Excel

### 12.1 Archivos Generados

| Archivo                     | Datos                                                |
| --------------------------- | ---------------------------------------------------- |
| `ventas_YYYY-MM-DD.xlsx`    | Detalle de facturas con cliente, vendedor, sección   |
| `productos_YYYY-MM-DD.xlsx` | Inventario con categoría, proveedor, stock, precios  |
| `clientes_YYYY-MM-DD.xlsx`  | Directorio de clientes con tipo, teléfono, dirección |
| `compras_YYYY-MM-DD.xlsx`   | Historial de compras con proveedor, total            |
| `facturas_YYYY-MM-DD.xlsx`  | Resumen de facturas con estados                      |
| `completo_YYYY-MM-DD.xlsx`  | Todos los datos en hojas separadas                   |

### 12.2 Implementación

- **Librería**: PhpSpreadsheet 5.8
- **Estilo**: Encabezados en azul, bordes, formato de moneda
- **Endpoint**: `export.php` (usa `ReportesRepository`)
- **Permisos**: Solo admin puede exportar

---

## 13. Estructura del Proyecto (PSR-4)

```bash
src/
├── Repository/
│   ├── NotificacionRepository.php    # CRUD JSON notificaciones
│   ├── ReportesRepository.php        # Queries de reportes
│   ├── ConfiguracionRepository.php   # Config de cuenta
│   ├── FacturaRepository.php         # CRUD facturas
│   ├── ClienteRepository.php         # CRUD clientes
│   ├── ProductoRepository.php        # CRUD productos
│   ├── CompraRepository.php          # CRUD compras
│   ├── CategoriaRepository.php       # CRUD categorías
│   ├── ProveedorRepository.php       # CRUD proveedores
│   ├── UsuarioRepository.php         # CRUD usuarios
│   ├── AuditoriaRepository.php       # Queries de auditoría
│   ├── SeccionRepository.php         # Secciones
│   └── RolRepository.php             # Roles
├── Service/
│   ├── NotificacionService.php       # Lógica de notificaciones
│   ├── FacturaService.php            # Orquestación de facturas
│   ├── FacturaValidationService.php  # Validación
│   ├── FacturaCalculationService.php # Cálculos
│   ├── ExportService.php             # Generación Excel
│   ├── BackupService.php             # Gestión de backups
│   └── ...
```

**Autoloading**: PSR-4 con `App\` namespace, compatible con wrappers `class_alias` en `repositories/` y `services/`.

---

## 14. Pruebas

### 14.1 Suite de Pruebas CRUD (`test_crud.sh`)

- **Total**: 129 tests
- **Cobertura**: Todas las rutas HTTP de cada módulo
- **Verificación**: Código de respuesta HTTP (200, 302)

### 14.2 Suite de Pruebas de Integración (`test_crud_integration.sh`)

- **Total**: 32 tests
- **Ciclo completo**: CREATE → READ → UPDATE → DELETE
- **Verificación**: Consultas SQL directas a la base de datos
- **Módulos**: Categorías, proveedores, clientes, trabajadores, productos, facturas, exportación

### 14.3 Suite de Pruebas de Notificaciones (`test_notificaciones.sh`)

- **Total**: 32 tests
- **Cobertura**: Páginas, AJAX, API, permisos, badge del sidebar

---

## 15. Diagramas Generados

| Archivo                 | Herramientia | Formato           | Contenido                                |
| ----------------------- | ------------ | ----------------- | ---------------------------------------- |
| `sql/ER_CHEN.md`        | Manual       | Markdown          | Diagrama ER en notación Chen             |
| `sql/UML_CLASES.md`     | PlantUML     | Markdown + código | Diagrama UML de clases                   |
| `sql/uml_clases.puml`   | PlantUML     | .puml             | Código PlantUML listo para renderizar    |
| `sql/erd.mmd`           | Mermaid      | .mmd              | Diagrama ER para Mermaid Live Editor     |
| `sql/erd.dbml`          | DBML         | .dbml             | Para dbdiagram.io                        |
| `sql/schema_drawio.sql` | draw.io      | SQL               | SQL adaptado para importación en draw.io |
| `sql/schema_full.sql`   | pg_dump      | SQL               | Schema completo (13 tablas)              |

---

## 16. Credenciales y Acceso

| Servicio       | URL                     | Credenciales               |
| -------------- | ----------------------- | -------------------------- |
| Aplicación Web | `http://localhost:8080` | Ver abajo                  |
| pgAdmin        | `http://localhost:5050` | `admin@admin.com> / admin` |
| PostgreSQL     | `localhost:5432`        | postgres / root            |

**Usuarios del sistema**:

| Email                                 | Contraseña | Rol           |
| ------------------------------------- | ---------- | ------------- |
| `leonel.messi@admin.pandakitsune.com` | password0  | Administrador |

---

## 17. Archivos Clave de Referencia

| Archivo                            | Descripción                              |
| ---------------------------------- | ---------------------------------------- |
| `docker/docker-compose.yml`        | Orquestación Docker                      |
| `docker/Dockerfile`                | Imagen PHP 8.3 + Apache                  |
| `composer.json`                    | Dependencias y autoloading PSR-4         |
| `bootstrap.php`                    | Inicialización de autoloader + dotenv    |
| `.env` / `.env.example`            | Variables de entorno                     |
| `config/database.php`              | Conexión PDO a PostgreSQL                |
| `config/constants.php`             | Constantes del sistema (IVA, tipos)      |
| `helpers/csrf.php`                 | Funciones CSRF                           |
| `helpers/format.php`               | Formateo de números/fechas               |
| `helpers/notificaciones.php`       | Funciones de notificación                |
| `includes/auth.php`                | Login/logout                             |
| `includes/auth_guard.php`          | Guard de autenticación + CSRF            |
| `sql/01_data.sql`                  | Dump completo: schema + funciones + seed |
| `sql/schema_full.sql`              | Solo las 13 tablas                       |
| `sql/README.md`                    | Documentación de la BD                   |
| `scripts/setup.sh`                 | Inicialización del proyecto              |
| `scripts/test_crud.sh`             | 129 tests CRUD                           |
| `scripts/test_crud_integration.sh` | 32 tests integración                     |
| `scripts/test_notificaciones.sh`   | 32 tests notificaciones                  |
