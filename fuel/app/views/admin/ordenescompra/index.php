<!-- Encabezado y Estadísticas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="fas fa-file-invoice"></i> Órdenes de Compra</h2>
            <a href="<?php echo Uri::create('admin/ordenescompra/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Orden
            </a>
        </div>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total</h5>
                <h2 class="mb-0"><?php echo $stats['total']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Borradores</h5>
                <h2 class="mb-0 text-secondary"><?php echo $stats['draft']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Pendientes</h5>
                <h2 class="mb-0 text-warning"><?php echo $stats['pending']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Aprobadas</h5>
                <h2 class="mb-0 text-success"><?php echo $stats['approved']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Recibidas</h5>
                <h2 class="mb-0 text-info"><?php echo $stats['received']; ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?php echo Uri::create('admin/ordenescompra/index'); ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="Código, proveedor..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="draft" <?php echo $current_status == 'draft' ? 'selected' : ''; ?>>Borrador</option>
                    <option value="pending" <?php echo $current_status == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="approved" <?php echo $current_status == 'approved' ? 'selected' : ''; ?>>Aprobada</option>
                    <option value="rejected" <?php echo $current_status == 'rejected' ? 'selected' : ''; ?>>Rechazada</option>
                    <option value="received" <?php echo $current_status == 'received' ? 'selected' : ''; ?>>Recibida</option>
                    <option value="cancelled" <?php echo $current_status == 'cancelled' ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select">
                    <option value="">Todos</option>
                    <option value="inventory" <?php echo $current_type == 'inventory' ? 'selected' : ''; ?>>Inventario</option>
                    <option value="usage" <?php echo $current_type == 'usage' ? 'selected' : ''; ?>>Uso</option>
                    <option value="service" <?php echo $current_type == 'service' ? 'selected' : ''; ?>>Servicio</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="<?php echo Uri::create('admin/ordenescompra/index'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Órdenes -->
<div class="card">
    <div class="card-body">
        <?php if (count($orders) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Proveedor</th>
                            <th>Fecha Orden</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th class="text-end">Total</th>
                            <th>Creado Por</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $order->id); ?>">
                                        <strong><?php echo htmlspecialchars($order->code); ?></strong>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($order->provider->company_name); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($order->order_date)); ?></td>
                                <td><?php echo $order->get_type_badge(); ?></td>
                                <td><?php echo $order->get_status_badge(); ?></td>
                                <td class="text-end">
                                    <strong>$<?php echo number_format($order->total, 2); ?></strong>
                                </td>
                                <td><?php echo $order->creator ? htmlspecialchars($order->creator->username) : '-'; ?></td>
                                <td class="text-center">
                                    <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $order->id); ?>" 
                                       class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($order->can_edit()): ?>
                                        <a href="<?php echo Uri::create('admin/ordenescompra/edit/' . $order->id); ?>" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo $order->id; ?>)" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (!empty($pagination)): ?>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Mostrando <?php echo $pagination_info['offset'] + 1; ?> - 
                        <?php echo min($pagination_info['offset'] + $pagination_info['per_page'], $pagination_info['total_items']); ?> 
                        de <?php echo $pagination_info['total_items']; ?> órdenes
                    </div>
                    <div>
                        <?php echo $pagination; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron órdenes de compra.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar esta orden de compra?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    var deleteUrl = '<?php echo Uri::create("admin/ordenescompra/delete/"); ?>' + id;
    document.getElementById('confirmDeleteBtn').href = deleteUrl;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
