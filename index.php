<?php

session_start();

$pageTitle = "Catálogo de productos - Panda Estampados / Kitsune";

require_once __DIR__ . "/controllers/catalogo_controller.php";

$viewData = obtenerDatosCatalogo();

$usuario              = $viewData["usuario"];
$categorias           = $viewData["categorias"];
$productos            = $viewData["productos"];
$numeroWhatsApp       = $viewData["numeroWhatsApp"];
$busquedaTexto        = $viewData["busquedaTexto"];
$filtroCategoria      = $viewData["filtroCategoria"];
$filtroDisponibilidad = $viewData["filtroDisponibilidad"];

$totalProductos = count($productos);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <?php require __DIR__ . "/partials/catalogo/styles.php"; ?>
</head>

<body class="catalog-public-body">

    <?php require __DIR__ . "/partials/catalogo/navbar.php"; ?>

    <main class="catalog-container">

        <section class="catalog-hero">
            <div class="catalog-hero-copy">
                <p class="catalog-eyebrow">Catálogo público</p>

                <h1>Productos disponibles</h1>

                <p class="catalog-hero-text">
                    Explore los productos de Panda Estampados y Kitsune.
                    Filtre por nombre, código, categoría y disponibilidad,
                    y consulte directamente por WhatsApp.
                </p>
            </div>

            <div class="catalog-hero-info">
                <div class="catalog-hero-mini-card">
                    <span>Total de productos</span>
                    <strong><?= $totalProductos ?></strong>
                </div>

                <div class="catalog-hero-mini-card">
                    <span>Atención</span>
                    <strong>WhatsApp</strong>
                </div>

                <div class="catalog-hero-mini-card">
                    <span>Consulta</span>
                    <strong>Rápida</strong>
                </div>
            </div>
        </section>

        <section class="catalog-panel">

            <?php require __DIR__ . "/partials/catalogo/filters.php"; ?>

            <div class="catalog-results-header">
                <div>
                    <p class="catalog-section-eyebrow">Resultados</p>
                    <h2>Catálogo</h2>
                    <p>Productos disponibles según los filtros seleccionados.</p>
                </div>

                <span class="catalog-count">
                    <?= $totalProductos ?> producto(s)
                </span>
            </div>

            <?php require __DIR__ . "/partials/catalogo/grid.php"; ?>

        </section>

    </main>

</body>

</html>