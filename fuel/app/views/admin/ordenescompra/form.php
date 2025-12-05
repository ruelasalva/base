<div class="row">
    <div class="col-12">
        <h2>
            <i class="fas fa-file-invoice"></i> 
            <?php echo $is_edit ? 'Editar' : 'Nueva'; ?> Orden de Compra
        </h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <?php echo Form::open(array('class' => 'form-horizontal', 'id' => 'orderForm')); ?>
        
        <!-- Tabs de Navegación -->
        <ul class="nav nav-tabs mb-3" id="orderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                    <i class="fas fa-info-circle"></i> Información Básica
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button">
                    <i class="fas fa-list"></i> Partidas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button">
                    <i class="fas fa-sticky-note"></i> Notas
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="orderTabsContent">
            <!-- Tab 1: Información Básica -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <?php echo Form::select('provider_id', 
                            $order ? $order->provider_id : '', 
                            array_merge(array('' => '-- Seleccione --'), 
                                array_combine(
                                    array_map(function($p) { return $p->id; }, $providers),
                                    array_map(function($p) { return $p->company_name; }, $providers)
                                )
                            ), 
                            array('class' => 'form-select', 'required' => 'required')
                        ); ?>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha de Orden <span class="text-danger">*</span></label>
                        <?php echo Form::input('order_date', 
                            $order ? $order->order_date : date('Y-m-d'), 
                            array('class' => 'form-control', 'type' => 'date', 'required' => 'required')
                        ); ?>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha de Entrega</label>
                        <?php echo Form::input('delivery_date', 
                            $order ? $order->delivery_date : '', 
                            array('class' => 'form-control', 'type' => 'date')
                        ); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <?php echo Form::select('type', 
                            $order ? $order->type : 'inventory', 
                            array(
                                'inventory' => 'Inventario',
                                'usage' => 'Uso',
                                'service' => 'Servicio'
                            ), 
                            array('class' => 'form-select', 'required' => 'required')
                        ); ?>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <?php echo Form::select('status', 
                            $order ? $order->status : 'draft', 
                            array(
                                'draft' => 'Borrador',
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobada'
                            ), 
                            array('class' => 'form-select')
                        ); ?>
                        <small class="form-text text-muted">
                            Las órdenes en borrador pueden ser editadas.
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Tab 2: Partidas -->
            <div class="tab-pane fade" id="items" role="tabpanel">
                <div class="mb-3">
                    <button type="button" class="btn btn-success btn-sm" onclick="addItem()">
                        <i class="fas fa-plus"></i> Agregar Partida
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Tipo</th>
                                <th width="30%">Descripción</th>
                                <th width="10%">Cantidad</th>
                                <th width="12%">Precio Unit.</th>
                                <th width="8%">IVA %</th>
                                <th width="12%">Total</th>
                                <th width="8%">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <?php if ($order && count($order->items) > 0): ?>
                                <?php $index = 0; foreach ($order->items as $item): ?>
                                    <tr data-index="<?php echo $index; ?>">
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <select name="items[<?php echo $index; ?>][item_type]" class="form-select form-select-sm item-type">
                                                <option value="product" <?php echo $item->item_type == 'product' ? 'selected' : ''; ?>>Producto</option>
                                                <option value="service" <?php echo $item->item_type == 'service' ? 'selected' : ''; ?>>Servicio</option>
                                                <option value="custom" <?php echo $item->item_type == 'custom' ? 'selected' : ''; ?>>Personalizado</option>
                                            </select>
                                            <input type="hidden" name="items[<?php echo $index; ?>][product_id]" value="<?php echo $item->product_id; ?>" class="product-id">
                                        </td>
                                        <td>
                                            <input type="text" name="items[<?php echo $index; ?>][description]" 
                                                   class="form-control form-control-sm item-description" 
                                                   value="<?php echo htmlspecialchars($item->description); ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[<?php echo $index; ?>][quantity]" 
                                                   class="form-control form-control-sm text-end item-quantity" 
                                                   value="<?php echo $item->quantity; ?>" step="0.01" min="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[<?php echo $index; ?>][unit_price]" 
                                                   class="form-control form-control-sm text-end item-price" 
                                                   value="<?php echo $item->unit_price; ?>" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[<?php echo $index; ?>][tax_rate]" 
                                                   class="form-control form-control-sm text-end item-tax" 
                                                   value="<?php echo $item->tax_rate; ?>" step="0.01" min="0" max="100">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-end item-total" 
                                                   value="<?php echo number_format($item->total, 2); ?>" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php $index++; endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end"><strong id="orderSubtotal">$0.00</strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end"><strong>IVA:</strong></td>
                                <td class="text-end"><strong id="orderTax">$0.00</strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong id="orderTotal">$0.00</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Tab 3: Notas -->
            <div class="tab-pane fade" id="notes" role="tabpanel">
                <div class="mb-3">
                    <label class="form-label">Notas / Observaciones</label>
                    <?php echo Form::textarea('notes', 
                        $order ? $order->notes : '', 
                        array('class' => 'form-control', 'rows' => '8')
                    ); ?>
                </div>
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar
            </button>
            <a href="<?php echo Uri::create('admin/ordenescompra'); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
        
        <?php echo Form::close(); ?>
    </div>
</div>

<!-- Productos disponibles para búsqueda rápida -->
<script>
var productsData = <?php echo json_encode(array_map(function($p) {
    return array(
        'id' => $p->id,
        'name' => $p->name,
        'sku' => $p->sku,
        'price' => $p->price
    );
}, $products)); ?>;

var itemIndex = <?php echo $order && count($order->items) > 0 ? count($order->items) : 0; ?>;

function addItem() {
    var html = `
        <tr data-index="${itemIndex}">
            <td>${itemIndex + 1}</td>
            <td>
                <select name="items[${itemIndex}][item_type]" class="form-select form-select-sm item-type" onchange="handleTypeChange(this)">
                    <option value="product">Producto</option>
                    <option value="service">Servicio</option>
                    <option value="custom">Personalizado</option>
                </select>
                <input type="hidden" name="items[${itemIndex}][product_id]" value="" class="product-id">
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][description]" 
                       class="form-control form-control-sm item-description" 
                       list="productsList" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" 
                       class="form-control form-control-sm text-end item-quantity" 
                       value="1" step="0.01" min="0.01" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_price]" 
                       class="form-control form-control-sm text-end item-price" 
                       value="0" step="0.01" min="0" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][tax_rate]" 
                       class="form-control form-control-sm text-end item-tax" 
                       value="16" step="0.01" min="0" max="100">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end item-total" 
                       value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', html);
    itemIndex++;
    calculateTotals();
}

function removeItem(button) {
    button.closest('tr').remove();
    renumberItems();
    calculateTotals();
}

function renumberItems() {
    var rows = document.querySelectorAll('#itemsBody tr');
    rows.forEach(function(row, index) {
        row.querySelector('td:first-child').textContent = index + 1;
    });
}

function handleTypeChange(select) {
    var row = select.closest('tr');
    var descInput = row.querySelector('.item-description');
    
    if (select.value === 'product') {
        descInput.setAttribute('list', 'productsList');
    } else {
        descInput.removeAttribute('list');
    }
}

function calculateTotals() {
    var subtotal = 0;
    var totalTax = 0;
    
    document.querySelectorAll('#itemsBody tr').forEach(function(row) {
        var quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        var price = parseFloat(row.querySelector('.item-price').value) || 0;
        var taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;
        
        var itemSubtotal = quantity * price;
        var itemTax = itemSubtotal * (taxRate / 100);
        var itemTotal = itemSubtotal + itemTax;
        
        row.querySelector('.item-total').value = itemTotal.toFixed(2);
        
        subtotal += itemSubtotal;
        totalTax += itemTax;
    });
    
    var total = subtotal + totalTax;
    
    document.getElementById('orderSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('orderTax').textContent = '$' + totalTax.toFixed(2);
    document.getElementById('orderTotal').textContent = '$' + total.toFixed(2);
}

// Event listeners para recalcular al cambiar valores
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('itemsBody').addEventListener('input', function(e) {
        if (e.target.matches('.item-quantity, .item-price, .item-tax')) {
            calculateTotals();
        }
    });
    
    // Auto-completar producto
    document.getElementById('itemsBody').addEventListener('input', function(e) {
        if (e.target.matches('.item-description')) {
            var value = e.target.value;
            var row = e.target.closest('tr');
            var productId = row.querySelector('.product-id');
            var priceInput = row.querySelector('.item-price');
            
            var product = productsData.find(function(p) {
                return p.name === value || p.sku === value;
            });
            
            if (product) {
                productId.value = product.id;
                priceInput.value = product.price;
                calculateTotals();
            }
        }
    });
    
    // Inicializar tabs - Compatible Bootstrap 4/5
    var tabButtons = document.querySelectorAll('#orderTabs button[data-bs-target]')
    
    if (tabButtons.length > 0) {
        tabButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault()
                
                // Desactivar todos los tabs
                tabButtons.forEach(function(btn) {
                    btn.classList.remove('active')
                    var targetId = btn.getAttribute('data-bs-target')
                    var targetPane = document.querySelector(targetId)
                    if (targetPane) {
                        targetPane.classList.remove('show', 'active')
                    }
                })
                
                // Activar el tab clickeado
                button.classList.add('active')
                var targetId = button.getAttribute('data-bs-target')
                var targetPane = document.querySelector(targetId)
                if (targetPane) {
                    targetPane.classList.add('show', 'active')
                }
            })
        })
    }
    
    // Calcular totales iniciales
    calculateTotals();
});
</script>

<!-- Datalist para productos -->
<datalist id="productsList">
    <?php foreach ($products as $product): ?>
        <option value="<?php echo htmlspecialchars($product->name); ?>">
            <?php echo htmlspecialchars($product->sku); ?> - $<?php echo number_format($product->price, 2); ?>
        </option>
    <?php endforeach; ?>
</datalist>
