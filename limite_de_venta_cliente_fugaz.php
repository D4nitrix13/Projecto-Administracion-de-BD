<?php

session_start();

$pageTitle = "Límite de venta cliente fugaz - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";

requireAdmin();

date_default_timezone_set("America/Managua");

$user = $_SESSION["user"];

$error = null;
$success = null;

$configFile = __DIR__ . "/storage/system/configuracion_sistema.json";

function obtenerConfiguracionDefault(): array
{
    return [
        "limite_de_venta_cliente_fugaz" => 1000.00,
    ];
}

function asegurarArchivoConfiguracion(string $configFile): void
{
    if (!is_file($configFile)) {
        file_put_contents(
            $configFile,
            json_encode(obtenerConfiguracionDefault(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
}

function leerConfiguracionSistema(string $configFile): array
{
    $default = obtenerConfiguracionDefault();

    asegurarArchivoConfiguracion($configFile);

    $contenido = file_get_contents($configFile);

    if ($contenido === false || trim($contenido) === "") {
        return $default;
    }

    $data = json_decode($contenido, true);

    if (!is_array($data)) {
        return $default;
    }

    return array_merge($default, $data);
}

function guardarConfiguracionSistema(string $configFile, array $data): void
{
    file_put_contents(
        $configFile,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

asegurarArchivoConfiguracion($configFile);

$configuracion = leerConfiguracionSistema($configFile);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "save_config") {
        $limiteClienteFugazRaw = trim((string)($_POST["limite_de_venta_cliente_fugaz"] ?? ""));

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $limiteClienteFugazRaw)) {
            $error = "El límite debe contener solo números y máximo 2 decimales.";
        } else {
            $limiteClienteFugazNuevo = (float)$limiteClienteFugazRaw;

            if ($limiteClienteFugazNuevo <= 0) {
                $error = "El límite del cliente fugaz debe ser mayor a C$ 0.00.";
            } elseif ($limiteClienteFugazNuevo > 999999) {
                $error = "El límite del cliente fugaz no puede ser mayor a C$ 999,999.00.";
            } else {
                $configuracion["limite_de_venta_cliente_fugaz"] = $limiteClienteFugazNuevo;

                guardarConfiguracionSistema($configFile, $configuracion);

                $success = "El límite del cliente fugaz fue actualizado correctamente.";
            }
        }
    }

    if ($action === "reset_config") {
        $configuracion = obtenerConfiguracionDefault();

        guardarConfiguracionSistema($configFile, $configuracion);

        $success = "La configuración fue restablecida a C$ 1,000.00.";
    }
}

$configuracion = leerConfiguracionSistema($configFile);

$limiteClienteFugaz = (float)($configuracion["limite_de_venta_cliente_fugaz"] ?? 1000.00);

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/sistema/limite_de_venta_cliente_fugaz/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/sistema/limite_de_venta_cliente_fugaz/header.php"; ?>

        <?php require __DIR__ . "/partials/sistema/limite_de_venta_cliente_fugaz/alerts.php"; ?>

        <?php require __DIR__ . "/partials/sistema/limite_de_venta_cliente_fugaz/form.php"; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>