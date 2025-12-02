<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/formas_retenciones', 'Retenciones'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/formas_retenciones/info/'.$id, $code); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/formas_retenciones/editar/'.$id, 'Editar'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/formas_retenciones/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                <!-- CUSTOM FORM VALIDATION -->
                <div class="card">
                    <!-- CARD HEADER -->
                    <div class="card-header">
                        <h3 class="mb-0">Editar retención</h3>
                    </div>
                    <!-- CARD BODY -->
                    <div class="card-body">
                        <?php echo Form::open(array('method' => 'post')); ?>
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mt-0 mb-3">
                                        <legend class="mb-0 heading">Información de la retención</legend>
                                    </div>
                                    <!-- CÓDIGO -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['code']['form-group']; ?>">
                                            <?php echo Form::label('Código', 'code', array('class' => 'form-control-label', 'for' => 'code')); ?>
                                            <?php echo Form::input('code', (isset($code) ? $code : ''), array(
                                                'id' => 'code',
                                                'class' => 'form-control '.$classes['code']['form-control'],
                                                'placeholder' => 'Código'
                                            )); ?>
                                            <?php if(isset($errors['code'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['code']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- DESCRIPCIÓN -->
                                    <div class="col-md-8 mb-3">
                                        <div class="form-group <?php echo $classes['description']['form-group']; ?>">
                                            <?php echo Form::label('Descripción', 'description', array('class' => 'form-control-label', 'for' => 'description')); ?>
                                            <?php echo Form::input('description', (isset($description) ? $description : ''), array(
                                                'id' => 'description',
                                                'class' => 'form-control '.$classes['description']['form-control'],
                                                'placeholder' => 'Descripción'
                                            )); ?>
                                            <?php if(isset($errors['description'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['description']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- TIPO -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['type']['form-group']; ?>">
                                            <?php echo Form::label('Tipo', 'type', array('class' => 'form-control-label', 'for' => 'type')); ?>
                                            <?php echo Form::input('type', (isset($type) ? $type : ''), array(
                                                'id' => 'type',
                                                'class' => 'form-control '.$classes['type']['form-control'],
                                                'placeholder' => 'Tipo'
                                            )); ?>
                                            <?php if(isset($errors['type'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['type']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- CATEGORÍA -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['category']['form-group']; ?>">
                                            <?php echo Form::label('Categoría', 'category', array('class' => 'form-control-label', 'for' => 'category')); ?>
                                            <?php echo Form::input('category', (isset($category) ? $category : ''), array(
                                                'id' => 'category',
                                                'class' => 'form-control '.$classes['category']['form-control'],
                                                'placeholder' => 'Categoría'
                                            )); ?>
                                            <?php if(isset($errors['category'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['category']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- FECHA DE VIGENCIA -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['valid_from']['form-group']; ?>">
                                            <?php echo Form::label('Vigencia desde', 'valid_from', array('class' => 'form-control-label', 'for' => 'valid_from')); ?>
                                            <?php echo Form::input('valid_from', (isset($valid_from) ? $valid_from : ''), array(
                                                'id' => 'valid_from',
                                                'type' => 'date',
                                                'class' => 'form-control '.$classes['valid_from']['form-control']
                                            )); ?>
                                            <?php if(isset($errors['valid_from'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['valid_from']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- TIPO DE BASE -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['base_type']['form-group']; ?>">
                                            <?php echo Form::label('Tipo de base', 'base_type', array('class' => 'form-control-label', 'for' => 'base_type')); ?>
                                            <?php echo Form::input('base_type', (isset($base_type) ? $base_type : ''), array(
                                                'id' => 'base_type',
                                                'class' => 'form-control '.$classes['base_type']['form-control'],
                                                'placeholder' => 'Tipo de base'
                                            )); ?>
                                            <?php if(isset($errors['base_type'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['base_type']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- TASA -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['rate']['form-group']; ?>">
                                            <?php echo Form::label('Tasa (%)', 'rate', array('class' => 'form-control-label', 'for' => 'rate')); ?>
                                            <?php echo Form::input('rate', (isset($rate) ? $rate : ''), array(
                                                'id' => 'rate',
                                                'class' => 'form-control '.$classes['rate']['form-control'],
                                                'type' => 'number',
                                                'min' => '0',
                                                'step' => '0.00001',
                                                'placeholder' => '0.00000'
                                            )); ?>
                                            <?php if(isset($errors['rate'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['rate']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- CUENTA CONTABLE -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['account']['form-group']; ?>">
                                            <?php echo Form::label('Cuenta contable', 'account', array('class' => 'form-control-label', 'for' => 'account')); ?>
                                            <?php echo Form::input('account', (isset($account) ? $account : ''), array(
                                                'id' => 'account',
                                                'class' => 'form-control '.$classes['account']['form-control'],
                                                'placeholder' => 'Cuenta contable'
                                            )); ?>
                                            <?php if(isset($errors['account'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['account']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- TIPO DE FACTOR -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['factor_type']['form-group']; ?>">
                                            <?php echo Form::label('Tipo de factor', 'factor_type', array('class' => 'form-control-label', 'for' => 'factor_type')); ?>
                                            <?php echo Form::input('factor_type', (isset($factor_type) ? $factor_type : ''), array(
                                                'id' => 'factor_type',
                                                'class' => 'form-control '.$classes['factor_type']['form-control'],
                                                'placeholder' => 'Tipo de factor'
                                            )); ?>
                                            <?php if(isset($errors['factor_type'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['factor_type']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary submit-form')); ?>
                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
