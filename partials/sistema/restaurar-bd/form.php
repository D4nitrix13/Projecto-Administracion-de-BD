<section class="restore-layout">

    <article class="restore-card">
        <div class="restore-card-header">
            <div>
                <span class="restore-badge restore-badge-danger">Restauración</span>

                <h2>Seleccionar respaldo</h2>

                <p>
                    La restauración reemplaza la información actual de la base de datos con el contenido del archivo seleccionado.
                </p>
            </div>
        </div>

        <?php if (empty($archivos)): ?>
            <div class="restore-empty">
                <strong>No hay respaldos disponibles.</strong>

                <p>
                    Primero genere un respaldo manual o espere a que exista un respaldo automático.
                </p>

                <a href="backups_manuales.php" class="restore-primary-link">
                    Ir a backups manuales
                </a>
            </div>
        <?php else: ?>
            <?php
            $archivosDisponibles = array_values(array_filter(
                $archivos,
                fn(array $item): bool => empty($item["deletion_pending"])
            ));

            $ultimoRespaldo = $archivosDisponibles[0] ?? null;
            $ultimoNombre = $ultimoRespaldo["nombre"] ?? "";
            $ultimoFecha = restaurarFormatearFecha($ultimoRespaldo["fecha"] ?? null);
            ?>

            <form method="POST" class="restore-form">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="restore">

                <div class="restore-field">
                    <label>Filtrar respaldos</label>

                    <div class="restore-filter-grid">
                        <input
                            type="search"
                            id="restoreSearch"
                            placeholder="Buscar respaldo por nombre...">

                        <select id="restoreTypeFilter">
                            <option value="all">Todos los tipos</option>
                            <option value="manual">Manual</option>
                            <option value="completo">Completo</option>
                            <option value="diferencial">Diferencial</option>
                        </select>
                    </div>
                </div>

                <div class="restore-field">
                    <label>Archivo de respaldo</label>

                    <div class="restore-table-wrapper">
                        <table class="restore-backup-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tipo</th>
                                    <th>Archivo</th>
                                    <th>Tamaño</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>

                            <tbody id="restoreBackupList">
                                <?php foreach ($archivos as $archivo): ?>
                                    <?php
                                    $nombre = $archivo["nombre"] ?? "";
                                    $tipo = $archivo["tipo"] ?? "Respaldo";
                                    $fecha = restaurarFormatearFecha($archivo["fecha"] ?? null);
                                    $tamano = restaurarFormatearTamano((int)($archivo["tamanio"] ?? 0));
                                    $pendiente = (bool)($archivo["deletion_pending"] ?? false);
                                    $tipoFiltro = strtolower($tipo);
                                    $esUltimo = $nombre !== "" && $nombre === $ultimoNombre;
                                    ?>

                                    <?php if (!$pendiente): ?>
                                        <tr
                                            class="restore-backup-row"
                                            data-name="<?= htmlspecialchars(strtolower($nombre)) ?>"
                                            data-type="<?= htmlspecialchars($tipoFiltro) ?>"
                                            data-is-latest="<?= $esUltimo ? "1" : "0" ?>"
                                            data-readable-date="<?= htmlspecialchars($fecha) ?>"
                                            data-latest-name="<?= htmlspecialchars($ultimoNombre) ?>"
                                            data-latest-date="<?= htmlspecialchars($ultimoFecha) ?>">

                                            <td class="restore-radio-cell">
                                                <input
                                                    type="radio"
                                                    name="archivo"
                                                    value="<?= htmlspecialchars($nombre) ?>"
                                                    required>
                                            </td>

                                            <td>
                                                <span class="restore-type-pill">
                                                    <?= htmlspecialchars($tipo) ?>
                                                </span>
                                            </td>

                                            <td class="restore-file-name">
                                                <?= htmlspecialchars($nombre) ?>
                                            </td>

                                            <td class="restore-size-cell">
                                                <?= htmlspecialchars($tamano) ?>
                                            </td>

                                            <td class="restore-date-cell">
                                                <?= htmlspecialchars($fecha) ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <tr id="restoreNoResults" class="restore-no-results-row">
                                    <td colspan="5">
                                        No se encontraron respaldos con ese filtro.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <small>
                        Seleccione un respaldo disponible. Los archivos con borrado programado no se muestran para restauración.
                    </small>
                </div>

                <div class="restore-warning">
                    <strong>Advertencia importante</strong>

                    <p>
                        Esta acción reemplazará los datos actuales. Antes de restaurar, asegúrese de haber generado un respaldo reciente.
                    </p>
                </div>

                <div class="restore-old-backup-warning" id="restoreOldBackupWarning" hidden>
                    <strong>Este respaldo no es el más reciente</strong>

                    <p id="restoreOldBackupText">
                        Está intentando restaurar un respaldo anterior al último disponible.
                    </p>

                    <ul>
                        <li>Puede perder registros, facturas, clientes, productos o movimientos creados después de esa fecha.</li>
                        <li>No se recomienda restaurar respaldos antiguos si existe uno más reciente.</li>
                        <li>Use esta opción solo si necesita regresar el sistema a un estado anterior específico.</li>
                    </ul>

                    <div class="restore-field">
                        <label for="forzar_restauracion">Confirmación avanzada</label>

                        <input
                            type="text"
                            name="forzar_restauracion"
                            id="forzar_restauracion"
                            autocomplete="off"
                            placeholder="Escriba FORZAR para restaurar un respaldo antiguo">

                        <small>
                            Solo complete este campo si desea restaurar un respaldo que no es el más reciente.
                        </small>
                    </div>
                </div>

                <div class="restore-field">
                    <label for="confirmacion">Confirmación</label>

                    <input
                        type="text"
                        name="confirmacion"
                        id="confirmacion"
                        autocomplete="off"
                        placeholder="Escriba RESTAURAR para confirmar"
                        required>

                    <small>
                        Para evitar restauraciones accidentales, escriba exactamente RESTAURAR en mayúsculas.
                    </small>
                </div>

                <div class="restore-actions">
                    <a href="backups_manuales.php" class="restore-secondary-button">
                        Ver respaldos
                    </a>

                    <button
                        type="submit"
                        class="restore-danger-button"
                        onclick="return confirm('¿Está seguro de restaurar la base de datos con este respaldo? Esta acción reemplazará los datos actuales.');">
                        Restaurar base de datos
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </article>

    <aside class="restore-side">

        <article class="restore-side-card">
            <span class="restore-badge restore-badge-info">Información</span>

            <h2>¿Qué hace esta opción?</h2>

            <p>
                Toma un archivo de respaldo existente y lo carga nuevamente en la base de datos del sistema.
            </p>

            <div class="restore-info-list">
                <div>
                    <span>Archivos permitidos</span>
                    <strong>.sql</strong>
                </div>

                <div>
                    <span>Origen de respaldos</span>
                    <strong>Manual, completo y diferencial</strong>
                </div>

                <div>
                    <span>Confirmación requerida</span>
                    <strong>RESTAURAR</strong>
                </div>

                <div>
                    <span>Confirmación avanzada</span>
                    <strong>FORZAR si el respaldo no es el más reciente</strong>
                </div>
            </div>
        </article>

        <article class="restore-side-card restore-side-danger">
            <span class="restore-badge restore-badge-danger">Precaución</span>

            <h2>Antes de restaurar</h2>

            <ul class="restore-check-list">
                <li>Verifique que seleccionó el respaldo correcto.</li>
                <li>Confirme que el archivo no esté vacío.</li>
                <li>Genere un respaldo nuevo antes de reemplazar datos.</li>
                <li>No cierre el sistema mientras la restauración se ejecuta.</li>
                <li>Evite restaurar respaldos antiguos si existe uno más reciente.</li>
            </ul>
        </article>

    </aside>

</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("restoreSearch");
        const typeFilter = document.getElementById("restoreTypeFilter");
        const rows = Array.from(document.querySelectorAll(".restore-backup-row"));
        const noResults = document.getElementById("restoreNoResults");
        const oldBackupWarning = document.getElementById("restoreOldBackupWarning");
        const oldBackupText = document.getElementById("restoreOldBackupText");
        const forceInput = document.getElementById("forzar_restauracion");

        function updateOldBackupWarning(row) {
            if (!row || !oldBackupWarning || !oldBackupText || !forceInput) {
                return;
            }

            const isLatest = row.dataset.isLatest === "1";
            const selectedName = row.querySelector(".restore-file-name")?.textContent.trim() || "respaldo seleccionado";
            const selectedDate = row.dataset.readableDate || "fecha no disponible";
            const latestName = row.dataset.latestName || "último respaldo";
            const latestDate = row.dataset.latestDate || "fecha no disponible";

            if (isLatest) {
                oldBackupWarning.hidden = true;
                forceInput.value = "";
                forceInput.required = false;
                return;
            }

            oldBackupWarning.hidden = false;
            forceInput.required = true;

            oldBackupText.textContent =
                "Seleccionó " + selectedName +
                " con fecha " + selectedDate +
                " El respaldo más reciente disponible es " + latestName +
                " con fecha " + latestDate + ".";
        }

        function clearOldBackupWarningIfNoSelection() {
            const selectedRow = rows.find(function(row) {
                const radio = row.querySelector("input[type='radio']");
                return radio && radio.checked;
            });

            if (!selectedRow && oldBackupWarning && forceInput) {
                oldBackupWarning.hidden = true;
                forceInput.value = "";
                forceInput.required = false;
            }
        }

        function applyFilters() {
            const searchValue = searchInput.value.trim().toLowerCase();
            const typeValue = typeFilter.value;
            let visibleCount = 0;

            rows.forEach(function(row) {
                const name = row.dataset.name || "";
                const type = row.dataset.type || "";

                const matchesSearch = name.includes(searchValue);
                const matchesType = typeValue === "all" || type.includes(typeValue);
                const visible = matchesSearch && matchesType;

                row.style.display = visible ? "table-row" : "none";

                if (!visible) {
                    const radio = row.querySelector("input[type='radio']");
                    if (radio && radio.checked) {
                        radio.checked = false;
                        row.classList.remove("is-selected");
                    }
                }

                if (visible) {
                    visibleCount++;
                }
            });

            noResults.style.display = visibleCount === 0 ? "table-row" : "none";
            clearOldBackupWarningIfNoSelection();
        }

        rows.forEach(function(row) {
            row.addEventListener("click", function() {
                const radio = row.querySelector("input[type='radio']");

                if (!radio) {
                    return;
                }

                radio.checked = true;

                rows.forEach(function(item) {
                    item.classList.remove("is-selected");
                });

                row.classList.add("is-selected");
                updateOldBackupWarning(row);
            });
        });

        searchInput.addEventListener("input", applyFilters);
        typeFilter.addEventListener("change", applyFilters);

        applyFilters();
    });
</script>