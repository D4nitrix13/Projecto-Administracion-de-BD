<?php

if (!function_exists("formatearFechaBonitaRespaldo")) {
    function formatearFechaBonitaRespaldo(?string $fecha): string
    {
        if (!$fecha) {
            return "Fecha no disponible";
        }

        $timestamp = strtotime($fecha);

        if ($timestamp === false) {
            return $fecha;
        }

        $dias = [
            "domingo",
            "lunes",
            "martes",
            "miércoles",
            "jueves",
            "viernes",
            "sábado",
        ];

        $meses = [
            "enero",
            "febrero",
            "marzo",
            "abril",
            "mayo",
            "junio",
            "julio",
            "agosto",
            "septiembre",
            "octubre",
            "noviembre",
            "diciembre",
        ];

        $diaSemana = $dias[(int)date("w", $timestamp)];
        $dia = (int)date("j", $timestamp);
        $mes = $meses[(int)date("n", $timestamp) - 1];
        $anio = date("Y", $timestamp);
        $hora = date("h:i:s", $timestamp);
        $periodo = strtolower(date("A", $timestamp)) === "am"
            ? "a.m."
            : "p.m.";

        return "{$diaSemana} {$dia} de {$mes} {$anio} - {$hora} {$periodo}";
    }
}

if (!function_exists("formatearTamanoRespaldo")) {
    function formatearTamanoRespaldo(int $bytes): string
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return number_format($bytes / (1024 * 1024 * 1024), 2) . " GB";
        }

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2) . " MB";
        }

        return number_format($bytes / 1024, 2) . " KB";
    }
}

if (!function_exists("obtenerTituloAmigableRespaldo")) {
    function obtenerTituloAmigableRespaldo(array $archivo): string
    {
        $tipo = $archivo["tipo"] ?? "Manual";

        return match ($tipo) {
            "Completo" => "Respaldo completo",
            "Diferencial" => "Respaldo diferencial",
            default => "Respaldo manual",
        };
    }
}

if (!function_exists("obtenerSubtituloAmigableRespaldo")) {
    function obtenerSubtituloAmigableRespaldo(array $archivo): string
    {
        $nombre = pathinfo(
            $archivo["nombre"] ?? "",
            PATHINFO_FILENAME
        );

        $nombre = preg_replace(
            '/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/',
            '',
            $nombre
        );

        $nombre = str_replace(
            [
                "backup_manual_",
                "backup_full_",
                "backup_diff_"
            ],
            "",
            $nombre
        );

        $nombre = str_replace("_", " ", $nombre);
        $nombre = trim($nombre);

        if ($nombre === "") {
            return "Backup";
        }

        return ucwords($nombre);
    }
}

if (!function_exists("obtenerDescripcionRespaldo")) {
    function obtenerDescripcionRespaldo(array $archivo): string
    {
        return trim(
            (string)(
                $archivo["descripcion"]
                ?? $archivo["mensaje"]
                ?? ""
            )
        );
    }
}

$tiposDisponibles = [];

foreach ($archivos as $archivo) {
    $tipo = $archivo["tipo"] ?? "Manual";

    if (!in_array($tipo, $tiposDisponibles, true)) {
        $tiposDisponibles[] = $tipo;
    }
}

sort($tiposDisponibles);

?>

<section class="backup-history">

    <div class="backup-history-header">
        <div>
            <h2>Archivos de respaldo disponibles</h2>

            <p class="dashboard-muted">
                Busque, filtre, descargue o programe el borrado seguro de sus respaldos.
            </p>
        </div>

        <span class="backup-count" id="backupVisibleCount">
            <?= count($archivos) ?> archivo(s)
        </span>
    </div>

    <section class="backup-filter-panel">

        <div class="backup-filter-header">
            <div>
                <span class="backup-filter-badge">
                    Filtros
                </span>

                <h3>Buscar respaldo</h3>

                <p>
                    Filtre por nombre, descripción,
                    tipo o estado.
                </p>
            </div>

            <button
                type="button"
                class="backup-filter-clear"
                id="backupClearFilters">

                Limpiar filtros
            </button>
        </div>

        <div class="backup-filter-grid">

            <div class="backup-filter-field backup-filter-field-large">
                <label for="backupSearchInput">
                    Buscar
                </label>

                <input
                    type="search"
                    id="backupSearchInput"
                    class="input"
                    placeholder="Buscar por nombre, tipo o descripción">
            </div>

            <div class="backup-filter-field">
                <label for="backupTypeFilter">
                    Tipo
                </label>

                <select id="backupTypeFilter" class="input">
                    <option value="">
                        Todos los tipos
                    </option>

                    <?php foreach ($tiposDisponibles as $tipo): ?>
                        <option value="<?= htmlspecialchars(strtolower($tipo)) ?>">
                            <?= htmlspecialchars($tipo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="backup-filter-field">
                <label for="backupStatusFilter">
                    Estado
                </label>

                <select id="backupStatusFilter" class="input">
                    <option value="">
                        Todos los estados
                    </option>

                    <option value="disponible">
                        Disponible
                    </option>

                    <option value="pendiente">
                        Borrado programado
                    </option>
                </select>
            </div>

            <div class="backup-filter-field">
                <label for="backupSortFilter">
                    Ordenar por
                </label>

                <select id="backupSortFilter" class="input">
                    <option value="date_desc">
                        Más recientes primero
                    </option>

                    <option value="date_asc">
                        Más antiguos primero
                    </option>

                    <option value="size_desc">
                        Mayor tamaño primero
                    </option>

                    <option value="size_asc">
                        Menor tamaño primero
                    </option>
                </select>
            </div>

        </div>
    </section>

    <div
        class="backup-no-results"
        id="backupNoResults"
        hidden>

        <strong>
            No se encontraron respaldos.
        </strong>

        <p>
            Pruebe otra búsqueda.
        </p>
    </div>

    <div class="backup-files-grid" id="backupFilesGrid">

        <?php foreach ($archivos as $archivo): ?>

            <?php

            $titulo = obtenerTituloAmigableRespaldo($archivo);
            $subtitulo = obtenerSubtituloAmigableRespaldo($archivo);
            $descripcion = obtenerDescripcionRespaldo($archivo);

            $fechaBonita = formatearFechaBonitaRespaldo(
                $archivo["fecha"] ?? null
            );

            $pendiente = (bool)(
                $archivo["deletion_pending"] ?? false
            );

            $tipo = $archivo["tipo"] ?? "Manual";

            $nombreReal = $archivo["nombre"] ?? "";

            $timestamp = strtotime(
                $archivo["fecha"] ?? ""
            );

            $timestamp = $timestamp === false
                ? 0
                : $timestamp;

            $tamanio = (int)(
                $archivo["tamanio"] ?? 0
            );

            ?>

            <article
                class="backup-file-card"
                data-backup-card
                data-search="<?= htmlspecialchars(strtolower(
                                    $titulo . " " .
                                        $subtitulo . " " .
                                        $descripcion . " " .
                                        $nombreReal
                                )) ?>"
                data-type="<?= htmlspecialchars(strtolower($tipo)) ?>"
                data-status="<?= $pendiente ? 'pendiente' : 'disponible' ?>"
                data-date="<?= $timestamp ?>"
                data-size="<?= $tamanio ?>">

                <div class="backup-file-main">

                    <div class="backup-file-icon">
                        SQL
                    </div>

                    <div class="backup-file-info">

                        <div class="backup-file-topline">

                            <h3 class="backup-file-title">
                                <?= htmlspecialchars($titulo) ?>
                            </h3>

                            <span class="backup-status <?= $pendiente
                                                            ? 'backup-status-warning'
                                                            : 'backup-status-ok' ?>">

                                <?= $pendiente
                                    ? 'Borrado programado'
                                    : 'Disponible' ?>

                            </span>

                        </div>

                        <p class="backup-file-subtitle">
                            <?= htmlspecialchars($subtitulo) ?>
                        </p>

                        <p class="backup-file-original-name">
                            <strong>Descripción:</strong>

                            <?= $descripcion !== ""
                                ? htmlspecialchars($descripcion)
                                : 'Sin descripción registrada para este respaldo.' ?>
                        </p>

                        <p class="backup-file-original-name">
                            <strong>Archivo real:</strong>

                            <?= htmlspecialchars($nombreReal) ?>
                        </p>

                        <div class="backup-file-meta">

                            <span class="backup-chip">
                                <?= htmlspecialchars($tipo) ?>
                            </span>

                            <span class="backup-chip">
                                <?= htmlspecialchars(
                                    formatearTamanoRespaldo($tamanio)
                                ) ?>
                            </span>

                            <span class="backup-chip">
                                .sql
                            </span>

                        </div>
                    </div>
                </div>
            </article>

        <?php endforeach; ?>

    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const searchInput = document.getElementById("backupSearchInput");
        const typeFilter = document.getElementById("backupTypeFilter");
        const statusFilter = document.getElementById("backupStatusFilter");
        const sortFilter = document.getElementById("backupSortFilter");
        const clearButton = document.getElementById("backupClearFilters");
        const filesGrid = document.getElementById("backupFilesGrid");
        const visibleCount = document.getElementById("backupVisibleCount");
        const noResults = document.getElementById("backupNoResults");

        const cards = Array.from(
            document.querySelectorAll("[data-backup-card]")
        );

        const normalize = (value) => {
            return String(value || "")
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .trim();
        };

        function applyFilters() {

            const search = normalize(searchInput.value);
            const type = normalize(typeFilter.value);
            const status = normalize(statusFilter.value);

            let visible = 0;

            cards.forEach((card) => {

                const cardSearch = normalize(
                    card.dataset.search
                );

                const cardType = normalize(
                    card.dataset.type
                );

                const cardStatus = normalize(
                    card.dataset.status
                );

                const matchSearch =
                    search === "" ||
                    cardSearch.includes(search);

                const matchType =
                    type === "" ||
                    cardType === type;

                const matchStatus =
                    status === "" ||
                    cardStatus === status;

                const show =
                    matchSearch &&
                    matchType &&
                    matchStatus;

                card.style.display = show ?
                    "" :
                    "none";

                if (show) {
                    visible++;
                }
            });

            visibleCount.textContent =
                `${visible} archivo(s)`;

            noResults.hidden = visible > 0;
        }

        searchInput.addEventListener(
            "input",
            applyFilters
        );

        typeFilter.addEventListener(
            "change",
            applyFilters
        );

        statusFilter.addEventListener(
            "change",
            applyFilters
        );

        sortFilter.addEventListener(
            "change",
            applyFilters
        );

        clearButton.addEventListener(
            "click",
            () => {

                searchInput.value = "";
                typeFilter.value = "";
                statusFilter.value = "";
                sortFilter.value = "date_desc";

                applyFilters();
            }
        );

        applyFilters();
    });
</script>