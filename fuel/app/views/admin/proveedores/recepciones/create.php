<div class="page-header">
    <h1>Nueva Recepción de Inventario</h1>
</div>

<div class="card">
    <div class="card-body">
        <?= Form::open(array('action' => Uri::create('admin/proveedores/recepciones/create'), 'method' => 'post', 'id' => 'receipt-form')) ?>
        
        <div class="row">
            <!-- Información básica -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                    <select name="provider_id" id="provider_id" class="form-select" required 
                            <?= isset($order) ? 'disabled' : '' ?>>
                        <option value="">Seleccione un proveedor</option>
                        <?php foreach ($providers as $prov): ?>
                            <option value="<?= $prov['id'] ?>" 
                                    <?= isset($order) && $order['provider_id'] == $prov['id'] ? 'selected' : '' ?>>
                                <?= $prov['company_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($order)): ?>
                        <input type="hidden" name="provider_id" value="<?= $order['provider_id'] ?>">
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Orden de Compra</label>
                    <select name="purchase_order_id" id="purchase_order_id" class="form-select"
                            <?= isset($order) ? 'disabled' : '' ?>>
                        <option value="">Sin orden de compra</option>
                        <?php if (isset($order)): ?>
                            <option value="<?= $order['id'] ?>" selected><?= $order['code_order'] ?></option>
                        <?php endif; ?>
                    </select>
                    <?php if (isset($order)): ?>
                        <input type="hidden" name="purchase_order_id" value="<?= $order['id'] ?>">
                        <small class="form-text text-success">
                            <i class="fas fa-check-circle"></i> Orden: <?= $order['code_order'] ?> - 
                            Total: $<?= number_format($order['total'], 2) ?>
                        </small>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Fecha de Recepción <span class="text-danger">*</span></label>
                    <input type="date" name="receipt_date" class="form-control" 
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Almacén <span class="text-danger">*</span></label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">Seleccione almacén</option>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Información de factura -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Número de Factura</label>
                    <input type="text" name="invoice_number" class="form-control" 
                           placeholder="Ej: FACT-12345">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Fecha de Factura</label>
                    <input type="date" name="invoice_date" class="form-control">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" class="form-control" rows="4" 
                              placeholder="Observaciones sobre la recepción..."></textarea>
                </div>
            </div>
        </div>
        
        <!-- Productos -->
        <div class="card bg-light mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Productos a Recibir</h5>
                <?php if (!isset($order)): ?>
                    <button type="button" class="btn btn-sm btn-primary" id="add-product-btn">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="products-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th class="text-end">Ordenado</th>
                                <th class="text-end">Recibido</th>
                                <th class="text-end">Costo Unit.</th>
                                <th class="text-end">% IVA</th>
                                <th>Lote</th>
                                <th>Caducidad</th>
                                <th class="text-end">Total</th>
                                <?php if (!isset($order)): ?>
                                    <th></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            <?php if (isset($order_details)): ?>
                                <?php foreach ($order_details as $index => $detail): ?>
                                    <tr>
                                        <td>
                                            <?= $detail['product_name'] ?>
                                            <input type="hidden" name="products[<?= $index ?>][product_id]" value="<?= $detail['product_id'] ?>">
                                        </td>
                                        <td><?= $detail['product_code'] ?></td>
                                        <td class="text-end">
                                            <input type="number" name="products[<?= $index ?>][quantity_ordered]" 
                                                   class="form-control form-control-sm text-end" 
                                                   value="<?= $detail['quantity'] ?>" readonly>
                                        </td>
                                        <td class="text-end">
                                            <input type="number" name="products[<?= $index ?>][quantity_received]" 
                                                   class="form-control form-control-sm text-end quantity-received" 
                                                   value="<?= $detail['quantity'] ?>" 
                                                   min="0" step="0.01" required>
                                        </td>
                                        <td class="text-end">
                                            <input type="number" name="products[<?= $index ?>][unit_cost]" 
                                                   class="form-control form-control-sm text-end unit-cost" 
                                                   value="<?= $detail['price'] ?>" 
                                                   step="0.01" required>
                                        </td>
                                        <td class="text-end">
                                            <input type="number" name="products[<?= $index ?>][tax_rate]" 
                                                   class="form-control form-control-sm text-end tax-rate" 
                                                   value="16" step="0.01">
                                        </td>
                                        <td>
                                            <input type="text" name="products[<?= $index ?>][lot_number]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Lote">
                                        </td>
                                        <td>
                                            <input type="date" name="products[<?= $index ?>][expiration_date]" 
                                                   class="form-control form-control-sm">
                                        </td>
                                        <td class="text-end">
                                            <strong class="row-total">$0.00</strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
                                        Seleccione una orden de compra o agregue productos manualmente
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="8" class="text-end">Total de Recepción:</th>
                                <th class="text-end">
                                    <h5 class="mb-0" id="grand-total">$0.00</h5>
                                </th>
                                <?php if (!isset($order)): ?>
                                    <th></th>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="<?= Uri::create('admin/proveedores/recepciones') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Recepción
            </button>
        </div>
        
        <?= Form::close() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular totales
    function calculateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-received').value) || 0;
        const unitCost = parseFloat(row.querySelector('.unit-cost').value) || 0;
        const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;
        
        const subtotal = quantity * unitCost;
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;
        
        row.querySelector('.row-total').textContent = '$' + total.toFixed(2);
        
        calculateGrandTotal();
    }
    
    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('#products-tbody tr').forEach(row => {
            const totalText = row.querySelector('.row-total');
            if (totalText) {
                grandTotal += parseFloat(totalText.textContent.replace('$', '')) || 0;
            }
        });
        document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
    }
    
    // Event listeners para calcular totales
    document.querySelectorAll('.quantity-received, .unit-cost, .tax-rate').forEach(input => {
        input.addEventListener('input', function() {
            calculateRowTotal(this.closest('tr'));
        });
    });
    
    // Calcular totales iniciales
    document.querySelectorAll('#products-tbody tr').forEach(row => {
        if (row.querySelector('.quantity-received')) {
            calculateRowTotal(row);
        }
    });
    
    // Agregar producto (si no viene de orden)
    <?php if (!isset($order)): ?>
    document.getElementById('add-product-btn').addEventListener('click', function() {
        // TODO: Implementar modal o selector de productos
        alert('Funcionalidad de agregar producto manual en desarrollo');
    });
    <?php endif; ?>
});
</script>

<style>
.form-label {
    font-weight: 600;
}

.text-danger {
    color: #dc3545 !important;
}

.card.bg-light {
    background-color: #f8f9fa !important;
    border: 2px solid #dee2e6;
}

#products-table input.form-control-sm {
    min-width: 80px;
}

.row-total {
    font-size: 1.1rem;
    color: #28a745;
}

#grand-total {
    color: #007bff;
    font-size: 1.5rem;
}

.table-primary th {
    background-color: #cfe2ff !important;
}
</style>
