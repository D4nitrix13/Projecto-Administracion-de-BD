# Scripts de Backups y Mantenimiento

Esta carpeta contiene los scripts encargados de generar respaldos y ejecutar tareas básicas de mantenimiento para la base de datos PostgreSQL del proyecto Panda Estampados / Kitsune.

## `backup_full.sh`

Genera un backup completo de la base de datos.

Este script usa `pg_dump` para exportar toda la base de datos PostgreSQL, incluyendo estructura y datos. El archivo generado se guarda en:

```bash
backups/full/
```

También genera un log de ejecución en:

```bash
backups/logs/
```

---

## `backup_diff.sh`

Genera un respaldo rápido de tablas importantes.

Este script no respalda toda la base de datos, sino únicamente las tablas críticas del sistema, como productos, clientes, facturas, usuarios, compras y detalles relacionados.

El archivo generado se guarda en:

```bash
backups/diff/
```

También genera un log de ejecución en:

```bash
backups/logs/
```

---

## `backup_logs.sh`

Genera una copia comprimida de los logs de PostgreSQL.

Este script toma los archivos ubicados en:

```bash
database/logs/
```

y los comprime en un archivo `.tar.gz`.

El respaldo generado se guarda en:

```bash
backups/logs/
```

Este script sirve como evidencia del manejo y conservación de registros de la base de datos.

---

## `mantenimiento_bd.sh`

Ejecuta el plan completo de mantenimiento.

Este script llama automáticamente a los siguientes scripts:

```bash
backup_full.sh
backup_diff.sh
backup_logs.sh
```

Su función es centralizar el mantenimiento de la base de datos en un solo comando.

También genera su propio log en:

```bash
backups/logs/
```

---

## Dar permisos de ejecución

Antes de usar los scripts, se deben habilitar permisos de ejecución:

```bash
chmod +x scripts/backup_full.sh
chmod +x scripts/backup_diff.sh
chmod +x scripts/backup_logs.sh
chmod +x scripts/mantenimiento_bd.sh
```

---

## Ejecución manual

```bash
bash scripts/backup_full.sh
bash scripts/backup_diff.sh
bash scripts/backup_logs.sh
bash scripts/mantenimiento_bd.sh
```

---

## Nota

Estos scripts forman parte del sistema de administración de base de datos del proyecto y permiten demostrar procesos de respaldo completo, respaldo rápido, conservación de logs y mantenimiento general.
