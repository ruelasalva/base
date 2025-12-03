<!-- CONTENT -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Detalle Nota de Crédito</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/compras/notasdecredito', 'Notas de Crédito'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Detalle</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/compras/notasdecredito/status/'.$nota->id.'/aceptar', 'Aceptar', ['class'=>'btn btn-sm btn-success']); ?>
          <?php echo Html::anchor('admin/compras/notasdecredito/status/'.$nota->id.'/rechazar', 'Rechazar', ['class'=>'btn btn-sm btn-danger']); ?>
          <?php echo Html::anchor('admin/compras/notasdecredito/status/'.$nota->id.'/aplicar', 'Aplicar', ['class'=>'btn btn-sm btn-primary']); ?>
          <?php echo Html::anchor('admin/compras/notasdecredito/status/'.$nota->id.'/desaplicar', 'Desaplicar', ['class'=>'btn btn-sm btn-warning']); ?>
          <?php echo Html::anchor('admin/compras/notasdecredito/status/'.$nota->id.'/cancelar', 'Cancelar', ['class'=>'btn btn-sm btn-dark']); ?>
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
          <h3 class="mb-0">Información de la Nota</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <?php echo Form::label('Proveedor','provider'); ?>
              <span class="form-control"><?php echo $nota->provider->company_name ?? '-'; ?></span>
            </div>
            <div class="col-md-6 mb-3">
              <?php echo Form::label('UUID','uuid'); ?>
              <span class="form-control"><?php echo $nota->uuid; ?></span>
            </div>
            <div class="col-md-6 mb-3">
              <?php echo Form::label('Serie','serie'); ?>
              <span class="form-control"><?php echo $nota->serie; ?></span>
            </div>
            <div class="col-md-6 mb-3">
              <?php echo Form::label('Folio','folio'); ?>
              <span class="form-control"><?php echo $nota->folio; ?></span>
            </div>
            <div class="col-md-6 mb-3">
              <?php echo Form::label('Total','total'); ?>
              <span class="form-control">$<?php echo number_format($nota->total,2); ?></span>
            </div>
            <div class="col-md-6 mb-3">
              <?php echo Form::label('Estatus','status'); ?>
              <span class="form-control"><?php echo Helper_Status::creditnote($nota->status); ?></span>
            </div>
            <div class="col-md-12 mb-3">
              <?php echo Form::label('Observaciones','observations'); ?>
              <span class="form-control"><?php echo $nota->observations; ?></span>
            </div>
            <div class="col-md-12 mb-3">
              <?php echo Form::label('Archivos','files'); ?><br>
              <?php echo Html::anchor('uploads/xml/'.$nota->xml_file,'XML',['target'=>'_blank','class'=>'btn btn-outline-primary btn-sm']); ?>
              <?php if ($nota->pdf_file): ?>
                <?php echo Html::anchor('uploads/pdf/'.$nota->pdf_file,'PDF',['target'=>'_blank','class'=>'btn btn-outline-danger btn-sm']); ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <?php if ($relaciones): ?>
      <div class="card mt-4">
        <div class="card-header border-0">
          <h3 class="mb-0">Documentos Relacionados</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="thead-light">
              <tr>
                <th>Tipo</th>
                <th>UUID</th>
                <th>Total</th>
                <th>Importe Aplicado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($relaciones as $rel): ?>
              <tr>
                <td>Factura</td>
                <td><?php echo $rel->bill->uuid; ?></td>
                <td>$<?php echo number_format($rel->bill->total,2); ?></td>
                <td>$<?php echo number_format($rel->amount,2); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
