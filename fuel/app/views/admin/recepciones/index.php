<div class="container-fluid p-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-warehouse text-primary"></i> Recepciones de Mercancía
            </h1>
            <p class="text-muted mb-0">Gestión de ingreso físico al almacén</p>
        </div>
        <?php if (Helper_Permission::can('recepciones', 'create')): ?>
            <a href="<?php echo Uri::create('admin/recepciones/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Recepción
            </a>
        <?php endif; ?>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-boxes fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Recepciones</h6>
                            <h3 class="mb-0"><?php echo number_format($total_receipts); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Pendientes</h6>
                            <h3 class="mb-0"><?php echo number_format($pending); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-inbox fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Recibidos</h6>
                            <h3 class="mb-0"><?php echo number_format($received); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Verificados</h6>
                            <h3 class="mb-0"><?php echo number_format($verified); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Con Discrepancias</h6>
                            <h3 class="mb-0"><?php echo number_format($with_discrepancies); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo Uri::create('admin/recepciones/index'); ?>" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Búsqueda</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Código, almacén..." 
                           value="<?php echo Input::get('search'); ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label small">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pending" <?php echo Input::get('status') == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="received" <?php echo Input::get('status') == 'received' ? 'selected' : ''; ?>>Recibido</option>
                        <option value="verified" <?php echo Input::get('status') == 'verified' ? 'selected' : ''; ?>>Verificado</option>
                        <option value="discrepancy" <?php echo Input::get('status') == 'discrepancy' ? 'selected' : ''; ?>>Con Discrepancias</option>
                        <option value="cancelled" <?php echo Input::get('status') == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small">Proveedor</label>
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

                <div class="col-md-2">
                    <label class="form-label small">Desde</label>
                    <input type="date" name="date_from" class="form-control" 
                           value="<?php echo Input::get('date_from'); ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label small">Hasta</label>
                    <input type="date" name="date_to" class="form-control" 
                           value="<?php echo Input::get('date_to'); ?>">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <?php if (Input::get('search') || Input::get('status') || Input::get('provider_id') || Input::get('date_from') || Input::get('date_to')): ?>
                <div class="mt-3">
                    <a href="<?php echo Uri::create('admin/recepciones/index'); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar Filtros
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabla de recepciones -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (count($receipts) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Orden Compra</th>
                                <th>Proveedor</th>
                                <th>Almacén</th>
                                <th>Fecha Recepción</th>
                                <th class="text-center">Progreso</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receipts as $receipt): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $receipt->code; ?></strong>
                                        <?php if ($receipt->has_discrepancy): ?>
                                            <i class="fas fa-exclamation-triangle text-danger ms-1" 
                                               title="Con discrepancias"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($receipt->purchase_order): ?>
                                            <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $receipt->purchase_order->id); ?>">
                                                <?php echo $receipt->purchase_order->code; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($receipt->provider): ?>
                                            <a href="<?php echo Uri::create('admin/proveedores/view/' . $receipt->provider->id); ?>">
                                                <?php echo $receipt->provider->company_name; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-warehouse text-muted me-1"></i>
                                        <?php echo $receipt->almacen_name; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($receipt->receipt_date)); ?></td>
                                    <td style="width: 150px;">
                                        <?php $percentage = $receipt->get_completion_percentage(); ?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 me-2">
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar <?php 
                                                        echo $percentage >= 100 ? 'bg-success' : 
                                                            ($percentage > 0 ? 'bg-warning' : 'bg-secondary'); 
                                                    ?>" role="progressbar" 
                                                         style="width: <?php echo min($percentage, 100); ?>%">
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="text-muted"><?php echo number_format($percentage, 0); ?>%</small>
                                        </div>
                                    </td>
                                    <td><?php echo $receipt->get_status_badge(); ?></td>
                                    <td class="text-end">
                                        <strong>$<?php echo number_format($receipt->total_amount, 2); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <?php if (Helper_Permission::can('recepciones', 'view')): ?>
                                                <a href="<?php echo Uri::create('admin/recepciones/view/' . $receipt->id); ?>" 
                                                   class="btn btn-outline-primary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (Helper_Permission::can('recepciones', 'edit') && $receipt->can_edit()): ?>
                                                <a href="<?php echo Uri::create('admin/recepciones/edit/' . $receipt->id); ?>" 
                                                   class="btn btn-outline-secondary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (Helper_Permission::can('recepciones', 'delete') && $receipt->can_delete()): ?>
                                                <a href="<?php echo Uri::create('admin/recepciones/delete/' . $receipt->id); ?>" 
                                                   class="btn btn-outline-danger" title="Eliminar"
                                                   onclick="return confirm('¿Está seguro de eliminar esta recepción?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación (CORRECCIÓN: Usar HTML + info array) -->
                <?php if ($pagination_info['total'] > $pagination_info['per_page']): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Mostrando <?php echo $pagination_info['offset'] + 1; ?> 
                            a <?php echo min($pagination_info['offset'] + $pagination_info['per_page'], $pagination_info['total']); ?> 
                            de <?php echo $pagination_info['total']; ?> registros
                        </div>
                        <div>
                            <?php echo $pagination; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No se encontraron recepciones</p>
                    <?php if (Helper_Permission::can('recepciones', 'create')): ?>
                        <a href="<?php echo Uri::create('admin/recepciones/create'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Recepción
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
