<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/D4nitrix13 -->
<!-- GitLab: https://gitlab.com/D4nitrix13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# Guía rápida de instalación y configuración

mkdir -p storage/system

touch storage/system/backup_schedule.json
touch storage/system/maintenance_history.json

sudo chown -R 33:33 storage
sudo chmod -R 775 storage
sudo chmod 664 storage/system/backup_schedule.json
sudo chmod 664 storage/system/maintenance_history.json

mkdir -p backups/logs

touch backups/logs/delete_queue.json

echo "[]" > backups/logs/delete_queue.json

sudo chown -R 33:33 backups/logs
sudo chmod -R 775 backups/logs
sudo chmod 664 backups/logs/delete_queue.json

sudo chown -R 33:33 backups
sudo chmod -R 775 backups
sudo find backups -type f -exec chmod 664 {} \;
sudo find backups -type d -exec chmod 775 {} \;

mkdir -p backups/manual backups/full backups/diff backups/logs

touch backups/logs/delete_queue.json
echo "[]" > backups/logs/delete_queue.json

sudo chown -R 33:33 backups
sudo chmod -R 775 backups
sudo find backups -type f -exec chmod 664 {} \;
sudo find backups -type d -exec chmod 775 {} \;

mkdir -p database/wal_archive

sudo chown -R 33:33 database/wal_archive
sudo chmod -R 775 database/wal_archive
sudo find database/wal_archive -type f -exec chmod 664 {} \;
sudo find database/wal_archive -type d -exec chmod 775 {} \;

Esta guía explica cómo preparar, levantar y restaurar el entorno del sistema **Panda Estampados / Kitsune** usando Docker Compose, PostgreSQL 18, pgAdmin y respaldos locales. Está resumida para seguir los comandos en orden correcto. :contentReference[oaicite:0]{index=0}

---

## 1. Preparar carpetas del proyecto

Ejecutar desde la raíz del proyecto:

```bash
mkdir -p database/{logs,postgresql,wal_archive,pgadmin} backups/{full,diff,logs}
```

Estas carpetas se usan para:

```txt
database/postgresql   Datos internos de PostgreSQL
database/logs         Logs de PostgreSQL
database/wal_archive  Archivos WAL de PostgreSQL
database/pgadmin      Datos internos de pgAdmin
backups/              Respaldos generados desde la aplicación
```

---

## 2. Configurar permisos

Ejecutar desde la raíz del proyecto:

```bash
sudo chown -R $USER:33 backups
sudo chmod -R 775 backups
sudo find backups -type d -exec chmod g+s {} \;

sudo chown -R 999:999 database/
sudo chmod -R 700 database/postgresql
sudo chmod -R 755 database/logs database/wal_archive

sudo chown -R 5050:5050 database/pgadmin
sudo chmod -R 700 database/pgadmin
```

Importante:

* `33` corresponde normalmente al grupo `www-data`, usado por Apache/PHP.
* `999` corresponde normalmente al usuario interno de PostgreSQL.
* `5050` corresponde normalmente al usuario interno de pgAdmin.
* La carpeta `backups/` debe permitir escritura para que PHP pueda generar respaldos.

---

## 3. Levantar el proyecto

Ejecutar:

```bash
docker compose -f docker/docker-compose.yml up -d --build
```

Verificar que los contenedores estén activos:

```bash
docker ps
```

Contenedores esperados:

```txt
pandas_app
pandas_bd
pandas_pgadmin
```

---

## 4. Crear la base de datos si no existe

Si al importar aparece este error:

```txt
FATAL: database "pandas_estampados_y_kitsune" does not exist
```

crear la base manualmente:

```bash
docker exec -it pandas_bd createdb \
  -U postgres \
  pandas_estampados_y_kitsune
```

Verificar que exista:

```bash
docker exec -it pandas_bd psql \
  -U postgres \
  -d postgres \
  -c '\l'
```

---

## 5. Importar la base de datos

Primero importar el dump principal:

```bash
docker exec -i pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  < sql/01_data.sql
```

Luego importar procedimientos y funciones adicionales, solo si no están incluidos ya en `01_data.sql`:

```bash
docker exec -i pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  < sql/02_procedures.sql
```

Para verificar si `01_data.sql` ya trae funciones:

```bash
grep -n "CREATE FUNCTION\|CREATE PROCEDURE" sql/01_data.sql | head
```

Si aparecen resultados, normalmente no es necesario ejecutar `02_procedures.sql`.

---

## 6. Verificar la restauración

Ver tablas:

```bash
docker exec -it pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  -c '\dt'
```

Ver funciones:

```bash
docker exec -it pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  -c '\df'
```

---

## 7. Acceder a la aplicación

Abrir en el navegador:

```txt
http://localhost:8080
```

---

## 8. Acceder a pgAdmin

Abrir en el navegador:

```txt
http://localhost:5050
```

Credenciales de pgAdmin:

```txt
Correo: admin@admin.com
Contraseña: admin
```

Para registrar el servidor PostgreSQL en pgAdmin:

```txt
Name: Panda PostgreSQL
Host name/address: postgres
Port: 5432
Maintenance database: pandas_estampados_y_kitsune
Username: postgres
Password: root
```

Importante: dentro de pgAdmin el host no es `localhost`, es:

```txt
postgres
```

---

## 9. Generar respaldo manual desde consola

```bash
docker exec -i pandas_bd pg_dump \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  > backups/full/backup_manual_$(date +%Y-%m-%d_%H-%M-%S).sql
```

Verificar respaldo:

```bash
ls -lh backups/full/
```

---

## 10. Apagar el entorno

```bash
docker compose -f docker/docker-compose.yml down
```

---

## 11. Reiniciar todo desde cero

Usar esto solo si quieres borrar contenedores, volúmenes y datos locales de la base de datos.

```bash
docker compose -f docker/docker-compose.yml down --remove-orphans -v
sudo rm -rf database backups
mkdir -p database/{logs,postgresql,wal_archive,pgadmin} backups/{full,diff,logs}
```

Luego volver a configurar permisos:

```bash
sudo chown -R $USER:33 backups
sudo chmod -R 775 backups
sudo find backups -type d -exec chmod g+s {} \;

sudo chown -R 999:999 database/
sudo chmod -R 700 database/postgresql
sudo chmod -R 755 database/logs database/wal_archive

sudo chown -R 5050:5050 database/pgadmin
sudo chmod -R 700 database/pgadmin
```

Después levantar otra vez:

```bash
docker compose -f docker/docker-compose.yml up -d --build
```

Y restaurar la base:

```bash
docker exec -it pandas_bd createdb \
  -U postgres \
  pandas_estampados_y_kitsune

docker exec -i pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  < sql/01_data.sql
```

---

## 12. Comandos útiles

Ver logs de la aplicación:

```bash
docker logs -f pandas_app
```

Ver logs de PostgreSQL:

```bash
docker logs -f pandas_bd
```

Ver logs de pgAdmin:

```bash
docker logs -f pandas_pgadmin
```

Entrar al contenedor PHP/Apache:

```bash
docker exec -it pandas_app bash
```

Entrar al contenedor PostgreSQL:

```bash
docker exec -it pandas_bd bash
```

Entrar directamente a PostgreSQL:

```bash
docker exec -it pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune
```

---

## 13. Orden recomendado de instalación

Para una instalación limpia, seguir este orden:

```bash
mkdir -p database/{logs,postgresql,wal_archive,pgadmin} backups/{full,diff,logs}

sudo chown -R $USER:33 backups
sudo chmod -R 775 backups
sudo find backups -type d -exec chmod g+s {} \;

sudo chown -R 999:999 database/
sudo chmod -R 700 database/postgresql
sudo chmod -R 755 database/logs database/wal_archive

sudo chown -R 5050:5050 database/pgadmin
sudo chmod -R 700 database/pgadmin

docker compose -f docker/docker-compose.yml up -d --build

docker exec -it pandas_bd createdb \
  -U postgres \
  pandas_estampados_y_kitsune

docker exec -i pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  < sql/01_data.sql

docker exec -it pandas_bd psql \
  -U postgres \
  -d pandas_estampados_y_kitsune \
  -c '\dt'
```

---

## 14. Notas importantes

* `POSTGRES_DB` solo crea la base automáticamente en la primera inicialización del contenedor.
* Si la carpeta `database/postgresql` ya tenía datos, cambiar `POSTGRES_DB` no crea la base de nuevo.
* Si la base no existe, se puede crear manualmente con `createdb`.
* No ejecutes `02_procedures.sql` si `01_data.sql` ya contiene funciones y procedimientos.
* Si `/docker-entrypoint-initdb.d` está vacío, no se ejecutará ningún SQL automático.
* Para evitar errores de permisos, configura `backups/`, `database/postgresql/`, `database/logs/`, `database/wal_archive/` y `database/pgadmin/` antes de levantar los contenedores.
