<?php
$tipoClienteVenta = $factura["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL;
$idClienteActual = (int)($factura["id_cliente"] ?? 0);
$idUsuarioActual = (int)($factura["id_usuario"] ?? 0);
$idSeccionActual = (int)($factura["id_seccion"] ?? 0);

$montoPagadoActual = (float)($factura["monto_pagado"] ?? 0);
$fechaEntregaEstimadaActual = $factura["fecha_entrega_estimada"] ?? "";
?>

<form method="POST" class="factura-edit-card" id="facturaEditForm">
    <?= csrfField() ?>
    <input type="hidden" name="id_factura" value="<?= (int)$idFactura ?>">

    <section class="factura-edit-block">
        <div class="factura-edit-grid">
            <label class="factura-edit-field">
                <span>Fecha</span>
                <input
                    type="datetime-local"
                    name="fecha"
                    value="<?= htmlspecialchars(facturaEditarFechaInput($factura["fecha"] ?? null)) ?>">
            </label>

            <label class="factura-edit-field">
                <span>Trabajador / usuario</span>

                <input
                    type="hidden"
                    name="id_usuario"
                    id="facturaEditUsuarioId"
                    value="<?= $idUsuarioActual ?>">

                <input
                    type="text"
                    id="facturaEditUsuarioFiltro"
                    placeholder="Filtrar trabajador..."
                    autocomplete="off">

                <div class="factura-edit-filter-list" id="facturaEditUsuarioLista"></div>
            </label>

            <label class="factura-edit-field">
                <span>Sección</span>
                <select name="id_seccion">
                    <?php foreach ($secciones as $seccion): ?>
                        <option
                            value="<?= (int)$seccion["id_seccion"] ?>"
                            <?= (int)$seccion["id_seccion"] === $idSeccionActual ? "selected" : "" ?>>
                            <?= htmlspecialchars($seccion["nombre"] ?? "Sección") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="factura-edit-field">
                <span>Descuento global</span>
                <input
                    type="number"
                    name="descuento_global"
                    id="facturaEditDescuentoGlobal"
                    class="factura-edit-number-decimal"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars((string)($factura["descuento"] ?? "0")) ?>">
            </label>
        </div>
    </section>

    <section class="factura-edit-section">
        <h2>Cliente</h2>

        <div class="factura-edit-client-type">
            <label>
                <input
                    type="radio"
                    name="tipo_cliente_venta"
                    value="<?= TIPO_CLIENTE_HABITUAL ?>"
                    <?= $tipoClienteVenta === TIPO_CLIENTE_HABITUAL ? "checked" : "" ?>>
                Cliente habitual
            </label>

            <label>
                <input
                    type="radio"
                    name="tipo_cliente_venta"
                    value="<?= TIPO_CLIENTE_FUGAZ ?>"
                    <?= $tipoClienteVenta === TIPO_CLIENTE_FUGAZ ? "checked" : "" ?>>
                Cliente fugaz
            </label>
        </div>

        <div class="factura-edit-field" id="facturaEditClienteHabitualBox">
            <span>Cliente habitual</span>

            <input
                type="hidden"
                name="id_cliente"
                id="facturaEditClienteId"
                value="<?= $idClienteActual ?>">

            <input
                type="text"
                id="facturaEditClienteFiltro"
                placeholder="Filtrar cliente por nombre, teléfono o identificación..."
                autocomplete="off">

            <div class="factura-edit-filter-list" id="facturaEditClienteLista"></div>
        </div>

        <label class="factura-edit-field" id="facturaEditClienteFugazBox">
            <span>Nombre cliente fugaz</span>
            <input
                type="text"
                name="nombre_cliente_fugaz"
                value="<?= htmlspecialchars((string)($factura["nombre_cliente_fugaz"] ?? "")) ?>"
                placeholder="Ejemplo: Cliente rápido">
        </label>
    </section>

    <section class="factura-edit-section">
        <div class="factura-edit-section-title">
            <div>
                <h2>Productos</h2>
                <p>Seleccione productos, revise stock disponible y ajuste cantidades o descuentos.</p>
            </div>

            <button
                type="button"
                class="factura-edit-btn factura-edit-btn-secondary"
                id="facturaEditAgregarProducto">
                Agregar producto
            </button>
        </div>

        <div id="facturaEditProductos" class="factura-edit-products"></div>
    </section>

    <section class="factura-edit-section factura-edit-payment-section">
        <div class="factura-edit-section-title">
            <div>
                <h2>Pago y entrega</h2>
                <p>Actualice el abono del cliente y la fecha estimada de entrega.</p>
            </div>
        </div>

        <div class="factura-edit-payment-grid">
            <label class="factura-edit-field">
                <span>Monto pagado</span>
                <input
                    type="number"
                    name="monto_pagado"
                    id="facturaEditMontoPagado"
                    class="factura-edit-number-decimal"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars(number_format($montoPagadoActual, 2, ".", "")) ?>">
            </label>

            <label class="factura-edit-field">
                <span>Fecha estimada de entrega</span>
                <input
                    type="date"
                    name="fecha_entrega_estimada"
                    id="facturaEditFechaEntregaEstimada"
                    value="<?= htmlspecialchars((string)$fechaEntregaEstimadaActual) ?>">
            </label>
        </div>

        <div class="factura-edit-payment-summary">
            <div>
                <span>Mínimo requerido</span>
                <strong id="facturaEditMinimoRequerido">C$ 0.00</strong>
            </div>

            <div>
                <span>Saldo pendiente</span>
                <strong id="facturaEditSaldoPendiente">C$ 0.00</strong>
            </div>

            <div>
                <span>Estado de pago</span>
                <strong id="facturaEditEstadoPago" class="factura-edit-payment-status pending">
                    Pendiente
                </strong>
            </div>
        </div>

        <div id="facturaEditAvisoPago" class="factura-edit-payment-warning" style="display:none;">
            <strong>Pago mínimo requerido</strong>
            <p>El cliente debe pagar al menos el 50% del total para iniciar o mantener la producción activa.</p>
        </div>
    </section>

    <aside class="factura-edit-summary">
        <div>
            <span>Subtotal</span>
            <strong id="facturaEditSubtotal">C$ 0.00</strong>
        </div>

        <div>
            <span>Descuento</span>
            <strong id="facturaEditDescuentoResumen">C$ 0.00</strong>
        </div>

        <div>
            <span>IVA 15%</span>
            <strong id="facturaEditIva">C$ 0.00</strong>
        </div>

        <div class="total">
            <span>Total</span>
            <strong id="facturaEditTotal">C$ 0.00</strong>
        </div>
    </aside>

    <div id="facturaEditAvisoFugaz" class="alert alert-danger factura-edit-alert-inline" style="display: none;"></div>

    <div class="factura-edit-actions">
        <a href="facturas.php" class="factura-edit-btn factura-edit-btn-secondary">
            Cancelar
        </a>

        <button type="submit" class="factura-edit-btn factura-edit-btn-primary">
            Guardar cambios
        </button>
    </div>
</form>

<script>
    const usuarios = <?= json_encode($usuarios, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const clientes = <?= json_encode($clientes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const productos = <?= json_encode($productos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const detallesIniciales = <?= json_encode($detalles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const clienteFacturaActual = <?= json_encode($clienteFactura, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const limiteClienteFugaz = <?= json_encode((float)$limiteClienteFugaz, JSON_UNESCAPED_UNICODE) ?>;

    const usuarioActual = <?= $idUsuarioActual ?>;
    const clienteActual = <?= $idClienteActual ?>;

    function money(value) {
        return "C$ " + Number(value || 0).toLocaleString("es-NI", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function normalizarTexto(texto) {
        return String(texto || "")
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function bloquearCaracteresNoNumericos(event, permiteDecimal = false) {
        const teclasPermitidas = [
            "Backspace",
            "Delete",
            "Tab",
            "ArrowLeft",
            "ArrowRight",
            "ArrowUp",
            "ArrowDown",
            "Home",
            "End"
        ];

        if (teclasPermitidas.includes(event.key)) {
            return;
        }

        if (permiteDecimal && (event.key === "." || event.key === ",")) {
            if (event.currentTarget.value.includes(".") || event.currentTarget.value.includes(",")) {
                event.preventDefault();
            }

            return;
        }

        if (!/^\d$/.test(event.key)) {
            event.preventDefault();
        }
    }

    function normalizarNumeroInput(input, permiteDecimal = false) {
        if (permiteDecimal) {
            let valor = input.value.replace(",", ".").replace(/[^\d.]/g, "");
            const partes = valor.split(".");

            if (partes.length > 2) {
                valor = partes[0] + "." + partes.slice(1).join("");
            }

            input.value = valor;
            return;
        }

        input.value = input.value.replace(/[^\d]/g, "");
    }

    function obtenerNombreUsuario(usuario) {
        return `${usuario.nombre || ""} ${usuario.apellido || ""}`.trim() ||
            usuario.usuario ||
            usuario.nombre_usuario ||
            "Usuario";
    }

    function obtenerNombreCliente(cliente) {
        return `${cliente.nombres || ""} ${cliente.apellidos || ""}`.trim() ||
            cliente.nombre ||
            "Cliente";
    }

    function nombreProducto(producto) {
        return producto.nombre || producto.producto || "Producto";
    }

    function precioProducto(producto) {
        return Number(producto.precio_venta || producto.precio || 0);
    }

    function stockProducto(producto) {
        return Number(producto.stock || 0);
    }

    function eliminarDuplicadosVisuales(lista, callback) {
        const usados = new Set();

        return lista.filter(item => {
            const key = callback(item);

            if (usados.has(key)) {
                return false;
            }

            usados.add(key);
            return true;
        });
    }

    function crearBuscador({
        inputId,
        hiddenId,
        listaId,
        data,
        idKey,
        getTitle,
        getSubtitle,
        selectedId
    }) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const lista = document.getElementById(listaId);

        if (!input || !hidden || !lista) {
            return;
        }

        const actual = data.find(item => Number(item[idKey]) === Number(selectedId));

        if (actual) {
            input.value = getTitle(actual);
            hidden.value = actual[idKey];
        }

        input.addEventListener("input", () => {
            const query = normalizarTexto(input.value);
            hidden.value = "";
            lista.innerHTML = "";

            if (query.length === 0) {
                return;
            }

            data.filter(item => {
                return normalizarTexto(JSON.stringify(item)).includes(query);
            }).slice(0, 8).forEach(item => {
                const button = document.createElement("button");
                button.type = "button";
                button.className = "factura-edit-filter-option";

                button.innerHTML = `
                    <strong>${getTitle(item)}</strong>
                    <small>${getSubtitle(item)}</small>
                `;

                button.addEventListener("click", () => {
                    input.value = getTitle(item);
                    hidden.value = item[idKey];
                    lista.innerHTML = "";
                });

                lista.appendChild(button);
            });

            if (lista.innerHTML === "") {
                lista.innerHTML = `<div class="factura-edit-filter-empty">Sin resultados.</div>`;
            }
        });
    }

    crearBuscador({
        inputId: "facturaEditUsuarioFiltro",
        hiddenId: "facturaEditUsuarioId",
        listaId: "facturaEditUsuarioLista",
        data: usuarios,
        idKey: "id_usuario",
        selectedId: usuarioActual,
        getTitle: obtenerNombreUsuario,
        getSubtitle: usuario => usuario.rol || usuario.email || "Trabajador"
    });

    crearBuscador({
        inputId: "facturaEditClienteFiltro",
        hiddenId: "facturaEditClienteId",
        listaId: "facturaEditClienteLista",
        data: clientes,
        idKey: "id_cliente",
        selectedId: clienteActual,
        getTitle: obtenerNombreCliente,
        getSubtitle: cliente => [
            cliente.telefono || "",
            cliente.identificacion || ""
        ].filter(Boolean).join(" · ") || "Cliente habitual"
    });

    const contenedor = document.getElementById("facturaEditProductos");
    const btnAgregar = document.getElementById("facturaEditAgregarProducto");
    const descuentoGlobalInput = document.getElementById("facturaEditDescuentoGlobal");
    const montoPagadoInput = document.getElementById("facturaEditMontoPagado");
    const fechaEntregaInput = document.getElementById("facturaEditFechaEntregaEstimada");
    const avisoFugaz = document.getElementById("facturaEditAvisoFugaz");
    const avisoPago = document.getElementById("facturaEditAvisoPago");

    function stockProductoEdicion(idProducto) {
        const producto = productos.find(item => Number(item.id_producto) === Number(idProducto));
        const stockBase = producto ? stockProducto(producto) : 0;

        let cantidadOriginal = 0;

        detallesIniciales.forEach(detalle => {
            if (Number(detalle.id_producto) === Number(idProducto)) {
                cantidadOriginal += Number(detalle.cantidad || 0);
            }
        });

        return stockBase + cantidadOriginal;
    }

    function crearFilaProducto(detalle = null) {
        const row = document.createElement("div");
        row.className = "factura-edit-product-row";

        const productoActual = detalle ?
            productos.find(producto => Number(producto.id_producto) === Number(detalle.id_producto)) :
            null;

        const idProducto = detalle ? Number(detalle.id_producto || 0) : 0;
        const nombre = productoActual ? nombreProducto(productoActual) : (detalle?.nombre_producto || "");
        const precio = productoActual ? precioProducto(productoActual) : Number(detalle?.precio_unitario || 0);
        const stock = idProducto > 0 ? stockProductoEdicion(idProducto) : 0;

        row.innerHTML = `
            <div class="factura-edit-product-main">
                <label class="factura-edit-field factura-edit-product-search">
                    <span>Producto</span>

                    <input
                        type="hidden"
                        name="id_producto[]"
                        class="id-producto"
                        value="${idProducto || ""}">

                    <input
                        type="text"
                        class="producto-filtro"
                        value="${nombre}"
                        placeholder="Buscar producto..."
                        autocomplete="off">

                    <div class="producto-lista"></div>
                </label>

                <div class="factura-edit-product-meta">
                    <div>
                        <span>Stock disponible</span>
                        <strong class="stock-disponible">${stock}</strong>
                    </div>

                    <div>
                        <span>Precio unitario</span>
                        <strong class="precio-unitario">${money(precio)}</strong>
                    </div>
                </div>
            </div>

            <label class="factura-edit-field factura-edit-qty-field">
                <span>Cantidad</span>
                <input
                    type="number"
                    name="cantidad[]"
                    class="cantidad factura-edit-number-integer"
                    min="1"
                    step="1"
                    value="${detalle ? Number(detalle.cantidad || 1) : 1}">
            </label>

            <label class="factura-edit-field factura-edit-discount-field">
                <span>Descuento</span>
                <input
                    type="number"
                    name="descuento_linea[]"
                    class="descuento-linea factura-edit-number-decimal"
                    min="0"
                    step="0.01"
                    value="${detalle ? Number(detalle.descuento_linea || 0).toFixed(2) : "0.00"}">
            </label>

            <div class="factura-edit-line-actions">
                <div class="factura-edit-line-total">
                    <span>Total línea</span>
                    <strong>C$ 0.00</strong>
                    <small class="factura-edit-line-warning"></small>
                </div>

                <button type="button" class="factura-edit-btn factura-edit-btn-danger factura-edit-remove-btn">
                    Quitar
                </button>
            </div>
        `;

        const input = row.querySelector(".producto-filtro");
        const hidden = row.querySelector(".id-producto");
        const lista = row.querySelector(".producto-lista");
        const stockInfo = row.querySelector(".stock-disponible");
        const precioInfo = row.querySelector(".precio-unitario");

        input.addEventListener("input", () => {
            hidden.value = "";
            lista.innerHTML = "";

            const query = normalizarTexto(input.value);

            if (query.length === 0) {
                calcularTotales();
                return;
            }

            const productosUnicos = eliminarDuplicadosVisuales(
                productos,
                producto => producto.id_producto
            );

            productosUnicos.filter(producto => {
                return normalizarTexto(JSON.stringify(producto)).includes(query);
            }).slice(0, 8).forEach(producto => {
                const button = document.createElement("button");
                button.type = "button";
                button.className = "factura-edit-filter-option";

                button.innerHTML = `
                    <strong>${nombreProducto(producto)}</strong>
                    <small>Precio: ${money(precioProducto(producto))} · Stock disponible: ${stockProductoEdicion(producto.id_producto)}</small>
                `;

                button.addEventListener("click", () => {
                    hidden.value = producto.id_producto;
                    input.value = nombreProducto(producto);
                    stockInfo.textContent = stockProductoEdicion(producto.id_producto);
                    precioInfo.textContent = money(precioProducto(producto));
                    lista.innerHTML = "";
                    calcularTotales();
                });

                lista.appendChild(button);
            });
        });

        row.querySelector(".factura-edit-remove-btn").addEventListener("click", () => {
            row.remove();
            calcularTotales();
        });

        row.querySelectorAll(".factura-edit-number-integer").forEach(input => {
            input.addEventListener("keydown", event => bloquearCaracteresNoNumericos(event, false));
            input.addEventListener("input", () => {
                normalizarNumeroInput(input, false);
                calcularTotales();
            });
        });

        row.querySelectorAll(".factura-edit-number-decimal").forEach(input => {
            input.addEventListener("keydown", event => bloquearCaracteresNoNumericos(event, true));
            input.addEventListener("input", () => {
                normalizarNumeroInput(input, true);
                calcularTotales();
            });
        });

        contenedor.appendChild(row);
        calcularTotales();
    }

    function obtenerCantidadesSolicitadasPorProducto() {
        const cantidades = {};

        document.querySelectorAll(".factura-edit-product-row").forEach(row => {
            const id = row.querySelector(".id-producto").value;
            const cantidad = Number(row.querySelector(".cantidad").value || 0);

            if (!id) {
                return;
            }

            if (!cantidades[id]) {
                cantidades[id] = 0;
            }

            cantidades[id] += cantidad;
        });

        return cantidades;
    }

    function calcularTotales() {
        let subtotal = 0;
        let descuentoLineas = 0;
        const cantidadesSolicitadas = obtenerCantidadesSolicitadasPorProducto();

        document.querySelectorAll(".factura-edit-product-row").forEach(row => {
            const idProducto = Number(row.querySelector(".id-producto").value || 0);
            const cantidadInput = row.querySelector(".cantidad");
            const descuentoInput = row.querySelector(".descuento-linea");
            const totalLineaView = row.querySelector(".factura-edit-line-total strong");
            const warning = row.querySelector(".factura-edit-line-warning");

            const producto = productos.find(item => Number(item.id_producto) === idProducto);
            const precio = producto ? precioProducto(producto) : 0;
            const cantidad = Number(cantidadInput.value || 0);
            const descuento = Math.max(0, Number(descuentoInput.value || 0));
            const stockDisponible = idProducto > 0 ? stockProductoEdicion(idProducto) : 0;

            row.classList.remove("factura-edit-row-error");
            warning.textContent = "";

            if (idProducto <= 0 || !producto || cantidad <= 0) {
                totalLineaView.textContent = money(0);
                return;
            }

            if (cantidadesSolicitadas[idProducto] > stockDisponible) {
                row.classList.add("factura-edit-row-error");
                warning.textContent = `Stock máximo disponible: ${stockDisponible}.`;
            }

            const subtotalLinea = precio * cantidad;
            const descuentoSeguro = Math.min(descuento, subtotalLinea);
            const totalLinea = subtotalLinea - descuentoSeguro;

            subtotal += subtotalLinea;
            descuentoLineas += descuentoSeguro;
            totalLineaView.textContent = money(totalLinea);
        });

        const descuentoGlobal = Math.max(0, Number(descuentoGlobalInput.value || 0));
        const descuentoTotal = descuentoLineas + descuentoGlobal;
        const base = Math.max(0, subtotal - descuentoTotal);
        const iva = base * 0.15;
        const total = base + iva;

        document.getElementById("facturaEditSubtotal").textContent = money(subtotal);
        document.getElementById("facturaEditDescuentoResumen").textContent = money(descuentoTotal);
        document.getElementById("facturaEditIva").textContent = money(iva);
        document.getElementById("facturaEditTotal").textContent = money(total);

        actualizarResumenPago(total);
        validarLimiteFugaz(total);

        return total;
    }

    function actualizarResumenPago(total) {
        const minimo = Math.round(total * 0.50 * 100) / 100;
        let montoPagado = Number(montoPagadoInput.value || 0);

        if (Number.isNaN(montoPagado) || montoPagado < 0) {
            montoPagado = 0;
        }

        if (montoPagado > total) {
            montoPagado = total;
            montoPagadoInput.value = total.toFixed(2);
        }

        const saldo = Math.max(0, total - montoPagado);
        const estado = document.getElementById("facturaEditEstadoPago");

        document.getElementById("facturaEditMinimoRequerido").textContent = money(minimo);
        document.getElementById("facturaEditSaldoPendiente").textContent = money(saldo);

        estado.classList.remove("pending", "partial", "paid");

        if (montoPagado <= 0) {
            estado.textContent = "Pendiente";
            estado.classList.add("pending");
        } else if (montoPagado >= total && total > 0) {
            estado.textContent = "Pagado";
            estado.classList.add("paid");
        } else {
            estado.textContent = "Parcial";
            estado.classList.add("partial");
        }

        avisoPago.style.display = total > 0 && montoPagado < minimo ? "block" : "none";
    }

    function validarLimiteFugaz(total) {
        const tipo = document.querySelector('input[name="tipo_cliente_venta"]:checked')?.value;

        if (tipo === "<?= TIPO_CLIENTE_FUGAZ ?>" && total > limiteClienteFugaz) {
            avisoFugaz.style.display = "block";
            avisoFugaz.textContent =
                "Un cliente fugaz no puede realizar una compra mayor a " +
                money(limiteClienteFugaz) +
                ". Para continuar, registre al cliente como habitual.";
            return false;
        }

        avisoFugaz.style.display = "none";
        avisoFugaz.textContent = "";
        return true;
    }

    function toggleTipoCliente() {
        const tipo = document.querySelector('input[name="tipo_cliente_venta"]:checked')?.value;
        const habitual = document.getElementById("facturaEditClienteHabitualBox");
        const fugaz = document.getElementById("facturaEditClienteFugazBox");

        if (tipo === "<?= TIPO_CLIENTE_FUGAZ ?>") {
            habitual.style.display = "none";
            fugaz.style.display = "grid";
        } else {
            habitual.style.display = "grid";
            fugaz.style.display = "none";
        }

        calcularTotales();
    }

    document.querySelectorAll('input[name="tipo_cliente_venta"]').forEach(input => {
        input.addEventListener("change", toggleTipoCliente);
    });

    [descuentoGlobalInput, montoPagadoInput].forEach(input => {
        if (!input) {
            return;
        }

        input.addEventListener("keydown", event => bloquearCaracteresNoNumericos(event, true));
        input.addEventListener("input", () => {
            normalizarNumeroInput(input, true);
            calcularTotales();
        });

        input.addEventListener("blur", () => {
            if (input.value.trim() === "") {
                input.value = "0.00";
            }

            calcularTotales();
        });
    });

    btnAgregar.addEventListener("click", () => crearFilaProducto());

    detallesIniciales.forEach(detalle => crearFilaProducto(detalle));

    if (detallesIniciales.length === 0) {
        crearFilaProducto();
    }

    toggleTipoCliente();
    calcularTotales();

    document.getElementById("facturaEditForm").addEventListener("submit", event => {
        const filas = document.querySelectorAll(".factura-edit-product-row");

        if (filas.length === 0) {
            event.preventDefault();
            showToast("Debe agregar al menos un producto.", "error");
            return;
        }

        const tipo = document.querySelector('input[name="tipo_cliente_venta"]:checked')?.value;
        const total = calcularTotales();
        const montoPagado = Number(montoPagadoInput.value || 0);
        const minimo = Math.round(total * 0.50 * 100) / 100;

        if (tipo === "<?= TIPO_CLIENTE_HABITUAL ?>" && !document.getElementById("facturaEditClienteId").value) {
            event.preventDefault();
            showToast("Debe seleccionar un cliente habitual válido.", "error");
            return;
        }

        if (tipo === "<?= TIPO_CLIENTE_FUGAZ ?>" && total > limiteClienteFugaz) {
            event.preventDefault();
            showToast("Un cliente fugaz no puede realizar una compra mayor a " + money(limiteClienteFugaz) + ".", "error");
            return;
        }

        if (Number.isNaN(montoPagado) || montoPagado < minimo) {
            event.preventDefault();
            showToast("El cliente debe pagar al menos el 50% del total. Mínimo requerido: " + money(minimo), "error");
            return;
        }

        if (montoPagado > total) {
            event.preventDefault();
            showToast("El monto pagado no puede ser mayor al total de la factura.", "error");
            return;
        }

        if (!fechaEntregaInput.value.trim()) {
            event.preventDefault();
            showToast("Debe seleccionar una fecha estimada de entrega.", "error");
            return;
        }

        let error = false;

        filas.forEach(row => {
            const idProducto = row.querySelector(".id-producto").value;
            const cantidad = Number(row.querySelector(".cantidad").value || 0);

            if (!idProducto || cantidad < 1 || row.classList.contains("factura-edit-row-error")) {
                error = true;
            }
        });

        if (error) {
            event.preventDefault();
            showToast("Revise los productos, cantidades y stock disponible.", "error");
        }
    });
</script>