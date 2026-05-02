<script>
    const ventasLabels = <?= json_encode(array_column($ventasPorDia, "dia")) ?>;
    const ventasData = <?= json_encode(array_map("floatval", array_column($ventasPorDia, "total_dia"))) ?>;
    const ventasFacturas = <?= json_encode(array_map("intval", array_column($ventasPorDia, "cantidad_facturas"))) ?>;

    const ventasLabelsFinal = ventasLabels.length > 0 ? ventasLabels : ["Sin datos"];
    const ventasDataFinal = ventasData.length > 0 ? ventasData : [0];

    new Chart(document.getElementById("reporteVentasChart"), {
        type: "line",
        data: {
            labels: ventasLabelsFinal,
            datasets: [{
                label: "Ingresos C$",
                data: ventasDataFinal,
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            interaction: {
                mode: "index",
                intersect: false
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return "Ingresos: C$ " + Number(context.raw).toLocaleString();
                        },
                        afterLabel: function(context) {
                            const index = context.dataIndex;
                            const cantidad = ventasFacturas[index] ?? 0;
                            return "Facturas: " + cantidad;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const productosLabels = <?= json_encode(array_column($productosMasVendidos, "producto")) ?>;
    const productosData = <?= json_encode(array_map("intval", array_column($productosMasVendidos, "cantidad_vendida"))) ?>;

    const productosLabelsFinal = productosLabels.length > 0 ? productosLabels : ["Sin ventas"];
    const productosDataFinal = productosData.length > 0 ? productosData : [1];

    new Chart(document.getElementById("reporteProductosChart"), {
        type: "doughnut",
        data: {
            labels: productosLabelsFinal,
            datasets: [{
                label: "Cantidad vendida",
                data: productosDataFinal,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.label === "Sin ventas") {
                                return "Todavía no hay ventas registradas";
                            }

                            return context.label + ": " + context.raw + " unidades vendidas";
                        }
                    }
                }
            }
        }
    });
</script>