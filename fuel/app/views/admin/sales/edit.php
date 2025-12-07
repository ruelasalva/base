<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Venta #<?php echo $sale->id; ?></h2>
                <p class="text-body-secondary mb-0">Modifica la información de la venta</p>
            </div>
            <a href="<?php echo Uri::create('admin/sales/view/' . $sale->id); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Alert de estado -->
<?php if ($sale->status == 1 || $sale->status == 5): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Advertencia:</strong> Esta venta ya está <?php echo $sale->status == 1 ? 'pagada' : 'entregada'; ?>. 
    Los cambios deben hacerse con precaución.
    <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Formulario -->
<form method="POST" action="<?php echo Uri::current(); ?>">
    <?php echo Form::csrf(); ?>
    
    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Información Básica -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ID</label>
                            <input type="text" class="form-control" value="#<?php echo $sale->id; ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sale_date" class="form-label fw-bold">Fecha de Venta</label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" 
                                   value="<?php echo date('Y-m-d', strtotime($sale->sale_date)); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cliente</label>
                        <input type="text" class="form-control" 
                               value="<?php echo htmlspecialchars($sale->customer_id ?? 'Cliente General'); ?>" disabled>
                        <small class="text-muted">No se puede cambiar el cliente una vez creada la venta</small>
                    </div>
                </div>
            </div>

            <!-- Totales -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Montos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="total" class="form-label fw-bold">Total <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="total" name="total" 
                                       value="<?php echo $sale->total; ?>" step="0.01" min="0" required>
                            </div>
                            <small class="text-muted">Total de la venta (sin descuento)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discount" class="form-label fw-bold">Descuento</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="discount" name="discount" 
                                       value="<?php echo $sale->discount; ?>" step="0.01" min="0">
                            </div>
                            <small class="text-muted">Descuento aplicado</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <strong>Total Final:</strong> 
                        <span id="total-final" class="fs-5 fw-bold">$<?php echo number_format($sale->total - $sale->discount, 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notas</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo htmlspecialchars($sale->notes ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estado -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Estado</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Estado de la Venta <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="0" <?php echo ($sale->status == 0) ? 'selected' : ''; ?>>
                                Carrito (Sin Pagar)
                            </option>
                            <option value="3" <?php echo ($sale->status == 3) ? 'selected' : ''; ?>>
                                Pendiente
                            </option>
                            <option value="1" <?php echo ($sale->status == 1) ? 'selected' : ''; ?>>
                                Pagada
                            </option>
                            <option value="2" <?php echo ($sale->status == 2) ? 'selected' : ''; ?>>
                                En Transferencia
                            </option>
                            <option value="4" <?php echo ($sale->status == 4) ? 'selected' : ''; ?>>
                                Enviada
                            </option>
                            <option value="5" <?php echo ($sale->status == 5) ? 'selected' : ''; ?>>
                                Entregada
                            </option>
                            <option value="-1" <?php echo ($sale->status == -1) ? 'selected' : ''; ?>>
                                Cancelada
                            </option>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Cambiar el estado afectará el seguimiento de la venta
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card shadow-sm mb-4 border-success">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                        <a href="<?php echo Uri::create('admin/sales/view/' . $sale->id); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información -->
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Información</h6>
                    
                    <dl class="mb-0 small">
                        <dt class="text-muted">Creado por</dt>
                        <dd><?php echo htmlspecialchars($sale->user->username ?? 'Sistema'); ?></dd>
                        
                        <dt class="text-muted">Fecha de Creación</dt>
                        <dd><?php echo date('d/m/Y H:i', strtotime($sale->created_at)); ?></dd>
                        
                        <?php if ($sale->updated_at): ?>
                        <dt class="text-muted">Última Modificación</dt>
                        <dd class="mb-0"><?php echo date('d/m/Y H:i', strtotime($sale->updated_at)); ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Productos (info) -->
            <div class="card shadow-sm bg-info-subtle">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-lightbulb me-2"></i>Nota</h6>
                    <p class="small mb-0">
                        Para modificar los productos de esta venta, deberás cancelarla y crear una nueva. 
                        Esta limitación protege la integridad del inventario.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // Calcular total en tiempo real
    function updateTotal() {
        const total = parseFloat($('#total').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const final = total - discount;
        $('#total-final').text('$' + final.toFixed(2));
    }
    
    $('#total, #discount').on('input', updateTotal);
});
</script>
