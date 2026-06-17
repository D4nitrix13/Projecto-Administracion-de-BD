# DEVELOPMENT_GUIDE.md — Guia de Desarrollo

## Requisitos Previos

- Docker y Docker Compose instalados
- Git
- Bash (para setup.sh)
- Un editor de codigo (VS Code, PhpStorm, etc.)

## Instalacion

```bash
# Clonar el repositorio
git clone <url-del-repositorio>
cd Projecto-Administracion-de-BD

# Ejecutar setup completo
bash setup.sh
```

| Usuario                               | Contrasena                                                   | Rol   |
| ------------------------------------- | ------------------------------------------------------------ | ----- |
| `leonel.messi@admin.pandakitsune.com` | $2y$10$oCDDt/YuxYESRT8888zim.7Mn1AsfYVBXbVOgesp.1CLQuhuBxo2m | Admin |
El setup:

1. Detiene y limpia contenedores anteriores
2. Elimina datos de prueba (storage/, backups/, database/)
3. Crea estructura de directorios
4. Genera `.env` desde `.env.example` si no existe
5. Levanta PostgreSQL
6. Instala dependencias PHP via Composer
7. Crea archivos de configuracion iniciales
8. Asigna permisos
9. Levanta todos los servicios Docker
10. Carga datos iniciales desde `sql/01_data.sql`

## URLs del Sistema

| Servicio         | URL                                  |
| ---------------- | ------------------------------------ |
| Aplicacion       | `http://localhost:8080`              |
| pgAdmin          | `http://localhost:5050`              |
| Catalogo publico | `http://localhost:8080/catalogo.php` |

## Credenciales por Defecto

| (ver sql/01_data.sql)                 | (ver sql/01_data.sql)                                        | (ver sql/01_data.sql) |

**Nota:** Las credenciales se cargan desde `sql/01_data.sql` durante el setup.

## Comandos de Desarrollo

```bash
# Verificar sintaxis PHP de todos los archivos
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# Ver logs en tiempo real
docker logs -f pandas_app
docker logs -f pandas_bd

# Entrar al contenedor de la app
docker exec -it pandas_app bash

# Conectar a PostgreSQL
docker exec -it pandas_bd psql -U postgres -d pandas_estampados_y_kitsune

# Ver funciones almacenadas
docker exec -it pandas_bd psql -U postgres -d pandas_estampados_y_kitsune -c "\df"

# Ver tablas
docker exec -it pandas_bd psql -U postgres -d pandas_estampados_y_kitsune -c "\dt"

# Ver triggers
docker exec -it pandas_bd psql -U postgres -d pandas_estampados_y_kitsune -c "\dy"

# Reconstruir imagen Docker
docker compose -f docker/docker-compose.yml build --no-cache app

# Levantar servicios
docker compose -f docker/docker-compose.yml up -d

# Detener servicios
docker compose -f docker/docker-compose.yml down

# Detener y eliminar volumes
docker compose -f docker/docker-compose.yml down -v
```

## Estructura de un Modulo Nuevo

Para agregar un modulo nuevo, sigue este patron:

### 1. Crear repositorio (si accede a BD)

```php
// src/Repository/MiRepositorio.php
namespace App\Repository;

class MiRepositorio
{
    public function __construct(private \PDO $pdo) {}

    public function obtenerDatos(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM mi_funcion()");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

### 2. Crear wrapper (backward compatibility)

```php
// repositories/MiRepositorio.php
require_once __DIR__ . "/../bootstrap.php";
class_alias(\App\Repository\MiRepositorio::class, 'MiRepositorio');
```

### 3. Crear controller

```php
// controllers/mi_controller.php
function obtenerDatosMiModulo(): array
{
    $user = $_SESSION["user"];
    $connection = require __DIR__ . "/../sql/db.php";
    $miRepositorio = new MiRepositorio($connection);

    $datos = $miRepositorio->obtenerDatos();

    return [
        "user" => $user,
        "datos" => $datos,
    ];
}
```

### 4. Crear pagina de entrada

```php
// mi_modulo.php
<?php
session_start();
$pageTitle = "Mi Modulo - Panda Estampados / Kitsune";
require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/mi_controller.php";
requireLogin();
$viewData = obtenerDatosMiModulo();
?>
<!DOCTYPE html>
<html lang="es">
<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<body class="dashboard-body">
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>
    <main class="dashboard-main">
        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>
        <!-- Tu contenido aqui -->
    </main>
    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>
</body>
</html>
```

### 5. Crear partials

```bash
partials/mi-modulo/
├── header.php
├── table.php
├── filters.php
├── form.php
├── alerts.php
└── styles.php
```

## Recomendaciones para Nuevas Funcionalidades

1. **Si toca datos:** Crea un repositorio en `src/Repository/` y un wrapper en `repositories/`
2. **Si tiene logica de negocio:** Crea un servicio en `src/Service/`
3. **Si es un formulario POST:** Agrega `csrfField()` al form y el CSRF se valida automaticamente via `auth_guard.php`
4. **Si es una funcion almacenada nueva:** Agregarla al final de `sql/01_data.sql` o crear un nuevo archivo `sql/03_migrations.sql`
5. **Si necesitas una nueva tabla:** Agregar el CREATE TABLE a `sql/01_data.sql` y el repository correspondiente

## Depuracion de Errores Comunes

### "Failed to open stream: No such file or directory"

- Verifica que el path en `require`/`include` sea correcto
- Recuerda que `sql/db.php` usa `__DIR__ . "/../config/database.php"`

### "Class not found"

- Verifica que el namespace este correcto en `src/`
- Verifica que `composer.json` tenga el autoload PSR-4 configurado
- Ejecuta `composer dump-autoload`

### "Token CSRF invalido"

- El formulario no tiene `<?= csrfField() ?>`
- O la sesion expiro (recarga la pagina)

### "Connection refused" (PostgreSQL)

- Verifica que pandas_bd este corriendo: `docker ps`
- Verifica que las credenciales en `.env` sean correctas
- Verifica que PostgreSQL este listo: `docker exec pandas_bd pg_isready`

### Docker no cachea

- No uses `--no-cache` en build a menos que sea estrictamente necesario
- El Dockerfile del proyecto solo instala sistema, el codigo se monta via volumes
