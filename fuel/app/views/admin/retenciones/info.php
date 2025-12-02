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
                                <?php echo Html::anchor('admin/formas_retenciones', 'Retenciones'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/formas_retenciones/info/'.$id, $code); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/formas_retenciones/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                    <!-- CARD HEADER -->
                    <div class="card-header">
                        <h3 class="mb-0">Ver información</h3>
                    </div>
                    <!-- CARD BODY -->
                    <div class="card-body">
                        <fieldset>
                            <div class="form-row">
                                <div class="col-md-12 mt-0 mb-3">
                                    <legend class="mb-0 heading">Información de la retención</legend>
                                </div>
                                <!-- Código -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Código', 'code', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $code; ?></span>
                                    </div>
                                </div>
                                <!-- Descripción -->
                                <div class="col-md-8 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Descripción', 'description', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $description; ?></span>
                                    </div>
                                </div>
                                <!-- Tipo -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Tipo', 'type', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $type; ?></span>
                                    </div>
                                </div>
                                <!-- Categoría -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Categoría', 'category', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $category; ?></span>
                                    </div>
                                </div>
                                <!-- Vigencia desde -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Vigencia desde', 'valid_from', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo ($valid_from != '' && $valid_from != null) ? date('d/m/Y', strtotime($valid_from)) : '-'; ?></span>
                                    </div>
                                </div>
                                <!-- Tipo de base -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Tipo de base', 'base_type', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $base_type; ?></span>
                                    </div>
                                </div>
                                <!-- Tasa -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Tasa (%)', 'rate', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo ($rate !== null) ? number_format($rate, 5) : '-'; ?></span>
                                    </div>
                                </div>
                                <!-- Cuenta contable -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Cuenta contable', 'account', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $account; ?></span>
                                    </div>
                                </div>
                                <!-- Tipo de factor -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Tipo de factor', 'factor_type', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo $factor_type; ?></span>
                                    </div>
                                </div>
                                <!-- Creado -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Creado en', 'created_at', array('class' => 'form-control-label')); ?>
                                        <span class="form-control"><?php echo (!empty($created_at)) ? date('d/m/Y H:i', $created_at) : '-'; ?></span>
                                    </div>
                                </div>
                                <!-- Actualizado -->
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
