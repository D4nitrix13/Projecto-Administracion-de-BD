<?php

declare(strict_types=1);

function calcularPaginacion(
    int $totalRegistros,
    int $paginaActual,
    int $porPagina = 15
): array {
    if ($totalRegistros <= 0) {
        return [
            "totalRegistros" => 0,
            "totalPaginas" => 0,
            "paginaActual" => 1,
            "porPagina" => $porPagina,
            "offset" => 0,
            "tieneAnterior" => false,
            "tieneSiguiente" => false,
            "paginas" => [],
        ];
    }

    $totalPaginas = (int) ceil($totalRegistros / $porPagina);
    $paginaActual = max(1, min($paginaActual, $totalPaginas));
    $offset = ($paginaActual - 1) * $porPagina;

    $paginas = [];
    $maxVisibles = 5;

    if ($totalPaginas <= $maxVisibles + 2) {
        for ($i = 1; $i <= $totalPaginas; $i++) {
            $paginas[] = $i;
        }
    } else {
        $paginas[] = 1;

        $inicio = max(2, $paginaActual - (int) floor($maxVisibles / 2));
        $fin = min($totalPaginas - 1, $inicio + $maxVisibles - 1);

        if ($fin >= $totalPaginas - 1) {
            $fin = $totalPaginas - 1;
            $inicio = max(2, $fin - $maxVisibles + 1);
        }

        if ($inicio > 2) {
            $paginas[] = "...";
        }

        for ($i = $inicio; $i <= $fin; $i++) {
            $paginas[] = $i;
        }

        if ($fin < $totalPaginas - 1) {
            $paginas[] = "...";
        }

        $paginas[] = $totalPaginas;
    }

    return [
        "totalRegistros" => $totalRegistros,
        "totalPaginas" => $totalPaginas,
        "paginaActual" => $paginaActual,
        "porPagina" => $porPagina,
        "offset" => $offset,
        "tieneAnterior" => $paginaActual > 1,
        "tieneSiguiente" => $paginaActual < $totalPaginas,
        "paginas" => $paginas,
    ];
}

function construirUrlPagina(string $baseUrl, array $filtros, int $pagina): string
{
    $params = array_filter($filtros, fn($v) => $v !== "" && $v !== null && $v !== 0);
    $params["pagina"] = $pagina;
    return $baseUrl . "?" . http_build_query($params);
}
