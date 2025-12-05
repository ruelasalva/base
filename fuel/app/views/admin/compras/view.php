<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-file-invoice me-2"></i>
        Factura: <?php echo $purchase->code; ?>
    </h4>
    <div>
        <?php if ($purchase->can_edit()): ?>
            <a href="<?php echo Uri::create('admin/compras/edit/' . $purchase->id); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        <?php endif; ?>
        <a href="<?php echo Uri::create('admin/compras/index'); ?>" class="btn btn-secondary">
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
<ul class="nav nav-tabs mb-3" id="purchaseViewTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="general-tab" type="button" role="tab" 
                aria-controls="general" aria-selected="true" data-tab="general">
            <i class="fas fa-info-circle"></i> Información General
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payments-tab" type="button" role="tab" 
                aria-controls="payments" aria-selected="false" data-tab="payments">
            <i class="fas fa-money-bill-wave"></i> Pagos
            <?php if ($purchase->balance > 0): ?>
                <span class="badge bg-warning ms-1">Pendiente</span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="documents-tab" type="button" role="tab" 
                aria-controls="documents" aria-selected="false" data-tab="documents">
            <i class="fas fa-file-alt"></i> Documentos
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="purchaseViewTabsContent">
    
    <!-- Tab 1: General Information -->
    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
        <div class="row">
            <!-- Main Information Card -->
            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Información de la Factura</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong><i class="fas fa-barcode"></i> Código:</strong>
                                <p class="mb-0"><?php echo $purchase->code; ?></p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-receipt"></i> Número de Factura:</strong>
                                <p class="mb-0"><?php echo $purchase->invoice_number; ?></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong><i class="fas fa-building"></i> Proveedor:</strong>
                                <p class="mb-0">
                                    <a href="<?php echo Uri::create('admin/proveedores/view/' . $purchase->provider->id); ?>">
                                        <?php echo $purchase->provider->company_name; ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-shopping-cart"></i> Orden de Compra:</strong>
                                <p class="mb-0">
                                    <?php if ($purchase->purchase_order_id): ?>
                                        <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $purchase->purchase_order->id); ?>">
                                            <?php echo $purchase->purchase_order->code; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Sin orden asociada</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-calendar"></i> Fecha Factura:</strong>
                                <p class="mb-0"><?php echo date('d/m/Y', strtotime($purchase->invoice_date)); ?></p>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-calendar-times"></i> Fecha Vencimiento:</strong>
                                <p class="mb-0">
                                    <?php echo date('d/m/Y', strtotime($purchase->due_date)); ?>
                                    <?php if ($purchase->is_overdue()): ?>
                                        <br><span class="badge bg-danger mt-1">Vencida hace <?php echo $purchase->get_days_overdue(); ?> días</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-calendar-check"></i> Fecha Pago:</strong>
                                <p class="mb-0">
                                    <?php echo $purchase->payment_date ? date('d/m/Y', strtotime($purchase->payment_date)) : '<span class="text-muted">Sin pagar</span>'; ?>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-dollar-sign"></i> Subtotal:</strong>
                                <p class="mb-0 fs-5">$<?php echo number_format($purchase->subtotal, 2); ?></p>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-percentage"></i> IVA:</strong>
                                <p class="mb-0 fs-5">$<?php echo number_format($purchase->tax, 2); ?></p>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-coins"></i> Total:</strong>
                                <p class="mb-0 fs-4 text-primary"><strong>$<?php echo number_format($purchase->total, 2); ?></strong></p>
                            </div>
                        </div>

                        <?php if ($purchase->notes): ?>
                            <hr>
                            <div class="mb-0">
                                <strong><i class="fas fa-sticky-note"></i> Notas:</strong>
                                <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($purchase->notes)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Method Card -->
                <?php if ($purchase->payment_method): ?>
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-credit-card"></i> Método de Pago</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 fs-5">
                                <?php
                                $methods = [
                                    'cash' => 'Efectivo',
                                    'transfer' => 'Transferencia',
                                    'check' => 'Cheque',
                                    'credit_card' => 'Tarjeta de Crédito',
                                    'debit_card' => 'Tarjeta de Débito'
                                ];
                                echo $methods[$purchase->payment_method] ?? $purchase->payment_method;
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Status and Actions Card -->
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-flag"></i> Estado</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php echo $purchase->get_status_badge(); ?>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Pagado:</h6>
                            <h4 class="text-success">$<?php echo number_format($purchase->paid_amount, 2); ?></h4>
                            <small class="text-muted"><?php echo $purchase->get_payment_percentage(); ?>%</small>
                        </div>

                        <div class="mb-3">
                            <h6>Saldo:</h6>
                            <h4 class="text-danger">$<?php echo number_format($purchase->balance, 2); ?></h4>
                        </div>

                        <hr>

                        <!-- Action Buttons -->
                        <?php if ($purchase->status != 'paid' && $purchase->status != 'cancelled'): ?>
                            <button type="button" class="btn btn-success btn-sm w-100 mb-2" 
                                    data-bs-toggle="modal" data-bs-target="#markPaidModal">
                                <i class="fas fa-check-circle"></i> Marcar como Pagada
                            </button>
                            
                            <?php if ($purchase->balance > 0): ?>
                                <button type="button" class="btn btn-primary btn-sm w-100 mb-2" 
                                        data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                                    <i class="fas fa-plus"></i> Registrar Pago
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($purchase->can_delete()): ?>
                            <button type="button" class="btn btn-danger btn-sm w-100" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Eliminar Factura
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
                            <?php echo $purchase->creator->username; ?>
                        </p>
                        <p class="mb-2">
                            <strong>Fecha creación:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($purchase->created_at)); ?>
                        </p>
                        <p class="mb-0">
                            <strong>Última actualización:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($purchase->updated_at)); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Payments -->
    <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Historial de Pagos</h5>
            </div>
            <div class="card-body">
                <?php if ($purchase->status == 'paid'): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Factura pagada completamente</strong>
                        <br>Fecha de pago: <?php echo date('d/m/Y', strtotime($purchase->payment_date)); ?>
                        <br>Monto: $<?php echo number_format($purchase->total, 2); ?>
                        <?php if ($purchase->payment_method): ?>
                            <br>Método: <?php 
                            $methods = [
                                'cash' => 'Efectivo',
                                'transfer' => 'Transferencia',
                                'check' => 'Cheque',
                                'credit_card' => 'Tarjeta de Crédito',
                                'debit_card' => 'Tarjeta de Débito'
                            ];
                            echo $methods[$purchase->payment_method] ?? $purchase->payment_method;
                            ?>
                        <?php endif; ?>
                    </div>
                <?php elseif ($purchase->paid_amount > 0): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Pago parcial registrado</strong>
                        <br>Monto pagado: $<?php echo number_format($purchase->paid_amount, 2); ?>
                        <br>Saldo pendiente: $<?php echo number_format($purchase->balance, 2); ?>
                        <br>Porcentaje pagado: <?php echo $purchase->get_payment_percentage(); ?>%
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Sin pagos registrados</strong>
                        <br>Saldo total: $<?php echo number_format($purchase->total, 2); ?>
                    </div>
                <?php endif; ?>

                <!-- Payment Summary -->
                <div class="row mt-4">
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <h6 class="text-muted">Total Factura</h6>
                            <h3 class="mb-0">$<?php echo number_format($purchase->total, 2); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted">Monto Pagado</h6>
                            <h3 class="mb-0 text-success">$<?php echo number_format($purchase->paid_amount, 2); ?></h3>
                            <small class="text-muted"><?php echo $purchase->get_payment_percentage(); ?>%</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <h6 class="text-muted">Saldo Pendiente</h6>
                            <h3 class="mb-0 text-danger">$<?php echo number_format($purchase->balance, 2); ?></h3>
                        </div>
                    </div>
                </div>

                <?php if ($purchase->balance > 0 && $purchase->status != 'cancelled'): ?>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" 
                                data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="fas fa-plus"></i> Registrar Nuevo Pago
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 3: Documents -->
    <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Documentos Adjuntos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- XML File -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-code fa-4x text-info mb-3"></i>
                                <h5>Archivo XML (CFDI)</h5>
                                <?php if ($purchase->xml_file): ?>
                                    <p class="text-success">
                                        <i class="fas fa-check-circle"></i> Archivo disponible
                                    </p>
                                    <a href="<?php echo Uri::base() . 'uploads/compras/' . $purchase->xml_file; ?>" 
                                       class="btn btn-primary" download>
                                        <i class="fas fa-download"></i> Descargar XML
                                    </a>
                                    <p class="text-muted small mt-2"><?php echo $purchase->xml_file; ?></p>
                                <?php else: ?>
                                    <p class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Sin archivo XML
                                    </p>
                                    <?php if ($purchase->can_edit()): ?>
                                        <a href="<?php echo Uri::create('admin/compras/edit/' . $purchase->id); ?>" 
                                           class="btn btn-secondary btn-sm">
                                            <i class="fas fa-upload"></i> Subir XML
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- PDF File -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                <h5>Archivo PDF</h5>
                                <?php if ($purchase->pdf_file): ?>
                                    <p class="text-success">
                                        <i class="fas fa-check-circle"></i> Archivo disponible
                                    </p>
                                    <a href="<?php echo Uri::base() . 'uploads/compras/' . $purchase->pdf_file; ?>" 
                                       class="btn btn-danger" target="_blank">
                                        <i class="fas fa-eye"></i> Ver PDF
                                    </a>
                                    <a href="<?php echo Uri::base() . 'uploads/compras/' . $purchase->pdf_file; ?>" 
                                       class="btn btn-primary" download>
                                        <i class="fas fa-download"></i> Descargar
                                    </a>
                                    <p class="text-muted small mt-2"><?php echo $purchase->pdf_file; ?></p>
                                <?php else: ?>
                                    <p class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Sin archivo PDF
                                    </p>
                                    <?php if ($purchase->can_edit()): ?>
                                        <a href="<?php echo Uri::create('admin/compras/edit/' . $purchase->id); ?>" 
                                           class="btn btn-secondary btn-sm">
                                            <i class="fas fa-upload"></i> Subir PDF
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Archivos CFDI:</strong> Los archivos XML y PDF corresponden al Comprobante Fiscal Digital por Internet (CFDI) emitido por el proveedor.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Mark as Paid -->
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?php echo Uri::create('admin/compras/mark_paid/' . $purchase->id); ?>">
            <?php echo Form::csrf(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markPaidModalLabel">Marcar como Pagada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mark_payment_date" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="mark_payment_date" 
                               class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mark_payment_method" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select name="payment_method" id="mark_payment_method" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="cash">Efectivo</option>
                            <option value="transfer">Transferencia</option>
                            <option value="check">Cheque</option>
                            <option value="credit_card">Tarjeta de Crédito</option>
                            <option value="debit_card">Tarjeta de Débito</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Se marcará la factura como completamente pagada por el monto de <strong>$<?php echo number_format($purchase->total, 2); ?></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Marcar como Pagada</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Payment -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?php echo Uri::create('admin/compras/add_payment/' . $purchase->id); ?>">
            <?php echo Form::csrf(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Registrar Pago Parcial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_amount" class="form-label">Monto a Pagar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount" id="add_amount" class="form-control" 
                                   step="0.01" min="0.01" max="<?php echo $purchase->balance; ?>" 
                                   value="<?php echo $purchase->balance; ?>" required>
                        </div>
                        <small class="text-muted">Saldo pendiente: $<?php echo number_format($purchase->balance, 2); ?></small>
                    </div>
                    <div class="mb-3">
                        <label for="add_payment_date" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="add_payment_date" 
                               class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_payment_method" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select name="payment_method" id="add_payment_method" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="cash">Efectivo</option>
                            <option value="transfer">Transferencia</option>
                            <option value="check">Cheque</option>
                            <option value="credit_card">Tarjeta de Crédito</option>
                            <option value="debit_card">Tarjeta de Débito</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Delete Purchase -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar esta factura?
                <br><strong>Esta acción no se puede deshacer.</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="<?php echo Uri::create('admin/compras/delete/' . $purchase->id); ?>" style="display: inline;">
                    <?php echo Form::csrf(); ?>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Manual tab switching implementation (following BOOTSTRAP_TABS_FIX.md pattern)
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#purchaseViewTabs button[data-tab]');
    const tabPanes = document.querySelectorAll('#purchaseViewTabsContent .tab-pane');

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
