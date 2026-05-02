<section class="invoice-toolbar">
    <div>
        <h1>Factura #<?= (int)$factura["id_factura"] ?></h1>
        <p>
            <?= htmlspecialchars($fechaFactura) ?> ·
            <?= htmlspecialchars($factura["seccion_nombre"] ?? "") ?> ·
            <?= htmlspecialchars($factura["usuario_nombre"] ?? "") ?>
        </p>
    </div>

    <div class="invoice-toolbar-actions">
        <a href="facturas.php" class="btn-secondary-inline">
            Volver
        </a>

        <button type="button" class="btn-primary-inline" onclick="window.print()">
            Imprimir factura
        </button>
    </div>
</section>