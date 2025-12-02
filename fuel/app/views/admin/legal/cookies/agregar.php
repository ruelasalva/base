<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Preferencias de Cookies</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/legal/cookies', 'Cookies'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Agregar Preferencia</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/cookies', '<i class="fa-solid fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
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
                        <h3 class="mb-0"><i class="fa-solid fa-cookie-bite text-success"></i> Nueva Preferencia de Cookies</h3>
                    </div>

                    <div class="card-body">
                        <?php echo Form::open(['method'=>'post']); ?>

                        <!-- Usuario -->
                        <div class="form-group">
                            <?php echo Form::label('Usuario', 'user_id', ['class'=>'form-control-label']); ?>
                            <?php echo Form::select(
                                'user_id',
                                Input::post('user_id', isset($user_id) ? $user_id : ''),
                                $user_opts,
                                ['class'=>'form-control', 'data-toggle'=>'select']
                            ); ?>
                            <small class="form-text text-muted">Si es usuario logeado, selecciona aquí.</small>
                        </div>

                        <!-- Token (para invitados) -->
                        <div class="form-group">
                            <?php echo Form::label('Token invitado', 'token', ['class'=>'form-control-label']); ?>
                            <?php echo Form::input('token', Input::post('token'), [
                                'class'=>'form-control',
                                'placeholder'=>'Ej: cookie_abc123'
                            ]); ?>
                            <small class="form-text text-muted">Usar solo si no es usuario logeado.</small>
                        </div>

                        <!-- Analíticas -->
                        <div class="form-check mb-2">
                            <?php echo Form::checkbox('analytics', 0, false, ['id'=>'analytics','class'=>'form-check-input']); ?>
                            <?php echo Form::label('Aceptar cookies analíticas', 'analytics', ['class'=>'form-check-label']); ?>
                        </div>

                        <!-- Marketing -->
                        <div class="form-check mb-2">
                            <?php echo Form::checkbox('marketing', 0, false, ['id'=>'marketing','class'=>'form-check-input']); ?>
                            <?php echo Form::label('Aceptar cookies de marketing', 'marketing', ['class'=>'form-check-label']); ?>
                        </div>

                        <!-- Personalización -->
                        <div class="form-check mb-4">
                            <?php echo Form::checkbox('personalization', 0, false, ['id'=>'personalization','class'=>'form-check-input']); ?>
                            <?php echo Form::label('Aceptar cookies de personalización', 'personalization', ['class'=>'form-check-label']); ?>
                        </div>

                        <!-- Botones -->
                        <div class="form-group">
                            <?php echo Form::submit('guardar', 'Guardar Preferencia', ['class'=>'btn btn-primary']); ?>
                            <?php echo Html::anchor('admin/legal/cookies', 'Cancelar', ['class'=>'btn btn-secondary']); ?>
                        </div>

                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
