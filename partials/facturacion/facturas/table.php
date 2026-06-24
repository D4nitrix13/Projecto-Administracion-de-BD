<?php
$baseUrl = "facturas.php";
$filtrosActuales = $filtrosGET ?? [];
?>
<div class="table-wrapper">
    <table class="table-products" id="facturas-table">
        <thead>
            <tr>
                <th data-sort="id" class="sortable-col">ID <span class="sort-icon"></span></th>
                <th data-sort="fecha" class="sortable-col">Fecha <span class="sort-icon"></span></th>
                <th data-sort="cliente" class="sortable-col">Cliente <span class="sort-icon"></span></th>
                <th data-sort="seccion" class="sortable-col">Sección <span class="sort-icon"></span></th>
                <th data-sort="usuario" class="sortable-col">Usuario <span class="sort-icon"></span></th>
                <th data-sort="total" class="sortable-col">Total <span class="sort-icon"></span></th>
                <th data-sort="pago" class="sortable-col">Pago <span class="sort-icon"></span></th>
                <th data-sort="produccion" class="sortable-col">Producción <span class="sort-icon"></span></th>
                <th class="col-acciones">Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($facturas)): ?>
                <tr>
                    <td colspan="9" class="dashboard-muted">
                        No se encontraron facturas con los filtros aplicados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($facturas as $factura): ?>
                    <?php
                    $estadoPago = $factura["estado_pago"] ?? "Pendiente";
                    $estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";

                    $clasePago = match ($estadoPago) {
                        "Pagado" => "badge-success",
                        "Parcial" => "badge-info",
                        default => "badge-warning",
                    };

                    $claseProduccion = match ($estadoProduccion) {
                        "Entregada" => "badge-success",
                        "Lista para entregar" => "badge-info",
                        "En producción" => "badge-primary",
                        "Cancelada" => "badge-danger",
                        default => "badge-warning",
                    };
                    ?>

                    <tr
                        data-id="<?= (int)$factura["id_factura"] ?>"
                        data-fecha="<?= htmlspecialchars($factura["fecha"]) ?>"
                        data-cliente="<?= htmlspecialchars(strtolower($factura["cliente"])) ?>"
                        data-seccion="<?= htmlspecialchars(strtolower($factura["seccion"])) ?>"
                        data-usuario="<?= htmlspecialchars(strtolower($factura["usuario"])) ?>"
                        data-total="<?= (float)$factura["total"] ?>"
                        data-pago="<?= htmlspecialchars($estadoPago) ?>"
                        data-produccion="<?= htmlspecialchars($estadoProduccion) ?>">

                        <td><?= (int)$factura["id_factura"] ?></td>

                        <td>
                            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($factura["fecha"]))) ?>
                        </td>

                        <td><?= htmlspecialchars($factura["cliente"]) ?></td>

                        <td><?= htmlspecialchars($factura["seccion"]) ?></td>

                        <td><?= htmlspecialchars($factura["usuario"]) ?></td>

                        <td>
                            C$ <?= number_format((float)$factura["total"], 2) ?>
                        </td>

                        <td>
                            <span class="status-badge <?= $clasePago ?>">
                                <?= htmlspecialchars($estadoPago) ?>
                            </span>
                        </td>

                        <td>
                            <span class="status-badge <?= $claseProduccion ?>">
                                <?= htmlspecialchars($estadoProduccion) ?>
                            </span>
                        </td>

                        <td class="acciones">
                            <a
                                href="detalle_factura.php?id=<?= (int)$factura["id_factura"] ?>"
                                class="btn-accion btn-accion-detalle">
                                Ver detalles
                            </a>

                            <a
                                href="editar_factura.php?id=<?= urlencode((string)$factura["id_factura"]) ?>"
                                class="btn-accion btn-accion-editar">
                                Editar
                            </a>

                            <form method="POST" action="eliminar_factura.php" style="display:inline;"
                                  class="delete-factura-form">
                                <input type="hidden" name="id" value="<?= (int)$factura["id_factura"] ?>">
                                <?= csrfField() ?>
                                <button type="submit" class="btn-accion btn-accion-eliminar">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . "/../../shared/pagination.php"; ?>

<style>
    .sortable-col {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 24px !important;
    }

    .sortable-col:hover {
        background: #e5e7eb;
    }

    .sort-icon {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.7rem;
        color: #9ca3af;
    }

    .sortable-col.sort-asc .sort-icon::after {
        content: "\25B2";
    }

    .sortable-col.sort-desc .sort-icon::after {
        content: "\25BC";
    }

    .sortable-col:not(.sort-asc):not(.sort-desc) .sort-icon::after {
        content: "\25C6";
        opacity: 0.4;
    }
</style>

<script>
    (function () {
        const table = document.getElementById("facturas-table");
        if (!table) return;

        const headers = table.querySelectorAll(".sortable-col");
        const tbody = table.querySelector("tbody");
        let currentSort = { column: null, direction: null };

        headers.forEach(header => {
            header.addEventListener("click", () => {
                const column = header.dataset.sort;
                let direction = "asc";

                if (currentSort.column === column && currentSort.direction === "asc") {
                    direction = "desc";
                }

                currentSort = { column, direction };

                headers.forEach(h => {
                    h.classList.remove("sort-asc", "sort-desc");
                });

                header.classList.add(direction === "asc" ? "sort-asc" : "sort-desc");

                const rows = Array.from(tbody.querySelectorAll("tr[data-id]"));

                rows.sort((a, b) => {
                    let valA, valB;

                    switch (column) {
                        case "id":
                            valA = parseInt(a.dataset.id, 10);
                            valB = parseInt(b.dataset.id, 10);
                            break;
                        case "fecha":
                            valA = a.dataset.fecha || "";
                            valB = b.dataset.fecha || "";
                            break;
                        case "cliente":
                            valA = a.dataset.cliente || "";
                            valB = b.dataset.cliente || "";
                            break;
                        case "seccion":
                            valA = a.dataset.seccion || "";
                            valB = b.dataset.seccion || "";
                            break;
                        case "usuario":
                            valA = a.dataset.usuario || "";
                            valB = b.dataset.usuario || "";
                            break;
                        case "total":
                            valA = parseFloat(a.dataset.total || "0");
                            valB = parseFloat(b.dataset.total || "0");
                            break;
                        case "pago":
                            valA = a.dataset.pago || "";
                            valB = b.dataset.pago || "";
                            break;
                        case "produccion":
                            valA = a.dataset.produccion || "";
                            valB = b.dataset.produccion || "";
                            break;
                        default:
                            return 0;
                    }

                    if (typeof valA === "string") {
                        valA = valA.toLowerCase();
                        valB = valB.toLowerCase();
                    }

                    if (valA < valB) return direction === "asc" ? -1 : 1;
                    if (valA > valB) return direction === "asc" ? 1 : -1;
                    return 0;
                });

                rows.forEach(row => tbody.appendChild(row));
            });
        });
    })();
</script>