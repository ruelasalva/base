<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-clipboard-list fa-2x text-primary mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['total']; ?></h3>
                <p class="text-muted mb-0">Total</p>
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
                <i class="fas fa-tasks fa-2x text-info mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['partial']; ?></h3>
                <p class="text-muted mb-0">Parciales</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['completed']; ?></h3>
                <p class="text-muted mb-0">Completados</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                <h3 class="mb-0"><?php echo $stats['rejected']; ?></h3>
                <p class="text-muted mb-0">Rechazados</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?php echo Uri::create('admin/contrarecibos/index'); ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Código, Notas, Proveedor..." 
                       value="<?php echo Input::get('search', ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pending" <?php echo Input::get('status') == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="partial" <?php echo Input::get('status') == 'partial' ? 'selected' : ''; ?>>Parcial</option>
                    <option value="completed" <?php echo Input::get('status') == 'completed' ? 'selected' : ''; ?>>Completado</option>
                    <option value="rejected" <?php echo Input::get('status') == 'rejected' ? 'selected' : ''; ?>>Rechazado</option>
                    <option value="cancelled" <?php echo Input::get('status') == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
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
                <a href="<?php echo Uri::create('admin/contrarecibos/index'); ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Actions Bar -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Contrarecibos</h4>
    <a href="<?php echo Uri::create('admin/contrarecibos/create'); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Nuevo Contrarecibo
    </a>
</div>

<!-- Delivery Notes Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Proveedor</th>
                        <th>OC / Factura</th>
                        <th>Fecha Entrega</th>
                        <th>Fecha Recepción</th>
                        <th>Productos</th>
                        <th>% Completado</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($delivery_notes) > 0): ?>
                        <?php foreach ($delivery_notes as $dn): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo Uri::create('admin/contrarecibos/view/' . $dn->id); ?>">
                                        <strong><?php echo $dn->code; ?></strong>
                                    </a>
                                </td>
                                <td><?php echo $dn->provider->company_name; ?></td>
                                <td>
                                    <?php if ($dn->purchase_order_id): ?>
                                        <small>
                                            <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $dn->purchase_order->id); ?>">
                                                OC: <?php echo $dn->purchase_order->code; ?>
                                            </a>
                                        </small>
                                    <?php endif; ?>
                                    <?php if ($dn->purchase_id): ?>
                                        <br><small>
                                            <a href="<?php echo Uri::create('admin/compras/view/' . $dn->purchase->id); ?>">
                                                FC: <?php echo $dn->purchase->code; ?>
                                            </a>
                                        </small>
                                    <?php endif; ?>
                                    <?php if (!$dn->purchase_order_id && !$dn->purchase_id): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($dn->delivery_date)); ?></td>
                                <td>
                                    <?php if ($dn->received_date): ?>
                                        <?php echo date('d/m/Y', strtotime($dn->received_date)); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo count($dn->items); ?> items</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar <?php echo $dn->get_completion_percentage() == 100 ? 'bg-success' : 'bg-info'; ?>" 
                                             role="progressbar" 
                                             style="width: <?php echo $dn->get_completion_percentage(); ?>%"
                                             aria-valuenow="<?php echo $dn->get_completion_percentage(); ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?php echo $dn->get_completion_percentage(); ?>%
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $dn->get_status_badge(); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo Uri::create('admin/contrarecibos/view/' . $dn->id); ?>" 
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($dn->can_edit()): ?>
                                            <a href="<?php echo Uri::create('admin/contrarecibos/edit/' . $dn->id); ?>" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($dn->can_delete()): ?>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?php echo $dn->id; ?>)" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No se encontraron contrarecibos</p>
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
                    de <?php echo $pagination_info['total_items']; ?> contrarecibos
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
                ¿Está seguro que desea eliminar este contrarecibo?
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
function confirmDelete(dnId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '<?php echo Uri::create('admin/contrarecibos/delete/'); ?>' + dnId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
