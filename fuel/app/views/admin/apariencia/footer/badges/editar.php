<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Editar Distintivo</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer/badges/index/'.$footer->id, 'Badges'); ?></li>
              <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FORMULARIO -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card">
        <?php echo Form::open(['enctype' => 'multipart/form-data']); ?>
        <div class="card-body">
          <div class="form-group">
            <?php echo Form::label('Título', 'title', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('title', Input::post('title', isset($badge) ? $badge->title : ''), ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Imagen (PNG/JPG máx 200kb)', 'image', ['class'=>'form-control-label']); ?>
            <?php echo Form::file('image', ['class'=>'form-control']); ?>
            <?php if (!empty($badge->image)): ?>
              <div class="mt-2">
                <?php if ($badge->image): ?>
                    <?php echo Html::img(Uri::base(false).'assets/'.$badge->image, [
                        'alt'   => $badge->title,
                        'style' => 'max-height:80px'
                    ]); ?>
                <?php else: ?>
                    <span class="text-muted">Sin imagen</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Orden', 'sort_order', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('sort_order', Input::post('sort_order', isset($badge) ? $badge->sort_order : ''), ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Estado', 'status', ['class'=>'form-control-label']); ?>
            <?php echo Form::select('status', Input::post('status', isset($badge) ? $badge->status : 1), [1=>'Activo', 0=>'Inactivo'], ['class'=>'form-control']); ?>
          </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
          <?php echo Form::submit('submit', 'Actualizar', ['class'=>'btn btn-primary']); ?>
          <?php echo Html::anchor('admin/apariencia/footer/badges/index/'.$footer->id, 'Cancelar', ['class'=>'btn btn-secondary']); ?>
        </div>
        <?php echo Form::close(); ?>
      </div>
    </div>
  </div>
</div>
