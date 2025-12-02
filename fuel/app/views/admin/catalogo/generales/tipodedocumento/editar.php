<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Editar Tipo de Documento</h6>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/catalogo/generales/tipodedocumento', '<i class="fas fa-arrow-left"></i> Volver', ['class' => 'btn btn-secondary']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="card shadow">
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-edit text-warning"></i> Editar Tipo</h3>
        </div>
        <div class="card-body">
            <?= Form::open(); ?>

            <div class="form-group">
                <?= Form::label('Nombre del tipo', 'name', ['class'=>'form-control-label']); ?>
                <?= Form::input('name', $tipo->name, ['class'=>'form-control', 'required'=>'required']); ?>
            </div>

            <div class="form-group">
                <?= Form::label('Ámbito', 'scope', ['class'=>'form-control-label']); ?>
                <?= Form::select('scope', $tipo->scope, [
                    '' => 'Selecciona...',
                    'provider' => 'Proveedor',
                    'customer' => 'Cliente',
                    'internal' => 'Interno',
                    'general' => 'General'
                ], ['class'=>'form-control', 'required'=>'required']); ?>
            </div>

            <div class="form-group">
                <?= Form::label('Estado', 'active', ['class'=>'form-control-label']); ?>
                <?= Form::select('active', $tipo->active, [1=>'Activo',0=>'Inactivo'], ['class'=>'form-control']); ?>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
            </div>

            <?= Form::close(); ?>
        </div>
    </div>
</div>
