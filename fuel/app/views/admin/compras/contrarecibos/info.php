<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-8 col-md-7">
          <h6 class="h2 text-white d-inline-block mb-0">
            Detalle de Contrarecibo 
            <span class="text-muted">#<?php echo $receipt_number; ?></span>
          </h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/compras/contrarecibos', 'Contrarecibos'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Detalle</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-4 col-md-5 text-right">
          <?php echo Html::anchor('admin/compras/contrarecibos', '<i class="fas fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">

      <!-- DATOS GENERALES -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light font-weight-bold">Datos Generales</div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-3">
              <label><strong>Número Contrarecibo</strong></label>
              <div class="form-control bg-light"><?php echo $receipt_number ?: '---'; ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <label><strong>Proveedor</strong></label>
              <div class="form-control bg-light">
                <span class="d-block font-weight-bold"><?php echo $provider; ?></span>
                <small class="text-muted"><?php echo $rfc; ?></small>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <label><strong>Total Contrarecibo</strong></label>
              <div class="form-control bg-light font-weight-bold text-success">
                $<?php echo $total; ?>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <label><strong>Fecha de Recepción</strong></label>
              <div class="form-control bg-light"><?php echo $receipt_date; ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <label><strong>Fecha Programada de Pago</strong></label>
              <div class="form-control bg-light"><?php echo $payment_date; ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <label><strong>Fecha Real de Pago</strong></label>
              <div class="form-control bg-light"><?php echo $payment_date_real; ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <label><strong>Estatus</strong></label>
              <div class="form-control bg-light">
                <span class="badge badge-<?php echo $badge_color; ?>">
                  <?php echo $status_label; ?>
                </span>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <label><strong>Notas</strong></label>
              <div class="form-control bg-light"><?php echo $notes; ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- FACTURAS ASOCIADAS -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light font-weight-bold">
          <i class="fas fa-file-invoice"></i> Facturas Asociadas
        </div>
        <div class="card-body p-0">
          <?php if (!empty($facturas)): ?>
            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>UUID</th>
                        <th>Orden</th>
                        <th>Fecha Factura</th>
                        <th>Fecha de Carga</th>
                        <th>Estatus</th>
                        <th class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($facturas as $f): ?>
                        <tr>
                        <td><?php echo $f['uuid']; ?></td>
                        <td><?php echo $f['order_code']; ?></td>
                        <td><?php echo $f['fecha_factura']; ?></td>
                        <td><?php echo $f['fecha_carga']; ?></td>
                        <td><span class="badge badge-<?php echo $f['badge']; ?>"><?php echo $f['status']; ?></span></td>
                        <td class="text-right">$<?php echo $f['total']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-secondary m-3">No hay facturas asociadas a este contrarecibo.</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- PRODUCTOS / CONCEPTOS -->
      <?php if (!empty($productos)): ?>
        <div class="card mb-4 shadow-sm">
          <div class="card-header bg-light font-weight-bold">
            <i class="fas fa-boxes"></i> Conceptos / Productos
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-striped table-bordered mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>Descripción</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($productos as $p): ?>
                    <tr>
                      <td><?php echo $p['descripcion']; ?></td>
                      <td class="text-center"><?php echo $p['cantidad']; ?></td>
                      <td class="text-right">$<?php echo $p['precio']; ?></td>
                      <td class="text-right">$<?php echo $p['subtotal']; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- FECHAS DE REGISTRO -->
      <div class="text-muted small mt-3 mb-5">
        <i class="fas fa-clock"></i> Creado: <?php echo $created_at; ?> |
        <i class="fas fa-sync-alt"></i> Actualizado: <?php echo $updated_at; ?>
      </div>

    </div>
  </div>
</div>
