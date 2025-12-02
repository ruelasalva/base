<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Editar Link</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer/links/index/'.$footer->id, 'Links'); ?></li>
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
        <?php echo Form::open(); ?>
        <div class="card-body">

          <!-- TÍTULO -->
          <div class="form-group">
            <?php echo Form::label('Título', 'title', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('title', Input::post('title', $link->title ?? ''), ['class'=>'form-control']); ?>
          </div>

          <!-- URL -->
          <div class="form-group" id="url-field" style="<?php echo ($link->type == 'legal') ? 'display:none;' : ''; ?>">
            <?php echo Form::label('URL', 'url', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('url', Input::post('url', $link->url ?? ''), ['class'=>'form-control']); ?>
          </div>

          <!-- SLUG -->
          <div class="form-group" id="slug-field" style="<?php echo ($link->type == 'legal') ? 'display:none;' : ''; ?>">
            <?php echo Form::label('Slug', 'slug', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('slug', Input::post('slug', $link->slug ?? ''), ['class'=>'form-control']); ?>
          </div>

          <!-- TIPO -->
          <div class="form-group">
            <?php echo Form::label('Tipo', 'type', ['class'=>'form-control-label']); ?>
            <?php echo Form::select('type', Input::post('type', $link->type ?? 'sitemap'), [
              'sitemap' => 'Mapa de sitio',
              'legal'   => 'Documento legal'
            ], ['class'=>'form-control', 'id'=>'type-select']); ?>
          </div>

          <!-- DOCUMENTO LEGAL -->
          <div class="form-group" id="legal-field" style="<?php echo ($link->type == 'legal') ? '' : 'display:none;'; ?>">
            <?php echo Form::label('Documento legal', 'legal_id', ['class'=>'form-control-label']); ?>
            <?php
              $options = [];
              foreach ($legal_docs as $doc) {
                $options[$doc->id] = $doc->title;
              }
              echo Form::select('legal_id', Input::post('legal_id', isset($link) ? $link->legal_id : ''), $options, ['class'=>'form-control']);
            ?>
          </div>


          <!-- ORDEN -->
          <div class="form-group">
            <?php echo Form::label('Orden', 'sort_order', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('sort_order', Input::post('sort_order', $link->sort_order ?? ''), ['class'=>'form-control']); ?>
          </div>

          <!-- ESTADO -->
          <div class="form-group">
            <?php echo Form::label('Estado', 'status', ['class'=>'form-control-label']); ?>
            <?php echo Form::select('status', Input::post('status', $link->status ?? 1), [1=>'Activo', 0=>'Inactivo'], ['class'=>'form-control']); ?>
          </div>

        </div>

        <div class="card-footer d-flex justify-content-between">
          <?php echo Form::submit('submit', 'Actualizar', ['class'=>'btn btn-primary']); ?>
          <?php echo Html::anchor('admin/apariencia/footer/links/index/'.$footer->id, 'Cancelar', ['class'=>'btn btn-secondary']); ?>
        </div>
        <?php echo Form::close(); ?>
      </div>
    </div>
  </div>
</div>


