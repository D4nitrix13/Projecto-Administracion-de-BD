# Diagrama UML de Clases — Panda Estampados y Kitsune

> **Herramienta recomendada:** [PlantUML Online](https://www.plantuml.com/plantuml)
> **Alternativa:** [draw.io](https://app.diagrams.net) → Nuevo diagrama → "UML Class"

---

## Cómo generarlo

### Opción 1: PlantUML (automático)

1. Copiá el código de abajo
2. Pegalo en [www.plantuml.com/plantuml](https://www.plantuml.com/plantuml)
3. Se genera el diagrama automáticamente
4. Click derecho → "Save as" para exportar PNG/SVG

### Opción 2: draw.io (manual)

1. Abrí [app.diagrams.net](https://app.diagrams.net)
2. Nuevo diagrama → "UML Class"
3. Creá clases con atributos y métodos
4. Conectá con asociaciones (líneas)

---

## Código PlantUML — Copiar y Pegar

```plantuml
@startuml
skinparam classAttributeIconSize 0
skinparam classFontSize 14
skinparam packageFontSize 16

title Panda Estampados y Kitsune — Diagrama UML de Clases

' ============================================================
' ENTIDADES DE REFERENCIA
' ============================================================

class ROL {
  - idRol: int
  - nombre: String
  --
  + getIdRol(): int
  + getNombre(): String
  + setNombre(nombre: String): void
}

class SECCION {
  - idSeccion: int
  - nombre: String
  --
  + getIdSeccion(): int
  + getNombre(): String
  + setNombre(nombre: String): void
}

class CATEGORIA {
  - idCategoria: int
  - nombre: String
  --
  + getIdCategoria(): int
  + getNombre(): String
  + setNombre(nombre: String): void
}

class PROVEEDOR {
  - idProveedor: int
  - nombre: String
  - telefono: String
  - email: String
  - direccion: String
  --
  + getIdProveedor(): int
  + getNombre(): String
  + getTelefono(): String
  + getEmail(): String
  + getDireccion(): String
}

' ============================================================
' ENTIDADES PRINCIPALES
' ============================================================

class USUARIO {
  - idUsuario: int
  - nombre: String
  - email: String
  - password: String
  - idRol: int {FK}
  - idSeccion: int {FK, nullable}
  --
  + getIdUsuario(): int
  + getNombre(): String
  + getEmail(): String
  + getRol(): ROL
  + getSeccion(): SECCION
  + setNombre(nombre: String): void
  + setEmail(email: String): void
  + esAdmin(): boolean
  + puedeEliminar(): boolean
}

class CLIENTE {
  - idCliente: int
  - nombres: String
  - apellidos: String
  - telefono: String
  - direccion: String
  - identificacion: String
  - tipoCliente: String
  - fechaRegistro: Date
  --
  + getIdCliente(): int
  + getNombreCompleto(): String
  + getTelefono(): String
  + getTipo(): String
  + esMayorista(): boolean
  + getFacturas(): List<FACTURA>
  + getResumen(): Object
}

class PRODUCTO {
  - idProducto: int
  - codigo: String
  - nombre: String
  - descripcion: String
  - imagen: String
  - precioCompra: Decimal
  - precioVenta: Decimal
  - stock: int
  - idCategoria: int {FK}
  - idProveedor: int {FK}
  --
  + getIdProducto(): int
  + getCodigo(): String
  + getNombre(): String
  + getStock(): int
  + getPrecioVenta(): Decimal
  + reducirStock(cantidad: int): void
  + aumentarStock(cantidad: int): void
  + hayStock(): boolean
  + getCategoria(): CATEGORIA
  + getProveedor(): PROVEEDOR
}

' ============================================================
' ENTIDADES DE TRANSACCIONES
' ============================================================

class COMPRA {
  - idCompra: int
  - fecha: DateTime
  - total: Decimal
  - idProveedor: int {FK}
  - idUsuario: int {FK}
  --
  + getIdCompra(): int
  + getFecha(): DateTime
  + getTotal(): Decimal
  + getProveedor(): PROVEEDOR
  + getUsuario(): USUARIO
  + getDetalles(): List<DETALLE_COMPRA>
  + calcularTotal(): Decimal
}

class DETALLE_COMPRA {
  - idDetalle: int
  - cantidad: int
  - costoUnitario: Decimal
  - totalLinea: Decimal
  - idCompra: int {FK}
  - idProducto: int {FK}
  --
  + getIdDetalle(): int
  + getCantidad(): int
  + getCostoUnitario(): Decimal
  + getTotalLinea(): Decimal
  + getCompra(): COMPRA
  + getProducto(): PRODUCTO
  + calcularTotal(): Decimal
}

class FACTURA {
  - idFactura: int
  - fecha: DateTime
  - subtotal: Decimal
  - descuento: Decimal
  - impuesto: Decimal
  - total: Decimal
  - tipoClienteVenta: String
  - nombreClienteFugaz: String
  - montoPagado: Decimal
  - saldoPendiente: Decimal
  - porcentajePagado: Decimal
  - estadoPago: String
  - estadoProduccion: String
  - fechaEntregaEstimada: Date
  - fechaEntregaReal: Date
  - idCliente: int {FK}
  - idUsuario: int {FK}
  - idSeccion: int {FK}
  --
  + getIdFactura(): int
  + getFecha(): DateTime
  + getTotal(): Decimal
  + getEstadoPago(): String
  + getEstadoProduccion(): String
  + getCliente(): CLIENTE
  + getUsuario(): USUARIO
  + getSeccion(): SECCION
  + getDetalles(): List<DETALLE_FACTURA>
  + getHistorial(): List<FACTURA_HISTORIAL>
  + calcularTotales(): void
  + estaPagada(): boolean
  + estaCancelada(): boolean
  + cancelar(): void
}

class DETALLE_FACTURA {
  - idDetalle: int
  - cantidad: int
  - precioUnitario: Decimal
  - descuentoLinea: Decimal
  - totalLinea: Decimal
  - idFactura: int {FK}
  - idProducto: int {FK}
  --
  + getIdDetalle(): int
  + getCantidad(): int
  + getPrecioUnitario(): Decimal
  + getDescuentoLinea(): Decimal
  + getTotalLinea(): Decimal
  + getFactura(): FACTURA
  + getProducto(): PRODUCTO
  + calcularTotal(): Decimal
}

class FACTURA_HISTORIAL {
  - idHistorial: int
  - tipoEvento: String
  - estadoPagoAnterior: String
  - estadoPagoNuevo: String
  - estadoProdAnterior: String
  - estadoProdNuevo: String
  - montoPagadoAnterior: Decimal
  - montoPagadoNuevo: Decimal
  - montoAbonado: Decimal
  - saldoAnterior: Decimal
  - saldoNuevo: Decimal
  - fechaEstimadaAnterior: Date
  - fechaEstimadaNueva: Date
  - comentario: String
  - fechaEvento: DateTime
  - idFactura: int {FK}
  --
  + getIdHistorial(): int
  + getTipoEvento(): String
  + getEstadoAnterior(): String
  + getEstadoNuevo(): String
  + getComentario(): String
  + getFechaEvento(): DateTime
  + getFactura(): FACTURA
}

class AUDITORIA {
  - idAuditoria: int
  - usuario: String
  - accion: String
  - tablaAfectada: String
  - descripcion: String
  - fecha: DateTime
  - idUsuario: int {FK, nullable}
  - registroId: String
  - datosAnteriores: JSON
  --
  + getIdAuditoria(): int
  + getAccion(): String
  + getTablaAfectada(): String
  + getDescripcion(): String
  + getFecha(): DateTime
  + getDatosAnteriores(): JSON
  + getUsuario(): USUARIO
}

' ============================================================
' RELACIONES
' ============================================================

ROL "1" -- "*" USUARIO : tiene >
SECCION "1" -- "*" USUARIO : asignado_a >
CATEGORIA "1" -- "*" PRODUCTO : clasifica >
PROVEEDOR "1" -- "*" PRODUCTO : suministra >
PROVEEDOR "1" -- "*" COMPRA : compra_a >
USUARIO "1" -- "*" COMPRA : registra >
USUARIO "1" -- "*" FACTURA : emite >
SECCION "1" -- "*" FACTURA : procesa >
CLIENTE "1" -- "*" FACTURA : realiza >
COMPRA "1" -- "*" DETALLE_COMPRA : contiene >
PRODUCTO "1" -- "*" DETALLE_COMPRA : incluye >
FACTURA "1" -- "*" DETALLE_FACTURA : contiene >
PRODUCTO "1" -- "*" DETALLE_FACTURA : incluye >
FACTURA "1" -- "*" FACTURA_HISTORIAL : historial >
USUARIO "0..1" -- "*" AUDITORIA : genera >

@enduml
```

---

## Relaciones UML (multiplicidad)

| Clase 1   | Multiplicidad | Relación   | Multiplicidad | Clase 2           |
| --------- | :-----------: | ---------- | :-----------: | ----------------- |
| ROL       |       1       | tiene      |       *       | USUARIO           |
| SECCION   |       1       | asignado_a |       *       | USUARIO           |
| CATEGORIA |       1       | clasifica  |       *       | PRODUCTO          |
| PROVEEDOR |       1       | suministra |       *       | PRODUCTO          |
| PROVEEDOR |       1       | compra_a   |       *       | COMPRA            |
| USUARIO   |       1       | registra   |       *       | COMPRA            |
| USUARIO   |       1       | emite      |       *       | FACTURA           |
| SECCION   |       1       | procesa    |       *       | FACTURA           |
| CLIENTE   |       1       | realiza    |       *       | FACTURA           |
| COMPRA    |       1       | contiene   |       *       | DETALLE_COMPRA    |
| PRODUCTO  |       1       | incluye    |       *       | DETALLE_COMPRA    |
| FACTURA   |       1       | contiene   |       *       | DETALLE_FACTURA   |
| PRODUCTO  |       1       | incluye    |       *       | DETALLE_FACTURA   |
| FACTURA   |       1       | historial  |       *       | FACTURA_HISTORIAL |
| USUARIO   |     0..1      | genera     |       *       | AUDITORIA         |
