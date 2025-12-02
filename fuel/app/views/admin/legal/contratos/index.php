<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Contratos Legales</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?= Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Contratos Legales</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/legal/contratos/agregar', '<i class="fas fa-plus-circle"></i> Nuevo Contrato', ['class' => 'btn btn-sm btn-neutral']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">

    <?php if (Session::get_flash('error')): ?>
        <div class="alert alert-danger"><?= Session::get_flash('error'); ?></div>
    <?php endif; ?>

    <?php if (Session::get_flash('success')): ?>
        <div class="alert alert-success"><?= Session::get_flash('success'); ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header border-0">
            <h3 class="mb-0"><i class="fas fa-file-contract text-info"></i> Listado de Contratos</h3>
        </div>

        <!-- FILTROS -->
        <div class="card-body border-bottom pb-3">
            <?= Form::open(['method' => 'get', 'class' => 'form-row align-items-end']); ?>

                <div class="col-md-4 mb-2">
                    <?= Form::label('Buscar por título o código', 'search', ['class' => 'form-control-label']); ?>
                    <?= Form::input('search', Input::get('search', ''), ['class' => 'form-control', 'placeholder' => 'Ej. Contrato proveedor']); ?>
                </div>

                <div class="col-md-3 mb-2">
                    <?= Form::label('Categoría', 'category', ['class' => 'form-control-label']); ?>
                    <?= Form::select('category', Input::get('category', ''), [
                        '' => 'Todas',
                        'provider' => 'Proveedor',
                        'employee' => 'Empleado',
                        'customer' => 'Cliente',
                        'external' => 'Externo'
                    ], ['class' => 'form-control']); ?>
                </div>

                <div class="col-md-3 mb-2">
                    <?= Form::label('Estatus', 'status', ['class' => 'form-control-label']); ?>
                    <?= Form::select('status', Input::get('status', ''), [
                        '' => 'Todos',
                        '0' => 'Borrador',
                        '1' => 'Activo',
                        '2' => 'Vencido',
                        '3' => 'Cancelado'
                    ], ['class' => 'form-control']); ?>
                </div>

                <div class="col-md-2 mb-2 text-right">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>

            <?= Form::close(); ?>
        </div>

        <!-- TABLA -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th>Usuario</th>
                        <th>Tipo Documento</th>
                        <th>Vigencia</th>
                        <th>Estatus</th>
                        <th>Global</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($contratos)): ?>
                    <?php foreach ($contratos as $c): ?>
                        <tr>
                            <td><?= $c->id; ?></td>
                            <td><?= Html::anchor('admin/legal/contratos/info/'.$c->id, e(Str::truncate($c->title, 40))); ?></td>
                            <td><?= e($c->code ?: '---'); ?></td>
                            <td>
                                <?php
                                    $cats = [
                                        'provider' => 'Proveedor',
                                        'employee' => 'Empleado',
                                        'customer' => 'Cliente',
                                        'external' => 'Externo'
                                    ];
                                    echo e($cats[$c->category] ?? ucfirst($c->category));
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($c->user)): ?>
                                    <i class="fas fa-user"></i> <?= e(Str::truncate($c->user->username, 20)); ?>
                                <?php else: ?>
                                    <em class="text-muted">No asignado</em>
                                <?php endif; ?>
                            </td>
                            <td><?= !empty($c->type) ? e($c->type->name) : '<span class="text-muted">---</span>'; ?></td>
                            <td>
                                <?php
                                    $inicio = $c->start_date ? date('d/m/Y', strtotime($c->start_date)) : '---';
                                    $fin    = $c->end_date ? date('d/m/Y', strtotime($c->end_date)) : '---';
                                    echo "{$inicio} <br><small class='text-muted'>al</small><br> {$fin}";
                                ?>
                            </td>
                            <td><?= Model_Legal_Contract::status_label($c->status); ?></td>
                            <td>
                                <?php if ($c->is_global): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?= Html::anchor('admin/legal/contratos/info/'.$c->id, '<i class="fas fa-eye"></i>', ['class' => 'btn btn-sm btn-info', 'title' => 'Ver']); ?>
                                <?= Html::anchor('admin/legal/contratos/editar/'.$c->id, '<i class="fas fa-edit"></i>', ['class' => 'btn btn-sm btn-warning', 'title' => 'Editar']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10" class="text-center text-muted">No se encontraron contratos registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <?php if (isset($pagination)): ?>
        <div class="card-footer py-4">
            <nav aria-label="...">
                <?= $pagination; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
