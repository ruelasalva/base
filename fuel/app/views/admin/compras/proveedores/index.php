<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-8 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Compras</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Reporte por Proveedor
              </li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-4 col-5 text-right">
          <?php echo Html::anchor('admin/compras', '<i class="fas fa-arrow-left"></i> Regresar', ['class' => 'btn btn-neutral']); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <div class="card-header border-0">
          <?php echo Form::open(['action' => 'admin/compras/proveedores/buscar', 'method' => 'post']); ?>
          <div class="form-row">
            <div class="col-md-8">
              <h3 class="mb-0">Proveedores registrados</h3>
            </div>
            <div class="col-md-4 mb-0">
              <div class="input-group input-group-sm mt-3 mt-md-0">
                <?php echo Form::input('search', (isset($search) ? $search : ''), [
                    'id' => 'search',
                    'class' => 'form-control',
                    'placeholder' => 'Nombre o RFC del proveedor',
                    'aria-describedby' => 'button-addon'
                ]); ?>
                <div class="input-group-append">
                  <?php echo Form::submit([
                      'value' => 'Buscar',
                      'name' => 'submit',
                      'id' => 'button-addon',
                      'class' => 'btn btn-outline-primary'
                  ]); ?>
                </div>
              </div>
            </div>
          </div>
          <?php echo Form::close(); ?>
        </div>

        <!-- TABLA -->
        <div class="table-responsive">
          <table class="table align-items-center table-flush table-hover">
            <thead class="thead-light">
              <tr>
                <th>Proveedor</th>
                <th>RFC</th>
                <th class="text-center">Órdenes</th>
                <th class="text-center">Facturas</th>
                <th class="text-center">Contrarecibos</th>
                <th class="text-center">REP</th>
                <th class="text-center">Notas Crédito</th>
                <th class="text-center">Saldo Pendiente</th>
                <th class="text-center">Última Actividad</th>
                <th class="text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($providers)): ?>
                <?php foreach ($providers as $prov): ?>
                  <tr>
                    <td class="font-weight-bold">
                      <?php echo Html::anchor('admin/compras/proveedores/info/'.$prov['id'], $prov['company_name']); ?>
                    </td>
                    <td><?php echo $prov['rfc']; ?></td>
                    <td class="text-center">
                      <span class="badge badge-primary">
                        <?php echo $prov['ordenes_count']; ?>
                      </span><br>
                      <small class="text-muted">$<?php echo number_format($prov['ordenes_monto'], 2); ?></small>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-info">
                        <?php echo $prov['facturas_count']; ?>
                      </span><br>
                      <small class="text-muted">$<?php echo number_format($prov['total_facturado'], 2); ?></small>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-success">
                        <?php echo $prov['contrarecibos_count']; ?>
                      </span><br>
                      <small class="text-muted">$<?php echo number_format($prov['contrarecibos_monto'], 2); ?></small>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-warning">
                        <?php echo $prov['reps_count']; ?>
                      </span><br>
                      <small class="text-muted">$<?php echo number_format($prov['reps_monto'], 2); ?></small>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-secondary">
                        <?php echo $prov['notas_count']; ?>
                      </span><br>
                      <small class="text-muted">$<?php echo number_format($prov['notas_monto'], 2); ?></small>
                    </td>
                    <td class="text-center">
                      <?php
                        $saldo = $prov['saldo_pendiente_general'];
                        $badge = ($saldo > 0) ? 'badge-danger' : 'badge-success';
                      ?>
                      <span class="badge <?php echo $badge; ?>">
                        $<?php echo number_format($saldo, 2); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-info">
                        <?php echo $prov['ultima_actividad']; ?>
                      </span>
                    </td>
                    <td class="text-right">
                      <?php echo Html::anchor('admin/compras/proveedores/info/'.$prov['id'], '<i class="fas fa-eye"></i> Ver', ['class' => 'btn btn-sm btn-info']); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="10" class="text-center text-muted">No se encontraron proveedores.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- PAGINACIÓN -->
        <?php if (isset($pagination) && $pagination != ''): ?>
          <div class="card-footer py-4">
            <?php echo $pagination; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
