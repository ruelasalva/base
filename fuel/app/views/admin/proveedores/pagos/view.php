<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Pago: <?= $payment->payment_number ?></h1>
    <div>
        <?php
        $badges = [
            'draft' => '<span class="badge bg-warning fs-5">Borrador</span>',
            'completed' => '<span class="badge bg-success fs-5">Completado</span>',
            'cancelled' => '<span class="badge bg-danger fs-5">Cancelado</span>'
        ];
        echo $badges[$payment->status] ?? $payment->status;
        ?>
    </div>
</div>

<div class="row">
    <!-- Información del pago -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Pago</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Número:</dt>
                            <dd class="col-sm-7"><strong><?= $payment->payment_number ?></strong></dd>
                            
                            <dt class="col-sm-5">Fecha:</dt>
                            <dd class="col-sm-7"><?= date('d/m/Y', strtotime($payment->payment_date)) ?></dd>
                            
                            <dt class="col-sm-5">Proveedor:</dt>
                            <dd class="col-sm-7">
                                <a href="<?= Uri::create('admin/proveedores/view/' . $payment->provider->id) ?>">
                                    <?= $payment->provider->company_name ?>
                                </a>
                            </dd>
                            
                            <dt class="col-sm-5">Forma de Pago:</dt>
                            <dd class="col-sm-7">
                                <?php
                                echo Helper_Sat::get_forma_pago_descripcion($payment->payment_method);
                                ?>
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Referencia:</dt>
                            <dd class="col-sm-7"><?= $payment->reference_number ?: '-' ?></dd>
                            
                            <dt class="col-sm-5">Monto:</dt>
                            <dd class="col-sm-7">
                                <h4 class="text-success mb-0">
                                    $<?= number_format($payment->amount, 2) ?> <?= $payment->currency ?>
                                </h4>
                            </dd>
                            
                            <?php if ($payment->currency != 'MXN'): ?>
                                <dt class="col-sm-5">Tipo de Cambio:</dt>
                                <dd class="col-sm-7"><?= number_format($payment->exchange_rate, 4) ?></dd>
                            <?php endif; ?>
                            
                            <dt class="col-sm-5">Cuenta Bancaria:</dt>
                            <dd class="col-sm-7">
                                <?php if ($payment->bank_account_id): ?>
                                    <?= $payment->bank_account->bank_name ?? 'N/A' ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <?php if ($payment->notes): ?>
                    <hr>
                    <div>
                        <strong>Notas:</strong>
                        <p class="mb-0"><?= nl2br(e($payment->notes)) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Aplicación del pago -->
        <?php if (count($allocations) > 0): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Aplicación del Pago</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Documento</th>
                                    <th class="text-end">Monto Aplicado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_allocated = 0;
                                foreach ($allocations as $alloc): 
                                    $total_allocated += $alloc['amount_allocated'];
                                ?>
                                    <tr>
                                        <td>
                                            <?php if ($alloc['invoice_id']): ?>
                                                <span class="badge bg-primary">Factura</span>
                                            <?php elseif ($alloc['order_id']): ?>
                                                <span class="badge bg-secondary">Orden</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($alloc['invoice_number']): ?>
                                                <a href="<?= Uri::create('admin/compras/facturas/view/' . $alloc['invoice_id']) ?>">
                                                    <?= $alloc['invoice_number'] ?>
                                                </a>
                                            <?php elseif ($alloc['order_number']): ?>
                                                <a href="<?= Uri::create('admin/compras/ordenes/view/' . $alloc['order_id']) ?>">
                                                    <?= $alloc['order_number'] ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <strong>$<?= number_format($alloc['amount_allocated'], 2) ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="2" class="text-end">Total Aplicado:</th>
                                    <th class="text-end">$<?= number_format($total_allocated, 2) ?></th>
                                </tr>
                                <?php if ($total_allocated < $payment->amount): ?>
                                    <tr class="table-warning">
                                        <th colspan="2" class="text-end">Saldo sin Aplicar:</th>
                                        <th class="text-end">$<?= number_format($payment->amount - $total_allocated, 2) ?></th>
                                    </tr>
                                <?php endif; ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Este pago no ha sido aplicado a ninguna factura u orden de compra.
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
                    <?php if ($payment->status == 'draft'): ?>
                        <a href="<?= Uri::create('admin/proveedores/pagos/complete/' . $payment->id) ?>" 
                           class="btn btn-success"
                           onclick="return confirm('¿Completar este pago? Se generará la póliza contable y no se podrá revertir.')">
                            <i class="fas fa-check"></i> Completar Pago
                        </a>
                        <a href="<?= Uri::create('admin/proveedores/pagos/edit/' . $payment->id) ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="<?= Uri::create('admin/proveedores/pagos/cancel/' . $payment->id) ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('¿Cancelar este pago?')">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    <?php endif; ?>
                    
                    <a href="#" class="btn btn-primary" onclick="window.print(); return false;">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                    
                    <a href="<?= Uri::create('admin/proveedores/pagos') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información Adicional</h5>
            </div>
            <div class="card-body">
                <dl>
                    <dt>Creado por:</dt>
                    <dd><?= $payment->created_by ? 'Usuario #' . $payment->created_by : 'Sistema' ?></dd>
                    
                    <dt>Fecha de creación:</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($payment->created_at)) ?></dd>
                    
                    <?php if ($payment->updated_at): ?>
                        <dt>Última actualización:</dt>
                        <dd><?= date('d/m/Y H:i', strtotime($payment->updated_at)) ?></dd>
                    <?php endif; ?>
                    
                    <?php if ($payment->status == 'completed' && $payment->posted_at): ?>
                        <dt>Completado el:</dt>
                        <dd><?= date('d/m/Y H:i', strtotime($payment->posted_at)) ?></dd>
                    <?php endif; ?>
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

.card-header h5 {
    margin: 0;
}

dl.row {
    margin-bottom: 0;
}

dl dt {
    font-weight: 600;
}

dl dd {
    margin-bottom: 0.5rem;
}

.badge.fs-5 {
    font-size: 1.25rem !important;
    padding: 0.5rem 1rem;
}

@media print {
    .card {
        page-break-inside: avoid;
    }
    
    .btn, .page-header .badge {
        display: none !important;
    }
}
</style>
