<?php

session_start();

$pageTitle = "Detalle del producto - Panda Estampados / Kitsune";

require_once __DIR__ . "/controllers/catalogo_controller.php";

use App\Repository\CatalogoRepository;

$idProductoRaw = $_GET["id"] ?? "";

if (!ctype_digit($idProductoRaw)) {
    header("Location: index.php");
    exit();
}

$connection = createConnection();
$catalogoRepo = new CatalogoRepository($connection);
$producto = $catalogoRepo->obtenerDetalleProducto((int) $idProductoRaw);

if ($producto === null) {
    header("Location: index.php");
    exit();
}

$numeroWhatsApp = obtenerNumeroWhatsAppCatalogo();
$stock = (int) $producto["stock"];
$stockClass = obtenerClaseStockCatalogo($stock);
$stockLabel = obtenerTextoStockCatalogo($stock);
$urlWhatsApp = obtenerUrlWhatsAppCatalogo($numeroWhatsApp, $producto);

$rutaImagen = __DIR__ . "/uploads/productos/" . ($producto["imagen"] ?? "");
$tieneImagen = !empty($producto["imagen"]) && is_file($rutaImagen);

$descripcionCompleta = trim((string) ($producto["descripcion"] ?? ""));
if ($descripcionCompleta === "") {
    $descripcionCompleta = "Sin descripción disponible.";
}

$filtroCategoria = $_GET["categoria"] ?? "";
$filtroUrl = $filtroCategoria !== "" ? "?categoria=" . urlencode($filtroCategoria) : "index.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto["nombre"]) ?> - Catálogo</title>

    <?php require __DIR__ . "/partials/inicio-publico/catalogo/styles.php"; ?>

    <style>
        .detail-container {
            width: min(1000px, calc(100% - 40px));
            margin: 0 auto;
            padding: 28px 0 44px;
        }

        .detail-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
            color: #2563eb;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
        }

        .detail-back:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .detail-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .detail-image-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .detail-image-main {
            width: 100%;
            aspect-ratio: 1;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            display: grid;
            place-items: center;
        }

        .detail-image-main img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 16px;
        }

        .detail-image-placeholder {
            color: #94a3b8;
            font-size: 1rem;
            font-weight: 700;
        }

        .detail-info {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .detail-category {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-size: 0.82rem;
            font-weight: 800;
            width: fit-content;
        }

        .detail-name {
            margin: 0;
            color: #0f172a;
            font-size: 1.6rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .detail-code {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .detail-price {
            color: #111827;
            font-size: 1.5rem;
            font-weight: 900;
        }

        .detail-stock {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 0.85rem;
            font-weight: 900;
            width: fit-content;
        }

        .detail-description-title {
            margin: 0;
            color: #0f172a;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .detail-description {
            margin: 0;
            color: #4b5563;
            font-size: 0.95rem;
            line-height: 1.65;
        }

        .detail-actions {
            display: flex;
            gap: 12px;
            margin-top: auto;
            padding-top: 8px;
        }

        .detail-wa-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
            padding: 0 24px;
            border-radius: 12px;
            background: #16a34a;
            color: #ffffff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 900;
            flex: 1;
            transition:
                background 0.15s ease,
                transform 0.15s ease,
                box-shadow 0.15s ease;
        }

        .detail-wa-btn:hover {
            background: #15803d;
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(22, 163, 74, 0.2);
        }

        .detail-wa-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 24px;
            border-radius: 12px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 1rem;
            font-weight: 900;
            flex: 1;
        }

        @media (max-width: 760px) {
            .detail-card {
                grid-template-columns: 1fr;
                padding: 18px;
            }

            .detail-name {
                font-size: 1.3rem;
            }

            .detail-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body class="catalog-public-body">

    <?php require __DIR__ . "/partials/inicio-publico/catalogo/navbar.php"; ?>

    <main class="detail-container">

        <a href="<?= htmlspecialchars($filtroUrl) ?>" class="detail-back">
            &larr; Volver al catálogo
        </a>

        <div class="detail-card">

            <div class="detail-image-section">
                <div class="detail-image-main">
                    <?php if ($tieneImagen): ?>
                        <img
                            src="uploads/productos/<?= htmlspecialchars($producto["imagen"]) ?>"
                            alt="<?= htmlspecialchars($producto["nombre"]) ?>">
                    <?php else: ?>
                        <span class="detail-image-placeholder">Sin imagen</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-info">
                <span class="detail-category">
                    <?= htmlspecialchars($producto["categoria"] ?? "Sin categoría") ?>
                </span>

                <h1 class="detail-name">
                    <?= htmlspecialchars($producto["nombre"]) ?>
                </h1>

                <div class="detail-code">
                    Código: <?= htmlspecialchars($producto["codigo"]) ?>
                </div>

                <div class="detail-price">
                    C$ <?= number_format((float) $producto["precio_venta"], 2) ?>
                </div>

                <span class="detail-stock <?= htmlspecialchars($stockClass) ?>">
                    <?= htmlspecialchars($stockLabel) ?>
                </span>

                <div>
                    <h2 class="detail-description-title">Descripción</h2>
                    <p class="detail-description">
                        <?= nl2br(htmlspecialchars($descripcionCompleta)) ?>
                    </p>
                </div>

                <div class="detail-actions">
                    <?php if ($stock <= 0): ?>
                        <span class="detail-wa-disabled">Producto agotado</span>
                    <?php elseif ($urlWhatsApp === ""): ?>
                        <span class="detail-wa-disabled">WhatsApp no configurado</span>
                    <?php else: ?>
                        <a
                            href="<?= htmlspecialchars($urlWhatsApp) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="detail-wa-btn">
                            Consultar por WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </main>

</body>

</html>
