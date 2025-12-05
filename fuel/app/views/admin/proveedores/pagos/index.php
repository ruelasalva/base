<div class="page-header">
    <h1>Pagos a Proveedores</h1>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= Uri::create('admin/proveedores/pagos') ?>" class="row g-3">
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
                    <option value="draft" <?= isset($filters['status']) && $filters['status'] == 'draft' ? 'selected' : '' ?>>Borrador</option>
                    <option value="completed" <?= isset($filters['status']) && $filters['status'] == 'completed' ? 'selected' : '' ?>>Completado</option>
                    <option value="cancelled" <?= isset($filters['status']) && $filters['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
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
                <a href="<?= Uri::create('admin/proveedores/pagos') ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Botones de acción -->
<div class="mb-3">
    <a href="<?= Uri::create('admin/proveedores/pagos/create') ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Nuevo Pago
    </a>
    <a href="<?= Uri::create('admin/proveedores/pagos/report') ?>" class="btn btn-info">
        <i class="fas fa-chart-bar"></i> Reportes
    </a>
</div>

<!-- Tabla de pagos -->
<div class="card">
    <div class="card-body">
        <?php if (count($payments) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td>
                                    <a href="<?= Uri::create('admin/proveedores/pagos/view/' . $payment['id']) ?>">
                                        <strong><?= $payment['payment_number'] ?></strong>
                                    </a>
                                </td>
                                <td><?= date('d/m/Y', strtotime($payment['payment_date'])) ?></td>
                                <td><?= $payment['provider_name'] ?></td>
                                <td>
                                    <?php
                                    $formas_pago_sat = Helper_Sat::get_formas_pago();
                                    $codigo = $payment['payment_method'];
                                    $descripcion = isset($formas_pago_sat[$codigo]) ? $formas_pago_sat[$codigo] : $codigo;
                                    
                                    // Iconos por tipo
                                    $icons = [
                                        '01' => '<i class="fas fa-money-bill"></i>',      // Efectivo
                                        '02' => '<i class="fas fa-money-check"></i>',      // Cheque
                                        '03' => '<i class="fas fa-exchange-alt"></i>',     // Transferencia
                                        '04' => '<i class="fas fa-credit-card"></i>',      // Tarjeta crédito
                                        '28' => '<i class="fas fa-credit-card"></i>',      // Tarjeta débito
                                    ];
                                    
                                    $icon = isset($icons[$codigo]) ? $icons[$codigo] : '<i class="fas fa-wallet"></i>';
                                    echo $icon . ' ' . $descripcion;
                                    ?>
                                </td>
                                <td><?= $payment['reference_number'] ?></td>
                                <td class="text-end">
                                    <strong><?= number_format($payment['amount'], 2) ?> <?= $payment['currency'] ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $badges = [
                                        'draft' => '<span class="badge bg-warning">Borrador</span>',
                                        'completed' => '<span class="badge bg-success">Completado</span>',
                                        'cancelled' => '<span class="badge bg-danger">Cancelado</span>'
                                    ];
                                    echo $badges[$payment['status']] ?? $payment['status'];
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Uri::create('admin/proveedores/pagos/view/' . $payment['id']) ?>" 
                                           class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($payment['status'] == 'draft'): ?>
                                            <a href="<?= Uri::create('admin/proveedores/pagos/complete/' . $payment['id']) ?>" 
                                               class="btn btn-success" 
                                               title="Completar"
                                               onclick="return confirm('¿Completar este pago? Se generará la póliza contable.')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="<?= Uri::create('admin/proveedores/pagos/cancel/' . $payment['id']) ?>" 
                                               class="btn btn-danger" 
                                               title="Cancelar"
                                               onclick="return confirm('¿Cancelar este pago?')">
                                                <i class="fas fa-times"></i>
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
                <i class="fas fa-info-circle"></i> No se encontraron pagos con los filtros aplicados.
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
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
</style>
