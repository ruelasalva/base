<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Agregar Unidad SAT</h6>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/formas/unidades', '<i class="fas fa-arrow-left"></i> Volver', ['class' => 'btn btn-secondary']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="card shadow">
        <div class="card-header"><h3 class="mb-0"><i class="fas fa-plus-circle"></i> Nueva Unidad</h3></div>
        <div class="card-body">
            <?= Form::open(); ?>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <?= Form::label('Clave SAT', 'code'); ?>
                    <?= Form::input('code', '', ['class'=>'form-control','required']); ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= Form::label('Nombre', 'name'); ?>
                    <?= Form::input('name', '', ['class'=>'form-control','required']); ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= Form::label('Abreviatura', 'abbreviation'); ?>
                    <?= Form::input('abbreviation', '', ['class'=>'form-control']); ?>
                </div>
                <div class="col-md-12 mb-3">
                    <?= Form::label('Descripción', 'description'); ?>
                    <?= Form::textarea('description', '', ['class'=>'form-control','rows'=>3]); ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= Form::label('Factor de Conversión', 'conversion_factor'); ?>
                    <?= Form::input('conversion_factor', '1', ['class'=>'form-control']); ?>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-4">
                        <?= Form::checkbox('is_internal', 1, false, ['class'=>'form-check-input','id'=>'is_internal']); ?>
                        <?= Form::label('Marcar como unidad interna', 'is_internal', ['class'=>'form-check-label']); ?>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-4">
                        <?= Form::checkbox('active', 1, true, ['class'=>'form-check-input','id'=>'active']); ?>
                        <?= Form::label('Activa', 'active', ['class'=>'form-check-label']); ?>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Unidad</button>
            </div>
            <?= Form::close(); ?>
        </div>
    </div>
</div>
