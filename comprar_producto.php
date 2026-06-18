<?php

session_start();

$pageTitle = "Comprar producto - Panda Estampados / Kitsune";

require_once __DIR__ . "/includes/auth_guard.php";
require_once __DIR__ . "/controllers/comprar_producto_controller.php";

requireAdmin();

$viewData = obtenerDatosComprarProducto();

$user        = $viewData["user"];
$producto    = $viewData["producto"];
$proveedores = $viewData["proveedores"];
$error       = $viewData["error"];
$success     = $viewData["success"];

$imagen      = trim($producto["imagen"] ?? "");
$rutaImagen  = $imagen !== "" ? "uploads/productos/" . $imagen : "assets/img/no-product.png";

$stockActual   = (int)$producto["stock"];
$costoActual   = (float)$producto["precio_compra"];
$precioVenta   = (float)$producto["precio_venta"];
$margen        = $costoActual > 0 ? (($precioVenta - $costoActual) / $costoActual) * 100 : 0;

?>

<!DOCTYPE html>
<html lang="es">

<?php require __DIR__ . "/partials/inicio-publico/dashboard/styles.php"; ?>
<?php require __DIR__ . "/partials/inventario/productos/comprar/styles.php"; ?>

<body class="dashboard-body">

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar.php"; ?>

    <main class="dashboard-main">

        <?php require __DIR__ . "/partials/inicio-publico/dashboard/topbar.php"; ?>

        <section class="comprar-hero">
            <div class="comprar-hero-text">
                <p class="dashboard-eyebrow">Inventario</p>
                <h1 class="dashboard-title">Comprar producto</h1>
                <p class="dashboard-muted">Registre una compra para agregar stock al inventario.</p>
            </div>
            <a href="catalogo.php" class="comprar-hero-back">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                Volver al catálogo
            </a>
        </section>

        <?php if ($error): ?>
            <div class="comprar-alert comprar-alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="comprar-alert comprar-alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <section class="comprar-layout">

            <div class="comprar-left">

                <article class="comprar-producto-card">
                    <div class="comprar-producto-img-wrapper">
                        <img
                            src="<?= htmlspecialchars($rutaImagen) ?>"
                            alt="<?= htmlspecialchars($producto["nombre"]) ?>"
                            class="comprar-producto-img"
                            onerror="this.src='assets/img/no-product.png'">
                        <?php if ($stockActual <= 5 && $stockActual > 0): ?>
                            <span class="comprar-badge comprar-badge-warning">Stock bajo</span>
                        <?php elseif ($stockActual <= 0): ?>
                            <span class="comprar-badge comprar-badge-danger">Agotado</span>
                        <?php endif; ?>
                    </div>

                    <div class="comprar-producto-body">
                        <span class="comprar-producto-codigo"><?= htmlspecialchars($producto["codigo"]) ?></span>
                        <h2 class="comprar-producto-nombre"><?= htmlspecialchars($producto["nombre"]) ?></h2>
                        <?php if (!empty($producto["descripcion"])): ?>
                            <p class="comprar-producto-desc"><?= htmlspecialchars($producto["descripcion"]) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="comprar-producto-stats">
                        <div class="comprar-stat">
                            <span class="comprar-stat-label">Stock actual</span>
                            <strong class="comprar-stat-value <?= $stockActual <= 5 ? 'comprar-stat-warning' : '' ?>">
                                <?= $stockActual ?> <small>unid.</small>
                            </strong>
                        </div>
                        <div class="comprar-stat">
                            <span class="comprar-stat-label">Costo compra</span>
                            <strong class="comprar-stat-value">C$ <?= number_format($costoActual, 2) ?></strong>
                        </div>
                        <div class="comprar-stat">
                            <span class="comprar-stat-label">Precio venta</span>
                            <strong class="comprar-stat-value">C$ <?= number_format($precioVenta, 2) ?></strong>
                        </div>
                        <div class="comprar-stat">
                            <span class="comprar-stat-label">Margen</span>
                            <strong class="comprar-stat-value <?= $margen < 20 ? 'comprar-stat-warning' : '' ?>">
                                <?= number_format($margen, 1) ?>%
                            </strong>
                        </div>
                    </div>
                </article>

                <article class="comprar-preview-card" id="previewCard" style="display:none;">
                    <div class="comprar-preview-header">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        <span>Vista previa</span>
                    </div>
                    <div class="comprar-preview-body">
                        <div class="comprar-preview-row">
                            <span>Stock actual</span>
                            <strong id="previewStockActual"><?= $stockActual ?></strong>
                        </div>
                        <div class="comprar-preview-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                        </div>
                        <div class="comprar-preview-row comprar-preview-highlight">
                            <span>Stock nuevo</span>
                            <strong id="previewStockNuevo"><?= $stockActual ?></strong>
                        </div>
                        <div class="comprar-preview-divider"></div>
                        <div class="comprar-preview-row">
                            <span>Cantidad a comprar</span>
                            <strong id="previewCantidad">0</strong>
                        </div>
                        <div class="comprar-preview-row">
                            <span>Costo unitario</span>
                            <strong id="previewCosto">C$ <?= number_format($costoActual, 2) ?></strong>
                        </div>
                        <div class="comprar-preview-divider"></div>
                        <div class="comprar-preview-row comprar-preview-total">
                            <span>Total</span>
                            <strong id="previewTotal">C$ 0.00</strong>
                        </div>
                    </div>
                </article>
            </div>

            <div class="comprar-right">
                <article class="comprar-form-card">
                    <div class="comprar-form-header">
                        <div class="comprar-form-header-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </div>
                        <div>
                            <h2>Registrar compra</h2>
                            <p>Complete los datos para agregar stock al inventario.</p>
                        </div>
                    </div>

                    <form method="POST" action="comprar_producto.php?id=<?= (int)$producto["id_producto"] ?>" class="comprar-form" id="compraForm">
                        <?= csrfField() ?>

                        <div class="comprar-form-section">
                            <h3 class="comprar-form-section-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                Proveedor
                            </h3>
                            <div class="comprar-form-field">
                                <label class="comprar-label">Proveedor que suministra el producto *</label>
                                <input type="hidden" name="id_proveedor" id="proveedorHidden" required>
                                <div class="comprar-select-search" id="proveedorDropdown">
                                    <div class="comprar-select-trigger" id="proveedorTrigger" tabindex="0">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                        <span id="proveedorLabel">Seleccione un proveedor</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                    </div>
                                    <div class="comprar-select-dropdown" id="proveedorLista">
                                        <div class="comprar-select-search-box">
                                            <input type="text" id="proveedorSearch" placeholder="Buscar proveedor..." class="comprar-select-search-input">
                                        </div>
                                        <ul class="comprar-select-options" id="proveedorOptions">
                                            <?php foreach ($proveedores as $prov): ?>
                                                <li class="comprar-select-option" data-value="<?= (int)$prov["id_proveedor"] ?>" data-label="<?= htmlspecialchars($prov["nombre"]) ?>">
                                                    <?= htmlspecialchars($prov["nombre"]) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div class="comprar-select-empty" id="proveedorEmpty" style="display:none;">
                                            No se encontraron proveedores
                                        </div>
                                    </div>
                                </div>
                                <p class="comprar-field-hint">Escriba para filtrar. El proveedor queda registrado en el historial de compras.</p>
                            </div>
                        </div>

                        <div class="comprar-form-section">
                            <h3 class="comprar-form-section-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                Detalles de la compra
                            </h3>
                            <div class="comprar-form-grid">
                                <div class="comprar-form-field">
                                    <label class="comprar-label">Cantidad de unidades *</label>
                                    <div class="comprar-input-wrapper">
                                        <input
                                            type="number"
                                            name="cantidad"
                                            class="comprar-input"
                                            min="1"
                                            required
                                            placeholder="0"
                                            id="inputCantidad"
                                            oninput="actualizarPreview()">
                                        <span class="comprar-input-suffix">unid.</span>
                                    </div>
                                </div>

                                <div class="comprar-form-field">
                                    <label class="comprar-label">Costo unitario (C$) *</label>
                                    <div class="comprar-input-wrapper">
                                        <span class="comprar-input-prefix">C$</span>
                                        <input
                                            type="number"
                                            name="costo_unitario"
                                            class="comprar-input comprar-input-prefix-field"
                                            min="0"
                                            step="0.01"
                                            required
                                            placeholder="0.00"
                                            value="<?= number_format($costoActual, 2, '.', '') ?>"
                                            id="inputCosto"
                                            oninput="actualizarPreview()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="comprar-form-actions">
                            <a href="catalogo.php" class="comprar-btn comprar-btn-ghost">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Cancelar
                            </a>
                            <button type="submit" class="comprar-btn comprar-btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Confirmar compra
                            </button>
                        </div>
                    </form>
                </article>
            </div>

        </section>

    </main>

    <?php require __DIR__ . "/partials/inicio-publico/dashboard/sidebar-script.php"; ?>

    <script>
    const stockActual = <?= $stockActual ?>;
    const costoActual = <?= $costoActual ?>;

    function actualizarPreview() {
        const cantidad = parseInt(document.getElementById('inputCantidad').value) || 0;
        const costo = parseFloat(document.getElementById('inputCosto').value) || 0;
        const total = cantidad * costo;
        const stockNuevo = stockActual + cantidad;

        document.getElementById('previewStockActual').textContent = stockActual;
        document.getElementById('previewStockNuevo').textContent = stockNuevo;
        document.getElementById('previewCantidad').textContent = cantidad;
        document.getElementById('previewCosto').textContent = 'C$ ' + costo.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        document.getElementById('previewTotal').textContent = 'C$ ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        const previewCard = document.getElementById('previewCard');
        if (cantidad > 0 || costo > 0) {
            previewCard.style.display = 'block';
        } else {
            previewCard.style.display = 'none';
        }
    }

    (function() {
        const trigger = document.getElementById('proveedorTrigger');
        const dropdown = document.getElementById('proveedorLista');
        const searchInput = document.getElementById('proveedorSearch');
        const optionsList = document.getElementById('proveedorOptions');
        const options = optionsList.querySelectorAll('.comprar-select-option');
        const hidden = document.getElementById('proveedorHidden');
        const label = document.getElementById('proveedorLabel');
        const emptyMsg = document.getElementById('proveedorEmpty');
        let isOpen = false;

        function openDropdown() {
            isOpen = true;
            dropdown.classList.add('open');
            trigger.classList.add('active');
            searchInput.value = '';
            filterOptions('');
            setTimeout(() => searchInput.focus(), 50);
        }

        function closeDropdown() {
            isOpen = false;
            dropdown.classList.remove('open');
            trigger.classList.remove('active');
        }

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            isOpen ? closeDropdown() : openDropdown();
        });

        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                isOpen ? closeDropdown() : openDropdown();
            }
            if (e.key === 'Escape') closeDropdown();
        });

        document.addEventListener('click', (e) => {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                closeDropdown();
            }
        });

        searchInput.addEventListener('input', () => {
            filterOptions(searchInput.value.trim().toLowerCase());
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDropdown();
                trigger.focus();
            }
        });

        function filterOptions(query) {
            let visible = 0;
            options.forEach(opt => {
                const text = opt.dataset.label.toLowerCase();
                const match = query === '' || text.includes(query);
                opt.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            emptyMsg.style.display = visible === 0 ? 'block' : 'none';
        }

        options.forEach(opt => {
            opt.addEventListener('click', () => {
                hidden.value = opt.dataset.value;
                label.textContent = opt.dataset.label;
                label.classList.remove('placeholder');
                closeDropdown();
                hidden.dispatchEvent(new Event('change'));
            });
        });
    })();
    </script>

</body>

</html>
