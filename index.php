<?php

session_start();

$pageTitle = "Catálogo de productos - Panda Estampados / Kitsune";

require_once __DIR__ . "/controllers/catalogo_controller.php";

$viewData = obtenerDatosCatalogo();

$usuario                = $viewData["usuario"];
$categorias             = $viewData["categorias"];
$productos              = $viewData["productos"];
$numeroWhatsApp         = $viewData["numeroWhatsApp"];
$busquedaTexto          = $viewData["busquedaTexto"];
$filtroCategoria        = $viewData["filtroCategoria"];
$filtroDisponibilidad   = $viewData["filtroDisponibilidad"];

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/header.php"; ?>
<?php require __DIR__ . "/partials/catalogo/styles.php"; ?>

<body class="catalog-public-body">

    <?php require __DIR__ . "/partials/catalogo/navbar.php"; ?>

    <main class="catalog-container">

        <?php require __DIR__ . "/partials/catalogo/hero.php"; ?>

        <section class="catalog-panel">

            <?php require __DIR__ . "/partials/catalogo/filters.php"; ?>

            <div class="catalog-results-header">
                <div>
                    <h2>Catálogo</h2>
                    <p>Resultados según los filtros seleccionados.</p>
                </div>

                <span class="catalog-count">
                    <?= count($productos) ?> producto(s)
                </span>
            </div>

            <?php require __DIR__ . "/partials/catalogo/grid.php"; ?>

        </section>

    </main>

</body>

</html>