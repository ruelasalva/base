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
                                <?php echo Html::anchor('admin/legal/cookies', 'Preferencias de Cookies'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
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
                        <h3 class="mb-0"><i class="fa-solid fa-cookie-bite text-primary"></i> Detalle Preferencias de Cookies</h3>
                    </div>

                    <div class="card-body">

                        <!-- Usuario / Visitante -->
                        <div class="form-group">
                            <?php echo Form::label('Usuario / Visitante', 'user_id'); ?>
                            <span class="form-control">
                                <?php 
                                if ($cookie->user_id && $cookie->user) {
                                    echo '<i class="fa-solid fa-user text-info"></i> ' . $cookie->user->username . ' (' . $cookie->user->email . ')';
                                } else {
                                    echo '<i class="fa-solid fa-user-secret text-muted"></i> Visitante Anónimo<br><small class="text-muted">Token: ' . ($cookie->token ?: 'N/A') . '</small>';
                                }
                                ?>
                            </span>
                        </div>

                        <!-- Analíticas -->
                        <div class="form-group">
                            <?php echo Form::label('Analíticas', 'analytics'); ?>
                            <span class="form-control">
                                <?php echo $cookie->analytics == 0 
                                    ? '<span class="badge badge-success">Aceptadas</span>' 
                                    : '<span class="badge badge-danger">Rechazadas</span>'; ?>
                            </span>
                        </div>

                        <!-- Marketing -->
                        <div class="form-group">
                            <?php echo Form::label('Marketing', 'marketing'); ?>
                            <span class="form-control">
                                <?php echo $cookie->marketing == 0 
                                    ? '<span class="badge badge-success">Aceptadas</span>' 
                                    : '<span class="badge badge-danger">Rechazadas</span>'; ?>
                            </span>
                        </div>

                        <!-- Personalización -->
                        <div class="form-group">
                            <?php echo Form::label('Personalización', 'personalization'); ?>
                            <span class="form-control">
                                <?php echo $cookie->personalization == 0 
                                    ? '<span class="badge badge-success">Aceptadas</span>' 
                                    : '<span class="badge badge-danger">Rechazadas</span>'; ?>
                            </span>
                        </div>

                        <!-- Datos Técnicos -->
                        <div class="form-group">
                            <?php echo Form::label('Dirección IP', 'ip_address'); ?>
                            <span class="form-control"><?php echo $cookie->ip_address ?: '<em class="text-muted">N/A</em>'; ?></span>
                        </div>

                        <div class="form-group">
                            <?php echo Form::label('User Agent', 'user_agent'); ?>
                            <div class="p-2 border rounded bg-light">
                                <?php echo $cookie->user_agent ?: '<em class="text-muted">N/A</em>'; ?>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <?php echo Form::label('Fecha aceptación', 'accepted_at'); ?>
                                <span class="form-control">
                                    <?php echo $cookie->accepted_at 
                                        ? Date::forge($cookie->accepted_at)->format('%d/%m/%Y %H:%M') 
                                        : '<em class="text-muted">N/A</em>'; ?>
                                </span>
                            </div>
                            <div class="form-group col-md-6">
                                <?php echo Form::label('Última actualización', 'updated_at'); ?>
                                <span class="form-control">
                                    <?php echo $cookie->updated_at 
                                        ? Date::forge($cookie->updated_at)->format('%d/%m/%Y %H:%M') 
                                        : '<em class="text-muted">N/A</em>'; ?>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
