# Modelo Entidad-Relación — Notación Chen

# Panda Estampados y Kitsune

> **Herramienta recomendada:** [dbdiagram.io](https://dbdiagram.io)
> **Alternativa:** [draw.io](https://app.diagrams.net) → Nuevo diagrama → "Entity Relation"

---

## Cómo dibujarlo en draw.io

1. Abrí [app.diagrams.net](https://app.diagrams.net)
2. Click "Create New Diagram" → vacío
3. En la barra lateral buscá "Entity Relation"
4. Arrastrá rectángulos (entidades), rombos (relaciones) y óvalos (atributos)
5. Usá líneas con cardinalidades: `(1,1)`, `(1,n)`, `(0,n)`, etc.

---

## Entidades (Rectángulos Amarillos)

### ROL

```
┌──────────────────────┐
│         ROL          │
├──────────────────────┤
│ PK  id_rol  integer  │
│     nombre  varchar  │
└──────────────────────┘
```

### SECCIÓN

```
┌──────────────────────┐
│        SECCIÓN       │
├──────────────────────┤
│ PK  id_seccion integer│
│     nombre  varchar   │
└──────────────────────┘
```

### CATEGORÍA

```
┌──────────────────────┐
│       CATEGORÍA      │
├──────────────────────┤
│ PK  id_categoria integer│
│     nombre  varchar   │
└──────────────────────┘
```

### PROVEEDOR

```
┌──────────────────────────────┐
│          PROVEEDOR           │
├──────────────────────────────┤
│ PK  id_proveedor  integer    │
│     nombre        varchar    │
│     telefono      varchar    │
│     email         varchar    │
│     direccion     varchar    │
└──────────────────────────────┘
```

### CLIENTE

```
┌──────────────────────────────┐
│           CLIENTE            │
├──────────────────────────────┤
│ PK  id_cliente    integer    │
│     nombres       varchar    │
│     apellidos     varchar    │
│     telefono      varchar    │
│     direccion     varchar    │
│     identificacion varchar   │
│     tipo_cliente  varchar    │
│     fecha_registro date      │
└──────────────────────────────┘
```

### USUARIO

```
┌──────────────────────────────┐
│           USUARIO            │
├──────────────────────────────┤
│ PK  id_usuario    integer    │
│     nombre        varchar    │
│     email         varchar    │
│     password      text       │
│ FK  id_rol        integer    │
│ FK  id_seccion    integer    │
└──────────────────────────────┘
```

### PRODUCTO

```
┌──────────────────────────────┐
│          PRODUCTO            │
├──────────────────────────────┤
│ PK  id_producto   integer    │
│     codigo        varchar    │
│     nombre        varchar    │
│     descripcion   text       │
│     imagen        varchar    │
│ FK  id_categoria  integer    │
│ FK  id_proveedor  integer    │
│     precio_compra numeric    │
│     precio_venta  numeric    │
│     stock         integer    │
└──────────────────────────────┘
```

### COMPRA

```
┌──────────────────────────────┐
│           COMPRA             │
├──────────────────────────────┤
│ PK  id_compra     integer    │
│     fecha         timestamp  │
│ FK  id_proveedor  integer    │
│ FK  id_usuario    integer    │
│     total         numeric    │
└──────────────────────────────┘
```

### DETALLE_COMPRA

```
┌──────────────────────────────┐
│        DETALLE_COMPRA        │
├──────────────────────────────┤
│ PK  id_detalle    integer    │
│ FK  id_compra     integer    │
│ FK  id_producto   integer    │
│     cantidad      integer    │
│     costo_unitario numeric   │
│     total_linea   numeric    │
└──────────────────────────────┘
```

### FACTURA

```
┌──────────────────────────────┐
│           FACTURA            │
├──────────────────────────────┤
│ PK  id_factura    integer    │
│     fecha         timestamp  │
│ FK  id_cliente    integer    │
│ FK  id_usuario    integer    │
│ FK  id_seccion    integer    │
│     subtotal      numeric    │
│     descuento     numeric    │
│     impuesto      numeric    │
│     total         numeric    │
│     tipo_cliente_venta varchar│
│     nombre_cliente_fugaz varchar│
│     monto_pagado  numeric    │
│     saldo_pendiente numeric  │
│     porcentaje_pagado numeric│
│     estado_pago   varchar    │
│     estado_produccion varchar│
│     fecha_entrega_estimada date│
│     fecha_entrega_real date  │
└──────────────────────────────┘
```

### DETALLE_FACTURA

```
┌──────────────────────────────┐
│       DETALLE_FACTURA        │
├──────────────────────────────┤
│ PK  id_detalle    integer    │
│ FK  id_factura    integer    │
│ FK  id_producto   integer    │
│     cantidad      integer    │
│     precio_unitario numeric  │
│     descuento_linea numeric  │
│     total_linea   numeric    │
└──────────────────────────────┘
```

### FACTURA_ESTADO_HISTORIAL

```
┌──────────────────────────────┐
│    FACTURA_ESTADO_HISTORIAL   │
├──────────────────────────────┤
│ PK  id_historial  integer    │
│ FK  id_factura    integer    │
│     tipo_evento   varchar    │
│     estado_pago_anterior varchar│
│     estado_pago_nuevo varchar│
│     estado_produccion_anterior varchar│
│     estado_produccion_nuevo varchar│
│     monto_pagado_anterior numeric│
│     monto_pagado_nuevo numeric│
│     monto_abonado numeric    │
│     saldo_anterior numeric   │
│     saldo_nuevo  numeric     │
│     fecha_entrega_estimada_anterior date│
│     fecha_entrega_estimada_nueva date│
│     comentario   text        │
│     fecha_evento timestamp   │
└──────────────────────────────┘
```

### AUDITORÍA

```
┌──────────────────────────────┐
│          AUDITORÍA           │
├──────────────────────────────┤
│ PK  id_auditoria integer     │
│     usuario      varchar     │
│     accion       varchar     │
│     tabla_afectada varchar   │
│     descripcion  text        │
│     fecha        timestamp   │
│ FK  id_usuario   integer     │
│     registro_id  text        │
│     datos_anteriores jsonb   │
└──────────────────────────────┘
```

---

## Relaciones (Rombos/Diamantes)

Cada relación se dibuja como un **rombo** entre dos entidades con cardinalidades en las líneas.

### 1. ROL — USUARIO

```
┌──────┐       ┌───────────┐       ┌──────┐
│ ROL  │──(1,1)◇ PERTENECE ◇(0,n)──│USUARIO│
└──────┘       └───────────┘       └──────┘
```

- Un ROL tiene **muchos** usuarios
- Un USUARIO tiene **un** rol

---

### 2. SECCIÓN — USUARIO

```
┌────────┐     ┌───────────┐     ┌──────┐
│SECCIÓN │─(1,1)◇ASIGNADO_A◇(0,n)─│USUARIO│
└────────┘     └───────────┘     └──────┘
```

- Una SECCIÓN tiene **muchos** usuarios
- Un USUARIO tiene **una** sección (nullable para admin)

---

### 3. CATEGORÍA — PRODUCTO

```
┌──────────┐    ┌───────────┐    ┌──────────┐
│CATEGORÍA │─(1,1)◇ CLASIFICA◇(0,n)─│ PRODUCTO │
└──────────┘    └───────────┘    └──────────┘
```

- Una CATEGORÍA clasifica **muchos** productos
- Un PRODUCTO tiene **una** categoría

---

### 4. PROVEEDOR — PRODUCTO

```
┌──────────┐    ┌───────────┐    ┌──────────┐
│PROVEEDOR │─(1,1)◇SUMINISTRA◇(0,n)─│ PRODUCTO │
└──────────┘    └───────────┘    └──────────┘
```

- Un PROVEEDOR suministra **muchos** productos
- Un PRODUCTO tiene **un** proveedor

---

### 5. CLIENTE — FACTURA

```
┌──────────┐    ┌───────────┐    ┌──────────┐
│ CLIENTE  │─(1,1)◇ REALIZA  ◇(0,n)─│ FACTURA  │
└──────────┘    └───────────┘    └──────────┘
```

- Un CLIENTE realiza **muchas** facturas
- Una FACTURA es de **un** cliente

---

### 6. USUARIO — FACTURA

```
┌──────┐       ┌───────────┐       ┌──────────┐
│USUARIO│──(1,1)◇   EMITE   ◇(0,n)──│ FACTURA  │
└──────┘       └───────────┘       └──────────┘
```

- Un USUARIO emite **muchas** facturas
- Una FACTURA es emitida por **un** usuario

---

### 7. SECCIÓN — FACTURA

```
┌────────┐     ┌───────────┐     ┌──────────┐
│SECCIÓN │─(1,1)◇ PROCESA   ◇(0,n)─│ FACTURA  │
└────────┘     └───────────┘     └──────────┘
```

- Una SECCIÓN procesa **muchas** facturas
- Una FACTURA es procesada por **una** sección

---

### 8. FACTURA — DETALLE_FACTURA

```
┌──────────┐    ┌───────────┐    ┌──────────────┐
│ FACTURA  │─(1,1)◇ CONTIENE  ◇(1,n)─│DETALLE_FACTURA│
└──────────┘    └───────────┘    └──────────────┘
```

- Una FACTURA tiene **uno o más** detalles
- Un DETALLE pertenece a **una** factura

---

### 9. PRODUCTO — DETALLE_FACTURA

```
┌──────────┐    ┌───────────┐    ┌──────────────┐
│ PRODUCTO │─(1,1)◇ INCLUYE_DF◇(0,n)─│DETALLE_FACTURA│
└──────────┘    └───────────┘    └──────────────┘
```

- Un PRODUCTO aparece en **muchos** detalles de factura
- Un DETALLE incluye **un** producto

---

### 10. PROVEEDOR — COMPRA

```
┌──────────┐    ┌───────────┐    ┌──────────┐
│PROVEEDOR │─(1,1)◇ COMPRA_A  ◇(0,n)─│  COMPRA  │
└──────────┘    └───────────┘    └──────────┘
```

- Un PROVEEDOR tiene **muchas** compras
- Una COMPRA es a **un** proveedor

---

### 11. USUARIO — COMPRA

```
┌──────┐       ┌───────────┐       ┌──────────┐
│USUARIO│──(1,1)◇ REGISTRA_C◇(0,n)──│  COMPRA  │
└──────┘       └───────────┘       └──────────┘
```

- Un USUARIO registra **muchas** compras
- Una COMPRA es registrada por **un** usuario

---

### 12. COMPRA — DETALLE_COMPRA

```
┌──────────┐    ┌───────────┐    ┌──────────────┐
│  COMPRA  │─(1,1)◇ CONTIENE_DC◇(1,n)─│DETALLE_COMPRA│
└──────────┘    └───────────┘    └──────────────┘
```

- Una COMPRA tiene **uno o más** detalles
- Un DETALLE pertenece a **una** compra

---

### 13. PRODUCTO — DETALLE_COMPRA

```
┌──────────┐    ┌───────────┐    ┌──────────────┐
│ PRODUCTO │─(1,1)◇ INCLUYE_DC◇(0,n)─│DETALLE_COMPRA│
└──────────┘    └───────────┘    └──────────────┘
```

- Un PRODUCTO aparece en **muchos** detalles de compra
- Un DETALLE incluye **un** producto

---

### 14. FACTURA — HISTORIAL

```
┌──────────┐    ┌───────────┐    ┌──────────────────────┐
│ FACTURA  │─(1,1)◇ HISTORIAL  ◇(0,n)─│FACTURA_ESTADO_HISTORIAL│
└──────────┘    └───────────┘    └──────────────────────┘
```

- Una FACTURA tiene **muchos** registros de historial
- Un HISTORIAL pertenece a **una** factura

---

### 15. USUARIO — AUDITORÍA

```
┌──────┐       ┌───────────┐       ┌──────────┐
│USUARIO│──(0,1)◇  GENERA   ◇(0,n)──│AUDITORÍA │
└──────┘       └───────────┘       └──────────┘
```

- Un USUARIO genera **muchas** auditorías
- Una AUDITORÍA es generada por **un** usuario (nullable)
