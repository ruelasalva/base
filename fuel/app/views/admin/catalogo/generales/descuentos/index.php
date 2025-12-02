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
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/catalogo/generales/descuentos', 'Descuentos'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/descuentos/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">
                <!-- CARD HEADER -->
                <div class="card-header border-0">
                    <?php echo Form::open(array('action' => 'admin/catalogo/generales/descuentos/buscar', 'method' => 'post')); ?>
                    <div class="form-row">
                        <div class="col-md-9">
                            <h3 class="mb-0">Lista de descuentos</h3>
                        </div>
                        <div class="col-md-3 mb-0">
                            <div class="input-group input-group-sm mt-3 mt-md-0">
                                <?php echo Form::input('search', (isset($search) ? $search : ''), array(
                                    'id' => 'search',
                                    'class' => 'form-control',
                                    'placeholder' => 'Nombre o estructura',
                                    'aria-describedby' => 'button-addon'
                                )); ?>
                                <div class="input-group-append">
                                    <?php echo Form::submit(array(
                                        'value'=> 'Buscar',
                                        'name'=>'submit',
                                        'id' => 'button-addon',
                                        'class' => 'btn btn-outline-primary'
                                    )); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Estructura</th>
                                <th>Tipo</th>
                                <th>% Final</th>
                                <th>Activo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($discounts)): ?>
                                <?php foreach($discounts as $d): ?>
                                    <tr>
                                        <td>
                                            <?php echo Html::anchor('admin/catalogo/generales/descuentos/info/'.$d->id, $d->name); ?>
                                        </td>
                                        <td><?php echo $d->structure; ?></td>
                                        <td><?php echo ucfirst($d->type); ?></td>
                                        <td><?php echo ($d->final_effective !== null) ? number_format($d->final_effective, 2).'%' : '-'; ?></td>
                                        <td><?php echo $d->active ? 'Sí' : 'No'; ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/catalogo/generales/descuentos/info/'.$d->id, 'Ver', array('class' => 'dropdown-item')); ?>
                                                    <?php echo Html::anchor('admin/catalogo/generales/descuentos/editar/'.$d->id, 'Editar', array('class' => 'dropdown-item')); ?>
                                                    <div class="dropdown-divider"></div>
                                                    <?php echo Html::anchor('admin/catalogo/generales/descuentos/eliminar/'.$d->id, 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No existen registros</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($pagination != ''): ?>
                    <!-- CARD FOOTER -->
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
