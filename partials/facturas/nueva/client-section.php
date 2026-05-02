<section class="invoice-form-section">
    <div class="invoice-form-section-header">
        <h3>Datos de la venta</h3>
        <p>Seleccione cliente, sección y descuento general de la factura.</p>
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

            <input
                type="text"
                id="cliente-search"
                class="input"
                placeholder="Filtrar por nombre, teléfono o identificación..."
                style="margin-bottom:8px;">

            <select name="id_cliente" class="input">
                <option value="">Seleccione un cliente</option>

                <?php foreach ($clientes as $cliente): ?>
                    <?php
                    $searchParts = [
                        $cliente["nombres"] ?? "",
                        $cliente["apellidos"] ?? "",
                        $cliente["telefono"] ?? "",
                        $cliente["direccion"] ?? "",
                        $cliente["identificacion"] ?? "",
                        $cliente["tipo_cliente"] ?? "",
                    ];

                    $searchText = strtolower(implode(" ", array_filter($searchParts)));
                    ?>

                    <option
                        value="<?= (int)$cliente["id_cliente"] ?>"
                        data-search="<?= htmlspecialchars($searchText) ?>"
                        <?= ((string)$idCliente === (string)$cliente["id_cliente"] && $tipoClienteVenta === TIPO_CLIENTE_HABITUAL) ? "selected" : "" ?>>
                        <?= htmlspecialchars($cliente["nombres"] . " " . $cliente["apellidos"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="grupo-cliente-fugaz" style="display: <?= $tipoClienteVenta === TIPO_CLIENTE_FUGAZ ? "block" : "none" ?>;">
            <label class="label">Nombre del cliente fugaz</label>

            <input
                type="text"
                name="nombre_cliente_fugaz"
                class="input"
                placeholder="Ejemplo: Cliente de feria, visitante, etc."
                value="<?= htmlspecialchars($nombreClienteFugaz) ?>">
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
</section>