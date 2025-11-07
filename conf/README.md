# Configuración de PHP – Instrucciones para aplicar `conf/php.ini`

Este proyecto incluye un archivo de configuración personalizado para PHP ubicado en:

```bash
conf/php.ini
```

Este archivo contiene los ajustes necesarios para que la aplicación funcione correctamente, incluyendo:

* Límites aumentados para subida de archivos
* Configuración para manejo de imágenes
* Parámetros recomendados para rendimiento
* Ajustes compatibles con Apache en PHP 8.3

Para que estos cambios tengan efecto, es necesario reemplazar el archivo de configuración principal de PHP del sistema.

---

## **1. Ubicación del php.ini del sistema**

En distribuciones basadas en Debian/Ubuntu (incluyendo Ubuntu Server y Linux Mint), el archivo que PHP usa cuando corre bajo Apache está en:

```bash
/etc/php/8.3/apache2/php.ini
```

> *Si estás usando otra versión de PHP, como 8.2 o 8.1, la ruta cambia ligeramente.*

---

## **2. Copiar el php.ini del proyecto al sistema**

Ejecuta este comando en la raíz del proyecto:

```bash
sudo cp conf/php.ini /etc/php/8.3/apache2/php.ini
```

Esto reemplaza por completo el archivo de configuración del sistema con el que viene incluido en el proyecto.

---

## **3. Reiniciar Apache para aplicar cambios**

Es obligatorio reiniciar Apache para que PHP lea la nueva configuración:

```bash
sudo systemctl restart apache2
```

También puedes verificar que se aplicaron los cambios ejecutando:

```bash
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

---

## **4. ¿Qué incluye este php.ini personalizado?**

* `upload_max_filesize` aumentado (permite subir imágenes grandes)
* `post_max_size` ajustado para formularios con archivos
* `max_execution_time` optimizado
* Configuración segura para producción
* Mejor manejo de errores
* Ajustes para que subidas de productos/imágenes funcionen sin fallar

---

## **5. Advertencia importante**

Reemplazar el archivo `php.ini` afecta **a todo el sistema**, no solo a este proyecto.

Si tienes otras aplicaciones PHP en el mismo servidor, asegúrate de que esta configuración no las afecte negativamente.
