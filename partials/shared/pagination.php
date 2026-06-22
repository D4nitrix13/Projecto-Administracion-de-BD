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
        gap: 16px;
        margin-top: 28px;
        margin-bottom: 12px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .pagination-info {
        color: #475569;
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .pagination-info strong {
        color: #1e293b;
        font-weight: 800;
    }

    .pagination-nav {
        display: flex;
        align-items: center;
        gap: 6px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pagination-nav li a,
    .pagination-nav li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 10px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        color: #475569;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .pagination-nav li a:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .pagination-nav li.active span {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border-color: transparent;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        transform: translateY(-1px);
    }

    .pagination-nav li.disabled span {
        color: #cbd5e1;
        border-color: #f1f5f9;
        background: #f8fafc;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    .pagination-nav li.ellipsis span {
        border: none;
        background: transparent;
        color: #94a3b8;
        cursor: default;
        min-width: 28px;
        font-weight: 400;
        letter-spacing: 2px;
    }

    .pagination-nav .pagination-arrow {
        font-size: 0.84rem;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    @media (max-width: 640px) {
        .pagination-wrapper {
            flex-direction: column;
            align-items: center;
            padding: 16px;
        }

        .pagination-nav {
            gap: 4px;
        }

        .pagination-nav li a,
        .pagination-nav li span {
            min-width: 36px;
            height: 36px;
            font-size: 0.82rem;
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
