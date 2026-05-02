<?php

$clienteSeleccionadoTexto = "";

if (!empty($idCliente)) {
    foreach ($clientes as $cliente) {
        if ((int)$cliente["id_cliente"] === (int)$idCliente) {
            $clienteSeleccionadoTexto = trim(
                ($cliente["nombres"] ?? "") . " " .
                    ($cliente["apellidos"] ?? "")
            );

            if (!empty($cliente["telefono"])) {
                $clienteSeleccionadoTexto .= " - " . $cliente["telefono"];
            }

            if (!empty($cliente["identificacion"])) {
                $clienteSeleccionadoTexto .= " - " . $cliente["identificacion"];
            }

            break;
        }
    }
}

?>

<section class="invoice-form-section">
    <div class="invoice-form-section-header">
        <h3>Datos de la venta</h3>
        <p>Seleccione el tipo de cliente, sección y descuento general de la factura.</p>
    </div>

    <div class="invoice-form-grid cols-2">
        <div class="form-group">
            <label class="label">Tipo de cliente</label>

            <select name="tipo_cliente_venta" id="tipo_cliente_venta" class="input">
                <option value="<?= TIPO_CLIENTE_HABITUAL ?>" <?= $tipoClienteVenta === TIPO_CLIENTE_HABITUAL ? "selected" : "" ?>>
                    Habitual registrado
                </option>

                <option value="<?= TIPO_CLIENTE_FUGAZ ?>" <?= $tipoClienteVenta === TIPO_CLIENTE_FUGAZ ? "selected" : "" ?>>
                    Fugaz no registrado
                </option>
            </select>
        </div>

        <div class="form-group" id="grupo-cliente-habitual">
            <label class="label">Cliente habitual (*)</label>

            <div class="client-picker">
                <input
                    type="text"
                    id="cliente-picker-input"
                    class="input"
                    list="clientes-list"
                    placeholder="Buscar por nombre, teléfono o identificación..."
                    value="<?= htmlspecialchars($clienteSeleccionadoTexto) ?>">

                <input
                    type="hidden"
                    name="id_cliente"
                    id="id_cliente"
                    value="<?= htmlspecialchars((string)$idCliente) ?>">

                <datalist id="clientes-list">
                    <?php foreach ($clientes as $cliente): ?>
                        <?php
                        $nombreCompleto = trim(
                            ($cliente["nombres"] ?? "") . " " .
                                ($cliente["apellidos"] ?? "")
                        );

                        $clienteTexto = $nombreCompleto;

                        if (!empty($cliente["telefono"])) {
                            $clienteTexto .= " - " . $cliente["telefono"];
                        }

                        if (!empty($cliente["identificacion"])) {
                            $clienteTexto .= " - " . $cliente["identificacion"];
                        }
                        ?>

                        <option value="<?= htmlspecialchars($clienteTexto) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="client-picker-actions">
                <a
                    href="nuevo_cliente.php?redirect=nueva_factura.php"
                    class="btn-secondary-inline">
                    + Registrar nuevo cliente
                </a>
            </div>

            <small class="dashboard-muted client-picker-help">
                Si el cliente no aparece, regístrelo antes de continuar con la factura.
            </small>
        </div>

        <div
            class="form-group"
            id="grupo-cliente-fugaz"
            style="display: <?= $tipoClienteVenta === TIPO_CLIENTE_FUGAZ ? "block" : "none" ?>;">
            <label class="label">Nombre del cliente fugaz</label>

            <input
                type="text"
                name="nombre_cliente_fugaz"
                class="input"
                placeholder="Ejemplo: Cliente de feria, visitante, etc."
                value="<?= htmlspecialchars($nombreClienteFugaz) ?>">

            <small class="dashboard-muted client-picker-help">
                El cliente fugaz solo se permite para ventas menores o iguales a C$ 1,000.00.
            </small>
        </div>

        <div class="form-group">
            <label class="label">Sección (*)</label>

            <?php if (!empty($user["id_seccion"])): ?>
                <?php
                $nombreSeccion = $seccionUsuario["nombre"]
                    ?? ("Sección #" . (int)$user["id_seccion"]);
                ?>

                <input
                    type="text"
                    class="input"
                    value="<?= htmlspecialchars($nombreSeccion) ?>"
                    disabled>

                <input
                    type="hidden"
                    name="id_seccion"
                    value="<?= (int)$user["id_seccion"] ?>">
            <?php else: ?>
                <select name="id_seccion" class="input" required>
                    <option value="">Seleccione sección</option>

                    <?php foreach ($secciones as $seccion): ?>
                        <option
                            value="<?= (int)$seccion["id_seccion"] ?>"
                            <?= ((string)$idSeccion === (string)$seccion["id_seccion"]) ? "selected" : "" ?>>
                            <?= htmlspecialchars($seccion["nombre"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="label">Descuento global C$</label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="descuento_global"
                class="input"
                value="<?= htmlspecialchars($descuentoGlobal) ?>">
        </div>
    </div>

    <script type="application/json" id="clientes-data">
        <?= json_encode(
            $clientes,
            JSON_UNESCAPED_UNICODE |
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
        ) ?>
    </script>
</section>