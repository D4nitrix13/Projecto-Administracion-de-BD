<?php
session_start();
$pageTitle = "Vista de producto - Panda Estampados / Kitsune";

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


// ID de producto
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Cargar producto
$stmt = $connection->prepare("
    SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.imagen,
        p.precio_venta,
        p.stock,
        c.nombre AS categoria
    FROM Producto p
    LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
    WHERE p.id_producto = :id
");
$stmt->execute([":id" => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: index.php");
    exit();
}

// Datos para WhatsApp
$textoWA = "Hola, estoy interesado en el producto "
    . $producto["nombre"]
    . " (código " . $producto["codigo"] . "). ¿Podrían brindarme más información?";
$urlWA = "https://wa.me/" . $numeroWhatsApp . "?text=" . urlencode($textoWA);

// ¿Hay trabajador logueado?
$usuario = $_SESSION["user"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<style>
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

    .catalog-container {
        max-width: 1180px;
        margin: 0 auto;
    }

    .producto-view {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
        gap: 28px;
        margin-top: 16px;
        align-items: start;
    }

    .producto-view-image {
        background: radial-gradient(circle at top left, #0f172a, #020617);
        border-radius: 22px;
        padding: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 320px;
    }

    .producto-view-image img {
        max-width: 100%;
        max-height: 420px;
        border-radius: 18px;
        object-fit: contain;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.75);
    }

    .producto-view-info h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .producto-code {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 10px;
    }

    .producto-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        background: #e0f2fe;
        color: #0369a1;
        margin-bottom: 12px;
    }

    .producto-desc {
        font-size: 0.9rem;
        color: #4b5563;
        margin-bottom: 14px;
        line-height: 1.5;
    }

    .producto-price {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #111827;
    }

    .producto-stock {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        display: inline-block;
        margin-bottom: 18px;
        font-weight: 600;
    }

    .producto-stock.stock-bajo {
        background: #fee2e2;
        color: #b91c1c;
    }

    .producto-stock.stock-agotado {
        background: #e5e7eb;
        color: #4b5563;
    }

    .producto-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 4px;
    }

    .btn-wa-big {
        font-size: 0.9rem;
        padding: 9px 16px;
        border-radius: 999px;
        border: none;
        text-decoration: none;
        background: #22c55e;
        color: #ffffff;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 10px 25px rgba(34, 197, 94, 0.5);
    }

    .btn-wa-big:hover {
        background: #16a34a;
    }

    .btn-wa-big span.icon {
        font-size: 1.2rem;
    }

    .btn-back {
        font-size: 0.85rem;
        padding: 8px 14px;
        border-radius: 999px;
        background: #e5e7eb;
        color: #111827;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background: #d1d5db;
    }

    @media (max-width: 900px) {
        .producto-view {
            grid-template-columns: 1fr;
        }

        .producto-view-image {
            order: -1;
        }
    }
</style>

<body class="page-bg">

    <!-- NAVBAR pública -->
    <header class="public-navbar">
        <div class="public-navbar-inner">
            <div class="public-navbar-brand">
                Panda Estampados <span class="brand-separator">/</span> Kitsune
            </div>

            <div>
                <?php if ($usuario): ?>
                    <span style="font-size:0.8rem; margin-right:8px;">
                        Sesión: <?= htmlspecialchars($usuario["nombre"] ?? "Usuario") ?>
                    </span>
                    <a href="dashboard.php" class="btn-login">
                        Panel de gestión
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">
                        Acceso trabajadores
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- CONTENIDO -->
    <main class="dashboard-container catalog-container">

        <section class="dashboard-card">
            <a href="index.php" class="btn-back">
                ← Volver al catálogo
            </a>

            <div class="producto-view">

                <!-- IMAGEN GRANDE -->
                <div class="producto-view-image">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img
                            src="uploads/productos/<?= htmlspecialchars($producto['imagen']) ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>">
                    <?php else: ?>
                        <p class="dashboard-muted">Este producto no tiene imagen registrada.</p>
                    <?php endif; ?>
                </div>

                <!-- INFO DEL PRODUCTO -->
                <div class="producto-view-info">
                    <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
                    <div class="producto-code">
                        Código: <?= htmlspecialchars($producto['codigo']) ?>
                    </div>

                    <div class="producto-chip">
                        <?= htmlspecialchars($producto['categoria'] ?? 'Sin categoría') ?>
                    </div>

                    <p class="producto-desc">
                        <?= htmlspecialchars($producto['descripcion'] ?: 'Sin descripción disponible.') ?>
                    </p>

                    <div class="producto-price">
                        Precio: C$ <?= number_format((float)$producto["precio_venta"], 2) ?>
                    </div>

                    <?php
                    $stock = (int)$producto["stock"];
                    $stockClass = "";
                    $stockLabel = "Stock: $stock";

                    if ($stock <= 0) {
                        $stockClass = "stock-agotado";
                        $stockLabel = "Agotado";
                    } elseif ($stock <= 5) {
                        $stockClass = "stock-bajo";
                    }
                    ?>
                    <div class="producto-stock <?= $stockClass ?>">
                        <?= htmlspecialchars($stockLabel) ?>
                    </div>

                    <div class="producto-actions">
                        <a href="<?= htmlspecialchars($urlWA) ?>" target="_blank" class="btn-wa-big">
                            <span class="icon">💬</span>
                            Consultar por WhatsApp
                        </a>
                    </div>
                </div>

            </div>
        </section>

    </main>

</body>

</html>