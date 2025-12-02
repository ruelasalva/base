<!-- CONTENT -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Reportes</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo Html::anchor('admin/reportes', 'Módulo de Reportes'); ?>
              </li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/reportes/agregar', 'Agregar Reporte', array('class' => 'btn btn-sm btn-neutral')); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header border-0">
          <h3 class="mb-0">Reportes por Departamento</h3>
        </div>
        <div class="card-body">
          <div class="container-fluid mt-4">
            <div class="row">
              <!-- PANEL IZQUIERDO -->
              <div class="col-md-4">
                <div class="card shadow-sm">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Departamentos y Reportes</h5>
                  </div>
                  <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                    <?php foreach ($departments as $dept): ?>
                      <div class="mb-3">
                        <strong><?php echo strtoupper($dept->name); ?></strong>
                        <ul class="list-group list-group-flush">
                          <?php if (isset($reportes_por_departamento[$dept->id])): ?>
                            <?php foreach ($reportes_por_departamento[$dept->id] as $r): ?>
                              <li class="list-group-item py-2">
                                <a href="#" class="ejecutar-reporte" data-id="<?php echo $r->id; ?>">
                                  <i class="fas fa-file-alt text-success"></i> <?php echo $r->query_name; ?>
                                </a>
                                <br><small class="text-muted"><?php echo $r->description; ?></small>
                              </li>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <li class="list-group-item text-muted py-2">Sin reportes</li>
                          <?php endif; ?>
                        </ul>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>

              <!-- PANEL DERECHO -->
              <div class="col-md-8">
                <div class="card shadow-sm">
                  <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resultado del Reporte</h5>
                    <div>
                      <button class="btn btn-success btn-sm d-none" id="btn-exportar-csv-top">
                        <i class="fas fa-file-csv"></i> Exportar CSV
                      </button>
                      <button class="btn btn-danger btn-sm d-none" id="btn-exportar-pdf-top">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                      </button>
                       <button class="btn btn-info btn-sm d-none" id="btn-graficar">
                        <i class="fas fa-chart-bar"></i> Graficar
                      </button>
                    </div>
                  </div>

                  <div class="card-body">
                    <div id="resultado-container" style="max-height:70vh; overflow:auto;">
                      <div id="resultado">
                        <p class="text-muted">Selecciona un reporte para ejecutarlo.</p>
                      </div>
                    </div>

                    <!-- BOTONES DE EXPORTACIÓN (INFERIORES) -->
                    <div class="text-right mt-3">
                      <button class="btn btn-success btn-sm d-none" id="btn-exportar-csv-bottom">
                        <i class="fas fa-file-csv"></i> Exportar CSV
                      </button>
                      <button class="btn btn-danger btn-sm d-none" id="btn-exportar-pdf-bottom">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                      </button>
                      <button class="btn btn-info btn-sm d-none" id="btn-graficar">
                        <i class="fas fa-chart-bar"></i> Graficar
                      </button>
                    </div>
                    

                    <!-- FORMULARIOS DE EXPORTACIÓN -->
                    <form id="form-exportar-csv" method="post" action="<?php echo Uri::create('admin/reportes/exportar_csv'); ?>" style="display:none;">
                      <input type="hidden" name="query_id" id="query_id_csv">
                    </form>
                    <form id="form-exportar-pdf" method="post" action="<?php echo Uri::create('admin/reportes/exportar_pdf'); ?>" style="display:none;">
                      <input type="hidden" name="query_id" id="query_id_pdf">
                    </form>
                  </div><!-- /card-body -->
                </div><!-- /card -->
              </div><!-- /col-md-8 -->
            </div><!-- /row -->
          </div><!-- /container interno -->
        </div><!-- /card-body -->
      </div><!-- /card -->
    </div><!-- /col -->
  </div><!-- /row -->
  <!-- MODAL DE CONFIGURACIÓN DE GRÁFICO -->
    <div class="modal fade" id="modalGrafico" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
        <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title"><i class="fa-solid fa-gear mr-2"></i> Configurar gráfico</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body bg-light">
            <div class="row">
            <div class="col-md-4 border-right">
                <label><b>Tipo de gráfico</b></label>
                <select id="tipoGrafico" class="form-control mb-3">
                <option value="bar">Barras</option>
                <option value="line">Líneas</option>
                <option value="pie">Circular</option>
                </select>

                <div class="form-check">
                <input class="form-check-input" type="checkbox" id="mostrarLeyenda" checked>
                <label class="form-check-label" for="mostrarLeyenda">Mostrar leyenda</label>
                </div>
            </div>

            <div class="col-md-8">
                <label><b>Selecciona campos a graficar</b></label>
                <table id="tabla-campos" class="table table-sm table-bordered bg-white">
                <thead class="thead-light">
                    <tr><th>#</th><th>Campo</th><th>Incluir</th></tr>
                </thead>
                <tbody></tbody>
                </table>
            </div>
            </div>
        </div>
        <div class="modal-footer bg-white">
            <button class="btn btn-success btn-sm" id="btn-generar-grafico">
            <i class="fa fa-check"></i> Generar gráfico
            </button>
        </div>
        </div>
    </div>
    </div>

    <!-- CONTENEDOR DEL GRÁFICO -->
    <div id="contenedor-grafico" class="d-none mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fa-solid fa-chart-column mr-2"></i> Visualización dinámica</h5>
        </div>
        <div class="card-body" style="background-color:#f8f9fa;">
        <div style="position:relative; height:420px;">
            <canvas id="graficoAuto"></canvas>
        </div>
        </div>
    </div>
    </div>

</div><!-- /container -->

<script>
$(function() {

  let chartInstance = null;
  let reporteData = [];

  // ==============================
  // EJECUTAR REPORTE
  // ==============================
  $('.ejecutar-reporte').on('click', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $('#query_id_csv').val(id);
    $('#query_id_pdf').val(id);

    $('#resultado').html('<div class="alert alert-info">Ejecutando reporte...</div>');
    $('#btn-exportar-csv-top, #btn-exportar-csv-bottom, #btn-exportar-pdf-top, #btn-exportar-pdf-bottom, #btn-graficar').addClass('d-none');

    $.post('<?php echo Uri::create('admin/reportes/ejecutar'); ?>', {query_id: id}, function(res) {
      try {
        let data = JSON.parse(res);
        if (data.error) {
          Swal.fire('Error', data.error, 'error');
          return;
        }

        if (data.rows && data.rows.length > 0) {
          reporteData = data.rows; // guardamos datos globalmente para graficar
          let html = '<table class="table table-bordered table-striped table-hover table-sm">';
          html += '<thead class="thead-light sticky-top"><tr>';
          Object.keys(data.rows[0]).forEach(k => html += '<th>'+k+'</th>');
          html += '</tr></thead><tbody>';
          data.rows.forEach(row => {
            html += '<tr>';
            Object.keys(row).forEach(k => {
                let v = row[k];

                // Detectar y convertir timestamps UNIX (10 dígitos)
                if (/^\d{10}$/.test(v)) {
                const fecha = new Date(v * 1000);
                v = fecha.toLocaleString('es-MX', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                }

                html += '<td>' + v + '</td>';
            });
            html += '</tr>';
});

          html += '</tbody></table>';

          $('#resultado').html(html);
          // Mostrar botones de exportar + graficar
          $('#btn-exportar-csv-top, #btn-exportar-csv-bottom, #btn-exportar-pdf-top, #btn-exportar-pdf-bottom, #btn-graficar').removeClass('d-none');
        } else {
          $('#resultado').html('<div class="alert alert-warning">Sin resultados.</div>');
        }
      } catch(err) {
        console.error(err);
        Swal.fire('Error', 'Error al procesar los datos del reporte.', 'error');
      }
    });
  });

  // ==============================
  // EXPORTAR CSV / PDF
  // ==============================
  $(document).on('click', '#btn-exportar-csv-top, #btn-exportar-csv-bottom', function(e){
    e.preventDefault();
    $('#form-exportar-csv').submit();
  });

  $(document).on('click', '#btn-exportar-pdf-top, #btn-exportar-pdf-bottom', function(e){
    e.preventDefault();
    $('#form-exportar-pdf').submit();
  });

  // ==============================
  // ABRIR MODAL DE GRÁFICO
  // ==============================
  $(document).on('click', '#btn-graficar', function() {
    if (!reporteData || reporteData.length === 0) {
      Swal.fire('Atención', 'Primero ejecuta un reporte con resultados.', 'info');
      return;
    }

    // Cargar campos dinámicamente en el modal
    const keys = Object.keys(reporteData[0]);
    let html = '';
    keys.forEach((k, i) => {
      html += `
        <tr>
          <td>${i + 1}</td>
          <td>${k}</td>
          <td class="text-center"><input type="checkbox" class="chk-campo" value="${k}"></td>
        </tr>`;
    });
    $('#tabla-campos tbody').html(html);
    $('#modalGrafico').modal('show');
  });

  // ==============================
  // GENERAR GRÁFICO DINÁMICO
  // ==============================
  $('#btn-generar-grafico').on('click', function() {
    const tipoGrafico = $('#tipoGrafico').val();
    const mostrarLeyenda = $('#mostrarLeyenda').is(':checked');
    const campos = $('.chk-campo:checked').map(function(){ return this.value; }).get();

    if (campos.length === 0) {
      Swal.fire('Atención', 'Selecciona al menos un campo para graficar.', 'info');
      return;
    }

    // Determinar campo de etiquetas (texto)
    const sample = reporteData[0];
    const campoLabel = Object.keys(sample).find(k => typeof sample[k] === 'string') || Object.keys(sample)[0];
    const colores = ['#007bff','#00b894','#ffc107','#6c757d','#dc3545','#6610f2','#20c997'];

    const datasets = campos.map((c, i) => ({
      label: c,
      data: reporteData.map(r => parseFloat(r[c]) || 0),
      backgroundColor: tipoGrafico === 'line' ? 'transparent' : colores[i % colores.length],
      borderColor: colores[i % colores.length],
      borderWidth: 2,
      fill: tipoGrafico !== 'line',
      tension: 0.3
    }));

    if (chartInstance) chartInstance.destroy();

    const ctx = document.getElementById('graficoAuto').getContext('2d');
    chartInstance = new Chart(ctx, {
      type: tipoGrafico,
      data: {
        labels: reporteData.map(r => {
        let v = r[campoLabel];
        if (/^\d{10}$/.test(v)) {
            const fecha = new Date(v * 1000);
            v = fecha.toLocaleDateString('es-MX', {
            year: '2-digit', month: '2-digit', day: '2-digit'
            });
        }
        return v;
        }),
        datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: mostrarLeyenda, position: 'bottom' },
          tooltip: { mode: 'index', intersect: false }
        },
        scales: {
          y: { beginAtZero: true, grid: { color: '#e9ecef' } },
          x: { grid: { color: '#f8f9fa' } }
        }
      }
    });

    $('#modalGrafico').modal('hide');
    $('#contenedor-grafico').removeClass('d-none');
    $('html, body').animate({ scrollTop: $('#contenedor-grafico').offset().top - 50 }, 400);
  });

});
</script>
