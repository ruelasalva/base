<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">
                        <i class="fa-brands fa-mercadolibre"></i> Panel Mercado Libre
                    </h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin'); ?>">
                                    <i class="fas fa-home"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin/plataforma/ml'); ?>">Mercado Libre</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Panel – <?php echo e($config->name); ?>
                            </li>
                        </ol>
                    </nav>
                </div>

                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor(
                        'admin/plataforma/ml/productos?config_id='.$config->id,
                        '<i class="fa-solid fa-boxes-stacked"></i> Productos',
                        ['class' => 'btn btn-neutral']
                    ); ?>

                    <?php echo Html::anchor(
                        'admin/plataforma/ml/sync/'.$config->id,
                        '<i class="fa-solid fa-rotate"></i> Sincronizar ahora',
                        ['class' => 'btn btn-warning']
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid mt--6">

    <!-- RESUMEN DE CUENTA -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Modo</h5>
                            <span class="h3 font-weight-bold mb-0 text-white">
                                <span class="badge badge-info text-uppercase">
                                    <?php echo e($config->mode); ?>
                                </span>
                            </span>
                            <br>
                            <small class="text-white-50">
                                <?php echo $config->is_active ? 'Cuenta activa' : 'Cuenta inactiva'; ?>
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="fa-brands fa-mercadolibre"></i>
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-nowrap">
                            Token:
                            <?php if ($config->access_token): ?>
                                <span class="text-success">
                                    válido hasta <?php echo date('d/m/Y H:i', (int) $config->token_expires_at); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-danger">no configurado</span>
                            <?php endif; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- KPI: Productos vinculados -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Productos vinculados</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo (int) $total_products; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                <i class="fa-solid fa-link"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm text-muted">
                        <span class="text-nowrap">Relación ERP ↔ ML</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- KPI: Publicados -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Publicados activos</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo (int) $published; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm text-muted">
                        <span class="text-nowrap">Con item_id en ML y habilitados</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- KPI: Errores 7 días -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Errores últimos 7 días</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo (int) $errors_7d; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm text-muted">
                        <span class="text-nowrap">Monitoreo de integridad</span>
                    </p>
                </div>
            </div>
        </div>
    </div>


    <!-- ESTADO DETALLADO Y ACCESOS -->
    <div class="row">

        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Estado de catálogo</h3>
                </div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Vinculados sin publicar:</strong><br>
                            <?php echo (int) $linked_only; ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Publicados activos:</strong><br>
                            <?php echo (int) $published; ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Inactivos ML:</strong><br>
                            <?php echo (int) $inactive; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <?php echo Html::anchor(
                                'admin/plataforma/ml/productos/sin_publicar?config_id='.$config->id,
                                '<i class="fa-solid fa-boxes-stacked"></i> Ver productos',
                                ['class' => 'btn btn-sm btn-primary btn-block']
                            ); ?>
                        </div>
                        <div class="col-md-4 mb-2">
                            <?php echo Html::anchor(
                                'admin/plataforma/ml/logs?config_id='.$config->id,
                                '<i class="fa-solid fa-list-ul"></i> Logs',
                                ['class' => 'btn btn-sm btn-info btn-block']
                            ); ?>
                        </div>
                        <div class="col-md-4 mb-2">
                            <?php echo Html::anchor(
                                'admin/plataforma/ml/errores?config_id='.$config->id,
                                '<i class="fa-solid fa-bug"></i> Errores',
                                ['class' => 'btn btn-sm btn-danger btn-block']
                            ); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ÚLTIMOS LOGS -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header border-0">
                    <h3 class="mb-0">Últimas operaciones</h3>
                </div>
                <div class="card-body">

                    <?php if ($last_logs): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($last_logs as $log): ?>
                                <li class="list-group-item px-0">
                                    <small class="d-block text-muted">
                                        <?php echo date('d/m/Y H:i', (int) $log->created_at); ?>
                                    </small>
                                    <strong><?php echo e($log->operation); ?></strong>
                                    <span class="text-muted">
                                        (<?php echo e($log->resource); ?> #<?php echo (int) $log->resource_id; ?>)
                                    </span>
                                    <br>
                                    <small class="<?php echo ($log->status >= 400 ? 'text-danger' : 'text-success'); ?>">
                                        <?php echo e(Str::truncate($log->message, 80)); ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">Sin registros recientes.</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
