# Guía de Herramientas — Diagramas ER y UML

---

## Diagrama Entidad-Relación (ER)

### Opción 1: dbdiagram.io (RECOMENDADO)
**Archivo:** `sql/erd.dbml`
1. Abrí [dbdiagram.io](https://dbdiagram.io)
2. Click "Create Diagram" → se abre el editor
3. Borrá el código de ejemplo
4. Copiá el contenido de `erd.dbml` y pegalo
5. El diagrama se dibuja automáticamente
6. Click "Share" → "Export as PNG" o "Export as PDF"

**Ventajas:** Automático, bonito, compartible online

---

### Opción 2: draw.io (GRATUITO)
**Archivo:** `sql/ER_CHEN.md`
1. Abrí [app.diagrams.net](https://app.diagrams.net)
2. Click "Create New Diagram" → "Blank Diagram"
3. En la barra lateral izquierda buscá "Entity Relation"
4. Seguí el archivo `ER_CHEN.md` para dibujar:
   - **Rectángulos** = Entidades (ROL, USUARIO, FACTURA, etc.)
   - **Rombos** = Relaciones (PERTENECE, EMITE, CONTIENE, etc.)
   - **Óvalos** = Atributos (id_rol, nombre, email, etc.)
   - **Líneas** = Conexiones con cardinalidades (1,1), (0,n), (1,n)
5. Exportá como PNG, SVG o PDF

**Ventajas:** Totalmente personalizable, offline, gratis

---

### Opción 3: Mermaid (automático)
**Archivo:** `sql/erd.mmd`
1. Abrí [mermaid.live](https://mermaid.live)
2. Pegá el contenido de `erd.mmd`
3. Se renderiza automáticamente
4. Click "Actions" → "PNG" o "SVG" para descargar

**Alternativas Mermaid:**
- VS Code: instalá extensión "Mermaid Preview"
- GitHub: pegalo en un `.md` y se renderiza
- Notion: pegalo como código "Mermaid"

---

### Opción 4: MySQL Workbench
1. Open MySQL Workbench
2. File → New Model → Add Diagram
3. Dibujá tablas y relaciones manualmente
4. Exportá como imagen

---

### Opción 5: pgModeler (PostgreSQL)
1. Descargá [pgmodeler.io](https://pgmodeler.io)
2. File → Import → Database
3. Conectá a tu DB y se importan las tablas
4. Exportá como imagen

---

## Diagrama UML de Clases

### Opción 1: PlantUML (RECOMENDADO)
**Archivo:** `sql/UML_CLASES.md`
1. Abrí [www.plantuml.com/plantuml](https://www.plantuml.com/plantuml)
2. Copiá el código PlantUML del archivo `UML_CLASES.md`
3. Pegalo en el editor de la izquierda
4. El diagrama se genera automáticamente a la derecha
5. Click derecho sobre la imagen → "Save Image As"

**Ventajas:** Automático, basado en código, versionable

---

### Opción 2: draw.io (GRATUITO)
**Archivo:** `sql/UML_CLASES.md`
1. Abrí [app.diagrams.net](https://app.diagrams.net)
2. Nuevo diagrama →搜 "UML Class"
3. Creá clases con:
   - **Nombre** arriba
   - **Atributos** en la sección del medio
   - **Métodos** en la sección de abajo
4. Conectá con líneas de asociación
5. Agregá multiplicidades: `1`, `*`, `0..1`

---

### Opción 3: Lucidchart
1. Abrí [lucidchart.com](https://lucidchart.com)
2. Nuevo documento →搜 "UML Class"
3. Usá plantillas predefinidas
4. Exportá como PNG o PDF

---

### Opción 4: IntelliJ IDEA
1. Instalá plugin "ObjectAid UML Explorer"
2. Click derecho en una clase → "ObjectAid" → "New UML Class Diagram"
3. Arrastrá las clases al diagrama
4. Exportá como imagen

---

### Opción 5: Visual Paradigm
1. Descargá [visual-paradigm.com](https://www.visual-paradigm.com)
2. Diagram → New → UML Class Diagram
3. Dibujá clases y relaciones
4. Exportá como imagen

---

## Resumen de Archivos

| Archivo | Contenido | Herramienta principal |
|---------|-----------|----------------------|
| `ER_CHEN.md` | Modelo ER en notación Chen | draw.io |
| `UML_CLASES.md` | Diagrama UML de clases | PlantUML |
| `erd.mmd` | ER en formato Mermaid | mermaid.live |
| `erd.dbml` | ER en formato DBML | dbdiagram.io |

---

## Flujo de Trabajo Recomendado

1. **Primero:** Abrí `erd.dbml` en dbdiagram.io → verás el ER automático
2. **Después:** Copiá el código PlantUML de `UML_CLASES.md` en plantuml.com → verás el UML
3. **Si querés personalizar:** Usá draw.io con los archivos `ER_CHEN.md` y `UML_CLASES.md`
