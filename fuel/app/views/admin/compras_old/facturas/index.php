<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Compras</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/compras', 'Facturas'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/compras/facturas/subir_multiple', 'Subir Multiples Facturas', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/compras/facturas/subir_factura', 'Subir Factura', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                    <?php echo Form::open(array('action' => 'admin/compras/index', 'method' => 'post')); ?>
                    <div class="form-row">
                        <div class="col-md-9">
                            <h3 class="mb-0">Lista de Facturas</h3>
                        </div>
                        <div class="col-md-3 mb-0">
                            <div class="input-group input-group-sm mt-3 mt-md-0">
                                <?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'UUID o Proveedor', 'aria-describedby' => 'button-addon')); ?>
                                <div class="input-group-append">
                                    <?php echo Form::submit(array('value' => 'Buscar', 'name' => 'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>

                <!-- LIGHT TABLE -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">UUID</th>
                                <th scope="col">Proveedor</th>
                                <th scope="col">Total</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha <br> de carga</th>
                                <th scope="col">Fecha <br> de pago</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($facturas)): ?>
                                <?php foreach ($facturas as $factura): ?>
                                    <tr>
                                        <th>
                                            <?php echo Html::anchor('admin/compras/facturas/info/' . $factura['id'], $factura['id']); ?>
                                        </th>
                                        <td><?php echo $factura['uuid']; ?></td>
                                        <td><?php echo $factura['provider']; ?></td>
                                        <td><?php echo $factura['total']; ?></td>
                                        <td><?php echo $factura['status_badge']; ?></td>
                                        <td><?php echo $factura['created_at']; ?></td>
                                        <td><?php echo $factura['payment_date']; ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/compras/facturas/info/' . $factura['id'], 'Ver Detalles', array('class' => 'dropdown-item')); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <th scope="row" colspan="7">No existen registros</th>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pagination != ''): ?>
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
