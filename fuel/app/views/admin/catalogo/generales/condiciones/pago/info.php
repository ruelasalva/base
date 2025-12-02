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
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/catalogo/generales/condiciones/pago/info/'.$id, $code); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/condiciones/pago/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                                    <legend class="mb-0 heading">Información de la condición de pago</legend>
                                </div>
                                <!-- Código -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Código', 'code', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $code; ?></span>
                                    </div>
                                </div>
                                <!-- Nombre -->
                                <div class="col-md-8 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $name; ?></span>
                                    </div>
                                </div>
                                <!-- Tipo de Base -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Tipo de Base', 'base_date_type', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $base_date_type; ?></span>
                                    </div>
                                </div>
                                <!-- Días de Inicio -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Días de Inicio', 'start_offset_days', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo ($start_offset_days !== null) ? $start_offset_days : '-'; ?></span>
                                    </div>
                                </div>
                                <!-- Días de Tolerancia -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Días de Tolerancia', 'days_tolerance', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo ($days_tolerance !== null) ? $days_tolerance : '-'; ?></span>
                                    </div>
                                </div>
                                <!-- Parcialidades -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Parcialidades', 'installment_count', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo ($installment_count !== null) ? $installment_count : '-'; ?></span>
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
