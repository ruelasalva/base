
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
                                <?php echo Html::anchor('admin/catalogo/generales/descuentos', 'Descuentos'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/catalogo/generales/descuentos/info/'.$id, $name); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Editar
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/descuentos/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                        <h3 class="mb-0">Editar descuento</h3>
                    </div>
                    <!-- CARD BODY -->
                    <div class="card-body">
                        <?php echo Form::open(array('method' => 'post')); ?>
                        <fieldset>
                            <div class="form-row">
                                <div class="col-md-12 mt-0 mb-3">
                                    <legend class="mb-0 heading">Información del descuento</legend>
                                </div>
                                <!-- Nombre -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group <?php echo $classes['name']['form-group']; ?>">
                                        <?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
                                        <?php echo Form::input('name', (isset($name) ? $name : ''), array(
                                            'id' => 'name',
                                            'class' => 'form-control '.$classes['name']['form-control'],
                                            'placeholder' => 'Nombre del descuento'
                                        )); ?>
                                        <?php if(isset($errors['name'])) : ?>
                                            <div class="invalid-feedback">
                                                <?php echo $errors['name']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Estructura -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group <?php echo $classes['structure']['form-group']; ?>">
                                        <?php echo Form::label('Estructura', 'structure', array('class' => 'form-control-label')); ?>
                                        <?php echo Form::input('structure', (isset($structure) ? $structure : ''), array(
                                            'id' => 'structure',
                                            'class' => 'form-control '.$classes['structure']['form-control'],
                                            'placeholder' => 'Ejemplo: 30+10'
                                        )); ?>
                                        <?php if(isset($errors['structure'])) : ?>
                                            <div class="invalid-feedback">
                                                <?php echo $errors['structure']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Tipo -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group <?php echo $classes['type']['form-group']; ?>">
                                        <?php echo Form::label('Tipo', 'type', array('class' => 'form-control-label')); ?>
                                        <?php echo Form::select('type', (isset($type) ? $type : ''), array('simple' => 'Simple', 'compuesto' => 'Compuesto'), array(
                                            'id' => 'type',
                                            'class' => 'form-control '.$classes['type']['form-control']
                                        )); ?>
                                        <?php if(isset($errors['type'])) : ?>
                                            <div class="invalid-feedback">
                                                <?php echo $errors['type']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Porcentaje efectivo final -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group <?php echo $classes['final_effective']['form-group']; ?>">
                                        <?php echo Form::label('Porcentaje efectivo final (opcional)', 'final_effective', array('class' => 'form-control-label')); ?>
                                        <?php echo Form::input('final_effective', (isset($final_effective) ? $final_effective : ''), array(
                                            'id' => 'final_effective',
                                            'class' => 'form-control '.$classes['final_effective']['form-control'],
                                            'placeholder' => 'Ej. 37.00'
                                        )); ?>
                                        <?php if(isset($errors['final_effective'])) : ?>
                                            <div class="invalid-feedback">
                                                <?php echo $errors['final_effective']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary submit-form')); ?>
                        <?php echo Html::anchor('admin/catalogo/generales/descuentos/info/'.$id, 'Cancelar', array('class' => 'btn btn-secondary ml-2')); ?>
                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
