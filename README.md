<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/D4nitrix13 -->
<!-- GitLab: https://gitlab.com/D4nitrix13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# Guía de instalación y configuración del entorno

Este documento describe los comandos necesarios para preparar el entorno, instalar PHP con sus extensiones, configurar PostgreSQL Client 18 (pg_dump), y activar el cron para la ejecución automática de respaldos.

---

## 1. Actualizar repositorios

```bash
apt update
```

---

## 2. Instalar PHP y extensiones necesarias

Instala PHP junto a los módulos requeridos para el proyecto:

```bash
apt install php libapache2-mod-php php-mbstring php-xmlrpc php-soap php-gd php-xml php-cli php-zip php-bcmath php-tokenizer php-json php-pear
apt install php-pgsql
```

---

## 3. Instalar utilidades del sistema

```bash
apt install -y cron vim
service cron start
```

---

## 4. Instalar PostgreSQL Client 18 (pg_dump 18)

Se agrega el repositorio oficial de PostgreSQL, se actualizan los paquetes y se instala la herramienta `postgresql-client-18`.

```bash
apt-get install -y wget gnupg lsb-release

echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" \
    > /etc/apt/sources.list.d/pgdg.list

wget -qO- https://www.postgresql.org/media/keys/ACCC4CF8.asc \
    | gpg --dearmor -o /etc/apt/trusted.gpg.d/postgresql.gpg

apt-get update
apt-get install -y postgresql-client-18
```

Esto instalará herramientas como:

* `pg_dump`
* `psql`
* `pg_restore`

---

## 5. Crear la entrada del cron para los respaldos automáticos

Edita el crontab:

```bash
crontab -e
```

Agrega la línea:

Ejecutar cada día a medianoche (00:00)

```bash
0 0 * * * /usr/bin/php /app/utils/cron_backup.php
```

* **Significado:**
El script `cron_backup.php` se ejecutará **cada minuto**, generando un respaldo automático según la lógica definida dentro del archivo.
