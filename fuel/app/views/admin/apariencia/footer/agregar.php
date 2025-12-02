<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Agregar Footer</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?></li>
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
          
          <div class="form-group">
            <?php echo Form::label('Logo principal (PNG/JPG máx 200kb)', 'logo_main', ['class'=>'form-control-label']); ?>
            <?php echo Form::file('logo_main', ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Logo secundario (opcional)', 'logo_secondary', ['class'=>'form-control-label']); ?>
            <?php echo Form::file('logo_secondary', ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Dirección', 'address', ['class'=>'form-control-label']); ?>
            <?php echo Form::textarea('address', Input::post('address',''), ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Teléfono', 'phone', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('phone', Input::post('phone',''), ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Correo electrónico', 'email', ['class'=>'form-control-label']); ?>
            <?php echo Form::input('email', Input::post('email',''), ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Horario entre semana', 'office_hours_week', ['class'=>'form-control-label']); ?>
            <?php echo Form::textarea('office_hours_week', Input::post('office_hours_week',''), ['class'=>'form-control']); ?>
          </div>

          <div class="form-group">
            <?php echo Form::label('Horario fin de semana', 'office_hours_weekend', ['class'=>'form-control-label']); ?>
            <?php echo Form::textarea('office_hours_weekend', Input::post('office_hours_weekend',''), ['class'=>'form-control']); ?>
          </div>

          <!-- aquí irían las redes sociales como en editar -->

          <div class="form-group">
            <?php echo Form::label('Estado', 'status', ['class'=>'form-control-label']); ?>
            <?php echo Form::select('status', Input::post('status', 0), [1=>'Activo', 0=>'Inactivo'], ['class'=>'form-control']); ?>
          </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
          <?php echo Form::submit('submit', 'Guardar', ['class'=>'btn btn-primary']); ?>
          <?php echo Html::anchor('admin/apariencia/footer', 'Cancelar', ['class'=>'btn btn-secondary']); ?>
        </div>
        <?php echo Form::close(); ?>
      </div>
    </div>
  </div>
</div>
