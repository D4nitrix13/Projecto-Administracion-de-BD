<section class="reports-charts-grid">
    <article class="reports-chart-card">
        <div class="card-header">
            <div>
                <h3>Ventas por día</h3>
                <span>Ingresos agrupados por fecha. Pase el cursor o haga click sobre un punto.</span>
            </div>
        </div>

        <div class="reports-chart-box">
            <canvas id="reporteVentasChart"></canvas>
        </div>

        <div class="reports-chart-detail" id="ventasChartDetail">
            <span>Detalle seleccionado</span>
            <strong>Seleccione un punto de la gráfica</strong>
            <p>Al hacer click verá fecha, ingresos y cantidad de facturas.</p>
        </div>
    </article>

    <article class="reports-chart-card">
        <div class="card-header">
            <div>
                <h3>Productos más vendidos</h3>
                <span>Cantidad vendida por producto. Pase el cursor o haga click sobre una sección.</span>
            </div>
        </div>

        <div class="reports-chart-box">
            <canvas id="reporteProductosChart"></canvas>
        </div>

        <div class="reports-chart-detail" id="productosChartDetail">
            <span>Detalle seleccionado</span>
            <strong>Seleccione un producto de la gráfica</strong>
            <p>Al hacer click verá cantidad vendida y participación.</p>
        </div>
    </article>
</section>