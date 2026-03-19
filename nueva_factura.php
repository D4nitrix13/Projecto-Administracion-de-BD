<?php
session_start();
$pageTitle = "Nueva factura - Panda Estampados / Kitsune";

// Proteger la página
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user  = $_SESSION["user"];
$idRol = (int)($user["id_rol"] ?? 0);

$connection = require "./sql/db.php";

const IVA_RATE = 0.15; // 15% IVA Nicaragua

// Constantes para tipo de cliente
const TIPO_CLIENTE_HABITUAL = 'Habitual';
const TIPO_CLIENTE_FUGAZ    = 'Fugaz';

$error = null;

/*
 * 1) Cargar clientes HABITUALES (EXCLUIMOS el cliente FUGAZ del combo)
 *    y traemos todos los campos para el filtrado:
 *    nombres, apellidos, telefono, direccion, identificacion, tipo_cliente
 */
$stmtCli = $connection->query("
    SELECT 
        id_cliente,
        nombres,
        apellidos,
        telefono,
        direccion,
        identificacion,
        tipo_cliente
    FROM Cliente
    WHERE identificacion IS DISTINCT FROM 'FUGAZ'
    ORDER BY nombres, apellidos
");
$clientes = $stmtCli->fetchAll(PDO::FETCH_ASSOC);

// 2) Cargar productos
$stmtProd = $connection->query("
    SELECT id_producto, codigo, nombre, precio_venta, stock
    FROM Producto
    ORDER BY nombre
");
$productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

// 3) Sección
$seccionUsuario = null;
$secciones      = [];

if (!empty($user["id_seccion"])) {
    $stmtSecUser = $connection->prepare("
        SELECT id_seccion, nombre
        FROM Seccion
        WHERE id_seccion = :id
    ");
    $stmtSecUser->execute([":id" => (int)$user["id_seccion"]]);
    $seccionUsuario = $stmtSecUser->fetch(PDO::FETCH_ASSOC);
} else {
    $stmtSec = $connection->query("
        SELECT id_seccion, nombre
        FROM Seccion
        ORDER BY nombre
    ");
    $secciones = $stmtSec->fetchAll(PDO::FETCH_ASSOC);
}

// Valores por defecto para el formulario
$id_cliente             = "";
$id_seccion             = $user["id_seccion"] ?? "";
$descuento_global       = "0.00";
$tipo_cliente_venta     = TIPO_CLIENTE_HABITUAL; // por defecto
$nombre_cliente_fugaz   = "";

// Función para obtener el id del cliente FUGAZ
function obtenerIdClienteFugaz(PDO $connection): int
{
    $stmt = $connection->prepare("
        SELECT id_cliente
        FROM Cliente
        WHERE identificacion = 'FUGAZ'
        LIMIT 1
    ");
    $stmt->execute();
    $id = $stmt->fetchColumn();
    return $id ? (int)$id : 0;
}

// Procesar envío
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Tipo de cliente de la factura
    $tipo_cliente_venta = $_POST["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL;
    $tipo_cliente_venta = ($tipo_cliente_venta === TIPO_CLIENTE_FUGAZ)
        ? TIPO_CLIENTE_FUGAZ
        : TIPO_CLIENTE_HABITUAL;

    // Para habitual, viene del select; para fugaz se ignora y se usará el genérico
    $id_cliente_form      = isset($_POST["id_cliente"]) ? (int)$_POST["id_cliente"] : 0;
    $nombre_cliente_fugaz = trim($_POST["nombre_cliente_fugaz"] ?? "");

    $descuento_global = trim($_POST["descuento_global"] ?? "0");

    // Determinar sección:
    if (!empty($user["id_seccion"])) {
        $id_seccion = (int)$user["id_seccion"];
    } else {
        $id_seccion = isset($_POST["id_seccion"]) && $_POST["id_seccion"] !== ""
            ? (int)$_POST["id_seccion"]
            : 0;
    }

    // Recoger líneas de productos
    $ids_prod    = $_POST["id_producto"] ?? [];
    $cantidades  = $_POST["cantidad"] ?? [];
    $desc_lineas = $_POST["descuento_linea"] ?? [];

    $items = [];
    for ($i = 0; $i < count($ids_prod); $i++) {
        $pid  = (int)($ids_prod[$i] ?? 0);
        $cant = (int)($cantidades[$i] ?? 0);
        $dlin = trim($desc_lineas[$i] ?? "0");

        if ($pid > 0 && $cant > 0) {
            $dlinNum = is_numeric($dlin) ? (float)$dlin : 0.0;
            $items[] = [
                "id_producto"     => $pid,
                "cantidad"        => $cant,
                "descuento_linea" => $dlinNum,
            ];
        }
    }

    // =========================
    // VALIDACIONES BÁSICAS
    // =========================

    // 1) Cliente según tipo
    if ($tipo_cliente_venta === TIPO_CLIENTE_HABITUAL) {
        if ($id_cliente_form <= 0) {
            $error = "Debe seleccionar un cliente habitual.";
        } else {
            $id_cliente = $id_cliente_form;
        }
    } else {
        // FUGAZ: usamos el cliente genérico
        $id_cliente_fugaz = obtenerIdClienteFugaz($connection);
        if ($id_cliente_fugaz <= 0) {
            $error = "No está configurado el cliente fugaz en la base de datos.";
        } else {
            $id_cliente = $id_cliente_fugaz;
        }
    }

    // 2) Sección
    if ($error === null && $id_seccion <= 0) {
        $error = "Debe seleccionar una sección válida.";
    }

    // 3) Productos
    if ($error === null && empty($items)) {
        $error = "Debe agregar al menos un producto a la factura.";
    }

    // 4) Descuento global
    if ($error === null && $descuento_global !== "" && !is_numeric($descuento_global)) {
        $error = "El descuento global debe ser numérico.";
    }

    if ($error === null) {
        try {
            // Mapear productos desde BD para validar precios y stock
            $idsUnicos    = array_unique(array_column($items, "id_producto"));
            $placeholders = implode(",", array_fill(0, count($idsUnicos), "?"));

            $stmt = $connection->prepare("
                SELECT id_producto, precio_venta, stock
                FROM Producto
                WHERE id_producto IN ($placeholders)
            ");
            $stmt->execute($idsUnicos);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $productosMap = [];
            foreach ($rows as $r) {
                $productosMap[$r["id_producto"]] = [
                    "precio_venta" => (float)$r["precio_venta"],
                    "stock"        => (int)$r["stock"],
                ];
            }

            // Validar que todos existan
            foreach ($items as $item) {
                if (!isset($productosMap[$item["id_producto"]])) {
                    throw new Exception("Uno de los productos seleccionados no existe.");
                }
            }

            // Validar stock agrupando por producto
            $cantPorProducto = [];
            foreach ($items as $item) {
                $pid = $item["id_producto"];
                if (!isset($cantPorProducto[$pid])) {
                    $cantPorProducto[$pid] = 0;
                }
                $cantPorProducto[$pid] += $item["cantidad"];
            }

            foreach ($cantPorProducto as $pid => $totalCant) {
                if ($totalCant > $productosMap[$pid]["stock"]) {
                    throw new Exception("Stock insuficiente para el producto ID $pid.");
                }
            }

            // Calcular totales
            $subtotal               = 0.0;
            $descuento_total_lineas = 0.0;

            foreach ($items as &$item) {
                $pid                          = $item["id_producto"];
                $precio                       = $productosMap[$pid]["precio_venta"];
                $item["precio_unitario"]      = $precio;

                $lineaSubtotal                = $precio * $item["cantidad"];
                $lineaDesc                    = max(0.0, min($item["descuento_linea"], $lineaSubtotal));
                $lineaTotal                   = $lineaSubtotal - $lineaDesc;

                $item["total_linea"]          = $lineaTotal;

                $subtotal                    += $lineaSubtotal;
                $descuento_total_lineas      += $lineaDesc;
            }
            unset($item);

            $descuento_global_num = (float)$descuento_global;
            if ($descuento_global_num < 0) {
                $descuento_global_num = 0.0;
            }

            $descuento_factura = $descuento_total_lineas + $descuento_global_num;
            $base_imponible    = max(0.0, $subtotal - $descuento_factura);
            $impuesto          = round($base_imponible * IVA_RATE, 2);
            $total             = $base_imponible + $impuesto;

            // Transacción
            $connection->beginTransaction();

            // Insertar factura (AHORA con tipo_cliente_venta y nombre_cliente_fugaz)
            $stmtFac = $connection->prepare("
                INSERT INTO Factura
                    (id_cliente, id_usuario, id_seccion, subtotal, descuento, impuesto, total, tipo_cliente_venta, nombre_cliente_fugaz)
                VALUES
                    (:id_cliente, :id_usuario, :id_seccion, :subtotal, :descuento, :impuesto, :total, :tipo_cliente_venta, :nombre_cliente_fugaz)
                RETURNING id_factura
            ");

            $stmtFac->execute([
                ":id_cliente"           => $id_cliente,
                ":id_usuario"           => (int)$user["id_usuario"],
                ":id_seccion"           => $id_seccion,
                ":subtotal"             => $subtotal,
                ":descuento"            => $descuento_factura,
                ":impuesto"             => $impuesto,
                ":total"                => $total,
                ":tipo_cliente_venta"   => $tipo_cliente_venta,
                ":nombre_cliente_fugaz" => ($tipo_cliente_venta === TIPO_CLIENTE_FUGAZ && $nombre_cliente_fugaz !== "")
                    ? $nombre_cliente_fugaz
                    : null,
            ]);

            $idFactura = (int)$stmtFac->fetchColumn();

            // Insertar detalles y actualizar stock
            $stmtDet = $connection->prepare("
                INSERT INTO DetalleFactura
                    (id_factura, id_producto, cantidad, precio_unitario, descuento_linea, total_linea)
                VALUES
                    (:id_factura, :id_producto, :cantidad, :precio_unitario, :descuento_linea, :total_linea)
            ");

            $stmtUpdStock = $connection->prepare("
                UPDATE Producto
                SET stock = stock - :cantidad
                WHERE id_producto = :id_producto
            ");

            foreach ($items as $item) {
                $stmtDet->execute([
                    ":id_factura"      => $idFactura,
                    ":id_producto"     => $item["id_producto"],
                    ":cantidad"        => $item["cantidad"],
                    ":precio_unitario" => $item["precio_unitario"],
                    ":descuento_linea" => $item["descuento_linea"],
                    ":total_linea"     => $item["total_linea"],
                ]);

                $stmtUpdStock->execute([
                    ":cantidad"    => $item["cantidad"],
                    ":id_producto" => $item["id_producto"],
                ]);
            }

            $connection->commit();

            $_SESSION["flash_success"] = "Factura registrada correctamente.";
            header("Location: factura_detalle.php?id=" . $idFactura);
            exit();
        } catch (Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $error = "Error al registrar la factura: " . $e->getMessage();
        }
    }
}

/* ============================
 * Texto del subtítulo por rol
 * ============================ */
if ($idRol === 1) {
    $textoSubtitulo = "Registre una nueva venta para Panda Estampados y Kitsune.";
} else {
    $textoSubtitulo = "Registre una nueva venta para Kitsune.";
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require "partials/header.php"; ?>

<body class="page-bg">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="dashboard-container">

        <section class="dashboard-card dashboard-welcome">
            <p class="dashboard-eyebrow">Facturación</p>
            <h1 class="dashboard-title">Crear nueva factura</h1>

            <p class="dashboard-muted">
                <?= htmlspecialchars($textoSubtitulo) ?>
            </p>

            <a href="facturas.php" class="back-link" style="text-align:left; margin-top:10px;">
                ← Volver al historial de facturas
            </a>
        </section>

        <section class="dashboard-card">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="nueva_factura.php" method="POST">

                <!-- Datos generales -->
                <div class="form-grid">

                    <div class="form-group">
                        <label class="label">Tipo de cliente</label>
                        <select name="tipo_cliente_venta" id="tipo_cliente_venta" class="input">
                            <option value="<?= TIPO_CLIENTE_HABITUAL ?>" <?= $tipo_cliente_venta === TIPO_CLIENTE_HABITUAL ? 'selected' : '' ?>>
                                Habitual (registrado)
                            </option>
                            <option value="<?= TIPO_CLIENTE_FUGAZ ?>" <?= $tipo_cliente_venta === TIPO_CLIENTE_FUGAZ ? 'selected' : '' ?>>
                                Fugaz (no registrado)
                            </option>
                        </select>
                    </div>

                    <div class="form-group" id="grupo-cliente-habitual">
                        <label class="label">Cliente habitual (*)</label>
                        <!-- Barra para filtrar clientes -->
                        <input
                            type="text"
                            id="cliente-search"
                            class="input"
                            placeholder="Filtrar por nombre, apellido, teléfono..."
                            style="margin-bottom:6px;">
                        <select name="id_cliente" class="input">
                            <option value="">Seleccione un cliente</option>
                            <?php foreach ($clientes as $cli): ?>
                                <?php
                                // Texto de búsqueda: concatenamos TODOS los campos relevantes
                                $searchParts = [
                                    $cli["nombres"] ?? '',
                                    $cli["apellidos"] ?? '',
                                    $cli["telefono"] ?? '',
                                    $cli["direccion"] ?? '',
                                    $cli["identificacion"] ?? '',
                                    $cli["tipo_cliente"] ?? '',
                                ];
                                $searchText = strtolower(implode(' ', array_filter($searchParts)));
                                ?>
                                <option
                                    value="<?= (int)$cli["id_cliente"] ?>"
                                    data-search="<?= htmlspecialchars($searchText) ?>"
                                    <?= ($id_cliente == $cli["id_cliente"] && $tipo_cliente_venta === TIPO_CLIENTE_HABITUAL) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($cli["nombres"] . " " . $cli["apellidos"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="grupo-cliente-fugaz" style="display: <?= $tipo_cliente_venta === TIPO_CLIENTE_FUGAZ ? 'block' : 'none' ?>;">
                        <label class="label">Nombre del cliente fugaz (opcional)</label>
                        <input
                            type="text"
                            name="nombre_cliente_fugaz"
                            class="input"
                            placeholder="Ejemplo: Cliente festival, Juan, etc."
                            value="<?= htmlspecialchars($nombre_cliente_fugaz) ?>">
                    </div>

                    <div class="form-group">
                        <label class="label">Sección (*)</label>

                        <?php if (!empty($user["id_seccion"])): ?>
                            <?php
                            $nombreSeccion = $seccionUsuario["nombre"]
                                ?? ("Sección #" . (int)$user["id_seccion"]);
                            ?>
                            <!-- Usuario normal: se muestra su sección y no se puede editar -->
                            <input
                                type="text"
                                class="input"
                                value="<?= htmlspecialchars($nombreSeccion) ?>"
                                disabled>
                            <input type="hidden" name="id_seccion" value="<?= (int)$user["id_seccion"] ?>">
                        <?php else: ?>
                            <!-- Admin general: puede elegir -->
                            <select name="id_seccion" class="input" required>
                                <option value="">Seleccione sección</option>
                                <?php foreach ($secciones as $sec): ?>
                                    <option
                                        value="<?= (int)$sec["id_seccion"] ?>"
                                        <?= ($id_seccion == $sec["id_seccion"]) ? "selected" : "" ?>>
                                        <?= htmlspecialchars($sec["nombre"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="label">Descuento global (C$)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="descuento_global"
                            class="input"
                            value="<?= htmlspecialchars($descuento_global) ?>">
                    </div>
                </div>

                <!-- Detalles de productos -->
                <h2 style="margin-top:24px; margin-bottom:12px;">Productos</h2>

                <!-- Barra para filtrar productos -->
                <div class="form-group" style="max-width:320px; margin-bottom:12px;">
                    <label class="label">Buscar producto</label>
                    <input
                        type="text"
                        id="producto-search"
                        class="input"
                        placeholder="Filtrar por nombre o código...">
                </div>

                <div class="table-wrapper">
                    <table class="table-products" id="items-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Cantidad</th>
                                <th>Desc. línea</th>
                                <th>Total línea</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <tr class="item-row">
                                <td>
                                    <select name="id_producto[]" class="input producto-select" required>
                                        <option value="">Seleccione producto</option>
                                        <?php foreach ($productos as $p): ?>
                                            <option
                                                value="<?= (int)$p["id_producto"] ?>"
                                                data-precio="<?= htmlspecialchars($p["precio_venta"]) ?>"
                                                data-stock="<?= (int)$p["stock"] ?>">
                                                <?= htmlspecialchars($p["nombre"] . " (" . $p["codigo"] . ")") ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="input precio" value="" readonly>
                                </td>
                                <td>
                                    <input type="text" class="input stock" value="0" readonly>
                                </td>
                                <td>
                                    <input type="number" name="cantidad[]" class="input cantidad" min="1" value="1">
                                </td>
                                <td>
                                    <input type="number" name="descuento_linea[]" class="input desc-linea" min="0" step="0.01" value="0">
                                </td>
                                <td>
                                    <input type="text" class="input total-linea" value="0.00" readonly>
                                </td>
                                <td class="cell-remove" style="padding:0; text-align:center; vertical-align:middle;">
                                    <button type="button"
                                        class="btn-remove-row"
                                        style=" display:inline-flex; align-items:center; justify-content:center; width:32px; height:38px; background:#fecaca; border:1px solid #fca5a5; color:#b91c1c; font-size:18px; border-radius:8px; padding:0; cursor:pointer; line-height:1; position:relative; top:10px; transition:background .2s ease;"
                                        onmouseover="this.style.background='#fda4a4';"
                                        onmouseout="this.style.background='#fecaca';">×</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:10px;">
                    <button type="button" class="btn-primary-inline" id="btn-add-row">
                        + Agregar producto
                    </button>
                </div>

                <!-- Totales (visual, el servidor recalcula de nuevo) -->
                <div class="totales-card">
                    <div class="totales-row">
                        <span>Subtotal estimado</span>
                        <span id="subtotal-view">C$ 0.00</span>
                    </div>

                    <div class="totales-row">
                        <span>Descuento global</span>
                        <span id="descuento-global-view">C$ 0.00</span>
                    </div>

                    <div class="totales-row">
                        <span>Impuesto (15%)</span>
                        <span id="impuesto-view">C$ 0.00</span>
                    </div>

                    <div class="totales-row total-final">
                        <span>Total estimado</span>
                        <span id="total-view">C$ 0.00</span>
                    </div>
                </div>

                <div class="form-actions" style="margin-top:24px;">
                    <button type="submit" class="btn-primary">
                        Guardar factura
                    </button>
                </div>

            </form>
        </section>

    </main>

    <script>
        (function() {
            const IVA_RATE = 0.15;

            const tbody = document.getElementById('items-body');
            const btnAdd = document.getElementById('btn-add-row');

            // Cambio de tipo de cliente (habitual / fugaz)
            const selectTipoCliente = document.getElementById('tipo_cliente_venta');
            const grupoHabitual = document.getElementById('grupo-cliente-habitual');
            const grupoFugaz = document.getElementById('grupo-cliente-fugaz');
            const selectCliente = document.querySelector('select[name="id_cliente"]');

            if (selectTipoCliente) {
                const toggleClienteGroups = () => {
                    if (selectTipoCliente.value === '<?= TIPO_CLIENTE_FUGAZ ?>') {
                        // Fugaz: escondemos el combo de clientes y lo ponemos no requerido
                        grupoHabitual.style.display = 'none';
                        if (selectCliente) {
                            selectCliente.removeAttribute('required');
                            selectCliente.value = '';
                        }
                        // Mostramos campo de nombre fugaz
                        grupoFugaz.style.display = 'block';
                    } else {
                        // Habitual: mostramos el combo y lo requerimos
                        grupoHabitual.style.display = 'block';
                        if (selectCliente) {
                            selectCliente.setAttribute('required', 'required');
                        }
                        grupoFugaz.style.display = 'none';
                    }
                };

                selectTipoCliente.addEventListener('change', toggleClienteGroups);
                toggleClienteGroups(); // estado inicial
            }

            // ----- Filtro de clientes (por todos los campos: nombre, apellido, teléfono, etc.) -----
            const clienteSearch = document.getElementById('cliente-search');

            if (clienteSearch && selectCliente) {
                clienteSearch.addEventListener('input', function() {
                    const term = this.value.toLowerCase();

                    Array.from(selectCliente.options).forEach(opt => {
                        if (!opt.value) {
                            opt.style.display = '';
                            return;
                        }

                        // Usamos data-search que viene con todos los campos concatenados
                        const searchText = (opt.dataset.search || opt.textContent || '').toLowerCase();

                        // Siempre mostramos la opción seleccionada aunque no coincida
                        if (opt.selected || searchText.includes(term)) {
                            opt.style.display = '';
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                });
            }

            // ----- Filtro de productos (afecta a todas las filas) -----
            const productoSearch = document.getElementById('producto-search');

            function filtrarProductos(term) {
                term = term.toLowerCase();
                const selects = document.querySelectorAll('.producto-select');

                selects.forEach(select => {
                    Array.from(select.options).forEach(opt => {
                        if (!opt.value) {
                            opt.style.display = '';
                            return;
                        }
                        const text = opt.textContent.toLowerCase();
                        if (opt.selected || text.includes(term)) {
                            opt.style.display = '';
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                });
            }

            if (productoSearch) {
                productoSearch.addEventListener('input', function() {
                    filtrarProductos(this.value);
                });
            }

            // ----- Cálculos de filas y totales -----
            function recalcRow(row) {
                const select = row.querySelector('.producto-select');
                const precioInput = row.querySelector('.precio');
                const stockInput = row.querySelector('.stock');
                const cantInput = row.querySelector('.cantidad');
                const descInput = row.querySelector('.desc-linea');
                const totalInput = row.querySelector('.total-linea');

                if (!select || !precioInput || !stockInput || !cantInput || !descInput || !totalInput) {
                    return;
                }

                const option = select.options[select.selectedIndex];
                const precio = parseFloat(option?.getAttribute('data-precio') || '0');
                const stock = parseInt(option?.getAttribute('data-stock') || '0', 10);
                const cant = parseInt(cantInput.value || '0', 10);
                const desc = parseFloat(descInput.value || '0');

                precioInput.value = precio > 0 ? precio.toFixed(2) : "";
                stockInput.value = !isNaN(stock) ? stock : "";

                const subtotal = precio * cant;
                const descOk = Math.min(Math.max(desc, 0), subtotal);
                const total = subtotal - descOk;

                totalInput.value = total.toFixed(2);

                recalcTotals();
            }

            function recalcTotals() {
                let subtotal = 0;
                let descLineas = 0;

                tbody.querySelectorAll('.item-row').forEach(row => {
                    const precio = parseFloat(row.querySelector('.precio')?.value || '0');
                    const cant = parseInt(row.querySelector('.cantidad')?.value || '0', 10);
                    const desc = parseFloat(row.querySelector('.desc-linea')?.value || '0');

                    const lineSub = precio * cant;
                    const lineDesc = Math.min(Math.max(desc, 0), lineSub);

                    subtotal += lineSub;
                    descLineas += lineDesc;
                });

                const descGlobalInput = document.querySelector('input[name="descuento_global"]');
                const descGlobal = parseFloat(descGlobalInput?.value || '0');
                const descTotal = descLineas + Math.max(descGlobal, 0);

                const base = Math.max(0, subtotal - descTotal);
                const impuesto = base * IVA_RATE;
                const total = base + impuesto;

                document.getElementById('subtotal-view').textContent = `C$ ${subtotal.toFixed(2)}`;
                document.getElementById('descuento-global-view').textContent = `C$ ${Math.max(descGlobal, 0).toFixed(2)}`;
                document.getElementById('impuesto-view').textContent = `C$ ${impuesto.toFixed(2)}`;
                document.getElementById('total-view').textContent = `C$ ${total.toFixed(2)}`;
            }

            function attachRowEvents(row) {
                const select = row.querySelector('.producto-select');
                const cantidad = row.querySelector('.cantidad');
                const descLinea = row.querySelector('.desc-linea');

                if (select) {
                    select.addEventListener('change', () => recalcRow(row));
                }

                if (cantidad) {
                    cantidad.addEventListener('input', () => recalcRow(row));
                }

                if (descLinea) {
                    descLinea.addEventListener('input', () => recalcRow(row));
                }
            }

            // Delegación para el botón eliminar
            tbody.addEventListener('click', (event) => {
                const btn = event.target.closest('.btn-remove-row');
                if (!btn) return;

                const row = btn.closest('.item-row');
                if (!row) return;

                // No permitir eliminar la última fila
                if (tbody.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    recalcTotals();
                }
            });

            // Inicial
            const firstRow = tbody.querySelector('.item-row');
            if (firstRow) {
                attachRowEvents(firstRow);
                recalcRow(firstRow);
            }

            btnAdd.addEventListener('click', () => {
                const baseRow = tbody.querySelector('.item-row');
                if (!baseRow) return;

                const newRow = baseRow.cloneNode(true);

                // Resetear valores de la nueva fila
                const select = newRow.querySelector('.producto-select');
                if (select) select.selectedIndex = 0;

                const precio = newRow.querySelector('.precio');
                if (precio) precio.value = "";

                const stock = newRow.querySelector('.stock');
                if (stock) stock.value = "0";

                const cantidad = newRow.querySelector('.cantidad');
                if (cantidad) cantidad.value = "1";

                const descLinea = newRow.querySelector('.desc-linea');
                if (descLinea) descLinea.value = "0";

                const totalLinea = newRow.querySelector('.total-linea');
                if (totalLinea) totalLinea.value = "0.00";

                attachRowEvents(newRow);
                tbody.appendChild(newRow);

                // Aplicar el filtro actual de productos a la nueva fila
                if (productoSearch && productoSearch.value) {
                    filtrarProductos(productoSearch.value);
                }

                recalcTotals();
            });

            const descGlobalInput = document.querySelector('input[name="descuento_global"]');
            if (descGlobalInput) {
                descGlobalInput.addEventListener('input', recalcTotals);
            }
        })();
    </script>

</body>

</html>