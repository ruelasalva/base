<!-- ================================================ -->
<!-- PLANTILLAS ML – LISTADO                          -->
<!-- ================================================ -->

<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Plantillas de Descripción ML</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/plataforma/ml', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/plataforma/ml/panel/'.$config->id, 'Panel: '.$config->name); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Plantillas</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor(
                        'admin/plataforma/ml/plantillas/agregar?config_id='.$config->id,
                        '<i class="fa-solid fa-plus"></i> Nueva plantilla',
                        ['class' => 'btn btn-success']
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================ -->
<!-- LISTADO                                           -->
<!-- ================================================ -->

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">

            <div class="card shadow">
                <div class="card-header">
                    <h3 class="mb-0">Plantillas registradas</h3>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th style="width:160px;">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php if (count($plantillas) === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No hay plantillas registradas.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($plantillas as $p): ?>
                            <tr>
                                <td><?php echo $p->id; ?></td>
                                <td><?php echo $p->name; ?></td>
                                <td>
                                    <?php if ($p->is_active): ?>
                                        <span class="badge badge-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactiva</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo Html::anchor(
                                        'admin/plataforma/ml/plantillas/editar/'.$p->id,
                                        '<i class="fa-solid fa-pen"></i>',
                                        ['class' => 'btn btn-sm btn-primary']
                                    ); ?>

                                    <?php echo Html::anchor(
                                        'admin/plataforma/ml/plantillas/eliminar/'.$p->id,
                                        '<i class="fa-solid fa-trash"></i>',
                                        [
                                            'class' => 'btn btn-sm btn-danger',
                                            'onclick' => "return confirm('¿Eliminar plantilla?');"
                                        ]
                                    ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
