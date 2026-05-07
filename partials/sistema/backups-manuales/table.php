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
        $periodo = strtolower(date("A", $timestamp)) === "am" ? "a.m." : "p.m.";

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
        $nombre = pathinfo($archivo["nombre"] ?? "", PATHINFO_FILENAME);

        $nombre = preg_replace('/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/', '', $nombre);

        $nombre = str_replace(
            ["backup_manual_", "backup_full_", "backup_diff_"],
            "",
            $nombre
        );

        $nombre = str_replace("_", " ", $nombre);
        $nombre = trim($nombre);

        if ($nombre === "" || $nombre === "pandas estampados y kitsune") {
            return "Base de datos principal del sistema";
        }

        return mb_convert_case($nombre, MB_CASE_TITLE, "UTF-8");
    }
}

if (!function_exists("obtenerDescripcionRespaldo")) {
    function obtenerDescripcionRespaldo(array $archivo): string
    {
        $descripcion = trim((string)($archivo["descripcion"] ?? ""));
        $mensaje = trim((string)($archivo["mensaje"] ?? ""));
        $comentario = trim((string)($archivo["comentario"] ?? ""));

        if ($descripcion !== "") {
            return $descripcion;
        }

        if ($mensaje !== "") {
            return $mensaje;
        }

        if ($comentario !== "") {
            return $comentario;
        }

        return "Sin descripción registrada para este respaldo.";
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

    <?php if (empty($archivos)): ?>
        <div class="backup-empty">
            <strong>No hay respaldos generados todavía.</strong>
            <p>Cuando genere un respaldo, aparecerá en esta sección.</p>
        </div>
    <?php else: ?>
        <section class="backup-filter-panel">
            <div class="backup-filter-header">
                <div>
                    <span class="backup-filter-badge">Filtros</span>

                    <h3>Buscar respaldo</h3>

                    <p>
                        Filtre por nombre, descripción, tipo, estado y ordene los respaldos.
                    </p>
                </div>

                <button type="button" class="backup-filter-clear" id="backupClearFilters">
                    Limpiar filtros
                </button>
            </div>

            <div class="backup-filter-grid">
                <div class="backup-filter-field backup-filter-field-large">
                    <label for="backupSearchInput">Buscar</label>

                    <input
                        type="search"
                        id="backupSearchInput"
                        class="input"
                        autocomplete="off"
                        placeholder="Buscar por nombre, tipo o descripción">
                </div>

                <div class="backup-filter-field">
                    <label for="backupTypeFilter">Tipo</label>

                    <select id="backupTypeFilter" class="input">
                        <option value="">Todos los tipos</option>

                        <?php foreach ($tiposDisponibles as $tipo): ?>
                            <option value="<?= htmlspecialchars(mb_strtolower($tipo, "UTF-8")) ?>">
                                <?= htmlspecialchars($tipo) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="backup-filter-field">
                    <label for="backupStatusFilter">Estado</label>

                    <select id="backupStatusFilter" class="input">
                        <option value="">Todos los estados</option>
                        <option value="disponible">Disponible</option>
                        <option value="pendiente">Borrado programado</option>
                    </select>
                </div>

                <div class="backup-filter-field">
                    <label for="backupSortFilter">Ordenar por</label>

                    <select id="backupSortFilter" class="input">
                        <option value="date_desc">Más recientes primero</option>
                        <option value="date_asc">Más antiguos primero</option>
                        <option value="size_desc">Mayor tamaño primero</option>
                        <option value="size_asc">Menor tamaño primero</option>
                        <option value="name_asc">Nombre A-Z</option>
                        <option value="name_desc">Nombre Z-A</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="backup-no-results" id="backupNoResults" hidden>
            <strong>No se encontraron respaldos.</strong>
            <p>Pruebe con otro nombre, descripción, tipo de respaldo o estado.</p>
        </div>

        <div class="backup-files-grid" id="backupFilesGrid">
            <?php foreach ($archivos as $archivo): ?>
                <?php
                $titulo = obtenerTituloAmigableRespaldo($archivo);
                $subtitulo = obtenerSubtituloAmigableRespaldo($archivo);
                $descripcion = obtenerDescripcionRespaldo($archivo);

                $fechaBonita = formatearFechaBonitaRespaldo($archivo["fecha"] ?? null);
                $fechaBorrado = formatearFechaBonitaRespaldo($archivo["deletion_at"] ?? null);

                $pendiente = (bool)($archivo["deletion_pending"] ?? false);
                $tipo = $archivo["tipo"] ?? "Manual";
                $nombreReal = $archivo["nombre"] ?? "";
                $fechaOriginal = $archivo["fecha"] ?? "";

                $timestamp = strtotime($fechaOriginal);
                $timestamp = $timestamp === false ? 0 : $timestamp;

                $tamanio = (int)($archivo["tamanio"] ?? 0);
                $estadoFiltro = $pendiente ? "pendiente" : "disponible";

                $textoBusqueda = mb_strtolower(
                    $titulo . " " .
                        $subtitulo . " " .
                        $descripcion . " " .
                        $nombreReal . " " .
                        $tipo . " " .
                        $estadoFiltro . " " .
                        $fechaBonita . " " .
                        formatearTamanoRespaldo($tamanio),
                    "UTF-8"
                );
                ?>

                <article
                    class="backup-file-card <?= $pendiente ? "backup-file-card-pending" : "" ?>"
                    data-backup-card
                    data-search="<?= htmlspecialchars($textoBusqueda) ?>"
                    data-type="<?= htmlspecialchars(mb_strtolower($tipo, "UTF-8")) ?>"
                    data-status="<?= htmlspecialchars($estadoFiltro) ?>"
                    data-date="<?= htmlspecialchars((string)$timestamp) ?>"
                    data-size="<?= htmlspecialchars((string)$tamanio) ?>"
                    data-name="<?= htmlspecialchars(mb_strtolower($subtitulo . " " . $nombreReal, "UTF-8")) ?>">

                    <div class="backup-file-main">
                        <div class="backup-file-icon">
                            SQL
                        </div>

                        <div class="backup-file-info">
                            <div class="backup-file-topline">
                                <h3 class="backup-file-title">
                                    <?= htmlspecialchars($titulo) ?>
                                </h3>

                                <span class="backup-status <?= $pendiente ? "backup-status-warning" : "backup-status-ok" ?>">
                                    <?= $pendiente ? "Borrado programado" : "Disponible" ?>
                                </span>
                            </div>

                            <p class="backup-file-subtitle">
                                <?= htmlspecialchars($subtitulo) ?>
                            </p>

                            <p class="backup-file-original-name">
                                <strong>Descripción:</strong>
                                <?= htmlspecialchars($descripcion) ?>
                            </p>

                            <p class="backup-file-original-name" title="<?= htmlspecialchars($nombreReal) ?>">
                                <strong>Archivo real:</strong>
                                <?= htmlspecialchars($nombreReal) ?>
                            </p>

                            <div class="backup-file-meta">
                                <span class="backup-chip"><?= htmlspecialchars($tipo) ?></span>
                                <span class="backup-chip"><?= htmlspecialchars(formatearTamanoRespaldo($tamanio)) ?></span>
                                <span class="backup-chip">.sql</span>
                            </div>
                        </div>
                    </div>

                    <div class="backup-file-side">
                        <div class="backup-file-date-block">
                            <span class="backup-file-date-label">Generado el</span>

                            <strong class="backup-file-date">
                                <?= htmlspecialchars($fechaBonita) ?>
                            </strong>
                        </div>

                        <?php if ($pendiente): ?>
                            <div class="backup-delete-box">
                                <strong>Este archivo será eliminado automáticamente</strong>
                                <p><?= htmlspecialchars($fechaBorrado) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="backup-delete-box backup-delete-box-neutral">
                                <strong>Borrado seguro</strong>
                                <p>Si lo elimina, quedará en espera durante 24 horas antes de borrarse definitivamente.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="backup-file-actions">
                        <a
                            href="descargar_respaldo.php?archivo=<?= urlencode($nombreReal) ?>"
                            class="backup-action-btn backup-action-btn-primary">
                            Descargar
                        </a>

                        <?php if ($pendiente): ?>
                            <form method="POST" class="backup-inline-form">
                                <input type="hidden" name="action" value="cancel_delete">
                                <input type="hidden" name="archivo" value="<?= htmlspecialchars($nombreReal) ?>">

                                <button type="submit" class="backup-action-btn backup-action-btn-dark">
                                    Cancelar borrado
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" class="backup-inline-form">
                                <input type="hidden" name="action" value="schedule_delete">
                                <input type="hidden" name="archivo" value="<?= htmlspecialchars($nombreReal) ?>">

                                <button
                                    type="submit"
                                    class="backup-action-btn backup-action-btn-danger"
                                    onclick="return confirm('¿Desea programar el borrado de este respaldo dentro de 24 horas?');">
                                    Programar borrado
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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

        if (
            !searchInput ||
            !typeFilter ||
            !statusFilter ||
            !sortFilter ||
            !clearButton ||
            !filesGrid ||
            !visibleCount ||
            !noResults
        ) {
            return;
        }

        const normalizeText = (value) => {
            return String(value || "")
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/\s+/g, " ")
                .trim();
        };

        const getCards = () => {
            return Array.from(filesGrid.querySelectorAll("[data-backup-card]"));
        };

        const getCardSearchText = (card) => {
            return normalizeText(
                `${card.dataset.search || ""} ${card.textContent || ""}`
            );
        };

        const updateVisibleCount = (count) => {
            visibleCount.textContent = `${count} archivo(s)`;
        };

        const sortCards = () => {
            const sortValue = sortFilter.value;
            const cards = getCards();

            cards.sort((a, b) => {
                const dateA = Number(a.dataset.date || 0);
                const dateB = Number(b.dataset.date || 0);
                const sizeA = Number(a.dataset.size || 0);
                const sizeB = Number(b.dataset.size || 0);
                const nameA = normalizeText(a.dataset.name || "");
                const nameB = normalizeText(b.dataset.name || "");

                switch (sortValue) {
                    case "date_asc":
                        return dateA - dateB;

                    case "size_desc":
                        return sizeB - sizeA;

                    case "size_asc":
                        return sizeA - sizeB;

                    case "name_asc":
                        return nameA.localeCompare(nameB, "es", {
                            sensitivity: "base",
                            numeric: true
                        });

                    case "name_desc":
                        return nameB.localeCompare(nameA, "es", {
                            sensitivity: "base",
                            numeric: true
                        });

                    case "date_desc":
                    default:
                        return dateB - dateA;
                }
            });

            const fragment = document.createDocumentFragment();

            cards.forEach((card) => {
                fragment.appendChild(card);
            });

            filesGrid.appendChild(fragment);
        };

        const applyFilters = () => {
            sortCards();

            const cards = getCards();
            const searchValue = normalizeText(searchInput.value);
            const typeValue = normalizeText(typeFilter.value);
            const statusValue = normalizeText(statusFilter.value);

            let visible = 0;

            cards.forEach((card) => {
                const cardSearch = getCardSearchText(card);
                const cardType = normalizeText(card.dataset.type || "");
                const cardStatus = normalizeText(card.dataset.status || "");

                const matchesSearch = searchValue === "" || cardSearch.includes(searchValue);
                const matchesType = typeValue === "" || cardType === typeValue;
                const matchesStatus = statusValue === "" || cardStatus === statusValue;

                const shouldShow = matchesSearch && matchesType && matchesStatus;

                card.style.display = shouldShow ? "" : "none";

                if (shouldShow) {
                    visible++;
                }
            });

            updateVisibleCount(visible);
            noResults.hidden = visible > 0;
        };

        searchInput.addEventListener("input", applyFilters);
        typeFilter.addEventListener("change", applyFilters);
        statusFilter.addEventListener("change", applyFilters);
        sortFilter.addEventListener("change", applyFilters);

        clearButton.addEventListener("click", () => {
            searchInput.value = "";
            typeFilter.value = "";
            statusFilter.value = "";
            sortFilter.value = "date_desc";

            applyFilters();
            searchInput.focus();
        });

        applyFilters();
    });
</script>