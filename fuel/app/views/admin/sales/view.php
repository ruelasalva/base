<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>Venta #<?php echo $sale['id']; ?>
                </h2>
                <p class="text-body-secondary mb-0">
                    <?php echo date('d/m/Y H:i', strtotime($sale['sale_date'])); ?>
                </p>
            </div>
            <div>
                <?php if (Helper_Permission::can('sales', 'edit')): ?>
                <a href="<?php echo Uri::create('admin/sales/edit/' . $sale['id']); ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo Uri::create('admin/sales'); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Columna Principal -->
    <div class="col-lg-8">
        <!-- Información de la Venta -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Cliente</label>
                        <div class="fw-bold"><?php echo htmlspecialchars($sale['customer_id'] ?? 'Cliente General'); ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Vendedor</label>
                        <div class="fw-bold"><?php echo htmlspecialchars($sale['username'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Fecha de Venta</label>
                        <div class="fw-bold"><?php echo date('d/m/Y H:i', strtotime($sale['sale_date'])); ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Estado</label>
                        <div>
                            <?php
                            $status_badges = [
                                0 => '<span class="badge bg-secondary"><i class="fas fa-shopping-cart me-1"></i>Carrito</span>',
                                1 => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Pagada</span>',
                                2 => '<span class="badge bg-info"><i class="fas fa-exchange-alt me-1"></i>En Transferencia</span>',
                                3 => '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pendiente</span>',
                                4 => '<span class="badge bg-primary"><i class="fas fa-truck me-1"></i>Enviada</span>',
                                5 => '<span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Entregada</span>',
                                -1 => '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Cancelada</span>'
                            ];
                            echo $status_badges[$sale['status']] ?? '<span class="badge bg-secondary">Desconocido</span>';
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($sale['notes'])): ?>
                <hr>
                <div>
                    <label class="text-muted small">Notas</label>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($sale['notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Productos -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>Productos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($items) > 0): ?>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['product_name'] ?? 'Producto #' . $item['product_id']); ?></strong>
                                    </td>
                                    <td class="text-end">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                    <td class="text-end fw-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No hay productos en esta venta
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                <td class="text-end">$<?php echo number_format($sale['total'] + $sale['discount'], 2); ?></td>
                            </tr>
                            <?php if ($sale['discount'] > 0): ?>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Descuento:</td>
                                <td class="text-end text-danger">-$<?php echo number_format($sale['discount'], 2); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-primary">
                                <td colspan="3" class="text-end fw-bold fs-5">TOTAL:</td>
                                <td class="text-end fw-bold fs-5">$<?php echo number_format($sale['total'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Resumen -->
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Resumen</h5>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Subtotal</dt>
                    <dd class="fs-5">$<?php echo number_format($sale['total'] + $sale['discount'], 2); ?></dd>
                    
                    <?php if ($sale['discount'] > 0): ?>
                    <dt class="text-muted small">Descuento</dt>
                    <dd class="fs-5 text-danger">-$<?php echo number_format($sale['discount'], 2); ?></dd>
                    <?php endif; ?>
                    
                    <dt class="text-muted small">Total</dt>
                    <dd class="fs-3 fw-bold text-primary mb-0">
                        $<?php echo number_format($sale['total'], 2); ?>
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Acciones -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Acciones</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                    <button type="button" class="btn btn-outline-info">
                        <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                    </button>
                    <button type="button" class="btn btn-outline-success">
                        <i class="fas fa-envelope me-2"></i>Enviar por Email
                    </button>
                    
                    <?php if (Helper_Permission::can('sales', 'delete') && $sale['status'] != -1): ?>
                    <hr>
                    <button type="button" class="btn btn-danger" id="btn-cancel-sale">
                        <i class="fas fa-ban me-2"></i>Cancelar Venta
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Histórico -->
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Histórico</h6>
                <small class="text-muted d-block mb-2">
                    <i class="fas fa-calendar me-1"></i>
                    Creado: <?php echo date('d/m/Y H:i', strtotime($sale['created_at'] ?? $sale['sale_date'])); ?>
                </small>
                <?php if (!empty($sale['updated_at'])): ?>
                <small class="text-muted d-block">
                    <i class="fas fa-edit me-1"></i>
                    Modificado: <?php echo date('d/m/Y H:i', strtotime($sale['updated_at'])); ?>
                </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (Helper_Permission::can('sales', 'delete')): ?>
<script>
$(document).ready(function() {
    $('#btn-cancel-sale').on('click', function() {
        Swal.fire({
            title: '¿Cancelar Venta?',
            text: 'Esta acción marcará la venta como cancelada',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo Uri::create('admin/sales/delete/' . $sale['id']); ?>';
            }
        });
    });
});
</script>
<?php endif; ?>
