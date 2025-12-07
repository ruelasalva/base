<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nueva Venta</h2>
                <p class="text-body-secondary mb-0">Registra una nueva venta o pedido</p>
            </div>
            <a href="<?php echo Uri::create('admin/sales'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Formulario -->
<form method="POST" action="<?php echo Uri::current(); ?>" id="form-sale">
    <?php echo Form::csrf(); ?>
    
    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Información del Cliente -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label fw-bold">Cliente <span class="text-danger">*</span></label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">Seleccionar cliente...</option>
                                <!-- Se llenará via AJAX o PHP -->
                            </select>
                            <small class="text-muted">Busca por nombre o RFC</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sale_date" class="form-label fw-bold">Fecha de Venta <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Productos</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-product">
                        <i class="fas fa-plus me-1"></i>Agregar Producto
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="products-table">
                            <thead>
                                <tr>
                                    <th width="40%">Producto</th>
                                    <th width="15%">Precio</th>
                                    <th width="15%">Cantidad</th>
                                    <th width="20%">Subtotal</th>
                                    <th width="10%">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody">
                                <tr id="no-products-row">
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No hay productos agregados. Haz clic en "Agregar Producto"
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                    <td colspan="2"><span id="display-subtotal">$0.00</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Descuento:</td>
                                    <td colspan="2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="discount" name="discount" 
                                                   value="0" min="0" step="0.01">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end fw-bold fs-5">TOTAL:</td>
                                    <td colspan="2" class="fw-bold fs-5"><span id="display-total">$0.00</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <input type="hidden" id="total" name="total" value="0">
                    <input type="hidden" id="items_json" name="items_json" value="[]">
                </div>
            </div>

            <!-- Notas -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notas y Observaciones</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                              placeholder="Información adicional sobre la venta..."></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estado -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Estado</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Estado de la Venta</label>
                        <select class="form-select" id="status" name="status">
                            <option value="0">Carrito (Sin Pagar)</option>
                            <option value="3" selected>Pendiente</option>
                            <option value="1">Pagada</option>
                            <option value="2">En Transferencia</option>
                            <option value="4">Enviada</option>
                            <option value="5">Entregada</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Guardar Venta
                        </button>
                        <a href="<?php echo Uri::create('admin/sales'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ayuda -->
            <div class="card shadow-sm bg-info-subtle">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-lightbulb me-2"></i>Ayuda</h6>
                    <ul class="small mb-0 ps-3">
                        <li>Selecciona un cliente existente</li>
                        <li>Agrega productos con precio y cantidad</li>
                        <li>El descuento se resta del total</li>
                        <li>Puedes guardar como "Carrito" para continuar después</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Agregar Producto -->
<div class="modal fade" id="modal-add-product" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box me-2"></i>Agregar Producto</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="modal-product-search" class="form-label">Buscar Producto</label>
                    <input type="text" class="form-control" id="modal-product-search" 
                           placeholder="Nombre, código o SKU...">
                </div>
                <div class="mb-3">
                    <label for="modal-product-select" class="form-label">Producto</label>
                    <select class="form-select" id="modal-product-select">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="modal-product-price" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="modal-product-price" 
                               step="0.01" min="0" required>
                    </div>
                    <div class="col-6">
                        <label for="modal-product-qty" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="modal-product-qty" 
                               value="1" min="1" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-add-product">
                    <i class="fas fa-plus me-1"></i>Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let products = [];
let modalProduct = null;

$(document).ready(function() {
    // Inicializar modal
    modalProduct = new coreui.Modal(document.getElementById('modal-add-product'));
    
    // Botón agregar producto
    $('#btn-add-product').on('click', function() {
        modalProduct.show();
        loadProducts();
    });
    
    // Confirmar agregar producto
    $('#btn-confirm-add-product').on('click', function() {
        addProductToTable();
    });
    
    // Cambio en descuento
    $('#discount').on('input', function() {
        calculateTotals();
    });
    
    // Envío del formulario
    $('#form-sale').on('submit', function(e) {
        // Validar que hay productos
        if (products.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Sin productos',
                text: 'Debes agregar al menos un producto a la venta'
            });
            return false;
        }
        
        // Actualizar campo hidden con JSON de productos
        $('#items_json').val(JSON.stringify(products));
        return true;
    });
});

// Cargar productos disponibles
function loadProducts() {
    // Simular carga - en producción sería AJAX
    const mockProducts = [
        {id: 1, name: 'Producto Demo 1', price: 100.00},
        {id: 2, name: 'Producto Demo 2', price: 250.00},
        {id: 3, name: 'Producto Demo 3', price: 75.50}
    ];
    
    let options = '<option value="">Seleccionar...</option>';
    mockProducts.forEach(p => {
        options += `<option value="${p.id}" data-price="${p.price}">${p.name} - $${p.price}</option>`;
    });
    
    $('#modal-product-select').html(options);
    
    // Actualizar precio al seleccionar
    $('#modal-product-select').on('change', function() {
        const price = $(this).find(':selected').data('price') || 0;
        $('#modal-product-price').val(price);
    });
}

// Agregar producto a la tabla
function addProductToTable() {
    const productId = $('#modal-product-select').val();
    const productName = $('#modal-product-select option:selected').text();
    const price = parseFloat($('#modal-product-price').val()) || 0;
    const qty = parseInt($('#modal-product-qty').val()) || 1;
    
    if (!productId) {
        Swal.fire({icon: 'warning', title: 'Selecciona un producto'});
        return;
    }
    
    // Verificar si ya existe
    const existing = products.find(p => p.product_id == productId);
    if (existing) {
        existing.quantity += qty;
        existing.subtotal = existing.quantity * existing.price;
    } else {
        products.push({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: qty,
            subtotal: price * qty
        });
    }
    
    renderProductsTable();
    calculateTotals();
    modalProduct.hide();
    
    // Limpiar form
    $('#modal-product-select').val('');
    $('#modal-product-price').val('');
    $('#modal-product-qty').val('1');
}

// Renderizar tabla de productos
function renderProductsTable() {
    const tbody = $('#products-tbody');
    tbody.empty();
    
    if (products.length === 0) {
        tbody.append($('#no-products-row').clone());
        return;
    }
    
    products.forEach((p, index) => {
        const row = `
            <tr>
                <td>${p.product_name}</td>
                <td>$${p.price.toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm qty-input" 
                           data-index="${index}" value="${p.quantity}" min="1">
                </td>
                <td class="fw-bold">$${p.subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-remove" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Eventos para actualizar cantidad
    $('.qty-input').on('change', function() {
        const index = $(this).data('index');
        const newQty = parseInt($(this).val()) || 1;
        products[index].quantity = newQty;
        products[index].subtotal = products[index].price * newQty;
        renderProductsTable();
        calculateTotals();
    });
    
    // Eventos para eliminar
    $('.btn-remove').on('click', function() {
        const index = $(this).data('index');
        products.splice(index, 1);
        renderProductsTable();
        calculateTotals();
    });
}

// Calcular totales
function calculateTotals() {
    const subtotal = products.reduce((sum, p) => sum + p.subtotal, 0);
    const discount = parseFloat($('#discount').val()) || 0;
    const total = subtotal - discount;
    
    $('#display-subtotal').text('$' + subtotal.toFixed(2));
    $('#display-total').text('$' + total.toFixed(2));
    $('#total').val(total.toFixed(2));
}
</script>
