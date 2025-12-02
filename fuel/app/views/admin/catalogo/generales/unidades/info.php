<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Información de la Unidad</h6>
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
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">
                <?php echo e($name); ?>
                <?php if (!empty($is_internal)): ?>
                    <span class="badge badge-info">Interna</span>
                <?php else: ?>
                    <span class="badge badge-success">SAT</span>
                <?php endif; ?>
            </h3>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?php echo Form::label('Código', 'code'); ?>
                    <span class="form-control"><?php echo e($code); ?></span>
                </div>

                <div class="col-md-6 mb-3">
                    <?php echo Form::label('Abreviatura', 'abbreviation'); ?>
                    <span class="form-control"><?php echo e($abbreviation); ?></span>
                </div>

                <div class="col-md-12 mb-3">
                    <?php echo Form::label('Descripción', 'description'); ?>
                    <span class="form-control"><?php echo e($description); ?></span>
                </div>

                <div class="col-md-4 mb-3">
                    <?php echo Form::label('Factor conversión', 'conversion_factor'); ?>
                    <span class="form-control"><?php echo number_format((float)$conversion_factor, 4); ?></span>
                </div>

                <div class="col-md-4 mb-3">
                    <?php echo Form::label('Estado', 'active'); ?>
                    <span class="form-control">
                        <?php echo !empty($active) ? 'Activa' : 'Inactiva'; ?>
                    </span>
                </div>

                <div class="col-md-4 mb-3">
                    <?php echo Form::label('Origen', 'origin'); ?>
                    <span class="form-control">
                        <?php echo !empty($is_internal) ? 'Interna' : 'SAT'; ?>
                    </span>
                </div>
            </div>

            <div class="text-right">
                <?php echo Html::anchor('admin/catalogo/generales/unidades/editar/'.$id, '<i class="fas fa-edit"></i> Editar', array('class'=>'btn btn-primary')); ?>
            </div>
        </div>
    </div>
</div>
