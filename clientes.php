<?php
session_start();
$pageTitle = "Clientes - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user  = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

/** @var PDO $connection */
$connection = require "./sql/db.php";

// Mensajes flash
$flashSuccess = $_SESSION["flash_success"] ?? null;
$flashError   = $_SESSION["flash_error"] ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

/* ==============================
 * 1) LECTURA DE FILTROS (GET)
 * ============================== */
$busqueda   = trim($_GET['q'] ?? "");
$tipoFiltro = trim($_GET['tipo'] ?? "");

/**
 * Reglas de visibilidad por rol:
 *  - Admin (1): puede ver todos los tipos de clientes (Mayorista y Detallista).
 *  - Supervisor (2): solo clientes Detallista (sin poder cambiar el filtro).
 *  - Facturador (3): solo clientes Detallista (sin poder cambiar el filtro).
 */
$soloDetallista = in_array($idRol, [2, 3], true); // Supervisor y Facturador

if ($soloDetallista) {
    // Forzamos a Detallista para Supervisor y Facturador
    $tipoFiltro = 'Detallista';
}

/* ==============================
 * 2) OBTENER CLIENTES CON FILTRO
 * ============================== */
$sql = "
    SELECT id_cliente, nombres, apellidos, telefono, direccion,
           identificacion, tipo_cliente
    FROM Cliente
    WHERE 1 = 1
";
$params = [];

// filtro de texto: nombres, apellidos, teléfono, dirección, identificación
if ($busqueda !== "") {
    $sql .= "
        AND (
            nombres ILIKE :q
            OR apellidos ILIKE :q
            OR telefono ILIKE :q
            OR direccion ILIKE :q
            OR identificacion ILIKE :q
        )
    ";
    $params[':q'] = '%' . $busqueda . '%';
}

// filtro por tipo de cliente (solo se aplica si el valor es válido)
if ($tipoFiltro === 'Mayorista' || $tipoFiltro === 'Detallista') {
    $sql .= " AND tipo_cliente = :tipo";
    $params[':tipo'] = $tipoFiltro;
}

$sql .= " ORDER BY id_cliente DESC";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
 * 3) TEXTO SEGÚN ROL
 * ============================== */
$textoSubtitulo = "Administre los clientes de Panda Estampados y Kitsune.";

if ($idRol === 2 || $idRol === 3) {
    // Supervisor y Facturador
    $textoSubtitulo = "Administre los clientes de Kitsune.";
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">CLIENTES</p>
            <h1 class="dashboard-title">Listado de clientes</h1>
            <p class="dashboard-muted">
                <?= htmlspecialchars($textoSubtitulo) ?>
            </p>
        </section>

        <section class="dashboard-card">

            <?php if ($flashSuccess): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
            <?php endif; ?>

            <div class="productos-header-actions">
                <a href="nuevo_cliente.php" class="btn-primary-inline">
                    + Agregar nuevo cliente
                </a>
            </div>

            <!-- FILTROS -->
            <form method="get" class="proveedores-filtros-bar" style="margin-top:14px; margin-bottom:16px;">
                <div class="filtro-item-full">
                    <label for="q" class="label">Buscar cliente</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="input"
                        placeholder="Nombre, apellido, teléfono, dirección o identificación..."
                        value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <div class="filtro-item">
                    <label for="tipo" class="label">Tipo de cliente</label>
                    <select
                        id="tipo"
                        name="tipo"
                        class="input"
                        <?= $soloDetallista ? 'disabled' : '' ?>>
                        <option value="">Todos</option>
                        <option value="Mayorista" <?= $tipoFiltro === 'Mayorista' ? 'selected' : '' ?>>Mayorista</option>
                        <option value="Detallista" <?= $tipoFiltro === 'Detallista' ? 'selected' : '' ?>>Detallista</option>
                    </select>
                    <?php if ($soloDetallista): ?>
                        <small class="dashboard-muted" style="font-size:12px;display:block;margin-top:4px;">
                            Su rol solo permite ver clientes de tipo Detallista.
                        </small>
                    <?php endif; ?>
                </div>

                <div class="filtro-actions">
                    <button type="submit" class="btn-primary-inline">
                        Aplicar filtros
                    </button>
                    <a href="clientes.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>

            <div class="table-wrapper">
                <table class="table-products">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Identificación</th>
                            <th>Tipo</th>
                            <th class="col-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                            <tr>
                                <td colspan="8" class="dashboard-muted">
                                    No se encontraron clientes con los filtros aplicados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clientes as $c): ?>
                                <tr>
                                    <td><?= (int)$c["id_cliente"] ?></td>
                                    <td><?= htmlspecialchars($c["nombres"]) ?></td>
                                    <td><?= htmlspecialchars($c["apellidos"]) ?></td>
                                    <td><?= htmlspecialchars($c["telefono"] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($c["direccion"] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($c["identificacion"] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($c["tipo_cliente"]) ?></td>
                                    <td class="acciones">
                                        <a
                                            href="editar_cliente.php?id=<?= (int)$c['id_cliente'] ?>"
                                            class="btn-accion btn-accion-editar">
                                            Editar
                                        </a>

                                        <a
                                            href="eliminar_cliente.php?id=<?= (int)$c['id_cliente'] ?>"
                                            class="btn-accion btn-accion-eliminar"
                                            onclick="return confirm('¿Seguro que desea eliminar este cliente?');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>

</body>

</html>