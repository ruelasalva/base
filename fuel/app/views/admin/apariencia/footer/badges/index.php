<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Distintivos del Footer</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?></li>
              <li class="breadcrumb-item active" aria-current="page">Badges</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/apariencia/footer/badges/agregar/'.$footer->id, '<i class="fas fa-plus"></i> Agregar', ['class'=>'btn btn-sm btn-warning']); ?>
          <?php echo Html::anchor('admin/apariencia/footer/info/'.$footer->id, '<i class="fas fa-eye"></i> Previsualizar', ['class'=>'btn btn-sm btn-info']); ?>
          <?php echo Html::anchor('admin/apariencia/footer', '<i class="fas fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
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
          <h3 class="mb-0">Listado de Distintivos</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-items-center">
            <thead class="thead-light">
              <tr>
                <th>Título</th>
                <th>Imagen</th>
                <th>Orden</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($badges): ?>
                <?php foreach ($badges as $badge): ?>
                <tr>
                  <td><?php echo e($badge->title); ?></td>
                  <td>
                    <?php if ($badge->image): ?>
                        <?php echo Html::img(Uri::base(false).'assets/'.$badge->image, [
                            'alt'   => $badge->title,
                            'style' => 'max-height:40px'
                        ]); ?>
                    <?php else: ?>
                        <span class="text-muted">Sin imagen</span>
                    <?php endif; ?>
                </td>

                  <td><?php echo e($badge->sort_order); ?></td>
                  <td>
                    <?php if ($badge->status): ?>
                      <span class="badge badge-success">Activo</span>
                    <?php else: ?>
                      <span class="badge badge-danger">Inactivo</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-right">
                    <?php echo Html::anchor('admin/apariencia/footer/badges/editar/'.$badge->id, '<i class="fas fa-edit"></i>', ['class'=>'btn btn-sm btn-warning']); ?>
                    <?php echo Html::anchor('admin/apariencia/footer/badges/info/'.$badge->id, '<i class="fas fa-eye"></i>', ['class'=>'btn btn-sm btn-info']); ?>
                    <?php echo Html::anchor('admin/apariencia/footer/badges/eliminar/'.$badge->id, '<i class="fas fa-trash"></i>', ['class'=>'btn btn-sm btn-danger', 'onclick'=>"return confirm('¿Seguro de eliminar este distintivo?')"]); ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted">No hay distintivos registrados.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
