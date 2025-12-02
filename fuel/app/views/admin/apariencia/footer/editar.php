<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Editar Footer</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <!-- Acceso directo a Links -->
          <?php echo Html::anchor('admin/apariencia/footer/links/index/'.$footer->id, 
            '<i class="fas fa-link"></i> Links', 
            ['class'=>'btn btn-sm btn-info']); ?>

          <!-- Acceso directo a Badges -->
          <?php echo Html::anchor('admin/apariencia/footer/badges/index/'.$footer->id, 
            '<i class="fas fa-certificate"></i> Distintivos', 
            ['class'=>'btn btn-sm btn-warning']); ?>

          <!-- Editar / Volver -->
          <?php echo Html::anchor('admin/apariencia/footer', '<i class="fas fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
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

        <?php echo Form::open(['class'=>'form-horizontal', 'enctype'=>'multipart/form-data']); ?>
        <div class="card-header border-0">
          <h3 class="mb-0">Datos generales del Footer</h3>
        </div>
        <div class="card-body">

          <!-- Logos -->
          <div class="form-group">
            <?php echo Form::label('Logo principal', 'logo_main', ['class'=>'form-control-label']); ?>
            <div class="mb-2">
              <?php if ($footer->logo_main): ?>
                <?php echo Html::img(Uri::base(false).'assets/'.$footer->logo_main, [
                  'style'=>'max-height:60px;',
                  'alt'  =>'Logo principal'
                ]); ?>
              <?php else: ?>
                <span class="text-muted">No definido</span>
              <?php endif; ?>
            </div>
            <?php echo Form::file('logo_main', ['class'=>'form-control']); ?>
            <small class="form-text text-muted">Formatos permitidos: JPG, PNG. Tamaño recomendado: 143x65 px.</small>
          </div>

          <div class="form-group">
            <?php echo Form::label('Logo secundario', 'logo_secondary', ['class'=>'form-control-label']); ?>
            <div class="mb-2">
              <?php if ($footer->logo_secondary): ?>
                <?php echo Html::img(Uri::base(false).'assets/'.$footer->logo_secondary, [
                  'style'=>'max-height:60px;',
                  'alt'  =>'Logo secundario'
                ]); ?>
              <?php else: ?>
                <span class="text-muted">No definido</span>
              <?php endif; ?>
            </div>
            <?php echo Form::file('logo_secondary', ['class'=>'form-control']); ?>
            <small class="form-text text-muted">Formatos permitidos: JPG, PNG. Tamaño recomendado: 143x65 px.</small>
          </div>


          <!-- Atención al cliente -->
          <div class="form-group">
            <?php echo Form::label('Texto de atención al cliente', 'customer_service', ['class'=>'form-control-label']); ?>
            <?php echo Form::textarea('customer_service', Input::post('customer_service', $footer->customer_service), ['class'=>'form-control', 'rows'=>3]); ?>
            <small class="form-text text-muted">Ejemplo: “¡Estamos para ayudarte! Escríbenos a atencionaclientes@sajor.mx”.</small>
          </div>

          <!-- Dirección -->
          <div class="form-group">
            <?php echo Form::label('Dirección', 'address', ['class'=>'form-control-label']); ?>
            <?php echo Form::textarea('address', Input::post('address', $footer->address), ['class'=>'form-control', 'rows'=>2]); ?>
          </div>

          <!-- Contacto -->
          <div class="form-group">
            <?php echo Form::label('Teléfono', 'phone', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('phone', Input::post('phone', $footer->phone), ['class'=>'form-control']); ?>
            <small class="form-text text-muted">Ejemplo: 33 3942 7070</small>
          </div>
          <div class="form-group">
            <?php echo Form::label('Email', 'email', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('email', Input::post('email', $footer->email), ['class'=>'form-control']); ?>
          </div>

          <!-- Horarios -->
          <div class="form-group">
            <?php echo Form::label('Horario entre semana', 'office_hours_week', ['class'=>'form-control-label']); ?>
            <?php echo Form::textarea('office_hours_week', Input::post('office_hours_week', $footer->office_hours_week), ['class'=>'form-control','rows'=>2]); ?>
            <small class="form-text text-muted">Ejemplo: Lunes a Viernes 9:00 - 19:00 hrs</small>
          </div>
          <div class="form-group">
            <?php echo Form::label('Horario fin de semana', 'office_hours_weekend', ['class'=>'form-control-label']); ?>
            <?php echo Form::textarea('office_hours_weekend', Input::post('office_hours_weekend', $footer->office_hours_weekend), ['class'=>'form-control','rows'=>2]); ?>
            <small class="form-text text-muted">Ejemplo: Sábado 9:00 - 14:00 hrs / Domingo cerrado</small>
          </div>

          <!-- Redes sociales -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-facebook text-primary mr-1"></i> Facebook', 'facebook', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('facebook', Input::post('facebook', $footer->facebook), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://www.facebook.com/empresa'
                ]); ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-instagram text-danger mr-1"></i> Instagram', 'instagram', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('instagram', Input::post('instagram', $footer->instagram), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://www.instagram.com/empresa'
                ]); ?>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-linkedin text-info mr-1"></i> LinkedIn', 'linkedin', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('linkedin', Input::post('linkedin', $footer->linkedin), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://www.linkedin.com/company/empresa'
                ]); ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-youtube text-danger mr-1"></i> YouTube', 'youtube', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('youtube', Input::post('youtube', $footer->youtube), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://www.youtube.com/@empresa'
                ]); ?>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-twitter text-info mr-1"></i> Twitter (X)', 'twitter', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('twitter', Input::post('twitter', $footer->twitter), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://twitter.com/empresa'
                ]); ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-tiktok text-dark mr-1"></i> TikTok', 'tiktok', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('tiktok', Input::post('tiktok', $footer->tiktok), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://www.tiktok.com/@empresa'
                ]); ?>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-whatsapp text-success mr-1"></i> WhatsApp', 'whatsapp', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('whatsapp', Input::post('whatsapp', $footer->whatsapp), [
                    'class' => 'form-control',
                    'placeholder' => '5210000000000 (solo números con lada país)'
                ]); ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-telegram text-primary mr-1"></i> Telegram', 'telegram', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('telegram', Input::post('telegram', $footer->telegram), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://t.me/empresa'
                ]); ?>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-pinterest text-danger mr-1"></i> Pinterest', 'pinterest', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('pinterest', Input::post('pinterest', $footer->pinterest), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://www.pinterest.com/empresa'
                ]); ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <?php echo Form::label('<i class="fab fa-snapchat text-warning mr-1"></i> Snapchat', 'snapchat', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('snapchat', Input::post('snapchat', $footer->snapchat), [
                    'class' => 'form-control',
                    'type' => 'url',
                    'placeholder' => 'https://www.snapchat.com/add/empresa'
                ]); ?>
              </div>
            </div>
          </div>


          <!-- Estado -->
          <div class="form-group">
            <?php echo Form::label('Estado', 'status', ['class'=>'form-control-label']); ?>
            <?php echo Form::select('status', Input::post('status', $footer->status), [1=>'Activo', 0=>'Inactivo'], ['class'=>'form-control']); ?>
          </div>

        </div>
        <div class="card-footer text-right">
          <?php echo Form::submit('submit', 'Guardar cambios', ['class'=>'btn btn-primary']); ?>
          <?php echo Html::anchor('admin/apariencia/footer', 'Cancelar', ['class'=>'btn btn-secondary']); ?>
        </div>
        <?php echo Form::close(); ?>

      </div>
    </div>
  </div>
</div>
