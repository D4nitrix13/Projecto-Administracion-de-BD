<?php
$tipoClienteVenta = $factura["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL;
$idClienteActual = (int)($factura["id_cliente"] ?? 0);
$idUsuarioActual = (int)($factura["id_usuario"] ?? 0);
$idSeccionActual = (int)($factura["id_seccion"] ?? 0);
?>

<form method="POST" class="factura-edit-card" id="facturaEditForm">
    <input type="hidden" name="id_factura" value="<?= (int)$idFactura ?>">

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
                min="0"
                step="0.01"
                value="<?= htmlspecialchars((string)($factura["descuento"] ?? "0")) ?>">
        </label>
    </div>

    <div class="factura-edit-section">
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
                placeholder="Filtrar cliente..."
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
    </div>

    <div class="factura-edit-section">
        <div class="factura-edit-section-title">
            <div>
                <h2>Productos</h2>
                <p>Edite cantidades, descuentos o agregue nuevos productos.</p>
            </div>

            <button
                type="button"
                class="factura-edit-btn factura-edit-btn-secondary"
                id="facturaEditAgregarProducto">
                Agregar producto
            </button>
        </div>

        <div id="facturaEditProductos" class="factura-edit-products"></div>
    </div>

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

    function nombrePersona(item) {
        if (!item) {
            return "Sin nombre";
        }

        const nombresApellidos = [
            item.nombres,
            item.apellidos
        ].filter(Boolean).join(" ").trim();

        return (
            item.nombre ||
            item.nombre_cliente ||
            item.cliente ||
            item.nombre_completo ||
            item.nombre_usuario ||
            item.usuario ||
            item.username ||
            item.razon_social ||
            nombresApellidos ||
            "Sin nombre"
        );
    }

    function subtituloCliente(item) {
        return item.telefono || item.identificacion || item.tipo_cliente || "Cliente habitual";
    }

    function nombreProducto(producto) {
        if (!producto) {
            return "";
        }

        return `${producto.codigo || ""} ${producto.nombre || producto.producto || ""}`.trim();
    }

    function precioProducto(producto) {
        return Number(producto.precio_venta || producto.precio || producto.precio_unitario || 0);
    }

    function buscarProducto(id) {
        return productos.find(producto => Number(producto.id_producto) === Number(id));
    }

    function stockProductoEdicion(idProducto) {
        const producto = buscarProducto(idProducto);

        if (!producto) {
            return 0;
        }

        const stockBase = Number(producto.stock || 0);

        const cantidadOriginal = detallesIniciales
            .filter(detalle => Number(detalle.id_producto) === Number(idProducto))
            .reduce((total, detalle) => total + Number(detalle.cantidad || 0), 0);

        return stockBase + cantidadOriginal;
    }

    function eliminarDuplicadosVisuales(data, keyCallback) {
        const map = new Map();

        data.forEach(item => {
            const key = String(keyCallback(item)).toLowerCase().trim();

            if (!map.has(key)) {
                map.set(key, item);
            }
        });

        return Array.from(map.values());
    }

    function pintarFiltro(input, hidden, lista, data, idKey, render, onSelect, keyCallback = null) {
        const query = input.value.toLowerCase().trim();

        lista.innerHTML = "";

        if (query.length === 0) {
            return;
        }

        const fuente = keyCallback ?
            eliminarDuplicadosVisuales(data, keyCallback) :
            data;

        const filtrados = fuente.filter(item => {
            return JSON.stringify(item).toLowerCase().includes(query);
        }).slice(0, 8);

        if (filtrados.length === 0) {
            lista.innerHTML = `<div class="factura-edit-filter-empty">Sin resultados</div>`;
            return;
        }

        filtrados.forEach(item => {
            const button = document.createElement("button");
            button.type = "button";
            button.className = "factura-edit-filter-option";
            button.innerHTML = render(item);

            button.addEventListener("click", () => {
                hidden.value = item[idKey];
                input.value = onSelect ? onSelect(item) : nombrePersona(item);
                lista.innerHTML = "";
                calcularTotales();
            });

            lista.appendChild(button);
        });
    }

    function crearFiltro(inputId, hiddenId, listaId, data, idKey, render, onSelect, keyCallback = null) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const lista = document.getElementById(listaId);

        input.addEventListener("input", () => {
            hidden.value = "";
            pintarFiltro(input, hidden, lista, data, idKey, render, onSelect, keyCallback);
        });

        input.addEventListener("focus", () => {
            pintarFiltro(input, hidden, lista, data, idKey, render, onSelect, keyCallback);
        });
    }

    function crearFilaProducto(detalle = null) {
        const contenedor = document.getElementById("facturaEditProductos");
        const row = document.createElement("div");

        row.className = "factura-edit-product-row";

        const idProducto = detalle ? detalle.id_producto : "";
        const producto = detalle ? buscarProducto(detalle.id_producto) : null;
        const productoTexto = producto ? nombreProducto(producto) : "";
        const stockActual = producto ? stockProductoEdicion(producto.id_producto) : 0;

        row.innerHTML = `
        <input type="hidden" name="id_producto[]" class="id-producto" value="${idProducto}">

        <div class="factura-edit-field">
            <span>Producto</span>
            <input
                type="text"
                class="producto-filtro"
                value="${productoTexto}"
                placeholder="Filtrar producto..."
                autocomplete="off">

            <small class="factura-edit-stock-info">
                Stock disponible para edición: <strong>${stockActual}</strong>
            </small>

            <div class="factura-edit-filter-list producto-lista"></div>
        </div>

        <label class="factura-edit-field">
            <span>Cantidad</span>
            <input
                type="number"
                name="cantidad[]"
                class="cantidad"
                min="1"
                step="1"
                value="${detalle ? detalle.cantidad : 1}">
        </label>

        <label class="factura-edit-field">
            <span>Descuento</span>
            <input
                type="number"
                name="descuento_linea[]"
                class="descuento-linea"
                min="0"
                step="0.01"
                value="${detalle ? detalle.descuento_linea : "0.00"}">
        </label>

        <div class="factura-edit-line-total">
            <span>Total línea</span>
            <strong>C$ 0.00</strong>
            <small class="factura-edit-line-warning"></small>
        </div>

        <button type="button" class="factura-edit-btn factura-edit-btn-danger">
            Quitar
        </button>
    `;

        const input = row.querySelector(".producto-filtro");
        const hidden = row.querySelector(".id-producto");
        const lista = row.querySelector(".producto-lista");
        const stockInfo = row.querySelector(".factura-edit-stock-info strong");

        input.addEventListener("input", () => {
            hidden.value = "";

            const query = input.value.toLowerCase().trim();
            lista.innerHTML = "";

            if (query.length === 0) {
                return;
            }

            const productosUnicos = eliminarDuplicadosVisuales(
                productos,
                producto => producto.id_producto
            );

            productosUnicos.filter(producto => {
                return JSON.stringify(producto).toLowerCase().includes(query);
            }).slice(0, 8).forEach(producto => {
                const button = document.createElement("button");
                button.type = "button";
                button.className = "factura-edit-filter-option";

                button.innerHTML = `
                <strong>${nombreProducto(producto)}</strong>
                <small>Precio: ${money(precioProducto(producto))} · Stock: ${stockProductoEdicion(producto.id_producto)}</small>
            `;

                button.addEventListener("click", () => {
                    hidden.value = producto.id_producto;
                    input.value = nombreProducto(producto);
                    stockInfo.textContent = stockProductoEdicion(producto.id_producto);
                    lista.innerHTML = "";
                    calcularTotales();
                });

                lista.appendChild(button);
            });
        });

        row.querySelector(".factura-edit-btn-danger").addEventListener("click", () => {
            row.remove();
            calcularTotales();
        });

        row.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", calcularTotales);
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

        const cantidadesPorProducto = obtenerCantidadesSolicitadasPorProducto();

        document.querySelectorAll(".factura-edit-product-row").forEach(row => {
            const id = row.querySelector(".id-producto").value;
            const producto = buscarProducto(id);
            const cantidad = Number(row.querySelector(".cantidad").value || 0);
            const descuento = Number(row.querySelector(".descuento-linea").value || 0);
            const precio = producto ? precioProducto(producto) : 0;
            const totalLinea = Math.max((precio * cantidad) - descuento, 0);
            const warning = row.querySelector(".factura-edit-line-warning");

            subtotal += precio * cantidad;
            descuentoLineas += descuento;

            row.querySelector(".factura-edit-line-total strong").textContent = money(totalLinea);

            if (producto) {
                const disponible = stockProductoEdicion(id);
                const solicitadoTotal = cantidadesPorProducto[id] || 0;

                if (solicitadoTotal > disponible) {
                    warning.textContent = `Stock insuficiente. Disponible: ${disponible}`;
                    row.classList.add("factura-edit-row-error");
                } else {
                    warning.textContent = "";
                    row.classList.remove("factura-edit-row-error");
                }
            } else {
                warning.textContent = "";
                row.classList.remove("factura-edit-row-error");
            }
        });

        const descuentoGlobal = Number(document.getElementById("facturaEditDescuentoGlobal").value || 0);
        const descuentoTotal = descuentoLineas + descuentoGlobal;
        const base = Math.max(subtotal - descuentoTotal, 0);
        const iva = base * 0.15;
        const total = base + iva;

        document.getElementById("facturaEditSubtotal").textContent = money(subtotal);
        document.getElementById("facturaEditDescuentoResumen").textContent = money(descuentoTotal);
        document.getElementById("facturaEditIva").textContent = money(iva);
        document.getElementById("facturaEditTotal").textContent = money(total);

        validarLimiteFugaz(total);
    }

    function validarLimiteFugaz(total) {
        const tipo = document.querySelector('input[name="tipo_cliente_venta"]:checked')?.value || "Habitual";
        const aviso = document.getElementById("facturaEditAvisoFugaz");

        if (tipo === "Fugaz" && total > limiteClienteFugaz) {
            aviso.textContent = `Un cliente fugaz no puede comprar más de ${money(limiteClienteFugaz)}. Cambie a cliente habitual o reduzca el total.`;
            aviso.style.display = "block";
        } else {
            aviso.style.display = "none";
        }
    }

    function toggleCliente() {
        const tipo = document.querySelector('input[name="tipo_cliente_venta"]:checked').value;

        document.getElementById("facturaEditClienteHabitualBox").style.display = tipo === "Habitual" ? "grid" : "none";
        document.getElementById("facturaEditClienteFugazBox").style.display = tipo === "Fugaz" ? "grid" : "none";

        calcularTotales();
    }

    document.addEventListener("click", event => {
        if (!event.target.closest(".factura-edit-field")) {
            document.querySelectorAll(".factura-edit-filter-list").forEach(lista => {
                lista.innerHTML = "";
            });
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        const usuario = usuarios.find(usuario => Number(usuario.id_usuario) === Number(usuarioActual));

        if (usuario) {
            document.getElementById("facturaEditUsuarioFiltro").value = nombrePersona(usuario);
        }

        const cliente = clientes.find(cliente => Number(cliente.id_cliente) === Number(clienteActual));

        if (cliente) {
            document.getElementById("facturaEditClienteFiltro").value = nombrePersona(cliente);
        } else if (clienteFacturaActual) {
            document.getElementById("facturaEditClienteFiltro").value = nombrePersona(clienteFacturaActual);
        }

        crearFiltro(
            "facturaEditUsuarioFiltro",
            "facturaEditUsuarioId",
            "facturaEditUsuarioLista",
            usuarios,
            "id_usuario",
            item => `<strong>${nombrePersona(item)}</strong><small>${item.email || "Trabajador del sistema"}</small>`,
            item => nombrePersona(item),
            item => `${nombrePersona(item)}|${item.email || ""}`
        );

        crearFiltro(
            "facturaEditClienteFiltro",
            "facturaEditClienteId",
            "facturaEditClienteLista",
            clientes,
            "id_cliente",
            item => `<strong>${nombrePersona(item)}</strong><small>${subtituloCliente(item)}</small>`,
            item => nombrePersona(item),
            item => `${nombrePersona(item)}|${item.telefono || ""}|${item.identificacion || ""}`
        );

        detallesIniciales.forEach(detalle => crearFilaProducto(detalle));

        if (detallesIniciales.length === 0) {
            crearFilaProducto();
        }

        document.getElementById("facturaEditAgregarProducto").addEventListener("click", () => crearFilaProducto());
        document.getElementById("facturaEditDescuentoGlobal").addEventListener("input", calcularTotales);

        document.querySelectorAll('input[name="tipo_cliente_venta"]').forEach(radio => {
            radio.addEventListener("change", toggleCliente);
        });

        toggleCliente();
        calcularTotales();
    });
</script>