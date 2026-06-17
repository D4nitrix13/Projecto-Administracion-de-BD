<?php
// * Stored function or procedure has been executed

session_start();

$pageTitle = "Nuevo cliente - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

$isRestrictedRole = in_array($idRol, [2, 3], true);

/** @var PDO $connection */
$connection = require __DIR__ . "/sql/db.php";

$error = null;

$nombres = "";
$apellidos = "";
$telefono = "";
$direccion = "";
$identificacion = "";
$tipo_cliente = "Detallista";

$redirect = trim($_GET["redirect"] ?? $_POST["redirect"] ?? "clientes.php");

$allowedRedirects = [
    "clientes.php",
    "nueva_factura.php",
];

if (!in_array($redirect, $allowedRedirects, true)) {
    $redirect = "clientes.php";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombres = trim($_POST["nombres"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $identificacion = trim($_POST["identificacion"] ?? "");

    if ($isRestrictedRole) {
        $tipo_cliente = "Detallista";
    } else {
        $tipo_cliente = $_POST["tipo_cliente"] ?? "Detallista";
    }

    if ($nombres === "" || $apellidos === "") {
        $error = "Complete los campos obligatorios marcados con (*).";
    } elseif (!in_array($tipo_cliente, ["Mayorista", "Detallista"], true)) {
        $error = "Tipo de cliente no válido.";
    } else {
        try {
            $stmt = $connection->prepare("
                SELECT registrar_cliente_sistema(
                    :nombres,
                    :apellidos,
                    :telefono,
                    :direccion,
                    :identificacion,
                    :tipo_cliente
                ) AS registrado
            ");

            $stmt->execute([
                ":nombres" => $nombres,
                ":apellidos" => $apellidos,
                ":telefono" => $telefono,
                ":direccion" => $direccion,
                ":identificacion" => $identificacion,
                ":tipo_cliente" => $tipo_cliente,
            ]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($resultado["registrado"])) {
                $_SESSION["flash_success"] = "Cliente registrado correctamente.";
                header("Location: " . $redirect);
                exit();
            }

            $error = "No se pudo registrar el cliente.";
        } catch (PDOException $e) {
            $error = "Error al guardar el cliente: " . $e->getMessage();
        }
    }
}

if ($idRol === 1) {
    $textoSubtitulo = "Registre un nuevo cliente para las ventas de Panda Estampados y Kitsune.";
} else {
    $textoSubtitulo = "Registre un nuevo cliente detallista para las ventas de Kitsune.";
}
?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/facturacion/facturas/nueva/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="dashboard-page-heading">
            <p class="dashboard-eyebrow">Clientes</p>

            <h1>Agregar nuevo cliente</h1>

            <p>
                <?= htmlspecialchars($textoSubtitulo) ?>
            </p>

            <a href="<?= htmlspecialchars($redirect) ?>" class="btn-secondary-inline" style="margin-top:16px;">
                ← Volver
            </a>
        </section>

        <section class="invoice-create-card">

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="nuevo_cliente.php" method="POST" class="invoice-form-grid cols-2">
                <?= csrfField() ?>

                <input
                    type="hidden"
                    name="redirect"
                    value="<?= htmlspecialchars($redirect) ?>">

                <div class="form-group">
                    <label class="label">Nombres (*)</label>

                    <input
                        type="text"
                        name="nombres"
                        class="input"
                        maxlength="80"
                        required
                        value="<?= htmlspecialchars($nombres) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Apellidos (*)</label>

                    <input
                        type="text"
                        name="apellidos"
                        class="input"
                        maxlength="80"
                        required
                        value="<?= htmlspecialchars($apellidos) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Teléfono</label>

                    <input
                        type="text"
                        name="telefono"
                        class="input"
                        maxlength="30"
                        value="<?= htmlspecialchars($telefono) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Dirección</label>

                    <input
                        type="text"
                        name="direccion"
                        class="input"
                        maxlength="200"
                        value="<?= htmlspecialchars($direccion) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Identificación</label>

                    <input
                        type="text"
                        name="identificacion"
                        class="input"
                        maxlength="40"
                        value="<?= htmlspecialchars($identificacion) ?>">
                </div>

                <div class="form-group">
                    <label class="label">Tipo de cliente</label>

                    <select
                        name="tipo_cliente"
                        class="input"
                        <?= $isRestrictedRole ? "disabled" : "" ?>>
                        <option value="Detallista" <?= $tipo_cliente === "Detallista" ? "selected" : "" ?>>
                            Detallista
                        </option>

                        <option value="Mayorista" <?= $tipo_cliente === "Mayorista" ? "selected" : "" ?>>
                            Mayorista
                        </option>
                    </select>

                    <?php if ($isRestrictedRole): ?>
                        <input type="hidden" name="tipo_cliente" value="Detallista">

                        <small class="dashboard-muted client-picker-help">
                            Su rol solo permite registrar clientes de tipo Detallista.
                        </small>
                    <?php endif; ?>
                </div>

                <div class="invoice-create-actions" style="grid-column: 1 / -1;">
                    <a href="<?= htmlspecialchars($redirect) ?>" class="btn-secondary-inline">
                        Cancelar
                    </a>

                    <button type="submit" class="btn-primary-inline">
                        Guardar cliente
                    </button>
                </div>

            </form>
        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

</body>

</html>