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
                                <?php echo Html::anchor('admin/catalogo/generales/condiciones/pago', 'Condiciones de Pago'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/catalogo/generales/condiciones/pago/info/'.$id, $code); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/catalogo/generales/condiciones/pago/editar/'.$id, 'Editar'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/condiciones/pago/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                    <div class="card-header">
                        <h3 class="mb-0">Editar condición de pago</h3>
                    </div>
                    <div class="card-body">
                        <?php echo Form::open(array('method' => 'post')); ?>
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mt-0 mb-3">
                                        <legend class="mb-0 heading">Información de la condición</legend>
                                    </div>
                                    <!-- Código -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['code']['form-group']; ?>">
                                            <?php echo Form::label('Código', 'code', array('class' => 'form-control-label')); ?>
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
                                    <!-- Nombre -->
                                    <div class="col-md-8 mb-3">
                                        <div class="form-group <?php echo $classes['name']['form-group']; ?>">
                                            <?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('name', (isset($name) ? $name : ''), array(
                                                'id' => 'name',
                                                'class' => 'form-control '.$classes['name']['form-control'],
                                                'placeholder' => 'Nombre'
                                            )); ?>
                                            <?php if(isset($errors['name'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['name']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Tipo base (opcional) -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['base_date_type']['form-group']; ?>">
                                            <?php echo Form::label('Tipo de Base', 'base_date_type', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('base_date_type', (isset($base_date_type) ? $base_date_type : ''), array(
                                                'id' => 'base_date_type',
                                                'class' => 'form-control '.$classes['base_date_type']['form-control'],
                                                'placeholder' => 'Ej. Documento, Entrega'
                                            )); ?>
                                            <?php if(isset($errors['base_date_type'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['base_date_type']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Días de inicio (opcional) -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['start_offset_days']['form-group']; ?>">
                                            <?php echo Form::label('Días de inicio', 'start_offset_days', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('start_offset_days', (isset($start_offset_days) ? $start_offset_days : ''), array(
                                                'id' => 'start_offset_days',
                                                'class' => 'form-control '.$classes['start_offset_days']['form-control'],
                                                'placeholder' => 'Días desde la base'
                                            )); ?>
                                            <?php if(isset($errors['start_offset_days'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['start_offset_days']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Días de tolerancia (opcional) -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['days_tolerance']['form-group']; ?>">
                                            <?php echo Form::label('Días de tolerancia', 'days_tolerance', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('days_tolerance', (isset($days_tolerance) ? $days_tolerance : ''), array(
                                                'id' => 'days_tolerance',
                                                'class' => 'form-control '.$classes['days_tolerance']['form-control'],
                                                'placeholder' => 'Tolerancia'
                                            )); ?>
                                            <?php if(isset($errors['days_tolerance'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['days_tolerance']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Parcialidades (opcional) -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['installment_count']['form-group']; ?>">
                                            <?php echo Form::label('Parcialidades', 'installment_count', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('installment_count', (isset($installment_count) ? $installment_count : ''), array(
                                                'id' => 'installment_count',
                                                'class' => 'form-control '.$classes['installment_count']['form-control'],
                                                'placeholder' => 'Núm. de parcialidades'
                                            )); ?>
                                            <?php if(isset($errors['installment_count'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['installment_count']; ?>
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
