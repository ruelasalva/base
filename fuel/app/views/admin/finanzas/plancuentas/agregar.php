<!-- HEADER -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Finanzas</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/finanzas/plancuentas', 'Plan de Cuentas'); ?></li>
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
      <div class="card-wrapper">
        <div class="card">
          <div class="card-header">
            <h3 class="mb-0">Agregar cuenta</h3>
          </div>

          <div class="card-body">
            <?php echo Form::open(array('method' => 'post')); ?>
              <fieldset>
                <div class="form-row">
                  <div class="col-md-12 mt-0 mb-3">
                    <legend class="mb-0 heading">Información de la cuenta</legend>
                  </div>

                  <!-- Código -->
                  <div class="col-md-4 mb-3">
                    <div class="form-group <?php echo $classes['code']['form-group']; ?>">
                      <?php echo Form::label('Código', 'code', array('class' => 'form-control-label')); ?>
                      <?php echo Form::input('code', (isset($code) ? $code : ''), array('class' => 'form-control '.$classes['code']['form-control'], 'placeholder' => 'Ej. 1001-01')); ?>
                    </div>
                  </div>

                  <!-- Nombre -->
                  <div class="col-md-8 mb-3">
                    <div class="form-group <?php echo $classes['name']['form-group']; ?>">
                      <?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
                      <?php echo Form::input('name', (isset($name) ? $name : ''), array('class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Nombre de la cuenta')); ?>
                    </div>
                  </div>

                  <!-- Tipo -->
                  <div class="col-md-4 mb-3">
                    <div class="form-group <?php echo $classes['type']['form-group']; ?>">
                      <?php echo Form::label('Tipo', 'type', array('class' => 'form-control-label')); ?>
                      <?php echo Form::select('type', (isset($type) ? $type : ''), array(
                        'Activo' => 'Activo',
                        'Pasivo' => 'Pasivo',
                        'Capital' => 'Capital',
                        'Ingreso' => 'Ingreso',
                        'Egreso' => 'Egreso'
                      ), array('class' => 'form-control '.$classes['type']['form-control'])); ?>
                    </div>
                  </div>

                  <!-- Nivel -->
                  <div class="col-md-2 mb-3">
                    <div class="form-group <?php echo $classes['level']['form-group']; ?>">
                      <?php echo Form::label('Nivel', 'level', array('class' => 'form-control-label')); ?>
                      <?php echo Form::input('level', (isset($level) ? $level : '1'), array('class' => 'form-control '.$classes['level']['form-control'], 'type' => 'number', 'min' => '1')); ?>
                    </div>
                  </div>

                  <!-- Moneda -->
                  <div class="col-md-3 mb-3">
                    <div class="form-group <?php echo $classes['currency_id']['form-group']; ?>">
                      <?php echo Form::label('Moneda', 'currency_id', array('class' => 'form-control-label')); ?>
                      <?php echo Form::select('currency_id', (isset($currency_id) ? $currency_id : ''), $currency_opts, array('class' => 'form-control '.$classes['currency_id']['form-control'])); ?>
                    </div>
                  </div>

                  <!-- Cuenta Padre -->
                  <div class="col-md-3 mb-3">
                    <div class="form-group <?php echo $classes['parent_id']['form-group']; ?>">
                      <?php echo Form::label('Cuenta Padre', 'parent_id', array('class' => 'form-control-label')); ?>
                      <?php echo Form::select('parent_id', (isset($parent_id) ? $parent_id : ''), $parent_opts, array('class' => 'form-control '.$classes['parent_id']['form-control'])); ?>
                    </div>
                  </div>

                  <!-- Anexo 24 -->
                  <div class="col-md-3 mb-3">
                    <div class="form-group <?php echo $classes['annex24_code']['form-group']; ?>">
                      <?php echo Form::label('Código Anexo 24', 'annex24_code', array('class' => 'form-control-label')); ?>
                      <?php echo Form::input('annex24_code', (isset($annex24_code) ? $annex24_code : ''), array('class' => 'form-control '.$classes['annex24_code']['form-control'])); ?>
                    </div>
                  </div>

                  <!-- Clase -->
                  <div class="col-md-3 mb-3">
                    <div class="form-group <?php echo $classes['account_class']['form-group']; ?>">
                      <?php echo Form::label('Clase de Cuenta', 'account_class', array('class' => 'form-control-label')); ?>
                      <?php echo Form::input('account_class', (isset($account_class) ? $account_class : ''), array('class' => 'form-control '.$classes['account_class']['form-control'])); ?>
                    </div>
                  </div>

                  <!-- Booleans -->
                  <div class="col-md-2 mb-3">
                    <?php echo Form::label('Confidencial', 'is_confidential', array('class' => 'form-control-label')); ?>
                    <?php echo Form::select('is_confidential', (isset($is_confidential) ? $is_confidential : 0), array(0=>'No',1=>'Sí'), array('class' => 'form-control '.$classes['is_confidential']['form-control'])); ?>
                  </div>
                  <div class="col-md-2 mb-3">
                    <?php echo Form::label('Efectivo', 'is_cash_account', array('class' => 'form-control-label')); ?>
                    <?php echo Form::select('is_cash_account', (isset($is_cash_account) ? $is_cash_account : 0), array(0=>'No',1=>'Sí'), array('class' => 'form-control '.$classes['is_cash_account']['form-control'])); ?>
                  </div>
                  <div class="col-md-2 mb-3">
                    <?php echo Form::label('Activa', 'is_active', array('class' => 'form-control-label')); ?>
                    <?php echo Form::select('is_active', (isset($is_active) ? $is_active : 1), array(1=>'Sí',0=>'No'), array('class' => 'form-control '.$classes['is_active']['form-control'])); ?>
                  </div>
                </div>
              </fieldset>

              <div class="form-group text-right mt-4">
                <?php echo Form::submit('submit', 'Guardar Cuenta', array('class' => 'btn btn-success')); ?>
                <?php echo Html::anchor('admin/finanzas/plancuentas', 'Cancelar', array('class' => 'btn btn-secondary')); ?>
              </div>
            <?php echo Form::close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
