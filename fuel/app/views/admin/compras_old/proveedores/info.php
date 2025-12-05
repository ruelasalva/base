<!-- ENCABEZADO VISUAL ESTILO SAJOR -->
<div class="header bg-primary pb-5 shadow-sm">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-3">
        <div class="col-lg-8 col-7">
          <h4 class="text-white mb-1 font-weight-bold">
            Proveedor: <?php echo $provider->name; ?>
            <?php if (!empty($provider->rfc)): ?>
              <span class="text-white-50 small font-weight-normal">(<?php echo $provider->rfc; ?>)</span>
            <?php endif; ?>
          </h4>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block mt-1">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark mb-0">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/compras/proveedores', 'Por Proveedor'); ?></li>
              <li class="breadcrumb-item active" aria-current="page"><?php echo $provider->name; ?></li>
            </ol>
          </nav>
        </div>

        <!-- Botones de acción -->
        <div class="col-lg-4 col-5 text-right">
          <div class="btn-group" role="group" aria-label="Acciones proveedor">
            <a href="<?php echo Uri::create('admin/compras/facturas/agregar/'.$provider->id); ?>" class="btn btn-light btn-sm shadow-sm px-3 hover-rise">
              <i class="fas fa-file-upload text-primary"></i> <span class="d-none d-md-inline">Subir Factura</span>
            </a>
            <a href="<?php echo Uri::create('admin/compras/contrarecibos/agregar/'.$provider->id); ?>" class="btn btn-light btn-sm shadow-sm px-3 ml-1 hover-rise">
              <i class="fas fa-file-invoice text-info"></i> <span class="d-none d-md-inline">Generar Contrarecibo</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- RESUMEN GENERAL ESTILIZADO -->
  <div class="container-fluid mt-3">
    <div class="row">
      <div class="col">
        <div class="card border-0 shadow-sm" style="border-radius: 10px;">
          <div class="card-body py-3">
            <div class="row text-center">
              <div class="col-md-3 mb-3 mb-md-0">
                <div class="resume-box bg-primary bg-opacity-10">
                  <i class="fas fa-file-invoice text-primary mb-2 fa-lg"></i>
                  <div class="resume-label small text-muted">Total Órdenes</div>
                  <div class="resume-value text-primary font-weight-bold h5 mb-0">$<?php echo number_format($ordenes_monto, 2); ?></div>
                </div>
              </div>
              <div class="col-md-3 mb-3 mb-md-0">
                <div class="resume-box bg-success bg-opacity-10">
                  <i class="fas fa-file-alt text-success mb-2 fa-lg"></i>
                  <div class="resume-label small text-muted">Total Facturado</div>
                  <div class="resume-value text-success font-weight-bold h5 mb-0">$<?php echo number_format($facturas_monto, 2); ?></div>
                </div>
              </div>
              <div class="col-md-3 mb-3 mb-md-0">
                <div class="resume-box bg-info bg-opacity-10">
                  <i class="fas fa-receipt text-info mb-2 fa-lg"></i>
                  <div class="resume-label small text-muted">Total Pagado</div>
                  <div class="resume-value text-info font-weight-bold h5 mb-0">$<?php echo number_format($contrarecibos_monto, 2); ?></div>
                </div>
              </div>
              <div class="col-md-3 mb-3 mb-md-0">
                <div class="resume-box bg-danger bg-opacity-10">
                  <i class="fas fa-balance-scale text-danger mb-2 fa-lg"></i>
                  <div class="resume-label small text-muted">Saldo Pendiente</div>
                  <div class="resume-value text-danger font-weight-bold h5 mb-0">$<?php echo number_format($saldo_pendiente, 2); ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- RESUMEN Y TOTALES -->
<div class="bg-white shadow-sm border-bottom py-3 mb-2">
  <div class="container-fluid">
    <div class="row text-center text-lg-left">
      <div class="col-md-2 mb-2">
        <span class="font-weight-bold text-dark">Órdenes:</span>
        <span class="text-primary font-weight-bold"><?php echo $ordenes_count; ?></span>
        <span class="text-muted small">($<?php echo number_format($ordenes_monto,2); ?>)</span>
      </div>
      <div class="col-md-2 mb-2">
        <span class="font-weight-bold text-dark">Facturas:</span>
        <span class="text-success font-weight-bold"><?php echo $facturas_count; ?></span>
        <span class="text-muted small">($<?php echo number_format($facturas_monto,2); ?>)</span>
      </div>
      <div class="col-md-2 mb-2">
        <span class="font-weight-bold text-dark">Contrarecibos:</span>
        <span class="text-info font-weight-bold"><?php echo $contrarecibos_count; ?></span>
        <span class="text-muted small">($<?php echo number_format($contrarecibos_monto,2); ?>)</span>
      </div>
      <div class="col-md-2 mb-2">
        <span class="font-weight-bold text-dark">REPs:</span>
        <span class="text-warning font-weight-bold"><?php echo $reps_count; ?></span>
        <span class="text-muted small">($<?php echo number_format($reps_monto,2); ?>)</span>
      </div>
      <div class="col-md-2 mb-2">
        <span class="font-weight-bold text-dark">Notas Crédito:</span>
        <span class="text-secondary font-weight-bold"><?php echo $notas_count; ?></span>
        <span class="text-muted small">($<?php echo number_format($notas_monto,2); ?>)</span>
      </div>
      <div class="col-md-2">
        <span class="font-weight-bold text-dark">Saldo Pendiente:</span>
        <span class="text-danger font-weight-bold">$<?php echo number_format($saldo_pendiente,2); ?></span>
      </div>
    </div>
  </div>
</div>

<!-- TIMELINE -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card mb-4 shadow">
        <div class="card-header bg-light">
          <h3 class="mb-0 text-primary">Histórico de Movimientos</h3>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-sm table-bordered mb-0">
            <thead class="thead-light">
              <tr>
                <th>Tipo</th>
                <th>Referencia</th>
                <th>Fecha</th>
                <th>Estatus</th>
                <th class="text-right">Monto</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($timeline as $mov): ?>
                <tr>
                  <td>
                    <?php
                      $icon = '';
                      switch ($mov['tipo']) {
                        case 'Orden de Compra': $icon = '<i class="fas fa-file-invoice text-primary"></i>'; break;
                        case 'Factura': $icon = '<i class="fas fa-file-alt text-success"></i>'; break;
                        case 'Contrarecibo': $icon = '<i class="fas fa-receipt text-info"></i>'; break;
                        case 'REP': $icon = '<i class="fas fa-money-check text-warning"></i>'; break;
                        case 'Nota de Crédito': $icon = '<i class="fas fa-file-invoice-dollar text-secondary"></i>'; break;
                      }
                      echo $icon.' '.$mov['tipo'];
                    ?>
                  </td>
                  <td><?php echo $mov['ref']; ?></td>
                  <td><?php echo !empty($mov['fecha']) ? date('d/m/Y', strtotime($mov['fecha'])) : '---'; ?></td>
                  <td><span class="badge badge-<?php echo $mov['badge']; ?>"><?php echo $mov['status_label']; ?></span></td>
                  <td class="text-right font-weight-bold">$<?php echo number_format($mov['monto'],2); ?></td>
                  <td>
                    <?php if($mov['ruta']=='orden'): ?>
                      <a href="<?php echo Uri::create('admin/compras/ordenes/info/'.$mov['doc_id']); ?>" class="btn btn-info btn-sm">Ver OC</a>
                    <?php elseif($mov['ruta']=='factura'): ?>
                      <a href="<?php echo Uri::create('admin/compras/facturas/info/'.$mov['doc_id']); ?>" class="btn btn-success btn-sm">Ver Factura</a>
                    <?php elseif($mov['ruta']=='contrarecibo'): ?>
                      <a href="<?php echo Uri::create('admin/compras/contrarecibos/info/'.$mov['doc_id']); ?>" class="btn btn-warning btn-sm">Ver CR</a>
                    <?php elseif($mov['ruta']=='rep'): ?>
                      <a href="<?php echo Uri::create('admin/compras/rep/info/'.$mov['doc_id']); ?>" class="btn btn-warning btn-sm">Ver REP</a>
                    <?php elseif($mov['ruta']=='nota'): ?>
                      <a href="<?php echo Uri::create('admin/compras/notas/info/'.$mov['doc_id']); ?>" class="btn btn-secondary btn-sm">Ver Nota</a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($timeline)): ?>
                <tr><td colspan="6" class="text-center">Sin movimientos registrados.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TABS DETALLE -->
      <div class="card mb-4 shadow">
        <div class="card-header bg-light">
          <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-ocs">Órdenes</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-facturas">Facturas</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-contrarecibos">Contrarecibos</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-reps">REPs</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-notas">Notas de Crédito</a></li>
          </ul>
        </div>

        <div class="card-body tab-content">
          <!-- ÓRDENES -->
          <div class="tab-pane fade show active" id="tab-ocs">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Estatus</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($ordenes as $oc): ?>
                  <tr>
                    <td><?php echo $oc->code_order; ?></td>
                    <td><?php echo !empty($oc->date_order) ? date('d/m/Y', is_numeric($oc->date_order)?$oc->date_order:strtotime($oc->date_order)) : '---'; ?></td>
                    <td>$<?php echo number_format($oc->total,2); ?></td>
                    <td><span class="badge badge-<?php echo Helper_Purchases::badge_class('order',$oc->status); ?>"><?php echo Helper_Purchases::label('order',$oc->status); ?></span></td>
                    <td><a href="<?php echo Uri::create('admin/compras/ordenes/info/'.$oc->id); ?>" class="btn btn-info btn-xs">Ver</a></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($ordenes)): ?>
                  <tr><td colspan="5" class="text-center">Sin órdenes de compra registradas.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- FACTURAS -->
          <div class="tab-pane fade" id="tab-facturas">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>UUID</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Estatus</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($facturas as $f): ?>
                  <tr>
                    <td><?php echo $f->uuid; ?></td>
                    <td><?php echo !empty($f->created_at) ? date('d/m/Y', is_numeric($f->created_at)?$f->created_at:strtotime($f->created_at)) : '---'; ?></td>
                    <td>$<?php echo number_format($f->total,2); ?></td>
                    <td><span class="badge badge-<?php echo Helper_Purchases::badge_class('bill',$f->status); ?>"><?php echo Helper_Purchases::label('bill',$f->status); ?></span></td>
                    <td><a href="<?php echo Uri::create('admin/compras/facturas/info/'.$f->id); ?>" class="btn btn-success btn-xs">Ver</a></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($facturas)): ?>
                  <tr><td colspan="5" class="text-center">Sin facturas registradas.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- CONTRARECIBOS -->
          <div class="tab-pane fade" id="tab-contrarecibos">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>Folio</th>
                  <th>Fecha de Recibo</th>
                  <th>Fecha Estimada de Pago</th>
                  <th>Total</th>
                  <th>Estatus</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($contrarecibos as $cr): ?>
                  <tr>
                    <td><?php echo $cr->receipt_number; ?></td>
                    <td><?php echo !empty($cr->receipt_date) ? date('d/m/Y', is_numeric($cr->receipt_date)?$cr->receipt_date:strtotime($cr->receipt_date)) : '---'; ?></td>
                    <td><?php echo !empty($cr->payment_date) ? date('d/m/Y', is_numeric($cr->payment_date)?$cr->payment_date:strtotime($cr->payment_date)) : '---'; ?></td>
                    <td>$<?php echo number_format($cr->total,2); ?></td>
                    <td><span class="badge badge-<?php echo Helper_Purchases::badge_class('receipt',$cr->status); ?>"><?php echo Helper_Purchases::label('receipt',$cr->status); ?></span></td>
                    <td><a href="<?php echo Uri::create('admin/compras/contrarecibos/info/'.$cr->id); ?>" class="btn btn-warning btn-xs">Ver</a></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($contrarecibos)): ?>
                  <tr><td colspan="6" class="text-center">Sin pagos/contrarecibos registrados.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- REPs -->
          <div class="tab-pane fade" id="tab-reps">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>UUID REP</th>
                  <th>Fecha Pago</th>
                  <th>Monto Pagado</th>
                  <th>Estatus</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($reps as $rep): ?>
                  <tr>
                    <td><?php echo $rep->uuid; ?></td>
                    <td><?php echo !empty($rep->payment_date) ? date('d/m/Y', strtotime($rep->payment_date)) : '---'; ?></td>
                    <td>$<?php echo number_format($rep->amount_paid,2); ?></td>
                    <td><span class="badge badge-<?php echo Helper_Purchases::badge_class('rep',$rep->status); ?>"><?php echo Helper_Purchases::label('rep',$rep->status); ?></span></td>
                    <td><a href="<?php echo Uri::create('admin/compras/rep/info/'.$rep->id); ?>" class="btn btn-warning btn-xs">Ver</a></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($reps)): ?>
                  <tr><td colspan="5" class="text-center">Sin REPs registrados.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- NOTAS DE CRÉDITO -->
          <div class="tab-pane fade" id="tab-notas">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>UUID</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Estatus</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($notas as $n): ?>
                  <tr>
                    <td><?php echo $n->uuid; ?></td>
                    <td><?php echo !empty($n->created_at) ? date('d/m/Y', is_numeric($n->created_at)?$n->created_at:strtotime($n->created_at)) : '---'; ?></td>
                    <td>$<?php echo number_format($n->total,2); ?></td>
                    <td><span class="badge badge-<?php echo Helper_Purchases::badge_class('cn',$n->status); ?>"><?php echo Helper_Purchases::label('cn',$n->status); ?></span></td>
                    <td><a href="<?php echo Uri::create('admin/compras/notas/info/'.$n->id); ?>" class="btn btn-secondary btn-xs">Ver</a></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($notas)): ?>
                  <tr><td colspan="5" class="text-center">Sin notas de crédito registradas.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
