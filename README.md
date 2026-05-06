# setup.sh

Este script prepara automáticamente todo el entorno del proyecto Panda Estampados / Kitsune.

Su objetivo es reiniciar completamente la estructura del sistema, configurar permisos correctos y levantar nuevamente los contenedores Docker.

---

## ¿Qué hace el script?

## 1. Detiene y elimina contenedores Docker

```bash
docker compose -f docker/docker-compose.yml down --remove-orphans -v
```

Elimina:

* contenedores
* redes
* volúmenes temporales
* servicios huérfanos

---

## 2. Elimina almacenamiento anterior

```bash
sudo rm -rf storage/ backups/ database/
```

Borra completamente:

* respaldos
* logs
* datos PostgreSQL
* configuración temporal
* archivos pgAdmin

Esto permite reiniciar el proyecto desde cero.

---

## 3. Crea nuevamente la estructura del proyecto

```txt
storage/
backups/
database/
```

Incluyendo:

```txt
storage/system
backups/manual
backups/full
backups/diff
backups/logs
database/postgresql
database/logs
database/wal_archive
database/pgadmin
```

---

## 4. Genera archivos JSON iniciales

Crea automáticamente:

```txt
storage/system/backup_schedule.json
storage/system/maintenance_history.json
backups/logs/delete_queue.json
```

Estos archivos son utilizados por el sistema de backups y mantenimiento.

---

## 5. Configura permisos correctamente

El script asigna propietarios y permisos adecuados para:

* PHP/Apache (`33:33`)
* PostgreSQL (`999:999`)
* pgAdmin (`5050:5050`)

También aplica permisos seguros para:

* logs
* WAL archive
* backups
* almacenamiento interno

---

## 6. Habilita ejecución de scripts

Activa permisos de ejecución para:

```txt
scripts/backup_full.sh
scripts/backup_diff.sh
scripts/backup_logs.sh
scripts/mantenimiento_bd.sh
```

---

## 7. Reconstruye y levanta Docker

Finalmente ejecuta:

```bash
docker compose -f docker/docker-compose.yml up -d --build
```

Esto:

* reconstruye imágenes
* levanta PostgreSQL
* levanta PHP/Apache
* levanta pgAdmin
* inicia scheduler de backups

---

## Uso

Dar permisos:

```bash
chmod +x setup.sh
```

Ejecutar:

```bash
./setup.sh
```

---

## Advertencia

Este script elimina completamente:

* datos PostgreSQL
* backups existentes
* logs
* configuración persistente

Debe usarse únicamente cuando se quiera reiniciar el entorno desde cero.
