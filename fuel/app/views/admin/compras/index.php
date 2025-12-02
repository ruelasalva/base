<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Compras - Dashboard</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/compras', 'Compras - Dashboard'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- FILTRO DE MES/AÑO -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <?php echo Form::open(['action' => 'admin/compras', 'method' => 'get', 'class' => 'form-inline']); ?>
                    <div class="input-group input-group-sm">
                        <?php
                        $meses = [
                            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                        ];
                        echo Form::select('mes', Input::get('mes', date('m')), $meses, ['class' => 'form-control']);
                        ?>
                        <span class="mx-2">/</span>
                        <?php
                        $years = [];
                        for ($y = date('Y'); $y >= 2023; $y--) $years[$y] = $y;
                        echo Form::select('anio', Input::get('anio', date('Y')), $years, ['class' => 'form-control']);
                        ?>
                        <div class="input-group-append ml-2">
                        <?php echo Form::submit('filtrar', 'Filtrar', ['class' => 'btn btn-sm btn-primary']); ?>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">

    <!-- KPIs FILA 1 -->
    <div class="row">
        <!-- Órdenes sin Factura -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Órdenes sin Factura</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $ordenes_sin_factura; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                <i class="fas fa-folder-open"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">Órdenes abiertas sin factura asociada.</p>
                </div>
            </div>
        </div>

        <!-- Facturas sin Orden -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Facturas sin Orden</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $facturas_sin_orden; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="fas fa-upload"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">Facturas cargadas sin relación a orden.</p>
                </div>
            </div>
        </div>

        <!-- Órdenes Abiertas -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Órdenes Abiertas</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $ordenes_abiertas; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                <i class="fas fa-tasks"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">Órdenes en proceso o parcialmente recibidas.</p>
                </div>
            </div>
        </div>

        <!-- Facturas en Proceso -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Facturas en Revisión</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $facturas_proceso; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">Pendientes de autorización o validación.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs FILA 2 -->
    <div class="row">
        <!-- Facturas Rechazadas -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Facturas Rechazadas</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $facturas_rechazadas; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">Rechazadas por inconsistencias.</p>
                </div>
            </div>
        </div>

        <!-- Notas de Crédito -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Notas de Crédito</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $notas_credito; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">Notas vigentes o aplicadas a saldo.</p>
                </div>
            </div>
        </div>

        <!-- Pagos Programados -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Pagos Programados</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $pagos_programados; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">REP pendientes por aplicar.</p>
                </div>
            </div>
        </div>

        <!-- Proveedores Activos -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Proveedores Activos</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo $proveedores_activos; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-sm text-muted">Proveedores registrados y habilitados.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- GRÁFICAS -->
    <div id="app">
    <div class="text-right mb-3">
        <button class="btn btn-sm btn-neutral" @click="actualizarDatos">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header"><h3 class="mb-0">Órdenes vs Facturas</h3></div>
                    <div class="card-body">
                        <canvas id="chartOrdenesFacturas"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card shadow">
                    <div class="card-header"><h3 class="mb-0">Facturas por Estatus</h3></div>
                    <div class="card-body">
                        <canvas id="chartFacturasStatus"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card shadow">
                    <div class="card-header"><h3 class="mb-0">Pagos Programados</h3></div>
                    <div class="card-body">
                        <canvas id="chartPagos"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-4">
  <div class="card shadow">
    <div class="card-header"><h3 class="mb-0">Participación de Proveedores</h3></div>
    <div class="card-body">
      <canvas id="chartProveedores"></canvas>
    </div>
  </div>
</div>

        </div>
    </div>

    <!-- LISTAS RÁPIDAS -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header"><h3 class="mb-0"><i class="fas fa-folder-open text-primary"></i> Últimas Órdenes</h3></div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach($ultimas_ordenes as $orden): ?>
                            <li class="list-group-item">
                                <?php echo Html::anchor('admin/compras/ordenes/info/'.$orden->id, $orden->code_order); ?> -
                                <?php echo $orden->provider ? $orden->provider->name : 'N/D'; ?>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($ultimas_ordenes)): ?>
                            <li class="list-group-item text-center text-muted">Sin registros</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header"><h3 class="mb-0"><i class="fas fa-file-invoice text-warning"></i> Últimas Facturas</h3></div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach($ultimas_facturas as $factura): ?>
                            <li class="list-group-item">
                                <?php echo Html::anchor('admin/compras/facturas/info/'.$factura->id, $factura->id); ?> -
                                <?php echo $factura->provider ? $factura->provider->name : 'N/D'; ?>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($ultimas_facturas)): ?>
                            <li class="list-group-item text-center text-muted">Sin registros</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header"><h3 class="mb-0"><i class="fas fa-dollar-sign text-secondary"></i> Últimos Pagos</h3></div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach($ultimos_pagos as $pago): ?>
                            <li class="list-group-item">
                                <?php echo $pago['proveedor'] ?? 'N/D'; ?> - <?php echo $pago['fecha'] ?? ''; ?>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($ultimos_pagos)): ?>
                            <li class="list-group-item text-center text-muted">Sin registros</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTRARECIBOS Y REP -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header"><h3 class="mb-0"><i class="fas fa-clipboard-check text-primary"></i> Últimos Contra-recibos</h3></div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach($ultimos_contrarecibos as $cr): ?>
                            <li class="list-group-item"><?php echo $cr['code'] ?? 'N/D'; ?> - <?php echo $cr['fecha'] ?? ''; ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($ultimos_contrarecibos)): ?>
                            <li class="list-group-item text-center text-muted">Sin registros</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header"><h3 class="mb-0"><i class="fas fa-receipt text-info"></i> Últimos REP Emitidos</h3></div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach($ultimos_rep as $rep): ?>
                            <li class="list-group-item"><?php echo $rep['code'] ?? 'N/D'; ?> - <?php echo $rep['fecha'] ?? ''; ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($ultimos_rep)): ?>
                            <li class="list-group-item text-center text-muted">Sin registros</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTAS DE CRÉDITO -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header"><h3 class="mb-0"><i class="fas fa-file-invoice-dollar text-success"></i> Últimas Notas de Crédito</h3></div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach($ultimas_notas as $nota): ?>
                            <li class="list-group-item"><?php echo $nota['code'] ?? 'N/D'; ?> - <?php echo $nota['fecha'] ?? ''; ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($ultimas_notas)): ?>
                            <li class="list-group-item text-center text-muted">Sin registros</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- RESUMEN DE CONCLUIDOS -->
<div class="row mt-5">
  <div class="col-lg-12">
    <div class="card shadow">
      <div class="card-header bg-success text-white">
        <h3 class="mb-0"><i class="fas fa-check-circle"></i> Resumen de Concluidos (<?php echo $meses[$mes]; ?> <?php echo $anio; ?>)</h3>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-md-3">
            <h4 class="text-success mb-0"><?php echo $ordenes_concluidas; ?></h4>
            <p class="text-muted mb-0">Órdenes Cerradas</p>
            <div class="progress mt-2">
              <div class="progress-bar bg-success" role="progressbar"
                   style="width: <?php echo $porcentaje_ordenes; ?>%">
                <?php echo $porcentaje_ordenes; ?>%
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <h4 class="text-success mb-0"><?php echo $facturas_pagadas; ?></h4>
            <p class="text-muted mb-0">Facturas Pagadas</p>
            <div class="progress mt-2">
              <div class="progress-bar bg-info" role="progressbar"
                   style="width: <?php echo $porcentaje_facturas; ?>%">
                <?php echo $porcentaje_facturas; ?>%
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <h4 class="text-success mb-0"><?php echo $pagos_realizados; ?></h4>
            <p class="text-muted mb-0">Pagos Efectuados</p>
            <div class="progress mt-2">
              <div class="progress-bar bg-primary" role="progressbar"
                   style="width: 100%"></div>
            </div>
          </div>
          <div class="col-md-3">
            <h4 class="text-success mb-0"><?php echo $notas_credito; ?></h4>
            <p class="text-muted mb-0">Notas Aplicadas</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- TOP PROVEEDORES -->
<div class="row mt-5">
  <div class="col-lg-12">
    <div class="card shadow">
      <div class="card-header bg-info text-white">
        <h3 class="mb-0"><i class="fas fa-chart-line"></i> Top 5 Proveedores del Mes</h3>
      </div>
      <div class="card-body">
        <?php if (!empty($top_proveedores)): ?>
        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th>Proveedor</th>
                <th class="text-center"># Facturas</th>
                <th class="text-right">Monto Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($top_proveedores as $prov): ?>
              <tr>
                <td><strong><?php echo $prov['name'] ?: 'N/D'; ?></strong></td>
                <td class="text-center"><?php echo $prov['facturas']; ?></td>
                <td class="text-right">$<?php echo number_format($prov['total'], 2); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
          <p class="text-muted text-center mb-0">No se registraron facturas este mes.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>



</div>

<script>
    window.dashboardData = JSON.parse('<?php echo addslashes(html_entity_decode($dashboard_data_json)); ?>');
</script>


<?php echo Asset::js('admin/compras/dashboard-vue.js'); ?>