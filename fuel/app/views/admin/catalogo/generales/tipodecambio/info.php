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
                                <?php echo Html::anchor('admin/catalogo/generales/tipodecambio', 'Tipos de Cambio'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/info/'.$id, $currency . ' ' . $date); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                        <h3 class="mb-0">Ver información</h3>
                    </div>
                    <div class="card-body">
                        <fieldset>
                            <div class="form-row">
                                <div class="col-md-12 mt-0 mb-3">
                                    <legend class="mb-0 heading">Información del tipo de cambio</legend>
                                </div>
                                <!-- Moneda -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Moneda', 'currency', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $currency; ?></span>
                                    </div>
                                </div>
                                <!-- Fecha -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Fecha', 'date', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $date; ?></span>
                                    </div>
                                </div>
                                <!-- Tipo de Cambio -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Tipo de Cambio', 'rate', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo number_format($rate, 6); ?></span>
                                    </div>
                                </div>
                                <!-- Creado en -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Creado en', 'created_at', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo (!empty($created_at)) ? date('d/m/Y H:i', $created_at) : '-'; ?></span>
                                    </div>
                                </div>
                                <!-- Actualizado en -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Actualizado en', 'updated_at', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo (!empty($updated_at)) ? date('d/m/Y H:i', $updated_at) : '-'; ?></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
