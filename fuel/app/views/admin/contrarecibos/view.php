<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-clipboard-list me-2"></i>
        Contrarecibo: <?php echo $delivery_note->code; ?>
    </h4>
    <div>
        <?php if ($delivery_note->can_edit()): ?>
            <a href="<?php echo Uri::create('admin/contrarecibos/edit/' . $delivery_note->id); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        <?php endif; ?>
        <a href="<?php echo Uri::create('admin/contrarecibos/index'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (Session::get_flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo Session::get_flash('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (Session::get_flash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo Session::get_flash('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-3" id="deliveryNoteViewTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="general-tab" type="button" role="tab" 
                aria-controls="general" aria-selected="true" data-tab="general">
            <i class="fas fa-info-circle"></i> Información General
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="products-tab" type="button" role="tab" 
                aria-controls="products" aria-selected="false" data-tab="products">
            <i class="fas fa-boxes"></i> Productos Recibidos
            <span class="badge bg-primary ms-1"><?php echo count($delivery_note->items); ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="documents-tab" type="button" role="tab" 
                aria-controls="documents" aria-selected="false" data-tab="documents">
            <i class="fas fa-file-alt"></i> Documentos Relacionados
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="deliveryNoteViewTabsContent">
    
    <!-- Tab 1: General Information -->
    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
        <div class="row">
            <!-- Main Information Card -->
            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Información del Contrarecibo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong><i class="fas fa-barcode"></i> Código:</strong>
                                <p class="mb-0"><?php echo $delivery_note->code; ?></p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-building"></i> Proveedor:</strong>
                                <p class="mb-0">
                                    <a href="<?php echo Uri::create('admin/proveedores/view/' . $delivery_note->provider->id); ?>">
                                        <?php echo $delivery_note->provider->company_name; ?>
                                    </a>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar"></i> Fecha de Entrega:</strong>
                                <p class="mb-0"><?php echo date('d/m/Y', strtotime($delivery_note->delivery_date)); ?></p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-check"></i> Fecha de Recepción:</strong>
                                <p class="mb-0">
                                    <?php if ($delivery_note->received_date): ?>
                                        <?php echo date('d/m/Y', strtotime($delivery_note->received_date)); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Pendiente de recepción</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user"></i> Recibido por:</strong>
                                <p class="mb-0">
                                    <?php if ($delivery_note->received_by): ?>
                                        <?php echo $delivery_note->receiver->username; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No asignado</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-chart-line"></i> Progreso:</strong>
                                <div class="progress mt-1" style="height: 25px;">
                                    <div class="progress-bar <?php echo $delivery_note->get_completion_percentage() == 100 ? 'bg-success' : 'bg-info'; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $delivery_note->get_completion_percentage(); ?>%"
                                         aria-valuenow="<?php echo $delivery_note->get_completion_percentage(); ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <strong><?php echo $delivery_note->get_completion_percentage(); ?>%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($delivery_note->notes): ?>
                            <hr>
                            <div class="mb-0">
                                <strong><i class="fas fa-sticky-note"></i> Notas:</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    <?php echo nl2br(htmlspecialchars($delivery_note->notes)); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Status and Actions Card -->
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-flag"></i> Estado</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php echo $delivery_note->get_status_badge(); ?>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Productos:</h6>
                            <h4><?php echo count($delivery_note->items); ?> items</h4>
                        </div>

                        <div class="mb-3">
                            <h6>Total Estimado:</h6>
                            <h4 class="text-success">$<?php echo number_format($delivery_note->get_total(), 2); ?></h4>
                        </div>

                        <hr>

                        <!-- Action Buttons -->
                        <?php if ($delivery_note->status != 'completed' && $delivery_note->status != 'rejected' && $delivery_note->status != 'cancelled'): ?>
                            <button type="button" class="btn btn-success btn-sm w-100 mb-2" 
                                    data-bs-toggle="modal" data-bs-target="#completeModal">
                                <i class="fas fa-check-circle"></i> Marcar como Completado
                            </button>
                            
                            <button type="button" class="btn btn-danger btn-sm w-100 mb-2" 
                                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times-circle"></i> Marcar como Rechazado
                            </button>
                        <?php endif; ?>

                        <?php if ($delivery_note->can_delete()): ?>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Eliminar Contrarecibo
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Audit Information -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información Adicional</h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2">
                            <strong>Creado por:</strong><br>
                            <?php echo $delivery_note->creator->username; ?>
                        </p>
                        <p class="mb-2">
                            <strong>Fecha creación:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($delivery_note->created_at)); ?>
                        </p>
                        <p class="mb-0">
                            <strong>Última actualización:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($delivery_note->updated_at)); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Products Received -->
    <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-boxes"></i> Productos Recibidos</h5>
            </div>
            <div class="card-body">
                <?php if (count($delivery_note->items) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cant. Ordenada</th>
                                    <th class="text-center">Cant. Recibida</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center">% Recibido</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $index = 1; ?>
                                <?php foreach ($delivery_note->items as $item): ?>
                                    <tr <?php echo $item->is_complete() ? 'class="table-success"' : ($item->is_partial() ? 'class="table-warning"' : ''); ?>>
                                        <td><?php echo $index++; ?></td>
                                        <td>
                                            <strong><?php echo $item->product->name; ?></strong><br>
                                            <small class="text-muted">SKU: <?php echo $item->product->sku; ?></small>
                                        </td>
                                        <td class="text-center"><?php echo number_format($item->quantity_ordered, 2); ?></td>
                                        <td class="text-center">
                                            <strong><?php echo number_format($item->quantity_received, 2); ?></strong>
                                            <?php if ($item->quantity_received < $item->quantity_ordered): ?>
                                                <br><small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> Faltante: <?php echo number_format($item->quantity_ordered - $item->quantity_received, 2); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">$<?php echo number_format($item->unit_price, 2); ?></td>
                                        <td class="text-end"><strong>$<?php echo number_format($item->get_subtotal(), 2); ?></strong></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $item->is_complete() ? 'bg-success' : ($item->is_partial() ? 'bg-warning' : 'bg-secondary'); ?>">
                                                <?php echo $item->get_percentage(); ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($item->notes): ?>
                                                <small><?php echo htmlspecialchars($item->notes); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">TOTAL:</th>
                                    <th class="text-end">$<?php echo number_format($delivery_note->get_total(), 2); ?></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Productos Completos</h6>
                                    <h3 class="text-success">
                                        <?php 
                                        $complete = 0;
                                        foreach ($delivery_note->items as $item) {
                                            if ($item->is_complete()) $complete++;
                                        }
                                        echo $complete;
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Productos Parciales</h6>
                                    <h3 class="text-warning">
                                        <?php 
                                        $partial = 0;
                                        foreach ($delivery_note->items as $item) {
                                            if ($item->is_partial()) $partial++;
                                        }
                                        echo $partial;
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Sin Recibir</h6>
                                    <h3 class="text-secondary">
                                        <?php 
                                        $pending = 0;
                                        foreach ($delivery_note->items as $item) {
                                            if ($item->quantity_received == 0) $pending++;
                                        }
                                        echo $pending;
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p class="mb-0">No hay productos asociados a este contrarecibo</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 3: Related Documents -->
    <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Documentos Relacionados</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Purchase Order -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-4x text-primary mb-3"></i>
                                <h5>Orden de Compra</h5>
                                <?php if ($delivery_note->purchase_order_id): ?>
                                    <p class="text-success mb-2">
                                        <i class="fas fa-check-circle"></i> Orden vinculada
                                    </p>
                                    <p class="mb-2">
                                        <strong><?php echo $delivery_note->purchase_order->code; ?></strong>
                                    </p>
                                    <p class="text-muted small mb-3">
                                        Fecha: <?php echo date('d/m/Y', strtotime($delivery_note->purchase_order->created_at)); ?><br>
                                        Total: $<?php echo number_format($delivery_note->purchase_order->total, 2); ?>
                                    </p>
                                    <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $delivery_note->purchase_order->id); ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-eye"></i> Ver Orden
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle"></i> Sin orden asociada
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Invoice -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-file-invoice fa-4x text-success mb-3"></i>
                                <h5>Factura de Compra</h5>
                                <?php if ($delivery_note->purchase_id): ?>
                                    <p class="text-success mb-2">
                                        <i class="fas fa-check-circle"></i> Factura vinculada
                                    </p>
                                    <p class="mb-2">
                                        <strong><?php echo $delivery_note->purchase->code; ?></strong>
                                    </p>
                                    <p class="text-muted small mb-3">
                                        Núm: <?php echo $delivery_note->purchase->invoice_number; ?><br>
                                        Fecha: <?php echo date('d/m/Y', strtotime($delivery_note->purchase->invoice_date)); ?><br>
                                        Total: $<?php echo number_format($delivery_note->purchase->total, 2); ?>
                                    </p>
                                    <a href="<?php echo Uri::create('admin/compras/view/' . $delivery_note->purchase->id); ?>" 
                                       class="btn btn-success">
                                        <i class="fas fa-eye"></i> Ver Factura
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle"></i> Sin factura asociada
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> El contrarecibo puede estar vinculado a una orden de compra y/o a una factura de compra. 
                    Estos vínculos ayudan a mantener la trazabilidad del proceso de adquisición.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Mark as Complete -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?php echo Uri::create('admin/contrarecibos/mark_complete/' . $delivery_note->id); ?>">
            <?php echo Form::csrf(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeModalLabel">Marcar como Completado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="complete_received_by" class="form-label">Recibido por</label>
                        <select name="received_by" id="complete_received_by" class="form-select">
                            <option value="">Usuario actual</option>
                            <?php foreach (Model_User::query()->get() as $user): ?>
                                <option value="<?php echo $user->id; ?>"><?php echo $user->username; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Se marcará el contrarecibo como completado con la fecha actual.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Marcar como Completado</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Mark as Rejected -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?php echo Uri::create('admin/contrarecibos/mark_rejected/' . $delivery_note->id); ?>">
            <?php echo Form::csrf(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Marcar como Rechazado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reject_reason" class="form-control" rows="4" 
                                  placeholder="Describa el motivo del rechazo..." required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Esta acción marcará el contrarecibo como rechazado y agregará el motivo a las notas.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Marcar como Rechazado</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Delete Delivery Note -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar este contrarecibo?
                <br><strong>Esta acción no se puede deshacer.</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="<?php echo Uri::create('admin/contrarecibos/delete/' . $delivery_note->id); ?>" style="display: inline;">
                    <?php echo Form::csrf(); ?>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Manual tab switching implementation
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#deliveryNoteViewTabs button[data-tab]');
    const tabPanes = document.querySelectorAll('#deliveryNoteViewTabsContent .tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
            });
            tabPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            
            // Show corresponding pane
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });
});
</script>
