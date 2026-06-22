<section class="reports-section">
    <div class="reports-section-header">
        <div>
            <h2>Ranking de clientes</h2>
            <p class="dashboard-muted">
                Clientes que más y menos compran, filtrado por período.
            </p>
        </div>
    </div>

    <div style="display: grid; gap: 24px;">

        <div class="reports-chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 4px;">
                <div>
                    <h3>Clientes que más compran</h3>
                    <p>Top clientes con mayor volumen de compra.</p>
                </div>
                <form method="GET" action="reportes.php" style="display: flex; align-items: center; gap: 6px;">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoReporte) ?>">
                    <?php if ($fechaDesde): ?><input type="hidden" name="desde" value="<?= htmlspecialchars($fechaDesde) ?>"><?php endif; ?>
                    <?php if ($fechaHasta): ?><input type="hidden" name="hasta" value="<?= htmlspecialchars($fechaHasta) ?>"><?php endif; ?>
                    <input type="hidden" name="limit_mas" value="<?= $limitMasVendidos ?>">
                    <input type="hidden" name="limit_menos" value="<?= $limitMenosVendidos ?>">
                    <input type="hidden" name="limit_categorias" value="<?= $limitCategorias ?>">
                    <input type="hidden" name="limit_clientes_bajo" value="<?= $limitClientesBajo ?>">
                    <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Mostrar:</label>
                    <input type="number" name="limit_clientes_top" value="<?= $limitClientesTop ?>" min="1" max="100"
                        style="width: 60px; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.85rem; text-align: center;">
                    <button type="submit" class="btn-primary-inline" style="padding: 4px 10px; font-size: 0.8rem;">Aplicar</button>
                </form>
            </div>

            <div class="ranking-tabs" data-group="clientes-top">
                <button class="ranking-tab active" onclick="switchRankingTab('clientes-top', 'semana')">Semana</button>
                <button class="ranking-tab" onclick="switchRankingTab('clientes-top', 'mes')">Mes</button>
                <button class="ranking-tab" onclick="switchRankingTab('clientes-top', 'anio')">Año</button>
            </div>

            <div id="ranking-clientes-top-semana" class="ranking-panel active">
                <?php if (empty($rankingClientesTopSemanal)): ?>
                    <p class="empty-message">No hay compras registradas esta semana.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Facturas</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingClientesTopSemanal as $i => $c): ?>
                                    <tr>
                                        <td class="ranking-pos <?= $i < 3 ? 'ranking-pos-' . ($i + 1) : '' ?>"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($c["cliente"]) ?></td>
                                        <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                        <td><span class="report-status report-status-info"><?= htmlspecialchars($c["tipo_cliente"]) ?></span></td>
                                        <td><?= (int)$c["cantidad_facturas"] ?></td>
                                        <td>C$ <?= number_format((float)$c["total_comprado"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-clientes-top-mes" class="ranking-panel">
                <?php if (empty($rankingClientesTopMensual)): ?>
                    <p class="empty-message">No hay compras registradas este mes.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Facturas</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingClientesTopMensual as $i => $c): ?>
                                    <tr>
                                        <td class="ranking-pos <?= $i < 3 ? 'ranking-pos-' . ($i + 1) : '' ?>"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($c["cliente"]) ?></td>
                                        <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                        <td><span class="report-status report-status-info"><?= htmlspecialchars($c["tipo_cliente"]) ?></span></td>
                                        <td><?= (int)$c["cantidad_facturas"] ?></td>
                                        <td>C$ <?= number_format((float)$c["total_comprado"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-clientes-top-anio" class="ranking-panel">
                <?php if (empty($rankingClientesTopAnual)): ?>
                    <p class="empty-message">No hay compras registradas este año.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Facturas</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingClientesTopAnual as $i => $c): ?>
                                    <tr>
                                        <td class="ranking-pos <?= $i < 3 ? 'ranking-pos-' . ($i + 1) : '' ?>"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($c["cliente"]) ?></td>
                                        <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                        <td><span class="report-status report-status-info"><?= htmlspecialchars($c["tipo_cliente"]) ?></span></td>
                                        <td><?= (int)$c["cantidad_facturas"] ?></td>
                                        <td>C$ <?= number_format((float)$c["total_comprado"], 2) ?></td>
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
                    <h3>Clientes que menos compran</h3>
                    <p>Clientes mayoristas con menor volumen de compra (o sin compras).</p>
                </div>
                <form method="GET" action="reportes.php" style="display: flex; align-items: center; gap: 6px;">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoReporte) ?>">
                    <?php if ($fechaDesde): ?><input type="hidden" name="desde" value="<?= htmlspecialchars($fechaDesde) ?>"><?php endif; ?>
                    <?php if ($fechaHasta): ?><input type="hidden" name="hasta" value="<?= htmlspecialchars($fechaHasta) ?>"><?php endif; ?>
                    <input type="hidden" name="limit_mas" value="<?= $limitMasVendidos ?>">
                    <input type="hidden" name="limit_menos" value="<?= $limitMenosVendidos ?>">
                    <input type="hidden" name="limit_categorias" value="<?= $limitCategorias ?>">
                    <input type="hidden" name="limit_clientes_top" value="<?= $limitClientesTop ?>">
                    <label style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Mostrar:</label>
                    <input type="number" name="limit_clientes_bajo" value="<?= $limitClientesBajo ?>" min="1" max="100"
                        style="width: 60px; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.85rem; text-align: center;">
                    <button type="submit" class="btn-primary-inline" style="padding: 4px 10px; font-size: 0.8rem;">Aplicar</button>
                </form>
            </div>

            <div class="ranking-tabs" data-group="clientes-bajo">
                <button class="ranking-tab active" onclick="switchRankingTab('clientes-bajo', 'semana')">Semana</button>
                <button class="ranking-tab" onclick="switchRankingTab('clientes-bajo', 'mes')">Mes</button>
                <button class="ranking-tab" onclick="switchRankingTab('clientes-bajo', 'anio')">Año</button>
            </div>

            <div id="ranking-clientes-bajo-semana" class="ranking-panel active">
                <?php if (empty($rankingClientesBajoSemanal)): ?>
                    <p class="empty-message">No hay datos de clientes para esta semana.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Facturas</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingClientesBajoSemanal as $i => $c): ?>
                                    <tr>
                                        <td class="ranking-pos"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($c["cliente"]) ?></td>
                                        <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                        <td><span class="report-status report-status-info"><?= htmlspecialchars($c["tipo_cliente"]) ?></span></td>
                                        <td><?= (int)$c["cantidad_facturas"] ?></td>
                                        <td>C$ <?= number_format((float)$c["total_comprado"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-clientes-bajo-mes" class="ranking-panel">
                <?php if (empty($rankingClientesBajoMensual)): ?>
                    <p class="empty-message">No hay datos de clientes para este mes.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Facturas</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingClientesBajoMensual as $i => $c): ?>
                                    <tr>
                                        <td class="ranking-pos"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($c["cliente"]) ?></td>
                                        <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                        <td><span class="report-status report-status-info"><?= htmlspecialchars($c["tipo_cliente"]) ?></span></td>
                                        <td><?= (int)$c["cantidad_facturas"] ?></td>
                                        <td>C$ <?= number_format((float)$c["total_comprado"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ranking-clientes-bajo-anio" class="ranking-panel">
                <?php if (empty($rankingClientesBajoAnual)): ?>
                    <p class="empty-message">No hay datos de clientes para este año.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Facturas</th>
                                    <th>Total C$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankingClientesBajoAnual as $i => $c): ?>
                                    <tr>
                                        <td class="ranking-pos"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($c["cliente"]) ?></td>
                                        <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                        <td><span class="report-status report-status-info"><?= htmlspecialchars($c["tipo_cliente"]) ?></span></td>
                                        <td><?= (int)$c["cantidad_facturas"] ?></td>
                                        <td>C$ <?= number_format((float)$c["total_comprado"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
