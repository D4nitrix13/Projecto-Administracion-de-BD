<?php
session_start();
$pageTitle = "Listado de productos - Panda Estampados / Kitsune";

// Si no hay usuario en sesión, regresar al login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user  = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

/** @var PDO $connection */
$connection = require "./sql/db.php";

// Mensajes flash (crear / editar / comprar / eliminar)
$flash_success = $_SESSION["flash_success"] ?? null;
$flash_error   = $_SESSION["flash_error"]   ?? null;
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

/* ============================================
 * 1) LEER FILTROS DESDE GET
 * ============================================ */
$busquedaTexto  = trim($_GET['q'] ?? '');
$filtroCategoria = $_GET['categoria'] ?? '';
$filtroProveedor = $_GET['proveedor'] ?? '';

/* Normalizamos a enteros cuando aplique */
$filtroCategoria = ctype_digit($filtroCategoria) ? (int)$filtroCategoria : null;
$filtroProveedor = ctype_digit($filtroProveedor) ? (int)$filtroProveedor : null;

/* ============================================
 * 2) CARGAR LISTAS DE CATEGORÍAS / PROVEEDORES
 * ============================================ */
$stmtCat = $connection->query("
    SELECT id_categoria, nombre
    FROM Categoria
    ORDER BY nombre
");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$stmtProv = $connection->query("
    SELECT id_proveedor, nombre
    FROM Proveedor
    ORDER BY nombre
");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

/* ============================================
 * 3) ARMAR CONSULTA DE PRODUCTOS CON FILTROS
 * ============================================ */

$sql = "
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.imagen,
        c.nombre   AS categoria,
        pr.nombre  AS proveedor,
        p.precio_compra,
        p.precio_venta,
        p.stock
    FROM Producto p
    LEFT JOIN Categoria c  ON p.id_categoria = c.id_categoria
    LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
    WHERE 1=1
";

$params = [];

// Filtro texto: busca en código y nombre
if ($busquedaTexto !== '') {
    $sql .= " AND (p.codigo ILIKE :q OR p.nombre ILIKE :q)";
    $params[':q'] = '%' . $busquedaTexto . '%';
}

// Filtro por categoría
if (!is_null($filtroCategoria)) {
    $sql .= " AND p.id_categoria = :cat";
    $params[':cat'] = $filtroCategoria;
}

// Filtro por proveedor
if (!is_null($filtroProveedor)) {
    $sql .= " AND p.id_proveedor = :prov";
    $params[':prov'] = $filtroProveedor;
}

$sql .= " ORDER BY p.nombre";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ============================================
 * 4) Texto del subtítulo según rol
 * ============================================ */
if ($idRol === 1) {
    $textoSubtitulo = "Consulte la información de cada producto y realice acciones de edición, compra de stock o eliminación.";
} elseif ($idRol === 2) {
    $textoSubtitulo = "Consulte la información de cada producto y realice acciones de edición o eliminación.";
} elseif ($idRol === 3) {
    $textoSubtitulo = "Consulte la información de cada producto (solo lectura, sin modificaciones).";
} else {
    $textoSubtitulo = "Consulte la información de cada producto.";
}
?>

<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<!-- Estilos específicos para la vista en tarjetas -->
<style>
    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 18px;
        margin-top: 16px;
    }

    .producto-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 14px 14px 12px;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        display: flex;
        flex-direction: column;
        min-height: 230px;
    }

    .producto-card-header {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .producto-card-img {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        background: radial-gradient(circle at 30% 30%, #1e293b, #020617);
    }

    .producto-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .producto-card-title {
        font-weight: 600;
        font-size: 0.98rem;
        margin-bottom: 2px;
        color: #0f172a;
    }

    .producto-card-sub {
        font-size: 0.78rem;
        color: #6b7280;
    }

    .producto-card-body {
        margin-top: 8px;
        font-size: 0.8rem;
        color: #4b5563;
        flex-grow: 1;
    }

    .producto-card-body p {
        margin: 0 0 6px;
    }

    .producto-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        font-size: 0.78rem;
        color: #6b7280;
    }

    .producto-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .producto-precios {
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .producto-precios strong {
        font-weight: 600;
    }

    .producto-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        gap: 8px;
    }

    .producto-stock-badge {
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e0f2fe;
        color: #0369a1;
    }

    .producto-stock-badge.stock-bajo {
        background: #fee2e2;
        color: #b91c1c;
    }

    .producto-card-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .btn-accion-xs {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 999px;
        border: none;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-accion-editar-xs {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .btn-accion-comprar-xs {
        background: #ecfdf3;
        color: #15803d;
    }

    .btn-accion-eliminar-xs {
        background: #fef2f2;
        color: #b91c1c;
    }

    .btn-accion-xs:hover {
        filter: brightness(0.95);
    }

    @media (max-width: 640px) {
        .productos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<body class="page-bg">

    <!-- NAVBAR -->
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Inventario</p>
            <h1 class="dashboard-title">Listado de productos</h1>
            <p class="dashboard-muted">
                <?= htmlspecialchars($textoSubtitulo) ?>
            </p>

            <?php if ($idRol !== 3): ?>
                <!-- Solo Admin y Supervisor pueden crear productos -->
                <div class="productos-header-actions">
                    <a href="nuevo_producto.php" class="btn-primary-inline">
                        + Agregar producto
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <section class="dashboard-card">

            <?php if ($flash_error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>

            <?php if ($flash_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
            <?php endif; ?>

            <!-- FILTROS -->
            <form method="get" class="productos-filtros-bar">
                <!-- Búsqueda texto -->
                <div class="filtro-item">
                    <label for="q" class="label">Buscar</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        placeholder="Código o nombre..."
                        value="<?= htmlspecialchars($busquedaTexto) ?>"
                        class="input">
                </div>

                <!-- Filtro categoría -->
                <div class="filtro-item">
                    <label for="categoria" class="label">Categoría</label>
                    <select
                        id="categoria"
                        name="categoria"
                        class="input">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option
                                value="<?= (int)$cat['id_categoria'] ?>"
                                <?= ($filtroCategoria === (int)$cat['id_categoria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtro proveedor -->
                <div class="filtro-item">
                    <label for="proveedor" class="label">Proveedor</label>
                    <select
                        id="proveedor"
                        name="proveedor"
                        class="input">
                        <option value="">Todos</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option
                                value="<?= (int)$prov['id_proveedor'] ?>"
                                <?= ($filtroProveedor === (int)$prov['id_proveedor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prov['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botones -->
                <div class="filtro-actions">
                    <button type="submit" class="btn-primary-inline">
                        Aplicar filtros
                    </button>
                    <a href="productos.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>


            <?php if (empty($productos)): ?>
                <p class="dashboard-muted" style="margin-top: 1rem;">
                    No se encontraron productos con los filtros aplicados.
                </p>
            <?php else: ?>

                <div class="productos-grid">
                    <?php foreach ($productos as $prod): ?>
                        <?php
                        $desc = trim($prod['descripcion'] ?? '');
                        if ($desc === '') {
                            $desc = 'Sin descripción disponible.';
                        }
                        if (strlen($desc) > 140) {
                            $desc = substr($desc, 0, 137) . '...';
                        }

                        $stockClass = ($prod["stock"] <= 5) ? 'stock-bajo' : '';
                        ?>
                        <article class="producto-card">
                            <div class="producto-card-header">
                                <div class="producto-card-img">
                                    <a href="ver_imagen.php?id=<?= (int)$prod['id_producto'] ?>">
                                        <img
                                            src="uploads/productos/<?= htmlspecialchars($prod['imagen']) ?>"
                                            alt="<?= htmlspecialchars($prod['nombre']) ?>"
                                            style="cursor: zoom-in;">
                                    </a>
                                </div>
                                <div>
                                    <div class="producto-card-title">
                                        <?= htmlspecialchars($prod['nombre']) ?>
                                    </div>
                                    <div class="producto-card-sub">
                                        Código: <?= htmlspecialchars($prod['codigo']) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="producto-card-body">
                                <p><?= htmlspecialchars($desc) ?></p>
                                <div class="producto-meta">
                                    <span><strong>Categoría:</strong> <?= htmlspecialchars($prod['categoria'] ?? 'Sin categoría') ?></span>
                                    <span><strong>Proveedor:</strong> <?= htmlspecialchars($prod['proveedor'] ?? 'Sin proveedor') ?></span>
                                </div>
                                <div class="producto-precios">
                                    <div><strong>Compra:</strong> C$ <?= number_format((float)$prod["precio_compra"], 2) ?></div>
                                    <div><strong>Venta:</strong> C$ <?= number_format((float)$prod["precio_venta"], 2) ?></div>
                                </div>
                            </div>

                            <div class="producto-card-footer">
                                <span class="producto-stock-badge <?= $stockClass ?>">
                                    Stock: <?= (int)$prod["stock"] ?>
                                </span>

                                <div class="producto-card-actions">
                                    <?php if ($idRol === 3): ?>
                                        <span class="dashboard-muted" style="font-size:0.78rem;">Solo lectura</span>
                                    <?php else: ?>
                                        <a
                                            href="editar_producto.php?id=<?= (int)$prod['id_producto'] ?>"
                                            class="btn-accion-xs btn-accion-editar-xs">
                                            Editar
                                        </a>

                                        <?php if ($idRol === 1): ?>
                                            <a
                                                href="comprar_producto.php?id=<?= (int)$prod['id_producto'] ?>"
                                                class="btn-accion-xs btn-accion-comprar-xs">
                                                Comprar
                                            </a>
                                        <?php endif; ?>

                                        <a
                                            href="eliminar_producto.php?id=<?= (int)$prod['id_producto'] ?>"
                                            class="btn-accion-xs btn-accion-eliminar-xs"
                                            onclick="return confirm('¿Seguro que desea eliminar este producto?');">
                                            Eliminar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>