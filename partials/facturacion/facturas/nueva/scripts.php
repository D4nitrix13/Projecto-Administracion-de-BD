<?php
$rutasConfiguracionSistema = [
    __DIR__ . "/../storage/system/configuracion_sistema.json",
    __DIR__ . "/../../storage/system/configuracion_sistema.json",
    dirname(__DIR__) . "/storage/system/configuracion_sistema.json",
    dirname(__DIR__, 2) . "/storage/system/configuracion_sistema.json",
    $_SERVER["DOCUMENT_ROOT"] . "/storage/system/configuracion_sistema.json",
];

$limiteClienteFugaz = 1000.00;
$rutaConfiguracionEncontrada = null;

foreach ($rutasConfiguracionSistema as $rutaConfiguracionSistema) {
    if (!is_file($rutaConfiguracionSistema)) {
        continue;
    }

    $contenidoConfiguracion = file_get_contents($rutaConfiguracionSistema);
    $configuracionSistema = json_decode($contenidoConfiguracion, true);

    if (
        json_last_error() === JSON_ERROR_NONE &&
        isset($configuracionSistema["limite_de_venta_cliente_fugaz"]) &&
        is_numeric($configuracionSistema["limite_de_venta_cliente_fugaz"])
    ) {
        $limiteClienteFugaz = (float) $configuracionSistema["limite_de_venta_cliente_fugaz"];
        $rutaConfiguracionEncontrada = $rutaConfiguracionSistema;
        break;
    }
}
?>

<script>
    (function() {
        const IVA_RATE = 0.15;
        const LIMITE_CLIENTE_FUGAZ = <?php echo json_encode($limiteClienteFugaz, JSON_NUMERIC_CHECK); ?>;

        const productos = JSON.parse(
            document.getElementById("productos-data").textContent
        );

        const clientes = JSON.parse(
            document.getElementById("clientes-data").textContent
        );

        const tbody = document.getElementById("items-body");
        const form = document.getElementById("form-factura");
        const inputProducto = document.getElementById("producto-picker-input");
        const btnAddSelected = document.getElementById("btn-add-selected-product");
        const emptyMessage = document.getElementById("empty-products-message");

        const selectTipoCliente = document.getElementById("tipo_cliente_venta");
        const grupoHabitual = document.getElementById("grupo-cliente-habitual");
        const grupoFugaz = document.getElementById("grupo-cliente-fugaz");

        const clientePickerInput = document.getElementById("cliente-picker-input");
        const clienteHiddenInput = document.getElementById("id_cliente");

        const montoPagadoInput = document.getElementById("monto_pagado");
        const fechaEntregaEstimadaInput = document.getElementById("fecha_entrega_estimada");
        const minimoRequeridoView = document.getElementById("minimo-requerido-view");
        const saldoPendienteView = document.getElementById("saldo-pendiente-view");
        const estadoPagoView = document.getElementById("estado-pago-view");
        const paymentWarning = document.getElementById("invoice-payment-warning");

        function formatMoney(value) {
            return `C$ ${value.toFixed(2)}`;
        }

        function actualizarResumenPago(total) {
            if (!montoPagadoInput) {
                return;
            }

            let montoPagado = parseFloat(montoPagadoInput.value || "0");

            if (Number.isNaN(montoPagado) || montoPagado < 0) {
                montoPagado = 0;
            }

            if (montoPagado > total) {
                montoPagado = total;
                montoPagadoInput.value = total.toFixed(2);
            }

            const minimoRequerido = total * 0.50;
            const saldoPendiente = Math.max(0, total - montoPagado);

            if (minimoRequeridoView) {
                minimoRequeridoView.textContent = formatMoney(minimoRequerido);
            }

            if (saldoPendienteView) {
                saldoPendienteView.textContent = formatMoney(saldoPendiente);
            }

            if (estadoPagoView) {
                estadoPagoView.classList.remove("pending", "partial", "paid");

                if (montoPagado <= 0) {
                    estadoPagoView.textContent = "Pendiente";
                    estadoPagoView.classList.add("pending");
                } else if (montoPagado >= total && total > 0) {
                    estadoPagoView.textContent = "Pagado";
                    estadoPagoView.classList.add("paid");
                } else {
                    estadoPagoView.textContent = "Parcial";
                    estadoPagoView.classList.add("partial");
                }
            }

            if (paymentWarning) {
                paymentWarning.style.display =
                    total > 0 && montoPagado < minimoRequerido ? "flex" : "none";
            }
        }

        function normalizarTexto(texto) {
            return String(texto || "")
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .trim();
        }

        function escapeHtml(texto) {
            const div = document.createElement("div");
            div.textContent = texto;
            return div.innerHTML;
        }

        function obtenerTextoCliente(cliente) {
            const nombreCompleto = `${cliente.nombres || ""} ${cliente.apellidos || ""}`.trim();

            let texto = nombreCompleto;

            if (cliente.telefono) {
                texto += ` - ${cliente.telefono}`;
            }

            if (cliente.identificacion) {
                texto += ` - ${cliente.identificacion}`;
            }

            return texto;
        }

        function obtenerClienteDesdeInput() {
            if (!clientePickerInput) {
                return null;
            }

            const valor = normalizarTexto(clientePickerInput.value);

            return clientes.find(cliente => {
                const textoCompleto = obtenerTextoCliente(cliente);
                const nombreCompleto = `${cliente.nombres || ""} ${cliente.apellidos || ""}`;
                const telefono = cliente.telefono || "";
                const identificacion = cliente.identificacion || "";

                return normalizarTexto(textoCompleto) === valor ||
                    normalizarTexto(nombreCompleto) === valor ||
                    normalizarTexto(telefono) === valor ||
                    normalizarTexto(identificacion) === valor;
            }) || null;
        }

        function actualizarClienteSeleccionado() {
            if (!clientePickerInput || !clienteHiddenInput) {
                return;
            }

            const cliente = obtenerClienteDesdeInput();

            if (cliente) {
                clienteHiddenInput.value = String(cliente.id_cliente);
            } else {
                clienteHiddenInput.value = "";
            }
        }

        if (clientePickerInput) {
            clientePickerInput.addEventListener("input", actualizarClienteSeleccionado);
            clientePickerInput.addEventListener("change", actualizarClienteSeleccionado);
            clientePickerInput.addEventListener("blur", actualizarClienteSeleccionado);
        }

        if (selectTipoCliente) {
            const toggleClienteGroups = () => {
                if (selectTipoCliente.value === "<?= TIPO_CLIENTE_FUGAZ ?>") {
                    grupoHabitual.style.display = "none";

                    if (clientePickerInput) {
                        clientePickerInput.removeAttribute("required");
                        clientePickerInput.value = "";
                    }

                    if (clienteHiddenInput) {
                        clienteHiddenInput.value = "";
                    }

                    grupoFugaz.style.display = "block";
                } else {
                    grupoHabitual.style.display = "block";

                    if (clientePickerInput) {
                        clientePickerInput.setAttribute("required", "required");
                    }

                    grupoFugaz.style.display = "none";
                }
            };

            selectTipoCliente.addEventListener("change", toggleClienteGroups);
            toggleClienteGroups();
        }

        function obtenerProductoDesdeInput() {
            const valor = inputProducto.value.trim();
            const valorNormalizado = normalizarTexto(valor);

            return productos.find(producto => {
                const nombre = producto.nombre || "";
                const codigo = producto.codigo || "";
                const opcion = `${nombre} (${codigo})`;

                return normalizarTexto(opcion) === valorNormalizado ||
                    normalizarTexto(nombre) === valorNormalizado ||
                    normalizarTexto(codigo) === valorNormalizado;
            });
        }

        function productoYaAgregado(idProducto) {
            return !!tbody.querySelector(`tr[data-id-producto="${idProducto}"]`);
        }

        function actualizarMensajeVacio() {
            emptyMessage.style.display = tbody.querySelectorAll(".item-row").length > 0 ?
                "none" :
                "block";
        }

        function agregarProductoSeleccionado() {
            const producto = obtenerProductoDesdeInput();

            if (!producto) {
                alert("Seleccione un producto válido de la lista.");
                return;
            }

            const idProducto = parseInt(producto.id_producto, 10);

            if (productoYaAgregado(idProducto)) {
                alert("Este producto ya fue agregado. Puede modificar la cantidad en la tabla.");
                inputProducto.value = "";
                return;
            }

            if (parseInt(producto.stock || "0", 10) <= 0) {
                alert("Este producto no tiene stock disponible.");
                return;
            }

            crearFilaProducto(producto);
            inputProducto.value = "";
        }

        function crearFilaProducto(producto) {
            const idProducto = parseInt(producto.id_producto, 10);
            const nombre = producto.nombre || "";
            const codigo = producto.codigo || "";
            const precio = parseFloat(producto.precio_venta || "0");
            const stock = parseInt(producto.stock || "0", 10);

            const row = document.createElement("tr");
            row.className = "item-row";
            row.dataset.idProducto = String(idProducto);
            row.dataset.precio = String(precio);
            row.dataset.stock = String(stock);

            row.innerHTML = `
                <td class="product-name-cell">
                    <strong>${escapeHtml(nombre)}</strong>
                    <small>${escapeHtml(codigo)}</small>
                    <input type="hidden" name="id_producto[]" value="${idProducto}">
                </td>

                <td>
                    <input type="text" class="input precio" value="${precio.toFixed(2)}" readonly>
                </td>

                <td>
                    <input type="text" class="input stock" value="${stock}" readonly>
                </td>

                <td>
                    <input
                        type="number"
                        name="cantidad[]"
                        class="input cantidad"
                        min="1"
                        max="${stock}"
                        step="1"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        value="1"
                        required>
                </td>

                <td>
                    <input
                        type="number"
                        name="descuento_linea[]"
                        class="input desc-linea"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        value="0">
                </td>

                <td>
                    <input type="text" class="input total-linea" value="0.00" readonly>
                </td>

                <td class="cell-remove">
                    <div class="remove-button-wrap">
                        <button type="button" class="btn-remove-row">×</button>
                    </div>
                </td>
            `;

            attachRowEvents(row);
            tbody.appendChild(row);
            recalcRow(row, true);
            actualizarMensajeVacio();
        }

        function normalizeCantidadInput(input) {
            let value = input.value ?? "";
            value = value.replace(/[^\d]/g, "");
            input.value = value;
        }

        function normalizeDecimalInput(input) {
            let value = input.value ?? "";

            value = value.replace(",", ".");
            value = value.replace(/[^0-9.]/g, "");

            const parts = value.split(".");

            if (parts.length > 2) {
                value = parts[0] + "." + parts.slice(1).join("");
            }

            input.value = value;
        }

        function recalcRow(row, forceNormalize = false) {
            const precio = parseFloat(row.dataset.precio || "0");
            const stock = parseInt(row.dataset.stock || "0", 10);
            const cantidadInput = row.querySelector(".cantidad");
            const descInput = row.querySelector(".desc-linea");
            const totalInput = row.querySelector(".total-linea");

            if (!cantidadInput || !descInput || !totalInput) {
                return;
            }

            if (stock <= 0) {
                cantidadInput.setCustomValidity("Este producto no tiene stock disponible.");
                totalInput.value = "0.00";
                recalcTotals();
                return;
            }

            const rawCantidad = (cantidadInput.value || "").trim();

            if (rawCantidad === "") {
                if (forceNormalize) {
                    cantidadInput.value = "1";
                } else {
                    totalInput.value = "0.00";
                    recalcTotals();
                    return;
                }
            }

            let cantidad = parseInt(cantidadInput.value || "0", 10);

            if (Number.isNaN(cantidad)) {
                cantidad = 0;
            }

            if (forceNormalize) {
                cantidad = Math.max(1, Math.min(cantidad, stock));
                cantidadInput.value = String(cantidad);
                cantidadInput.setCustomValidity("");
            } else {
                if (cantidad < 1) {
                    cantidadInput.setCustomValidity("La cantidad mínima es 1.");
                    totalInput.value = "0.00";
                    recalcTotals();
                    return;
                }

                if (cantidad > stock) {
                    cantidadInput.setCustomValidity(`Stock máximo disponible: ${stock}.`);
                    cantidadInput.reportValidity();
                    totalInput.value = "0.00";
                    recalcTotals();
                    return;
                }

                cantidadInput.setCustomValidity("");
            }

            let descuento = parseFloat(descInput.value || "0");

            if (Number.isNaN(descuento) || descuento < 0) {
                descuento = 0;

                if (forceNormalize) {
                    descInput.value = "0";
                }
            }

            const subtotalLinea = precio * cantidad;
            const descuentoOk = Math.min(Math.max(descuento, 0), subtotalLinea);
            const totalLinea = subtotalLinea - descuentoOk;

            totalInput.value = totalLinea.toFixed(2);
            recalcTotals();
        }

        function recalcTotals() {
            let subtotal = 0;
            let descuentoLineas = 0;

            tbody.querySelectorAll(".item-row").forEach(row => {
                const precio = parseFloat(row.dataset.precio || "0");
                const cantidad = parseInt(row.querySelector(".cantidad")?.value || "0", 10);
                const descuento = parseFloat(row.querySelector(".desc-linea")?.value || "0");

                if (!Number.isNaN(precio) && !Number.isNaN(cantidad) && cantidad > 0) {
                    const subtotalLinea = precio * cantidad;
                    const descuentoLinea = Math.min(
                        Math.max(Number.isNaN(descuento) ? 0 : descuento, 0),
                        subtotalLinea
                    );

                    subtotal += subtotalLinea;
                    descuentoLineas += descuentoLinea;
                }
            });

            const descuentoGlobalInput = document.querySelector('input[name="descuento_global"]');
            const descuentoGlobal = parseFloat(descuentoGlobalInput?.value || "0");
            const descuentoGlobalSafe = Math.max(Number.isNaN(descuentoGlobal) ? 0 : descuentoGlobal, 0);

            const descuentoTotal = descuentoLineas + descuentoGlobalSafe;
            const base = Math.max(0, subtotal - descuentoTotal);
            const impuesto = base * IVA_RATE;
            const total = base + impuesto;

            document.getElementById("subtotal-view").textContent = formatMoney(subtotal);
            document.getElementById("descuento-global-view").textContent = formatMoney(descuentoGlobalSafe);
            document.getElementById("impuesto-view").textContent = formatMoney(impuesto);
            document.getElementById("total-view").textContent = formatMoney(total);

            actualizarResumenPago(total);

            return total;
        }

        function bloquearNumeroInvalido(event, permitirDecimal = false) {
            const allowedKeys = [
                "Backspace",
                "Delete",
                "ArrowLeft",
                "ArrowRight",
                "Tab",
                "Home",
                "End"
            ];

            if (allowedKeys.includes(event.key)) {
                return;
            }

            if (permitirDecimal && (event.key === "." || event.key === ",")) {
                if (event.target.value.includes(".") || event.target.value.includes(",")) {
                    event.preventDefault();
                }

                return;
            }

            if (/^[0-9]$/.test(event.key)) {
                return;
            }

            event.preventDefault();
        }

        function attachRowEvents(row) {
            const cantidad = row.querySelector(".cantidad");
            const descuentoLinea = row.querySelector(".desc-linea");

            if (cantidad) {
                cantidad.addEventListener("keydown", event => {
                    bloquearNumeroInvalido(event, false);
                });

                cantidad.addEventListener("paste", event => {
                    const pasted = (event.clipboardData || window.clipboardData).getData("text");

                    if (!/^\d+$/.test(pasted)) {
                        event.preventDefault();
                    }
                });

                cantidad.addEventListener("input", () => {
                    normalizeCantidadInput(cantidad);
                    recalcRow(row, false);
                });

                cantidad.addEventListener("blur", () => recalcRow(row, true));
                cantidad.addEventListener("change", () => recalcRow(row, true));
            }

            if (descuentoLinea) {
                descuentoLinea.addEventListener("keydown", event => {
                    bloquearNumeroInvalido(event, true);
                });

                descuentoLinea.addEventListener("paste", event => {
                    const pasted = (event.clipboardData || window.clipboardData).getData("text");

                    if (!/^\d*\.?\d*$/.test(pasted)) {
                        event.preventDefault();
                    }
                });

                descuentoLinea.addEventListener("input", () => {
                    normalizeDecimalInput(descuentoLinea);

                    if (parseFloat(descuentoLinea.value || "0") < 0) {
                        descuentoLinea.value = "0";
                    }

                    recalcRow(row, false);
                });

                descuentoLinea.addEventListener("blur", () => {
                    if (descuentoLinea.value.trim() === "") {
                        descuentoLinea.value = "0";
                    }

                    const value = parseFloat(descuentoLinea.value || "0");

                    if (Number.isNaN(value) || value < 0) {
                        descuentoLinea.value = "0";
                    }

                    recalcRow(row, true);
                });

                descuentoLinea.addEventListener("change", () => {
                    if (descuentoLinea.value.trim() === "") {
                        descuentoLinea.value = "0";
                    }

                    recalcRow(row, true);
                });
            }
        }

        if (btnAddSelected) {
            btnAddSelected.addEventListener("click", agregarProductoSeleccionado);
        }

        if (inputProducto) {
            inputProducto.addEventListener("keydown", event => {
                if (event.key === "Enter") {
                    event.preventDefault();
                    agregarProductoSeleccionado();
                }
            });
        }

        tbody.addEventListener("click", event => {
            const button = event.target.closest(".btn-remove-row");

            if (!button) {
                return;
            }

            const row = button.closest(".item-row");

            if (!row) {
                return;
            }

            row.remove();
            recalcTotals();
            actualizarMensajeVacio();
        });

        const descuentoGlobalInput = document.querySelector('input[name="descuento_global"]');

        if (descuentoGlobalInput) {
            descuentoGlobalInput.addEventListener("keydown", event => {
                bloquearNumeroInvalido(event, true);
            });

            descuentoGlobalInput.addEventListener("input", () => {
                normalizeDecimalInput(descuentoGlobalInput);
                recalcTotals();
            });

            descuentoGlobalInput.addEventListener("blur", () => {
                if (descuentoGlobalInput.value.trim() === "") {
                    descuentoGlobalInput.value = "0";
                }

                recalcTotals();
            });
        }

        if (montoPagadoInput) {
            montoPagadoInput.addEventListener("keydown", event => {
                bloquearNumeroInvalido(event, true);
            });

            montoPagadoInput.addEventListener("input", () => {
                normalizeDecimalInput(montoPagadoInput);
                recalcTotals();
            });

            montoPagadoInput.addEventListener("blur", () => {
                if (montoPagadoInput.value.trim() === "") {
                    montoPagadoInput.value = "0";
                }

                recalcTotals();
            });
        }

        if (form) {
            form.addEventListener("submit", event => {
                const filas = tbody.querySelectorAll(".item-row");

                if (filas.length === 0) {
                    event.preventDefault();
                    alert("Debe agregar al menos un producto a la factura.");
                    return;
                }

                if (
                    selectTipoCliente &&
                    selectTipoCliente.value === "<?= TIPO_CLIENTE_HABITUAL ?>"
                ) {
                    actualizarClienteSeleccionado();

                    if (!clienteHiddenInput || clienteHiddenInput.value.trim() === "") {
                        event.preventDefault();
                        alert("Debe seleccionar un cliente habitual válido de la lista.");
                        return;
                    }
                }

                const totalFactura = recalcTotals();

                const montoPagado = parseFloat(montoPagadoInput?.value || "0");
                const minimoRequerido = totalFactura * 0.50;

                if (Number.isNaN(montoPagado) || montoPagado < minimoRequerido) {
                    event.preventDefault();

                    alert(
                        "El cliente debe pagar al menos el 50% del total para iniciar la producción.\n\n" +
                        "Mínimo requerido: " + formatMoney(minimoRequerido)
                    );

                    return;
                }

                if (montoPagado > totalFactura) {
                    event.preventDefault();

                    alert("El monto pagado no puede ser mayor al total de la factura.");

                    return;
                }

                if (fechaEntregaEstimadaInput && fechaEntregaEstimadaInput.value.trim() === "") {
                    event.preventDefault();

                    alert("Debe seleccionar una fecha estimada de entrega.");

                    return;
                }

                if (
                    selectTipoCliente &&
                    selectTipoCliente.value === "<?= TIPO_CLIENTE_FUGAZ ?>" &&
                    totalFactura > LIMITE_CLIENTE_FUGAZ
                ) {
                    event.preventDefault();

                    alert(
                        "Un cliente fugaz no puede realizar una compra mayor a C$ " +
                        LIMITE_CLIENTE_FUGAZ.toFixed(2) +
                        ".\n\nPara continuar con esta venta, registre al cliente como cliente habitual."
                    );

                    return;
                }

                let invalidFound = false;

                filas.forEach(row => {
                    recalcRow(row, true);

                    const cantidad = row.querySelector(".cantidad");
                    const descuentoLinea = row.querySelector(".desc-linea");

                    if (cantidad && !cantidad.checkValidity()) {
                        invalidFound = true;
                        cantidad.reportValidity();
                    }

                    if (descuentoLinea && !descuentoLinea.checkValidity()) {
                        invalidFound = true;
                        descuentoLinea.reportValidity();
                    }
                });

                if (invalidFound) {
                    event.preventDefault();
                }
            });
        }

        actualizarClienteSeleccionado();
        actualizarMensajeVacio();
        recalcTotals();
    })();
</script>