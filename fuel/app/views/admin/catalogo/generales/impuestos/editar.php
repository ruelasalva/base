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
                                <?php echo Html::anchor('admin/catalogo/generales/impuestos', 'Impuestos'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/catalogo/generales/impuestos/info/'.$id, $code); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/catalogo/generales/impuestos/editar/'.$id, 'Editar'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/impuestos/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                        <h3 class="mb-0">Editar impuesto</h3>
                    </div>
                    <!-- CARD BODY -->
                    <div class="card-body">
                        <?php echo Form::open(array('method' => 'post')); ?>
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mt-0 mb-3">
                                        <legend class="mb-0 heading">Información del impuesto</legend>
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
                                    <!-- Tipo de factor -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['type_factor']['form-group']; ?>">
                                            <?php echo Form::label('Tipo de Factor', 'type_factor', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('type_factor', (isset($type_factor) ? $type_factor : ''), array(
                                                'id' => 'type_factor',
                                                'class' => 'form-control '.$classes['type_factor']['form-control'],
                                                'placeholder' => 'Tasa, Cuota, etc.'
                                            )); ?>
                                            <?php if(isset($errors['type_factor'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['type_factor']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Tasa -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['rate']['form-group']; ?>">
                                            <?php echo Form::label('Tasa (%)', 'rate', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('rate', (isset($rate) ? $rate : ''), array(
                                                'id' => 'rate',
                                                'class' => 'form-control '.$classes['rate']['form-control'],
                                                'placeholder' => 'Ejemplo: 0.16000 para 16%'
                                            )); ?>
                                            <?php if(isset($errors['rate'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['rate']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Clave SAT -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['clave_sat']['form-group']; ?>">
                                            <?php echo Form::label('Clave SAT', 'clave_sat', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('clave_sat', (isset($clave_sat) ? $clave_sat : ''), array(
                                                'id' => 'clave_sat',
                                                'class' => 'form-control '.$classes['clave_sat']['form-control'],
                                                'placeholder' => 'Clave SAT (ej. 002)'
                                            )); ?>
                                            <?php if(isset($errors['clave_sat'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['clave_sat']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Tipo SAT -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['tipo_sat']['form-group']; ?>">
                                            <?php echo Form::label('Tipo SAT', 'tipo_sat', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('tipo_sat', (isset($tipo_sat) ? $tipo_sat : ''), array(
                                                'id' => 'tipo_sat',
                                                'class' => 'form-control '.$classes['tipo_sat']['form-control'],
                                                'placeholder' => 'Ejemplo: IVA16, IVA0'
                                            )); ?>
                                            <?php if(isset($errors['tipo_sat'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['tipo_sat']; ?>
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
