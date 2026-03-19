<?php
// facturas.php
session_start();
$pageTitle = "Facturas - Panda Estampados / Kitsune";

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
$busqueda      = trim($_GET['q'] ?? "");
$seccionFiltro = $_GET['seccion'] ?? "";
$usuarioFiltro = $_GET['usuario'] ?? "";
$fechaDesde    = $_GET['desde'] ?? "";
$fechaHasta    = $_GET['hasta'] ?? "";

// normalizar ids
$seccionFiltroInt = ctype_digit($seccionFiltro) ? (int)$seccionFiltro : null;
$usuarioFiltroInt = ctype_digit($usuarioFiltro) ? (int)$usuarioFiltro : null;

/* ==============================
 * 2) CARGAR SECCIONES Y USUARIOS
 * ============================== */

// Para supervisores y facturadores SOLO mostramos la sección Kitsune en el filtro
if ($idRol === 2 || $idRol === 3) {
    $stmtSec = $connection->prepare("
        SELECT id_seccion, nombre
        FROM Seccion
        WHERE nombre = 'Kitsune'
        ORDER BY nombre
    ");
    $stmtSec->execute();
    $secciones = $stmtSec->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Admin ve todas las secciones
    $stmtSec = $connection->query("
        SELECT id_seccion, nombre
        FROM Seccion
        ORDER BY nombre
    ");
    $secciones = $stmtSec->fetchAll(PDO::FETCH_ASSOC);
}

$stmtUsu = $connection->query("SELECT id_usuario, nombre FROM Usuario ORDER BY nombre");
$usuariosFiltro = $stmtUsu->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
 * 3) OBTENER FACTURAS CON FILTRO
 * ============================== */

$sql = "
    SELECT 
        f.id_factura,
        f.fecha,
        f.total,
        c.nombres || ' ' || c.apellidos AS cliente,
        u.nombre AS usuario,
        s.nombre AS seccion
    FROM Factura f
    JOIN Cliente  c ON f.id_cliente = c.id_cliente
    JOIN Usuario  u ON f.id_usuario = u.id_usuario
    JOIN Seccion  s ON f.id_seccion = s.id_seccion
    WHERE 1 = 1
";

$params = [];

// Restricción por rol:
//  - Supervisor / Facturador: SOLO clientes Detallista y SOLO sección Kitsune
if ($idRol === 2 || $idRol === 3) {
    $sql .= " AND c.tipo_cliente = 'Detallista' AND s.nombre = 'Kitsune'";
}

// filtro texto: cliente, usuario o id de factura
if ($busqueda !== "") {
    $sql .= "
        AND (
            (c.nombres || ' ' || c.apellidos) ILIKE :q
            OR u.nombre ILIKE :q
            OR CAST(f.id_factura AS TEXT) ILIKE :q
        )
    ";
    $params[':q'] = '%' . $busqueda . '%';
}

// filtro sección (para admin; para los otros ya está forzado a Kitsune arriba)
if (!is_null($seccionFiltroInt)) {
    $sql .= " AND f.id_seccion = :sec";
    $params[':sec'] = $seccionFiltroInt;
}

// filtro usuario
if (!is_null($usuarioFiltroInt)) {
    $sql .= " AND f.id_usuario = :usr";
    $params[':usr'] = $usuarioFiltroInt;
}

// filtro fecha desde
if ($fechaDesde !== "") {
    // se espera formato YYYY-MM-DD
    $sql .= " AND f.fecha >= :desde";
    $params[':desde'] = $fechaDesde . " 00:00:00";
}

// filtro fecha hasta
if ($fechaHasta !== "") {
    $sql .= " AND f.fecha <= :hasta";
    $params[':hasta'] = $fechaHasta . " 23:59:59";
}

$sql .= " ORDER BY f.fecha DESC, f.id_factura DESC";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ============================
 * Texto del subtítulo por rol
 * ============================ */
if ($idRol === 1) {
    $textoSubtitulo = "Revise todas las facturas emitidas y cómo se registraron las ventas de Panda Estampados y Kitsune.";
} else {
    $textoSubtitulo = "Revise todas las facturas emitidas y cómo se registraron las ventas de Kitsune.";
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Facturación</p>
            <h1 class="dashboard-title">Historial de facturas</h1>
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
                <a href="nueva_factura.php" class="btn-primary-inline">
                    + Nueva factura
                </a>
            </div>

            <!-- FILTROS DE FACTURAS -->
            <form method="get" class="proveedores-filtros-bar" style="margin-top:14px; margin-bottom:16px;">
                <div class="filtro-item-full">
                    <label for="q" class="label">Buscar</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="input"
                        placeholder="Cliente, usuario o ID de factura..."
                        value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <div class="filtro-item">
                    <label for="seccion" class="label">Sección</label>
                    <select id="seccion" name="seccion" class="input">
                        <option value="">Todas</option>
                        <?php foreach ($secciones as $sec): ?>
                            <option
                                value="<?= (int)$sec['id_seccion'] ?>"
                                <?= ($seccionFiltroInt === (int)$sec['id_seccion']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sec['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="usuario" class="label">Usuario</label>
                    <select id="usuario" name="usuario" class="input">
                        <option value="">Todos</option>
                        <?php foreach ($usuariosFiltro as $u): ?>
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
                    <a href="facturas.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>

            <div class="table-wrapper">
                <table class="table-products">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Sección</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th class="col-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($facturas)): ?>
                            <tr>
                                <td colspan="7" class="dashboard-muted">
                                    No se encontraron facturas con los filtros aplicados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($facturas as $fac): ?>
                                <tr>
                                    <td><?= (int)$fac["id_factura"] ?></td>
                                    <td><?= date("d/m/Y H:i", strtotime($fac["fecha"])) ?></td>
                                    <td><?= htmlspecialchars($fac["cliente"]) ?></td>
                                    <td><?= htmlspecialchars($fac["seccion"]) ?></td>
                                    <td><?= htmlspecialchars($fac["usuario"]) ?></td>
                                    <td>C$ <?= number_format((float)$fac["total"], 2) ?></td>
                                    <td class="acciones">
                                        <a
                                            href="factura_detalle.php?id=<?= (int)$fac['id_factura'] ?>"
                                            class="btn-accion btn-accion-detalle">
                                            Ver detalles
                                        </a>

                                        <a
                                            href="eliminar_factura.php?id=<?= (int)$fac['id_factura'] ?>"
                                            class="btn-accion btn-accion-eliminar"
                                            onclick="return confirm('¿Seguro que desea eliminar esta factura?');">
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