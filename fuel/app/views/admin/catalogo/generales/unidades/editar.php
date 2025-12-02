<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Editar Unidad</h6>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/unidades', '<i class="fa fa-arrow-left"></i> Volver', array('class'=>'btn btn-sm btn-neutral')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <?php if (Session::get_flash('error')): ?>
        <div class="alert alert-danger"><?php echo Session::get_flash('error'); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Unidad: <span class="text-primary"><?php echo e($name); ?></span></h3>
            <small>
                Origen:
                <?php if (!empty($is_internal)): ?>
                    <span class="badge badge-info">Interna</span>
                <?php else: ?>
                    <span class="badge badge-success">SAT</span>
                <?php endif; ?>
            </small>
        </div>
        <div class="card-body">
            <?php echo Form::open(); ?>

            <div class="form-row">
                <div class="col-md-3 <?php echo $classes['code']['form-group']; ?>">
                    <?php echo Form::label('Código', 'code'); ?>
                    <?php echo Form::input('code', isset($code)?$code:'', array('class'=>'form-control '.$classes['code']['form-control'], 'required')); ?>
                </div>

                <div class="col-md-5 <?php echo $classes['name']['form-group']; ?>">
                    <?php echo Form::label('Nombre', 'name'); ?>
                    <?php echo Form::input('name', isset($name)?$name:'', array('class'=>'form-control '.$classes['name']['form-control'], 'required')); ?>
                </div>

                <div class="col-md-2 <?php echo $classes['abbreviation']['form-group']; ?>">
                    <?php echo Form::label('Abreviatura', 'abbreviation'); ?>
                    <?php echo Form::input('abbreviation', isset($abbreviation)?$abbreviation:'', array('class'=>'form-control '.$classes['abbreviation']['form-control'], 'maxlength'=>16)); ?>
                </div>

                <div class="col-md-2 <?php echo $classes['conversion_factor']['form-group']; ?>">
                    <?php echo Form::label('Factor Conversión', 'conversion_factor'); ?>
                    <?php echo Form::input('conversion_factor', isset($conversion_factor)?$conversion_factor:'1', array('class'=>'form-control '.$classes['conversion_factor']['form-control'], 'required')); ?>
                </div>
            </div>

            <div class="form-group mt-3 <?php echo $classes['description']['form-group']; ?>">
                <?php echo Form::label('Descripción', 'description'); ?>
                <?php echo Form::textarea('description', isset($description)?$description:'', array('class'=>'form-control '.$classes['description']['form-control'], 'rows'=>2)); ?>
            </div>

            <div class="form-group mt-2">
                <div class="form-check">
                    <input type="checkbox" name="active" id="active" value="1" class="form-check-input" <?php echo (!empty($active) ? 'checked' : ''); ?>>
                    <label for="active" class="form-check-label">Activa</label>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Guardar cambios</button>
            </div>

            <?php echo Form::close(); ?>
        </div>
    </div>
</div>
