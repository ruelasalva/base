Vue.component('dashboard-kpi', {
  data() {
    return {
      kpis: {
        'Ingresos Hoy': '$12,450.00',
        'Órdenes Nuevas': '32',
        'Clientes Nuevos': '18',
        'Tickets Resueltos': '27',
        'Cotizaciones Emitidas': '12',
        'Producto Más Vendido': 'Foamy A4 Azul',
        'Cliente Top Ventas': 'Librerías Juárez',
        'Socio Top Ventas': 'Distribuidora Central'
      },
      ventasRecientes: [
        { nombre: 'Juan Pérez', email: 'juan@example.com', total: '$1,299.00', tipo: 'Contado', fecha: '2025-07-27 14:30', status: 'Pagado', folio: 'F-1234' },
        { nombre: 'Ana Ruiz', email: 'ana@example.com', total: '$950.00', tipo: 'Crédito', fecha: '2025-07-27 11:20', status: 'Pagado', folio: 'F-1235' }
      ],
      chartData: {
        semana: [1200, 1350, 1800, 2100, 1750, 1900, 2250],
        meses: [12000, 15500, 13900, 16800, 17800, 21000, 23000],
        anual: [52000, 68000, 74000, 88000],
        ticketsStatus: [10, 5, 12, 3],
        ticketsDepartamentos: [12, 18, 7, 5, 10],
        labelsMeses: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'],
        labelsAnios: ['2022', '2023', '2024', '2025'],
        labelsStatus: ['Abiertos', 'Pendientes', 'Resueltos', 'Cerrados'],
        labelsDepartamentos: ['Ventas', 'Soporte', 'Facturación', 'Logística', 'Otros']
      },
      config: {
        google_analytics_url: '',
        analytics_active: 0
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      setTimeout(this.renderCharts, 500);
    });
  },
  methods: {
    renderCharts() {
      const colores = [
        '#007bff', '#6610f2', '#6f42c1', '#e83e8c', '#fd7e14',
        '#ffc107', '#28a745', '#20c997', '#17a2b8', '#6c757d'
      ];

      const charts = [
        { id: 'chart-semana', data: this.chartData.semana, label: 'Ingresos por Semana' },
        { id: 'chart-mensual', data: this.chartData.meses, label: 'Ventas por Mes', labels: this.chartData.labelsMeses },
        { id: 'chart-anual', data: this.chartData.anual, label: 'Tendencia Anual', labels: this.chartData.labelsAnios },
        { id: 'chart-tickets-status', data: this.chartData.ticketsStatus, label: 'Estatus de Tickets', labels: this.chartData.labelsStatus },
        { id: 'chart-tickets-depto', data: this.chartData.ticketsDepartamentos, label: 'Tickets por Departamento', labels: this.chartData.labelsDepartamentos }
      ];

      charts.forEach((chart, i) => {
        const canvas = document.getElementById(chart.id);
        if (!canvas) return;
        const ctx = canvas.getContext("2d");
        const type = ['chart-semana', 'chart-mensual', 'chart-anual'].includes(chart.id) ? 'line' : 'bar';

        new Chart(ctx, {
          type,
          data: {
            labels: chart.labels || ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
              label: chart.label,
              data: chart.data,
              backgroundColor: colores,
              borderColor: '#007bff',
              borderWidth: 2,
              fill: true
            }]
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
          <div class='card border-left-info shadow h-100 py-2'>
            <div class='card-body'>
              <div class='row no-gutters align-items-center'>
                <div class='col mr-2'>
                  <div class='text-xs font-weight-bold text-uppercase mb-1 text-info'>{{ clave }}</div>
                  <div class='h5 mb-0 font-weight-bold text-gray-800'>{{ valor }}</div>
                </div>
                <div class='col-auto'>
                  <i class='fas fa-chart-line fa-2x text-gray-300'></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-6'>
          <div class='card shadow mb-4'>
            <div class='card-header py-3'>
              <h6 class='m-0 font-weight-bold text-primary'>Tendencia Semanal</h6>
            </div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-semana'></canvas>
            </div>
          </div>
        </div>
        <div class='col-md-6'>
          <div class='card shadow mb-4'>
            <div class='card-header py-3'>
              <h6 class='m-0 font-weight-bold text-primary'>Ventas por Mes</h6>
            </div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-mensual'></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-12'>
          <div class='card shadow mb-4'>
            <div class='card-header py-3'>
              <h6 class='m-0 font-weight-bold text-primary'>Tendencia Anual</h6>
            </div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-anual'></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-6'>
          <div class='card shadow mb-4'>
            <div class='card-header py-3'>
              <h6 class='m-0 font-weight-bold text-primary'>Tickets por Estatus</h6>
            </div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-tickets-status'></canvas>
            </div>
          </div>
        </div>
        <div class='col-md-6'>
          <div class='card shadow mb-4'>
            <div class='card-header py-3'>
              <h6 class='m-0 font-weight-bold text-primary'>Tickets por Departamento</h6>
            </div>
            <div class='card-body' style='min-height:280px'>
              <canvas id='chart-tickets-depto'></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class='card shadow mb-4'>
        <div class='card-header py-3'>
          <h6 class='m-0 font-weight-bold text-success'>Últimas Ventas</h6>
        </div>
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

      <div v-if="config.analytics_active == 1 && config.google_analytics_url" class='card mt-4'>
        <div class='card-header'>
          <h6 class='m-0 font-weight-bold text-primary'><i class='fab fa-google'></i> Google Analytics</h6>
        </div>
        <div class='card-body'>
          <iframe :src='config.google_analytics_url' style='width:100%;height:400px;border:0;' allowfullscreen loading='lazy'></iframe>
        </div>
      </div>
      <div v-else class='alert alert-info mt-4 text-center'>
        <i class='fas fa-info-circle'></i> Google Analytics no está configurado.
      </div>
    </div>
  `
});

new Vue({ el: '#dashboard-app' });