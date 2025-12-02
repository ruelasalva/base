<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Finanzas</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/finanzas/plancuentas', 'Plan de Cuentas'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/finanzas/plancuentas/info/'.$id, $code); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/finanzas/plancuentas/editar/'.$id, 'Editar'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/finanzas/plancuentas/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <div class="card">
                    <!-- CARD HEADER -->
                    <div class="card-header">
                        <h3 class="mb-0">Editar cuenta</h3>
                    </div>
                    <!-- CARD BODY -->
                    <div class="card-body">
                        <?php echo Form::open(array('method' => 'post')); ?>
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mt-0 mb-3">
                                        <legend class="mb-0 heading">Información de la cuenta</legend>
                                    </div>

                                    <!-- CÓDIGO -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['code']['form-group']; ?>">
                                            <?php echo Form::label('Código', 'code', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('code', (isset($code) ? $code : ''), array(
                                                'id' => 'code',
                                                'class' => 'form-control '.$classes['code']['form-control'],
                                                'placeholder' => 'Código'
                                            )); ?>
                                            <?php if(isset($errors['code'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['code']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- NOMBRE -->
                                    <div class="col-md-8 mb-3">
                                        <div class="form-group <?php echo $classes['name']['form-group']; ?>">
                                            <?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('name', (isset($name) ? $name : ''), array(
                                                'id' => 'name',
                                                'class' => 'form-control '.$classes['name']['form-control'],
                                                'placeholder' => 'Nombre'
                                            )); ?>
                                            <?php if(isset($errors['name'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- TIPO -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['type']['form-group']; ?>">
                                            <?php echo Form::label('Tipo', 'type', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('type', (isset($type) ? $type : ''), array(
                                                'id' => 'type',
                                                'class' => 'form-control '.$classes['type']['form-control'],
                                                'placeholder' => 'Tipo (Activo, Pasivo, Capital, etc.)'
                                            )); ?>
                                            <?php if(isset($errors['type'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['type']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- NIVEL -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['level']['form-group']; ?>">
                                            <?php echo Form::label('Nivel', 'level', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('level', (isset($level) ? $level : ''), array(
                                                'id' => 'level',
                                                'type' => 'number',
                                                'class' => 'form-control '.$classes['level']['form-control'],
                                                'placeholder' => 'Nivel jerárquico (1, 2, 3...)'
                                            )); ?>
                                            <?php if(isset($errors['level'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['level']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- MONEDA -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['currency_id']['form-group']; ?>">
                                            <?php echo Form::label('Moneda', 'currency_id', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::select('currency_id', (isset($currency_id) ? $currency_id : ''), $currency_opts, array(
                                                'id' => 'currency_id',
                                                'class' => 'form-control '.$classes['currency_id']['form-control']
                                            )); ?>
                                            <?php if(isset($errors['currency_id'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['currency_id']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- CUENTA PADRE -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['parent_id']['form-group']; ?>">
                                            <?php echo Form::label('Cuenta Padre', 'parent_id', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::select('parent_id', (isset($parent_id) ? $parent_id : ''), $account_opts, array(
                                                'id' => 'parent_id',
                                                'class' => 'form-control '.$classes['parent_id']['form-control']
                                            )); ?>
                                            <?php if(isset($errors['parent_id'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['parent_id']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- CONFIDENCIAL -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['is_confidential']['form-group']; ?>">
                                            <?php echo Form::label('¿Confidencial?', 'is_confidential', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::select('is_confidential', (isset($is_confidential) ? $is_confidential : '0'), array('0'=>'No','1'=>'Sí'), array(
                                                'id' => 'is_confidential',
                                                'class' => 'form-control '.$classes['is_confidential']['form-control']
                                            )); ?>
                                        </div>
                                    </div>

                                    <!-- CUENTA DE EFECTIVO -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['is_cash_account']['form-group']; ?>">
                                            <?php echo Form::label('¿Cuenta de Efectivo?', 'is_cash_account', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::select('is_cash_account', (isset($is_cash_account) ? $is_cash_account : '0'), array('0'=>'No','1'=>'Sí'), array(
                                                'id' => 'is_cash_account',
                                                'class' => 'form-control '.$classes['is_cash_account']['form-control']
                                            )); ?>
                                        </div>
                                    </div>

                                    <!-- ACTIVA -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['is_active']['form-group']; ?>">
                                            <?php echo Form::label('¿Activa?', 'is_active', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::select('is_active', (isset($is_active) ? $is_active : '1'), array('0'=>'No','1'=>'Sí'), array(
                                                'id' => 'is_active',
                                                'class' => 'form-control '.$classes['is_active']['form-control']
                                            )); ?>
                                        </div>
                                    </div>

                                    <!-- ANEXO 24 -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['annex24_code']['form-group']; ?>">
                                            <?php echo Form::label('Código Anexo 24', 'annex24_code', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('annex24_code', (isset($annex24_code) ? $annex24_code : ''), array(
                                                'id' => 'annex24_code',
                                                'class' => 'form-control '.$classes['annex24_code']['form-control'],
                                                'placeholder' => 'Código Anexo 24'
                                            )); ?>
                                        </div>
                                    </div>

                                    <!-- CLASE DE CUENTA -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['account_class']['form-group']; ?>">
                                            <?php echo Form::label('Clase de Cuenta', 'account_class', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('account_class', (isset($account_class) ? $account_class : ''), array(
                                                'id' => 'account_class',
                                                'class' => 'form-control '.$classes['account_class']['form-control'],
                                                'placeholder' => 'Clase de Cuenta'
                                            )); ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="text-right mt-4">
                                <?php echo Form::submit(array('value'=> 'Guardar cambios', 'name'=>'submit', 'class' => 'btn btn-primary submit-form')); ?>
                            </div>

                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
