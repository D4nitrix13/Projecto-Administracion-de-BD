<?php

session_start();

$pageTitle = "Editar factura - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
requireAdmin();

require_once __DIR__ . "/bootstrap.php";
require_once __DIR__ . "/config/constants.php";
require_once __DIR__ . "/repositories/ProductoRepository.php";
require_once __DIR__ . "/services/FacturaService.php";

date_default_timezone_set("America/Managua");

$user = $_SESSION["user"] ?? [];
$currentPage = basename($_SERVER["PHP_SELF"]);

$connection = require __DIR__ . "/sql/db.php";
$facturaService = new FacturaService(
    $connection,
    new FacturaValidationService($connection),
    new FacturaCalculationService($connection, new ProductoRepository($connection)),
);

$error = null;
$success = null;

$idFactura = (int)($_GET["id"] ?? $_POST["id_factura"] ?? 0);

$factura = null;
$detalles = [];
$clientes = [];
$productos = [];
$secciones = [];
$usuarios = [];
$clienteFactura = null;
$limiteClienteFugaz = $facturaService->obtenerLimiteClienteFugazParaVista();

function facturaEditarFechaInput(?string $fecha): string
{
    $timestamp = $fecha ? strtotime($fecha) : false;

    if ($timestamp === false) {
        return date("Y-m-d\TH:i");
    }

    return date("Y-m-d\TH:i", $timestamp);
}

function facturaEditarDinero(float $valor): string
{
    return "C$ " . number_format($valor, 2);
}

if ($idFactura <= 0) {
    $error = "Debe indicar una factura válida.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$error) {
    $resultado = $facturaService->editarFactura($idFactura, $_POST, $user);

    if ($resultado["success"]) {
        $success = $resultado["message"];
    } else {
        $error = $resultado["message"];
    }
}

try {
    if ($idFactura > 0) {
        $factura = $facturaService->obtenerFacturaParaEditar($idFactura);

        if (!$factura) {
            $error = "No se encontró la factura solicitada.";
        } else {
            $detalles = $facturaService->obtenerDetallesFacturaParaEditar($idFactura);

            $clienteFactura = $facturaService->obtenerClienteFacturaParaEditar(
                (int)$factura["id_cliente"]
            );
        }
    }

    $clientes = $connection
        ->query("SELECT * FROM listar_clientes_habituales()")
        ->fetchAll(PDO::FETCH_ASSOC);

    $productos = $connection
        ->query("SELECT * FROM listar_productos_para_factura()")
        ->fetchAll(PDO::FETCH_ASSOC);

    $secciones = $connection
        ->query("SELECT * FROM listar_secciones_ordenadas()")
        ->fetchAll(PDO::FETCH_ASSOC);

    $usuarios = $connection
        ->query("SELECT * FROM listar_usuarios_ordenados()")
        ->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/facturacion/facturas/editar/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <?php require __DIR__ . "/partials/facturacion/facturas/editar/header.php"; ?>

        <?php require __DIR__ . "/partials/facturacion/facturas/editar/alerts.php"; ?>

        <?php if ($factura): ?>
            <?php require __DIR__ . "/partials/facturacion/facturas/editar/form.php"; ?>
        <?php endif; ?>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>