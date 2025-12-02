<!-- ENCABEZADO --> 
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Gestión de Footer</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Footer</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/apariencia/footer/agregar', '<i class="fas fa-plus"></i> Agregar Footer', ['class'=>'btn btn-sm btn-warning']); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card">

        <div class="card-header border-0">
          <h3 class="mb-0">Configuración actual</h3>
        </div>

        <div class="table-responsive">
          <table class="table align-items-center table-hover">
            <thead class="thead-light">
              <tr>
                <th>Logo principal</th>
                <th>Logo secundario</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Horario</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($footers): ?>
                <?php foreach ($footers as $footer): ?>
                  <tr>
                    <td>
                      <?php echo $footer->logo_main
                        ? Html::img(Uri::base(false).'assets/'.$footer->logo_main, [
                            'alt'   => 'Logo principal',
                            'style' => 'max-height:40px;'
                          ])
                        : '<span class="text-muted">No definido</span>'; ?>
                    </td>

                    <td>
                      <?php echo $footer->logo_secondary
                        ? Html::img(Uri::base(false).'assets/'.$footer->logo_secondary, [
                            'alt'   => 'Logo secundario',
                            'style' => 'max-height:40px;'
                          ])
                        : '<span class="text-muted">No definido</span>'; ?>
                    </td>

                    <td><?php echo Str::truncate($footer->address, 40); ?></td>
                    <td><?php echo e($footer->phone); ?></td>
                    <td><?php echo e($footer->email); ?></td>
                    <td>
                      <?php echo $footer->office_hours_week; ?><br>
                      <?php echo $footer->office_hours_weekend; ?>
                    </td>
                    <td>
                      <?php if ($footer->status == 1): ?>
                        <span class="badge badge-success">Activo</span>
                      <?php else: ?>
                        <span class="badge badge-secondary">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-right">
                      <?php echo Html::anchor('admin/apariencia/footer/info/'.$footer->id, '<i class="fas fa-eye"></i>', ['class'=>'btn btn-sm btn-info', 'title'=>'Previsualizar']); ?>
                      <?php echo Html::anchor('admin/apariencia/footer/editar/'.$footer->id, '<i class="fas fa-edit"></i>', ['class'=>'btn btn-sm btn-warning', 'title'=>'Editar']); ?>

                      <?php if ($footer->status == 0): ?>
                        <?php echo Html::anchor('admin/apariencia/footer/activar/'.$footer->id, '<i class="fas fa-check"></i>', [
                          'class'=>'btn btn-sm btn-success',
                          'title'=>'Activar',
                          'onclick'=>"return confirm('¿Seguro que deseas activar este footer? Se desactivarán los demás.');"
                        ]); ?>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">No hay configuración registrada.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
