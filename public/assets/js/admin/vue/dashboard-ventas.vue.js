Vue.component('dashboard-ventas', {
    data() {
        return {
            kpis: {
                'Total Ventas Hoy': '$10,500.00',
                'Órdenes del Día': '28',
                'Cotizaciones Enviadas': '14',
                'Clientes Nuevos': '9',
                'Producto Más Vendido': 'Cartulina Metálica Azul',
                'Cliente Top': 'Papelería La Moderna',
                'Socio con Más Ventas': 'Distribuciones Norte'
            },
            ventasRecientes: [
                { nombre: 'Eduardo Méndez', email: 'eduardo@cliente.com', total: '$820.00', tipo: 'Contado', fecha: '2025-07-27 13:10', status: 'Pagado', folio: 'F-1240' },
                { nombre: 'Sonia López', email: 'sonia@empresa.com', total: '$2,450.00', tipo: 'Crédito', fecha: '2025-07-27 09:45', status: 'Pendiente', folio: 'F-1241' }
            ],
            chartData: {
                semanal: [800, 1100, 1450, 1900, 1300, 1750, 2200],
                mensual: [10500, 12400, 13950, 15800, 16200, 17900, 21000],
                anual: [52000, 64500, 78000, 90500],
                porProducto: [120, 95, 80, 60, 50],
                porCliente: [24000, 19000, 15000, 9000],
                porSocio: [33000, 27000, 18000, 12000],
                labelsProductos: ['Foamy Azul', 'Cartulina Roja', 'Resistol 850', 'Papel China', 'Tijeras Escolares'],
                labelsClientes: ['La Moderna', 'Papelería Express', 'Oficinas GDL', 'Corporativo XYZ'],
                labelsSocios: ['Distribuciones Norte', 'Distribuidor Sureste', 'Papelería Aliada', 'Centro Papelero']
            }
        };
    },
    mounted() {
        this.$nextTick(() => {
            setTimeout(this.renderCharts, 300);
        });
    },
    methods: {
        renderCharts() {
            const colores = [
                '#007bff', '#6610f2', '#6f42c1', '#e83e8c', '#fd7e14',
                '#ffc107', '#28a745', '#20c997', '#17a2b8', '#6c757d'
            ];

            const charts = [
                { id: 'chart-semanal', data: this.chartData.semanal, label: 'Ingresos Semanales' },
                { id: 'chart-mensual', data: this.chartData.mensual, label: 'Ventas Mensuales', labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'] },
                { id: 'chart-anual', data: this.chartData.anual, label: 'Tendencia Anual', labels: ['2022', '2023', '2024', '2025'] },
                { id: 'chart-producto', data: this.chartData.porProducto, label: 'Top Productos', labels: this.chartData.labelsProductos },
                { id: 'chart-clientes', data: this.chartData.porCliente, label: 'Clientes con Más Compras', labels: this.chartData.labelsClientes },
                { id: 'chart-socios', data: this.chartData.porSocio, label: 'Socios con Más Ventas', labels: this.chartData.labelsSocios }
            ];

            charts.forEach((chart, i) => {
                const canvas = document.getElementById(chart.id);
                if (!canvas) return;
                const ctx = canvas.getContext("2d");
                const type = ['chart-semanal', 'chart-mensual', 'chart-anual'].includes(chart.id) ? 'line' : 'bar';

                new Chart(ctx, {
                    type,
                    data: {
                        labels: chart.labels || ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                        datasets: [
                            {
                                label: chart.label,
                                data: chart.data,
                                backgroundColor: colores,
                                borderColor: '#007bff',
                                borderWidth: 2,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'bottom' },
                            title: { display: true, text: chart.label }
                        },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            });
        }
    },
    template: `
    <div class='px-4'>
      <div class='row mb-4'>
        <div v-for='(valor, clave) in kpis' :key='clave' class='col-xl-3 col-md-6 mb-4'>
          <div class='card border-left-warning shadow h-100 py-2'>
            <div class='card-body'>
              <div class='row no-gutters align-items-center'>
                <div class='col mr-2'>
                  <div class='text-xs font-weight-bold text-uppercase mb-1 text-warning'>{{ clave }}</div>
                  <div class='h5 mb-0 font-weight-bold text-gray-800'>{{ valor }}</div>
                </div>
                <div class='col-auto'>
                  <i class='fas fa-coins fa-2x text-gray-300'></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-6 mb-4'>
          <div class='card shadow'>
            <div class='card-header'><h6 class='mb-0 text-primary'>Ingresos Semanales</h6></div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-semanal'></canvas>
            </div>
          </div>
        </div>
        <div class='col-md-6 mb-4'>
          <div class='card shadow'>
            <div class='card-header'><h6 class='mb-0 text-primary'>Ventas Mensuales</h6></div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-mensual'></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-12 mb-4'>
          <div class='card shadow'>
            <div class='card-header'><h6 class='mb-0 text-primary'>Tendencia Anual</h6></div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-anual'></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-6 mb-4'>
          <div class='card shadow'>
            <div class='card-header'><h6 class='mb-0 text-success'>Productos más Vendidos</h6></div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-producto'></canvas>
            </div>
          </div>
        </div>
        <div class='col-md-6 mb-4'>
          <div class='card shadow'>
            <div class='card-header'><h6 class='mb-0 text-success'>Clientes con más Compras</h6></div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-clientes'></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-12 mb-4'>
          <div class='card shadow'>
            <div class='card-header'><h6 class='mb-0 text-success'>Socios con más Ventas</h6></div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-socios'></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class='card shadow'>
        <div class='card-header'><h6 class='mb-0 text-info'>Últimas Ventas</h6></div>
        <div class='card-body'>
          <div v-for="venta in ventasRecientes" :key="venta.folio" class="mb-3 border-bottom pb-2">
            <p><strong>Cliente:</strong> {{ venta.nombre }} ({{ venta.email }})</p>
            <p><strong>Total:</strong> {{ venta.total }} - {{ venta.tipo }}</p>
            <p><strong>Fecha:</strong> {{ venta.fecha }}</p>
            <p><strong>Estatus:</strong> {{ venta.status }}</p>
            <p><strong>Folio:</strong> {{ venta.folio }}</p>
          </div>
        </div>
      </div>
    </div>
  `
});

new Vue({ el: '#dashboard-app' });
