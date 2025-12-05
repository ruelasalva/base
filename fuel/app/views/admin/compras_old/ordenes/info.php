<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Compras</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/compras/ordenes', 'Órdenes de Compra'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Detalle OC #<?php echo $code_order; ?>
              </li>
            </ol>
          </nav>
        </div>

        <div class="col-lg-6 col-5 text-right">
          <?php if (!empty($puede_autorizar)): ?>
            <?php echo Html::anchor('admin/compras/ordenes/autorizar/'.$id, 'Revisar y Autorizar', ['class'=>'btn btn-sm btn-success']); ?>
          <?php endif; ?>

          <?php if (empty($orden_completa)): ?>
            <?php echo Html::anchor('admin/compras/ordenes/asociar/'.$id, 'Asociar Factura', ['class'=>'btn btn-sm btn-info']); ?>
            <?php echo Html::anchor('admin/compras/facturas/subir_factura/'.$id, 'Subir Factura', ['class'=>'btn btn-sm btn-neutral']); ?>
          <?php endif; ?>

          <?php if (!empty($puede_contrarecibo)): ?>
            <?php echo Html::anchor('admin/compras/contrarecibos/crear/'.$id, 'Generar Contrarecibo', ['class'=>'btn btn-sm btn-success']); ?>
          <?php endif; ?>
          
          <?php if (!empty($puede_editar)): ?>
            <?php echo Html::anchor('admin/compras/ordenes/editar/'.$id, 'Editar', ['class'=>'btn btn-sm btn-warning']); ?>
          <?php endif; ?>
          <?php echo Html::anchor('admin/compras/ordenes/imprimir/'.$id, '<i class="fas fa-print"></i> Imprimir', ['class'=>'btn btn-sm btn-secondary']); ?>
        </div>
      </div>

      <?php if (!empty($puede_contrarecibo)): ?>
        <div class="row mt-3">
          <div class="col">
            <div class="card bg-white shadow-sm">
              <div class="card-body">
                <?php echo Form::open(['action' => 'admin/compras/contrarecibos/crear/'.$id, 'method' => 'post']); ?>
                <div class="form-row">
                  <div class="col-md-4 mb-2">
                    <label><strong>Fecha de recepción</strong></label>
                    <?php echo Form::input('fecha_recepcion', date('Y-m-d'), [
                      'type' => 'date',
                      'class'=> 'form-control',
                      'required'
                    ]); ?>
                  </div>
                  <div class="col-md-4 mb-2">
                    <label><strong>Días de crédito</strong></label>
                    <?php echo Form::input('dias_credito', $credit_days ?: 0, [
                      'type' => 'number',
                      'class'=> 'form-control',
                      'min'=>0
                    ]); ?>
                  </div>
                  <div class="col-md-4 mb-2">
                    <label><strong>Pago estimado</strong></label>
                    <div class="form-control bg-light"><?php echo $tentative_payment; ?></div>
                  </div>
                </div>
                <div class="text-right">
                  <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-invoice-dollar"></i> Generar Contrarecibo
                  </button>
                </div>
                <?php echo Form::close(); ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($tiene_contrarecibo)): ?>
        <div class="alert alert-warning shadow-sm mx-3 mt-2">
          <i class="fas fa-exclamation-triangle"></i>
          <strong>Atención:</strong>
          Esta orden ya está incluida en un 
          <?php if (!empty($tiene_contrarecibo_dir)): ?>
            <b>contrarecibo directo</b>.
          <?php else: ?>
            <b>contrarecibo agrupado</b> (junto con otras órdenes del mismo proveedor).
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <div class="card-header">
          <h3 class="mb-0">
            Orden de Compra <span class="badge badge-primary">#<?php echo $code_order; ?></span>
          </h3>
          <div class="small text-muted">
            Creada: <?php echo date('d/m/Y H:i', $created_at); ?> |
            Última actualización: <?php echo $updated_at ? date('d/m/Y H:i', $updated_at) : '-'; ?>
            <?php if (isset($deleted) && $deleted): ?> | <span class="text-danger">Registro eliminado</span><?php endif; ?>
          </div>
        </div>

        <div class="card-body">
          <!-- INFORMACIÓN GENERAL -->
          <!-- INFORMACIÓN GENERAL -->
<fieldset>
  <legend class="heading">Información de la Orden</legend>
  <div class="form-row">
    <div class="col-md-6 mb-3">
      <label><strong>Proveedor</strong></label>
      <div class="form-control bg-light">
        <span class="d-block font-weight-bold"><?php echo $provider; ?></span>
        <small class="text-muted"><?php echo $rfc; ?></small>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Fecha para compra</strong></label>
      <div class="form-control bg-light">
        <?php echo date('d/m/Y', is_numeric($date_order) ? $date_order : strtotime($date_order)); ?>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Moneda</strong></label>
      <div class="form-control bg-light"><?php echo $currency; ?></div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Estatus</strong></label>
      <div class="form-control bg-light"><?php echo $status; ?></div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Origen</strong></label>
      <div class="form-control bg-light"><?php echo $origin; ?></div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Total OC</strong></label>
      <div class="form-control bg-light font-weight-bold text-success">
        $<?php echo number_format($total, 2); ?>
      </div>
    </div>

    <!-- NUEVOS CAMPOS -->
    <div class="col-md-3 mb-3">
      <label><strong>Total Facturado</strong></label>
      <div class="form-control bg-light text-info font-weight-bold">
        <?php echo isset($invoiced_total) && $invoiced_total > 0
          ? '$' . number_format($invoiced_total, 2)
          : '---'; ?>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Saldo Pendiente</strong></label>
      <div class="form-control bg-light text-danger font-weight-bold">
        <?php echo isset($balance_total) && $balance_total > 0
          ? '$' . number_format($balance_total, 2)
          : '---'; ?>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Autorizado por</strong></label>
      <div class="form-control bg-light">
        <?php echo !empty($authorized_by_name)
          ? $authorized_by_name
          : '---'; ?>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <label><strong>Fecha de autorización</strong></label>
      <div class="form-control bg-light">
        <?php echo !empty($authorized_at) ? date('d/m/Y H:i', strtotime($authorized_at)): '---'; ?>
      </div>
    </div>
    <!-- FIN NUEVOS CAMPOS -->

    <div class="col-md-12 mb-3">
      <label><strong>Notas</strong></label>
      <div class="form-control bg-light"><?php echo $notes ?: '---'; ?></div>
    </div>
  </div>
</fieldset>


          <!-- DETALLES -->
          <hr>
          <fieldset>
            <legend class="heading">Artículos / Servicios</legend>
            <div class="table-responsive">
              <table class="table align-items-center table-flush table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cuenta Contable</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Retención</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sum_subtotal  = 0;
                  $sum_iva       = 0;
                  $sum_retencion = 0;
                  $sum_total     = 0;
                  foreach($detalles as $d):
                    $sum_subtotal  += $d['subtotal'];
                    $sum_iva       += $d['iva'];
                    $sum_retencion += $d['retencion'];
                    $sum_total     += $d['total'];
                  ?>
                    <tr>
                      <td><?php echo $d['code_product']; ?></td>
                      <td><?php echo $d['description']; ?></td>
                      <td><?php echo !empty($d['account_code']) ? $d['account_code'].' - '.$d['account_name'] : '---'; ?></td>
                      <td><?php echo number_format($d['quantity'], 2); ?></td>
                      <td>$<?php echo number_format($d['unit_price'], 2); ?></td>
                      <td>$<?php echo number_format($d['subtotal'], 2); ?></td>
                      <td>$<?php echo number_format($d['iva'], 2); ?></td>
                      <td>$<?php echo number_format($d['retencion'], 2); ?></td>
                      <td><strong>$<?php echo number_format($d['total'], 2); ?></strong></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (empty($detalles)): ?>
                    <tr><td colspan="9" class="text-center">Sin productos o servicios capturados</td></tr>
                  <?php endif; ?>
                </tbody>
                <tfoot class="bg-light">
                  <tr class="font-weight-bold">
                    <td colspan="5" class="text-right">Totales:</td>
                    <td>$<?php echo number_format($sum_subtotal, 2); ?></td>
                    <td>$<?php echo number_format($sum_iva, 2); ?></td>
                    <td>$<?php echo number_format($sum_retencion, 2); ?></td>
                    <td>$<?php echo number_format($sum_total, 2); ?></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </fieldset>

          <!-- FACTURAS -->
          <?php if (!empty($facturas)): ?>
            <hr>
            <fieldset>
              <legend class="heading">Facturas Asociadas</legend>
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>UUID</th>
                    <th>Total</th>
                    <th>Estatus</th>
                    <th>Fecha</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($facturas as $f): ?>
                  <tr>
                    <td><?php echo $f['uuid']; ?></td>
                    <td>$<?php echo number_format($f['total'],2); ?></td>
                    <td><span class="badge <?php echo $f['badge']; ?>"><?php echo $f['status']; ?></span></td>
                    <td><?php echo $f['created']; ?></td>
                    <td><?php echo Html::anchor('admin/compras/facturas/info/'.$f['id'], 'Ver Detalle', ['class'=>'btn btn-sm btn-info']); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </fieldset>
          <?php endif; ?>

          <!-- CONTRARECIBOS -->
          <?php if (!empty($contrarecibos)): ?>
            <hr>
            <fieldset>
              <legend class="heading">Contrarecibos Generados</legend>
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>Código</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Estatus</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($contrarecibos as $cr): ?>
                  <tr>
                    <td><?php echo $cr['code']; ?></td>
                    <td>$<?php echo number_format($cr['total'], 2); ?></td>
                    <td><?php echo $cr['date']; ?></td>
                    <td><span class="badge <?php echo $cr['badge']; ?>"><?php echo $cr['status']; ?></span></td>
                    <td><?php echo Html::anchor('admin/compras/contrarecibos/info/'.$cr['id'], 'Ver Detalle', ['class'=>'btn btn-sm btn-info']); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </fieldset>
          <?php endif; ?>

          <!-- REPS -->
          <?php if (!empty($reps)): ?>
            <hr>
            <fieldset>
              <legend class="heading">Pagos / REP Asociados</legend>
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>Folio</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($reps as $rep): ?>
                  <tr>
                    <td><?php echo $rep['folio']; ?></td>
                    <td>$<?php echo number_format($rep['amount'], 2); ?></td>
                    <td><?php echo $rep['date']; ?></td>
                    <td><?php echo Html::anchor('admin/compras/reps/info/'.$rep['id'], 'Ver REP', ['class'=>'btn btn-sm btn-info']); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </fieldset>
          <?php endif; ?>

          <!-- NOTAS DE CRÉDITO -->
          <?php if (!empty($creditnotes)): ?>
            <hr>
            <fieldset>
              <legend class="heading">Notas de Crédito Relacionadas</legend>
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>UUID</th>
                    <th>Total</th>
                    <th>Estatus</th>
                    <th>Fecha</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($creditnotes as $cn): ?>
                  <tr>
                    <td><?php echo $cn['uuid']; ?></td>
                    <td>$<?php echo number_format($cn['total'], 2); ?></td>
                    <td><span class="badge <?php echo $cn['badge']; ?>"><?php echo $cn['status']; ?></span></td>
                    <td><?php echo $cn['date']; ?></td>
                    <td><?php echo Html::anchor('admin/compras/notas/info/'.$cn['id'], 'Ver Nota', ['class'=>'btn btn-sm btn-info']); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </fieldset>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
