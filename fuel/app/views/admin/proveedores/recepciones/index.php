<div class="page-header">
    <h1>Recepciones de Inventario</h1>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= Uri::create('admin/proveedores/recepciones') ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Proveedor</label>
                <select name="provider_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($providers as $prov): ?>
                        <option value="<?= $prov['id'] ?>" <?= isset($filters['provider_id']) && $filters['provider_id'] == $prov['id'] ? 'selected' : '' ?>>
                            <?= $prov['company_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="received" <?= isset($filters['status']) && $filters['status'] == 'received' ? 'selected' : '' ?>>Recibido</option>
                    <option value="verified" <?= isset($filters['status']) && $filters['status'] == 'verified' ? 'selected' : '' ?>>Verificado</option>
                    <option value="posted" <?= isset($filters['status']) && $filters['status'] == 'posted' ? 'selected' : '' ?>>Afectado</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="<?= Uri::create('admin/proveedores/recepciones') ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Botones de acción -->
<div class="mb-3">
    <a href="<?= Uri::create('admin/proveedores/recepciones/create') ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Nueva Recepción
    </a>
    <a href="<?= Uri::create('admin/compras/ordenes') ?>" class="btn btn-info">
        <i class="fas fa-file-alt"></i> Órdenes de Compra
    </a>
</div>

<!-- Tabla de recepciones -->
<div class="card">
    <div class="card-body">
        <?php if (count($receipts) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Orden de Compra</th>
                            <th>Factura</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Recibido por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipts as $receipt): ?>
                            <tr>
                                <td>
                                    <a href="<?= Uri::create('admin/proveedores/recepciones/view/' . $receipt['id']) ?>">
                                        <strong><?= $receipt['receipt_number'] ?></strong>
                                    </a>
                                </td>
                                <td><?= date('d/m/Y', strtotime($receipt['receipt_date'])) ?></td>
                                <td><?= $receipt['provider_name'] ?></td>
                                <td>
                                    <?php if ($receipt['order_number']): ?>
                                        <a href="<?= Uri::create('admin/compras/ordenes/view/' . $receipt['purchase_order_id']) ?>">
                                            <?= $receipt['order_number'] ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $receipt['invoice_number'] ?: '-' ?></td>
                                <td class="text-end">
                                    <strong>$<?= number_format($receipt['total_amount'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $badges = [
                                        'received' => '<span class="badge bg-info"><i class="fas fa-box"></i> Recibido</span>',
                                        'verified' => '<span class="badge bg-warning"><i class="fas fa-check-circle"></i> Verificado</span>',
                                        'posted' => '<span class="badge bg-success"><i class="fas fa-check-double"></i> Afectado</span>'
                                    ];
                                    echo $badges[$receipt['status']] ?? $receipt['status'];
                                    ?>
                                </td>
                                <td><?= $receipt['received_by_name'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Uri::create('admin/proveedores/recepciones/view/' . $receipt['id']) ?>" 
                                           class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($receipt['status'] == 'received'): ?>
                                            <a href="<?= Uri::create('admin/proveedores/recepciones/verify/' . $receipt['id']) ?>" 
                                               class="btn btn-warning" 
                                               title="Verificar"
                                               onclick="return confirm('¿Verificar esta recepción?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($receipt['status'] == 'verified'): ?>
                                            <a href="<?= Uri::create('admin/proveedores/recepciones/post/' . $receipt['id']) ?>" 
                                               class="btn btn-success" 
                                               title="Afectar Inventario"
                                               onclick="return confirm('¿Afectar inventario? Esta acción actualizará las existencias.')">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?= $pagination->render() ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No se encontraron recepciones con los filtros aplicados.
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    border-radius: 0.5rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.badge i {
    margin-right: 0.25rem;
}

/* Estados con colores específicos */
.badge.bg-info {
    background-color: #17a2b8 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-success {
    background-color: #28a745 !important;
}
</style>
