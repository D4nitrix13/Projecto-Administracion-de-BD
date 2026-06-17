# SQL — Base de Datos

Directorio con el schema completo, procedimientos almacenados y datos iniciales del sistema.

## Archivos

| Archivo                  | Contenido                                                                                | Lineas |
| ------------------------ | ---------------------------------------------------------------------------------------- | ------ |
| `01_data.sql`            | Schema DDL completo: tablas, funciones, procedimientos, triggers, indices, datos semilla | ~5,770 |
| `02_procedures.sql`      | Versiones alternativas/addicionales de funciones (CREATE OR REPLACE)                     | ~3,040 |
| `data_default.sql`       | Script de generacion de datos de prueba (120 productos, 40 compras, 80 facturas)         | ~366   |
| `00_create_database.sql` | Creacion de la base de datos (vacio — se crea via Docker)                                | 0      |
| `db.php`                 | Conexion PDO a PostgreSQL                                                                | 39     |

## Credenciales de Prueba

### Convencion de contrasenas

| Patron                       | Contrasena   |
| ---------------------------- | ------------ |
| Admin General (Leonel Messi) | `password0`  |
| Daniel Perez                 | `password1`  |
| ...                          | `passwordN`  |
| Nidia Solis (ultimo usuario) | `password29` |

### Administradores (rol 1)

| Usuario        | Email                                   | Contrasena  |
| -------------- | --------------------------------------- | ----------- |
| Leonel Messi   | `leonel.messi@admin.pandakitsune.com`   | `password0` |
| Laura Castillo | `laura.castillo@admin.pandakitsune.com` | `password0` |
| Oscar Mejia    | `oscar.mejia@admin.pandakitsune.com`    | `password0` |

### Supervisores (rol 2, seccion Kitsune)

| Usuario           | Email                           | Contrasena  |
| ----------------- | ------------------------------- | ----------- |
| Daniel Perez      | `daniel.perez@kitsune.com`      | `password1` |
| Jeremy Perez      | `jeremy.perez@kitsune.com`      | `password2` |
| Jhossep Ramos     | `jhossep.ramos@kitsune.com`     | `password3` |
| Diego Torres      | `diego.torres@kitsune.com`      | `password4` |
| Carlos Nunez      | `carlos.nunez@kitsune.com`      | `password5` |
| Monica Larios     | `monica.larios@kitsune.com`     | `password6` |
| Esteban Rodriguez | `esteban.rodriguez@kitsune.com` | `password7` |
| Eduardo Molina    | `eduardo.molina@kitsune.com`    | `password8` |

### Facturadores (rol 3, seccion Kitsune)

| Usuario           | Email                           | Contrasena   |
| ----------------- | ------------------------------- | ------------ |
| Andy Sanchez      | `andy.sanchez@panda.com`        | `password9`  |
| Sofia Gomez       | `sofia.gomez@kitsune.com`       | `password10` |
| Luis Torres       | `luis.torres@panda.com`         | `password11` |
| Carla Bermudez    | `carla.bermudez@kitsune.com`    | `password12` |
| Karla Medina      | `karla.medina@panda.com`        | `password13` |
| Wilmer Ruiz       | `wilmer.ruiz@kitsune.com`       | `password14` |
| Miguel Hernandez  | `miguel.hernandez@panda.com`    | `password15` |
| Paola Lopez       | `paola.lopez@panda.com`         | `password16` |
| Kevin Castillo    | `kevin.castillo@panda.com`      | `password17` |
| Maria Fernandez   | `maria.fernandez@kitsune.com`   | `password18` |
| Josefina Rivas    | `josefina.rivas@kitsune.com`    | `password19` |
| Roberto Gutierrez | `roberto.gutierrez@kitsune.com` | `password20` |
| Lucia Herrera     | `lucia.herrera@kitsune.com`     | `password21` |
| Brandon Morales   | `brandon.morales@kitsune.com`   | `password22` |
| Andrea Vega       | `andrea.vega@panda.com`         | `password23` |
| Sergio Mairena    | `sergio.mairena@panda.com`      | `password24` |
| Julia Campos      | `julia.campos@panda.com`        | `password25` |
| Carmen Rojas      | `carmen.rojas@panda.com`        | `password26` |
| Nidia Solis       | `nidia.solis@kitsune.com`       | `password27` |

## Schema Resumido

```
rol (1) ──< (N) usuario (N) >── (1) seccion
                                  │
cliente (1) ──< (N) factura ──> (1) seccion
                                 ├──> (1) usuario
                                 ├──< (N) detallefactura >── (1) producto
                                 └──< (N) factura_estado_historial

proveedor (1) ──< (N) compra ──> (1) usuario
                        └──< (N) detallecompra >── (1) producto

categoria (1) ──< (N) producto (N) >── (1) proveedor

usuario (1) ──< (N) auditoria
```

### Tablas (13)

| Tabla                      | Proposito                                         |
| -------------------------- | ------------------------------------------------- |
| `rol`                      | Roles del sistema (Admin, Supervisor, Facturador) |
| `seccion`                  | Secciones (Panda Estampados, Kitsune)             |
| `usuario`                  | Usuarios del sistema                              |
| `cliente`                  | Clientes (Habitual o Detallista)                  |
| `categoria`                | Categorias de productos                           |
| `proveedor`                | Proveedores                                       |
| `producto`                 | Productos con precios y stock                     |
| `factura`                  | Facturas/ventas con estados                       |
| `detallefactura`           | Lineas de detalle de facturas                     |
| `factura_estado_historial` | Historial de cambios de estado                    |
| `compra`                   | Compras a proveedores                             |
| `detallecompra`            | Lineas de detalle de compras                      |
| `auditoria`                | Registro de eliminaciones (soft delete)           |

### Funciones Almacenadas (62)

| Categoria         | Cantidad | Ejemplos                                                                       |
| ----------------- | -------- | ------------------------------------------------------------------------------ |
| CRUD Clientes     | 6        | `registrar_cliente_sistema()`, `obtener_cliente_por_id()`                      |
| CRUD Productos    | 5        | `registrar_producto_formulario()`, `buscar_productos_inventario()`             |
| CRUD Proveedores  | 5        | `crear_proveedor_sistema()`, `buscar_proveedores_filtrados()`                  |
| CRUD Categorias   | 4        | `registrar_categoria()`, `buscar_categorias()`                                 |
| CRUD Usuarios     | 6        | `crear_usuario_sistema()`, `buscar_usuarios_filtrados()`                       |
| CRUD Facturas     | 7        | `registrar_factura_sistema()`, `eliminar_factura_sistema()`                    |
| CRUD Compras      | 2        | `obtener_compra_por_id()`, `obtener_detalles_compra()`                         |
| Dashboard         | 6        | `obtener_metricas_dashboard()`, `obtener_ventas_dashboard()`                   |
| Reportes          | 8        | `obtener_total_ventas_reportes()`, `obtener_productos_mas_vendidos_reportes()` |
| Busqueda/Filtros  | 8        | `buscar_facturas_filtradas()`, `buscar_clientes_filtrados()`                   |
| Listas/Referencia | 11       | `listar_categorias_ordenadas()`, `listar_roles_ordenados()`                    |
| Clientes          | 3        | `obtener_resumen_cliente()`, `obtener_ultimas_facturas_cliente()`              |
| Detalle Factura   | 4        | `obtener_lineas_detalle_factura()`, `obtener_productos_factura_por_ids()`      |
| Calculos          | 2        | `calcular_subtotal_factura()`, `calcular_total_compra()`                       |
| Autenticacion     | 2        | `obtener_usuario_login()`, `actualizar_password_usuario_login()`               |
| Secciones         | 2        | `obtener_seccion_por_id()`, `obtener_seccion_por_nombre()`                     |

### Procedimientos (10)

| Procedimiento                | Proposito                                                         |
| ---------------------------- | ----------------------------------------------------------------- |
| `registrar_producto`         | INSERT producto (version simplificada)                            |
| `registrar_cliente`          | INSERT cliente (version simplificada)                             |
| `registrar_auditoria`        | INSERT registro de auditoria manual                               |
| `agregar_detalle_compra`     | INSERT linea de compra + actualizar stock + total                 |
| `agregar_detalle_factura`    | INSERT linea de factura + descontar stock + totales               |
| `aumentar_stock_producto`    | Incrementar stock                                                 |
| `disminuir_stock_producto`   | Decrementar stock                                                 |
| `editar_factura_sistema`     | Edicion completa: restaurar stock, reconstruir lineas, recalcular |
| `actualizar_totales_factura` | Recalcular subtotal/impuesto/total                                |
| `actualizar_total_compra`    | Recalcular total de compra                                        |

### Triggers (5)

| Trigger                        | Tabla     | Evento        | Funcion                                |
| ------------------------------ | --------- | ------------- | -------------------------------------- |
| `trg_auditar_delete_categoria` | categoria | DELETE        | `fn_auditar_delete_generico()`         |
| `trg_auditar_delete_cliente`   | cliente   | DELETE        | `fn_auditar_delete_generico()`         |
| `trg_auditar_delete_producto`  | producto  | DELETE        | `fn_auditar_delete_generico()`         |
| `trg_auditar_delete_proveedor` | proveedor | DELETE        | `fn_auditar_delete_generico()`         |
| `trg_factura_estado_historial` | factura   | INSERT/UPDATE | `registrar_historial_estado_factura()` |
