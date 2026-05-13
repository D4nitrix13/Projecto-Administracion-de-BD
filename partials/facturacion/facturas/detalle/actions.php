<div class="invoice-actions">
    <a href="facturas.php" class="btn-secondary-inline">
        Volver al historial
    </a>

    <div class="invoice-actions-group">
        <a
            href="editar_factura.php?id=<?= (int)$factura["id_factura"] ?>"
            class="btn-secondary-inline">
            Editar factura
        </a>

        <a
            href="imprimir_factura.php?id=<?= (int)$factura["id_factura"] ?>"
            class="btn-primary-inline">
            Imprimir factura
        </a>
    </div>
</div>