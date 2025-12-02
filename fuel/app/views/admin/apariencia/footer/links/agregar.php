<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Agregar Link</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer/links/index/'.$footer->id, 'Links'); ?></li>
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
        <?php echo Form::open(); ?>
        <div class="card-body">
          
          <!-- TÍTULO -->
          <div class="form-group">
            <?php echo Form::label('Título', 'title', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('title', Input::post('title', ''), ['class'=>'form-control']); ?>
          </div>

          <!-- TIPO -->
          <div class="form-group">
            <?php echo Form::label('Tipo', 'type', ['class'=>'form-control-label']); ?>
            <?php echo Form::select('type', Input::post('type', 'sitemap'), [
              'sitemap'=>'Mapa de sitio',
              'legal'=>'Documento legal'
            ], ['class'=>'form-control', 'id'=>'type-select']); ?>
          </div>

          <!-- URL -->
          <div class="form-group" id="url-field">
            <?php echo Form::label('URL', 'url', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('url', Input::post('url', ''), ['class'=>'form-control']); ?>
          </div>

          <!-- SLUG -->
          <div class="form-group" id="slug-field">
            <?php echo Form::label('Slug', 'slug', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('slug', Input::post('slug', ''), ['class'=>'form-control']); ?>
          </div>

          <!-- DOCUMENTO LEGAL -->
          <div class="form-group" id="legal-field" style="display:none;">
            <?php echo Form::label('Documento legal', 'legal_id', ['class'=>'form-control-label']); ?>
            <?php
              $options = [];
              foreach ($legal_docs as $doc) {
                $options[$doc->id] = $doc->title;
              }
              echo Form::select('legal_id', Input::post('legal_id', ''), $options, ['class'=>'form-control']);
            ?>
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
          <?php echo Html::anchor('admin/apariencia/footer/links/index/'.$footer->id, 'Cancelar', ['class'=>'btn btn-secondary']); ?>
        </div>
        <?php echo Form::close(); ?>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT PARA MOSTRAR OCULTAR CAMPOS -->
<script>
document.getElementById('type-select').addEventListener('change', function() {
  const legalField = document.getElementById('legal-field');
  const urlField   = document.getElementById('url-field');
  const slugField  = document.getElementById('slug-field');
  
  if (this.value === 'legal') {
    legalField.style.display = 'block';
    urlField.style.display   = 'none';
    slugField.style.display  = 'none';
  } else {
    legalField.style.display = 'none';
    urlField.style.display   = 'block';
    slugField.style.display  = 'block';
  }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const typeSelect  = document.getElementById('type-select');
  const legalField  = document.getElementById('legal-field');
  const legalSelect = document.getElementById('legal-select');
  const slugInput   = document.getElementById('form_slug');

  // Mostrar u ocultar el campo de documentos legales
  if (typeSelect) {
    typeSelect.addEventListener('change', function() {
      if (this.value === 'legal') {
        legalField.style.display = 'block';
      } else {
        legalField.style.display = 'none';
        slugInput.value = '';
      }
    });
  }

  // Copiar el shortcode seleccionado al slug
  if (legalSelect) {
    legalSelect.addEventListener('change', function() {
      const selected = this.options[this.selectedIndex].text;
      const shortcode = selected.match(/\(([^)]+)\)$/); // toma lo que está entre paréntesis
      if (shortcode && shortcode[1]) {
        slugInput.value = shortcode[1];
      }
    });
  }
});
</script>

