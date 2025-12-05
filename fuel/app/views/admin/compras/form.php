<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-file-invoice me-2"></i>
        <?php echo isset($purchase) ? 'Editar Factura' : 'Nueva Factura'; ?>
    </h4>
    <a href="<?php echo Uri::create('admin/compras/index'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
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

<form method="post" enctype="multipart/form-data" 
      action="<?php echo isset($purchase) ? Uri::create('admin/compras/edit/' . $purchase->id) : Uri::create('admin/compras/create'); ?>">
    <?php echo Form::csrf(); ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" id="purchaseTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" type="button" role="tab" 
                    aria-controls="info" aria-selected="true" data-tab="info">
                <i class="fas fa-info-circle"></i> Información de Factura
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-tab" type="button" role="tab" 
                    aria-controls="payment" aria-selected="false" data-tab="payment">
                <i class="fas fa-credit-card"></i> Información de Pago
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="files-tab" type="button" role="tab" 
                    aria-controls="files" aria-selected="false" data-tab="files">
                <i class="fas fa-file-upload"></i> Archivos XML/PDF
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="purchaseTabsContent">
        
        <!-- Tab 1: Invoice Information -->
        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="provider_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select name="provider_id" id="provider_id" class="form-select" required>
                                <option value="">Seleccionar proveedor...</option>
                                <?php foreach ($providers as $provider): ?>
                                    <option value="<?php echo $provider->id; ?>" 
                                            <?php echo (isset($purchase) && $purchase->provider_id == $provider->id) ? 'selected' : ''; ?>>
                                        <?php echo $provider->company_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="purchase_order_id" class="form-label">Orden de Compra (Opcional)</label>
                            <select name="purchase_order_id" id="purchase_order_id" class="form-select">
                                <option value="">Sin orden asociada</option>
                                <?php foreach ($purchase_orders as $po): ?>
                                    <option value="<?php echo $po->id; ?>" 
                                            <?php echo (isset($purchase) && $purchase->purchase_order_id == $po->id) ? 'selected' : ''; ?>>
                                        <?php echo $po->code; ?> - $<?php echo number_format($po->total, 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="invoice_number" class="form-label">Número de Factura <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_number" id="invoice_number" class="form-control" 
                                   value="<?php echo isset($purchase) ? $purchase->invoice_number : ''; ?>" 
                                   placeholder="A123456" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="invoice_date" class="form-label">Fecha de Factura <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" id="invoice_date" class="form-control" 
                                   value="<?php echo isset($purchase) ? date('Y-m-d', strtotime($purchase->invoice_date)) : date('Y-m-d'); ?>" 
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date" class="form-control" 
                                   value="<?php echo isset($purchase) ? date('Y-m-d', strtotime($purchase->due_date)) : ''; ?>" 
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending" <?php echo (isset($purchase) && $purchase->status == 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="partial" <?php echo (isset($purchase) && $purchase->status == 'partial') ? 'selected' : ''; ?>>Pago Parcial</option>
                                <option value="paid" <?php echo (isset($purchase) && $purchase->status == 'paid') ? 'selected' : ''; ?>>Pagada</option>
                                <option value="overdue" <?php echo (isset($purchase) && $purchase->status == 'overdue') ? 'selected' : ''; ?>>Vencida</option>
                                <option value="cancelled" <?php echo (isset($purchase) && $purchase->status == 'cancelled') ? 'selected' : ''; ?>>Cancelada</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="subtotal" class="form-label">Subtotal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="subtotal" id="subtotal" class="form-control" 
                                       value="<?php echo isset($purchase) ? $purchase->subtotal : '0.00'; ?>" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tax" class="form-label">IVA <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="tax" id="tax" class="form-control" 
                                       value="<?php echo isset($purchase) ? $purchase->tax : '0.00'; ?>" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="total" class="form-label">Total <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="total" id="total" class="form-control" 
                                       value="<?php echo isset($purchase) ? $purchase->total : '0.00'; ?>" 
                                       step="0.01" min="0" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Notas adicionales sobre la factura..."><?php echo isset($purchase) ? $purchase->notes : ''; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Payment Information -->
        <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Método de Pago</label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                <option value="">Seleccionar...</option>
                                <option value="cash" <?php echo (isset($purchase) && $purchase->payment_method == 'cash') ? 'selected' : ''; ?>>Efectivo</option>
                                <option value="transfer" <?php echo (isset($purchase) && $purchase->payment_method == 'transfer') ? 'selected' : ''; ?>>Transferencia</option>
                                <option value="check" <?php echo (isset($purchase) && $purchase->payment_method == 'check') ? 'selected' : ''; ?>>Cheque</option>
                                <option value="credit_card" <?php echo (isset($purchase) && $purchase->payment_method == 'credit_card') ? 'selected' : ''; ?>>Tarjeta de Crédito</option>
                                <option value="debit_card" <?php echo (isset($purchase) && $purchase->payment_method == 'debit_card') ? 'selected' : ''; ?>>Tarjeta de Débito</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_date" class="form-label">Fecha de Pago</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" 
                                   value="<?php echo (isset($purchase) && $purchase->payment_date) ? date('Y-m-d', strtotime($purchase->payment_date)) : ''; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="paid_amount" class="form-label">Monto Pagado</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="paid_amount" id="paid_amount" class="form-control" 
                                       value="<?php echo isset($purchase) ? $purchase->paid_amount : '0.00'; ?>" 
                                       step="0.01" min="0">
                            </div>
                            <small class="text-muted">El saldo se calculará automáticamente</small>
                        </div>

                        <?php if (isset($purchase)): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Saldo Pendiente</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" 
                                           value="<?php echo number_format($purchase->balance, 2); ?>" 
                                           readonly>
                                </div>
                                <small class="text-muted"><?php echo $purchase->get_payment_percentage(); ?>% pagado</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($purchase) && $purchase->balance > 0): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Pagos parciales:</strong> Puede registrar pagos parciales desde la vista de detalle de la factura.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tab 3: Files Upload -->
        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
            <div class="card">
                <div class="card-body">
                    <?php if (isset($purchase)): ?>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Archivo XML Actual</h6>
                                <?php if ($purchase->xml_file): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-file-code"></i>
                                        <a href="<?php echo Uri::base() . 'uploads/compras/' . $purchase->xml_file; ?>" 
                                           target="_blank" class="alert-link">
                                            <?php echo $purchase->xml_file; ?>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Sin archivo XML
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <h6>Archivo PDF Actual</h6>
                                <?php if ($purchase->pdf_file): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-file-pdf"></i>
                                        <a href="<?php echo Uri::base() . 'uploads/compras/' . $purchase->pdf_file; ?>" 
                                           target="_blank" class="alert-link">
                                            <?php echo $purchase->pdf_file; ?>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Sin archivo PDF
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="xml_file" class="form-label">
                                <i class="fas fa-file-code"></i> Archivo XML (CFDI)
                            </label>
                            <input type="file" name="xml_file" id="xml_file" class="form-control" accept=".xml">
                            <small class="text-muted">
                                <?php echo isset($purchase) ? 'Dejar vacío para mantener el archivo actual' : 'Archivo opcional'; ?>
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pdf_file" class="form-label">
                                <i class="fas fa-file-pdf"></i> Archivo PDF
                            </label>
                            <input type="file" name="pdf_file" id="pdf_file" class="form-control" accept=".pdf">
                            <small class="text-muted">
                                <?php echo isset($purchase) ? 'Dejar vacío para mantener el archivo actual' : 'Archivo opcional'; ?>
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Archivos aceptados:</strong>
                        <ul class="mb-0 mt-2">
                            <li>XML: Comprobante Fiscal Digital por Internet (CFDI) de la factura</li>
                            <li>PDF: Representación impresa de la factura</li>
                            <li>Tamaño máximo por archivo: 10 MB</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="<?php echo Uri::create('admin/compras/index'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo isset($purchase) ? 'Actualizar' : 'Guardar'; ?> Factura
                </button>
            </div>
        </div>
    </div>
</form>

<script>
// Manual tab switching implementation (following BOOTSTRAP_TABS_FIX.md pattern)
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#purchaseTabs button[data-tab]');
    const tabPanes = document.querySelectorAll('#purchaseTabsContent .tab-pane');

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

    // Auto-calculate total from subtotal + tax
    const subtotalInput = document.getElementById('subtotal');
    const taxInput = document.getElementById('tax');
    const totalInput = document.getElementById('total');

    function calculateTotal() {
        const subtotal = parseFloat(subtotalInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        totalInput.value = (subtotal + tax).toFixed(2);
    }

    subtotalInput.addEventListener('input', calculateTotal);
    taxInput.addEventListener('input', calculateTotal);

    // Auto-calculate tax (16% IVA) when subtotal changes
    subtotalInput.addEventListener('change', function() {
        const subtotal = parseFloat(this.value) || 0;
        if (taxInput.value == 0 || taxInput.value == '') {
            taxInput.value = (subtotal * 0.16).toFixed(2);
            calculateTotal();
        }
    });
});
</script>
