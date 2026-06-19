<?php
/** @var array $paginacion Resultado de calcularPaginacion() */
/** @var string $baseUrl URL base para construir links (ej: "facturas.php") */
/** @var array $filtrosActuales Filtros GET actuales para preservar en links */

$paginacion = $paginacion ?? [];
$baseUrl = $baseUrl ?? "";
$filtrosActuales = $filtrosActuales ?? [];

if (empty($paginacion) || ($paginacion["totalPaginas"] ?? 0) <= 1) {
    return;
}

$paginaActual = $paginacion["paginaActual"];
$totalPaginas = $paginacion["totalPaginas"];
$totalRegistros = $paginacion["totalRegistros"];
$tieneAnterior = $paginacion["tieneAnterior"];
$tieneSiguiente = $paginacion["tieneSiguiente"];
$paginas = $paginacion["paginas"];
?>

<style>
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .pagination-info {
        color: #6b7280;
        font-size: 0.86rem;
        font-weight: 600;
    }

    .pagination-nav {
        display: flex;
        align-items: center;
        gap: 4px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pagination-nav li a,
    .pagination-nav li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #ffffff;
        color: #374151;
        font-size: 0.86rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .pagination-nav li a:hover {
        background: #f3f4f6;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .pagination-nav li.active span {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    .pagination-nav li.disabled span {
        color: #d1d5db;
        border-color: #f3f4f6;
        background: #f9fafb;
        cursor: not-allowed;
    }

    .pagination-nav li.ellipsis span {
        border: none;
        background: transparent;
        color: #9ca3af;
        cursor: default;
        min-width: 24px;
    }

    .pagination-nav .pagination-arrow {
        font-size: 0.82rem;
        font-weight: 800;
    }

    @media (max-width: 640px) {
        .pagination-wrapper {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<?php
$prevUrl = $tieneAnterior
    ? construirUrlPagina($baseUrl, $filtrosActuales, $paginaActual - 1)
    : "#";

$nextUrl = $tieneSiguiente
    ? construirUrlPagina($baseUrl, $filtrosActuales, $paginaActual + 1)
    : "#";
?>

<div class="pagination-wrapper">
    <span class="pagination-info">
        <?= number_format($totalRegistros) ?> registro<?= $totalRegistros !== 1 ? "s" : "" ?>
        — Página <?= $paginaActual ?> de <?= $totalPaginas ?>
    </span>

    <ul class="pagination-nav">
        <li class="<?= $tieneAnterior ? "" : "disabled" ?>">
            <a href="<?= $prevUrl ?>" class="pagination-arrow" <?= $tieneAnterior ? "" : "tabindex=\"-1\" aria-disabled=\"true\"" ?>>← Anterior</a>
        </li>

        <?php foreach ($paginas as $pagina): ?>
            <?php if ($pagina === "..."): ?>
                <li class="ellipsis"><span>…</span></li>
            <?php elseif ($pagina === $paginaActual): ?>
                <li class="active"><span><?= $pagina ?></span></li>
            <?php else: ?>
                <li>
                    <a href="<?= construirUrlPagina($baseUrl, $filtrosActuales, (int)$pagina) ?>">
                        <?= $pagina ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>

        <li class="<?= $tieneSiguiente ? "" : "disabled" ?>">
            <a href="<?= $nextUrl ?>" class="pagination-arrow" <?= $tieneSiguiente ? "" : "tabindex=\"-1\" aria-disabled=\"true\"" ?>>Siguiente →</a>
        </li>
    </ul>
</div>
