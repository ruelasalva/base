Vue.component('dashboard-general', {
    data() {
        return {
            kpis: {},
            ultimasVentas: []
        };
    },
    mounted() {
        this.cargarDatos();
    },
    methods: {
        cargarDatos() {
            fetch('get_dashboard_general_data', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(data => {
                    this.kpis = data.kpis;
                    this.ultimasVentas = data.ultimasVentas;
                });
        }
    },
    template: `
    <div class='px-4'>
      <div class='row mb-4'>
        <div v-for='(valor, clave) in kpis' :key='clave' class='col-xl-4 col-md-6 mb-4'>
          <div class='card border-left-primary shadow h-100 py-2'>
            <div class='card-body'>
              <div class='row no-gutters align-items-center'>
                <div class='col mr-2'>
                  <div class='text-xs font-weight-bold text-uppercase mb-1 text-primary'>{{ clave }}</div>
                  <div class='h5 mb-0 font-weight-bold text-gray-800'>{{ valor }}</div>
                </div>
                <div class='col-auto'>
                  <i class='fas fa-info-circle fa-2x text-gray-300'></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class='card shadow mb-4'>
        <div class='card-header py-3'>
          <h6 class='m-0 font-weight-bold text-success'>Últimas Ventas</h6>
        </div>
        <div class='card-body'>
          <div v-if="ultimasVentas.length > 0">
            <div v-for="venta in ultimasVentas" :key="venta.folio" class="mb-3 border-bottom pb-2">
              <p><strong>Cliente:</strong> {{ venta.cliente }}</p>
              <p><strong>Total:</strong> {{ venta.total }} - {{ venta.tipo }}</p>
              <p><strong>Fecha:</strong> {{ venta.fecha }}</p>
              <p><strong>Folio:</strong> {{ venta.folio }}</p>
            </div>
          </div>
          <div v-else class="text-muted text-center">
            No hay ventas recientes.
          </div>
        </div>
      </div>
    </div>
  `
});

new Vue({ el: '#dashboard-app' });