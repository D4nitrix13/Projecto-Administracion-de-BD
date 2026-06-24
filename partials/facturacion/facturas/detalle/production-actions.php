<?php
/** @var array $factura */
/** @var array $user */

$estadoProduccion = $factura["estado_produccion"] ?? "Pendiente";
$idFactura = (int)($factura["id_factura"] ?? 0);
$idRol = (int)($user["id_rol"] ?? 0);

$transicionesPermitidas = [
    "Pendiente"           => ["En producción", "Cancelada"],
    "En producción"       => ["Lista para entregar", "Cancelada"],
    "Lista para entregar" => ["Entregada", "Cancelada"],
    "Entregada"           => [],
    "Cancelada"           => [],
];

$estadosDisponibles = $transicionesPermitidas[$estadoProduccion] ?? [];

if (empty($estadosDisponibles) || $idRol > 2) {
    return;
}

$etiquetas = [
    "En producción"       => "Iniciar producción",
    "Lista para entregar" => "Marcar lista para entregar",
    "Entregada"           => "Marcar como entregada",
    "Cancelada"           => "Cancelar factura",
];

$clases = [
    "En producción"       => "btn-production-primary",
    "Lista para entregar" => "btn-production-info",
    "Entregada"           => "btn-production-success",
    "Cancelada"           => "btn-production-danger",
];
?>

<style>
    .production-actions {
        margin-top: 20px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .production-actions-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .production-actions-header h3 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
    }

    .production-actions-header .current-state {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .production-actions-header .current-state.estado-cancelada {
        background: #fee2e2;
        color: #dc2626;
    }

    .production-actions-header .current-state.estado-entregada {
        background: #d1fae5;
        color: #059669;
    }

    .production-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .production-buttons form {
        display: inline;
    }

    .btn-production-primary,
    .btn-production-info,
    .btn-production-success,
    .btn-production-danger {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-production-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-production-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-production-info {
        background: #0891b2;
        color: #ffffff;
    }

    .btn-production-info:hover {
        background: #0e7490;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
    }

    .btn-production-success {
        background: #059669;
        color: #ffffff;
    }

    .btn-production-success:hover {
        background: #047857;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .btn-production-danger {
        background: #dc2626;
        color: #ffffff;
    }

    .btn-production-danger:hover {
        background: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
</style>

<div class="production-actions">
    <div class="production-actions-header">
        <h3>Acciones de producción</h3>
        <span class="current-state <?= $estadoProduccion === "Cancelada" ? "estado-cancelada" : ($estadoProduccion === "Entregada" ? "estado-entregada" : "") ?>">
            <?= htmlspecialchars($estadoProduccion) ?>
        </span>
    </div>

    <div class="production-buttons">
        <?php foreach ($estadosDisponibles as $estado): ?>
            <form method="POST" action="transicionar_estado_produccion.php"
                  onsubmit="return confirm('¿Desea cambiar el estado a \"<?= htmlspecialchars($estado) ?>\"?');">
                <input type="hidden" name="_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="id_factura" value="<?= $idFactura ?>">
                <input type="hidden" name="nuevo_estado" value="<?= htmlspecialchars($estado) ?>">
                <button type="submit" class="<?= $clases[$estado] ?? "btn-production-primary" ?>">
                    <?= htmlspecialchars($etiquetas[$estado] ?? $estado) ?>
                </button>
            </form>
        <?php endforeach; ?>
    </div>
</div>
