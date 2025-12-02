<!-- HEADER y BREADCRUMB igual que siempre -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-8 col-7">
                    <h4 class="text-white mb-0 font-weight-bold">
                        Seguimiento REP (Facturas con pago 99)
                    </h4>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block mt-1">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin/compras', 'Compras'); ?></li>
                            <li class="breadcrumb-item active" aria-current="page">REP</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-4 col-5 text-right">
                    <?php echo Html::anchor('admin/compras/rep/agregar', '<i class="fa fa-plus"></i> Agregar REP', ['class' => 'btn btn-success']); ?>
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
                    <?php echo Form::open(['action' => 'admin/compras/rep/buscar', 'method' => 'post']); ?>
                    <div class="form-row">
                        <div class="col-md-9">
                            <h3 class="mb-0">Facturas que requieren REP</h3>
                        </div>
                        <div class="col-md-3 mb-0">
                            <div class="input-group input-group-sm mt-3 mt-md-0">
                                <?php echo Form::input('search', (isset($search) ? $search : ''), [
                                    'id' => 'search', 
                                    'class' => 'form-control', 
                                    'placeholder' => 'Folio, UUID o Proveedor', 
                                    'aria-describedby' => 'button-addon'
                                ]); ?>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit" id="button-addon">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>

                <!-- TABLA PRINCIPAL -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>Folio</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estatus</th>
                                <th>REP Asociados</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bills)): ?>
                                <?php foreach($bills as $bill): ?>
                                    <tr>
                                        <td><?php echo $bill['uuid'] ?: '---'; ?></td>
                                        <td><?php echo $bill['provider'] ?: '<span class="text-muted">Sin proveedor</span>'; ?></td>
                                        <td><?php echo $bill['created_at'] ?: '---'; ?></td>
                                        <td class="text-right"><?php echo $bill['total'] ?: '$0.00'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $bill['badge_color']; ?>">
                                                <?php echo $bill['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            if (!empty($bill['has_rep']) && !empty($bill['rep_badges'])) {
                                                echo $bill['rep_badges'];
                                            } else {
                                                echo '<span class="badge badge-danger">Sin REP</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right">
                                            <?php
                                            // Si ya tiene REP y cubre el total
                                            if (!empty($bill['has_rep']) && !empty($bill['rep_completo']) && $bill['rep_completo'] === true) {
                                                // Mostrar botón Editar REP (estilo amarillo)
                                                echo Html::anchor(
                                                    'admin/compras/rep/editar/' . $bill['rep_ids'][0],
                                                    '<i class="fa fa-edit"></i> Editar REP',
                                                    ['class' => 'btn btn-warning btn-sm', 'title' => 'Editar REP existente']
                                                );
                                            } 
                                            // Si ya tiene REP pero no cubre todo (puede agregar más)
                                            elseif (!empty($bill['has_rep'])) {
                                                echo Html::anchor(
                                                    'admin/compras/rep/agregar?bill_id=' . $bill['id'],
                                                    '<i class="fa fa-plus"></i> Agregar REP adicional',
                                                    ['class' => 'btn btn-success btn-sm', 'title' => 'Agregar REP adicional']
                                                );

                                                // Mostrar también los botones Ver REP existentes
                                                foreach ($bill['rep_ids'] as $rep_id) {
                                                    echo Html::anchor(
                                                        'admin/compras/rep/info/' . $rep_id,
                                                        '<i class="fa fa-eye"></i> Ver REP',
                                                        ['class' => 'btn btn-info btn-sm ml-1', 'title' => 'Ver detalle del REP']
                                                    );
                                                }
                                            } 
                                            // Si no tiene ningún REP todavía
                                            else {
                                                echo Html::anchor(
                                                    'admin/compras/rep/agregar?bill_id=' . $bill['id'],
                                                    '<i class="fa fa-upload"></i> Agregar REP',
                                                    ['class' => 'btn btn-success btn-sm', 'title' => 'Asociar REP a esta factura']
                                                );
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No existen facturas pendientes de REP.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <?php if (!empty($pagination)): ?>
                    <div class="card-footer py-4">
                        <div class="d-flex justify-content-center">
                            <?php echo $pagination; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
