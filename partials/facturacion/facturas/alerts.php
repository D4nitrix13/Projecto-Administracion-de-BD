<?php
$flashSuccess = $flashSuccess ?? null;
$flashError = $flashError ?? null;

$alerts = [];

if (!empty($flashSuccess)) {
    $alerts[] = [
        "type" => "success",
        "icon" => "✓",
        "title" => "Operación completada",
        "message" => $flashSuccess,
    ];
}

if (!empty($flashError)) {
    $alerts[] = [
        "type" => "danger",
        "icon" => "!",
        "title" => "No se pudo completar la acción",
        "message" => $flashError,
    ];
}
?>

<?php if (!empty($alerts)): ?>
    <style>
        .invoice-flash-stack {
            display: grid;
            gap: 12px;
            margin: 0 0 18px;
        }

        .invoice-flash-alert {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px solid transparent;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
            animation: invoiceFlashIn 0.22s ease-out both;
        }

        .invoice-flash-alert-icon {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 0.95rem;
            font-weight: 900;
            line-height: 1;
        }

        .invoice-flash-alert-content {
            display: grid;
            gap: 3px;
            min-width: 0;
        }

        .invoice-flash-alert-content strong {
            color: #111827;
            font-size: 0.92rem;
            font-weight: 900;
        }

        .invoice-flash-alert-content p {
            margin: 0;
            color: #475569;
            font-size: 0.88rem;
            line-height: 1.45;
        }

        .invoice-flash-alert-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border-color: #86efac;
        }

        .invoice-flash-alert-success .invoice-flash-alert-icon {
            background: #bbf7d0;
            color: #15803d;
        }

        .invoice-flash-alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fff7ed 100%);
            border-color: #fecaca;
        }

        .invoice-flash-alert-danger .invoice-flash-alert-icon {
            background: #fee2e2;
            color: #b91c1c;
        }

        @keyframes invoiceFlashIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .invoice-flash-alert {
                padding: 14px;
                border-radius: 14px;
            }

            .invoice-flash-alert-icon {
                width: 30px;
                height: 30px;
                min-width: 30px;
            }
        }
    </style>

    <div class="invoice-flash-stack" role="status" aria-live="polite">
        <?php foreach ($alerts as $alert): ?>
            <div class="invoice-flash-alert invoice-flash-alert-<?= htmlspecialchars($alert["type"]) ?>">
                <div class="invoice-flash-alert-icon">
                    <?= htmlspecialchars($alert["icon"]) ?>
                </div>

                <div class="invoice-flash-alert-content">
                    <strong><?= htmlspecialchars($alert["title"]) ?></strong>
                    <p><?= htmlspecialchars($alert["message"]) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
