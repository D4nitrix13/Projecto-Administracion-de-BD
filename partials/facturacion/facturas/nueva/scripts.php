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
        const productoDropdown = document.getElementById("producto-picker-dropdown");
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

            const saldoPendiente = Math.max(0, total - montoPagado);
            const porcentajePagado = total > 0 ? Math.round((montoPagado / total) * 100 * 100) / 100 : 0;

            if (minimoRequeridoView) {
                minimoRequeridoView.textContent = porcentajePagado.toFixed(1) + "%";
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
                const mitadTotal = Math.round(total * 0.50 * 100) / 100;
                paymentWarning.style.display =
                    total > 0 && montoPagado > 0 && montoPagado < mitadTotal ? "flex" : "none";
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

        let productoSeleccionado = null;
        let indiceActivoDropdown = -1;

        function filtrarProductos(query) {
            const queryNormalizado = normalizarTexto(query);

            if (queryNormalizado.length === 0) {
                return [];
            }

            return productos.filter(producto => {
                const nombre = producto.nombre || "";
                const codigo = producto.codigo || "";
                const nombreNormalizado = normalizarTexto(nombre);
                const codigoNormalizado = normalizarTexto(codigo);

                return nombreNormalizado.includes(queryNormalizado) ||
                    codigoNormalizado.includes(queryNormalizado);
            }).slice(0, 10);
        }

        function renderDropdown(resultados) {
            productoDropdown.innerHTML = "";
            indiceActivoDropdown = -1;

            if (resultados.length === 0) {
                productoDropdown.innerHTML = `<div class="producto-picker-empty">No se encontraron productos.</div>`;
                productoDropdown.style.display = "block";
                return;
            }

            resultados.forEach((producto, index) => {
                const stock = parseInt(producto.stock || "0", 10);
                const precio = parseFloat(producto.precio_venta || "0");

                let stockClass = "producto-picker-option-stock-ok";
                let stockText = `Stock: ${stock}`;

                if (stock <= 0) {
                    stockClass = "producto-picker-option-stock-empty";
                    stockText = "Sin stock";
                } else if (stock <= 5) {
                    stockClass = "producto-picker-option-stock-low";
                    stockText = `Stock bajo: ${stock}`;
                }

                const option = document.createElement("div");
                option.className = "producto-picker-option";
                option.dataset.index = index;

                option.innerHTML = `
                    <div class="producto-picker-option-info">
                        <span class="producto-picker-option-name">${escapeHtml(producto.nombre)}</span>
                        <span class="producto-picker-option-code">${escapeHtml(producto.codigo)}</span>
                    </div>
                    <div class="producto-picker-option-meta">
                        <span>C$ ${precio.toFixed(2)}</span>
                        <span class="${stockClass}">${stockText}</span>
                    </div>
                `;

                option.addEventListener("click", () => {
                    seleccionarProducto(producto);
                });

                option.addEventListener("mouseenter", () => {
                    indiceActivoDropdown = index;
                    actualizarIndiceActivo();
                });

                productoDropdown.appendChild(option);
            });

            productoDropdown.style.display = "block";
        }

        function actualizarIndiceActivo() {
            const opciones = productoDropdown.querySelectorAll(".producto-picker-option");
            opciones.forEach((option, index) => {
                option.classList.toggle("active", index === indiceActivoDropdown);
            });
        }

        function seleccionarProducto(producto) {
            productoSeleccionado = producto;
            inputProducto.value = `${producto.nombre} (${producto.codigo})`;
            productoDropdown.style.display = "none";
            productoDropdown.innerHTML = "";
        }

        function obtenerProductoSeleccionado() {
            if (productoSeleccionado) {
                const textoInput = normalizarTexto(inputProducto.value);
                const textoProducto = normalizarTexto(`${productoSeleccionado.nombre} (${productoSeleccionado.codigo})`);

                if (textoInput === textoProducto) {
                    return productoSeleccionado;
                }
            }

            const valor = normalizarTexto(inputProducto.value);

            return productos.find(producto => {
                const nombre = producto.nombre || "";
                const codigo = producto.codigo || "";
                const opcion = `${nombre} (${codigo})`;

                return normalizarTexto(opcion) === valor ||
                    normalizarTexto(nombre) === valor ||
                    normalizarTexto(codigo) === valor;
            }) || null;
        }

        inputProducto.addEventListener("input", () => {
            const query = inputProducto.value;
            const resultados = filtrarProductos(query);
            renderDropdown(resultados);
            productoSeleccionado = null;
        });

        inputProducto.addEventListener("keydown", (event) => {
            const opciones = productoDropdown.querySelectorAll(".producto-picker-option");
            const totalOpciones = opciones.length;

            if (event.key === "ArrowDown") {
                event.preventDefault();
                indiceActivoDropdown = (indiceActivoDropdown + 1) % totalOpciones;
                actualizarIndiceActivo();
            } else if (event.key === "ArrowUp") {
                event.preventDefault();
                indiceActivoDropdown = (indiceActivoDropdown - 1 + totalOpciones) % totalOpciones;
                actualizarIndiceActivo();
            } else if (event.key === "Enter") {
                event.preventDefault();

                if (indiceActivoDropdown >= 0 && indiceActivoDropdown < totalOpciones) {
                    const resultados = filtrarProductos(inputProducto.value);
                    if (resultados[indiceActivoDropdown]) {
                        seleccionarProducto(resultados[indiceActivoDropdown]);
                    }
                } else {
                    agregarProductoSeleccionado();
                }
            } else if (event.key === "Escape") {
                productoDropdown.style.display = "none";
            }
        });

        inputProducto.addEventListener("blur", () => {
            setTimeout(() => {
                productoDropdown.style.display = "none";
            }, 200);
        });

        document.addEventListener("click", (event) => {
            if (!event.target.closest(".product-picker-search")) {
                productoDropdown.style.display = "none";
            }
        });

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
            const producto = obtenerProductoSeleccionado();

            if (!producto) {
                showToast("Seleccione un producto válido de la lista.", "error");
                return;
            }

            const idProducto = parseInt(producto.id_producto, 10);

            if (productoYaAgregado(idProducto)) {
                showToast("Este producto ya fue agregado. Puede modificar la cantidad en la tabla.", "warning");
                inputProducto.value = "";
                productoSeleccionado = null;
                return;
            }

            if (parseInt(producto.stock || "0", 10) <= 0) {
                showToast("Este producto no tiene stock disponible.", "error");
                return;
            }

            crearFilaProducto(producto);
            inputProducto.value = "";
            productoSeleccionado = null;
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

            const totalCalculadoInput = document.getElementById("total-calculado-input");
            if (totalCalculadoInput) {
                totalCalculadoInput.value = total.toFixed(2);
            }

            actualizarResumenPago(total);

            if (typeof initPlazos === "function" && !window._plazosRecalculando) {
                window._plazosRecalculando = true;
                setTimeout(() => {
                    initPlazos();
                    window._plazosRecalculando = false;
                }, 10);
            }

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
                    showToast("Debe agregar al menos un producto a la factura.", "error");
                    return;
                }

                if (
                    selectTipoCliente &&
                    selectTipoCliente.value === "<?= TIPO_CLIENTE_HABITUAL ?>"
                ) {
                    actualizarClienteSeleccionado();

                    if (!clienteHiddenInput || clienteHiddenInput.value.trim() === "") {
                        event.preventDefault();
                        showToast("Debe seleccionar un cliente habitual válido de la lista.", "error");
                        return;
                    }
                }

                const totalFactura = recalcTotals();

                const montoPagado = parseFloat(montoPagadoInput?.value || "0");
                const minimoRequerido = Math.round(totalFactura * 0.50 * 100) / 100;

                if (Number.isNaN(montoPagado) || montoPagado < 0) {
                    event.preventDefault();
                    showToast("El monto pagado no puede ser negativo.", "error");
                    return;
                }

                if (montoPagado > totalFactura) {
                    event.preventDefault();
                    showToast("El monto pagado no puede ser mayor al total de la factura.", "error");
                    return;
                }

                if (totalFactura > 0 && montoPagado < minimoRequerido) {
                    event.preventDefault();

                    confirmAction(
                        "El pago inicial es menor al 50% del total.\n\n" +
                        "Total: " + formatMoney(totalFactura) + "\n" +
                        "Pago inicial: " + formatMoney(montoPagado) + "\n" +
                        "Saldo pendiente: " + formatMoney(totalFactura - montoPagado) + "\n\n" +
                        "¿Desea continuar y configurar un plan de pagos para el saldo pendiente?",
                        () => {
                            form.submit();
                        }
                    );
                    return;
                }

                if (fechaEntregaEstimadaInput && fechaEntregaEstimadaInput.value.trim() === "") {
                    event.preventDefault();
                    showToast("Debe seleccionar una fecha estimada de entrega.", "error");
                    return;
                }

                if (
                    selectTipoCliente &&
                    selectTipoCliente.value === "<?= TIPO_CLIENTE_FUGAZ ?>" &&
                    totalFactura > LIMITE_CLIENTE_FUGAZ
                ) {
                    event.preventDefault();
                    showToast(
                        "Un cliente fugaz no puede realizar una compra mayor a C$ " +
                        LIMITE_CLIENTE_FUGAZ.toFixed(2) +
                        ". Para continuar con esta venta, registre al cliente como cliente habitual.",
                        "error"
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
                        showToast("Revise las cantidades de los productos.", "error");
                    }

                    if (descuentoLinea && !descuentoLinea.checkValidity()) {
                        invalidFound = true;
                        showToast("Revise los descuentos de los productos.", "error");
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

        // =========================================
        // PLAZOS - Payment Plan Configuration
        // =========================================

        const plazosSection = document.getElementById("invoice-plazos-section");
        const plazosNumeroInput = document.getElementById("plazos-numero");
        const plazosTableWrapper = document.getElementById("plazos-table-wrapper");
        const plazosTbody = document.getElementById("plazos-tbody");
        const plazosDataInput = document.getElementById("plazos-data-input");

        let plazosData = [];

        function getTodayStr() {
            const d = new Date();
            return d.toISOString().split("T")[0];
        }

        function addDays(dateStr, days) {
            const d = new Date(dateStr + "T00:00:00");
            d.setDate(d.getDate() + days);
            return d.toISOString().split("T")[0];
        }

        function generarPlazos() {
            const total = recalcTotals();
            const montoPagado = parseFloat(montoPagadoInput?.value || "0");
            const saldoPendiente = Math.max(0, total - montoPagado);

            if (saldoPendiente <= 0.01) {
                plazosTableWrapper.style.display = "none";
                plazosData = [];
                plazosDataInput.value = "";
                return;
            }

            const numCuotas = parseInt(plazosNumeroInput?.value || "1", 10);

            if (numCuotas < 1 || isNaN(numCuotas)) {
                plazosTableWrapper.style.display = "none";
                plazosData = [];
                plazosDataInput.value = "";
                return;
            }

            plazosTableWrapper.style.display = "block";

            const hoy = getTodayStr();
            const diasVentana = 15;
            const diasPorCuota = Math.max(1, Math.floor(diasVentana / numCuotas));

            const porcentajePorCuota = Math.round((100 / numCuotas) * 100) / 100;
            let porcentajeAcumulado = 0;

            plazosData = [];

            for (let i = 0; i < numCuotas; i++) {
                let porcentaje = porcentajePorCuota;

                if (i === numCuotas - 1) {
                    porcentaje = Math.round((100 - porcentajeAcumulado) * 100) / 100;
                }

                porcentajeAcumulado += porcentaje;

                const montoCuota = Math.round(saldoPendiente * porcentaje / 100 * 100) / 100;
                const fechaCuota = addDays(hoy, diasPorCuota * (i + 1));

                plazosData.push({
                    numero: i + 1,
                    porcentaje: porcentaje,
                    monto: montoCuota,
                    fecha_pago: fechaCuota,
                    observaciones: ""
                });
            }

            renderPlazosTable();
            updatePlazosHiddenInput();
        }

        function renderPlazosTable() {
            if (!plazosTbody) return;

            plazosTbody.innerHTML = "";

            let totalConfigurado = 0;

            plazosData.forEach((cuota, index) => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${cuota.numero}</td>
                    <td>
                        <input
                            type="number"
                            class="input plazo-porcentaje"
                            min="0"
                            max="100"
                            step="0.01"
                            value="${cuota.porcentaje}"
                            data-index="${index}">
                    </td>
                    <td>
                        <input
                            type="number"
                            class="input plazo-monto"
                            min="0"
                            step="0.01"
                            value="${cuota.monto.toFixed(2)}"
                            data-index="${index}">
                    </td>
                    <td>
                        <input
                            type="date"
                            class="input plazo-fecha"
                            value="${cuota.fecha_pago}"
                            data-index="${index}">
                    </td>
                    <td>
                        <input
                            type="text"
                            class="input plazo-obs"
                            value="${cuota.observaciones || ""}"
                            placeholder="Opcional..."
                            data-index="${index}">
                    </td>
                    <td>
                        <button type="button" class="btn-remove-plazo" data-index="${index}">×</button>
                    </td>
                `;
                totalConfigurado += cuota.monto;
                plazosTbody.appendChild(tr);
            });
        }

        function updatePlazosHiddenInput() {
            if (plazosDataInput) {
                plazosDataInput.value = JSON.stringify(plazosData);
            }
        }

        function recalcPlazosFromInputs() {
            const total = recalcTotals();
            const montoPagado = parseFloat(montoPagadoInput?.value || "0");
            const saldoPendiente = Math.max(0, total - montoPagado);

            plazosData.forEach(cuota => {
                cuota.monto = Math.round(saldoPendiente * cuota.porcentaje / 100 * 100) / 100;
            });

            let totalConfig = 0;
            plazosData.forEach(c => { totalConfig += c.monto; });

            if (plazosTotalConfigurado) {
                plazosTotalConfigurado.textContent = formatMoney(totalConfig);
            }

            renderPlazosTable();
            updatePlazosHiddenInput();
        }

        if (plazosNumeroInput) {
            plazosNumeroInput.addEventListener("input", generarPlazos);
        }

        if (plazosTbody) {
            plazosTbody.addEventListener("input", event => {
                const target = event.target;
                const index = parseInt(target.dataset.index, 10);

                if (Number.isNaN(index) || !plazosData[index]) return;

                if (target.classList.contains("plazo-porcentaje")) {
                    let val = parseFloat(target.value);
                    if (Number.isNaN(val) || val < 0) val = 0;
                    if (val > 100) val = 100;
                    plazosData[index].porcentaje = val;
                    recalcPlazosFromInputs();
                }

                if (target.classList.contains("plazo-monto")) {
                    const total = recalcTotals();
                    const montoPagado = parseFloat(montoPagadoInput?.value || "0");
                    const saldoPendiente = Math.max(0, total - montoPagado);

                    let val = parseFloat(target.value);
                    if (Number.isNaN(val) || val < 0) val = 0;
                    if (val > saldoPendiente) val = saldoPendiente;

                    plazosData[index].monto = val;
                    plazosData[index].porcentaje = saldoPendiente > 0
                        ? Math.round((val / saldoPendiente) * 100 * 100) / 100
                        : 0;

                    let totalConfig = 0;
                    plazosData.forEach(c => { totalConfig += c.monto; });
                    if (plazosTotalConfigurado) {
                        plazosTotalConfigurado.textContent = formatMoney(totalConfig);
                    }

                    renderPlazosTable();
                    updatePlazosHiddenInput();
                }

                if (target.classList.contains("plazo-fecha")) {
                    plazosData[index].fecha_pago = target.value;
                    updatePlazosHiddenInput();
                }

                if (target.classList.contains("plazo-obs")) {
                    plazosData[index].observaciones = target.value;
                    updatePlazosHiddenInput();
                }
            });

            plazosTbody.addEventListener("click", event => {
                const btn = event.target.closest(".btn-remove-plazo");
                if (!btn) return;

                const index = parseInt(btn.dataset.index, 10);
                if (Number.isNaN(index) || !plazosData[index]) return;

                plazosData.splice(index, 1);

                plazosData.forEach((c, i) => {
                    c.numero = i + 1;
                });

                renderPlazosTable();
                updatePlazosHiddenInput();
            });
        }

        function initPlazos() {
            const total = recalcTotals();
            const montoPagado = parseFloat(montoPagadoInput?.value || "0");
            const saldoPendiente = Math.max(0, total - montoPagado);

            const numCuotas = parseInt(plazosNumeroInput?.value || "1", 10);
            if (numCuotas >= 1 && !isNaN(numCuotas) && total > 0 && saldoPendiente > 0.01) {
                generarPlazos();
            } else {
                plazosTableWrapper.style.display = "none";
                plazosData = [];
                plazosDataInput.value = "";
            }
        }

        if (montoPagadoInput) {
            montoPagadoInput.addEventListener("input", () => {
                setTimeout(initPlazos, 50);
            });
        }

        initPlazos();
    })();
</script>