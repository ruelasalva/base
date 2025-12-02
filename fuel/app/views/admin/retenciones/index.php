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
                                <?php echo Html::anchor('admin/formas_retenciones', 'Retenciones'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/formas_retenciones/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                    <?php echo Form::open(array('action' => 'admin/formas_retenciones/buscar', 'method' => 'post')); ?>
                    <div class="form-row">
                        <div class="col-md-9">
                            <h3 class="mb-0">Lista de retenciones</h3>
                        </div>
                        <div class="col-md-3 mb-0">
                            <div class="input-group input-group-sm mt-3 mt-md-0">
                                <?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Descripción o código', 'aria-describedby' => 'button-addon')); ?>
                                <div class="input-group-append">
                                    <?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>
                <!-- LIGHT TABLE -->
                <div class="table-responsive" data-toggle="lists" data-list-values='["code", "description", "type", "category", "rate"]'>
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Código</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Categoría</th>
                                <th scope="col">Tasa (%)</th>
                                <th scope="col">Cuenta contable</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php if(!empty($retentions)): ?>
                                <?php foreach($retentions as $retention): ?>
                                    <tr>
                                        <td><?php echo $retention['code']; ?></td>
                                        <td><?php echo $retention['description']; ?></td>
                                        <td><?php echo $retention['type']; ?></td>
                                        <td><?php echo $retention['category']; ?></td>
                                        <td><?php echo ($retention['rate'] !== null) ? number_format($retention['rate'], 5) : '-'; ?></td>
                                        <td><?php echo $retention['account']; ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/formas_retenciones/info/'.$retention['id'], 'Ver', array('class' => 'dropdown-item')); ?>
                                                    <?php echo Html::anchor('admin/formas_retenciones/editar/'.$retention['id'], 'Editar', array('class' => 'dropdown-item')); ?>
                                                    <div class="dropdown-divider"></div>
                                                    <?php echo Html::anchor('admin/formas_retenciones/eliminar/'.$retention['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No existen registros</td>
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
