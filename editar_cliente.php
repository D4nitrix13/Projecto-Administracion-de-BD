<?php
session_start();
$pageTitle = "Editar cliente - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user  = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

// Supervisor y Facturador: roles restringidos para tipo_cliente
$isRestrictedRole = in_array($idRol, [2, 3], true);

/** @var PDO $connection */
$connection = require "./sql/db.php";

$error = null;

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    $_SESSION["flash_error"] = "Cliente no válido.";
    header("Location: clientes.php");
    exit();
}

// Cargar datos actuales
$stmt = $connection->prepare("
    SELECT id_cliente, nombres, apellidos, telefono, direccion, identificacion, tipo_cliente
    FROM Cliente
    WHERE id_cliente = :id
");
$stmt->execute([":id" => $id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    $_SESSION["flash_error"] = "Cliente no encontrado.";
    header("Location: clientes.php");
    exit();
}

// Si el rol es Supervisor/Facturador y por alguna razón el cliente no es Detallista,
// podemos opcionalmente bloquear la edición:
if ($isRestrictedRole && $cliente["tipo_cliente"] !== "Detallista") {
    $_SESSION["flash_error"] = "Su rol solo permite editar clientes detallistas.";
    header("Location: clientes.php");
    exit();
}

// Inicializar variables con datos existentes
$nombres        = $cliente["nombres"];
$apellidos      = $cliente["apellidos"];
$telefono       = $cliente["telefono"];
$direccion      = $cliente["direccion"];
$identificacion = $cliente["identificacion"];
$tipo_cliente   = $cliente["tipo_cliente"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombres        = trim($_POST["nombres"] ?? "");
    $apellidos      = trim($_POST["apellidos"] ?? "");
    $telefono       = trim($_POST["telefono"] ?? "");
    $direccion      = trim($_POST["direccion"] ?? "");
    $identificacion = trim($_POST["identificacion"] ?? "");

    // Para Supervisor y Facturador, el tipo SIEMPRE será Detallista
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
                UPDATE Cliente
                SET nombres = :nombres,
                    apellidos = :apellidos,
                    telefono = :telefono,
                    direccion = :direccion,
                    identificacion = :identificacion,
                    tipo_cliente = :tipo_cliente
                WHERE id_cliente = :id
            ");

            $stmt->execute([
                ":nombres"        => $nombres,
                ":apellidos"      => $apellidos,
                ":telefono"       => $telefono,
                ":direccion"      => $direccion,
                ":identificacion" => $identificacion,
                ":tipo_cliente"   => $tipo_cliente,
                ":id"             => $id,
            ]);

            $_SESSION["flash_success"] = "Cliente actualizado correctamente.";
            header("Location: clientes.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error al actualizar el cliente: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Clientes</p>
            <h1 class="dashboard-title">Editar cliente</h1>

            <p class="dashboard-muted">
                Actualice la información del cliente seleccionado.
            </p>

            <a href="clientes.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver al listado de clientes
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="editar_cliente.php?id=<?= (int)$id ?>" method="POST" class="form-grid">

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
                        <?= $isRestrictedRole ? 'disabled' : '' ?>>
                        <option value="Detallista" <?= $tipo_cliente === "Detallista" ? "selected" : "" ?>>
                            Detallista
                        </option>
                        <option value="Mayorista" <?= $tipo_cliente === "Mayorista" ? "selected" : "" ?>>
                            Mayorista
                        </option>
                    </select>

                    <?php if ($isRestrictedRole): ?>
                        <small class="dashboard-muted" style="font-size:12px; display:block; margin-top:4px;">
                            Su rol solo permite atender clientes de tipo Detallista.
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </section>

    </main>

</body>

</html>