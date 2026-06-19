<?php
$baseUrl = "compras.php";
$filtrosActuales = $filtrosGET ?? [];
?>

<div class="section-heading">
    <div>
        <h2>Listado de compras</h2>
        <p>Estas compras representan entradas de inventario registradas en el sistema.</p>
    </div>
</div>

<?php if (empty($compras)): ?>
    <p class="empty-message">
        No se encontraron compras con los filtros aplicados.
    </p>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Usuario</th>
                    <th>Total</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($compras as $compra): ?>
                    <tr>
                        <td><?= (int)$compra["id_compra"] ?></td>

                        <td>
                            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($compra["fecha"]))) ?>
                        </td>

                        <td><?= htmlspecialchars($compra["proveedor"]) ?></td>

                        <td><?= htmlspecialchars($compra["usuario"]) ?></td>

                        <td>
                            C$ <?= number_format((float)$compra["total"], 2) ?>
                        </td>

                        <td class="acciones">
                            <a
                                href="detalle_compra.php?id=<?= (int)$compra["id_compra"] ?>"
                                class="btn-accion btn-accion-editar">
                                Ver detalles
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php require __DIR__ . "/../../shared/pagination.php"; ?>
    </div>
<?php endif; ?>