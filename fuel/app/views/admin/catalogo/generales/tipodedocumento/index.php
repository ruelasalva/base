<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Tipos de Documento</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?= Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Catálogo - Tipos de Documento</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/catalogo/generales/tipodedocumento/agregar', '<i class="fas fa-plus"></i> Agregar Tipo', ['class' => 'btn btn-success']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
    <div class="card shadow">
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-folder"></i> Catálogo de Tipos de Documento</h3>
        </div>

        <div class="card-body">

            <!-- FILTROS -->
            <?= Form::open(['method' => 'get', 'class' => 'form-inline mb-3']); ?>
                <div class="form-row w-100">
                    <div class="col-md-5 mb-2">
                        <?= Form::input('search', $search, ['class' => 'form-control w-100', 'placeholder' => 'Buscar por nombre']); ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <?= Form::select('scope', $scope, [
                            '' => 'Todos los ámbitos',
                            'provider' => 'Proveedor',
                            'customer' => 'Cliente',
                            'internal' => 'Interno',
                            'general' => 'General'
                        ], ['class' => 'form-control w-100']); ?>
                    </div>
                    <div class="col-md-3 mb-2 text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            <?= Form::close(); ?>

            <!-- TABLA -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Ámbito</th>
                            <th>Activo</th>
                            <th>Creado</th>
                            <th>Actualizado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tipos): ?>
                            <?php foreach ($tipos as $t): ?>
                                <tr>
                                    <td><?= $t->name; ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= ucfirst($t->scope); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($t->active): ?>
                                            <span class="badge badge-success">Sí</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $t->created_at ? date('d/m/Y', $t->created_at) : '-'; ?></td>
                                    <td><?= $t->updated_at ? date('d/m/Y', $t->updated_at) : '-'; ?></td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <?= Html::anchor('admin/catalogo/generales/tipodedocumento/editar/'.$t->id, 'Editar', ['class' => 'dropdown-item']); ?>
                                                <?= Html::anchor('admin/catalogo/generales/tipodedocumento/eliminar/'.$t->id, 'Eliminar', ['class' => 'dropdown-item', 'onclick'=>"return confirm('¿Eliminar este tipo de documento?');"]); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay tipos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <?php if (!empty($pagination)): ?>
                <div class="card-footer py-4">
                    <?= $pagination; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
