<?php
session_start();
$pageTitle = "Catálogo de productos - Panda Estampados / Kitsune";

/** @var PDO $connection */
$connection = require "./sql/db.php";

// Número de WhatsApp de la tienda (se lee desde numero_de_whatsapp.txt)
$numeroWhatsApp = "";
$archivoWhatsApp = __DIR__ . "/numero_de_whatsapp.txt";

if (is_readable($archivoWhatsApp)) {
    $contenido = trim(file_get_contents($archivoWhatsApp));
    if ($contenido !== "") {
        $numeroWhatsApp = preg_replace("/\D+/", "", $contenido);
    }
}

// 1) Leer filtros desde GET
$busquedaTexto  = trim($_GET['q'] ?? '');
$filtroCategoria = $_GET['categoria'] ?? '';

$filtroCategoria = ctype_digit($filtroCategoria) ? (int)$filtroCategoria : null;

// 2) Cargar categorías
$stmtCat = $connection->query("
    SELECT id_categoria, nombre
    FROM Categoria
    ORDER BY nombre
");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// 3) Consultar productos con filtros
$sql = "
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.imagen,
        c.nombre   AS categoria,
        p.precio_venta,
        p.stock
    FROM Producto p
    LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
    WHERE 1=1
";

$params = [];

// filtro texto
if ($busquedaTexto !== '') {
    $sql .= " AND (p.codigo ILIKE :q OR p.nombre ILIKE :q)";
    $params[':q'] = '%' . $busquedaTexto . '%';
}

// filtro categoría
if (!is_null($filtroCategoria)) {
    $sql .= " AND p.id_categoria = :cat";
    $params[':cat'] = $filtroCategoria;
}

$sql .= " ORDER BY p.nombre";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Saber si hay sesión de trabajador
$usuario = $_SESSION["user"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<style>
    /* Barra superior */
    .public-navbar {
        width: 100%;
        padding: 12px 32px;
        display: flex;
        justify-content: center;
        background: #020617;
        color: #e5e7eb;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.5);
    }

    .public-navbar-inner {
        width: 100%;
        max-width: 1180px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .public-navbar-brand {
        font-weight: 700;
        letter-spacing: .04em;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .public-navbar-logo {
        height: 26px;
        width: 26px;
        border-radius: 50%;
        object-fit: cover;
        display: inline-block;
    }

    .public-navbar span.brand-separator {
        opacity: .6;
        margin: 0 4px;
    }

    .public-navbar a {
        text-decoration: none;
        color: #e5e7eb;
    }

    .public-navbar .btn-login {
        padding: 8px 16px;
        border-radius: 999px;
        background: #2563eb;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .public-navbar .btn-login:hover {
        background: #1d4ed8;
    }

    /* Contenedor catálogo */
    .catalog-container {
        max-width: 1180px;
        margin: 0 auto;
    }

    /* Hero */
    .hero-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .hero-text {
        flex: 1;
    }

    .hero-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    .hero-login-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border-radius: 999px;
        background: #2563eb;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
    }

    .hero-login-btn:hover {
        background: #1d4ed8;
    }

    .hero-login-caption {
        font-size: 0.78rem;
        color: #64748b;
        text-align: right;
    }

    /* Grid de productos */
    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 18px;
    }

    .catalog-card {
        background: radial-gradient(circle at top left, #f4f4ff 0, #ffffff 45%, #f9fafb 100%);
        border-radius: 18px;
        padding: 16px 16px 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14);
        display: flex;
        flex-direction: column;
        min-height: 270px;
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .catalog-card-header {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .catalog-card-img {
        width: 86px;
        height: 86px;
        border-radius: 16px;
        overflow: hidden;
        flex-shrink: 0;
        background: radial-gradient(circle at 30% 30%, #1e293b, #020617);
    }

    .catalog-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .catalog-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .catalog-card-code {
        font-size: 0.8rem;
        color: #9ca3af;
    }

    .catalog-card-body {
        margin-top: 10px;
        font-size: 0.86rem;
        color: #4b5563;
        flex-grow: 1;
        line-height: 1.35;
    }

    .catalog-card-body p {
        margin: 0 0 8px;
    }

    .catalog-meta {
        font-size: 0.8rem;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .catalog-meta strong {
        color: #111827;
    }

    .catalog-price {
        font-size: 0.95rem;
        font-weight: 700;
        margin-top: 4px;
        color: #111827;
    }

    .catalog-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        gap: 8px;
    }

    .catalog-stock {
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 600;
    }

    .catalog-stock.stock-bajo {
        background: #fee2e2;
        color: #b91c1c;
    }

    .catalog-stock.stock-agotado {
        background: #e5e7eb;
        color: #4b5563;
    }

    .btn-wa {
        font-size: 0.82rem;
        padding: 7px 12px;
        border-radius: 999px;
        border: none;
        text-decoration: none;
        background: #22c55e;
        color: #ffffff;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
        box-shadow: 0 6px 16px rgba(34, 197, 94, 0.4);
    }

    .btn-wa:hover {
        background: #16a34a;
    }

    .btn-wa span.icon {
        font-size: 1.05rem;
    }

    @media (max-width: 768px) {
        .hero-flex {
            flex-direction: column;
            align-items: flex-start;
        }

        .hero-actions {
            align-items: flex-start;
        }

        .hero-login-caption {
            text-align: left;
        }
    }

    @media (max-width: 640px) {
        .catalog-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<body class="page-bg">

    <!-- NAVBAR pública -->
    <header class="public-navbar">
        <div class="public-navbar-inner">
            <div class="public-navbar-brand">
                <img src="icono.png" alt="Logo" class="public-navbar-logo">
                <span>Panda Estampados</span>
                <span class="brand-separator">/</span>
                <span>Kitsune</span>
            </div>

            <div>
                <?php if ($usuario): ?>
                    <span style="font-size:0.8rem; margin-right:8px;">
                        Sesión: <?= htmlspecialchars($usuario["nombre"] ?? "Usuario") ?>
                    </span>
                    <a href="dashboard.php" class="btn-login">
                        Panel de gestión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="dashboard-container catalog-container">

        <!-- Hero / título -->
        <section class="dashboard-card dashboard-welcome">
            <div class="hero-flex">
                <div class="hero-text">
                    <p class="dashboard-eyebrow">CATÁLOGO</p>
                    <h1 class="dashboard-title">Productos disponibles</h1>
                    <p class="dashboard-muted">
                        Explora los productos de Panda Estampados y Kitsune. Puedes buscar por nombre, código o categoría
                        y consultar por WhatsApp para más información o pedidos personalizados.
                    </p>
                </div>

                <div class="hero-actions">
                    <?php if ($usuario): ?>
                        <a href="dashboard.php" class="hero-login-btn">
                            Ir al panel de trabajadores
                        </a>
                        <span class="hero-login-caption">
                            Ya tienes sesión iniciada.
                        </span>
                    <?php else: ?>
                        <a href="login.php" class="hero-login-btn">
                            Acceso trabajadores
                        </a>
                        <span class="hero-login-caption">
                            Solo para personal interno de la empresa.
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="dashboard-card">

            <!-- FILTROS PÚBLICOS -->
            <form method="get" class="productos-filtros-bar">
                <div class="filtro-item">
                    <label for="q" class="label">Buscar</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        placeholder="Nombre del producto..."
                        value="<?= htmlspecialchars($busquedaTexto) ?>"
                        class="input">
                </div>

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

                <div class="filtro-actions">
                    <button type="submit" class="btn-primary-inline">
                        Aplicar filtros
                    </button>
                    <a href="index.php" class="btn-secondary-inline">
                        Limpiar
                    </a>
                </div>
            </form>

            <?php if (empty($productos)): ?>
                <p class="dashboard-muted" style="margin-top: 1rem;">
                    No se encontraron productos con los filtros seleccionados.
                </p>
            <?php else: ?>

                <div class="catalog-grid">
                    <?php foreach ($productos as $prod): ?>
                        <?php
                        $desc = trim($prod['descripcion'] ?? '');
                        if ($desc === '') {
                            $desc = 'Sin descripción disponible.';
                        }
                        if (strlen($desc) > 150) {
                            $desc = substr($desc, 0, 147) . '...';
                        }

                        $stock = (int)$prod["stock"];
                        $stockClass = "";
                        $stockLabel = "Stock: $stock";

                        if ($stock <= 0) {
                            $stockClass = "stock-agotado";
                            $stockLabel = "Agotado";
                        } elseif ($stock <= 5) {
                            $stockClass = "stock-bajo";
                        }

                        // Texto para WhatsApp
                        $textoWA = "Hola, estoy interesado en el producto "
                            . $prod["nombre"]
                            . " (código " . $prod["codigo"] . "). ¿Podrían brindarme más información?";
                        $urlWA = "https://wa.me/" . $numeroWhatsApp . "?text=" . urlencode($textoWA);
                        ?>
                        <article class="catalog-card">
                            <div class="catalog-card-header">
                                <div class="catalog-card-img">
                                    <?php if (!empty($prod['imagen'])): ?>
                                        <a href="ver_imagen_cliente.php?id=<?= (int)$prod['id_producto'] ?>" title="Ver imagen en grande">
                                            <img
                                                src="uploads/productos/<?= htmlspecialchars($prod['imagen']) ?>"
                                                alt="<?= htmlspecialchars($prod['nombre']) ?>"
                                                style="cursor: zoom-in;">
                                        </a>

                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="catalog-card-title">
                                        <?= htmlspecialchars($prod['nombre']) ?>
                                    </div>
                                    <div class="catalog-card-code">
                                        Código: <?= htmlspecialchars($prod['codigo']) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="catalog-card-body">
                                <p><?= htmlspecialchars($desc) ?></p>
                                <div class="catalog-meta">
                                    Categoría:
                                    <strong><?= htmlspecialchars($prod['categoria'] ?? 'Sin categoría') ?></strong>
                                </div>
                                <div class="catalog-price">
                                    Precio: C$ <?= number_format((float)$prod["precio_venta"], 2) ?>
                                </div>
                            </div>

                            <div class="catalog-card-footer">
                                <span class="catalog-stock <?= $stockClass ?>">
                                    <?= htmlspecialchars($stockLabel) ?>
                                </span>

                                <a href="<?= htmlspecialchars($urlWA) ?>" target="_blank" class="btn-wa">
                                    <span class="icon">💬</span>
                                    WhatsApp
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>