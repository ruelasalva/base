<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Recepción: <?= $receipt->receipt_number ?></h1>
    <div>
        <?php
        $badges = [
            'received' => '<span class="badge bg-info fs-5"><i class="fas fa-box"></i> Recibido</span>',
            'verified' => '<span class="badge bg-warning fs-5"><i class="fas fa-check-circle"></i> Verificado</span>',
            'posted' => '<span class="badge bg-success fs-5"><i class="fas fa-check-double"></i> Afectado</span>'
        ];
        echo $badges[$receipt->status] ?? $receipt->status;
        ?>
    </div>
</div>

<div class="row">
    <!-- Información de la recepción -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información de la Recepción</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Número:</dt>
                            <dd class="col-sm-7"><strong><?= $receipt->receipt_number ?></strong></dd>
                            
                            <dt class="col-sm-5">Fecha:</dt>
                            <dd class="col-sm-7"><?= date('d/m/Y', strtotime($receipt->receipt_date)) ?></dd>
                            
                            <dt class="col-sm-5">Proveedor:</dt>
                            <dd class="col-sm-7">
                                <a href="<?= Uri::create('admin/proveedores/view/' . $receipt->provider->id) ?>">
                                    <?= $receipt->provider->company_name ?>
                                </a>
                            </dd>
                            
                            <dt class="col-sm-5">Orden de Compra:</dt>
                            <dd class="col-sm-7">
                                <?php if ($receipt->purchase_order_id): ?>
                                    <a href="<?= Uri::create('admin/compras/ordenes/view/' . $receipt->purchase_order_id) ?>">
                                        <?= $receipt->purchase_order->code_order ?? 'N/A' ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Sin orden</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-sm-5">Almacén:</dt>
                            <dd class="col-sm-7">
                                <?= $receipt->warehouse->name ?? 'N/A' ?>
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Factura:</dt>
                            <dd class="col-sm-7"><?= $receipt->invoice_number ?: '-' ?></dd>
                            
                            <?php if ($receipt->invoice_date): ?>
                                <dt class="col-sm-5">Fecha Factura:</dt>
                                <dd class="col-sm-7"><?= date('d/m/Y', strtotime($receipt->invoice_date)) ?></dd>
                            <?php endif; ?>
                            
                            <dt class="col-sm-5">Recibido por:</dt>
                            <dd class="col-sm-7">
                                <?= $receipt->receiver->username ?? 'N/A' ?>
                            </dd>
                            
                            <dt class="col-sm-5">Total:</dt>
                            <dd class="col-sm-7">
                                <h4 class="text-success mb-0">
                                    $<?= number_format($receipt->total_amount, 2) ?>
                                </h4>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <?php if ($receipt->notes): ?>
                    <hr>
                    <div>
                        <strong>Notas:</strong>
                        <p class="mb-0"><?= nl2br(e($receipt->notes)) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Productos recibidos -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-boxes"></i> Productos Recibidos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th class="text-end">Ordenado</th>
                                <th class="text-end">Recibido</th>
                                <th class="text-center">Diferencia</th>
                                <th class="text-end">Costo Unit.</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">IVA</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_subtotal = 0;
                            $total_tax = 0;
                            $total_amount = 0;
                            
                            foreach ($details as $detail): 
                                $difference = $detail['quantity_received'] - $detail['quantity_ordered'];
                                $difference_class = $difference == 0 ? 'text-success' : ($difference > 0 ? 'text-primary' : 'text-danger');
                                
                                $total_subtotal += $detail['subtotal'];
                                $total_tax += $detail['tax_amount'];
                                $total_amount += $detail['total'];
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= $detail['product_name'] ?></strong>
                                        <?php if ($detail['lot_number']): ?>
                                            <br><small class="text-muted">Lote: <?= $detail['lot_number'] ?></small>
                                        <?php endif; ?>
                                        <?php if ($detail['expiration_date']): ?>
                                            <br><small class="text-muted">Cad: <?= date('d/m/Y', strtotime($detail['expiration_date'])) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $detail['product_code'] ?></td>
                                    <td class="text-end"><?= number_format($detail['quantity_ordered'], 2) ?></td>
                                    <td class="text-end"><strong><?= number_format($detail['quantity_received'], 2) ?></strong></td>
                                    <td class="text-center">
                                        <span class="<?= $difference_class ?>">
                                            <?= $difference > 0 ? '+' : '' ?><?= number_format($difference, 2) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">$<?= number_format($detail['unit_cost'], 2) ?></td>
                                    <td class="text-end">$<?= number_format($detail['subtotal'], 2) ?></td>
                                    <td class="text-end">$<?= number_format($detail['tax_amount'], 2) ?></td>
                                    <td class="text-end"><strong>$<?= number_format($detail['total'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="6" class="text-end">Subtotal:</th>
                                <th class="text-end">$<?= number_format($total_subtotal, 2) ?></th>
                                <th class="text-end">$<?= number_format($total_tax, 2) ?></th>
                                <th class="text-end">$<?= number_format($total_amount, 2) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Timeline de eventos -->
        <?php if ($receipt->status != 'received'): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Historial</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <strong>Recibido</strong>
                                <p class="mb-0 text-muted">
                                    <?= date('d/m/Y H:i', strtotime($receipt->created_at)) ?>
                                    por <?= $receipt->receiver->username ?? 'N/A' ?>
                                </p>
                            </div>
                        </div>
                        
                        <?php if ($receipt->verified_at): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <strong>Verificado</strong>
                                    <p class="mb-0 text-muted">
                                        <?= date('d/m/Y H:i', strtotime($receipt->verified_at)) ?>
                                        por Usuario #<?= $receipt->verified_by ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($receipt->posted_at): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <strong>Afectado en Inventario</strong>
                                    <p class="mb-0 text-muted">
                                        <?= date('d/m/Y H:i', strtotime($receipt->posted_at)) ?>
                                        por Usuario #<?= $receipt->posted_by ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Panel lateral -->
    <div class="col-md-4">
        <!-- Acciones -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if ($receipt->status == 'received'): ?>
                        <a href="<?= Uri::create('admin/proveedores/recepciones/verify/' . $receipt->id) ?>" 
                           class="btn btn-warning"
                           onclick="return confirm('¿Verificar esta recepción?')">
                            <i class="fas fa-check"></i> Verificar Recepción
                        </a>
                        <a href="<?= Uri::create('admin/proveedores/recepciones/edit/' . $receipt->id) ?>" 
                           class="btn btn-secondary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($receipt->status == 'verified'): ?>
                        <a href="<?= Uri::create('admin/proveedores/recepciones/post/' . $receipt->id) ?>" 
                           class="btn btn-success"
                           onclick="return confirm('¿Afectar inventario? Esta acción actualizará las existencias y generará la póliza contable.')">
                            <i class="fas fa-arrow-up"></i> Afectar Inventario
                        </a>
                    <?php endif; ?>
                    
                    <a href="#" class="btn btn-primary" onclick="window.print(); return false;">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                    
                    <a href="<?= Uri::create('admin/proveedores/recepciones') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Alertas -->
        <?php
        $has_differences = false;
        foreach ($details as $detail) {
            if ($detail['quantity_received'] != $detail['quantity_ordered']) {
                $has_differences = true;
                break;
            }
        }
        ?>
        
        <?php if ($has_differences): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Atención:</strong> Hay diferencias entre cantidades ordenadas y recibidas.
            </div>
        <?php endif; ?>
        
        <?php if ($receipt->status == 'posted'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Completado:</strong> Esta recepción ya fue afectada en inventario.
            </div>
        <?php endif; ?>
        
        <!-- Resumen -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Resumen</h5>
            </div>
            <div class="card-body">
                <dl>
                    <dt>Total de productos:</dt>
                    <dd><strong><?= count($details) ?></strong></dd>
                    
                    <dt>Unidades recibidas:</dt>
                    <dd>
                        <strong>
                            <?= number_format(array_sum(array_column($details, 'quantity_received')), 2) ?>
                        </strong>
                    </dd>
                    
                    <dt>Valor total:</dt>
                    <dd>
                        <h4 class="text-success mb-0">
                            $<?= number_format($receipt->total_amount, 2) ?>
                        </h4>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #dee2e6;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}

.badge.fs-5 {
    font-size: 1.25rem !important;
    padding: 0.5rem 1rem;
}

.badge i {
    margin-right: 0.5rem;
}

/* Timeline */
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline-item {
    position: relative;
    padding-left: 2.5rem;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 0.65rem;
    top: 1.5rem;
    width: 2px;
    height: calc(100% - 1.5rem);
    background: #dee2e6;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-content {
    padding-left: 0.5rem;
}

@media print {
    .btn, .alert, .page-header .badge {
        display: none !important;
    }
    
    .col-md-4 {
        display: none !important;
    }
    
    .col-md-8 {
        width: 100% !important;
    }
}
</style>
