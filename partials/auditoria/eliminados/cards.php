<?php
function obtenerEtiquetaTablaAuditoria(string $tabla): string
{
    return match ($tabla) {
        "producto" => "Producto eliminado",
        "cliente" => "Cliente eliminado",
        "categoria" => "Categoría eliminada",
        "proveedor" => "Proveedor eliminado",
        default => ucfirst($tabla) . " eliminado",
    };
}

function obtenerIconoTablaAuditoria(string $tabla): string
{
    return match ($tabla) {
        "producto" => "P",
        "cliente" => "C",
        "categoria" => "G",
        "proveedor" => "V",
        default => "R",
    };
}

function obtenerCamposResumenAuditoria(array $datos): array
{
    $camposPrioritarios = [
        "nombre" => "Nombre",
        "descripcion" => "Descripción",
        "correo" => "Correo",
        "email" => "Correo",
        "telefono" => "Teléfono",
        "direccion" => "Dirección",
        "precio" => "Precio",
        "stock" => "Stock",
        "id_producto" => "ID producto",
        "id_cliente" => "ID cliente",
        "id_categoria" => "ID categoría",
        "id_proveedor" => "ID proveedor",
    ];

    $resumen = [];

    foreach ($camposPrioritarios as $campo => $etiqueta) {
        if (array_key_exists($campo, $datos) && $datos[$campo] !== null && $datos[$campo] !== "") {
            $resumen[$etiqueta] = $datos[$campo];
        }

        if (count($resumen) >= 5) {
            break;
        }
    }

    if (!empty($resumen)) {
        return $resumen;
    }

    $contador = 0;

    foreach ($datos as $campo => $valor) {
        if ($valor === null || $valor === "") {
            continue;
        }

        $resumen[$campo] = $valor;
        $contador++;

        if ($contador >= 5) {
            break;
        }
    }

    return $resumen;
}
?>

<section class="audit-records-card">
    <div class="audit-section-heading">
        <div>
            <h2>Papelera administrativa</h2>
            <p>Registros eliminados disponibles para restauración o eliminación definitiva.</p>
        </div>

        <span class="audit-badge audit-badge-primary">
            <?= count($registros) ?> resultado(s)
        </span>
    </div>

    <?php if (empty($registros)): ?>
        <div class="audit-empty">
            <strong>No hay registros recuperables</strong>
            <p>
                Cuando elimine productos, clientes, categorías o proveedores, aparecerán aquí si el trigger guardó sus datos anteriores.
            </p>
        </div>
    <?php else: ?>
        <div class="audit-record-grid">
            <?php foreach ($registros as $registro): ?>
                <?php
                $datosAnteriores = json_decode($registro["datos_anteriores"] ?? "{}", true);

                if (!is_array($datosAnteriores)) {
                    $datosAnteriores = [];
                }

                $resumenDatos = obtenerCamposResumenAuditoria($datosAnteriores);
                $jsonBonito = json_encode($datosAnteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $tabla = (string)($registro["tabla_afectada"] ?? "");
                ?>

                <article class="audit-record">
                    <header class="audit-record-header">
                        <div class="audit-record-title">
                            <span class="audit-record-icon">
                                <?= htmlspecialchars(obtenerIconoTablaAuditoria($tabla)) ?>
                            </span>

                            <div>
                                <h3><?= htmlspecialchars(obtenerEtiquetaTablaAuditoria($tabla)) ?></h3>

                                <p>
                                    ID auditoría #<?= (int)$registro["id_auditoria"] ?>
                                    · Registro #<?= htmlspecialchars((string)($registro["registro_id"] ?? "Sin ID")) ?>
                                </p>
                            </div>
                        </div>

                        <span class="audit-badge audit-badge-danger">
                            Eliminado
                        </span>
                    </header>

                    <div class="audit-record-body">
                        <div class="audit-data-list">
                            <?php foreach (array_slice($resumenDatos, 0, 4, true) as $campo => $valor): ?>
                                <div class="audit-data-row">
                                    <span><?= htmlspecialchars((string)$campo) ?></span>

                                    <strong>
                                        <?= htmlspecialchars((string)$valor) ?>
                                    </strong>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <details class="audit-technical">
                            <summary>Ver datos técnicos JSON</summary>

                            <pre class="audit-json"><?= htmlspecialchars($jsonBonito ?: "{}") ?></pre>
                        </details>
                    </div>

                    <footer class="audit-record-footer">
                        <div class="audit-record-meta">
                            Eliminado por <?= htmlspecialchars($registro["usuario"] ?? "Sistema") ?><br>
                            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($registro["fecha"]))) ?>
                        </div>

                        <div class="audit-actions">
                            <form
                                method="post"
                                class="audit-action-form"
                                onsubmit="return confirm('¿Desea restaurar este registro eliminado?');">
                                <?= csrfField() ?>
                                <input type="hidden" name="id_auditoria" value="<?= (int)$registro["id_auditoria"] ?>">
                                <input type="hidden" name="accion" value="restaurar">

                                <button type="submit" class="audit-btn-success">
                                    Restaurar
                                </button>
                            </form>

                            <form
                                method="post"
                                class="audit-action-form"
                                onsubmit="return confirm('Esta acción eliminará permanentemente este registro de auditoría. ¿Desea continuar?');">
                                <?= csrfField() ?>
                                <input type="hidden" name="id_auditoria" value="<?= (int)$registro["id_auditoria"] ?>">
                                <input type="hidden" name="accion" value="eliminar_permanente">

                                <button type="submit" class="audit-btn-danger">
                                    Eliminar permanente
                                </button>
                            </form>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
$baseUrl = "auditoria_eliminados.php";
$filtrosActuales = $filtrosGET ?? [];
require __DIR__ . "/../../shared/pagination.php";
?>