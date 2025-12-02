<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-3">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Mercado Libre</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::base(true).'admin'; ?>"><i class="fas fa-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::base(true).'admin/plataforma'; ?>">Plataformas</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Mercado Libre</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor(
                        'admin/plataforma/ml/agregar',
                        '<i class="fa-solid fa-plus"></i> Nueva Cuenta',
                        ['class' => 'btn btn-neutral']
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid mt--6">

    <div class="row">

        <?php if (empty($configs)) : ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No hay cuentas configuradas. Agrega una nueva cuenta de Mercado Libre.
                </div>
            </div>
        <?php endif; ?>


        <?php foreach ($configs as $c): ?>
            <div class="col-xl-4 col-md-6">
                <div class="card card-stats shadow">

                    <div class="card-body">

                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">
                                    <?php echo e($c->name); ?>
                                </h5>

                                <?php if ($c->is_active): ?>
                                    <span class="badge badge-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Inactiva</span>
                                <?php endif; ?>

                                <br>

                                <span class="h3 font-weight-bold mb-0">
                                    <?php echo $c->account_email ? e($c->account_email) : 'Sin conectar'; ?>
                                </span>

                                <?php if ($c->access_token): ?>
                                    <div class="mt-2">
                                        <small class="text-success">
                                            Token válido hasta:
                                            <?php echo date('d/m/Y H:i', (int)$c->token_expires_at); ?>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-2">
                                        <small class="text-danger">Sin token válido</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                    <i class="fa-brands fa-mercadolibre"></i>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
    <div class="col-12 text-right">

        <!-- Panel -->
        <?php echo Html::anchor(
            'admin/plataforma/ml/panel/'.$c->id,
            '<i class="fa-solid fa-gauge"></i>',
            [
                'class' => 'btn btn-sm btn-default',
                'title' => 'Panel de esta tienda ML'
            ]
        ); ?>

        <!-- Productos -->
        <?php echo Html::anchor(
            'admin/plataforma/ml/productos?config_id='.$c->id,
            '<i class="fa-solid fa-boxes-stacked"></i>',
            [
                'class' => 'btn btn-sm btn-info',
                'title' => 'Productos de esta tienda ML'
            ]
        ); ?>

        <!-- Plantillas -->
        <?php echo Html::anchor(
            'admin/plataforma/ml/plantillas?config_id='.$c->id,
            '<i class="fa-solid fa-file-lines"></i>',
            [
                'class' => 'btn btn-sm btn-secondary',
                'title' => 'Plantillas de descripción ML'
            ]
        ); ?>


        <!-- Editar -->
        <?php echo Html::anchor(
            'admin/plataforma/ml/editar/'.$c->id,
            '<i class="fa-solid fa-pen"></i>',
            ['class' => 'btn btn-sm btn-primary']
        ); ?>

        <!-- Eliminar -->
        <?php echo Html::anchor(
            'admin/plataforma/ml/eliminar/'.$c->id,
            '<i class="fa-solid fa-trash"></i>',
            [
                'class' => 'btn btn-sm btn-danger',
                'onclick' => "return confirm('¿Eliminar esta configuración?');"
            ]
        ); ?>

        <!-- Conectar OAuth -->
        <?php if (empty($c->access_token)): ?>
            <?php echo Html::anchor(
                'admin/plataforma/ml/oauth/'.$c->id,
                '<i class="fa-brands fa-mercadolibre"></i> Conectar',
                ['class' => 'btn btn-sm btn-warning']
            ); ?>
        <?php endif; ?>

    </div>
</div>


                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>
