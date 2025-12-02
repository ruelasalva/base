<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Agregar Distintivo</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer/badges/index/'.$footer->id, 'Badges'); ?></li>
              <li class="breadcrumb-item active" aria-current="page">Agregar</li>
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

          <!-- TÍTULO -->
          <div class="form-group">
            <?php echo Form::label('Título', 'title', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('title', Input::post('title', ''), ['class'=>'form-control']); ?>
          </div>

          <!-- IMAGEN -->
          <div class="form-group">
            <?php echo Form::label('Imagen (PNG/JPG máx 200kb)', 'image', ['class'=>'form-control-label']); ?>
            <div class="mb-2">
              <img id="preview" src="#" alt="Previsualización" style="display:none; max-height:80px; border:1px solid #ddd; padding:3px; background:#f9f9f9;">
            </div>
            <?php echo Form::file('image', ['class'=>'form-control', 'id'=>'imageInput']); ?>
            <small class="form-text text-muted">Formatos permitidos: JPG, PNG. Tamaño recomendado: 143x65 px.</small>
          </div>

          <!-- ORDEN -->
          <div class="form-group">
            <?php echo Form::label('Orden', 'sort_order', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('sort_order', Input::post('sort_order', ''), ['class'=>'form-control']); ?>
          </div>

          <!-- ESTADO -->
          <div class="form-group">
            <?php echo Form::label('Estado', 'status', ['class'=>'form-control-label']); ?>
            <?php echo Form::select('status', Input::post('status', 1), [1=>'Activo', 0=>'Inactivo'], ['class'=>'form-control']); ?>
          </div>

        </div>

        <div class="card-footer d-flex justify-content-between">
          <?php echo Form::submit('submit', 'Guardar', ['class'=>'btn btn-primary']); ?>
          <?php echo Html::anchor('admin/apariencia/footer/badges/index/'.$footer->id, 'Cancelar', ['class'=>'btn btn-secondary']); ?>
        </div>
        <?php echo Form::close(); ?>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT PREVISUALIZAR IMAGEN -->
<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById('preview');
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    }
    reader.readAsDataURL(file);
  } else {
    preview.style.display = 'none';
  }
});
</script>
