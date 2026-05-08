<script>
    const ventasLabels = <?= json_encode(array_column($ventasPorDia, "dia")) ?>;
    const ventasData = <?= json_encode(array_map("floatval", array_column($ventasPorDia, "total_dia"))) ?>;
    const ventasFacturas = <?= json_encode(array_map("intval", array_column($ventasPorDia, "cantidad_facturas"))) ?>;

    const ventasLabelsFinal = ventasLabels.length > 0 ? ventasLabels : ["Sin datos"];
    const ventasDataFinal = ventasData.length > 0 ? ventasData : [0];
    const ventasFacturasFinal = ventasFacturas.length > 0 ? ventasFacturas : [0];

    const ventasDetail = document.getElementById("ventasChartDetail");
    const productosDetail = document.getElementById("productosChartDetail");

    function formatCurrency(value) {
        return "C$ " + Number(value || 0).toLocaleString("es-NI", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function actualizarDetalleVentas(index) {
        if (!ventasDetail) {
            return;
        }

        const fecha = ventasLabelsFinal[index] ?? "Sin fecha";
        const ingresos = ventasDataFinal[index] ?? 0;
        const facturas = ventasFacturasFinal[index] ?? 0;

        ventasDetail.innerHTML = `
            <span>Día seleccionado</span>
            <strong>${fecha}</strong>
            <p>
                Ingresos: <b>${formatCurrency(ingresos)}</b><br>
                Facturas emitidas: <b>${facturas}</b>
            </p>
        `;
    }

    function actualizarDetalleProductos(index) {
        if (!productosDetail) {
            return;
        }

        const producto = productosLabelsFinal[index] ?? "Sin producto";
        const cantidad = productosDataFinal[index] ?? 0;
        const totalUnidades = productosDataFinal.reduce((acc, value) => acc + Number(value || 0), 0);
        const porcentaje = totalUnidades > 0 ? ((cantidad / totalUnidades) * 100).toFixed(2) : "0.00";

        productosDetail.innerHTML = `
            <span>Producto seleccionado</span>
            <strong>${producto}</strong>
            <p>
                Cantidad vendida: <b>${cantidad}</b> unidades<br>
                Participación: <b>${porcentaje}%</b> del total mostrado
            </p>
        `;
    }

    const ventasChart = new Chart(document.getElementById("reporteVentasChart"), {
        type: "line",
        data: {
            labels: ventasLabelsFinal,
            datasets: [{
                label: "Ingresos C$",
                data: ventasDataFinal,
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 9,
                pointHitRadius: 18
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 450
            },
            interaction: {
                mode: "nearest",
                intersect: false
            },
            onHover: function(event, elements) {
                event.native.target.style.cursor = elements.length ? "pointer" : "default";
            },
            onClick: function(event, elements) {
                if (!elements.length) {
                    return;
                }

                actualizarDetalleVentas(elements[0].index);
            },
            plugins: {
                tooltip: {
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return "Fecha: " + context[0].label;
                        },
                        label: function(context) {
                            return "Ingresos: " + formatCurrency(context.raw);
                        },
                        afterLabel: function(context) {
                            const cantidad = ventasFacturasFinal[context.dataIndex] ?? 0;
                            return "Facturas emitidas: " + cantidad;
                        }
                    }
                },
                legend: {
                    labels: {
                        usePointStyle: true
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return "C$ " + Number(value).toLocaleString("es-NI");
                        }
                    }
                }
            }
        }
    });

    const productosLabels = <?= json_encode(array_column($productosMasVendidos, "producto")) ?>;
    const productosData = <?= json_encode(array_map("intval", array_column($productosMasVendidos, "cantidad_vendida"))) ?>;
    const productosTotales = <?= json_encode(array_map("floatval", array_column($productosMasVendidos, "total_vendido"))) ?>;

    const productosLabelsFinal = productosLabels.length > 0 ? productosLabels : ["Sin ventas"];
    const productosDataFinal = productosData.length > 0 ? productosData : [1];
    const productosTotalesFinal = productosTotales.length > 0 ? productosTotales : [0];

    const productosChart = new Chart(document.getElementById("reporteProductosChart"), {
        type: "doughnut",
        data: {
            labels: productosLabelsFinal,
            datasets: [{
                label: "Cantidad vendida",
                data: productosDataFinal,
                borderWidth: 2,
                hoverOffset: 14
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 450
            },
            cutout: "62%",
            onHover: function(event, elements) {
                event.native.target.style.cursor = elements.length ? "pointer" : "default";
            },
            onClick: function(event, elements) {
                if (!elements.length) {
                    return;
                }

                actualizarDetalleProductos(elements[0].index);
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.label === "Sin ventas") {
                                return "Todavía no hay ventas registradas";
                            }

                            const index = context.dataIndex;
                            const cantidad = context.raw;
                            const totalVendido = productosTotalesFinal[index] ?? 0;

                            return [
                                context.label,
                                "Unidades: " + cantidad,
                                "Total vendido: " + formatCurrency(totalVendido)
                            ];
                        }
                    }
                },
                legend: {
                    position: "top",
                    labels: {
                        boxWidth: 14,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    actualizarDetalleVentas(0);
    actualizarDetalleProductos(0);
</script>