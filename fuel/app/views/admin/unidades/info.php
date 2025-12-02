<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Detalle Unidad SAT</h6>
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
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-info-circle text-primary"></i> Información</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <?= Form::label('Clave SAT'); ?>
                    <span class="form-control"><?= $code; ?></span>
                </div>
                <div class="col-md-5 mb-3">
                    <?= Form::label('Nombre'); ?>
                    <span class="form-control"><?= $name; ?></span>
                </div>
                <div class="col-md-4 mb-3">
                    <?= Form::label('Abreviatura'); ?>
                    <span class="form-control"><?= $abbreviation; ?></span>
                </div>
                <div class="col-md-12 mb-3">
                    <?= Form::label('Descripción'); ?>
                    <span class="form-control"><?= $description; ?></span>
                </div>
                <div class="col-md-4 mb-3">
                    <?= Form::label('Factor de Conversión'); ?>
                    <span class="form-control"><?= $conversion_factor; ?></span>
                </div>
                <div class="col-md-4 mb-3">
                    <?= Form::label('Origen'); ?>
                    <span class="form-control">
                        <?= $is_internal ? '<span class="badge badge-info">Interna</span>' : '<span class="badge badge-secondary">SAT</span>'; ?>
                    </span>
                </div>
                <div class="col-md-4 mb-3">
                    <?= Form::label('Estado'); ?>
                    <span class="form-control">
                        <?= $active ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-danger">Inactiva</span>'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
