<?php
function obtenerEtiquetaTablaAuditoria(string $tabla): string
{
    return match ($tabla) {
        "producto" => "Producto",
        "cliente" => "Cliente",
        "categoria" => "Categoría",
        "proveedor" => "Proveedor",
        default => ucfirst($tabla),
    };
}

function obtenerResumenDatosAuditoria(array $datos): array
{
    $camposPrioritarios = [
        "nombre",
        "descripcion",
        "correo",
        "telefono",
        "direccion",
        "precio",
        "stock",
        "id_producto",
        "id_cliente",
        "id_categoria",
        "id_proveedor",
    ];

    $resumen = [];

    foreach ($camposPrioritarios as $campo) {
        if (array_key_exists($campo, $datos) && $datos[$campo] !== null && $datos[$campo] !== "") {
            $resumen[$campo] = $datos[$campo];
        }
    }

    if (!empty($resumen)) {
        return $resumen;
    }

    return array_slice($datos, 0, 5, true);
}
?>

<div class="section-heading">
    <div>
        <h2>Papelera administrativa</h2>
        <p>Registros eliminados que todavía pueden restaurarse o eliminarse permanentemente.</p>
    </div>
</div>

<div class="table-wrapper">
    <table class="table-products">
        <thead>
            <tr>
                <th>ID auditoría</th>
                <th>Tabla</th>
                <th>ID registro</th>
                <th>Fecha eliminación</th>
                <th>Datos recuperados</th>
                <th>JSON completo</th>
                <th class="col-acciones">Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($registros)): ?>
                <tr>
                    <td colspan="7" class="dashboard-muted">
                        No se encontraron registros eliminados con los filtros aplicados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($registros as $registro): ?>
                    <?php
                    $datosAnteriores = json_decode($registro["datos_anteriores"] ?? "{}", true);

                    if (!is_array($datosAnteriores)) {
                        $datosAnteriores = [];
                    }

                    $resumenDatos = obtenerResumenDatosAuditoria($datosAnteriores);
                    $jsonBonito = json_encode($datosAnteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    ?>

                    <tr>
                        <td>
                            <?= (int)$registro["id_auditoria"] ?>
                        </td>

                        <td>
                            <span class="status-badge badge-primary">
                                <?= htmlspecialchars(obtenerEtiquetaTablaAuditoria($registro["tabla_afectada"])) ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars((string)($registro["registro_id"] ?? "Sin ID")) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($registro["fecha"]))) ?>
                        </td>

                        <td>
                            <div class="auditoria-data-preview">
                                <?php foreach ($resumenDatos as $campo => $valor): ?>
                                    <span>
                                        <strong><?= htmlspecialchars($campo) ?>:</strong>
                                        <?= htmlspecialchars((string)$valor) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </td>

                        <td>
                            <pre class="auditoria-json-box"><?= htmlspecialchars($jsonBonito ?: "{}") ?></pre>
                        </td>

                        <td class="acciones">
                            <form
                                method="post"
                                class="auditoria-action-form"
                                onsubmit="return confirm('¿Desea restaurar este registro eliminado?');">
                                <input type="hidden" name="id_auditoria" value="<?= (int)$registro["id_auditoria"] ?>">
                                <input type="hidden" name="accion" value="restaurar">

                                <button type="submit" class="btn-accion btn-accion-restaurar">
                                    Restaurar
                                </button>
                            </form>

                            <form
                                method="post"
                                class="auditoria-action-form"
                                onsubmit="return confirm('Esta acción eliminará permanentemente el historial de este registro. ¿Desea continuar?');">
                                <input type="hidden" name="id_auditoria" value="<?= (int)$registro["id_auditoria"] ?>">
                                <input type="hidden" name="accion" value="eliminar_permanente">

                                <button type="submit" class="btn-accion btn-accion-permanente">
                                    Eliminar permanente
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>