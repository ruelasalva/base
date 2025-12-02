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
                            <li class="breadcrumb-item active" aria-current="page">Consentimientos</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/consentimientos/agregar', 'Agregar', ['class'=>'btn btn-sm btn-neutral']); ?>
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

                    <!-- BUSCADOR ESTÁNDAR -->
                    <div class="card-header border-0">
                        <?php echo Form::open(['action' => 'admin/legal/buscar', 'method' => 'post']); ?>
                        <div class="form-row">
                            <div class="col-md-9">
                                <h3 class="mb-0">Lista de Documentos Legales</h3>
                            </div>
                            <div class="col-md-3 mb-0">
                                <div class="input-group input-group-sm mt-3 mt-md-0">
                                    <?php echo Form::input('search', (isset($search) ? $search : ''), [
                                        'id' => 'search',
                                        'class' => 'form-control',
                                        'placeholder' => 'Título, categoría o shortcode',
                                        'aria-describedby' => 'button-addon'
                                    ]); ?>
                                    <div class="input-group-append">
                                        <?php echo Form::submit(['value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php echo Form::close(); ?>
                    </div>


                    <!-- TABLA -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Canal</th>
                                    <th>IP</th>
                                    <th>Fecha</th>
                                    <th style="width:180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($consents)): ?>
                                    <?php foreach ($consents as $c): ?>
                                    <tr>
                                        <td><?php echo  Html::anchor('admin/legal/consentimientos/info/'.$c['id'] ,$c['id']); ?></td>
                                        <td><?php echo $c['user']; ?></td>
                                        <td><?php echo $c['email']; ?></td>
                                        <td><?php echo $c['document']; ?></td>
                                        <td>
                                            <?php echo $c['estado'] == 'Aceptado'
                                                ? '<span class="badge badge-success">Aceptado</span>'
                                                : '<span class="badge badge-danger">Rechazado</span>'; ?>
                                        </td>
                                        <td><?php echo $c['channel']; ?></td>
                                        <td><?php echo $c['ip']; ?></td>
                                        <td><?php echo $c['fecha']; ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/legal/consentimientos/info/'.$c['id'], 'Ver detalle', ['class'=>'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/legal/consentimientos/infopendiente/'.$c['id'], 'Pendientes de usuario', ['class'=>'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/legal/consentimientos/eliminar/'.$c['id'], 'Eliminar', [
                                                        'class'=>'dropdown-item text-danger',
                                                        'onclick'=>"return confirm('¿Seguro que deseas eliminar este consentimiento?');"
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No hay consentimientos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINACIÓN -->
                    <?php if ($pagination): ?>
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
