<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Consentimientos</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/legal/consentimientos', 'Consentimientos'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/consentimientos', '<i class="fa-solid fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
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
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0"><i class="fa-solid fa-file-contract text-primary"></i> Detalle Consentimiento</h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <?php echo Form::label('Usuario', 'usuario'); ?>
                            <span class="form-control"><?php echo $consent['usuario']; ?></span>
                        </div>

                        <div class="form-group">
                            <?php echo Form::label('Email', 'email'); ?>
                            <span class="form-control"><?php echo $consent['email']; ?></span>
                        </div>

                        <div class="form-group">
                            <?php echo Form::label('Documento', 'documento'); ?>
                            <span class="form-control"><?php echo $consent['documento']; ?></span>
                        </div>

                        <div class="form-group">
                            <?php echo Form::label('Shortcode', 'shortcode'); ?>
                            <span class="form-control"><?php echo $consent['shortcode']; ?></span>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Estado', 'estado'); ?>
                                <span class="form-control">
                                    <?php echo $consent['estado'] == 'Aceptado'
                                        ? '<span class="badge badge-success">Aceptado</span>'
                                        : '<span class="badge badge-danger">Rechazado</span>'; ?>
                                </span>
                            </div>
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Canal', 'canal'); ?>
                                <span class="form-control"><?php echo $consent['canal']; ?></span>
                            </div>
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Fecha', 'fecha'); ?>
                                <span class="form-control"><?php echo $consent['fecha']; ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo Form::label('Dirección IP', 'ip'); ?>
                            <span class="form-control"><?php echo $consent['ip']; ?></span>
                        </div>

                        <div class="form-group">
                            <?php echo Form::label('User Agent', 'user_agent'); ?>
                            <div class="p-3 border rounded bg-light">
                                <small><?php echo $consent['user_agent']; ?></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo Form::label('Extra (JSON)', 'extra'); ?>
                            <div class="p-3 border rounded bg-light">
                                <pre class="mb-0"><?php echo $consent['extra']; ?></pre>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
