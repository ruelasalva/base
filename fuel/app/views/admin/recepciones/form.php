<div class="container-fluid p-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-warehouse text-primary"></i>
                <?php echo isset($receipt) && $receipt ? 'Editar Recepción' : 'Nueva Recepción'; ?>
            </h1>
            <?php if (isset($receipt) && $receipt): ?>
                <p class="text-muted mb-0">Código: <?php echo $receipt->code; ?></p>
            <?php endif; ?>
        </div>
        <a href="<?php echo Uri::create('admin/recepciones'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="POST" id="receiptForm">
        <div class="card border-0 shadow-sm">
            <!-- Tabs (Manual JavaScript) -->
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" type="button" data-tab="general">
                            <i class="fas fa-info-circle"></i> Información General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" type="button" data-tab="productos">
                            <i class="fas fa-boxes"></i> Productos a Recibir
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" type="button" data-tab="notas">
                            <i class="fas fa-sticky-note"></i> Notas y Observaciones
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <!-- Tab: Información General -->
                <div class="tab-content active" data-tab-content="general">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Orden de Compra</label>
                            <select name="purchase_order_id" id="purchase_order_id" class="form-select" required>
                                <option value="">Seleccione una orden...</option>
                                <?php foreach ($purchase_orders as $order): ?>
                                    <option value="<?php echo $order->id; ?>" 
                                            data-provider="<?php echo $order->provider_id; ?>"
                                            <?php echo (isset($receipt) && $receipt && $receipt->purchase_order_id == $order->id) ? 'selected' : ''; ?>>
                                        <?php echo $order->code; ?> - 
                                        <?php echo $order->provider ? $order->provider->company_name : 'Sin proveedor'; ?> -
                                        $<?php echo number_format($order->total, 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Seleccione la orden de compra origen</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Proveedor</label>
                            <select name="provider_id" id="provider_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($providers as $provider): ?>
                                    <option value="<?php echo $provider->id; ?>"
                                            <?php echo (isset($receipt) && $receipt && $receipt->provider_id == $provider->id) ? 'selected' : ''; ?>>
                                        <?php echo $provider->company_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Se actualizará automáticamente según la OC</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Almacén Destino</label>
                            <input type="text" name="almacen_name" class="form-control" 
                                   value="<?php echo isset($receipt) && $receipt ? $receipt->almacen_name : ''; ?>" 
                                   placeholder="Ej: Almacén Central" required>
                            <small class="text-muted">Nombre del almacén donde se recibirá la mercancía</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Fecha de Recepción</label>
                            <input type="date" name="receipt_date" class="form-control" 
                                   value="<?php echo isset($receipt) && $receipt ? $receipt->receipt_date : date('Y-m-d'); ?>" 
                                   required>
                            <small class="text-muted">Fecha programada para recibir la mercancía</small>
                        </div>

                        <?php if (isset($receipt) && $receipt && $receipt->received_date): ?>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Recepción Real</label>
                                <input type="datetime-local" name="received_date" class="form-control" 
                                       value="<?php echo date('Y-m-d\TH:i', strtotime($receipt->received_date)); ?>">
                                <small class="text-muted">Fecha y hora en que se recibió</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab: Productos -->
                <div class="tab-content" data-tab-content="productos" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Productos a Recibir</h6>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Producto</th>
                                    <th style="width: 15%;">Cant. Ordenada</th>
                                    <th style="width: 15%;">Cant. Recibida</th>
                                    <th style="width: 12%;">Costo Unit.</th>
                                    <th style="width: 12%;">Condición</th>
                                    <th style="width: 12%;">Ubicación</th>
                                    <th style="width: 4%;" class="text-center">
                                        <i class="fas fa-trash"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <?php if (isset($receipt) && $receipt): ?>
                                    <?php foreach ($receipt->items as $index => $item): ?>
                                        <tr class="item-row">
                                            <td>
                                                <select name="items[<?php echo $index; ?>][product_id]" 
                                                        class="form-select form-select-sm product-select" required>
                                                    <option value="">Seleccione...</option>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?php echo $product->id; ?>" 
                                                                data-code="<?php echo $product->code; ?>"
                                                                <?php echo $item->product_id == $product->id ? 'selected' : ''; ?>>
                                                            <?php echo $product->code; ?> - <?php echo $product->name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="hidden" name="items[<?php echo $index; ?>][purchase_order_item_id]" 
                                                       value="<?php echo $item->purchase_order_item_id; ?>">
                                            </td>
                                            <td>
                                                <input type="number" name="items[<?php echo $index; ?>][quantity_ordered]" 
                                                       class="form-control form-control-sm text-end" 
                                                       value="<?php echo $item->quantity_ordered; ?>" 
                                                       step="0.01" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[<?php echo $index; ?>][quantity_received]" 
                                                       class="form-control form-control-sm text-end quantity-received" 
                                                       value="<?php echo $item->quantity_received; ?>" 
                                                       step="0.01" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[<?php echo $index; ?>][unit_cost]" 
                                                       class="form-control form-control-sm text-end unit-cost" 
                                                       value="<?php echo $item->unit_cost; ?>" 
                                                       step="0.01" required>
                                            </td>
                                            <td>
                                                <select name="items[<?php echo $index; ?>][condition]" 
                                                        class="form-select form-select-sm">
                                                    <option value="good" <?php echo $item->condition == 'good' ? 'selected' : ''; ?>>Bueno</option>
                                                    <option value="damaged" <?php echo $item->condition == 'damaged' ? 'selected' : ''; ?>>Dañado</option>
                                                    <option value="defective" <?php echo $item->condition == 'defective' ? 'selected' : ''; ?>>Defectuoso</option>
                                                    <option value="expired" <?php echo $item->condition == 'expired' ? 'selected' : ''; ?>>Caducado</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="items[<?php echo $index; ?>][location]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?php echo $item->location; ?>" 
                                                       placeholder="Ej: A1-R2-N3">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Si selecciona una orden de compra, se cargarán automáticamente sus productos. 
                        La condición predeterminada es "Bueno". Especifique la ubicación en almacén para cada producto.
                    </div>
                </div>

                <!-- Tab: Notas -->
                <div class="tab-content" data-tab-content="notas" style="display: none;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Notas Generales</label>
                            <textarea name="notes" class="form-control" rows="6" 
                                      placeholder="Observaciones, instrucciones especiales, condiciones de entrega..."><?php echo isset($receipt) && $receipt ? $receipt->notes : ''; ?></textarea>
                        </div>

                        <?php if (isset($receipt) && $receipt && $receipt->has_discrepancy && $receipt->discrepancy_notes): ?>
                            <div class="col-12">
                                <label class="form-label">Notas sobre Discrepancias</label>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo nl2br(e($receipt->discrepancy_notes)); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer con botones -->
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="<?php echo Uri::create('admin/recepciones'); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Recepción
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template para nueva fila de producto -->
<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <select name="items[__INDEX__][product_id]" class="form-select form-select-sm product-select" required>
                <option value="">Seleccione...</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product->id; ?>" data-code="<?php echo $product->code; ?>">
                        <?php echo $product->code; ?> - <?php echo $product->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="items[__INDEX__][purchase_order_item_id]" value="">
        </td>
        <td>
            <input type="number" name="items[__INDEX__][quantity_ordered]" 
                   class="form-control form-control-sm text-end" 
                   value="0" step="0.01" required>
        </td>
        <td>
            <input type="number" name="items[__INDEX__][quantity_received]" 
                   class="form-control form-control-sm text-end quantity-received" 
                   value="0" step="0.01" required>
        </td>
        <td>
            <input type="number" name="items[__INDEX__][unit_cost]" 
                   class="form-control form-control-sm text-end unit-cost" 
                   value="0" step="0.01" required>
        </td>
        <td>
            <select name="items[__INDEX__][condition]" class="form-select form-select-sm">
                <option value="good" selected>Bueno</option>
                <option value="damaged">Dañado</option>
                <option value="defective">Defectuoso</option>
                <option value="expired">Caducado</option>
            </select>
        </td>
        <td>
            <input type="text" name="items[__INDEX__][location]" 
                   class="form-control form-control-sm" 
                   placeholder="Ej: A1-R2-N3">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== TABS MANUALES (PATRÓN ESTÁNDAR) ==========
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('[data-tab-content]');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetTab = this.getAttribute('data-tab');
            
            // Remover active de todos
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.style.display = 'none');
            
            // Activar seleccionado
            this.classList.add('active');
            document.querySelector(`[data-tab-content="${targetTab}"]`).style.display = 'block';
        });
    });

    // ========== GESTIÓN DE ITEMS ==========
    let itemIndex = <?php echo isset($receipt) && $receipt ? count($receipt->items) : 0; ?>;
    const itemsTableBody = document.getElementById('itemsTableBody');
    const addItemBtn = document.getElementById('addItemBtn');
    const itemTemplate = document.getElementById('itemRowTemplate');

    // Agregar nueva fila
    addItemBtn.addEventListener('click', function() {
        const template = itemTemplate.content.cloneNode(true);
        const newRow = template.querySelector('.item-row');
        newRow.innerHTML = newRow.innerHTML.replace(/__INDEX__/g, itemIndex);
        itemsTableBody.appendChild(newRow);
        itemIndex++;
    });

    // Eliminar fila (delegación de eventos)
    itemsTableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });

    // ========== CARGAR ITEMS DE ORDEN DE COMPRA ==========
    const purchaseOrderSelect = document.getElementById('purchase_order_id');
    const providerSelect = document.getElementById('provider_id');

    purchaseOrderSelect.addEventListener('change', function() {
        const orderId = this.value;
        if (!orderId) return;

        // Actualizar proveedor automáticamente
        const selectedOption = this.options[this.selectedIndex];
        const providerId = selectedOption.getAttribute('data-provider');
        if (providerId) {
            providerSelect.value = providerId;
        }

        // Cargar items de la orden
        fetch(`<?php echo Uri::create('admin/recepciones/get_order_items'); ?>/${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.items) {
                    // Limpiar tabla
                    itemsTableBody.innerHTML = '';
                    
                    // Agregar items de la orden
                    data.items.forEach((item, index) => {
                        const template = itemTemplate.content.cloneNode(true);
                        const newRow = template.querySelector('.item-row');
                        newRow.innerHTML = newRow.innerHTML.replace(/__INDEX__/g, index);
                        
                        // Establecer valores
                        itemsTableBody.appendChild(newRow);
                        
                        const row = itemsTableBody.lastElementChild;
                        row.querySelector('[name$="[product_id]"]').value = item.product_id;
                        row.querySelector('[name$="[purchase_order_item_id]"]').value = item.id;
                        row.querySelector('[name$="[quantity_ordered]"]').value = item.quantity_ordered;
                        row.querySelector('[name$="[quantity_received]"]').value = item.quantity_ordered; // Por defecto, igual a ordenado
                        row.querySelector('[name$="[unit_cost]"]').value = item.unit_cost;
                    });
                    
                    itemIndex = data.items.length;
                }
            })
            .catch(error => console.error('Error al cargar items:', error));
    });

    // ========== VALIDACIÓN ==========
    const form = document.getElementById('receiptForm');
    form.addEventListener('submit', function(e) {
        const items = itemsTableBody.querySelectorAll('.item-row');
        if (items.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto a la recepción');
            return false;
        }
    });
});
</script>

<style>
.required::after {
    content: ' *';
    color: #dc3545;
}

.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 3px solid transparent;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    background: none;
}

.table td {
    vertical-align: middle;
}

.form-select-sm, .form-control-sm {
    font-size: 0.875rem;
}
</style>
