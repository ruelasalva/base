<!-- HEADER CON ESTILO UNIFORME -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Contrarecibos</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras', 'Compras'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Contrarecibos
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/compras/contrarecibos/agregar', '<i class="fas fa-plus"></i> Nuevo Contrarecibo', ['class'=>'btn btn-sm btn-neutral']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO DE LA PÁGINA -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">
                <!-- CARD HEADER CON FILTROS -->
                <div class="card-header border-0">
                    <?php echo Form::open(array('action' => 'admin/compras/contrarecibos/index_contrarecibo', 'method' => 'get')); ?>
                    <div class="form-row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h3 class="mb-0">Lista de Contrarecibos</h3>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <?php echo Form::input('search', $search ?? '', ['class'=>'form-control form-control-sm', 'placeholder'=>'Buscar proveedor, factura, OC']); ?>
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                            <?php
                            $status_options = [
                                ''          => 'Estatus',
                                'pendiente' => 'Pendiente',
                                'autorizado' => 'Autorizado',
                                'pagado'    => 'Pagado',
                            ];
                            echo Form::select('estatus', $estatus ?? '', $status_options, ['class'=>'form-control form-control-sm']);
                            ?>
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                            <?php echo Form::input('fecha', $fecha ?? '', ['class'=>'form-control form-control-sm', 'type'=>'date', 'placeholder'=>'Fecha de recepción']); ?>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-sm btn-primary btn-block" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>

                <!-- TABLA DE CONTRARECIBOS -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nº Contrarecibo</th>
                                <th scope="col">Proveedor</th>
                                <th scope="col">Factura(s)</th>
                                <th scope="col">OC(s)</th>
                                <th scope="col">Total</th>
                                <th scope="col">Fecha Recepción</th>
                                <th scope="col">Fecha de Pago Estimada</th>
                                <th scope="col">Estatus</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($contrarecibos)): ?>
                                <?php foreach ($contrarecibos as $cr): ?>
                                    <tr>
                                        <td><?php echo $cr->id; ?></td>
                                        <td><?php echo $cr->receipt_number ?? 'N/A'; ?></td>
                                        <td>
                                            <?php echo $cr->provider->company_name ?? ''; ?>
                                            <span class="small text-muted">(<?php echo $cr->provider->rfc ?? ''; ?>)</span>
                                        </td>
                                        <td>
                                            <?php if (!empty($cr->details)): ?>
                                                <?php
                                                $uuids = [];
                                                foreach ($cr->details as $detail) {
                                                    if ($detail->bill && $detail->bill->uuid) {
                                                        $uuids[] = $detail->bill->uuid;
                                                    }
                                                }
                                                $unique_uuids = array_unique($uuids);
                                                ?>
                                                <?php if (count($unique_uuids) > 1): ?>
                                                    <span class="badge badge-info" title="Contiene <?= count($unique_uuids) ?> facturas">Múltiples</span>
                                                <?php elseif (count($unique_uuids) == 1): ?>
                                                    <?php echo $unique_uuids[0]; ?>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($cr->details)): ?>
                                                <?php
                                                $oc_codes = [];
                                                foreach ($cr->details as $detail) {
                                                    if ($detail->bill && $detail->bill->order && $detail->bill->order->code_order) {
                                                        $oc_codes[] = $detail->bill->order->code_order;
                                                    }
                                                }
                                                $unique_oc_codes = array_unique($oc_codes);
                                                ?>
                                                <?php if (count($unique_oc_codes) > 1): ?>
                                                    <span class="badge badge-info" title="Contiene <?= count($unique_oc_codes) ?> órdenes de compra">Múltiples</span>
                                                <? elseif (count($unique_oc_codes) == 1): ?>
                                                    <?php echo $unique_oc_codes[0]; ?>
                                                <?php else: ?>
                                                    Sin OC
                                                <?php endif; ?>
                                            <?php else: ?>
                                                Sin OC
                                            <?php endif; ?>
                                        </td>
                                        <td>$<?php echo number_format($cr->total ?? 0, 2, '.', ','); ?></td>
                                        <td><?php echo date('d/m/Y', $cr->receipt_date); ?></td>
                                        <td><?php echo $cr->payment_date ? date('d/m/Y', $cr->payment_date) : '<span class="badge badge-warning">Pendiente</span>'; ?></td>
                                        <td>
                                            <?php echo $badges[$cr->id]; ?>
                                        </td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/compras/contrarecibos/info/'.$cr->id, 'Ver Detalles', ['class'=>'dropdown-item']); ?>
                                                    <?php //if($status == 'pendiente'): ?>
                                                        <?php echo Html::anchor('admin/compras/contrarecibos/editar/'.$cr->id, 'Editar', ['class'=>'dropdown-item']); ?>
                                                        <!-- Aquí podrías añadir más acciones como "Autorizar", "Marcar como pagado" si aplica -->
                                                    <?php //endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="10" class="text-center text-muted">No hay contrarecibos registrados que coincidan con los filtros.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($pagination)): ?>
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
t>
