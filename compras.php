<?php
session_start();
$pageTitle = "Historial de compras - Panda Estampados / Kitsune";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
/** @var PDO $connection */
$connection = require "./sql/db.php";

// Mensaje flash opcional
$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error   = $_SESSION["flash_error"]   ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

/* ==============================
 * 1) LECTURA DE FILTROS (GET)
 * ============================== */
$busqueda        = trim($_GET['q'] ?? "");
$proveedorFiltro = $_GET['proveedor'] ?? "";
$usuarioFiltro   = $_GET['usuario'] ?? "";
$fechaDesde      = $_GET['desde'] ?? "";
$fechaHasta      = $_GET['hasta'] ?? "";

// normalizar ids
$proveedorFiltroInt = ctype_digit($proveedorFiltro) ? (int)$proveedorFiltro : null;
$usuarioFiltroInt   = ctype_digit($usuarioFiltro) ? (int)$usuarioFiltro   : null;

/* ==============================
 * 2) CARGAR PROVEEDORES Y USUARIOS
 * ============================== */
$stmtProv = $connection->query("SELECT id_proveedor, nombre FROM Proveedor ORDER BY nombre");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

$stmtUsu = $connection->query("SELECT id_usuario, nombre FROM Usuario ORDER BY nombre");
$usuarios = $stmtUsu->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
 * 3) OBTENER COMPRAS CON FILTRO
 * ============================== */

$sql = "
    SELECT c.id_compra,
           c.fecha,
           c.total,
           p.nombre AS proveedor,
           u.nombre AS usuario
    FROM Compra c
    INNER JOIN Proveedor p ON c.id_proveedor = p.id_proveedor
    INNER JOIN Usuario   u ON c.id_usuario   = u.id_usuario
    WHERE 1 = 1
";

$params = [];

// filtro texto: proveedor, usuario o id de compra
if ($busqueda !== "") {
    $sql .= "
        AND (
            p.nombre ILIKE :q
            OR u.nombre ILIKE :q
            OR CAST(c.id_compra AS TEXT) ILIKE :q
        )
    ";
    $params[':q'] = '%' . $busqueda . '%';
}

// filtro proveedor
if (!is_null($proveedorFiltroInt)) {
    $sql .= " AND c.id_proveedor = :prov";
    $params[':prov'] = $proveedorFiltroInt;
}

// filtro usuario
if (!is_null($usuarioFiltroInt)) {
    $sql .= " AND c.id_usuario = :usr";
    $params[':usr'] = $usuarioFiltroInt;
}

// filtro fecha desde
if ($fechaDesde !== "") {
    $sql .= " AND c.fecha >= :desde";
    $params[':desde'] = $fechaDesde . " 00:00:00";
}

// filtro fecha hasta
if ($fechaHasta !== "") {
    $sql .= " AND c.fecha <= :hasta";
    $params[':hasta'] = $fechaHasta . " 23:59:59";
}

$sql .= " ORDER BY c.fecha DESC";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <!-- NAVBAR -->
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="dashboard-container">

        <!-- Cabecera -->
        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Inventario</p>
            <h1 class="dashboard-title">Historial de compras</h1>
            <p class="dashboard-muted">
                Revise todas las compras realizadas y cómo se actualizó el inventario de Panda Estampados y Kitsune.
            </p>
        </section>

        <!-- Tabla de compras -->
        <section class="dashboard-card">

            <?php if ($flash_error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>

            <?php if ($flash_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
            <?php endif; ?>

            <!-- FILTROS -->
            <form method="get" class="proveedores-filtros-bar" style="margin-bottom:16px;">
                <div class="filtro-item-full">
                    <label for="q" class="label">Buscar</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="input"
                        placeholder="Proveedor, usuario o ID de compra..."
                        value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <div class="filtro-item">
                    <label for="proveedor" class="label">Proveedor</label>
                    <select id="proveedor" name="proveedor" class="input">
                        <option value="">Todos</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option
                                value="<?= (int)$prov['id_proveedor'] ?>"
                                <?= ($proveedorFiltroInt === (int)$prov['id_proveedor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prov['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="usuario" class="label">Usuario</label>
                    <select id="usuario" name="usuario" class="input">
                        <option value="">Todos</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option
                                value="<?= (int)$u['id_usuario'] ?>"
                                <?= ($usuarioFiltroInt === (int)$u['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="desde" class="label">Desde</label>
                    <input
                        type="date"
                        id="desde"
                        name="desde"
                        class="input"
                        value="<?= htmlspecialchars($fechaDesde) ?>">
                </div>

                <div class="filtro-item">
                    <label for="hasta" class="label">Hasta</label>
                    <input
                        type="date"
                        id="hasta"
                        name="hasta"
                        class="input"
                        value="<?= htmlspecialchars($fechaHasta) ?>">
                </div>

                <div class="filtro-actions">
                    <button type="submit" class="btn-primary-inline">
                        Aplicar filtros
                    </button>
                    <a href="historial_compras.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>

            <?php if (empty($compras)): ?>
                <p class="dashboard-muted">No se encontraron compras con los filtros aplicados.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-products">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Usuario</th>
                                <th>Total</th>
                                <th class="col-acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($compras as $comp): ?>
                                <tr>
                                    <td><?= (int)$comp['id_compra'] ?></td>
                                    <td><?= date("d/m/Y H:i", strtotime($comp['fecha'])) ?></td>
                                    <td><?= htmlspecialchars($comp['proveedor']) ?></td>
                                    <td><?= htmlspecialchars($comp['usuario']) ?></td>
                                    <td>C$ <?= number_format((float)$comp['total'], 2) ?></td>
                                    <td class="acciones">
                                        <a
                                            href="detalle_compra.php?id=<?= (int)$comp['id_compra'] ?>"
                                            class="btn-accion btn-accion-editar">
                                            Ver detalles
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </section>

    </main>

</body>

</html>