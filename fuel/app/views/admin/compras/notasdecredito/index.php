<!-- CONTENT -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Notas de Crédito</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo Html::anchor('admin/compras/notasdecredito', 'Notas de Crédito'); ?>
              </li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/compras/notasdecredito/agregar', '<i class="fas fa-plus"></i> Subir Nota de Crédito', ['class'=>'btn btn-sm btn-neutral']); ?>
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
          <h3 class="mb-0">Listado de Notas de Crédito</h3>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-items-center">
            <thead class="thead-light">
              <tr>
                <th>Proveedor</th>
                <th>UUID</th>
                <th>Total</th>
                <th>Estatus</th>
                <th>Fecha</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($notas)): ?>
                <?php foreach ($notas as $nota): ?>
                  <tr>
                    <td><?php echo $nota['provider']; ?></td>
                    <td><?php echo $nota['uuid']; ?></td>
                    <td><?php echo $nota['total']; ?></td>
                    <td><?php echo $nota['status']; ?></td>
                    <td><?php echo $nota['created_at']; ?></td>
                    <td class="text-center">
                      <?php echo Html::anchor('admin/compras/notasdecredito/info/'.$nota['id'], '<i class="fas fa-eye"></i>', ['class'=>'btn btn-sm btn-info','title'=>'Ver']); ?>
                      <?php echo Html::anchor('admin/compras/notasdecredito/status/'.$nota['id'].'/aceptar', '<i class="fas fa-check"></i>', ['class'=>'btn btn-sm btn-success','title'=>'Aceptar']); ?>
                      <?php echo Html::anchor('admin/compras/notasdecredito/status/'.$nota['id'].'/rechazar', '<i class="fas fa-times"></i>', ['class'=>'btn btn-sm btn-danger','title'=>'Rechazar']); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle text-primary fa-lg mb-2"></i><br>
                    No hay notas de crédito registradas.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if (!empty($pagination)): ?>
          <div class="card-footer py-4">
            <?php echo $pagination; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>
