<?php if ($totalPaginas > 1): ?>
    <?php
    $params = $_GET;
    unset($params["pagina"]);
    $baseUrl = http_build_query($params);
    $baseUrl = $baseUrl !== "" ? $baseUrl . "&" : "";
    ?>

    <nav class="catalog-pagination" aria-label="Paginación del catálogo">
        <ul class="catalog-pagination-list">

            <?php if ($pagina > 1): ?>
                <li>
                    <a
                        href="catalogo.php?<?= $baseUrl ?>pagina=<?= $pagina - 1 ?>"
                        class="catalog-pagination-btn"
                        aria-label="Página anterior">
                        &laquo; Anterior
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $inicio = max(1, $pagina - 2);
            $fin = min($totalPaginas, $pagina + 2);

            if ($inicio > 1): ?>
                <li>
                    <a href="catalogo.php?<?= $baseUrl ?>pagina=1" class="catalog-pagination-btn">1</a>
                </li>
                <?php if ($inicio > 2): ?>
                    <li><span class="catalog-pagination-ellipsis">&hellip;</span></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                <li>
                    <?php if ($i === $pagina): ?>
                        <span class="catalog-pagination-btn catalog-pagination-active" aria-current="page">
                            <?= $i ?>
                        </span>
                    <?php else: ?>
                        <a href="catalogo.php?<?= $baseUrl ?>pagina=<?= $i ?>" class="catalog-pagination-btn">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>

            <?php if ($fin < $totalPaginas): ?>
                <?php if ($fin < $totalPaginas - 1): ?>
                    <li><span class="catalog-pagination-ellipsis">&hellip;</span></li>
                <?php endif; ?>
                <li>
                    <a href="catalogo.php?<?= $baseUrl ?>pagina=<?= $totalPaginas ?>" class="catalog-pagination-btn">
                        <?= $totalPaginas ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($pagina < $totalPaginas): ?>
                <li>
                    <a
                        href="catalogo.php?<?= $baseUrl ?>pagina=<?= $pagina + 1 ?>"
                        class="catalog-pagination-btn"
                        aria-label="Página siguiente">
                        Siguiente &raquo;
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </nav>
<?php endif; ?>
