console.log('[DASHBOARD-VUE] Inicializando módulo de Compras...');

document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('app')) {
        console.warn('[DASHBOARD-VUE] Contenedor #app no encontrado.');
        return;
    }

    new Vue({
        el: '#app',
        data() {
            return {
                kpi: window.dashboardData || {},
                charts: {}
            };
        },
        mounted() {
            console.log('[DASHBOARD-VUE] KPIs cargados:', this.kpi);
            this.renderAllCharts();
        },
        methods: {
            renderAllCharts() {
                this.destroyCharts(); // Limpia si existen
                this.renderChartOrdenesFacturas();
                this.renderChartFacturasStatus();
                this.renderChartPagos();
                this.renderChartProveedores();
            },
            destroyCharts() {
                for (const key in this.charts) {
                    if (this.charts[key]) {
                        this.charts[key].destroy();
                    }
                }
                this.charts = {};
            },
            renderChartOrdenesFacturas() {
                const ctx = document.getElementById('chartOrdenesFacturas');
                if (!ctx) return;

                const gBlue = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gBlue.addColorStop(0, '#5e72e4');
                gBlue.addColorStop(1, '#7889ef');

                const gCyan = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gCyan.addColorStop(0, '#11cdef');
                gCyan.addColorStop(1, '#7fdef6');

                const gGreen = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gGreen.addColorStop(0, '#2dce89');
                gGreen.addColorStop(1, '#7fe1b7');

                this.charts.ordenesFacturas = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Órdenes abiertas', 'Facturas proceso', 'Notas crédito'],
                        datasets: [{
                            label: 'Totales',
                            data: [
                                parseInt(this.kpi.ordenes_abiertas) || 0,
                                parseInt(this.kpi.facturas_proceso) || 0,
                                parseInt(this.kpi.notas_credito) || 0
                            ],
                            backgroundColor: [gBlue, gCyan, gGreen],
                            borderRadius: 10,
                            maxBarThickness: 45
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 1200, easing: 'easeOutQuart' },
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'Órdenes vs Facturas',
                                color: '#333',
                                font: { size: 16, weight: '600' },
                                padding: { bottom: 10 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                cornerRadius: 6,
                                displayColors: false,
                                callbacks: {
                                    label: ctx => ctx.label + ': ' + ctx.formattedValue
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                grid: { color: 'rgba(0,0,0,0.05)', borderDash: [3, 3] }
                            }
                        }
                    }
                });
            },

            renderChartFacturasStatus() {
                const ctx = document.getElementById('chartFacturasStatus');
                if (!ctx) return;

                const gYellow = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gYellow.addColorStop(0, '#f4d03f');
                gYellow.addColorStop(1, '#f9e79f');

                const gBlue = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gBlue.addColorStop(0, '#5e72e4');
                gBlue.addColorStop(1, '#aab8f6');

                const gRed = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gRed.addColorStop(0, '#f5365c');
                gRed.addColorStop(1, '#f783ac');

                this.charts.facturasStatus = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sin Orden', 'En Proceso', 'Rechazadas'],
                        datasets: [{
                            data: [
                                parseInt(this.kpi.facturas_sin_orden) || 0,
                                parseInt(this.kpi.facturas_proceso) || 0,
                                parseInt(this.kpi.facturas_rechazadas) || 0
                            ],
                            backgroundColor: [gYellow, gBlue, gRed],
                            borderWidth: 2,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 1200, easing: 'easeOutQuart' },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, padding: 15 }
                            },
                            title: {
                                display: true,
                                text: 'Distribución de Facturas',
                                color: '#333',
                                font: { size: 16, weight: '600' },
                                padding: { bottom: 10 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                cornerRadius: 6,
                                displayColors: false
                            }
                        }
                    }
                });
            },
            renderChartProveedores() {
                const ctx = document.getElementById('chartProveedores');
                if (!ctx || !window.dashboardData.proveedores) return;
                const data = window.dashboardData.proveedores;
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.map(p => p.name),
                        datasets: [{
                            data: data.map(p => p.total),
                            backgroundColor: ['#5e72e4', '#2dce89', '#11cdef', '#f5365c', '#fb6340']
                        }]
                    },
                    options: { plugins: { legend: { position: 'bottom' } } }
                });
            },
            renderChartPagos() {
                const ctx = document.getElementById('chartPagos');
                if (!ctx) return;

                const gGreen = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gGreen.addColorStop(0, '#2dce89');
                gGreen.addColorStop(1, '#7fe1b7');

                this.charts.pagos = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Pagos Programados', 'Proveedores Activos'],
                        datasets: [{
                            label: 'Pagos / Proveedores',
                            data: [
                                parseInt(this.kpi.pagos_programados) || 0,
                                parseInt(this.kpi.proveedores_activos) || 0
                            ],
                            borderColor: '#2dce89',
                            backgroundColor: gGreen,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 5,
                            pointBackgroundColor: '#2dce89',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 1200, easing: 'easeOutQuart' },
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'Pagos Programados',
                                color: '#333',
                                font: { size: 16, weight: '600' },
                                padding: { bottom: 10 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                cornerRadius: 6,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                grid: { color: 'rgba(0,0,0,0.05)', borderDash: [3, 3] }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            },
            actualizarDatos() {
                // 🚀 Si más adelante quieres usar AJAX, aquí iría la petición.
                // Por ahora solo vuelve a renderizar con los mismos datos PHP.
                console.log('[DASHBOARD-VUE] Refrescando gráficas...');
                this.renderAllCharts();
            }
        }
    });
});
