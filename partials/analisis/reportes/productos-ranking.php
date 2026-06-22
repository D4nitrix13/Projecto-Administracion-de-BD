<style>
    .ranking-tabs {
        display: flex;
        gap: 4px;
        margin-bottom: 16px;
        background: #f3f4f6;
        border-radius: 10px;
        padding: 4px;
        width: fit-content;
    }

    .ranking-tab {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #6b7280;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ranking-tab:hover {
        color: #374151;
        background: #e5e7eb;
    }

    .ranking-tab.active {
        background: #ffffff;
        color: #111827;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .ranking-panel {
        display: none;
    }

    .ranking-panel.active {
        display: block;
    }

    .ranking-table {
        min-width: 600px;
    }

    .ranking-pos {
        font-weight: 900;
        color: #6b7280;
        width: 40px;
    }

    .ranking-pos-1 { color: #f59e0b; }
    .ranking-pos-2 { color: #94a3b8; }
    .ranking-pos-3 { color: #cd7f32; }
</style>

<section class="reports-section">
    <div class="reports-section-header">
        <div>
            <h2>Ranking de productos</h2>
            <p class="dashboard-muted">
                Productos más y menos vendidos, y categorías con menor movimiento.
            </p>
        </div>
    </div>

    <div style="display: grid; gap: 24px;">

        <div class="reports-chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 4px;">
                <div>
                    <h3>Más vendidos</h3>
                    <p>Top productos con más unidades vendidas.</p>
                </div>
                <form method="GET" action="reportes.php" style="display: flex; align-items: center; gap: 6px;">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoReporte) ?>">
                    <?php if ($fechaDesde): ?><input type="hidden" name="desde" value="<?= htmlspecialchars($fechaDesde) ?>"><?php endif; ?>
                    <?php if ($fechaHasta): ?><input type="hidden" name="hasta" value="<?= htmlspecialchars($fechaHasta) ?>"><?php endif; ?>
                    <input type="hidden" name="limit_menos" value="<?= $limitMenosVendidos ?>">
                    <input type="hidden" name="limit_categorias" value="<?= $limitCategorias ?>">
                    <input type="hidden" name="limit_clientes_top" value="<?= $limitClientesTop ?>">
                    <input type="hidden" name="limit_clientes_bajo" value="<?= $limitClientesBajo ?>">
                    <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Mostrar:</label>
                    <input type="number" name="limit_mas" value="<?= $limitMasVendidos ?>" min="1" max="100"
                        style="width: 60px; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.85rem; text-align: center;">
                    <button type="submit" class="btn-primary-inline" style="padding: 4px 10px; font-size: 0.8rem;">Aplicar</button>
                </form>
            </div>

            <div class="ranking-tabs" data-group="mas">
                <button class="ranking-tab active" onclick="switchRankingTab('mas', 'mes')">Mes</button>
                <button class="ranking-tab" onclick="switchRankingTab('mas', 'semana')">Semana</button>
                <button class="ranking-tab" onclick="switchRankingTab('mas', 'anio')">Año</button>
            </div>

            <div id="ranking-mas-mes" class="ranking-panel active">
                <?php if (empty($rankingMasVendidosMes)): ?>
                    <p class="empty-message">No hay ventas registradas este mes.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Unidades</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingMasVendidosMes as $i => $p): ?>
                                    <tr>
                                        <td class="ranking-pos <?= $i < 3 ? 'ranking-pos-' . ($i + 1) : '' ?>"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($p["codigo"]) ?></td>
                                        <td><?= htmlspecialchars($p["producto"]) ?></td>
                                        <td><?= (int)$p["cantidad_vendida"] ?></td>
                                        <td>C$ <?= number_format((float)$p["total_vendido"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-mas-semana" class="ranking-panel">
                <?php if (empty($rankingMasVendidosSemana)): ?>
                    <p class="empty-message">No hay ventas registradas esta semana.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Unidades</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingMasVendidosSemana as $i => $p): ?>
                                    <tr>
                                        <td class="ranking-pos <?= $i < 3 ? 'ranking-pos-' . ($i + 1) : '' ?>"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($p["codigo"]) ?></td>
                                        <td><?= htmlspecialchars($p["producto"]) ?></td>
                                        <td><?= (int)$p["cantidad_vendida"] ?></td>
                                        <td>C$ <?= number_format((float)$p["total_vendido"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-mas-anio" class="ranking-panel">
                <?php if (empty($rankingMasVendidosAnio)): ?>
                    <p class="empty-message">No hay ventas registradas este año.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Unidades</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingMasVendidosAnio as $i => $p): ?>
                                    <tr>
                                        <td class="ranking-pos <?= $i < 3 ? 'ranking-pos-' . ($i + 1) : '' ?>"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($p["codigo"]) ?></td>
                                        <td><?= htmlspecialchars($p["producto"]) ?></td>
                                        <td><?= (int)$p["cantidad_vendida"] ?></td>
                                        <td>C$ <?= number_format((float)$p["total_vendido"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="reports-chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 4px;">
                <div>
                    <h3>Menos vendidos</h3>
                    <p>Productos con menos movimiento que tienen stock disponible.</p>
                </div>
                <form method="GET" action="reportes.php" style="display: flex; align-items: center; gap: 6px;">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoReporte) ?>">
                    <?php if ($fechaDesde): ?><input type="hidden" name="desde" value="<?= htmlspecialchars($fechaDesde) ?>"><?php endif; ?>
                    <?php if ($fechaHasta): ?><input type="hidden" name="hasta" value="<?= htmlspecialchars($fechaHasta) ?>"><?php endif; ?>
                    <input type="hidden" name="limit_mas" value="<?= $limitMasVendidos ?>">
                    <input type="hidden" name="limit_categorias" value="<?= $limitCategorias ?>">
                    <input type="hidden" name="limit_clientes_top" value="<?= $limitClientesTop ?>">
                    <input type="hidden" name="limit_clientes_bajo" value="<?= $limitClientesBajo ?>">
                    <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Mostrar:</label>
                    <input type="number" name="limit_menos" value="<?= $limitMenosVendidos ?>" min="1" max="100"
                        style="width: 60px; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.85rem; text-align: center;">
                    <button type="submit" class="btn-primary-inline" style="padding: 4px 10px; font-size: 0.8rem;">Aplicar</button>
                </form>
            </div>

            <div class="ranking-tabs" data-group="menos">
                <button class="ranking-tab active" onclick="switchRankingTab('menos', 'mes')">Mes</button>
                <button class="ranking-tab" onclick="switchRankingTab('menos', 'semana')">Semana</button>
                <button class="ranking-tab" onclick="switchRankingTab('menos', 'anio')">Año</button>
            </div>

            <div id="ranking-menos-mes" class="ranking-panel active">
                <?php if (empty($rankingMenosVendidosMes)): ?>
                    <p class="empty-message">No hay productos con stock disponible.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Stock actual</th>
                                    <th>Unidades vendidas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingMenosVendidosMes as $i => $p): ?>
                                    <tr>
                                        <td class="ranking-pos"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($p["codigo"]) ?></td>
                                        <td><?= htmlspecialchars($p["producto"]) ?></td>
                                        <td>
                                            <?php if ((int)$p["stock_actual"] <= 5): ?>
                                                <span class="report-status report-status-danger"><?= (int)$p["stock_actual"] ?></span>
                                            <?php else: ?>
                                                <span class="report-status report-status-ok"><?= (int)$p["stock_actual"] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int)$p["cantidad_vendida"] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-menos-semana" class="ranking-panel">
                <?php if (empty($rankingMenosVendidosSemana)): ?>
                    <p class="empty-message">No hay productos con stock disponible.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Stock actual</th>
                                    <th>Unidades vendidas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingMenosVendidosSemana as $i => $p): ?>
                                    <tr>
                                        <td class="ranking-pos"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($p["codigo"]) ?></td>
                                        <td><?= htmlspecialchars($p["producto"]) ?></td>
                                        <td>
                                            <?php if ((int)$p["stock_actual"] <= 5): ?>
                                                <span class="report-status report-status-danger"><?= (int)$p["stock_actual"] ?></span>
                                            <?php else: ?>
                                                <span class="report-status report-status-ok"><?= (int)$p["stock_actual"] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int)$p["cantidad_vendida"] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-menos-anio" class="ranking-panel">
                <?php if (empty($rankingMenosVendidosAnio)): ?>
                    <p class="empty-message">No hay productos con stock disponible.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Stock actual</th>
                                    <th>Unidades vendidas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingMenosVendidosAnio as $i => $p): ?>
                                    <tr>
                                        <td class="ranking-pos"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($p["codigo"]) ?></td>
                                        <td><?= htmlspecialchars($p["producto"]) ?></td>
                                        <td>
                                            <?php if ((int)$p["stock_actual"] <= 5): ?>
                                                <span class="report-status report-status-danger"><?= (int)$p["stock_actual"] ?></span>
                                            <?php else: ?>
                                                <span class="report-status report-status-ok"><?= (int)$p["stock_actual"] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int)$p["cantidad_vendida"] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="reports-chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 4px;">
                <div>
                    <h3>Categorías débiles</h3>
                    <p>Categorías con menos productos y stock total.</p>
                </div>
                <form method="GET" action="reportes.php" style="display: flex; align-items: center; gap: 6px;">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoReporte) ?>">
                    <?php if ($fechaDesde): ?><input type="hidden" name="desde" value="<?= htmlspecialchars($fechaDesde) ?>"><?php endif; ?>
                    <?php if ($fechaHasta): ?><input type="hidden" name="hasta" value="<?= htmlspecialchars($fechaHasta) ?>"><?php endif; ?>
                    <input type="hidden" name="limit_mas" value="<?= $limitMasVendidos ?>">
                    <input type="hidden" name="limit_menos" value="<?= $limitMenosVendidos ?>">
                    <input type="hidden" name="limit_clientes_top" value="<?= $limitClientesTop ?>">
                    <input type="hidden" name="limit_clientes_bajo" value="<?= $limitClientesBajo ?>">
                    <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Mostrar:</label>
                    <input type="number" name="limit_categorias" value="<?= $limitCategorias ?>" min="1" max="100"
                        style="width: 60px; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.85rem; text-align: center;">
                    <button type="submit" class="btn-primary-inline" style="padding: 4px 10px; font-size: 0.8rem;">Aplicar</button>
                </form>
            </div>

            <?php if (empty($rankingCategoriasDebiles)): ?>
                <p class="empty-message">No hay categorías registradas.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Categoría</th>
                                <th>Productos</th>
                                <th>Stock total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rankingCategoriasDebiles as $i => $c): ?>
                                <tr>
                                    <td class="ranking-pos"><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($c["categoria"]) ?></td>
                                    <td><?= (int)$c["cantidad_productos"] ?></td>
                                    <td><?= (int)$c["stock_total"] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<script>
function switchRankingTab(group, period) {
    document.querySelectorAll('.ranking-tabs[data-group="' + group + '"] .ranking-tab').forEach(function(tab) {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');

    document.querySelectorAll('[id^="ranking-' + group + '-"]').forEach(function(panel) {
        panel.classList.remove('active');
    });
    document.getElementById('ranking-' + group + '-' + period).classList.add('active');
}
</script>
