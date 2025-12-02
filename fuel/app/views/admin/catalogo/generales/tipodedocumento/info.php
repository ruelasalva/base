<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Detalle del Tipo de Documento</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?= Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?= Html::anchor('admin/catalogo/generales/tipodedocumento', 'Tipos de Documento'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Información</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/catalogo/generales/tipodedocumento', '<i class="fas fa-arrow-left"></i> Volver', ['class'=>'btn btn-secondary btn-sm']); ?>
                    <?= Html::anchor('admin/catalogo/generales/tipodedocumento/editar/'.$tipo->id, '<i class="fas fa-edit"></i> Editar', ['class'=>'btn btn-warning btn-sm']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <h3 class="mb-0"><i class="fas fa-info-circle text-info"></i> Información del Tipo</h3>
                </div>
                <div class="card-body">
                    <div class="row">

                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <?= Form::label('Nombre del tipo', '', ['class'=>'form-control-label font-weight-bold']); ?>
                            <div class="form-control bg-light"><?= $tipo->name ?: '-'; ?></div>
                        </div>

                        <!-- Ámbito -->
                        <div class="col-md-6 mb-3">
                            <?= Form::label('Ámbito', '', ['class'=>'form-control-label font-weight-bold']); ?>
                            <div class="form-control bg-light">
                                <span class="badge badge-info"><?= ucfirst($tipo->scope ?: 'General'); ?></span>
                            </div>
                        </div>

                        <!-- Activo -->
                        <div class="col-md-6 mb-3">
                            <?= Form::label('Activo', '', ['class'=>'form-control-label font-weight-bold']); ?>
                            <div class="form-control bg-light">
                                <?php if ($tipo->active): ?>
                                    <span class="badge badge-success">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Eliminado -->
                        <div class="col-md-6 mb-3">
                            <?= Form::label('Eliminado', '', ['class'=>'form-control-label font-weight-bold']); ?>
                            <div class="form-control bg-light">
                                <?php if ($tipo->deleted): ?>
                                    <span class="badge badge-danger">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-success">No</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="col-md-6 mb-3">
                            <?= Form::label('Creado el', '', ['class'=>'form-control-label font-weight-bold']); ?>
                            <div class="form-control bg-light"><?= $tipo->created_at ? date('d/m/Y H:i', $tipo->created_at) : '-'; ?></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <?= Form::label('Última actualización', '', ['class'=>'form-control-label font-weight-bold']); ?>
                            <div class="form-control bg-light"><?= $tipo->updated_at ? date('d/m/Y H:i', $tipo->updated_at) : '-'; ?></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
