<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Órdenes de Compra</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras', 'Compras'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Órdenes de Compra
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <a href="<?= Uri::create('admin/compras/ordenes/agregar'); ?>" class="btn btn-success">Agregar Orden</a>
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
                    <?php echo Form::open(['action' => 'admin/compras/ordenes/index', 'method' => 'get']); ?>
                    <div class="form-row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label><strong>Buscar</strong></label>
                            <div class="input-group input-group-sm">
                                <?php
                                echo Form::input('search', Input::get('search', ''), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Código OC o Proveedor'
                                ]);
                                ?>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label><strong>Estatus</strong></label>
                            <?php
                            $status_opts = ['' => 'Todos'];
                            foreach ($status_list as $key => $label) {
                                $status_opts[$key] = $label;
                            }
                            echo Form::select('status', Input::get('status', ''), $status_opts, [
                                'class' => 'form-control form-control-sm'
                            ]);
                            ?>
                        </div>


                        <div class="col-md-2 mb-2">
                            <label><strong>Desde</strong></label>
                            <?php
                            $year = date('Y');
                            echo Form::input('fecha_desde', Input::get('fecha_desde', "$year-01-01"), [
                                'type' => 'date',
                                'class' => 'form-control form-control-sm'
                            ]);
                            ?>
                        </div>

                        <div class="col-md-2 mb-2">
                            <label><strong>Hasta</strong></label>
                            <?php
                            echo Form::input('fecha_hasta', Input::get('fecha_hasta', "$year-12-31"), [
                                'type' => 'date',
                                'class' => 'form-control form-control-sm'
                            ]);
                            ?>
                        </div>

                        <div class="col-md-2 mb-2 text-right">
                            <label>&nbsp;</label><br>
                            <?php echo Form::submit(['value' => 'Filtrar', 'name' => 'submit', 'class' => 'btn btn-primary btn-sm']); ?>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>

                <!-- LIGHT TABLE -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Código OC</th>
                                <th>Proveedor</th>
                                <th>Moneda</th>
                                <th>Fecha Registro</th>
                                <th>Fecha Compra</th>
                                <th>Estatus</th>
                                <th>Facturas</th>
                                <th>Autorizado por</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Facturado</th>
                                <th class="text-right">Saldo</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ordenes)): ?>
                                <?php foreach ($ordenes as $oc): ?>
                                    <tr>
                                        <td><?php echo $oc['id']; ?></td>
                                        <td>
                                            <?php echo Html::anchor('admin/compras/ordenes/info/' . $oc['id'], '<strong>' . $oc['code_order'] . '</strong>'); ?>
                                            <?php if (strip_tags($oc['status']) == 'Autorizada'): ?>
                                                <i class="fas fa-check-circle text-success" title="Autorizada"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $oc['provider']; ?></td>
                                        <td><?php echo $oc['currency_label']; ?></td>
                                        <td><?php echo $oc['created_at']; ?></td>
                                        <td><?php echo $oc['date_order']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $oc['badge_color']; ?>">
                                                <?php echo $oc['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $oc['facturas_count']; ?></td>
                                        <td><?php echo $oc['authorized_by'] ?? '<span class="text-muted">---</span>'; ?></td>
                                        <td class="text-right"><?php echo '$' . $oc['total']; ?></td>
                                        <td class="text-right">
                                            <?php echo isset($oc['invoiced_total']) ? '$' . number_format($oc['invoiced_total'], 2) : '<span class="text-muted">--</span>'; ?>
                                        </td>
                                        <td class="text-right">
                                            <?php echo isset($oc['balance_total']) ? '$' . number_format($oc['balance_total'], 2) : '<span class="text-muted">--</span>'; ?>
                                        </td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/compras/ordenes/info/' . $oc['id'], 'Ver Detalles', ['class' => 'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/compras/ordenes/editar/' . $oc['id'], 'Editar', ['class' => 'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/compras/facturas/subir_factura/' . $oc['id'], 'Subir Factura', ['class' => 'dropdown-item']); ?>
                                                    <?php echo Html::anchor('admin/compras/ordenes/eliminar/' . $oc['id'], 'Eliminar', ['class' => 'dropdown-item', 'onclick' => "return confirm('¿Eliminar la orden?');"]); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <th colspan="12" class="text-center">No existen órdenes registradas.</th>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pagination) && $pagination != ''): ?>
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
