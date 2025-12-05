<div class="page-header">
    <h1>Nuevo Pago a Proveedor</h1>
</div>

<div class="card">
    <div class="card-body">
        <?= Form::open(array('action' => Uri::create('admin/proveedores/pagos/create'), 'method' => 'post', 'id' => 'payment-form')) ?>
        
        <div class="row">
            <!-- Información básica -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                    <select name="provider_id" id="provider_id" class="form-select" required>
                        <option value="">Seleccione un proveedor</option>
                        <?php foreach ($providers as $prov): ?>
                            <option value="<?= $prov['id'] ?>" data-credit-limit="<?= $prov['credit_limit'] ?? 0 ?>">
                                <?= $prov['company_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" 
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Forma de Pago SAT <span class="text-danger">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="">Seleccione forma de pago...</option>
                        <?php
                        $formas_pago_sat = Helper_Sat::get_formas_pago();
                        foreach ($formas_pago_sat as $codigo => $descripcion):
                        ?>
                            <option value="<?= $codigo ?>"><?= $descripcion ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Catálogo oficial del SAT (c_FormaPago)</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Número de Referencia</label>
                    <input type="text" name="reference_number" class="form-control" 
                           placeholder="Ej: TRANSFER-12345">
                </div>
            </div>
            
            <!-- Información de monto -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Monto <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control" 
                           step="0.01" min="0.01" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Moneda <span class="text-danger">*</span></label>
                    <select name="currency" class="form-select" required>
                        <option value="MXN" selected>MXN - Peso Mexicano</option>
                        <option value="USD">USD - Dólar Americano</option>
                        <option value="EUR">EUR - Euro</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tipo de Cambio</label>
                    <input type="number" name="exchange_rate" class="form-control" 
                           step="0.0001" value="1.0000">
                    <small class="form-text text-muted">Solo si la moneda es diferente a MXN</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Cuenta Bancaria</label>
                    <select name="bank_account_id" class="form-select">
                        <option value="">Seleccione cuenta...</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Notas</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>
        
        <!-- Aplicar pago a facturas -->
        <div class="card bg-light mb-3" id="allocation-section" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">Aplicar Pago a Facturas Pendientes</h5>
            </div>
            <div class="card-body">
                <div id="pending-invoices-container">
                    <div class="alert alert-info">
                        Seleccione un proveedor para ver las facturas pendientes.
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Total a Pagar:</strong>
                        <span id="payment-total" class="text-primary fs-4">$0.00</span>
                    </div>
                    <div class="col-md-6 text-end">
                        <strong>Total Aplicado:</strong>
                        <span id="allocated-total" class="text-success fs-4">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estado del pago -->
        <div class="mb-3">
            <label class="form-label">Estado <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
                <option value="draft" selected>Borrador (no genera póliza)</option>
                <option value="completed">Completado (genera póliza contable)</option>
            </select>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="<?= Uri::create('admin/proveedores/pagos') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Pago
            </button>
        </div>
        
        <?= Form::close() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('provider_id');
    const amountInput = document.getElementById('amount');
    const allocationSection = document.getElementById('allocation-section');
    const pendingInvoicesContainer = document.getElementById('pending-invoices-container');
    
    // Cuando se selecciona un proveedor
    providerSelect.addEventListener('change', function() {
        const providerId = this.value;
        
        if (providerId) {
            // Cargar facturas pendientes
            fetch('<?= Uri::create('admin/proveedores/pagos/get_pending_invoices') ?>/' + providerId)
                .then(response => response.json())
                .then(data => {
                    if (data.invoices && data.invoices.length > 0) {
                        renderPendingInvoices(data.invoices);
                        allocationSection.style.display = 'block';
                    } else {
                        pendingInvoicesContainer.innerHTML = '<div class="alert alert-warning">No hay facturas pendientes para este proveedor.</div>';
                        allocationSection.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            allocationSection.style.display = 'none';
        }
    });
    
    // Renderizar facturas pendientes
    function renderPendingInvoices(invoices) {
        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr>';
        html += '<th>Factura</th>';
        html += '<th>Fecha</th>';
        html += '<th class="text-end">Total</th>';
        html += '<th class="text-end">Pendiente</th>';
        html += '<th class="text-end">Aplicar</th>';
        html += '</tr></thead><tbody>';
        
        invoices.forEach((invoice, index) => {
            html += '<tr>';
            html += '<td>' + invoice.invoice_number + '</td>';
            html += '<td>' + invoice.bill_date + '</td>';
            html += '<td class="text-end">$' + parseFloat(invoice.total).toFixed(2) + '</td>';
            html += '<td class="text-end">$' + parseFloat(invoice.pending_amount).toFixed(2) + '</td>';
            html += '<td>';
            html += '<input type="hidden" name="products[' + index + '][invoice_id]" value="' + invoice.id + '">';
            html += '<input type="number" name="products[' + index + '][amount_allocated]" ';
            html += 'class="form-control form-control-sm allocation-input" ';
            html += 'step="0.01" min="0" max="' + invoice.pending_amount + '" ';
            html += 'data-max="' + invoice.pending_amount + '">';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        pendingInvoicesContainer.innerHTML = html;
        
        // Event listeners para calcular total aplicado
        document.querySelectorAll('.allocation-input').forEach(input => {
            input.addEventListener('input', updateAllocatedTotal);
        });
    }
    
    // Actualizar monto de pago
    amountInput.addEventListener('input', function() {
        document.getElementById('payment-total').textContent = '$' + parseFloat(this.value || 0).toFixed(2);
    });
    
    // Actualizar total aplicado
    function updateAllocatedTotal() {
        let total = 0;
        document.querySelectorAll('.allocation-input').forEach(input => {
            total += parseFloat(input.value || 0);
        });
        document.getElementById('allocated-total').textContent = '$' + total.toFixed(2);
    }
});
</script>

<style>
.form-label {
    font-weight: 600;
}

.text-danger {
    color: #dc3545 !important;
}

.fs-4 {
    font-size: 1.5rem !important;
}

#allocation-section {
    border: 2px dashed #dee2e6;
}

.allocation-input {
    max-width: 150px;
}
</style>
