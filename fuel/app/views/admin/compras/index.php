<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['total']; ?></h3>
                <p class="text-muted mb-0">Total Facturas</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['pending']; ?></h3>
                <p class="text-muted mb-0">Pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-coins fa-2x text-info mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['partial']; ?></h3>
                <p class="text-muted mb-0">Parciales</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['paid']; ?></h3>
                <p class="text-muted mb-0">Pagadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['overdue']; ?></h3>
                <p class="text-muted mb-0">Vencidas</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?php echo Uri::create('admin/compras/index'); ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Código, # Factura, Notas..." 
                       value="<?php echo Input::get('search', ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pending" <?php echo Input::get('status') == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="partial" <?php echo Input::get('status') == 'partial' ? 'selected' : ''; ?>>Parcial</option>
                    <option value="paid" <?php echo Input::get('status') == 'paid' ? 'selected' : ''; ?>>Pagada</option>
                    <option value="overdue" <?php echo Input::get('status') == 'overdue' ? 'selected' : ''; ?>>Vencida</option>
                    <option value="cancelled" <?php echo Input::get('status') == 'cancelled' ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Proveedor</label>
                <select name="provider_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($providers as $provider): ?>
                        <option value="<?php echo $provider->id; ?>" 
                                <?php echo Input::get('provider_id') == $provider->id ? 'selected' : ''; ?>>
                            <?php echo $provider->company_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="<?php echo Uri::create('admin/compras/index'); ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Actions Bar -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Facturas de Compra</h4>
    <a href="<?php echo Uri::create('admin/compras/create'); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Nueva Factura
    </a>
</div>

<!-- Purchases Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Factura #</th>
                        <th>Proveedor</th>
                        <th>Fecha Factura</th>
                        <th>Fecha Vencimiento</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Pagado</th>
                        <th class="text-end">Saldo</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($purchases) > 0): ?>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo Uri::create('admin/compras/view/' . $purchase->id); ?>">
                                        <?php echo $purchase->code; ?>
                                    </a>
                                </td>
                                <td><?php echo $purchase->invoice_number; ?></td>
                                <td><?php echo $purchase->provider->company_name; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($purchase->invoice_date)); ?></td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($purchase->due_date)); ?>
                                    <?php if ($purchase->is_overdue()): ?>
                                        <span class="badge bg-danger ms-1">
                                            <?php echo $purchase->get_days_overdue(); ?> días
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">$<?php echo number_format($purchase->total, 2); ?></td>
                                <td class="text-end">$<?php echo number_format($purchase->paid_amount, 2); ?></td>
                                <td class="text-end">
                                    <strong>$<?php echo number_format($purchase->balance, 2); ?></strong>
                                    <?php if ($purchase->balance > 0): ?>
                                        <br><small class="text-muted"><?php echo $purchase->get_payment_percentage(); ?>% pagado</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $purchase->get_status_badge(); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo Uri::create('admin/compras/view/' . $purchase->id); ?>" 
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($purchase->can_edit()): ?>
                                            <a href="<?php echo Uri::create('admin/compras/edit/' . $purchase->id); ?>" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($purchase->can_delete()): ?>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?php echo $purchase->id; ?>)" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No se encontraron facturas</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination)): ?>
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Mostrando <?php echo $pagination_info['offset'] + 1; ?> - 
                    <?php echo min($pagination_info['offset'] + $pagination_info['per_page'], $pagination_info['total_items']); ?> 
                    de <?php echo $pagination_info['total_items']; ?> facturas
                </div>
                <div>
                    <?php echo $pagination; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar esta factura?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(purchaseId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '<?php echo Uri::create('admin/compras/delete/'); ?>' + purchaseId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
