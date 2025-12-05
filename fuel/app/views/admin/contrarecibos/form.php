<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-clipboard-list me-2"></i>
        <?php echo isset($delivery_note) ? 'Editar Contrarecibo' : 'Nuevo Contrarecibo'; ?>
    </h4>
    <a href="<?php echo Uri::create('admin/contrarecibos/index'); ?>" class="btn btn-secondary">
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

<form method="post" action="<?php echo isset($delivery_note) ? Uri::create('admin/contrarecibos/edit/' . $delivery_note->id) : Uri::create('admin/contrarecibos/create'); ?>">
    <?php echo Form::csrf(); ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" id="deliveryNoteTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" type="button" role="tab" 
                    aria-controls="info" aria-selected="true" data-tab="info">
                <i class="fas fa-info-circle"></i> Información General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="products-tab" type="button" role="tab" 
                    aria-controls="products" aria-selected="false" data-tab="products">
                <i class="fas fa-boxes"></i> Productos Recibidos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="notes-tab" type="button" role="tab" 
                    aria-controls="notes" aria-selected="false" data-tab="notes">
                <i class="fas fa-sticky-note"></i> Notas
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="deliveryNoteTabsContent">
        
        <!-- Tab 1: General Information -->
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
                                            <?php echo (isset($delivery_note) && $delivery_note->provider_id == $provider->id) ? 'selected' : ''; ?>>
                                        <?php echo $provider->company_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending" <?php echo (isset($delivery_note) && $delivery_note->status == 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="partial" <?php echo (isset($delivery_note) && $delivery_note->status == 'partial') ? 'selected' : ''; ?>>Parcial</option>
                                <option value="completed" <?php echo (isset($delivery_note) && $delivery_note->status == 'completed') ? 'selected' : ''; ?>>Completado</option>
                                <option value="rejected" <?php echo (isset($delivery_note) && $delivery_note->status == 'rejected') ? 'selected' : ''; ?>>Rechazado</option>
                                <option value="cancelled" <?php echo (isset($delivery_note) && $delivery_note->status == 'cancelled') ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_order_id" class="form-label">Orden de Compra (Opcional)</label>
                            <select name="purchase_order_id" id="purchase_order_id" class="form-select">
                                <option value="">Sin orden asociada</option>
                                <?php foreach ($purchase_orders as $po): ?>
                                    <option value="<?php echo $po->id; ?>" 
                                            <?php echo (isset($delivery_note) && $delivery_note->purchase_order_id == $po->id) ? 'selected' : ''; ?>>
                                        <?php echo $po->code; ?> - <?php echo $po->provider->company_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="purchase_id" class="form-label">Factura (Opcional)</label>
                            <select name="purchase_id" id="purchase_id" class="form-select">
                                <option value="">Sin factura asociada</option>
                                <?php foreach ($purchases as $purchase): ?>
                                    <option value="<?php echo $purchase->id; ?>" 
                                            <?php echo (isset($delivery_note) && $delivery_note->purchase_id == $purchase->id) ? 'selected' : ''; ?>>
                                        <?php echo $purchase->code; ?> - <?php echo $purchase->invoice_number; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="delivery_date" class="form-label">Fecha de Entrega <span class="text-danger">*</span></label>
                            <input type="date" name="delivery_date" id="delivery_date" class="form-control" 
                                   value="<?php echo isset($delivery_note) ? date('Y-m-d', strtotime($delivery_note->delivery_date)) : date('Y-m-d'); ?>" 
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="received_date" class="form-label">Fecha de Recepción</label>
                            <input type="date" name="received_date" id="received_date" class="form-control" 
                                   value="<?php echo (isset($delivery_note) && $delivery_note->received_date) ? date('Y-m-d', strtotime($delivery_note->received_date)) : ''; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="received_by" class="form-label">Recibido por</label>
                            <select name="received_by" id="received_by" class="form-select">
                                <option value="">Seleccionar usuario...</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user->id; ?>" 
                                            <?php echo (isset($delivery_note) && $delivery_note->received_by == $user->id) ? 'selected' : ''; ?>>
                                        <?php echo $user->username; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Products -->
        <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Productos Recibidos</h5>
                    <button type="button" class="btn btn-sm btn-light" onclick="addProductRow()">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="productsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Producto</th>
                                    <th style="width: 15%;">Cantidad Ordenada</th>
                                    <th style="width: 15%;">Cantidad Recibida</th>
                                    <th style="width: 15%;">Precio Unitario</th>
                                    <th style="width: 20%;">Notas</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="productRows">
                                <?php if (isset($delivery_note) && count($delivery_note->items) > 0): ?>
                                    <?php foreach ($delivery_note->items as $item): ?>
                                        <tr>
                                            <td>
                                                <select name="products[]" class="form-select" required>
                                                    <option value="">Seleccionar...</option>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?php echo $product->id; ?>" 
                                                                <?php echo $item->product_id == $product->id ? 'selected' : ''; ?>>
                                                            <?php echo $product->name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="quantities_ordered[]" class="form-control" 
                                                       value="<?php echo $item->quantity_ordered; ?>" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" name="quantities_received[]" class="form-control" 
                                                       value="<?php echo $item->quantity_received; ?>" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" name="prices[]" class="form-control" 
                                                       value="<?php echo $item->unit_price; ?>" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="text" name="item_notes[]" class="form-control" 
                                                       value="<?php echo $item->notes; ?>" placeholder="Opcional">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> La cantidad recibida puede ser diferente a la ordenada. El estado se actualizará automáticamente según las cantidades ingresadas.
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Notes -->
        <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas Generales</label>
                        <textarea name="notes" id="notes" class="form-control" rows="8" 
                                  placeholder="Notas sobre la recepción, observaciones, diferencias encontradas, etc."><?php echo isset($delivery_note) ? $delivery_note->notes : ''; ?></textarea>
                    </div>

                    <div class="alert alert-secondary">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Sugerencias:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Registre cualquier diferencia entre lo ordenado y lo recibido</li>
                            <li>Anote el estado de la mercancía (daños, embalaje, etc.)</li>
                            <li>Documente cualquier problema o incidencia en la entrega</li>
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
                <a href="<?php echo Uri::create('admin/contrarecibos/index'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo isset($delivery_note) ? 'Actualizar' : 'Guardar'; ?> Contrarecibo
                </button>
            </div>
        </div>
    </div>
</form>

<script>
// Manual tab switching implementation
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#deliveryNoteTabs button[data-tab]');
    const tabPanes = document.querySelectorAll('#deliveryNoteTabsContent .tab-pane');

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

// Product row management
function addProductRow() {
    const tbody = document.getElementById('productRows');
    const emptyRow = tbody.querySelector('td[colspan="6"]');
    if (emptyRow) {
        emptyRow.parentElement.remove();
    }

    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="products[]" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" name="quantities_ordered[]" class="form-control" value="0" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" name="quantities_received[]" class="form-control" value="0" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" name="prices[]" class="form-control" value="0.00" step="0.01" min="0" required>
        </td>
        <td>
            <input type="text" name="item_notes[]" class="form-control" placeholder="Opcional">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

function removeRow(button) {
    const tbody = document.getElementById('productRows');
    const row = button.closest('tr');
    row.remove();

    // Si no quedan filas, mostrar mensaje
    if (tbody.children.length === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = `
            <td colspan="6" class="text-center text-muted">
                No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.
            </td>
        `;
        tbody.appendChild(emptyRow);
    }
}
</script>
