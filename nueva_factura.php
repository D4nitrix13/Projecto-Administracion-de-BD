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
const TIPO_CLIENTE_HABITUAL = 'Habitual';
const TIPO_CLIENTE_FUGAZ    = 'Fugaz';

$error = null;

/*
 * 1) Cargar clientes HABITUALES
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

/*
 * 2) Cargar productos
 */
$stmtProd = $connection->query("
    SELECT id_producto, codigo, nombre, precio_venta, stock
    FROM Producto
    ORDER BY nombre
");
$productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

/*
 * 3) Sección
 */
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

// Valores por defecto
$id_cliente           = "";
$id_seccion           = $user["id_seccion"] ?? "";
$descuento_global     = "0.00";
$tipo_cliente_venta   = TIPO_CLIENTE_HABITUAL;
$nombre_cliente_fugaz = "";

/*
 * Función para obtener el id del cliente FUGAZ
 */
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

/*
 * Procesar envío
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo_cliente_venta = $_POST["tipo_cliente_venta"] ?? TIPO_CLIENTE_HABITUAL;
    $tipo_cliente_venta = ($tipo_cliente_venta === TIPO_CLIENTE_FUGAZ)
        ? TIPO_CLIENTE_FUGAZ
        : TIPO_CLIENTE_HABITUAL;

    $id_cliente_form      = isset($_POST["id_cliente"]) ? (int)$_POST["id_cliente"] : 0;
    $nombre_cliente_fugaz = trim($_POST["nombre_cliente_fugaz"] ?? "");
    $descuento_global     = trim($_POST["descuento_global"] ?? "0");

    if (!empty($user["id_seccion"])) {
        $id_seccion = (int)$user["id_seccion"];
    } else {
        $id_seccion = isset($_POST["id_seccion"]) && $_POST["id_seccion"] !== ""
            ? (int)$_POST["id_seccion"]
            : 0;
    }

    $ids_prod    = $_POST["id_producto"] ?? [];
    $cantidades  = $_POST["cantidad"] ?? [];
    $desc_lineas = $_POST["descuento_linea"] ?? [];

    $items = [];

    for ($i = 0; $i < count($ids_prod); $i++) {
        $pidRaw  = $ids_prod[$i] ?? 0;
        $cantRaw = $cantidades[$i] ?? 0;
        $dlinRaw = $desc_lineas[$i] ?? "0";

        $pid  = (int)$pidRaw;
        $cant = (int)$cantRaw;
        $dlin = trim((string)$dlinRaw);

        // Ignorar fila vacía
        if ($pid <= 0 && $cant <= 0) {
            continue;
        }

        if ($pid <= 0) {
            $error = "Debe seleccionar un producto válido en todas las filas.";
            break;
        }

        if ($cant < 1) {
            $error = "La cantidad mínima por producto es 1.";
            break;
        }

        $dlinNum = is_numeric($dlin) ? (float)$dlin : 0.0;
        if ($dlinNum < 0) {
            $dlinNum = 0.0;
        }

        $items[] = [
            "id_producto"     => $pid,
            "cantidad"        => $cant,
            "descuento_linea" => $dlinNum,
        ];
    }

    // VALIDACIONES BÁSICAS
    if ($error === null) {
        if ($tipo_cliente_venta === TIPO_CLIENTE_HABITUAL) {
            if ($id_cliente_form <= 0) {
                $error = "Debe seleccionar un cliente habitual.";
            } else {
                $id_cliente = $id_cliente_form;
            }
        } else {
            $id_cliente_fugaz = obtenerIdClienteFugaz($connection);
            if ($id_cliente_fugaz <= 0) {
                $error = "No está configurado el cliente fugaz en la base de datos.";
            } else {
                $id_cliente = $id_cliente_fugaz;
            }
        }
    }

    if ($error === null && $id_seccion <= 0) {
        $error = "Debe seleccionar una sección válida.";
    }

    if ($error === null && empty($items)) {
        $error = "Debe agregar al menos un producto a la factura.";
    }

    if ($error === null && $descuento_global !== "" && !is_numeric($descuento_global)) {
        $error = "El descuento global debe ser numérico.";
    }

    if ($error === null) {
        try {
            $idsUnicos = array_unique(array_column($items, "id_producto"));
            $placeholders = implode(",", array_fill(0, count($idsUnicos), "?"));

            $stmt = $connection->prepare("
                SELECT id_producto, precio_venta, stock, nombre
                FROM Producto
                WHERE id_producto IN ($placeholders)
            ");
            $stmt->execute($idsUnicos);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $productosMap = [];
            foreach ($rows as $r) {
                $productosMap[(int)$r["id_producto"]] = [
                    "nombre"        => (string)$r["nombre"],
                    "precio_venta"  => (float)$r["precio_venta"],
                    "stock"         => (int)$r["stock"],
                ];
            }

            foreach ($items as $item) {
                if (!isset($productosMap[$item["id_producto"]])) {
                    throw new Exception("Uno de los productos seleccionados no existe.");
                }
            }

            // Validación individual
            foreach ($items as $item) {
                $pid              = (int)$item["id_producto"];
                $cantidad         = (int)$item["cantidad"];
                $stockDisponible  = (int)$productosMap[$pid]["stock"];
                $nombreProducto   = $productosMap[$pid]["nombre"];

                if ($cantidad < 1) {
                    throw new Exception("La cantidad mínima para '$nombreProducto' es 1.");
                }

                if ($cantidad > $stockDisponible) {
                    throw new Exception("La cantidad para '$nombreProducto' supera el stock disponible ($stockDisponible).");
                }
            }

            // Validación agrupada por si el mismo producto aparece varias veces
            $cantPorProducto = [];
            foreach ($items as $item) {
                $pid = (int)$item["id_producto"];
                if (!isset($cantPorProducto[$pid])) {
                    $cantPorProducto[$pid] = 0;
                }
                $cantPorProducto[$pid] += (int)$item["cantidad"];
            }

            foreach ($cantPorProducto as $pid => $totalCant) {
                $stockDisponible = (int)$productosMap[$pid]["stock"];
                $nombreProducto  = $productosMap[$pid]["nombre"];

                if ($totalCant > $stockDisponible) {
                    throw new Exception("Stock insuficiente para '$nombreProducto'. Disponible: $stockDisponible.");
                }
            }

            // Calcular totales
            $subtotal               = 0.0;
            $descuento_total_lineas = 0.0;

            foreach ($items as &$item) {
                $pid                     = (int)$item["id_producto"];
                $precio                  = (float)$productosMap[$pid]["precio_venta"];
                $item["precio_unitario"] = $precio;

                $lineaSubtotal           = $precio * (int)$item["cantidad"];
                $lineaDesc               = max(0.0, min((float)$item["descuento_linea"], $lineaSubtotal));
                $lineaTotal              = $lineaSubtotal - $lineaDesc;

                $item["descuento_linea"] = $lineaDesc;
                $item["total_linea"]     = $lineaTotal;

                $subtotal               += $lineaSubtotal;
                $descuento_total_lineas += $lineaDesc;
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

            $connection->beginTransaction();

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

            <form action="nueva_factura.php" method="POST" id="form-factura">

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
                            <input
                                type="text"
                                class="input"
                                value="<?= htmlspecialchars($nombreSeccion) ?>"
                                disabled>
                            <input type="hidden" name="id_seccion" value="<?= (int)$user["id_seccion"] ?>">
                        <?php else: ?>
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

                <h2 style="margin-top:24px; margin-bottom:12px;">Productos</h2>

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
                                    <input
                                        type="number"
                                        name="cantidad[]"
                                        class="input cantidad"
                                        min="1"
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
                                        value="0">
                                </td>
                                <td>
                                    <input type="text" class="input total-linea" value="0.00" readonly>
                                </td>
                                <td class="cell-remove" style="padding:0; text-align:center; vertical-align:middle;">
                                    <button
                                        type="button"
                                        class="btn-remove-row"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:38px; background:#fecaca; border:1px solid #fca5a5; color:#b91c1c; font-size:18px; border-radius:8px; padding:0; cursor:pointer; line-height:1; position:relative; top:10px; transition:background .2s ease;"
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
            const form = document.getElementById('form-factura');

            const selectTipoCliente = document.getElementById('tipo_cliente_venta');
            const grupoHabitual = document.getElementById('grupo-cliente-habitual');
            const grupoFugaz = document.getElementById('grupo-cliente-fugaz');
            const selectCliente = document.querySelector('select[name="id_cliente"]');

            if (selectTipoCliente) {
                const toggleClienteGroups = () => {
                    if (selectTipoCliente.value === '<?= TIPO_CLIENTE_FUGAZ ?>') {
                        grupoHabitual.style.display = 'none';
                        if (selectCliente) {
                            selectCliente.removeAttribute('required');
                            selectCliente.value = '';
                        }
                        grupoFugaz.style.display = 'block';
                    } else {
                        grupoHabitual.style.display = 'block';
                        if (selectCliente) {
                            selectCliente.setAttribute('required', 'required');
                        }
                        grupoFugaz.style.display = 'none';
                    }
                };

                selectTipoCliente.addEventListener('change', toggleClienteGroups);
                toggleClienteGroups();
            }

            const clienteSearch = document.getElementById('cliente-search');

            if (clienteSearch && selectCliente) {
                clienteSearch.addEventListener('input', function() {
                    const term = this.value.toLowerCase();

                    Array.from(selectCliente.options).forEach(opt => {
                        if (!opt.value) {
                            opt.style.display = '';
                            return;
                        }

                        const searchText = (opt.dataset.search || opt.textContent || '').toLowerCase();

                        if (opt.selected || searchText.includes(term)) {
                            opt.style.display = '';
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                });
            }

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

            function formatMoney(value) {
                return `C$ ${value.toFixed(2)}`;
            }

            function getRowData(row) {
                const select = row.querySelector('.producto-select');
                const precioInput = row.querySelector('.precio');
                const stockInput = row.querySelector('.stock');
                const cantInput = row.querySelector('.cantidad');
                const descInput = row.querySelector('.desc-linea');
                const totalInput = row.querySelector('.total-linea');

                return {
                    row,
                    select,
                    precioInput,
                    stockInput,
                    cantInput,
                    descInput,
                    totalInput
                };
            }

            function normalizeCantidadInput(cantInput) {
                let value = cantInput.value ?? '';

                // quitar todo lo que no sea dígito
                value = value.replace(/[^\d]/g, '');

                // permitir vacío temporal mientras escribe
                cantInput.value = value;
            }

            function recalcRow(row, forceNormalize = false) {
                const {
                    select,
                    precioInput,
                    stockInput,
                    cantInput,
                    descInput,
                    totalInput
                } = getRowData(row);

                if (!select || !precioInput || !stockInput || !cantInput || !descInput || !totalInput) {
                    return;
                }

                const option = select.options[select.selectedIndex];
                const hayProducto = !!select.value;
                const precio = parseFloat(option?.dataset?.precio || '0');
                const stock = parseInt(option?.dataset?.stock || '0', 10);

                precioInput.value = hayProducto && precio > 0 ? precio.toFixed(2) : "";
                stockInput.value = hayProducto ? String(isNaN(stock) ? 0 : stock) : "0";

                cantInput.min = "1";
                cantInput.step = "1";
                if (hayProducto && stock > 0) {
                    cantInput.max = String(stock);
                } else {
                    cantInput.removeAttribute('max');
                }

                const rawCant = (cantInput.value || '').trim();

                if (!hayProducto) {
                    cantInput.setCustomValidity('');
                    totalInput.value = "0.00";
                    recalcTotals();
                    return;
                }

                if (stock <= 0) {
                    cantInput.setCustomValidity('Este producto no tiene stock disponible.');
                    if (forceNormalize) {
                        cantInput.reportValidity();
                    }
                    totalInput.value = "0.00";
                    recalcTotals();
                    return;
                }

                if (rawCant === '') {
                    if (forceNormalize) {
                        cantInput.value = '1';
                        cantInput.setCustomValidity('');
                    } else {
                        cantInput.setCustomValidity('');
                        totalInput.value = "0.00";
                        recalcTotals();
                        return;
                    }
                }

                let cant = parseInt(cantInput.value || '0', 10);

                if (Number.isNaN(cant)) {
                    cant = 0;
                }

                if (!forceNormalize) {
                    if (cant < 1) {
                        cantInput.setCustomValidity('La cantidad mínima es 1.');
                        totalInput.value = "0.00";
                        recalcTotals();
                        return;
                    }

                    if (cant > stock) {
                        cantInput.setCustomValidity(`Stock máximo disponible: ${stock}. Al salir del campo se ajustará automáticamente.`);
                        cantInput.reportValidity();
                        totalInput.value = "0.00";
                        recalcTotals();
                        return;
                    }

                    cantInput.setCustomValidity('');
                } else {
                    if (cant < 1) {
                        cant = 1;
                    }

                    if (cant > stock) {
                        cant = stock;
                    }

                    cantInput.value = String(cant);
                    cantInput.setCustomValidity('');
                }

                let desc = parseFloat(descInput.value || '0');
                if (Number.isNaN(desc) || desc < 0) {
                    desc = 0;
                    if (forceNormalize) {
                        descInput.value = "0";
                    }
                }

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

                    if (!Number.isNaN(precio) && !Number.isNaN(cant) && cant > 0) {
                        const lineSub = precio * cant;
                        const lineDesc = Math.min(Math.max(Number.isNaN(desc) ? 0 : desc, 0), lineSub);

                        subtotal += lineSub;
                        descLineas += lineDesc;
                    }
                });

                const descGlobalInput = document.querySelector('input[name="descuento_global"]');
                const descGlobal = parseFloat(descGlobalInput?.value || '0');
                const descGlobalSafe = Math.max(Number.isNaN(descGlobal) ? 0 : descGlobal, 0);
                const descTotal = descLineas + descGlobalSafe;

                const base = Math.max(0, subtotal - descTotal);
                const impuesto = base * IVA_RATE;
                const total = base + impuesto;

                document.getElementById('subtotal-view').textContent = formatMoney(subtotal);
                document.getElementById('descuento-global-view').textContent = formatMoney(descGlobalSafe);
                document.getElementById('impuesto-view').textContent = formatMoney(impuesto);
                document.getElementById('total-view').textContent = formatMoney(total);
            }

            function attachRowEvents(row) {
                const select = row.querySelector('.producto-select');
                const cantidad = row.querySelector('.cantidad');
                const descLinea = row.querySelector('.desc-linea');

                if (select) {
                    select.addEventListener('change', () => {
                        recalcRow(row, true);
                    });
                }

                if (cantidad) {
                    cantidad.addEventListener('keydown', (e) => {
                        const allowedKeys = [
                            'Backspace',
                            'Delete',
                            'ArrowLeft',
                            'ArrowRight',
                            'Tab',
                            'Home',
                            'End'
                        ];

                        if (allowedKeys.includes(e.key)) {
                            return;
                        }

                        if (/^[0-9]$/.test(e.key)) {
                            return;
                        }

                        e.preventDefault();
                    });

                    cantidad.addEventListener('paste', (e) => {
                        const pasted = (e.clipboardData || window.clipboardData).getData('text');
                        if (!/^\d+$/.test(pasted)) {
                            e.preventDefault();
                        }
                    });

                    cantidad.addEventListener('input', () => {
                        normalizeCantidadInput(cantidad);
                        recalcRow(row, false);
                    });

                    cantidad.addEventListener('blur', () => {
                        recalcRow(row, true);
                    });

                    cantidad.addEventListener('change', () => {
                        recalcRow(row, true);
                    });
                }

                if (descLinea) {
                    descLinea.addEventListener('input', () => recalcRow(row, false));
                    descLinea.addEventListener('blur', () => recalcRow(row, true));
                }
            }

            tbody.addEventListener('click', (event) => {
                const btn = event.target.closest('.btn-remove-row');
                if (!btn) return;

                const row = btn.closest('.item-row');
                if (!row) return;

                if (tbody.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    recalcTotals();
                }
            });

            const firstRow = tbody.querySelector('.item-row');
            if (firstRow) {
                attachRowEvents(firstRow);
                recalcRow(firstRow, true);
            }

            btnAdd.addEventListener('click', () => {
                const baseRow = tbody.querySelector('.item-row');
                if (!baseRow) return;

                const newRow = baseRow.cloneNode(true);

                const select = newRow.querySelector('.producto-select');
                if (select) {
                    select.selectedIndex = 0;
                }

                const precio = newRow.querySelector('.precio');
                if (precio) {
                    precio.value = "";
                }

                const stock = newRow.querySelector('.stock');
                if (stock) {
                    stock.value = "0";
                }

                const cantidad = newRow.querySelector('.cantidad');
                if (cantidad) {
                    cantidad.value = "1";
                    cantidad.setCustomValidity('');
                    cantidad.min = "1";
                    cantidad.step = "1";
                    cantidad.removeAttribute('max');
                }

                const descLinea = newRow.querySelector('.desc-linea');
                if (descLinea) {
                    descLinea.value = "0";
                }

                const totalLinea = newRow.querySelector('.total-linea');
                if (totalLinea) {
                    totalLinea.value = "0.00";
                }

                attachRowEvents(newRow);
                tbody.appendChild(newRow);

                if (productoSearch && productoSearch.value) {
                    filtrarProductos(productoSearch.value);
                }

                recalcTotals();
            });

            const descGlobalInput = document.querySelector('input[name="descuento_global"]');
            if (descGlobalInput) {
                descGlobalInput.addEventListener('input', recalcTotals);
            }

            if (form) {
                form.addEventListener('submit', (e) => {
                    let invalidFound = false;

                    tbody.querySelectorAll('.item-row').forEach(row => {
                        recalcRow(row, true);

                        const cantidad = row.querySelector('.cantidad');
                        const select = row.querySelector('.producto-select');

                        if (select && select.value && cantidad && !cantidad.checkValidity()) {
                            invalidFound = true;
                            cantidad.reportValidity();
                        }
                    });

                    if (invalidFound) {
                        e.preventDefault();
                    }
                });
            }
        })();
    </script>

</body>

</html>